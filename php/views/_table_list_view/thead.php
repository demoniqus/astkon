<thead>
    <tr>
    <?php
    foreach ($config as $fieldConfig) {
        if (array_key_exists('nodisplay', $fieldConfig)) {
            continue;
        }
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
