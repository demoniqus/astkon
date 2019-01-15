<style type="text/css">
    .left-menu .nav-link {
        text-overflow: ellipsis;
        overflow-x: hidden;
    }
</style>
<div class="left-menu col-md-2 mb-3">
    <ul class="nav flex-column">
        <?php

        use Astkon\Model\User;

        if (CURRENT_USER['IsAdmin']) {
                $leftMenuItems = array(
                    array(
                        'Action' => '/',
                        'Caption' => 'Главная',
                    ),
                    //                array(
                    //                    'Action' => '/Articles/ArticlesList',
                    //                    'Caption' => 'Артикулы',
                    //                    'Icon' => '/barcode.png',
                    //                ),
//                    array(
//                        'Action' => '/Operations/Index',
//                        'Caption' => 'Операции',
//                        'Icon' => '/operations-icon.png',
//                    ),
                    array(
                        'Action' => '/Dictionaries/Index',
                        'Caption' => 'Справочники',
                        'Icon' => '/refbook.png',
                    ),
                    array(
                        'Action' => '/Operations/Index',
                        'Caption' => 'Операции',
                        'Icon' => '/operations-icon.png',
                    ),
                    array(
                        'Action' => '/Users/UsersList',
                        'Caption' => 'Пользователи',
                        'Icon' => '/users.png',
                    ),
                );
            }
            else {
                $leftMenuItems = array(
                    array(
                        'Action' => '/',
                        'Caption' => 'Главная',
                    ),
                    //                array(
                    //                    'Action' => '/Articles/ArticlesList',
                    //                    'Caption' => 'Артикулы',
                    //                    'Icon' => '/barcode.png',
                    //                ),
                    array(
                        'Action' => '/Operations/Index',
                        'Caption' => 'Операции',
                        'Icon' => '/operations-icon.png',
                    ),
                    array(
                        'Action' => '/Dictionaries/Index',
                        'Caption' => 'Справочники',
                        'Icon' => '/refbook.png',
                    ),
//                    array(
//                        'Action' => '/Users/UsersList',
//                        'Caption' => 'Пользователи',
//                        'Icon' => '/users.png',
//                    ),
                );
            }
            $leftMenuItems[] = array(
                'Action' => '/Users/ReservesList/' . CURRENT_USER[User::PrimaryColumnName],
                'Caption' => 'Инструмент в пользовании',
                'Icon' => '/tools-pict-time.png',
            );

            if (!isset($activeMenu)) {
                $activeMenu = null;
            }
            array_walk($leftMenuItems, function($menuItem) use ($activeMenu) {
                ?>
                <li class="nav-item<?php if (isset($activeMenu) && strtolower($activeMenu) === strtolower($menuItem['Action'])) echo ' btn btn-outline-primary p-0 text-left'; ?>">
                    <a class="nav-link" href="<?= $menuItem['Action']; ?>" title="<?= $menuItem['Caption']; ?>"><?= $menuItem['Caption']; ?></a>
                </li>
        <?php
            });
        ?>
    </ul>
</div>