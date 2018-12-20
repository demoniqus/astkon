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

class PeopleController extends Controller
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

    public function PeopleListAction($context) {
        $view = new View();
        $view->peoples = (new DataBase())->people->getRows();
        $view->generate();
    }

    public function EditAction($context) {
        $view = new View();
        $view->People = (new DataBase())->people->getFirstRow('id_people = :id_people', null, array('id_people' => $context['id']));
        $view->generate();
    }
}