<style type="text/css">

</style>

<div class="row mx-0">
    <?php
    use Astkon\GlobalConst;
    use Astkon\View\View;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-center">
        <div class="text-left">
            <a href="/Users/Edit/0" class="btn btn-success">Добавить пользователя</a>
            <a href="/UserGroups/UserGroupsList" class="btn btn-outline-secondary">Группы пользователей</a>
        </div>
        <?php
        View::TableList($modelConfig, $listItems, $listItemOptions);
        ?>
    </div>
</div>