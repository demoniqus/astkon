<div class="form-group row">
    <button type="submit" name="submit" class="btn btn-primary">Сохранить</button>
    <?php if (isset($options['backToList'])) {
        ?>
        <button type="button" class="btn btn-outline-secondary ml-3" onclick="document.location.href='<?= $options['backToList']; ?>'">Вернуться в список</button>
        <?php
    }
    ?>
    <button type="button" class="btn btn-outline-danger ml-3" style="display: none !important;" onclick="document.location.href='<?= isset($options['Rollback']) ? $options['Rollback'] : $_SERVER['HTTP_REFERER']; ?>'">Oтмена</button>
</div>