<?php
namespace Astkon\Lib;

use Astkon\DataBase;
use Astkon\ErrorCode;
use Astkon\View\View;

function TileMenu (array $tiles, int $tileColumnsCount = 0, int $tilesInRow = 0) {
    $rows = array();
    if ($tilesInRow === 0) {
        $rows[] = $tiles;
    }
    else if ($tilesInRow > 0){
        $rows = array_chunk($tiles, $tilesInRow);
    }
    else {
        $view = new View();
        $view->trace = array(
            'errorCode' => '00000',
            'errorMessage' => 'Неверное количество плиток на одной строке в плиточном меню',
            'method' => array_pop(debug_backtrace(2, 2))
        );
        $view->error(ErrorCode::PROGRAMMER_ERROR);
        die();
    }

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
                <div class="<?= $cssClass; ?> tail-item lightskyblue p-2 mr-1" onclick="window.location.href ='<?= $tile['Action']; ?>'">
                    <?php if (isset($tile['Icon']) && $tile['Icon']) {
                        ?>
                        <img src="<?= $tile['Icon']; ?>"  alt=""/>
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


function Redirect(string $controller, string $action = null, string $id = null) {
    $path = array($controller);
    $path[] = $action ?? 'index';
    if ($id !== null) {
        $path[] = $id;
    }
    RedirectToUrl('/' . implode('/', $path));

}

function RedirectToUrl(string $url) {
    ob_clean();
    header('Location: ' . $url);
    die();
}

/**
 * @param array|null $a
 * @return array
 */
function array_keys_CameCase($a) {
    if (is_null($a)) {
        return $a;
    }
    $r = array();
    foreach ($a as $k => $v) {
        $r[DataBase::underscoreToCamelCase($k)] = $v;
    }
    return $r;
}

function array_keys_underscore($a) {
    if (is_null($a)) {
        return $a;
    }
    $r = array();
    foreach ($a as $k => $v) {
        $r[DataBase::camelCaseToUnderscore($k)] = $v;
    }
    return $r;
}