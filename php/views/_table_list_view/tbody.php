<tbody>
    <?php

    use Astkon\Model\Model;

    foreach ($items as $item) {
        $PKVal = isset($item[$primaryKeyName]) ? $item[$primaryKeyName] : 0;
        ?>
        <tr data-item="<?= htmlspecialchars(json_encode($item)); ?>" class="<?= $primaryKeyName . $PKVal; ?>">
        <?php
        foreach ($config as $fieldConfig) {
            $val = isset($item[$fieldConfig['key']]) ? $item[$fieldConfig['key']] : '';
            if (array_key_exists('nodisplay', $fieldConfig)) {
                continue;
            }
            if ($fieldConfig['primary_key']) {
                ?>
                <th scope="row">
                    <?php
                        echo $val;
                    ?>
                </th>
                <?php
            } else if ($fieldConfig['foreign_key']) {
                ?>
                <td>
                    <?php
                    $fkName = Model::ForeignKeyPrefix . $fieldConfig['key'];
                    $val = isset($item[$fkName]) ? $item[$fkName]: $item[$fieldConfig['key']];
                    echo $val;
                    ?>
                </td>
                <?php
            }else {
                ?>
                <td>

                    <?php
                        if ($val instanceof DateTime) {
                            echo $val->format('d.m.Y');
                        }
                        else if (is_array($val)){
                            ?>
                            <p class="text-left">
                            <?php
                            var_dump($val);
//                            foreach ($val as $item) {
//                                ?>
<!--                                <div class="row text-nowrap">-->
<!--                                    <div class="col">--><?//= $item['label']; ?><!--</div>-->
<!--                                    <div class="col">--><?//= $item['caption']; ?><!--</div>-->
<!--                                </div>-->
<!--                                --><?php
//                            }
                        }
                        else {
                            echo $val;
                        }
                    ?>
                </td>
                <?php
            }
        }
        foreach($options as $option) {
            ?>
            <td>
                <?php
                    if(!isset($option['condition']) || $option['condition']($item) !== false) {
                        ?>
                        <a href="<?php
                                    if (isset($option['action']))  {
                                        $action = explode('?', $option['action'], 2);
                                        $params = count($action) > 1 ? '?' . $action[1] : '';
                                        $action = str_replace('//', '/', $action[0] . '/');
                                        echo $action . ($PKVal > 0 ? $PKVal : ''). $params;
                                    }
                                    else {
                                        echo 'javascript:void(0);';
                                    }
                                ?>"
                                onclick="<?php
                                if (isset($option['click'])) {
                                    $matches = null;

                                    if (preg_match_all('/@[A-Z]+/i', $option['click'], $matches)) {
                                        foreach ($matches as $match) {
                                            $replacement = $item[mb_substr($match[0], 1)];
                                            $option['click'] = str_replace($match[0], $replacement, $option['click']);
                                        }
                                    }
                                    echo $option['click'];
                                }
                                else {

                                    echo 'return true;';
                                }
                                ?>">
                            <img src="<?= $option['icon']; ?>"
                                 title="<?= isset($option['title']) ? $option['title'] : ''; ?>"
                                 class="action-icon <?= isset($option['class']) ? $option['class'] : '';?>"/>
                        </a>
                        <?php
                    }
                ?>

            </td>
            <?php
        }
        ?>
        </tr>
        <?php
    }
    ?>
</tbody>
