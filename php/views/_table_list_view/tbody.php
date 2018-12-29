<tbody>
    <?php
    foreach ($items as $item) {
        ?>
        <tr data-item="<?= htmlspecialchars(json_encode($item)); ?>">
        <?php
        $PKVal = 0;
        foreach ($config as $fieldConfig) {
            $val = isset($item[$fieldConfig['key']]) ? $item[$fieldConfig['key']] : '';
            if ($fieldConfig['primary_key']) {
                ?>
                <th scope="row">
                    <?php
                        $PKVal = $val;
                        echo $val;
                    ?>
                </th>
                <?php
            } else if ($fieldConfig['foreign_key']) {
                ?>
                <td>
                    <?php
                    $fkName = '$fk_' . $fieldConfig['key'];
                    $val = isset($item[$fkName]) ? $item[$fkName]: $item[$fieldConfig['key']];
                    echo $val;
                    ?>
                </td>
                <?php
            }else {
                ?>
                <td>
                    <?= $val; ?>
                </td>
                <?php
            }
        }
        foreach($options as $option) {
            ?>
            <td>
                <a
                    href="<?= isset($option['action']) ? $option['action'] . '/' . $PKVal : 'javascript:void(0);'; ?>"
                    onclick="<?= isset($option['click']) ? $option['click'] : 'return true;'; ?>">
                    <img src="<?= $option['icon']; ?>" title="<?= isset($option['title']) ? $option['title'] : ''; ?>" class="action-icon" />
                </a>

            </td>
            <?php
        }
        ?>
        </tr>
        <?php
    }
    ?>
</tbody>
