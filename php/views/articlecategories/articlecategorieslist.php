<style type="text/css">

</style>

<div class="row mx-0">
    <?php

    use Astkon\Controllers\ArticleCategoriesController;
    use Astkon\Controllers\ArticlesController;
    use Astkon\GlobalConst;
    use Astkon\View\View;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-center">
        <div class="text-left">
            <?php
                if (CURRENT_USER['IsAdmin']) {
                    ?>
                        <a href="/<?= ArticleCategoriesController::Name(); ?>/Edit/0" class="btn btn-success">Добавить категорию</a>
                    <?php
                }
            ?>
            <a href="/<?= ArticlesController::Name(); ?>/<?= ArticlesController::Name(); ?>List" class="btn btn-outline-secondary">Артикулы</a>
        </div>

        <?php
        View::TableList($modelConfig, $listItems, $listItemOptions, $tableViewConfig);
        ?>
    </div>
</div>
