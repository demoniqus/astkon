<?php

require getcwd() . DIRECTORY_SEPARATOR . \Astkon\GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . '_valid_state.php';

?>
<div class="form-group row">
    <label class="col-sm-3 col-form-label text-right"><?= $alias; ?></label>
    <div class="col-sm-9 col-lg-5">
        <input type="<?= $inputType; ?>" name="<?= $propName; ?>" class="form-control <?= $validClass; ?>" value="<?= htmlspecialchars($value); ?>" />
        <?= $feedback; ?>
    </div>
</div>