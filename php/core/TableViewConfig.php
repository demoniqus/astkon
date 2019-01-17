<?php
/**
 * Created by PhpStorm.
 * User: demoniqus
 * Date: 16.01.19
 * Time: 19:24
 */

namespace Astkon\View;


use Astkon\GlobalConst;

class TableViewConfig
{
    /**
     * @var int
     */
    public $currentPage;

    /**
     * @var int
     */
    public $totalItemsCount;

    /**
     * @var string
     */
    public $displayMode;

    /**
     * @var int
     */
    public $pageSize;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $baseURL;

    /**
     * Массив дополнительных параметров для Get-запроса, предоставляемые отдельно от параметров фильтрации, сортировки
     * и др. параметров настройки таблицы
     * @var array
     */
    public $GETParams;

    public function __construct()
    {
        $this->currentPage = 0;
        $this->pageSize = GlobalConst::DefaultListViewItemsCount;
        $this->displayMode = 'relocation';
        $this->totalItemsCount = GlobalConst::DefaultListViewItemsCount;
        $this->id = 'table_view_config_' . mt_rand();
        $this->baseURL = '/' . REQUIRED_CONTROLLER . '/' . REQUIRED_ACTION . (is_null(REQUIRED_ID) ? '' : '/' . REQUIRED_ID);
        $this->GETParams = array();
    }
}