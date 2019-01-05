<thead>
    <tr>
    <?php
    foreach ($config as $fieldConfig) {
        if (array_key_exists('nodisplay', $fieldConfig)) {
            continue;
        }
        ?>
        <th scope="col" class="align-top">
            <?= $fieldConfig['caption'] ? $fieldConfig['caption'] : $fieldConfig['key']; ?>
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
