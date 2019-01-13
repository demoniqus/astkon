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
use Astkon\DocComment;
use function Astkon\Lib\array_keys_CamelCase;
use function Astkon\Lib\Redirect;
use Astkon\linq;
use Astkon\Model\Model;
use Astkon\Model\Partial\UserPartial;
use Astkon\Model\User;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\View\View;
use ReflectionProperty;

class AuthController extends Controller
{
    const CurrentUserKey = 'CurrentUser';
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

    public function IndexAction($context) {
        if (isset($_SESSION[AuthController::CurrentUserKey])) {
            Redirect('Index', 'Index');
        }
        $view = new View();
        $view->validState = Model::ValidStateUndefined;
        $options = array();
        if (array_key_exists('submit', $_POST)) {
            $db = new DataBase();
            $usersList = $db->user->getRows('login = :login AND has_account = 1', null, array('login' => $_POST['Login']));
            $pwdFunction = DocComment::getDocCommentItem(new ReflectionProperty(UserPartial::class, 'Password'), 'save_wrapper');
            $pwdFunction = $pwdFunction ?? 'password';
            $password = $db->query('select ' . $pwdFunction . '(:password) as pwd', array('password' => $_POST['Password']));
            $password = $password[0]['pwd'];
            $user = (new linq($usersList))
                ->first(function($user) use ($password){ return $user['password'] === $password;});
            if ($user) {
                $user = User::getFirstRow($db, User::PrimaryColumnKey . ' = ' . $user[User::PrimaryColumnKey], null, null, null, 1);
                unset($user['password']);
                $_SESSION[AuthController::CurrentUserKey] = $GLOBALS[AuthController::CurrentUserKey] = array_keys_CamelCase($user);
                Redirect('Index', 'Index');
            }
            else {
                $view->validState = Model::ValidStateError;
            }
        }
        $view->options = $options;
        $view->generate();
    }

    public function LogoutAction($context) {
        $_SESSION[AuthController::CurrentUserKey] = $GLOBALS[AuthController::CurrentUserKey] = null;
        Redirect('Auth', 'Index');

    }

}