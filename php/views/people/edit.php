<style type="text/css">

</style>

<div class="row mx-0">
    <?php
    use Astkon\GlobalConst;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-left">
        <div class="alert alert-info text-center">
            <?php
            $actionCaption = $Entity[$Model::PrimaryColumnName] == 0 ? 'Создание ' : 'Редактирование ';
            ?>
            <?= $actionCaption; ?> информации о человеке
        </div>
        <?= $Model::EditForm($Entity, $options); ?>
    </div>
</div>
