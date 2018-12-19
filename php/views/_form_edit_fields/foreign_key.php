<div class="form-group row <?= $propName; ?>">
    <label class="col-sm-3 col-form-label text-right"><?= $alias; ?></label>
    <div class="col-sm-9 col-lg-5">
        <button type="button" class="form-control col-sm-1">...</button>
        <input type="hidden" name="<?= $propName; ?>" value="<?= $value; ?>" />
        <div class="col-sm-10 visible-value"></div>
    </div>
</div>