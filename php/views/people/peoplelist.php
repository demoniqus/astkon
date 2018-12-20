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
            <a href="/People/Edit/0" class="btn btn-success">Добавить человека</a>
        </div>
        <table>
            <tr>
                <th><?= \Astkon\Model\People::getFieldAlias('PeopleName'); ?></th>
                <th><?= \Astkon\Model\People::getFieldAlias('PostName'); ?></th>
                <th></th>
                <?php
                array_walk($peoples, function($people){
                    ?>
                    <tr>
                        <td><?= $people['people_name']; ?></td>
                        <td><?= $people['post_name']; ?></td>
                        <td><a href="/People/Edit/<?=  $people['id_people']; ?>"><img src="/icon-edit.png" class="action-icon"/></a></td>
                    </tr>
                    <?php
                })
                ?>
            </tr>
        </table>
    </div>
</div>
