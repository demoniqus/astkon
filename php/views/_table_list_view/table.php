<?php
/*
 * контейнер .table-view-container используется для определения в TableManager для определения элемента, содержимое которого
 * следует заменить при обновлении. Т.о. внутри данного контейнера можно иметь любую структуру данных
 */
?>
<div class="table-view-container">
    <script type="text/javascript" src="/TableManager.js"></script>
    <style type="text/css">
        .bool-field-true-value-icon {
            width: 24px;
            height: 24px;
            max-width: 24px;
            max-height: 24px;
            min-width: 24px;
            min-height: 24px;
            border: 0px none;
        }
    </style>
    <script type="text/javascript">

    </script>
    <table class="table table-hover my-2 table-sm" id="<?= $tableViewConfig->id; ?>">
    <?php
    $primaryKeyName = array_filter(
        $config,
        function($configItem){
            return $configItem['primary_key'];
        }
    )[0]['key'];

    require_once TABLE_LIST_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . 'thead.php';
    require_once TABLE_LIST_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . 'tbody.php';
    ?>
    </table>
    <?php
    require_once TABLE_LIST_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . 'pagination.php';
    ?>
    <script type="application/javascript">
        (new TableManager('<?= $tableViewConfig->id; ?>'))
            .setBaseURL('<?= $tableViewConfig->baseURL; ?>')
            .setGETParams(<?= json_encode($tableViewConfig->GETParams); ?>)
            .setMode('<?= $tableViewConfig->displayMode; ?>')
            .setPageSize(<?= $tableViewConfig->pageSize; ?>, true)
            .setPage(<?= $tableViewConfig->currentPage; ?>, true)
        <?php
            if ($tableViewConfig->displayMode === 'reload') {
            ?>
            .restoreSelectedItems('<?= $_REQUEST['dialogId']; ?>', '<?= $primaryKeyName; ?>')
            <?php
            }
        ?>
            ;
    </script>
</div>