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
        $view->buildObjects = (new DataBase())->build_object->getRows();
        $view->generate();
    }

    public function EditAction($context) {
        $view = new View();
        $view->BuildObject = (new DataBase())->build_object->getFirstRow('id_build_object = :id_build_object', null, array('id_build_object' => $context['id']));
        $view->generate();
    }
}