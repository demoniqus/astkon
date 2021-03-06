<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 17.12.18
 * Time: 15:47
 */

namespace Astkon\Controllers;

use Astkon\Controller\Controller;
use Astkon\DataBase;
use Astkon\GlobalConst;
use function Astkon\Lib\array_keys_CamelCase;
use function Astkon\Lib\Redirect;
use Astkon\linq;
use Astkon\Model\Article;
use Astkon\Model\ArticleBalance;
use Astkon\Model\ChangeBalanceMethod;
use Astkon\Model\Measure;
use Astkon\Model\Operation;
use Astkon\Model\OperationItem;
use Astkon\Model\OperationState;
use Astkon\Model\OperationType;
use Astkon\Model\User;
use Astkon\Model\UserGroup;
use Astkon\QueryConfig;
use Astkon\Traits\ListView;
use Astkon\View\View;
use DateTime;

class OperationsController extends Controller
{
    use ListView;
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    public function IndexAction(array $context) {
        (new View())->generate();
    }



    public function EditAction(array $context) {
        $db = new DataBase();
        $view = new View();
        $queryConfig = new QueryConfig();
        $queryConfig->Condition = Operation::PrimaryColumnKey . ' = :' . Operation::PrimaryColumnKey;
        $queryConfig->Substitution = array(Operation::PrimaryColumnKey => $context['id']);

        $operation = Operation::getFirstRow($db, $queryConfig);
        if (!$operation) {
            $view->generate(self::Name() . '/_operation_404');
        }

        $this->checkEditionRules($view, $operation);

        $queryConfig->Reset();
        $queryConfig->Condition = OperationType::PrimaryColumnKey . ' = :' . OperationType::PrimaryColumnKey;
        $queryConfig->Substitution = array(OperationType::PrimaryColumnKey => $operation[OperationType::PrimaryColumnKey]);

        $operationType = OperationType::getFirstRow($db, $queryConfig);

        $queryConfig->Reset();
        $queryConfig->Condition = '`state_name`=\'fixed\'';

        $fixedState = OperationState::getFirstRow($db, $queryConfig);

        if ($operation[OperationState::PrimaryColumnKey] === $fixedState[OperationState::PrimaryColumnKey]) {
            $view->operationType = array_keys_CamelCase($operationType);
            $view->message = 'Не допускается редактировать документ в статусе "' . $fixedState['state_label'] . '"';
            /*Запрещено редактировать закрытый документ*/
            $view->generate(self::Name() . '/_denied_action');
        }
        $opTypeName = $operationType['operation_name'];
        $operation = $this->defineCommonFormContext($view, $opTypeName, $context);
        $this->loadLinkedDataItems($view, $operation);

        $view->selectedItems = $this->getOperationItems($db, $operation);

        $view->generate();
    }

    public function DetailAction(array $context) {
        $db = new DataBase();
        $view = new View();
        $queryConfig = new QueryConfig();
        $queryConfig->Condition = Operation::PrimaryColumnKey . ' = :' . Operation::PrimaryColumnKey;
        $queryConfig->Substitution = array(Operation::PrimaryColumnKey => $context['id']);

        $operation = Operation::getFirstRow($db, $queryConfig);

        if (!$operation) {
            $view->generate(self::Name() . '/_operation_404');
        }

        $queryConfig->Reset();
        $queryConfig->Condition = '`' . OperationState::PrimaryColumnKey . '` = :' . OperationState::PrimaryColumnKey;
        $queryConfig->Substitution = array(
            OperationState::PrimaryColumnKey => $operation[OperationState::PrimaryColumnKey]
        );

        $view->operationState = OperationState::getFirstRow(
            $db,
            $queryConfig
        );

        $queryConfig->Reset();
        $queryConfig->Condition = OperationType::PrimaryColumnKey . ' = :' . OperationType::PrimaryColumnKey;
        $queryConfig->Substitution = array(OperationType::PrimaryColumnKey => $operation[OperationType::PrimaryColumnKey]);

        $operationType = OperationType::getFirstRow($db, $queryConfig);
        $opTypeName = $operationType['operation_name'];
        $operation = $this->defineCommonFormContext($view, $opTypeName, $context);
        $this->loadLinkedDataItems($view, $operation);
        $view->selectedItems = $this->getOperationItems($db, $operation);
        $view->generate();
    }

    private function getOperationItems(DataBase $db, array $operation) : array {
        $queryConfig = new QueryConfig(
            Operation::PrimaryColumnKey . ' = :' . Operation::PrimaryColumnKey,
            null,
            array(Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnKey])
        );
        $operationItems = OperationItem::getRows($db, $queryConfig);

