<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 27.12.18
 * Time: 17:00
 */

namespace Astkon\Traits;

use Astkon\DataBase;
use Astkon\GlobalConst;
use function Astkon\Lib\array_keys_CamelCase;
use Astkon\QueryConfig;
use Astkon\View\TableViewConfig;
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
        ?array $displayedFields = null,
        ?TableViewConfig $tableViewConfig = null
    ) {
        $view->listItemOptions = $options ?? array();
        $tableViewConfig = $tableViewConfig ?? new TableViewConfig();
        $tableViewConfig->displayMode = 'relocation';
        $this->configureListView($view, $model, $queryConfig, $displayedFields, $tableViewConfig);
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
        ?array $displayedFields = array(),
        ?TableViewConfig $tableViewConfig = null
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

        $queryConfig->Offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        if (!isset($queryConfig->OrderBy) || !count($queryConfig->OrderBy)) {
            $queryConfig->OrderBy = $this->getDefaultOrder();
        }

        $tableViewConfig = $tableViewConfig ?? new TableViewConfig();

        if (!isset($queryConfig->Limit)) {
            $queryConfig->Limit = isset($_GET['pageSize']) ?
                intval($_GET['pageSize']) :
                $tableViewConfig->displayMode === 'relocation' ?
                    GlobalConst::DefaultListViewItemsCount :
                    GlobalConst::DefaultDictViewItemsCount

            ;
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

        $tableViewConfig->pageSize = $queryConfig->Limit ?? GlobalConst::DefaultDictViewItemsCount;
        $tableViewConfig->currentPage = floor($queryConfig->Offset / $tableViewConfig->pageSize);
        $tableViewConfig->totalItemsCount = $model::getCount(null, $queryConfig, 2);

        $view->tableViewConfig = $tableViewConfig;
    }

    public function DictViewAction(
        View $view,
        $model,
        ?QueryConfig $queryConfig = null,
        ?array $displayedFields = array(),
        ?TableViewConfig $tableViewConfig = null
    ){
        $listItemOptions = [];
        $tableViewConfig = $tableViewConfig ?? new TableViewConfig();
        if (array_key_exists('mode', $_GET)) {
            $tableViewConfig->GETParams['mode'] = $_GET['mode'];
            if ( trim(strtolower($_GET['mode'])) === 'multiple') {
                $listItemOptions[] = array(
                    'action' => null,
                    'click'  => htmlspecialchars(
                        'DictionaryItemChangeCheckedState($(this).find("img:first"), "'
                        . $_REQUEST['dialogId'] . '","'
                        . $model::PrimaryColumnName
                        . '")'
                    ),
                    'icon'   => '/checkbox-unchecked.png',
                    'title'  => 'Отметить элемент',
                    'class'  => 'checkbox',
                );
            }
        }
        $listItemOptions[] = array(
            'action' => null,
            'click' => 'DictionarySelector.setValue(\'' .
                $_REQUEST['dialogId'] . '\', [JSON.parse($(this).parents(\'tr:first\').get(0).dataset.item)],' .
                htmlspecialchars(json_encode($model::ReferenceDisplayedKeys())) . ')',
            'icon' => '/icon-next.png',
            'title' => 'Выбрать элемент'
        );
        $view->listItemOptions = $listItemOptions;

        $tableViewConfig->displayMode = 'reload';
        $tableViewConfig->GETParams['dialogId'] = $_REQUEST['dialogId'];

        $view->setHeaderTemplate(null);
        $view->setFooterTemplate(null);
        $this->configureListView($view, $model, $queryConfig, $displayedFields, $tableViewConfig);
    }

    private function getDefaultOrder() {
        return array();
    }
}

