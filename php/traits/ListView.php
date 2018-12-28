<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 27.12.18
 * Time: 17:00
 */

namespace Astkon\Traits;

use Astkon\DataBase;
use function Astkon\Lib\array_keys_CameCase;
use Astkon\View\View;

trait ListView
{
    /**
     * @param View $view
     * @param $model
     * @param string|null $condition
     * @param array|null $requiredFields
     * @param array|null $substitution
     * @param int|null $offset
     * @param int|null $limit
     */
    public function ListViewAction(
        View $view,
        $model,
        $condition = null,
        $requiredFields = null,
        $substitution = null,
        $offset = null,
        $limit = null
    ) {
        $modelName = explode('\\', $model);
        $modelName = array_pop($modelName);
        $view->listItemOptions = array(
            array(
                'action' => '/' . $modelName  . '/Edit',
                'click' => null,
                'icon' => '/icon-edit.png',
                'title' => 'Редактировать'
            )
        );
        $this->configureListView($view, $model, $condition, $requiredFields, $substitution, $offset, $limit);
    }

    public function configureListView(
        View $view,
        $model,
        $condition = null,
        $requiredFields = null,
        $substitution = null,
        $offset = null,
        $limit = null
    ) {
        if (is_array($requiredFields)) {
            $requiredFields = array_map(function($fieldName){ return DataBase::camelCaseToUnderscore($fieldName);});
        }
        $dataTable = $model::DataTable;
        $view->modelConfig = $model::getConfigForListView();
        $rows = array_map(
            function($row){
                return array_keys_CameCase($row);
            },
            (new DataBase())->$dataTable->getRows($condition, $requiredFields, $substitution, $offset, $limit)
        );
        $model::decodeForeignKeys($rows);
        $view->listItems = $rows;
    }

    public function DictViewAction(
        View $view,
        $model,
        $condition = null,
        $requiredFields = null,
        $substitution = null,
        $offset = null,
        $limit = null
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
        $this->configureListView($view, $model, $condition, $requiredFields, $substitution, $offset, $limit);
    }
}
