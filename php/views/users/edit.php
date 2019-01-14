<style type="text/css">

</style>

<div class="row mx-0">
    <?php
    use Astkon\GlobalConst;
    use Astkon\Model\User;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-left">
        <div class="alert alert-info text-center">
            Редактирование пользователя
        </div>
        <?= User::EditForm($User, $options); ?>
    </div>
</div>