        $queryConfig->Reset();
        $queryConfig->Condition =Article::PrimaryColumnKey . ' in (' .
            implode(
                ',',
                array_map(
                    function($operationItem){ return $operationItem[Article::PrimaryColumnKey];},
                    $operationItems
                )
            )
            . ')';
        $articles = Article::getRows($db, $queryConfig);
        $operationItems = (new linq($operationItems))
            ->toAssoc(
                function($operationItem){ return $operationItem[Article::PrimaryColumnKey];},
                function($operationItem){return $operationItem['operation_count'];}
            )
            ->getData();
        return array_map(
            function($article) use ($operationItems){
                $article['Count'] = $operationItems[$article[Article::PrimaryColumnKey]];
                return array_keys_CamelCase($article);
            },
            $articles
        );
    }

    public function ChangeTypeAction(array $context) {
        $view = new View();
        $view->activeMenu = '/Operations/Index';
        $db = new DataBase();
        $db->beginTransaction();
        $idOperation = intval($context['id']);

        $operation = Operation::GetByPrimaryKey($idOperation, $db);

        if (!$operation) {
            $db->rollback();
            $view->generate(self::Name() . '/_operation_404');
        }

        $listOperationTypes = OperationType::getRows($db);

        $dictOperationTypes = (new linq($listOperationTypes))
            ->toAssoc(function($operationType){
                return $operationType[OperationType::PrimaryColumnKey];
            })
            ->getData();

        $operationType = $dictOperationTypes[$operation[OperationType::PrimaryColumnKey]];

        if (strtolower($operationType['operation_name']) !== 'reserving') {
            $db->rollback();
            $view->message = 'Для документа изменение типа запрещено.';
            $view->generate(self::Name() . '/_denied_action');
        }

        $targetType = isset($_GET['targetType']) ? intval($_GET['targetType']) : 0;
        if (!array_key_exists($targetType, $dictOperationTypes)) {
            $db->rollback();
            $view->message = 'Не указан целевой тип операции.';
            $view->generate(self::Name() . '/_denied_action');
        }

        $targetType = $dictOperationTypes[$targetType];
        if (!in_array(strtolower($targetType['operation_name']), array('sale', 'writeoff'))) {
            $db->rollback();
            $view->message = 'Указан недопустимый целевой тип операции.';
            $view->generate(self::Name() . '/_denied_action');
        }

        $operation['operation_info']['change_type'] = array(
            'label'    => 'Изменен тип документа',
            'value'    => CURRENT_USER['IdUser'],
            'caption'  => 'Пользователем ' . CURRENT_USER['UserName'] . ' тип документа изменен с "' .
                $operationType['operation_label'] . '" на "' . $targetType['operation_label'] . '"',
            'datetime' => (new DateTime())->format('Y-m-d H:i:s'),
        );

        Operation::Update(
            array(
                Operation::PrimaryColumnKey     => $operation[Operation::PrimaryColumnKey],
                OperationType::PrimaryColumnKey => $targetType[OperationType::PrimaryColumnKey],
                'operation_info'                => $operation['operation_info'],
            ),
            $db
        );
        $db->commit();
        Redirect(static::Name(), static::Name() . 'List', $operationType[OperationType::PrimaryColumnKey]);
    }

    public function OperationsListAction(array $context) {
        $view = new View();
        $view->activeMenu = '/Operations/Index';
        $IdOperationType = intval($context['id']);
        $operationType = OperationType::GetByPrimaryKey($IdOperationType);
        if (is_null($operationType)) {
            Redirect('Operations', 'Index');
        }
        $view->operationType = $operationType;

        $options = array(
            array(
                'action' => '/Operations/Detail',
                'click' => null,
                'icon' => '/icon-view.png',
                'title' => 'Просмотр'
            ),

        );

        $queryConfig = new QueryConfig('`state_name`=\'fixed\'');

        $fixedState = OperationState::getFirstRow(null, $queryConfig);

        $listOperationTypes = OperationType::getRows();
        $dictOperationTypes = (new linq($listOperationTypes))
            ->toAssoc(function($operationType){
                return strtolower($operationType['operation_name']);
            })
            ->getData();

        static::editOption($options, __CLASS__);

        $options[count($options) - 1]['condition'] = function($operation) use ($fixedState){
            return $operation[OperationState::PrimaryColumnName] !== $fixedState[OperationState::PrimaryColumnKey];
        };
        if (strtolower($operationType['operation_name']) === 'reserving') {
            $options[] = array(
                'action' => '/Operations/ChangeType?targetType=' . $dictOperationTypes['sale'][OperationType::PrimaryColumnKey],
                'click' => null,
                'icon' => '/leaving.png',
                'title' => 'Расход'
            );
            $options[] = array(
                'action' => '/Operations/ChangeType?targetType=' . $dictOperationTypes['writeoff'][OperationType::PrimaryColumnKey],
                'click' => null,
                'icon' => '/write-off.png',
                'title' => 'Списание'
            );
        }
        else {
            /*Операцию Reserving нельзя зафиксировать - ее артикулы можно или списать, или израсходовать*/
            $options[] = array(
                'action' => null,
                'click' => 'if (confirm(\'Вы уверены, что хотите зафиксировать документ?\')) {window.location.href=\'/Operations/Fixation/@IdOperation\';}',
                'icon' => '/set_fixed_state.png',
                'title' => 'Закрыть документ',
                'condition' => function($operation) use ($fixedState){
                    return $operation[OperationState::PrimaryColumnName] !== $fixedState[OperationState::PrimaryColumnKey];
                }
            );
        }
        $options[] = array(
            'action' => null,
            'click' => 'if (confirm(\'Вы уверены, что хотите удалить документ?\')) {window.location.href=\'/Operations/Delete/@IdOperation\';}',
            'icon' => '/trash-empty-icon.png',
            'title' => 'Удалить документ',
            'condition' => function($operation) use ($fixedState){
                return $operation[OperationState::PrimaryColumnName] !== $fixedState[OperationState::PrimaryColumnKey];
            }
        );

        $queryConfig->Reset();
        $queryConfig->Condition = '`' . Operation::DataTable . '`.id_operation_type = :id_operation_type and `'
            . Operation::DataTable . '`.id_user_group= :id_user_group';
        $queryConfig->RequiredFields = array(
            'id_operation',
            'id_operation_state',
            'create_datetime',
            'fix_datetime',
            'operation_info',
            'state_label',
        );
        $queryConfig->Substitution = array(
            'id_operation_type' => $IdOperationType,
            'id_user_group' => CURRENT_USER[UserGroup::PrimaryColumnName]
        );

        $this->ListViewAction(
            $view,
            Operation::class,
            $options,
            $queryConfig,
            array(
                'id_operation',
                'state_label',
                'create_datetime',
                'fix_datetime',
            )
        );
        $view->generate();
    }

    /**
     * Форма прихода на баланс
     * @param array $context
     */
    public function IncomeFormAction(array $context){
        $OpTypeName = 'Income';
        $view = new View();
        $this->defineCommonFormContext($view, $OpTypeName, $context);
        $view->generate();

    }

    /**
     * Форма расхода с баланса
     * @param array $context
     */
    public function SaleFormAction(array $context) {
        $OpTypeName = 'Sale';
        $view = new View();
        $operation = $this->defineCommonFormContext($view, $OpTypeName, $context);
        $this->loadLinkedDataItems($view, $operation);
        $view->generate();
    }

    /**
     * Форма безвозвратного безвозмездного списания
     * @param array $context
     */
    public function WriteOffFormAction(array $context) {
        $OpTypeName = 'WriteOff';
        $view = new View();
        $this->defineCommonFormContext($view, $OpTypeName, $context);
        $view->generate();
    }

    /**
     * Форма передачи во временное безвозмездное пользование
     * @param array $context
     */
    public function ReservingFormAction(array $context) {
        $OpTypeName = 'Reserving';
        $view = new View();
        $operation = $this->defineCommonFormContext($view, $OpTypeName, $context);
        $this->loadLinkedDataItems($view, $operation);
        $view->generate();
    }

    public function InventoryFormAction(array $context) {
        $OpTypeName = 'Inventory';
        $view = new View();
        $this->defineCommonFormContext($view, $OpTypeName, $context);
        $view->generate();
    }

    private function checkEditionRules(View $view, array $operation) {
        if ($operation[UserGroup::PrimaryColumnKey] !== CURRENT_USER[UserGroup::PrimaryColumnName]) {
            $view->message = 'У вас нет прав для изменения данного документа.';
            $view->generate(self::Name() . '/_denied_action');
        }
    }


    public function FixationAction (array $context) {
        $db = new DataBase();
        $view = new View();
        $db->beginTransaction();
        $operation = Operation::GetByPrimaryKey(intval($context['id']), $db);
        if (is_null($operation)) {
            $db->rollback();
            $view->generate(self::Name() . '/_operation_404');
        }

        $this->checkEditionRules($view, $operation);

        $res = $this->setOperationFixedState($operation, $db);
        if (is_array($res) && isset($res['errors'])) {
            $db->rollback();
            $view->operationType = array_keys_CamelCase(OperationType::GetByPrimaryKey(
                $operation[OperationType::PrimaryColumnKey],
                $db
            ));
            $view->message = implode('<br />', $res['errors']);
            $view->generate(self::Name() . '/_denied_action');
        }
        else {
            $db->commit();
        }
        Redirect(static::Name(), 'OperationsList', $operation[OperationType::PrimaryColumnKey]);
    }


    public function SaveAction(array $context) {
        $db = new DataBase();
        $view = new View();
        $errors = array();
        if (
            !isset($_POST['operation']) ||
            !isset($_POST['selectedItems']) ||
            !is_array($_POST['operation']) ||
            !is_array($_POST['selectedItems']) ||
            count($_POST['selectedItems']) < 0
        ) {
            $view->errors = array(
                'Получены некорректные данные. Сохранение невозможно.'
            );
            $view->JSONView();
        }

        $queryConfig = new QueryConfig();

        $db->beginTransaction();
        /*Проверяем, что запрошен допустимый тип операции*/
        $operation = $_POST['operation'];

        $existsOperation = null;
        if ($operation[Operation::PrimaryColumnName] != 0) {
            $existsOperation = Operation::GetByPrimaryKey(
                $operation[Operation::PrimaryColumnName],
                $db
            );
            if (!$existsOperation) {
                $errors[] = 'Получен недействительный идентификатор операции';
            }
            if ($existsOperation[OperationType::PrimaryColumnKey] != $operation[OperationType::PrimaryColumnName]) {
                $errors[] = 'Тип существующего документа не может быть изменен';
            }
            if ($existsOperation[OperationState::PrimaryColumnKey] != $operation[OperationState::PrimaryColumnName]) {
                $errors[] = 'Статус существующего документа не может быть изменен напрямую';
            }
        }
        if (count($errors)) {
            $db->rollback();
            $view->errors = $errors;
            $view->JSONView();
        }

        $queryConfig->Condition = OperationType::PrimaryColumnKey . ' = :' . OperationType::PrimaryColumnKey;
        $queryConfig->Substitution = array(
            OperationType::PrimaryColumnKey => $operation[OperationType::PrimaryColumnName],
        );

        $operationType = OperationType::getFirstRow($db, $queryConfig);
        if (is_null($operationType)) {
            $errors[] = 'Запрошен недопустимый тип операции';
        }

        /*
         * Проверим, что для накладной определены какие-то артикулы с допустимым для данного типа накладной количеством:
         * - для инвентаризации от 0 до +бесконечности, включая ноль
         * - для прочих накладных от нуля до +бесконечности, не включая ноль
         *
        */
        $selectedItems = $_POST['selectedItems'];
        if (is_array($selectedItems)) {
            if ($operationType['operation_name'] === 'Inventory') {
                $selectedItems = array_filter($selectedItems, function($item){ return $item['count'] >= 0;});
            }
            else {
                $selectedItems = array_filter($selectedItems, function($item){ return $item['count'] > 0;});
            }
        }

        if (!is_array($selectedItems) || count($selectedItems) === 0) {
            $errors[] = 'Нет элементов для сохранения';
        }

        /*Проверим, что переданы действительные идентификаторы артикулов*/
        $articleListId = array_map(
            function($item){
                /*Вряд ли кто-нибудь осилит создать 2 и более (для 64bit) млрд артикулов, так что здесь можно использовать такую проверку*/
                $id = intval($item[Article::PrimaryColumnName]);
                return $id == $item[Article::PrimaryColumnName] && $id > 0 ? $id : null;
            },
            $selectedItems
        );

        $articleListId = array_filter(
            $articleListId,
            function($id){ return !is_null($id);}
        );

        $deepDecodeForeignKeys = 0;

        $queryConfig->Reset();
        $queryConfig->Condition = Article::PrimaryColumnKey . ' in (' . implode(',', $articleListId) . ')';
        $queryConfig->Limit = count($articleListId);
        if (
            strtolower($operationType['operation_name']) === 'sale' ||
            strtolower($operationType['operation_name']) === 'writeoff'
        ) {
            $deepDecodeForeignKeys = 1;
            $queryConfig->RequiredFields = array_merge(
                Article::ModelPublicProperties(),
                array(
                    'is_writeoff',
                    'is_saleable',
                )
            );
        }

        $articles = Article::getRows(
            $db,
            $queryConfig,
            $deepDecodeForeignKeys
        );

        if (
            count($articles) !== count($articleListId) ||
            count($articleListId) !== count($selectedItems) ||
            count($articleListId) !== count($_POST['selectedItems'])
        ) {
            $errors[] = 'Некорректная информация об артикулах.';
        }

        switch (strtolower($operationType['operation_name'])) {
            case 'sale':
                $checkedFlag = 'is_saleable';
                foreach ($articles as $article) {
                    if (!$article[$checkedFlag]) {
                        $errors[] = 'Артикул "' . $article['article_name'] . '" не может быть израсходован';
                    }
                }
                break;
            case 'writeoff':
                $checkedFlag = 'is_writeoff';
                foreach ($articles as $article) {
                    if (!$article[$checkedFlag]) {
                        $errors[] = 'Артикул "' . $article['article_name'] . '" не может быть списан';
                    }
                }
                break;
        }

        /*
         * Проверим, что переданы правильные по типу количества - целые или дробные,
         * а заодно и приведем все $selectedItems к числовым типам
         */

        $queryConfig->Reset();
        $queryConfig->Condition = Measure::PrimaryColumnKey . ' in (' .
            implode(
                ',',
                array_map(
                    function($article){ return $article[Measure::PrimaryColumnKey];},
                    $articles
                )
            )
            . ')';

        $measures = Measure::getRows($db, $queryConfig);

        $measures = (new linq($measures))
            ->toAssoc(function($measure){
                    return $measure[Measure::PrimaryColumnKey];
                }
            )->getData();

        $articles = (new linq($articles))
            ->toAssoc(function($article){
                return $article[Article::PrimaryColumnKey];
            })->getData();

        foreach ($selectedItems as &$selectedItem) {
            $selectedItem[Article::PrimaryColumnName] = intval($selectedItem[Article::PrimaryColumnName]);
            $article = $articles[$selectedItem[Article::PrimaryColumnName]];
            $measure = $measures[$article[Measure::PrimaryColumnKey]];
            $count = $selectedItem['count'];
            $count = round($count, $measure['is_split'] ? $measure['precision'] : 0);
            if ($count != $selectedItem['count']) {
                $errors[] = 'Для артикула ' . $article['article_name'] . ' установлено недопустимое количество.';
            }
            $selectedItem['count'] = $count;
        }

        $linkedData = array();
        if (isset($_POST['linkedData'])) {
            if (is_array($_POST['linkedData'])) {
                foreach ($_POST['linkedData'] as $modelName => $items) {
                    if (!is_array($items) || count($items) < 1) {
                        $errors[] = 'Неверный формат связанных данных';
                        break;
                    }
                    $modelName = DataBase::underscoreToCamelCase($modelName);
                    $model = explode('\\', Operation::class);
                    $model[count($model) - 1] = $modelName;
                    $model = implode('\\', $model);
                    if (!class_exists($model)) {
                        $errors[] = 'Связанные данные содержат информацию о недопустимых типах';
                        break;
                    }
                    else {
                        $linkedData[$modelName] = array_map(
                            function($linkedItem) use ($model) {
                                return intval($linkedItem[$model::PrimaryColumnName]);
                            },
                            array_values($items)
                        );
                    }
                }
            }
        }

        if (count($errors)) {
            $view->errors = $errors;
            $view->JSONView();
        }

        $queryConfig->Reset();
        $queryConfig->Condition = ChangeBalanceMethod::PrimaryColumnKey . ' = :' . ChangeBalanceMethod::PrimaryColumnKey;
        $queryConfig->Substitution = array(
            ChangeBalanceMethod::PrimaryColumnKey => $operationType[ChangeBalanceMethod::PrimaryColumnKey],
        );

        $changeBalanceMethod = ChangeBalanceMethod::getFirstRow($db, $queryConfig);

        $saveMethod = 'Save_ChangeBalance' . DataBase::underscoreToCamelCase($changeBalanceMethod['method_name']);

        $operation = $this->$saveMethod(
            $db,
            $view,
            $operation,
            $selectedItems,
            $articles
        );

        Operation::Update(
            array(
                Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnKey],
                'linked_data' => count($linkedData) > 0 ? $linkedData : null
            ),
            $db
        );

        if (isset($_POST['setFixedState'])) {
            if (
                $_POST['setFixedState'] === true ||
                (is_string($_POST['setFixedState']) && strtolower($_POST['setFixedState']) === 'true')
            ) {
                $res = $this->setOperationFixedState($operation, $db);
                if (is_array($res) && isset($res['errors'])) {
                    $errors = $res['errors'];
                }
                $view->redirect = '/' . static::Name() . '/Detail/' . $operation[Operation::PrimaryColumnKey];
            }
        }
        if (count($errors)) {
            $db->rollback();
            $view->ClearVariables();
            $view->errors = $errors;
            $view->JSONView();
        }

        $db->commit();

        $view->success = true;
        $view->operation = array_keys_CamelCase($operation);
        $view->JSONView();

    }

    public function DeleteAction(array $context)  {
        $view = new View();
        $db = new DataBase();

        $db->beginTransaction();

        $operation = Operation::GetByPrimaryKey(
            intval($context['id']),
            $db
        );
        if ($operation) {
            $operationState = OperationState::GetByPrimaryKey(
                $operation[OperationState::PrimaryColumnKey],
                $db
            );

            $operationType = OperationType::GetByPrimaryKey(
                $operation[OperationType::PrimaryColumnKey],
                $db
            );
            if ($operationState['state_name'] === 'fixed') {
                $view->operationType = array_keys_CamelCase($operationType);

                $queryConfig = new QueryConfig('state_name=\'fixed\'');

                $view->message = 'Не допускается удалять документ в статусе "' . OperationState::getFirstRow(null, $queryConfig)['state_label'] . '"';
                /*Запрещено удалять закрытый документ*/
                $view->generate(self::Name() . '/_denied_action');
            }

            $changeBalanceMethod = ChangeBalanceMethod::GetByPrimaryKey(
                $operationType[ChangeBalanceMethod::PrimaryColumnKey],
                $db
            );

            $operationItems = Operation::getItems($operation, array(OperationItem::PrimaryColumnKey), $db);
            $operationItems = array_map(
                function($operationItem){ return $operationItem[OperationItem::PrimaryColumnKey];},
                $operationItems
            );
            if ($changeBalanceMethod['method_name'] === 'in_fixation') {
                /*Эта операция еще не изменила состояние запаса, поэтому просто удаляем ее элементы*/
                OperationItem::Delete(
                    $operationItems,
                    $db
                );
            }
            else {
                /*Эта операция при создании изменила состояние запаса и теперь нужно перед удалением элементов вернуть их количество на запас*/
                OperationItem::ReturnOnBalance($operationItems, $db);
            }
            Operation::Delete($operation[Operation::PrimaryColumnKey], $db);
            $db->commit();

            Redirect(static::Name(), 'OperationsList', $operationType[OperationType::PrimaryColumnKey]);
        }
        else {
            $db->rollback();
            Redirect(static::Name(), 'Index');

        }
    }

    /**
     * Метод устанавливает для операции статус Зафиксирована, а также при необходимости корректирует состояние запаса
     * Возвращает массив с ошибками, либо true в случае, если статус накладной изменен на Зафиксирован, либо false в случае,
     * если накладная уже имеет статус Зафиксирован
     *
     * @param array    $operation
     * @param DataBase $db
     * @return bool | array
     */
    private function setOperationFixedState(array $operation, DataBase $db) {
        $operationState = OperationState::GetByPrimaryKey($operation[OperationState::PrimaryColumnKey], $db);
        if (strtolower($operationState['state_name']) !== 'fixed') {
            $queryConfig = new QueryConfig();
            $queryConfig->Condition ='`state_name`=\'fixed\'';

            $fixedState = OperationState::getFirstRow($db, $queryConfig);

            $operationType = OperationType::GetByPrimaryKey($operation[OperationType::PrimaryColumnKey], $db);

            switch (strtolower($operationType['operation_name'])) {
                case 'reserving':
                    /*
                     * Артикулы могут быть лишь временно зарезервированы на сотруднике.
                     * После этого они должны быть либо израсходованы, либо списаны.
                     */
                    return array(
                        'errors' => array(
                            'Данный тип операций не может быть зафиксирован. Для фиксации измените тип на расходый или списание.'
                        )
                    );
                case 'inventory':
                    /*
                     * !!!!!Инвентаризация не может проводиться по тем элементам, по которым есть незакрытое движение
                     * (расход, резерв, списание, другая незакрытая инвентаризационная накладная) в текущей группе пользователей
                     */
                    $listIdOperationItems = Operation::getItems(
                        $operation,
                        array(Article::PrimaryColumnKey),
                        $db
                    );
                    $listIdOperationItems = array_map(
                        function($operationItem){ return $operationItem[Article::PrimaryColumnKey];},
                        $listIdOperationItems
                    );

                    $queryConfig->Reset();
                    $queryConfig->Condition = '`operation_name` = \'Income\'';
                    $queryConfig->RequiredFields = array(OperationType::PrimaryColumnKey);

                    $incomeType = OperationType::getFirstRow($db, $queryConfig, 0);

                    $queryConfig->Reset();
                    $queryConfig->Condition =
                        '`' . OperationItem::DataTable . '`.`' . Article::PrimaryColumnKey . '` in (' . implode(',', $listIdOperationItems) . ')' .
                        ' AND `' . OperationItem::DataTable . '`.`' . Operation::PrimaryColumnKey . '` <> ' . $operation[Operation::PrimaryColumnKey] .
                        ' AND `' . Operation::DataTable . '`.`' . OperationState::PrimaryColumnKey . '` <> ' . $fixedState[OperationState::PrimaryColumnKey] .
                        ' AND `' . Operation::DataTable . '`.`' . OperationType::PrimaryColumnKey . '` <> ' . $incomeType[OperationType::PrimaryColumnKey] .
                        ' AND `' . Operation::DataTable . '`.`' . UserGroup::PrimaryColumnKey . '` = ' . CURRENT_USER[UserGroup::PrimaryColumnName];
                    $queryConfig->RequiredFields = array(
                        Article::PrimaryColumnKey,
                        'article_name',
                        Operation::PrimaryColumnKey
                    );

                    $articlesData = OperationItem::getRows(
                        $db,
                        $queryConfig,
                        1
                    );
                    if (count($articlesData)) {
                        $tmp = array();
                        foreach ($articlesData as $articleData) {
                            if (!array_key_exists($articleData[Operation::PrimaryColumnKey], $tmp)) {
                                $tmp[$articleData[Operation::PrimaryColumnKey]] = array();
                            }
                            $tmp[$articleData[Operation::PrimaryColumnKey]][$articleData[Article::PrimaryColumnKey]] = $articleData['article_name'];
                        }

                        $errors = array();
                        foreach ($tmp  as $idOperation => $articleData) {
                            foreach ($articleData as $idArticle => $articleName) {
                                $errors[] = 'Инвентаризация по артикулу \'' . $articleName . '\' невозможна, т.к. он используется в '
                                    .'<a href="/' . self::Name() . '/Detail/' . $idOperation . '" target="_blank">операции #' . $idOperation . '</a>';
                            }
                        }
                        return array(
                            'errors' => $errors
                        );
                    }
                    break;
                case 'sale':
                case 'writeoff':

                    $operationItems = Operation::getItems(
                        $operation,
                        array(
                            'article_name',
                            'is_writeoff',
                            'is_saleable',
                        ),
                        $db,
                        2
                    );
                    if (strtolower($operationType['operation_name']) === 'sale') {
                        $message = ' не может быть проведен через документ расхода';
                        $checkedFlag = 'is_saleable';
                    }
                    else {
                        $message = ' не может быть проведен через документ списания';
                        $checkedFlag = 'is_writeoff';
                    }
                    $errors = array();
                    foreach ($operationItems as $operationItem) {
                        if (!$operationItem[$checkedFlag]) {
                            $errors[] = 'Артикул \'' . $operationItem['article_name'] . '\' ' . $message;
                        }
                    }
                    if (count($errors)) {
                        return array(
                            'errors' => $errors
                        );
                    }
                break;
            }

            $queryConfig = new QueryConfig();

            $changeBalanceMethod = ChangeBalanceMethod::GetByPrimaryKey($operationType[ChangeBalanceMethod::PrimaryColumnKey], $db);
            if ($changeBalanceMethod['method_name'] === 'in_fixation') {
                $queryConfig->Condition = '`' . Operation::PrimaryColumnKey . '` = ' . $operation[Operation::PrimaryColumnKey];
                $queryConfig->RequiredFields = array(Article::PrimaryColumnKey, 'operation_count');

                $listOperationItems = OperationItem::getRows($db, $queryConfig);
                switch (strtolower($operationType['operation_name'])) {
                    case 'income':
                        OperationItem::AddToBalance($listOperationItems, $db);
                        break;
                    case 'inventory':
                        OperationItem::InventoryBalance($listOperationItems, $db);
                        break;
                }
            }

            $operation['operation_info']['fixer'] = array(
                'value' => CURRENT_USER[User::PrimaryColumnName],
                'caption' => CURRENT_USER['UserName'],
                'label' => 'Перевел документ в статус \'' . $fixedState['state_label'] . '\''
            );
            Operation::Update(
                array(
                    Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnKey],
                    OperationState::PrimaryColumnKey => $fixedState[OperationState::PrimaryColumnKey],
                    'operation_info' => $operation['operation_info'],
                    'fix_datetime' => new DateTime(),
                ),
                $db
            );
            return true;
        }
        return false;
    }

    /**
     * Метод обеспечивает сохранение операций, влияющих на состояние запаса по факту перехода в статус Fixed
     *
     * @param DataBase $db
     * @param View     $view
     * @param array    $operation
     * @param array    $selectedItems
     * @param array    $foundArticles
     * @return array    возвращает $operation
     */
    private function Save_ChangeBalanceInFixation(
        DataBase $db,
        View $view,
        array $operation,
        array $selectedItems,
        array &$foundArticles
        ) : array {
        $queryConfig = new QueryConfig();

        if ($operation[Operation::PrimaryColumnName] == 0) {
            $queryConfig->Condition = '`state_name`=\'new\'';
            $operationNewState = OperationState::getFirstRow($db, $queryConfig);
            $operation = Operation::Create(
                array(
                    'create_datetime' => new DateTime(),
                    'id_user_group' => CURRENT_USER[UserGroup::PrimaryColumnName],
                    'id_operation_type' => intval($operation[OperationType::PrimaryColumnName]),
                    'id_operation_state' => $operationNewState[OperationState::PrimaryColumnKey],
                    'operation_info' => array(
                        'creator' => array(
                            'label' => 'Создал документ',
                            'caption' => CURRENT_USER['UserName'],
                            'value' => CURRENT_USER[User::PrimaryColumnName]
                        )
                    )
                ),
                $db,
                true
            );
        }
        else {
            $queryConfig->Condition = Operation::PrimaryColumnKey . ' = :' . Operation::PrimaryColumnKey;
            $queryConfig->Substitution =array(
                Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnName],
            );

            $operation = Operation::getFirstRow($db, $queryConfig);

            /*Дописываем инфу о модификации накладной*/
            $opInfo = $operation['operation_info'];
            if (!is_array($opInfo)) {
                $opInfo = array();
            }
            if (!array_key_exists('edited', $opInfo)) {
                $opInfo['edited'] = array(
                    'label' => 'Изменен',
                    'items' => array(),
                );
            }
            $opInfo['edited']['items'][] = array(
                User::PrimaryColumnKey => CURRENT_USER[User::PrimaryColumnName],
                'user_name' => CURRENT_USER['UserName'],
                'datetime' => date('Y-m-d H:i:s', time()),
            );

            Operation::Update(
                array(
                    Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnKey],
                    'operation_info' => $opInfo
                ),
                $db
            );

            $queryConfig->Reset();
            $queryConfig->Condition = Operation::PrimaryColumnKey . ' = ' . $operation[Operation::PrimaryColumnKey];
            $queryConfig->RequiredFields = array(OperationItem::PrimaryColumnKey);

            /*Стираем старые элементы*/
            $oldItems = OperationItem::getRows($db, $queryConfig);

            OperationItem::Delete(
                array_map(
                    function($oldOperationItem){ return $oldOperationItem[OperationItem::PrimaryColumnKey];},
                    $oldItems
                ),
                $db
            );

        }
        /*Пишем новые элементы*/
        foreach ($selectedItems as $selectedItem) {
            OperationItem::Create(
                array(
                    Article::PrimaryColumnKey => $selectedItem[Article::PrimaryColumnName],
                    Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnKey],
                    'operation_count' => $selectedItem['count'],
                    'consignment_balance' => $selectedItem['count']
                ),
                $db
            );
        }
        return $operation;
    }

    /**
     * Метод обеспечивает сохранение операций, изменяющих текущий запас в момент создания операции
     * @param DataBase $db
     * @param View     $view
     * @param array    $operation
     * @param array    $selectedItems
     * @param array    $foundArticles
     * @return array    Возвращает $operation
     */
    private function Save_ChangeBalanceInCreation(
        DataBase $db,
        View $view,
        array $operation,
        array $selectedItems,
        array &$foundArticles
    ) : array {
        /*Проверяем, что не снимают больше, чем есть на запасе*/
        $queryConfig = new QueryConfig(
            UserGroup::PrimaryColumnKey . ' = :' . UserGroup::PrimaryColumnKey .
            ' AND ' . Article::PrimaryColumnKey . ' in (' . implode(',', array_keys($foundArticles)) . ')',
            array(Article::PrimaryColumnKey, 'balance'),
            array(UserGroup::PrimaryColumnKey => CURRENT_USER[UserGroup::PrimaryColumnName]),
            null,
            count($foundArticles)
        );
        $articleBalanceItems = ArticleBalance::getRows(
            $db,
            $queryConfig
        );
        $articleBalanceItems = (new linq($articleBalanceItems))
            ->toAssoc(
                function($balanceItem){ return $balanceItem[Article::PrimaryColumnKey];},
                function($balanceItem){ return $balanceItem['balance'];}
            )
            ->getData();
        if ($operation[Operation::PrimaryColumnName] != 0) {
            /*
             * Т.к. накладная изменяется, то нужно при проверке учесть и то количество,
             * которое зарезервировано в накладной
            */
            $queryConfig->Reset();
            $queryConfig->Condition = Operation::PrimaryColumnKey . ' = ' . $operation[Operation::PrimaryColumnName];
            $oldOperationItems = OperationItem::getRows(
                $db,
                $queryConfig
            );
            $operationReservedItems = (new linq($oldOperationItems))
                ->toAssoc(
                    function($operationItem){ return $operationItem[Article::PrimaryColumnKey];},
                    function($balanceItem){ return $balanceItem['operation_count'];}
                )
                ->getData();
            foreach ($operationReservedItems as $idArticle => $count) {
                $articleBalanceItems[$idArticle] += $count;
            }
        }
        $errors = (new linq($selectedItems))
            ->where(function($selectedItem) use ($articleBalanceItems) {
                return floatval($selectedItem['count']) >
                    (
                        array_key_exists($selectedItem[Article::PrimaryColumnName], $articleBalanceItems) ?
                            $articleBalanceItems[$selectedItem[Article::PrimaryColumnName]] :
                            0
                    );
            })
            ->getData();
        if ($errors) {
            $db->rollback();
            $view->errors = (new linq($errors))
                ->select(function($errorSelectedItem) use (&$foundArticles) {
                    return 'Артикул ' . $foundArticles[$errorSelectedItem[Article::PrimaryColumnName]]['article_name'] . ' имеет недостаточный запас для создания операции';
                })
                ->getData();
            $view->JSONView();
        }

        $queryConfig->Reset();

        if ($operation[Operation::PrimaryColumnName] == 0) {
            $queryConfig->Condition = '`state_name`=\'new\'';

            $operationNewState = OperationState::getFirstRow($db, $queryConfig);

            $operation = Operation::Create(
                array(
                    'create_datetime' => new DateTime(),
                    'id_user_group' => CURRENT_USER[UserGroup::PrimaryColumnName],
                    'id_operation_type' => intval($operation[OperationType::PrimaryColumnName]),
                    'id_operation_state' => $operationNewState[OperationState::PrimaryColumnKey],
                    'operation_info' => array(
                        'creator' => array(
                            'label' => 'Создал документ',
                            'caption' => CURRENT_USER['UserName'],
                            'value' => CURRENT_USER[User::PrimaryColumnName]
                        )
                    )
                ),
                $db,
                true
            );

        }
        else {
            $queryConfig->Condition = Operation::PrimaryColumnKey . ' = :' . Operation::PrimaryColumnKey;
            $queryConfig->Substitution = array(
                Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnName],
            );

            $operation = Operation::getFirstRow($db, $queryConfig);

            /*Дописываем инфу о модификации накладной*/
            $opInfo = $operation['operation_info'];
            if (!is_array($opInfo)) {
                $opInfo = array();
            }
            if (!array_key_exists('edited', $opInfo)) {
                $opInfo['edited'] = array(
                    'label' => 'Изменен',
                    'items' => array(),
                );
            }
            $opInfo['edited']['items'][] = array(
                User::PrimaryColumnKey => CURRENT_USER[User::PrimaryColumnName],
                'user_name' => CURRENT_USER['UserName'],
                'datetime' => date('Y-m-d H:i:s', time()),
            );

            Operation::Update(
                array(
                    Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnKey],
                    'operation_info' => $opInfo
                )
            );

            $queryConfig->Reset();
            $queryConfig->Condition = Operation::PrimaryColumnKey . ' = ' . $operation[Operation::PrimaryColumnKey];
            $queryConfig->RequiredFields = array(OperationItem::PrimaryColumnKey);

            /*Возвращаем старые элементы на баланс*/
            $oldItems = OperationItem::getRows(
                $db,
                $queryConfig
            );

            OperationItem::ReturnOnBalance(
                array_map(
                    function($oldOperationItem){ return $oldOperationItem[OperationItem::PrimaryColumnKey];},
                    $oldItems
                ),
                $db
            );

        }

        /*Пишем новые элементы*/
        $newItems = array();
        foreach ($selectedItems as $selectedItem) {
            $newItems[] = OperationItem::Create(
                array(
                    Article::PrimaryColumnKey => $selectedItem[Article::PrimaryColumnName],
                    Operation::PrimaryColumnKey => $operation[Operation::PrimaryColumnKey],
                    'operation_count' => $selectedItem['count'],
                    'consignment_balance' => 0, //Для расходных операций всех типов данное поле не нужно заполнять
                ),
                $db,
                true
            );
        }
        /*Резервируем необходимое количество*/
        OperationItem::ReserveFromBalance($newItems, $db);

        return $operation;
    }

    /**
     * Метод обеспечивает отображение в операции самой актуальной информации о связанных с операцией дополнительных объектах
     * @param View  $view
     * @param array $operation
     */
    private function loadLinkedDataItems(View $view, array $operation) {
        if (isset($operation['linked_data'])) {
            $linkedData = array();
            $db = new DataBase();
            $_model = explode('\\', Operation::class);
            foreach ($operation['linked_data'] as $modelName => $listId) {
                $_model[count($_model) - 1] = $modelName;
                $model = implode('\\', $_model);
                $listLinkedItems = $model::getRows(
                    $db,
                    new QueryConfig(
                        '`' . $model::PrimaryColumnKey . '` in (' . implode(',', $listId) . ')'
                    )
                );
                $linkedData[$model] = (new linq($listLinkedItems))
                    ->select(function($linkedItem){ return array_keys_CamelCase($linkedItem);})
                    ->getData();

            }
            $view->linkedData = $linkedData;
        }
    }

    private function defineCommonFormContext(View $view, string $OpTypeName, array $context) : array {
        $db = new DataBase();

        $queryConfig = new QueryConfig('operation_name=\'' . $OpTypeName . '\'');

        $operationType = OperationType::getFirstRow(
            $db,
            $queryConfig
        );

        $view->title = OperationType::getLabel($OpTypeName);
        $view->activeMenu = '/' . static::Name() . '/Index';
        $view->operationType = array_keys_CamelCase($operationType);
        $operation = null;
        if ($context['id'] > 0) {
            $operation = Operation::getFirstRow(
                $db,
                new QueryConfig(
                    Operation::PrimaryColumnKey . '= :' . Operation::PrimaryColumnKey,
                    null,
                    array(Operation::PrimaryColumnKey => $context['id'])
                )
            );
        }
        else {
            $operation = Operation::EmptyEntity(
                array(
                    OperationType::PrimaryColumnKey => $operationType[OperationType::PrimaryColumnKey],
                    OperationState::PrimaryColumnKey => OperationState::getFirstRow($db, new QueryConfig('state_name=\'new\''))[OperationState::PrimaryColumnKey],
                )
            );
        }
        $view->operation = array_keys_CamelCase($operation);

        define('OPERATION_VIEW_DIRECTORY', getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'operations');
        $view->Measures = (new linq(Measure::getRows($db)))
            ->toAssoc(
                function($measure){
                    return $measure['id_measure'];
                },
                function($measure){
                    return array_keys_CamelCase($measure);
                }
            )->getData();
        $view->dictionaryAction = ArticleBalanceController::Name() . '/ArticleBalanceDict/';
        return $operation;
    }

    private function getDefaultOrder() {
        return array('CreateDatetime desc');
    }
}