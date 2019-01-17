<style type="text/css">

</style>

<div class="row mx-0">
    <?php
    use Astkon\GlobalConst;
    use Astkon\View\View;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-center">
        <div class="row mb-2">
            <div class="col alert alert-info">
                <?= $operationType['operation_label']; ?>
            </div>
        </div>
        <div class="text-left mb-2">
            <a href="/Operations/<?= $operationType['operation_name']; ?>Form/" class="btn btn-success">Добавить...</a>
        </div>
        <?php
        View::TableList($modelConfig, $listItems, $listItemOptions, $tableViewConfig);
        ?>
    </div>
</div>
