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
use function Astkon\Lib\array_keys_CameCase;
use Astkon\linq;
use Astkon\Model\Operation;
use Astkon\Model\OperationType;
use Astkon\View\View;

class OperationsController extends Controller
{
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

    public function OperationsListAction(array $context) {
        $view = new View();
        $db = new DataBase();
        $IdOperationType = intval($context['id']);
        $operation_type = $db->operation_type->getFirstRow('id_operation_type = :id_operation_type', null, array('id_operation_type' => $IdOperationType));
        $view->operation_type = $operation_type;
        $view->listItems = $db->operation->getRows(
            'id_operation_type = :id_operation_type',
            array('id_operation', 'id_operation_state', 'create_datetime', 'fix_datetime', 'operation_info'),
            array('id_operation_type' => $IdOperationType)
        );
        $listItemsOption = array(
            array(
                'action' => '/Operations/Detail',
                'click' => null,
                'icon' => '/icon-view.png',
                'title' => 'Просмотр'
            )
        );
        if ($operation_type['operation_name'] === 'Reserving') {
            $listItemsOption[] = array(
                'action' => '/Operations/Detail',
                'click' => null,
                'icon' => '/return.jpeg',
                'title' => 'Вернуть в запас'
            );
            $listItemsOption[] = array(
                'action' => '/Operations/Detail',
                'click' => null,
                'icon' => '/trash.jpg',
                'title' => 'Списать'
            );
        }
        $view->listItemOptions = $listItemsOption;
        $modelConfig = Operation::getConfigForListView(array('IdOperationType'));
//        var_dump($modelConfig);
        $view->modelConfig = $modelConfig;
        $view->generate();
    }


    private function getOperationsList(string $operationName) {
        $db = new DataBase();
        $operationType = $db->operation_type->getFirstRow('operation_name = \'' . $operationName . '\'', array('id_operation_type'));
        return $db->operation->getRows('id_operation_type = ' . $operationType['id_operation_type']);
    }

    /**
     * Форма прихода на баланс
     * @param array $context
     */
    public function IncomeFormAction(array $context){
        $OpTypeName = 'Income';
        $view = new View();
        $this->defineCommonFormContext($view, $OpTypeName);
        $view->generate();

    }

    /**
     * Форма расхода с баланса
     * @param array $context
     */
    public function SaleFormAction(array $context) {
        $OpTypeName = 'Sale';
        $view = new View();
        $this->defineCommonFormContext($view, $OpTypeName);
        $view->generate();
    }

    /**
     * Форма безвозвратного безвозмездного списания
     * @param array $context
     */
    public function WriteOffFormAction(array $context) {
        $OpTypeName = 'WriteOff';
        $view = new View();
        $this->defineCommonFormContext($view, $OpTypeName);
        $view->generate();
    }

    /**
     * Форма передачи во временное безвозмездное пользование
     * @param array $context
     */
    public function ReservingFormAction(array $context) {
        $OpTypeName = 'Reserving';
        $view = new View();
        $this->defineCommonFormContext($view, $OpTypeName);
        $view->generate();
    }

    public function InventoryFormAction(array $context) {
        $OpTypeName = 'Inventory';
        $view = new View();
        $this->defineCommonFormContext($view, $OpTypeName);
        $view->generate();
    }

    private function defineCommonFormContext(View $view, string $OpTypeName) {
        $view->title = OperationType::getLabel($OpTypeName);
        $view->operationType = array_keys_CameCase((new DataBase())->operation_type->getFirstRow('operation_name=\'' . $OpTypeName . '\''));
        define('OPERATION_VIEW_DIRECTORY', getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'operations');
        $view->Measures = (new linq((new DataBase())->measure->getRows()))
            ->toAssoc(
                function($measure){
                    return $measure['id_measure'];
                },
                function($measure){
                    return array_keys_CameCase($measure);
                }
            )->getData();
        $view->dictionaryAction = 'Articles/ArticlesDict';
    }
}