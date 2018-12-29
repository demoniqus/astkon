<?php

require getcwd() . DIRECTORY_SEPARATOR . \Astkon\GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . '_valid_state.php';

use Astkon\DataBase;

?>
<div class="form-group row <?= $propName; ?>">
    <label class="col-sm-3 col-form-label text-right"><?= $alias; ?></label>
    <div class="col-sm-9 col-lg-5 row">
        <div class="col-sm-2">
            <button type="button" class="btn btn-light" onclick="new DictionaryField({targetContainer: $(this).parents('.form-group:first'), extReferencePKName: '<?= DataBase::underscoreToCamelCase($ForeignKeyParams['field']); ?>', dataSourceUrl: '<?= $dictionaryAction; ?>', title: $(this).parents('.form-group:first').find('.col-form-label:first').text(), setValueCallback: 'setSingleReferenceValue'})">...</button>
            <!--<button type="button" class="btn btn-light" onclick="DictionarySelector.dialog($(this).parents('.form-group:first'), '<?= DataBase::underscoreToCamelCase($ForeignKeyParams['field']); ?>', '<?= $dictionaryAction; ?>')">...</button>-->
        </div>
        <div class="col-sm-9 visible-value"><?= $displayValue; ?></div>
        <input type="hidden" name="<?= $propName; ?>" value="<?= $value; ?>" class="form-control <?= $validClass; ?>"/>
        <?= $feedback; ?>
    </div>
</div>