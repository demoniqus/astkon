<style type="text/css">
    .action-icon {
        width: 24px;
        height: 24px;
        min-width: 24px;
        min-height: 24px;
        max-width: 24px;
        max-height: 24px;
    }
</style>

<div class="row mx-0">
    <?php use Astkon\GlobalConst;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        <div class="text-left">
            <a href="/Users/Edit/0" class="btn btn-success">Добавить пользователя</a>
        </div>
        <table>
            <tr>
                <th><?= \Astkon\Model\User::getFieldAlias('Login'); ?></th>
                <th><?= \Astkon\Model\User::getFieldAlias('Password'); ?></th>
                <th><?= \Astkon\Model\User::getFieldAlias('Config'); ?></th>
                <th></th>
                <?php
                array_walk($users, function($user){
                    ?>
                    <tr>
                        <td><?= str_repeat('*', mb_strlen($user['Login'])); ?></td>
                        <td><?= str_repeat('*', mb_strlen($user['Password'])); ?></td>
                        <td></td>
                        <td><a href="/Users/Edit/<?= $user['id_user']; ?>"><img src="/icon-edit.png" class="action-icon"/></a></td>
                    </tr>
                    <?php
                })
                ?>
            </tr>
        </table>
    </div>
</div>

