<div class="form-group row">
    <label class="col-sm-3 col-form-label text-right"><?= $alias; ?></label>
    <div class="col-sm-9 col-lg-5">
        <input type="hidden" name="<?= $propName; ?>" value="0" />
        <input type="checkbox" name="<?= $propName; ?>" class="" <?= ($value ? 'checked="CHECKED"' : ''); ?>" value="1" />
    </div>
</div>
<?php
