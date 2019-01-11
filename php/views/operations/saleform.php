<div class="row mx-0">
    <?php

    use Astkon\DataBase;
    use Astkon\GlobalConst;
    use Astkon\Model\Article;
    use Astkon\Model\BuildObject;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php';
    $editable = true;
    require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_' . DataBase::camelCaseToUnderscore(BuildObject::Name()) . '_script.php';

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
                        targetContainer: $('#<?= BuildObject::Name(); ?>ListItems'),
                        extReferencePKName: '<?= BuildObject::PrimaryColumnName; ?>',
                        dataSourceUrl: 'BuildObjects/BuildObjectsDict?mode=multiple',
                        title: '',
                        setValueCallback: 'select<?= BuildObject::Name(); ?>',
                        mode: 'multiple'
                        })">Выбрать объекты...</button>
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
        <div class="row" id="<?= BuildObject::Name(); ?>ListItems">

        </div>
        <?php
            require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_operation_form_body.php';
        ?>
    </div>
</div>

<?php

require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_edited_operation_data.php';
require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_set_operation_linked_data.php';

