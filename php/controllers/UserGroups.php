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
use Astkon\Model\UserGroup;
use Astkon\QueryConfig;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\View\View;

class UserGroupsController extends Controller
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

    public function UserGroupsListAction($context) {
        $view = new View();
        $options = array();
        static::editOption($options, __CLASS__);
        $this->ListViewAction(
            $view,
            UserGroup::class,
            $options
        );
        $view->generate();
    }

    public function UserGroupsDictAction($context) {
        $view = new View();
        $this->DictViewAction(
            $view,
            UserGroup::class,
            null,
            array(
                'UserGroupName',
                'comment',
            )
        );
        $view->generate();
    }

    public function EditAction($context) {
        if (!CURRENT_USER['IsAdmin']) {
            $view = new View();
            $view->error(ErrorCode::FORBIDDEN);
        }
        $options = array();
        $entity = array();
        $model = UserGroup::class;
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
}