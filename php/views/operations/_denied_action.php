<?php

use Astkon\Controllers\OperationsController;

require_once $this->defHeaderTemplate;
?>
<div class="row mx-5">
    <div class="col-md text-center alert alert-danger">
        <?=
            $message
        ?>
    </div>
</div>
<div class="row mt-3 mx-5">
    <a href="<?= '/' . OperationsController::Name() . '/OperationsList/' . $operationType['IdOperationType']; ?>" class="btn btn-outline-secondary ml-2">К списку документов</a>
</div>

<?php
require_once $this->defFooterTemplate;