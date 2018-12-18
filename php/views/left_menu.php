
<div class="left-menu col-lg-2">
    <ul class="nav flex-column">
        <?php
            $menu = array(
                array(
                    'Action' => '/',
                    'Caption' => 'Главная'
                ),
                array(
                    'Action' => '/Articles/List',
                    'Caption' => 'Артикулы'
                ),
                array(
                    'Action' => '/Operations/Index',
                    'Caption' => 'Операции'
                ),
                array(
                    'Action' => '/Settings/Index',
                    'Caption' => 'Настройки'
                ),
                array(
                    'Action' => '/User/List',
                    'Caption' => 'Пользователи'
                ),
            );
            if (!isset($activeMenu)) {
                $activeMenu = null;
            }
            array_walk($menu, function($menuItem) use ($activeMenu) {
                ?>
                <li class="nav-item<?php if (isset($activeMenu) && strtolower($activeMenu) === strtolower($menuItem['Action'])) echo ' btn btn-outline-primary p0 text-left'; ?>">
                    <a class="nav-link" href="<?= $menuItem['Action']; ?>"><?= $menuItem['Caption']; ?></a>
                </li>
        <?php
            });
        ?>
    </ul>
</div>