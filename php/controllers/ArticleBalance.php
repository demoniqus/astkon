<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 17.12.18
 * Time: 15:47
 */

namespace Astkon\Controllers;

use Astkon\Controller\Controller;
use Astkon\ErrorCode;
use Astkon\linq;
use Astkon\Model\Article;
use Astkon\Model\ArticleBalance;
use Astkon\Model\BuildObject;
use Astkon\Model\ChangeBalanceMethod;
use Astkon\Model\Operation;
use Astkon\Model\OperationItem;
use Astkon\Model\OperationState;
use Astkon\Model\OperationType;
use Astkon\Model\User;
use Astkon\Model\UserGroup;
use Astkon\QueryConfig;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\View\TableViewConfig;
use Astkon\View\View;

class ArticleBalanceController extends Controller
{
    use ListView;
    use EditAction;
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    public function ArticleBalanceByAction(array $context) {
        $view = new View();

        $targetModelName = $_GET['model'];

        $modelName = explode('\\', User::class);
        array_pop($modelName);
        $modelName[] = $targetModelName;
        $model = implode('\\', $modelName);
        if (!class_exists($model)) {
            $view->error(ErrorCode::NOT_FOUND);
            die();
        }

        $articleBalance = ArticleBalance::getFirstRow(
            null,
            new QueryConfig(
                UserGroup::PrimaryColumnKey . ' = :' . UserGroup::PrimaryColumnKey .
                    ' AND ' . ArticleBalance::PrimaryColumnKey . ' = :' . ArticleBalance::PrimaryColumnKey
                ,
                array(Article::PrimaryColumnKey),
                array(
                    UserGroup::PrimaryColumnKey      => CURRENT_USER[UserGroup::PrimaryColumnName],
                    ArticleBalance::PrimaryColumnKey => intval($context['id']),
                )
            )
        );
        if (!$articleBalance) {
            $view->error(ErrorCode::NOT_FOUND);
            die();
        }

        $opStateNew = OperationState::getFirstRow(
            null,
            new QueryConfig(
                '`state_name` = \'new\'',
                array(OperationState::PrimaryColumnKey)
            )
        );

        $targetModelName = strtolower($targetModelName);
        $substitution = array(
            OperationState::PrimaryColumnKey => $opStateNew[OperationState::PrimaryColumnKey],
        );

        $queryConfig = new QueryConfig();
        $queryConfig->RequiredFields = array(OperationType::PrimaryColumnKey);

        switch ($targetModelName) {
            case strtolower(User::Name()):
                $view->linkedDataCaprionFieldName = 'user_name';

                $queryConfig->Condition = '`operation_name` = \'Reserving\'';

                $substitution[OperationType::PrimaryColumnKey] = OperationType::getFirstRow(null, $queryConfig)[OperationType::PrimaryColumnKey];
                break;
            case strtolower(BuildObject::Name()):
                $view->linkedDataCaprionFieldName = 'build_object_name';

                $queryConfig->Condition = '`operation_name` = \'Sale\'';

                $substitution[OperationType::PrimaryColumnKey] = OperationType::getFirstRow(null, $queryConfig)[OperationType::PrimaryColumnKey];
                break;
            default:
                $view->error(ErrorCode::FORBIDDEN);
                die();
        }
        $view->article = Article::GetByPrimaryKey($articleBalance[Article::PrimaryColumnKey]);

        $rows = OperationItem::getRows(
            null,
            new QueryConfig(
                implode(
                    ' AND ',
                    array(
                        '`' . Operation::DataTable . '`.`' . OperationState::PrimaryColumnKey . '` = :' . OperationState::PrimaryColumnKey,
                        '`' . Operation::DataTable . '`.`' . OperationType::PrimaryColumnKey . '` = :' . OperationType::PrimaryColumnKey,
                    )
                ),
                array(
                    'operation_count',
                    'measure_name',
                    'linked_data',
                    Operation::PrimaryColumnKey,
                ),
                $substitution,
                null,
                null
            ),
            1
        );

        $dictionary = (new linq($model::getRows()))
            ->toAssoc(
                function($dictItem) use ($model) {
                    return $dictItem[$model::PrimaryColumnKey];
                },
                function($dictItem) use ($model) {
                    /*Для большей безопасности, если работа пойдет с моделью User, удалим из нее поля логина и пароля*/
                    unset ($dictItem['password']);
                    unset ($dictItem['login']);

                    return $dictItem;
                }
            )
            ->getData();

        $rows = array_map(
            function($row) use ($dictionary) {
                if (!is_array($row['linked_data']) || !count($row['linked_data'])) {
                    return $row;
                }
                foreach ($row['linked_data'] as $modelName => $listId) {
                    $linkedData = array_map(
                        function($linkedDataItemId) use ($dictionary) {
                            return array_key_exists($linkedDataItemId, $dictionary) ? $dictionary[$linkedDataItemId] : $linkedDataItemId;
                        },
                        $listId
                    );
                    $row['linked_data'][$modelName] = $linkedData;
                    /*Пока с накладной связывается только один заранее известный тип данных*/
                    break;
                }
                return $row;

            },
            $rows
        );
        $view->rows = $rows;

        $view->generate();
    }


