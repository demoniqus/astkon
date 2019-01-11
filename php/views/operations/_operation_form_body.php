<?php

use Astkon\Controllers\OperationsController;

?>

<div class="row" id="OperationListItems">
    <div class="container-fluid">

    </div>
</div>
<div class="row mt-3">

    <button id="btn-save" type="button" class="btn btn-primary" onclick="saveOperation(false)">Сохранить</button>
    <button id="btn-save-fixed" type="button" class="btn btn-primary ml-2" onclick="saveOperation(true)">Зафиксировать</button>
    <a id="btn-gotolist" href="<?= '/' . OperationsController::Name() . '/OperationsList/' . $operationType['IdOperationType']; ?>" class="btn btn-outline-secondary ml-2">К списку документов</a>
</div>