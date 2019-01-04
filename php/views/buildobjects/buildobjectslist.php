<style type="text/css">

</style>

<div class="row mx-0">
    <?php
    use Astkon\GlobalConst;
    use Astkon\View\View;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-center">
        <div class="text-left">
            <a href="/BuildObjects/Edit/0" class="btn btn-success">Добавить объект</a>
        </div>
        <?php
        View::TableList($modelConfig, $listItems, $listItemOptions);
        ?>
    </div>
</div>


