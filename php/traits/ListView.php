<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 27.12.18
 * Time: 17:00
 */

namespace Astkon\Traits;

use Astkon\DataBase;
use function Astkon\Lib\array_keys_CamelCase;
use Astkon\QueryConfig;
use Astkon\View\View;

trait ListView
{
    /**
     * @param View             $view
     * @param string           $model
     * @param array            $options
     * @param QueryConfig|null $queryConfig
     * @param array            $displayedFields - список полей, которые следует отображать (в camelCase)
     */
    public function ListViewAction(
        View $view,
        $model,
        ?array $options = array(),
        ?QueryConfig $queryConfig = null,
        ?array $displayedFields = null
    ) {
        $view->listItemOptions = $options ?? array();
        $this->configureListView($view, $model, $queryConfig, $displayedFields);
    }

    public static function editOption(array &$options, string $controller) {
        $controllerName = explode('\\', $controller);
        $controllerName = array_pop($controllerName);
        $controllerName = mb_substr($controllerName, 0, mb_strlen($controllerName) - mb_strlen('Controller'));
        $options[] = array(
            'action' => '/' . $controllerName  . '/Edit',
            'click' => null,
            'icon' => '/icon-edit.png',
            'title' => 'Редактировать'
        );
    }

    public function configureListView(
        View $view,
        $model,
        ?QueryConfig $queryConfig = null,
        ?array $displayedFields = array()
    ) {
        $queryConfig = $queryConfig ?? new QueryConfig();
        if (is_array($queryConfig->RequiredFields)) {
            $queryConfig->RequiredFields = array_map(
                function($fieldName){
                    return DataBase::camelCaseToUnderscore($fieldName);
                },
                $queryConfig->RequiredFields
            );
        }
        $modelConfig = $model::getConfigForListView();
        if (is_array($displayedFields) && count($displayedFields)) {
            $displayedFieldsDict = array_flip(
                array_map(
                    function($fieldName){
                        return DataBase::underscoreToCamelCase($fieldName);
                    },
                    $displayedFields
                )
            );
            foreach ($modelConfig as &$configItem) {
                if (!array_key_exists($configItem['key'], $displayedFieldsDict)) {
                    $configItem['nodisplay'] = 'true';
                }
                else{
                    $configItem['list_view_order'] = $displayedFieldsDict[$configItem['key']];
                }
            }
            $modelConfig = $model::Sort($modelConfig);
        }
        $view->modelConfig = $modelConfig;

        $rows = array_map(
            function($row){
                return array_keys_CamelCase($row);
            },
            $model::getRows(null, $queryConfig, 2)
        );
        $view->listItems = $rows;
    }

    public function DictViewAction(
        View $view,
        $model,
        ?QueryConfig $queryConfig = null,
        ?array $displayedFields = array()
    ){
        $listItemOptions = [];
        if (array_key_exists('mode', $_GET) && trim(strtolower($_GET['mode'])) === 'multiple') {
            $listItemOptions[] = array(
                'action' => null,
                'click' => htmlspecialchars('DictionaryItemChangeCheckedState($(this).find("img:first"))'),
                'icon' => '/checkbox-unchecked.png',
                'title' => 'Отметить элемент'
            );
        }
        $listItemOptions[] = array(
            'action' => null,
            'click' => 'DictionarySelector.setValue(\'' .
                $_POST['dialogId'] . '\', [JSON.parse($(this).parents(\'tr:first\').get(0).dataset.item)],' .
                htmlspecialchars(json_encode($model::ReferenceDisplayedKeys())) . ')',
            'icon' => '/icon-next.png',
            'title' => 'Выбрать элемент'
        );
        $view->listItemOptions = $listItemOptions;
        $view->setHeaderTemplate(null);
        $view->setFooterTemplate(null);
        $this->configureListView($view, $model, $queryConfig, $displayedFields);
    }
}

