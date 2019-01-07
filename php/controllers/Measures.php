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
use function Astkon\Lib\array_keys_CameCase;
use Astkon\Model\Measure;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\View\View;

class MeasuresController extends Controller
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

    public function MeasuresListAction($context) {
        $view = new View();
        $options = array();
        static::editOption($options, __CLASS__);
        $this->ListViewAction(
            $view,
            Measure::class,
            $options
        );
        $view->generate();
//        $pageId = isset($context['id']) ? intval($context['id']) : 0;
//        $pageSize = 5;
    }

    public function MeasuresDictAction($context) {
        $view = new View();
//        $pageId = isset($context['id']) ? intval($context['id']) : 0;
//        $pageSize = 5;
        $this->DictViewAction($view, Measure::class);
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $entity = array();
        $model = Measure::class;
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