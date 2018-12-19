<div class="form-group row">
    <button type="submit" name="submit" class="btn btn-primary">Сохранить</button>
    <button type="button" class="btn btn-outline-danger ml-3" onclick="document.location.href='<?= isset($options['Rollback']) ? $options['Rollback'] : $_SERVER['HTTP_REFERER']; ?>'">Oтмена</button>
</div>