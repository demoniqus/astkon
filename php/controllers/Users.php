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
use function Astkon\Lib\array_keys_CamelCase;
use function Astkon\Lib\Redirect;
use Astkon\Model\Model;
use Astkon\Model\User;
use Astkon\Model\UserGroup;
use Astkon\Traits\ListView;
use Astkon\Traits\ReserveView;
use Astkon\View\View;

class UsersController extends Controller
{
    use ListView;
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
        $view->linkedDataCaprionFieldName = 'user_name';

        $this->ReservesList(
            $context,
            $view,
            User::class,
            'Reserving'
        );
        $view->generate();
    }

    public function UsersListAction($context) {
        $view = new View();
        $options = array();
        static::editOption($options, __CLASS__);
        $options[] = array(
            'action' => '/' . self::Name() . '/ReservesList',
            'click' => null,
            'icon' => '/tools-pict-time.png',
            'title' => 'Артикулы во временном пользовании'
        );
        $this->ListViewAction(
            $view,
            User::class,
            $options,
            null,
            array(
                User::PrimaryColumnName,
                'UserName',
                'UserGroupName',
                'HasAccount',
                'IsAdmin',
                UserGroup::PrimaryColumnName,
            ),
            null,
            null,
            null,
            array(
                User::PrimaryColumnName,
                'UserName',
                'UserGroupName',
                'HasAccount',
                'IsAdmin'
            )
        );
        $view->generate();
    }

    public function UsersDictAction($context) {
        $view = new View();
        $condition = null;
        $substitution = null;
//        $pageId = isset($context['id']) ? intval($context['id']) : 0;
//        $pageSize = 5;

        $this->DictViewAction(
            $view,
            User::class,
            $condition,
            array(
                User::PrimaryColumnKey,
                'user_name',
                'has_account',
            ),
            $substitution,
            null,
            null,
            array(
                User::PrimaryColumnKey,
                'UserName',
                'HasAccount',
            )
        );
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $user = array();
        if (!CURRENT_USER['IsAdmin']) {
            $view = new View();
            $view->error(ErrorCode::FORBIDDEN);
        }
        if (array_key_exists('submit', $_POST)) {
            $inputValues = array_filter($_POST, function($v, $k){ return $k !== 'submit'; }, ARRAY_FILTER_USE_BOTH);
            if ($inputValues[User::PrimaryColumnName] == 0) {
                if (!$inputValues['Password']) {
                    //Ошибка
                }
                if ($inputValues['Password'] !== $inputValues['PasswordConfirm']) {
                    //Ошибка
                }

            }
            else {
                /*
                 * Если парольное поле НЕ ЗАПОЛНЕНО, тогда именно это поле не обновляем в БД
                 */
                if (
                    $inputValues['Password'] === $inputValues['PasswordConfirm'] &&
                    $inputValues['Password'] === ''

                ) {
                    unset ($inputValues['Password']);
                }
            }
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
                if ($_POST[User::PrimaryColumnName] == 0) {
                    /*Нужно сменить URL на вновь созданный элемент*/
                    list($controller, $action) = self::ThisAction();
                    Redirect(
                        $controller, $action, $res[DataBase::camelCaseToUnderscore(User::PrimaryColumnName)]
                    );
                }
                else {
                    $options['validation'] = array(
                        'state' => Model::ValidStateOK,
                        'message' => 'Данные успешно сохранены'
                    );
                    $user = array_keys_CamelCase(
                        (new DataBase())->
                        user->
                        getFirstRow('id_user = :id_user', null, array('id_user' => $context['id']))
                    );
                    unset($user['Password']);

                }
            }

        }
        else {
            $user = array_keys_CamelCase(
                (new DataBase())->
                user->
                getFirstRow('id_user = :id_user', null, array('id_user' => $context['id']))
            );
            unset($user['Password']);
        }
        $controllerName = self::ThisAction()[0];
        $options['backToList'] = '/' . $controllerName . '/' . $controllerName . 'List';
        $view = new View();
        $view->User = $user;
        $view->options = $options;
        $view->generate();
    }

    /**
     * Метод отвечает за инициализацию проекта и создания суперпользователя.
     * Вызываться должен только однажды, пока в БД нет еще пользователей.
     * В остальных случаях создание суперадмина должно осуществляться через БД
     * @param $context
     */
    public function InitProjectAction($context) {
        $view = new View();
        $db = new DataBase();
        $dataTable =  User::DataTable;
        if ($db->$dataTable->getFirstRow() !== null) {
            $view->error(ErrorCode::FORBIDDEN);
            die();
        }

        $options = array();
        $entity = array(
            'Login' => '',
            'Password' => '',
            'PasswordConfirm' => '',
        );
        if (array_key_exists('submit', $_POST)) {
            $login = $_POST['Login'];
            $password = $_POST['Password'];
            $pasConfirm = $_POST['PasswordConfirm'];
            $options['validation'] = array(
                'fields' => array()
            );
            if (!$login) {
                $options['validation']['fields']['Login'] = array(
                    'state' => Model::ValidStateError,
                    'message' => 'Логин не может быть пустым'
                );
            }

            if (!$password) {
                $options['validation']['fields']['Password'] = array(
                    'state' => Model::ValidStateError,
                    'message' => 'Пароль не может быть пустым'
                );
            }

            if ($password != $pasConfirm) {
                $options['validation']['fields']['Password'] = array(
                    'state' => Model::ValidStateError,
                    'message' => ''
                );
                $options['validation']['fields']['PasswordConfirm'] = array(
                    'state' => Model::ValidStateError,
                    'message' => 'Пароли не совпадают'
                );
            }
            if (count($options['validation']['fields']) > 0) {
                $entity = $_POST;
            }
            else {
                $db->beginTransaction();
                if (false === $db->query('insert into `user_group` set `user_group_name`=\'Администраторы\', `comment`=\'Группа пользователей, имеющих административные права\'')) {
                    $options['validation']['state'] = Model::ValidStateError;
                    $db->rollback();
                }
                else if (
                    false === $db->query(
                        'insert into `user` set `login` = :Login, `password` = PASSWORD(:Password), `has_account` = 1, `user_name` = \'SuperAdmin\', `is_admin` = 1, `id_user_group` = (select max(`id_user_group`) from `user_group`)',
                        array(
                            'Login' => $login,
                            'Password' => $password
                        )
                    )
                ) {
                    $options['validation']['state'] = Model::ValidStateError;
                    $db->rollback();

                }
                else {
                    $db->query('select * from `user` where `id_user` = (select max(`id_user`) from `user`)');
                    $result = $db->commit();
                    if (false === $result) {
                        $options['validation']['state'] = Model::ValidStateError;
                    }
                    else {
                        AuthController::Run('IndexAction', array());
                        return;
                    }
                }
            }
        }
        $view->Entity = $entity;
        $view->options = $options;
        $view->generate();

    }
}