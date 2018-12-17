
<div class="left-menu col-lg-2">
    <ul class="nav flex-column">
        <?php
            $menu = array(
                array(
                    'Action' => 'Entrance/EntranceList',
                    'Caption' => 'Поступления'
                ),
                array(
                    'Action' => 'Entrance/LeavingList',
                    'Caption' => 'Выбытие'
                ),
                array(
                    'Action' => 'Settings/Index',
                    'Caption' => 'Настройки'
                ),
                array(
                    'Action' => 'User/List',
                    'Caption' => 'Пользователи'
                ),
            );
            if (!isset($activeMenu)) {
                $activeMenu = null;
            }
            array_walk($menu, function($menuItem) use ($activeMenu) {
                ?>
                <li class="nav-item">
                    <a class="nav-link<?php if (isset($activeMenu) && $activeMenu === $menuItem['Action']) echo ' active'; ?>" href="<?= $menuItem['Action']; ?>"><?= $menuItem['Caption']; ?></a>
                </li>
        <?php
            });
        ?>
    </ul>
</div>