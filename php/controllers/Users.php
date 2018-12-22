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
use Astkon\Model\Measure;
use Astkon\Model\Model;
use Astkon\Model\User;
use Astkon\View\View;

class UsersController extends Controller
{
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    public function UsersListAction($context) {
        $view = new View();
        $view->listItemOptions = array(
            array(
                'action' => '/Users/Edit',
                'click' => null,
                'icon' => '/icon-edit.png',
                'title' => 'Редактировать'
            )
        );
        $view->modelConfig = User::getConfigForListView();
        $view->listItems = array_map(
            function($row){
                return array_keys_CameCase($row);
            },
            (new DataBase())->user->getRows()
        );
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $user = array();
        if (array_key_exists('submit', $_POST)) {
            $inputValues = array_filter($_POST, function($v, $k){ return $k !== 'submit'; }, ARRAY_FILTER_USE_BOTH);
            $res = User::SaveInstance($inputValues);
            if (isset($res['@error'])) {
                //Заполняем все значения обратно
                $user = $inputValues;
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
                    foreach (array_keys($user) as $fieldName) {
                        if (!array_key_exists($fieldName, $options['validation']['fields'])) {
                            $options['validation']['fields'][$fieldName] = array(
                                'state' => Model::ValidStateOK
                            );
                        }
                    }
                }
            }
            else  {
                if ($_POST[User::PKName()] == 0) {
                    /*Нужно сменить URL на вновь созданный элемент*/
                    list($controller, $action) = self::ThisAction();
                    Redirect(
                        $controller, $action, $res[DataBase::camelCaseToUnderscore(User::PKName())]
                    );
                }
                else {
                    $options['validation'] = array(
                        'state' => Model::ValidStateOK,
                        'message' => 'Данные успешно сохранены'
                    );
                    $user = array_keys_CameCase(
                        (new DataBase())->
                        user->
                        getFirstRow('id_user = :id_user', null, array('id_user' => $context['id']))
                    );
                }
            }

        }
        else {
            $user = array_keys_CameCase(
                (new DataBase())->
                user->
                getFirstRow('id_user = :id_user', null, array('id_user' => $context['id']))
            );
        }
        $view = new View();
        $view->User = $user;
        $view->options = $options;
        $view->generate();
    }
}