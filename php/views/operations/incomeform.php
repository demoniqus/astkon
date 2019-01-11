<div class="row mx-0">
<?php

use Astkon\GlobalConst;
use Astkon\Model\Article;

require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php';
?>
    <div class="col-md text-center" id="operation-form">
        <?php
        require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_operation_form_header.php';
        ?>
        <div class="row mb-3">
            <button
                    type="button"
                    class="btn btn-light"
                    onclick="new DictionaryField({
                            targetContainer: $('#OperationListItems').find('.container-fluid:first'),
                            extReferencePKName: '<?= Article::PrimaryColumnName; ?>',
                            dataSourceUrl: '<?= $dictionaryAction . '?mode=multiple&operation=' . $operationType['OperationName']; ?>',
                            title: '',
                            setValueCallback: 'setSelectedArticlesAsEditable',
                            mode: 'multiple'
                    })">Добавить элементы...</button>
        </div>
        <?php
            require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_operation_form_body.php';
        ?>
    </div>
</div>
<?php

require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_edited_operation_data.php';

