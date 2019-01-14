<style type="text/css">

</style>

<div class="row mx-0">
    <?php

    use Astkon\Controllers\OperationsController;
    use Astkon\GlobalConst;
    use Astkon\Model\Article;
    use Astkon\Model\Operation;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-left">
        <div class="mb-2">
            Резерв <span class="alert alert-warning py-1"> Артикул (#<?= $article[Article::PrimaryColumnKey]; ?>) <?= $article['article_name']; ?></span>
        </div>
        <table class="table table-hover my-2 table-sm">
            <tr>
                <th scope="col">Идентификатор документа</th>
                <th scope="col">Получатель</th>
                <th scope="col">Единица измерения</th>
                <th scope="col">Количество</th>
            </tr>

        <?php
        foreach ($rows as $row) {
            ?>
            <tr>
                <td><a href="/<?= OperationsController::Name(); ?>/Detail/<?= $row[Operation::PrimaryColumnKey]; ?>"><?= $row[Operation::PrimaryColumnKey]; ?></a></td>
                <td>
                    <?php
                    if (is_array($row['linked_data']) && count($row['linked_data']) > 0) {
                        $linkedData = array();
                        foreach ($row['linked_data'] as $modelName => $listLinkedItems) {
                            foreach ($listLinkedItems as $linkedItem) {
                                ?>
                                <p class="mb-0 pb-1"><a href=""><?= $linkedItem[$linkedDataCaprionFieldName]; ?></a></p>
                                <?php
                            }
                        }
                    }
                    ?>
                </td>
                <td><?= $row['measure_name']; ?></td>
                <td><?= $row['operation_count']; ?></td>
            </tr>
            <?php
        }
        ?>
        </table>
    </div>
</div>
