<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 17.12.18
 * Time: 17:22
 */

namespace Astkon\Controllers;

use Astkon\Controller\Controller;
use Astkon\DataBase;
use Astkon\View\View;

class MeasuresController extends  Controller
{
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    public function MeasuresListAction(array $context) {
        $view = new View();
        $db = new DataBase();
        $view->measures = $db->measure->getRows();
        $view->generate();
    }

    public function EditAction(array $context) {
        $id = $context['id'];
        $db = new DataBase();
        $view = new View();
        $view->Measure = $db->measure->getFirstRow('id_measure = :id_measure', null , array('id_measure' => $id));
        $view->generate();
    }

}