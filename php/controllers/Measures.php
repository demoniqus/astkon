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

class MeasuresController extends Controller
{
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

    public function MeasuresListAction($context) {
        $view = new View();
        $view->measures = (new DataBase())->measure->getRows();
        $view->generate();
    }

    public function EditAction($context) {
        $view = new View();
        $view->Measure = (new DataBase())->measure->getFirstRow('id_measure = :id_measure', null, array('id_measure' => $context['id']));
        $view->generate();
    }
}