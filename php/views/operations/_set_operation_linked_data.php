<?php

if (isset($linkedData)) {
    ?>
    <script type="application/javascript">
     <?php
    foreach ($linkedData as $model => $linkedItems) {
        ?>
        select<?= $model::Name(); ?>(
            $('#<?= $model::Name(); ?>ListItems'),
            '<?= $model::PrimaryColumnName; ?>',
            <?= json_encode($linkedItems); ?>,
            null
        );
     <?php
    }
    ?>
     </script>
     <?php
    }
?>