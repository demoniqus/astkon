
<div class="row mx-0">
    <?php use Astkon\GlobalConst;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        <div class="container-fluid">


            <?php
            $menu = array(
                array(
                    'Action' => '/Operations/IncomeForm',
                    'Caption' => 'Поступление на склад',
                    'Icon' => '/receipt.jpg',
                ),
                array(
                    'Action' => '/Operations/ExpenditureForm',
                    'Caption' => 'Расход со склада',
                    'Icon' => '/leaving.png',
                ),
                array(
                    'Action' => '/Operations/WriteoffForm',
                    'Caption' => 'Списание',
                    'Icon' => '/write-off.png',
                    'NewRow' => true,
                ),
                array(
                    'Action' => '/Operations/FreeRentForm',
                    'Caption' => 'Временное пользование',
                    'Icon' => '/tools-pict.png',
                ),
            );

            \Astkon\Lib\TileMenu($menu, 4, 2);
            ?>

        </div>
    </div>
</div>

