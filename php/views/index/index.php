<?php

use Astkon\GlobalConst;

?>
<div class="container-fluid" style="color: lawngreen; font-size: 14em; display: none !important;">
    THIS IS VIEW FOR INDEX ACTION OF INDEX CONTROLLER
    <p>
<?= $some_var; ?>
    <p>
    <?= getcwd(); ?><p>
    <?= GlobalConst::DefHeaderView; ?>
    <img src="4.jpg" style="width: 500px; height: 500px;"/>
</div>
<div class="row mx-0">
    <?php require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        <div class="alert alert-secondary p-2 d-inline-block mt-3">Для начала работы выберите пункт меню</div>
    </div>
</div>