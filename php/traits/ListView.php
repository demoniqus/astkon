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
    public function ListViewAction(View $view, $model) {
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
        $this->configureListView($view, $model);
    }

    public function configureListView(View $view, $model) {
        $dataTable = $model::DataTable;
        $view->modelConfig = $model::getConfigForListView();
        $rows = array_map(
            function($row){
                return array_keys_CameCase($row);
            },
            (new DataBase())->$dataTable->getRows()
        );
        $model::decodeForeignKeys($rows);
        $view->listItems = $rows;
    }

    public function DictViewAction(View $view, $model){
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
        $this->configureListView($view, $model);
    }
}

