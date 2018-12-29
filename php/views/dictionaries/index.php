<div class="row mx-0">
    <?php use Astkon\GlobalConst;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        <?php
        $menu = array(
            array(
                'Action' => '/Articles/ArticlesList',
                'Caption' => 'Артикулы',
                'Icon' => '/barcode.png',
            ),
            array(
                'Action' => '/Measures/MeasuresList',
                'Caption' => 'Единицы измерения',
                'Icon' => '/measures.png',
            ),
            array(
                'Action' => '/BuildObjects/BuildObjectsList',
                'Caption' => 'Объекты',
                'Icon' => '/building.png',
            ),
            array(
                'Action' => '/People/PeopleList',
                'Caption' => 'Люди',
                'Icon' => '/people.jpg',
            ),

        );

        \Astkon\Lib\TileMenu($menu, 4, 2);
        ?>
    </div>
</div>

