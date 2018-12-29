<thead>
    <tr>
    <?php
    foreach ($config as $fieldConfig) {
        ?>
        <th scope="col" class="align-top">
            <?= $fieldConfig['alias'] ? $fieldConfig['alias'] : $fieldConfig['key']; ?>
        </th>
        <?php
    }
    foreach ($options as $option) {
        ?>
        <th scope="col"></th>
        <?php
    }
    ?>
    </tr>
</thead>
