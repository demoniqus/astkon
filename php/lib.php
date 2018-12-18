<?php
namespace Astkon\Lib;

function TileMenu (array $tiles, int $tileColumnsCount = 0, int $tilesInRow = 0) {
    $rows = array();
    if ($tilesInRow === 0) {
        $rows[] = $tiles;
    }
    else if ($tilesInRow > 0){
        $rows = array_chunk($tiles, $tilesInRow);
    }
    else {
        //ошибка
    }
//    var_dump($rows);
//    die();
    $cssClass = 'col-md';
    if ($tileColumnsCount > 0) {
        $cssClass .= '-' . $tileColumnsCount;
    }

    foreach ($rows as $tilesSet) {
        ?>
        <div class="row">
            <?php
            foreach ($tilesSet as $tile) {
                ?>
                <div class="<?= $cssClass; ?> tail-item p-2 mr-1" onclick="window.location.href ='<?= $tile['Action']; ?>'">
                    <?php if (isset($tile['Icon']) && $tile['Icon']) {
                        ?>
                        <img src="<?= $tile['Icon']; ?>" />
                        <?php
                    }?>
                    <div>
                        <?=  $tile['Caption']; ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

}