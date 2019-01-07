<?php

use Astkon\Controllers\AuthController;
use Astkon\GlobalConst;

$bootstrapDir = '/bootstrap_v4.0.0';
$jqueryVers = '3.3.1';
$jqueryUIVers = '1.12.1';
?>
<!doctype html>
<html lang="ru" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="<?= GlobalConst::ViewDefCharset ;?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Astkon Warehouse</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/jquery-ui-<?= $jqueryUIVers;?>/jquery-ui.css" />
    <link rel="stylesheet" href="<?= $bootstrapDir; ?>/bootstrap.css" />
    <link rel="stylesheet" href="<?= $bootstrapDir; ?>/bootstrap-grid.css" />
    <link rel="stylesheet" href="<?= $bootstrapDir; ?>/bootstrap-reboot.css" />
    <link rel="stylesheet" href="/main.css" />
    <script type="text/javascript" src="/jquery/jquery-<?= $jqueryVers;?>.min.js"></script>
    <script type="text/javascript" src="/jquery/jquery-ui-<?= $jqueryUIVers;?>/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?= $bootstrapDir; ?>/bootstrap.js" ></script>
    <script type="text/javascript" src="<?= $bootstrapDir; ?>/bootstrap.bundle.js" ></script>
    <script type="text/javascript" src="/linq.js" ></script>
    <script type="text/javascript" src="/main.js" ></script>
    <style type="text/css">
        body, html {
            width: 100% !important;
            height: 100% !important;
            min-width: 100% !important;
            max-height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }
        #document-header, #document-body, #document-footer {
            border: 0px none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        #document-body {
            overflow: auto !important;
            background-image: url('/astkon_logo_extralight_small.png');
            background-repeat: no-repeat;
            background-position: center center
        }
        #document-header {
            overflow: hidden !important;
            min-height: 5px;
            content: "";
        }
    </style>
</head>
<body>
    <div id="document-header" class="container-fluid">
        <?php if (isset($_SESSION[AuthController::CurrentUserKey])) {
            ?>
                <div class="row justify-content-end py-1">
                    <div class="col-sm-3 text-right">
                        <p class="d-inline-block mb-1" title="Пользователь">
                            <?= CURRENT_USER['UserName']; ?>
                        </p>
                        <p class="d-inline-block text-nowrap mb-0" title="Группа">
                            <img src="/users.png" style="width: 24px; height: 24px; border: 0px none;" class="ml-3 mr-1"/>
                            <span class="d-inline-block">
                                <?= CURRENT_USER['$fkIdUserGroup']; ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-sm-1">
                        <a href="/Auth/Logout">
                            <img src="/logout.png" style="width: 32px; height: 32px; border: 0px none;" />
                        </a>
                    </div>
                </div>
            <?php
        }
        ?>
    </div>
    <div id="document-body" class="container-fluid">
