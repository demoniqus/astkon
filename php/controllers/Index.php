<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 15.12.18
 * Time: 15:11
 */

namespace Astkon\Controllers;

use Astkon\Controller\Controller;
use Astkon\DataBase;

class IndexController extends Controller
{
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    /**
     * @access all allow, admin deny, guest allow
     * @param array $context
     * @return string
     */
    public function IndexAction(array $context)
    {
        $this->view->generate();
    }

}

