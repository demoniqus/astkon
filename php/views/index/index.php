<?php

use Astkon\GlobalConst;

?>
<div class="row mx-0">
    <?php require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-center">
        <?php
        \Astkon\Lib\TileMenu(array_filter($leftMenuItems, function($menuItem){ return $menuItem['Action'] !== '/';}), 4, 2);
        ?>
    </div>
</div>