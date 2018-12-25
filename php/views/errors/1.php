<?php

use Astkon\GlobalConst;

?>
<style type="text/css">
    body {
        background-image: url(/backgrounds/programmer_error.jpg);
        background-attachment: fixed;
        background-size: 100%;
    }
    pre {
        color: lightgray;
    }
</style>
<div class="container-fluid">
    <div class="col pl-3 pt-3 m-0" style="font-size: 300%; color: #239abf;">
        <?= $code; ?>
    </div>
</div>
<div class="container-fluid">
    <div class="col pl-3 pt-3 m-0">
        <a href="/" style="font-size: 150%; color: #22a8bf;">На главную страницу</a>
    </div>
    <?php
    if (isset($trace)) {
        echo '<div class="row m-0 text-left px-3" style="font-weight: bold; color: lightcoral">';
        if (gettype($trace) === gettype('')) {
            echo $trace;
        }
        else{
            var_dump($trace);
        }
        echo '</div>';
    }
    ?>
</div>
