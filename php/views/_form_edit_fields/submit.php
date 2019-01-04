<div class="form-group row">
    <button type="submit" name="submit" class="btn btn-primary">Сохранить</button>
    <?php if (isset($options['backToList'])) {
        ?>
        <button type="button" class="btn btn-outline-secondary ml-3" onclick="document.location.href='<?= $options['backToList']; ?>'">Вернуться в список</button>
        <?php

    }
    ?>
<!--    <button type="button" class="btn btn-outline-secondary ml-3" onclick="document.location.href=''">Новый...</button>-->
    <button type="button" class="btn btn-outline-danger ml-3" style="display: none !important;" onclick="document.location.href='<?= isset($options['Rollback']) ? $options['Rollback'] : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/'); ?>'">Oтмена</button>
    <?php
        if (isset($Entity) && isset($Entity[$Model::PrimaryColumnName]) && $Entity[$Model::PrimaryColumnName] > 0) {
    ?>
            <button type="button" class="btn btn-outline-success ml-3" onclick="document.location.href='<?= '/' . REQUIRED_CONTROLLER . '/' . REQUIRED_ACTION . '/0'; ?>'">Создать еще...</button>
    <?php
        }
    ?>
</div>