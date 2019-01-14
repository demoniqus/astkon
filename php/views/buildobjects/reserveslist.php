<style type="text/css">

</style>

<div class="row mx-0">
    <?php

    use Astkon\Controllers\BuildObjectsController;
    use Astkon\Controllers\OperationsController;
    use Astkon\GlobalConst;
    use Astkon\Model\Operation;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-md text-left">
        <div class="mb-2">
            Резерв <span class="alert alert-warning py-1"> Объект (#<?= $item[$targetModel::PrimaryColumnKey]; ?>) <?= $item[$linkedDataCaprionFieldName]; ?></span>
        </div>
        <table class="table table-hover my-2 table-sm">
            <tr>
                <th scope="col">Артикул</th>
                <th scope="col">Единица измерения</th>
                <th scope="col">Количество</th>
                <th scope="col">Идентификатор документа</th>
            </tr>

        <?php
        foreach ($rows as $row) {
            ?>
            <tr>
                <td><?= $row['article_name']; ?></td>
                <td><?= $row['measure_name']; ?></td>
                <td><?= $row['operation_count']; ?></td>
                <td><a href="/<?= OperationsController::Name(); ?>/Detail/<?= $row[Operation::PrimaryColumnKey]; ?>"><?= $row[Operation::PrimaryColumnKey]; ?></a></td>
            </tr>
            <?php
        }
        ?>
        </table>
        <a href="/<?= BuildObjectsController::Name() . '/BuildObjectsList'; ?>" class="btn btn-outline-secondary">К списку объектов</a>
    </div>
</div>
