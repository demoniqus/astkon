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
            <a href="/BuildObjects/Edit/0" class="btn btn-success">Добавить объект</a>
        </div>
        <table>
            <tr>
                <th><?= \Astkon\Model\BuildObject::getFieldAlias('BuildObjectName'); ?></th>
                <th><?= \Astkon\Model\BuildObject::getFieldAlias('Comment'); ?></th>
                <th></th>
                <?php
                array_walk($buildObjects, function($buildObject){
                    ?>
                    <tr>
                        <td><?= $buildObject['build_object_name']; ?></td>
                        <td><?= $buildObject['comment']; ?></td>
                        <td><a href="/BuildObjects/Edit/<?=  $buildObject['id_people']; ?>"><img src="/icon-edit.png" class="action-icon"/></a></td>
                    </tr>
                    <?php
                })
                ?>
            </tr>
        </table>
    </div>
</div>

