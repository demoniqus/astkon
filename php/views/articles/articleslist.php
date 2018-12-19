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
            <a href="/Articles/Edit/0" class="btn btn-success">Добавить артикул</a>
        </div>
        <table>
            <tr>
                <th><?= \Astkon\Model\Article::getFieldAlias('VendorCode'); ?></th>
                <th>Делимая величина</th>
                <th>Точность деления (дес.зн.)</th>
                <th></th>
                <?php
                array_walk($measures, function($measure){
                    ?>
                    <tr>
                        <td><?= $measure['measure_name']; ?></td>
                        <td><?= $measure['is_split']; ?></td>
                        <td><?= $measure['is_split'] == 1 ? $measure['precision'] : ''; ?></td>
                        <td><a href="/Measures/Edit/<?=  $measure['id_measure']; ?>"><img src="/icon-edit.png" class="action-icon"/></a></td>
                    </tr>
                    <?php
                })
                ?>
            </tr>
        </table>
    </div>
</div>

