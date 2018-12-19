<style type="text/css">

</style>

<div class="row mx-0">
    <?php use Astkon\GlobalConst;
    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-left">
        <div class="alert alert-info text-center">
            Редактирование артикула
        </div>
        <?= \Astkon\Model\Article::EditForm($Article); ?>
    </div>
</div>

