<style type="text/css">

</style>

<div class="row mx-0">
    <?php use Astkon\GlobalConst;
    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-left">
        Форма редактирования меры
        <?= \Astkon\Model\Measure::EditForm($Measure); ?>
    </div>
</div>

