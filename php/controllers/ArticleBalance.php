<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 17.12.18
 * Time: 15:47
 */

namespace Astkon\Controllers;

use Astkon\Controller\Controller;
use Astkon\Model\ArticleCategory;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
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

    public function ArticleBalanceListAction($context) {
        $view = new View();
        $this->ListViewAction($view, ArticleCategory::class, __CLASS__);
        $view->generate();


//        if (isset($_GET['operation'])) {
//            $dataTableName = OperationType::DataTable;
//            $operationType = (new DataBase())->$dataTableName->getFirstRow('operation_name = :operation_name', null, array('operation_name' => $_GET['operation']));
//            if (!$operationType) {
//                $view->trace = 'Запрошена недопустимая операция';
//                $view->error(ErrorCode::PROGRAMMER_ERROR);
//                die();
//            }
//            switch ($operationType['operation_name']) {
//                case 'Income':
////                    Обозначение архивности нужно для того, чтобы не захламлять справочник артикулов, когда
////                    остатки по нему нулевые и поступлений не ожидается, по крайней мере некоторое время
//                    $condition = 'is_archive <> 1';
//                    break;
//                default:
//                    $condition = 'balance > 0 && is_archive <> 1';
//                    break;
//            }
//
//        }

    }

    public function ArticleBalanceDictAction($context) {
        $view = new View();
        $this->DictViewAction($view, ArticleCategory::class);
        $view->generate();
    }

}