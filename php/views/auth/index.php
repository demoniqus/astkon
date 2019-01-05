<?php

use Astkon\GlobalConst;
use Astkon\Model\Model;

?>
<form method="post" action="/Auth/Index">
    <div class="row mx-0">
        <div class="col-md text-center">
            <div class="mx-5 alert alert-secondary text-center">
                Авторизуйтесь
            </div>
            <?php
            $validMessage = '';
            $value = '';
            if ($validState === Model::ValidStateError) {
                $validationFormClass = 'alert-danger';
                $validationFormMessage = 'Пользователя с такими данными не существует';
                require GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'common_message.php';
            }

            $inputType = 'text';
            $caption = 'Логин';
            $propName = 'Login';
            require GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'input.php';

            $inputType = 'password';
            $caption = 'Пароль';
            $propName = 'Password';
            require GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . '_form_edit_fields' . DIRECTORY_SEPARATOR . 'input.php';
            ?>
            <div class="row">
                <div class="col text-center">
                    <button type="submit" name="submit" class="btn btn-primary">Войти</button>
                </div>
            </div>
        </div>
    </div>
</form>