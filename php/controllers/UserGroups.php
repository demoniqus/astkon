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
use Astkon\ErrorCode;
use function Astkon\Lib\array_keys_CameCase;
use function Astkon\Lib\Redirect;
use Astkon\Model\Article;
use Astkon\Model\Model;
use Astkon\Model\OperationType;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\View\View;

class ArticlesController extends Controller
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

    public function IndexAction() {
        (new View())->generate();
    }

    public function ArticlesListAction($context) {
        $view = new View();
        $this->ListViewAction($view, Article::class, __CLASS__);
        $view->generate();
    }

    public function ArticlesDictAction($context) {
        $view = new View();
        $condition = null;
        $substitution = null;
//        $pageId = isset($context['id']) ? intval($context['id']) : 0;
//        $pageSize = 5;
        if (isset($_GET['operation'])) {
            $dataTableName = OperationType::DataTable;
            $operationType = (new DataBase())->$dataTableName->getFirstRow('operation_name = :operation_name', null, array('operation_name' => $_GET['operation']));
            if (!$operationType) {
                $view->trace = 'Запрошена недопустимая операция';
                $view->error(ErrorCode::PROGRAMMER_ERROR);
                die();
            }
            switch ($operationType['operation_name']) {
                case 'Income':
//                    Обозначение архивности нужно для того, чтобы не захламлять справочник артикулов, когда
//                    остатки по нему нулевые и поступлений не ожидается, по крайней мере некоторое время
                    $condition = 'is_archive <> 1';
                    break;
                default:
                    $condition = 'balance > 0 && is_archive <> 1';
                    break;
            }

        }
        $this->DictViewAction($view, Article::class, $condition, null, $substitution);
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $article = array();
        if (array_key_exists('submit', $_POST)) {
            $this->processPostData($article, $options, Article::class, $context);

        }
        else {
            $article = array_keys_CameCase(
                (new DataBase())->
                article->
                getFirstRow('id_article = :id_article', null, array('id_article' => $context['id']))
            );
        }
        $controllerName = self::ThisAction()[0];
        $options['backToList'] = '/' . $controllerName . '/' . $controllerName . 'List';
        $view = new View();
        $view->Article = $article;
        $view->options = $options;
        $view->generate();
    }
}