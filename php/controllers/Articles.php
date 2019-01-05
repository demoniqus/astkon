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
use Astkon\Model\Article;
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

        $this->DictViewAction($view, Article::class, $condition, null, $substitution);
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $entity = array();
        $model = Article::class;
        if (array_key_exists('submit', $_POST)) {
            $this->processPostData($entity, $options, $model, $context);

        }
        else {
            $dataTable = $model::DataTable;
            $entity = array_keys_CameCase(
                (new DataBase())->
                $dataTable->
                getFirstRow(
                    $model::PrimaryColumnKey . ' = :' . $model::PrimaryColumnKey,
                    null, array(
                        $model::PrimaryColumnKey => $context['id']
                    )
                )
            );
        }
        $controllerName = self::ThisAction()[0];
        $options['backToList'] = '/' . $controllerName . '/' . $controllerName . 'List';
        $view = new View();
        $view->Entity = $entity;
        $view->options = $options;
        $view->Model = $model;
        $view->generate();
    }
}