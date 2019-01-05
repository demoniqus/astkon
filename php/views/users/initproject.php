<?php

use Astkon\GlobalConst;
use Astkon\Model\Model;

?>
<form method="post" action="">
    <div class="row mx-0">
        <div class="col-lg text-center">
            <div class="mx-5 alert alert-secondary text-center">
                Создание первого пользователя
            </div>
            <?php
            $fieldsValidation = isset($options['validation']) && isset($options['validation']['fields']) ? $options['validation']['fields'] : array();

            if (
                    isset($options['validation']) &&
                    isset($options['validation']['state']) &&
                    $options['validation']['state'] === Model::ValidStateError
            ) {
                $validationFormMessage = 'Ошибка при создании первого пользователя';
                $validationFormClass = 'alert-error';
                require_once GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'common_message.php';
            }

            $propName = 'Login';
            if (isset($fieldsValidation[$propName])) {
                $validState = $fieldsValidation[$propName]['state'];
                $validMessage = $fieldsValidation[$propName]['message'];
            }
            else {
                $validState = Model::ValidStateUndefined;
                $validMessage = '';
            }
            $inputType = 'text';
            $caption = 'Логин';
            $value = $Entity[$propName];
            require GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'input.php';

            $propName = 'Password';
            if (isset($fieldsValidation[$propName])) {
                $validState = $fieldsValidation[$propName]['state'];
                $validMessage = $fieldsValidation[$propName]['message'];
            }
            else {
                $validState = Model::ValidStateUndefined;
                $validMessage = '';
            }
            $inputType = 'password';
            $caption = 'Пароль';
            $value = $Entity[$propName];
            require GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'input.php';

            $propName = 'PasswordConfirm';
            if (isset($fieldsValidation[$propName])) {
                $validState = $fieldsValidation[$propName]['state'];
                $validMessage = $fieldsValidation[$propName]['message'];
            }
            else {
                $validState = Model::ValidStateUndefined;
                $validMessage = '';
            }
            $caption = 'Подтверждение пароля';
            $value = $Entity[$propName];
            require GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'input.php';
            ?>
            <div class="row">
                <div class="col text-center">
                    <button type="submit" name="submit" class="btn btn-primary">Зарегистрировать</button>
                </div>
            </div>
        </div>
    </div>
</form>