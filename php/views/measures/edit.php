<style type="text/css">

</style>

<div class="row mx-0">
    <?php use Astkon\GlobalConst;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        Форма редактирования меры
        <?= var_dump($Measure); ?>
    </div>
</div>

