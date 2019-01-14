<style type="text/css">

</style>

<div class="row mx-0">
    <?php

    use Astkon\Controllers\ArticleCategoriesController;
    use Astkon\Controllers\ArticlesController;
    use Astkon\Controllers\MeasuresController;
    use Astkon\GlobalConst;
    use Astkon\View\View;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-center">
        <div class="text-left">
            <a href="/<?= ArticlesController::Name(); ?>/Edit/0" class="btn btn-success">Добавить артикул</a>
            <a href="/<?= ArticleCategoriesController::Name(); ?>/<?= ArticleCategoriesController::Name(); ?>List" class="btn btn-outline-secondary">Категории</a>
            <a href="/<?= MeasuresController::Name(); ?>/<?= MeasuresController::Name(); ?>List" class="btn btn-outline-secondary">Единицы измерения</a>
            <?php
            if (CURRENT_USER['IsAdmin']) {
                ?>
                    <a href="/<?= ArticlesController::Name(); ?>/Import" class="btn offset-2 py-0"><img src="/icon_import_csv.jpg" style="border: 0px none; width: 38px; height: 38px;" title="Импорт из файла CSV"/></a>
                <?php
            }
            ?>

        </div>
        <?php
        View::TableList($modelConfig, $listItems, $listItemOptions);
        ?>
    </div>
</div>
