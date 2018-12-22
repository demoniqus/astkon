<div class="form-group row <?= $propName; ?>">
    <label class="col-sm-3 col-form-label text-right"><?= $alias; ?></label>
    <div class="col-sm-9 col-lg-5 row">
        <div class="col-sm-2">
            <button type="button" class="btn btn-light">...</button>
        </div>
        <input type="hidden" name="<?= $propName; ?>" value="<?= $value; ?>" />
        <div class="col-sm-9 visible-value"><?= $displayValue; ?></div>
    </div>
</div>