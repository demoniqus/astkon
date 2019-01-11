<tbody>
    <?php

    use Astkon\Model\Model;

    foreach ($items as $item) {
        ?>
        <tr data-item="<?= htmlspecialchars(json_encode($item)); ?>">
        <?php
        $PKVal = 0;
        foreach ($config as $fieldConfig) {
            if (array_key_exists('nodisplay', $fieldConfig)) {
                continue;
            }
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
                        <a
                                href="<?php
                                    if (isset($option['action']))  {
                                        $action = explode('?', $option['action'], 2);
                                        $params = count($action) > 1 ? '?' . $action[1] : '';
                                        $action = str_replace('//', '/', $action[0] . '/');
                                        echo $action . $PKVal . $params;
                                    }
                                    else {
                                        echo 'javascript:void(0);';
                                    }
                                ?>"
                                onclick="<?= isset($option['click']) ? $option['click'] : 'return true;'; ?>">
                            <img src="<?= $option['icon']; ?>"
                                 title="<?= isset($option['title']) ? $option['title'] : ''; ?>" class="action-icon"/>
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
