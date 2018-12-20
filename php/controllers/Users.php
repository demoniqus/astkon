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
        $view->users = (new DataBase())->user->getRows();
        $view->generate();
    }

    public function EditAction($context) {
        $view = new View();
        $view->User = (new DataBase())->user->getFirstRow('id_user = :id_user', null, array('id_user' => $context['id']));
        $view->generate();
    }
}