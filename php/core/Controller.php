<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 15.12.18
 * Time: 8:48
 */

namespace Astkon\Controller;

use Astkon\ErrorCode;
use Astkon\GlobalConst;
use Astkon\linq;
use Astkon\View\View;
use ReflectionMethod;

abstract class Controller
{
    /**
     * @var View
     */
    protected $view;

    protected function __construct()
    {
        $this->view = new View();
    }

    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context) {
        if (method_exists(static::class, $action)) {
            $reflectionMethod = new ReflectionMethod(static::class, $action);
            if ($reflectionMethod->isPublic() && !$reflectionMethod->isStatic()) {

                if (self::checkPermition($action)) {
                    (new static())->$action($context);
                }
                else {
                    $view = new View();
                    $view->error(ErrorCode::FORBIDDEN);
                }
            }
        }
        else {
            /*Возвращаем ошибку*/
            $view = new View();
            $view-> error(404);
        }

    }

    private static function checkPermition($action) {
        $reflectionMethod = new ReflectionMethod(static::class, $action);
        $permition = true;
        (new linq(explode(GlobalConst::NewLineChar,  $reflectionMethod->getDocComment())))
        ->for_each(function($line) use (&$permition){
            if (strpos($line, '@access') !== false) {

//                $currentUser = $_SESSION['CurrentUser'];
//                $role = strtolower($currentUser['Role']);
//                $line = str_replace('*', '', $line);
//                $line = str_replace('@access', '', $line);
//                $roles = (new linq(explode(',', $line)))
//                    ->select(function($item){ return explode(' ', preg_replace('/\s+/', ' ', trim($item)));})
//                    ->for_each(function($item) use (&$permition, $role){
//                        $r = strtolower($item[0]);
//                        $p = strtolower($item[1]) === 'allow';
//                        if (strtolower($item[0]) === 'all') {
//                            $permition = $p;
//                        }
//                        else  if ($r === $role) {
//                            $permition = $p;
//                        }
//
//                    })
//                    ->getData();
//                Метод пока не дописан из-за отсутствия реализации ролевой модели
            }
        });
        return $permition;
    }


    /**
     * @param array|null $backtrace
     * @return array
     */
    public static function ThisAction($backtrace = null) : array {
        if (!is_array($backtrace)) {
            $backtrace = (debug_backtrace(2, 2));
            $backtrace = $backtrace[1];
        }
        $classSegments = explode('\\',$backtrace['class']);
        $controller = preg_replace('/Controller$/i', '', array_pop($classSegments));
        $action = preg_replace('/Action$/i', '', $backtrace['function']);
        return array($controller, $action);
    }

    public static function Name() : ?string {
        $name = explode('\\', static::class);
        $name = array_pop($name);
        return preg_replace('/Controller$/i', '', $name);
    }
}
