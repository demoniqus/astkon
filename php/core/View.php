<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 16.12.18
 * Time: 17:03
 */

namespace Astkon\View;

use Astkon\GlobalConst;

define(__NAMESPACE__ . '\FORM_EDIT_FIELDS_TEMPLATES', getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields');



class View
{
    /**
     * @var array
     */
    private $variables = array();
    /**
     * @var string
     */
    private $defHeaderTemplate;
    /**
     * @var string
     */
    private $defFooterTemplate;

    public function __construct()
    {
        $this->defHeaderTemplate = getcwd() . DIRECTORY_SEPARATOR . GlobalConst::DefHeaderView;
        $this->defFooterTemplate = getcwd() . DIRECTORY_SEPARATOR . GlobalConst::DefFooterView;
        $backtraceLimit = 2;
        $backtrace = debug_backtrace(2, $backtraceLimit);
        if (count($backtrace) === $backtraceLimit) {
            list($controller, $action) = self::get_C_A_from_backtrace($backtrace[1]);
            $this->variables['activeMenu'] = '/' . $controller . '/' . $action;
        }
    }

    /**
     * @param null|string|array $template - наименование шаблона для вывода.
     *          null - соответствует текущему Controller->Action. По умолчанию дополняется дефолтными header и footer
     *          string - в формате 'Controller/Action' (Index/Authenticate). При выводе не дополняется никакими дополнительными шаблонами за исключением подключенных внутри
     *          array - может содержать как элементы типа string, так и пары Controler => Action
     */
    public function generate($template = null) {
        $controller = null;
        $action = null;
        $templates = array();

        if ($template === null) {
            $backtrace = debug_backtrace(2, 2);
            list($controller, $action) = self::get_C_A_from_backtrace($backtrace[1]);
            $templates[] = $this->defHeaderTemplate;
            $templates[] = getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . "$controller/$action.php";
            $templates[] = $this->defFooterTemplate;
        }
        else if (gettype($template) === gettype('')) {
            list($controller, $action) = explode('/', $template);
            $controller = strtolower($controller);
            $action = strtolower($action);
            $templates[] = getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . "$controller/$action.php";
        }
        else if (gettype($template) === gettype(array())) {
            array_walk($template, function($item) use ($templates) {
                $controller = null;
                $action = null;
                if (gettype($item) === gettype('')) {
                    list($controller, $action) = explode('/', $item);
                    $controller = strtolower($controller);
                    $action = strtolower($action);
                }
                else if (gettype($item) === gettype(array())) {
                    $controller = array_key_first($item);
                    $action = $item[$controller];
                }
                $templates[] = getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . "$controller/$action.php";
            });
        }
        foreach ($this->variables as $varName => $varValue) {
            $$varName = $varValue;
        }
        foreach ($templates as $template) {
            if ($template) {
                require $template;
            }
        }
    }

    public function error(int $code) {
        $templateName = getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . $code . '.php';
        if (!file_exists($templateName)) {
            $code = 404;
            $templateName = getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . $code . '.php';
        }
        foreach ($this->variables as $varName => $varValue) {
            $$varName = $varValue;
        }
        require_once $this->defHeaderTemplate;
        require_once $templateName;
        require_once $this->defFooterTemplate;
    }

    /**
     * Получает наименования вызвавшего Controller'а и Action'а
     * @param array $backtrace
     * @return array
     */
    protected static function get_C_A_from_backtrace(array $backtrace) {
        $controller = explode('\\', $backtrace['class']);
        $controller = $controller[count($controller) - 1];
        $controller = substr($controller, 0, strlen($controller) - strlen('Controller'));
        $controller = strtolower($controller);

        $action = $backtrace['function'];
        $action = substr($action, 0,strlen($action) - strlen('Action'));
        $action = strtolower($action);

        return array($controller , $action);

    }

    /**
     * @param null|string $template - абсолютный путь до файла шаблона
     */
    public function setHeaderTemplate($template = null) {
        $this->defHeaderTemplate = $template;
    }

    /**
     * @param null|string $template - абсолютный путь до файла шаблона
     */
    public function setFooterTemplate($template = null) {
        $this->defFooterTemplate = $template;
    }

    public function __set($name, $value)
    {
        $this->variables[$name] = $value;
    }

    public static function TableList($config, $items, $options = array()) {
        define('TABLE_LIST_VIEW_DIRECTORY', getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_table_list_view');
        require_once TABLE_LIST_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . 'table.php';
    }
}