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
use function Astkon\Lib\Redirect;
use Astkon\Model\BuildObject;
use Astkon\Model\Model;
use Astkon\View\View;

class BuildObjectsController extends Controller
{
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

//    public function IndexAction() {
//        (new View())->generate();
//    }

    public function BuildObjectsListAction($context) {
        $view = new View();
        $view->listItemOptions = array(
            array(
                'action' => '/BuildObjects/Edit',
                'click' => null,
                'icon' => '/icon-edit.png',
                'title' => 'Редактировать'
            )
        );
        $view->modelConfig = BuildObject::getConfigForListView();
        $view->listItems = array_map(
            function($row){
                return array_keys_CameCase($row);
            },
            (new DataBase())->build_object->getRows()
        );
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $buildObject = array();
        if (array_key_exists('submit', $_POST)) {
            $inputValues = array_filter($_POST, function($v, $k){ return $k !== 'submit'; }, ARRAY_FILTER_USE_BOTH);
            $res = BuildObject::SaveInstance($inputValues);
            if (isset($res['@error'])) {
                //Заполняем все значения обратно
                $buildObject = $inputValues;
                //Выделяем поля, в которых возникла ошибка, либо выводим общее сообщение об ошибке, если не удалось определить конктретное поле
                $options['validation'] = array(
                    'state' => Model::ValidStateError,
                    'message' => 'Ошибка при сохранении данных'
                );
                if (isset($res['expected_error_column_name'])) {
                    $message = isset($res['err_code_explain']) ? $res['err_code_explain'] : 'Недопустимое значение';
                    $options['validation']['fields'] =  array();
                    $errorColumns = explode(',', $res['expected_error_column_name']);
                    foreach ($errorColumns as $errorColumn) {
                        $options['validation']['fields'][$errorColumn] = array(
                            'state' => Model::ValidStateError,
                            'message' => $message
                        );
                    }
                    foreach (array_keys($buildObject) as $fieldName) {
                        if (!array_key_exists($fieldName, $options['validation']['fields'])) {
                            $options['validation']['fields'][$fieldName] = array(
                                'state' => Model::ValidStateOK
                            );
                        }
                    }
                }
            }
            else  {
                if ($_POST[BuildObject::PKName()] == 0) {
                    /*Нужно сменить URL на вновь созданный элемент*/
                    list($controller, $action) = self::ThisAction();
                    Redirect(
                        $controller, $action, $res[DataBase::camelCaseToUnderscore(BuildObject::PKName())]
                    );
                }
                else {
                    $options['validation'] = array(
                        'state' => Model::ValidStateOK,
                        'message' => 'Данные успешно сохранены'
                    );
                    $buildObject = array_keys_CameCase(
                        (new DataBase())->
                        build_object->
                        getFirstRow('id_build_object = :id_build_object', null, array('id_build_object' => $context['id']))
                    );
                }
            }

        }
        else {
            $buildObject = array_keys_CameCase(
                (new DataBase())->
                build_object->
                getFirstRow('id_build_object = :id_build_object', null, array('id_build_object' => $context['id']))
            );
        }
        $view = new View();
        $view->BuildObject = $buildObject;
        $view->options = $options;
        $view->generate();
    }
}