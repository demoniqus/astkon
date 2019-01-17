<?php

use Astkon\View\TableViewConfig;

/**
 * @var TableViewConfig
 */
$totalPagesCount = ceil($tableViewConfig->totalItemsCount / $tableViewConfig->pageSize);
if ($totalPagesCount > 1) {
    $bound = 3;
    $startPage = $tableViewConfig->currentPage > $bound ? $tableViewConfig->currentPage - $bound : 0;
    $endPage = $totalPagesCount - $tableViewConfig->currentPage > $bound ? $tableViewConfig->currentPage + $bound : $totalPagesCount;
    ?>
        <nav class="d-block text-center my-2">
            <ul class="pagination justify-content-center">
                <?php
                    while ($startPage < $endPage) {
                        ?>
                        <li class="page-item <?= intval($startPage) === intval($tableViewConfig->currentPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="javascript:void(0);" onclick="TableManager.get('<?= $tableViewConfig->id; ?>').setPage(<?= $startPage ; ?>);"><?= $startPage + 1; ?></a>
                        </li>
                        <?php
                        $startPage++;
                    }
                ?>
            </ul>
        </nav>
    <?php
}


