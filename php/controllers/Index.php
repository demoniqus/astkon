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
    public static function Run (string $action, array $context) {
        parent::Run($action, $context);
    }

    /**
     * @access all allow, admin deny, guest allow
     * @param array $context
     * @return string
     */
    public static function IndexAction(array $context) {
        echo '<img src="/4.jpg" />';
        echo '<pre style="background-color:rgb(' . mt_rand(50, 235) . ',' . mt_rand(50, 235) . ',' . mt_rand(50, 235) . ')">';
        echo 'Controller Index, action Index' . PHP_EOL;
        echo (new \DateTime())->format('Y-m-d H:i:s') . PHP_EOL;
        echo PHP_EOL;
        echo PHP_EOL;
        var_dump($_SERVER);
        echo PHP_EOL;
        echo PHP_EOL;
        var_dump(debug_backtrace());
        echo PHP_EOL;
        echo PHP_EOL;
        if (isset($_POST['login']) && isset($_POST['password'])) {
            $db = new DataBase();

        }
        else {
            return 'View';
        }
    }

    public static function AuthAction($context) {

    }


}