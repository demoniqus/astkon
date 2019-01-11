<?php

use Astkon\Controllers\OperationsController;
use Astkon\Model\OperationState;

require_once $this->defHeaderTemplate;
?>
<div class="row mx-5">
    <div class="col-md text-center alert alert-danger">
        Запрашиваемый документ не найден
    </div>
</div>
<div class="row mt-3 mx-5">
    <a href="<?= '/' . OperationsController::Name() . '/Index'; ?>" class="btn btn-outline-secondary ml-2">Вернуться в "Операции"</a>
</div>

<?php
require_once $this->defFooterTemplate;