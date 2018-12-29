
<div class="left-menu col-lg-2">
    <ul class="nav flex-column">
        <?php
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
                array(
                    'Action' => '/Users/UsersList',
                    'Caption' => 'Пользователи',
                    'Icon' => '/users.png',
                ),
            );
            if (!isset($activeMenu)) {
                $activeMenu = null;
            }
            array_walk($leftMenuItems, function($menuItem) use ($activeMenu) {
                ?>
                <li class="nav-item<?php if (isset($activeMenu) && strtolower($activeMenu) === strtolower($menuItem['Action'])) echo ' btn btn-outline-primary p-0 text-left'; ?>">
                    <a class="nav-link" href="<?= $menuItem['Action']; ?>"><?= $menuItem['Caption']; ?></a>
                </li>
        <?php
            });
        ?>
    </ul>
</div>