<?php

use Astkon\Model\Article;

if (isset($selectedItems)) {
        ?>
        <script type="application/javascript">
            setSelectedArticlesAsEditable(
                $('#OperationListItems').find('.container-fluid:first'),
                '<?= Article::PrimaryColumnName; ?>',
                <?= json_encode($selectedItems); ?>
            );
        </script>
        <?php
    }
?>