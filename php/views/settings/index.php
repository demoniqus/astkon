<div class="row mx-0">
    <?php use Astkon\GlobalConst;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        <?php
        $menu = array(
            array(
                'Action' => '/Measures/MeasuresList',
                'Caption' => 'Единицы измерения',
                'Icon' => '/measures.png',
            ),

        );

        \Astkon\Lib\TileMenu($menu, 2, 2);
        ?>
    </div>
</div>

