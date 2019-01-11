<div class="row mx-0">
<?php

use Astkon\Controllers\OperationsController;
use Astkon\DataBase;
use Astkon\GlobalConst;
use Astkon\Model\Article;
use Astkon\Model\Operation;
use Astkon\Model\OperationType;

require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php';

if (isset($linkedData)) {
    foreach ($linkedData as $model => $linkedItems) {
        require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_' . DataBase::camelCaseToUnderscore($model::Name()) . '_script.php';
    }
}
?>

    <div class="col-md text-center" id="operation-form">
        <?php
        require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_operation_form_header.php';
        ?>
        <?php
        if (isset($linkedData)) {
            foreach ($linkedData as $model => $linkedItems) {
        ?>
                <div class="row" id="<?= $model::Name(); ?>ListItems">

                </div>
        <?php
            }
        }
        ?>
        <div class="row" id="OperationListItems">
            <div class="container-fluid">

            </div>
        </div>
        <div class="row mt-3">
            <a href="<?= '/' . OperationsController::Name() . '/OperationsList/' . $operationType[OperationType::PrimaryColumnName]; ?>" class="btn btn-outline-secondary mr-2">К списку документов</a>
            <a href="<?= '/' . OperationsController::Name() . '/Edit/' . $operation[Operation::PrimaryColumnName]; ?>" class="btn btn-outline-info mr-2">Редактировать</a>
        </div>
    </div>
    <script type="application/javascript">
        setSelectedArticles(
            $('#OperationListItems').find('.container-fluid:first'),
            '<?= Article::PrimaryColumnName; ?>',
            <?= json_encode($selectedItems); ?>
        );
    </script>
</div>
<?php
require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_operation_linked_data.php';

