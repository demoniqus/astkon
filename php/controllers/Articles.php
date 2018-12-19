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

class ArticlesController extends Controller
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

    public function ArticlesListAction($context) {
        $view = new View();
        $view->articles = (new DataBase())->article->getRows();
        $view->generate();
    }

    public function EditAction($context) {
        $view = new View();
        $view->Article = (new DataBase())->article->getFirstRow('id_article = :id_article', null, array('id_article' => $context['id']));
        $view->generate();
    }
}