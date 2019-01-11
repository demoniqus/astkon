<div class="row mx-0">
    <?php

    use Astkon\Controllers\OperationsController;
    use Astkon\Controllers\UsersController;
    use Astkon\GlobalConst;
    use Astkon\Model\Article;
    use Astkon\Model\User;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php';
    $editable = true;
    require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_' . DataBase::camelCaseToUnderscore(User::Name()) . '_script.php';
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
                            targetContainer: $('#<?= User::Name(); ?>ListItems'),
                            extReferencePKName: '<?= User::PrimaryColumnName; ?>',
                            dataSourceUrl: '<?= UsersController::Name() . '/UsersDict'; ?>?mode=multiple',
                            title: '',
                            setValueCallback: 'select<?= User::Name(); ?>',
                            mode: 'multiple'
                            })">Выбрать людей...</button>
            <button
                    type="button"
                    class="btn btn-light ml-3"
                    onclick="new DictionaryField({
                            targetContainer: $('#OperationListItems').find('.container-fluid:first'),
                            extReferencePKName: '<?= Article::PrimaryColumnName; ?>',
                            dataSourceUrl: '<?= $dictionaryAction . '?mode=multiple&operation=' . $operationType['OperationName']; ?>',
                            title: '',
                            setValueCallback: 'setSelectedArticlesAsEditable',
                            mode: 'multiple'
                            })">Добавить элементы...</button>
        </div>
        <div class="row" id="<?= User::Name(); ?>ListItems">

        </div>
        <div class="row" id="OperationListItems">
            <div class="container-fluid">

            </div>
        </div>
        <div class="row mt-3">

            <button id="btn-save" type="button" class="btn btn-primary" onclick="saveOperation(false)">Сохранить</button>
            <a id="btn-gotolist" href="<?= '/' . OperationsController::Name() . '/OperationsList/' . $operationType['IdOperationType']; ?>" class="btn btn-outline-secondary ml-2">К списку документов</a>
        </div>
    </div>
</div>

<?php

require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_edited_operation_data.php';

require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_operation_linked_data.php';
