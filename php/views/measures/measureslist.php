<style type="text/css">
    .action-icon {
        width: 24px;
        height: 24px;
        min-width: 24px;
        min-height: 24px;
        max-width: 24px;
        max-height: 24px;
    }
</style>

<div class="row mx-0">
    <?php use Astkon\GlobalConst;
    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        <div class="text-left">
            <a href="/Measures/Edit/0" class="btn btn-success">Добавить единицу измерения</a>
        </div>
        <?php
            \Astkon\View\View::TableList($modelConfig, $measures, $listItemOptions);

        ?>

    </div>
</div>

