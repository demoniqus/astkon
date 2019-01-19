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
use Astkon\QueryConfig;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\Traits\ReserveView;
use Astkon\View\View;

class UsersController extends Controller
{
    use EditAction;
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
        $options[] = array(
            'action' => null,
            'click' => 'if (confirm(\'Вы уверены, что хотите удалить пользователя?\')) {window.location.href=\'/Users/Delete/@IdUser\';}',
            'icon' => '/trash-empty-icon.png',
            'title' => 'Удаление пользователя'
        );


        $queryConfig = new QueryConfig();
        $queryConfig->Condition = '`is_delete` <> 1';
        $queryConfig->RequiredFields = array(
            User::PrimaryColumnName,
            'UserName',
            'UserGroupName',
            'HasAccount',
            'IsAdmin',
            UserGroup::PrimaryColumnName,
        );

        $this->ListViewAction(
            $view,
            User::class,
            $options,
            $queryConfig,
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
        $condition = '`is_delete` <> 1';
        $substitution = null;

        $queryConfig = new QueryConfig();
        $queryConfig->Condition = $condition;
        $queryConfig->RequiredFields = array(
            User::PrimaryColumnKey,
            'user_name',
            'has_account',
        );
        $queryConfig->Substitution = $substitution;

        $this->DictViewAction(
            $view,
            User::class,
            $queryConfig,
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
        $entity = array();
        if (!CURRENT_USER['IsAdmin']) {
            $view = new View();
            $view->error(ErrorCode::FORBIDDEN);
        }

        $queryConfig = new QueryConfig(
            '`' . User::PrimaryColumnKey . '` = :' . User::PrimaryColumnKey,
            null,
            array(
                User::PrimaryColumnKey => $context['id'],
            )
        );

        if (array_key_exists('submit', $_POST)) {
            $res = array();
            $inputValues = array_filter($_POST, function($v, $k){ return $k !== 'submit'; }, ARRAY_FILTER_USE_BOTH);
            if ($inputValues[User::PrimaryColumnName] == 0) {
                if (!$inputValues['Password']) {
                    //Ошибка
                    $res['@error'] = true;
                    $res['errors'] = array(array(
                        'expected_error_column_name' => 'Password',
                        'err_code_explain' => 'Необходимо задать пароль',
                    ));
                }
                if ($inputValues['Password'] !== $inputValues['PasswordConfirm']) {
                    //Ошибка
                    $res['@error'] = true;
                    $res['errors'] = array(array(
                        'expected_error_column_name' => 'Password,PasswordConfirm',
                        'err_code_explain' => 'Пароли не совпадают',
                    ));
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
            if (isset ($res) && isset($res['@error'])) {
                $entity = array_filter($_POST, function($v, $k){ return $k !== 'submit'; }, ARRAY_FILTER_USE_BOTH);
                $this->processErrors($entity, $options, $res['errors']);
            }
            else {
                $this->processPostData($entity, $options, User::class, $context, $inputValues);
                unset($entity['Password']);
                unset($entity['PasswordConfirm']);
            }

        }
        else {
            $entity = array_keys_CamelCase(
                (new DataBase())->
                user->
                getFirstRow($queryConfig)
            );
            unset($entity['Password']);
        }
        $controllerName = self::Name();
        $options['backToList'] = '/' . $controllerName . '/' . $controllerName . 'List';
        $view = new View();
        $view->User = $entity;
        $view->options = $options;
        $view->generate();
    }

    public function DeleteAction(array $context)  {
        if (!CURRENT_USER['IsAdmin']) {
            $view = new View();
            $view->error(ErrorCode::FORBIDDEN);
        }

        $user = User::GetByPrimaryKey(intval($context['id']));
        if ($user) {
            User::Update(array(
                User::PrimaryColumnKey => $user[User::PrimaryColumnKey],
                'is_delete'            => true,
            ));
        }

        Redirect(static::Name(), 'UsersList');
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