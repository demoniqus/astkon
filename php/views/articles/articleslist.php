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
                array_walk($articles, function($article){
                    ?>
                    <tr>
                        <td><?= $article['measure_name']; ?></td>
                        <td><?= $article['is_split']; ?></td>
                        <td><?= $article['is_split'] == 1 ? $article['precision'] : ''; ?></td>
                        <td><a href="/Article/Edit/<?=  $article['id_article']; ?>"><img src="/icon-edit.png" class="action-icon"/></a></td>
                    </tr>
                    <?php
                })
                ?>
            </tr>
        </table>
    </div>
</div>

