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
use Astkon\Model\BuildObject;
use Astkon\QueryConfig;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\Traits\ReserveView;
use Astkon\View\View;

class BuildObjectsController extends Controller
{
    use ListView;
    use EditAction;
    use ReserveView;
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    public function ReservesListAction($context) {
        $view = new View();
        $view->linkedDataCaprionFieldName = 'build_object_name';
        $this->ReservesList(
            $context,
            $view,
            BuildObject::class,
            'Sale'
        );
        $view->generate();

    }

    public function BuildObjectsListAction($context) {
        $view = new View();
        $options = array();
        static::editOption($options, __CLASS__);
        $options[] = array(
            'action' => '/' . self::Name() . '/ReservesList',
            'click' => null,
            'icon' => '/building.png',
            'title' => 'Зарезервировано на объекте'
        );
        $this->ListViewAction(
            $view,
            BuildObject::class,
            $options
        );
        $view->generate();
    }

    public function BuildObjectsDictAction($context) {
        $view = new View();
        $this->DictViewAction(
            $view,
            BuildObject::class,
            null,
            array(
                'BuildObjectName',
                'comment',
            )
        );
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $entity = array();
        $model = BuildObject::class;
        if (array_key_exists('submit', $_POST)) {
            $this->processPostData($entity, $options, $model, $context);

        }
        else {
            $dataTable = $model::DataTable;
            $entity = array_keys_CamelCase(
                (new DataBase())->
                $dataTable->
                getFirstRow(
                    new QueryConfig(
                        $model::PrimaryColumnKey . ' = :' . $model::PrimaryColumnKey,
                        null,
                        array(
                            $model::PrimaryColumnKey => $context['id'],
                        )
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

    private function getDefaultOrder() {
        return array('BuildObjectName');
    }
}