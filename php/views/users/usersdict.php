<style type="text/css">

</style>

<div class="row mx-0">
    <?php
    use Astkon\View\View;

    ?>
    <div class="col-md text-center">

        <?php
            View::TableList($modelConfig, $listItems, $listItemOptions, $tableViewConfig);
        ?>
    </div>
</div>

