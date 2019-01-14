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
use function Astkon\Lib\array_keys_CamelCase;
use Astkon\Model\People;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\View\View;

class PeopleController extends Controller
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

    public function PeopleListAction($context) {
        $view = new View();
        $options = array();
        static::editOption($options, __CLASS__);
        $this->ListViewAction(
            $view,
            People::class,
            $options
        );
        $view->generate();
    }

    public function PeopleDictAction($context) {
        $view = new View();
//        $pageId = isset($context['id']) ? intval($context['id']) : 0;
//        $pageSize = 5;
        $this->DictViewAction($view, People::class);
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $entity = array();
        $model = People::class;
        if (array_key_exists('submit', $_POST)) {
            $this->processPostData($entity, $options, $model, $context);

        }
        else {
            $dataTable = $model::DataTable;
            $entity = array_keys_CamelCase(
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
        $controllerName = self::Name();
        $options['backToList'] = '/' . $controllerName . '/' . $controllerName . 'List';
        $view = new View();
        $view->Entity = $entity;
        $view->options = $options;
        $view->Model = $model;
        $view->generate();
    }
}