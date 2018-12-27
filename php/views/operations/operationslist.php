<style type="text/css">

</style>

<div class="row mx-0">
    <?php
    use Astkon\GlobalConst;
    use Astkon\View\View;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        <div class="text-left">
            <a href="/Operations/<?= $operation_type['operation_name']; ?>Form/" class="btn btn-success">Добавить...</a>
        </div>
        <?php
        View::TableList($modelConfig, $listItems, $listItemOptions);
        ?>
    </div>
</div>
