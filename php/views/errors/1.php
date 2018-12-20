<?php

use Astkon\GlobalConst;

?>
<style type="text/css">
    body {
        background-image: url(/programmer_error.jpg);
        background-attachment: fixed;
        background-size: 100%;
    }
</style>
<div class="container-fluid">
    <div class="col pl-3 pt-3 m-0" style="font-size: 300%; color: #005cbf;">
        1
    </div>
</div>
<div class="container-fluid">
    <div class="col pl-3 pt-3 m-0">
        <a href="/" style="font-size: 150%; color: #005cbf;">На главную страницу</a>
    </div>
    <?php
    if (isset($trase)) {
        echo '<div class="row m-0 text-left px-3">';
        var_dump($trace);
        echo '</div>';
    }
    ?>
</div>