    public function ArticleBalanceListAction($context) {
        $view = new View();
        $options = array(
            array(
                'action' => '/ArticleBalance/ArticleBalanceBy?model=' . User::Name(),
                'click' => null,
                'icon' => '/tools-pict-time.png',
                'title' => 'Остатки во временном пользовании'
            ),
            array(
                'action' => '/ArticleBalance/ArticleBalanceBy?model=' . BuildObject::Name(),
                'click' => null,
                'icon' => '/building.png',
                'title' => 'Остатки по объектам'
            ),
        );

        $queryConfig = new QueryConfig();
        $queryConfig->Condition =implode(
            ' AND ',
            array(
                '`' . ArticleBalance::DataTable . '`.`' . UserGroup::PrimaryColumnKey . '` = :' . UserGroup::PrimaryColumnKey,
                'balance > 0'
            )
        );
        $queryConfig->Substitution = array(UserGroup::PrimaryColumnKey => CURRENT_USER[UserGroup::PrimaryColumnName]);

        $this->ListViewAction(
            $view,
            ArticleBalance::class,
            $options,
            $queryConfig,
            array(
                'CategoryName',
                'ArticleName',
                'IsWriteoff',
                'IsSaleable',
                'MeasureName',
                'Balance'
            )
        );
        $view->generate();
    }

    public function ArticleBalanceDictAction($context) {
        $view = new View();
        $queryConfig = new QueryConfig();
        $queryConfig->Condition = '`operation_name` = :operation_name';
        $queryConfig->Substitution = array('operation_name' => $_GET['operation']);

        $operationType = OperationType::getFirstRow(null, $queryConfig);
        if (!$operationType) {
            $view->error(ErrorCode::NOT_FOUND);
            die();
        }

        $queryConfig->Reset();//Offset, Limit и OrderBy будут настроены в DictViewAction

        $tableViewConfig = new TableViewConfig();
        $tableViewConfig->GETParams = array( //Параметр mode будет настроен в DictViewAction
            'operation' => $_GET['operation']
        );

        $changeBalanceMethod = ChangeBalanceMethod::GetByPrimaryKey($operationType[ChangeBalanceMethod::PrimaryColumnKey]);
        if ($changeBalanceMethod['method_name'] === 'in_fixation') {
            $queryConfig->RequiredFields = array_merge(
                Article::ModelPublicProperties(),
                array(
                    'MeasureName',
                    'CategoryName'
                )
            );

            $this->DictViewAction(
                $view,
                Article::class,
                $queryConfig,
                array(
                    'CategoryName',
                    'ArticleName',
                    'MeasureName',
                ),
                $tableViewConfig
            );
        }
        else {
            $conditions = array(
                '`' . ArticleBalance::DataTable . '`.`' . UserGroup::PrimaryColumnKey . '` = :' . UserGroup::PrimaryColumnKey,
                'balance > 0'
            );
            $substitution = array(
                UserGroup::PrimaryColumnKey => CURRENT_USER[UserGroup::PrimaryColumnName]
            );
            $requiredFields = array(
                'MeasureName',
                'Balance',
                'CategoryName',
            );
            switch (strtolower($operationType['operation_name'])) {
                case 'sale':
                    $requiredFields[] = 'IsSaleable';
                    $conditions[] = 'is_saleable = 1';
                    break;
                case 'writeoff':
                    $requiredFields[] = 'IsWriteoff';
                    $conditions[] = 'is_writeoff = 1';
                    break;
            }

            $queryConfig->Condition = implode(' AND ', $conditions);
            $queryConfig->RequiredFields = array_merge(
                Article::ModelPublicProperties(),
                $requiredFields
            );
            $queryConfig->Substitution = $substitution;

            $this->DictViewAction(
                $view,
                ArticleBalance::class,
                $queryConfig,
                array(
                    'CategoryName',
                    'ArticleName',
                    'MeasureName',
                    'Balance',
                ),
                $tableViewConfig
            );
        }
        $view->generate();
    }

    private function getDefaultOrder() {
        return array(/*'CategoryName', */'ArticleName');
    }

}