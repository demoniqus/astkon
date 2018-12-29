<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 15.12.18
 * Time: 8:48
 */

namespace Astkon\Controller;

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
            if (self::checkPermition($action)) {
                (new static())->$action($context);
            }
            else {
                /*Возвращаем ошибку*/
            }
        }
        else {
            /*Возвращаем ошибку*/
            echo '<p style="font-size: 8em;">Запрошенный метод в данном классе не существует';
        }

    }

    private static function checkPermition($action) {
        $reflectionMethod = new ReflectionMethod(static::class, $action);
        $permition = true;
        (new linq(explode(GlobalConst::NewLineChar,  $reflectionMethod->getDocComment())))
        ->for_each(function($line) use (&$permition){
            if (strpos($line, '@access') !== false) {

                $currentUser = $_SESSION['CurrentUser'];
                $role = strtolower($currentUser['Role']);
                $line = str_replace('*', '', $line);
                $line = str_replace('@access', '', $line);
                $roles = (new linq(explode(',', $line)))
                    ->select(function($item){ return explode(' ', preg_replace('/\s+/', ' ', trim($item)));})
                    ->for_each(function($item) use (&$permition, $role){
                        $r = strtolower($item[0]);
                        $p = strtolower($item[1]) === 'allow';
                        if (strtolower($item[0]) === 'all') {
                            $permition = $p;
                        }
                        else  if ($r === $role) {
                            $permition = $p;
                        }

                    })
                    ->getData();
//                Метод пока не дописан из-за отсутствия реализации ролевой модели
            }
        });
        return $permition;
    }

//    protected function DictAction(View $view, $model) {
//        $listItemOptions = [];
//        if (array_key_exists('mode', $_GET) && trim(strtolower($_GET['mode'])) === 'multiple') {
//            $listItemOptions[] = array(
//                'action' => null,
//                'click' => htmlspecialchars('DictionaryItemChangeCheckedState($(this).find("img:first"))'),
//                'icon' => '/checkbox-unchecked.png',
//                'title' => 'Отметить элемент'
//            );
//        }
//        $listItemOptions[] = array(
//            'action' => null,
//            'click' => 'DictionarySelector.setValue(\'' .
//                $_POST['dialogId'] . '\', [JSON.parse($(this).parents(\'tr:first\').get(0).dataset.item)],' .
//                htmlspecialchars(json_encode($model::ReferenceDisplayedKeys())) . ')',
//            'icon' => '/icon-next.png',
//            'title' => 'Выбрать элемент'
//        );
//        $view->listItemOptions = $listItemOptions;
//        $view->setHeaderTemplate(null);
//        $view->setFooterTemplate(null);
//        $this->configureListView($view, $model);
//    }
//
//    protected function configureListView(View $view, $model) {
//        $dataTable = $model::DataTable;
//        $view->modelConfig = $model::getConfigForListView();
//        $rows = array_map(
//            function($row){
//                return array_keys_CameCase($row);
//            },
//            (new DataBase())->$dataTable->getRows()
//        );
//        $model::decodeForeignKeys($rows);
//        $view->listItems = $rows;
//    }

    /**
     * @return array
     */
    public static function ThisAction() : array {
        $backtrace = (debug_backtrace(2, 2));
        $backtrace = $backtrace[1];
        $classSegments = explode('\\',$backtrace['class']);
        $controller = preg_replace('/Controller$/i', '', array_pop($classSegments));
        $action = preg_replace('/Action$/i', '', $backtrace['function']);
        return array($controller, $action);
    }

}
