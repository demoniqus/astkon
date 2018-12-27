<div class="row mx-0">
<?php
use Astkon\GlobalConst;
use Astkon\Model\Article;

require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php';
?>
    <div class="col-lg text-center" id="operation-form">
        <?php
        require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_operation_form_header.php';
        ?>
        <div class="row">
            <button
                    type="button"
                    class="btn btn-light"
                    onclick="new DictionaryField({
                            targetContainer: $('#OperationListItems').find('.container-fluid:first'),
                            extReferencePKName: '<?= Article::PrimaryColumnName; ?>',
                            dataSourceUrl: '<?= $dictionaryAction . '?mode=multiple&operation=' . $operationType['OperationName']; ?>',
                            title: '',
                            setValueCallback: 'setSelectedArticles',
                            mode: 'multiple'
                    })">Добавить элементы...</button>
        </div>
        <div class="row" id="OperationListItems">
            <div class="container-fluid">

            </div>
        </div>
        <div class="row mt-3">

            <button type="button" class="btn btn-primary" onclick="saveOperation(false)">Сохранить</button>
            <button type="button" class="btn btn-primary ml-2" onclick="saveOperation(true)">Зафиксировать</button>
        </div>
    </div>
</div>

