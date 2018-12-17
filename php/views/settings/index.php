<style type="text/css">
    .rounded-rectangle {
        border: 1px solid transparent;;
        border-top-left-radius: 10px;
        border-bottom-right-radius: 10px;
        border-top-color: #add8e6;
        border-left-color: #add8e6;
        vertical-align: top;
        position: relative;
        box-sizing: border-box;
        cursor: pointer;
        -webkit-border-bottom-right-radius: 12px;
        -moz-border-bottom-right-radius: 12px;
        -o-border-bottom-right-radius: 12px;
        -ms-border-bottom-right-radius: 12px;
        border-bottom-right-radius: 12px;
        -webkit-border-top-left-radius: 8px;
        -moz-border-top-left-radius: 8px;
        -o-border-top-left-radius: 8px;
        -ms-border-top-left-radius: 8px;
        border-top-left-radius: 8px;
        border-right-color: #add8e6;
        border-bottom-color: #add8e6;
        box-shadow: lightskyblue 3px 3px 5px;
        margin-bottom: 8px;
        vertical-align: top;
    }
</style>

<div class="row mx-0">
    <?php use Astkon\GlobalConst;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php'; ?>
    <div class="col-lg text-center">
        <div class="container-fluid">
            <div class="row">


            <?php
            $menu = array(
                array(
                    'Action' => '/Measures/MeasuresList',
                    'Caption' => 'Единицы измерения',
                    'Icon' => '/setting-measures-icon.png',
                ),
                array(
                    'Action' => '/Measures/MeasuresList',
                    'Caption' => 'Единицы измерения',
                    'Icon' => '/setting-measures-icon.png',
                ),
                array(
                    'Action' => '/Measures/MeasuresList',
                    'Caption' => 'Единицы измерения',
                    'Icon' => '/setting-measures-icon.png',
                ),
                array(
                    'Action' => '/Measures/MeasuresList',
                    'Caption' => 'Единицы измерения',
                    'Icon' => '/setting-measures-icon.png',
                ),
                array(
                    'Action' => '/Measures/MeasuresList',
                    'Caption' => 'Единицы измерения',
                    'Icon' => '/setting-measures-icon.png',
                ),
                array(
                    'Action' => '/Measures/MeasuresList',
                    'Caption' => 'Единицы измерения',
                    'Icon' => '/setting-measures-icon.png',
                ),
                array(
                    'Action' => '/Measures/MeasuresList',
                    'Caption' => 'Единицы измерения',
                    'Icon' => '/setting-measures-icon.png',
                ),

            );
            array_walk($menu, function($menuItem) {
                ?>
                <div class="col-md-3 rounded-rectangle p-2 mr-1" onclick="window.location.href ='<?= $menuItem['Action']; ?>'">
                    <?php if (isset($menuItem['Icon']) && $menuItem['Icon']) {
                        ?>
                        <img src="<?= $menuItem['Icon']; ?>" />
                        <?php
                    }?>
                    <p>
                        <?=  $menuItem['Caption']; ?>
                    </p>
                </div>
                <?php
            });
            ?>

            </div>
        </div>
    </div>
</div>
