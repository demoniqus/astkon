<?php
namespace Astkon\Lib;

use Astkon\Controllers\ArticlesController;
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
function array_keys_CamelCase($a) {
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

function cleanedDump($value) {
    ob_clean();
    var_dump($value);
    die();
}

function getReferer() {
    $referer = null;
    if (isset($_SERVER['HTTP_REFERER'])) {
        $matches = null;
        if (
            preg_match(
                '=[^:]*:/+' . str_replace('.', '\\.', $_SERVER['HTTP_HOST']) . '(/[a-z0-9]+)?(/[a-z0-9]+)?(/[a-z0-9]+)?(/?\?.+)?=i',
                $_SERVER['HTTP_REFERER'],
                $matches
            ) &&
            count($matches) > 1
        ) {

            $referer = array(
                'ControllerName' => isset($matches[1]) ? $matches[1] : 'Index',
                'Action' => isset($matches[2]) ? $matches[2] : 'Index',
                'Id' => isset($matches[3]) ? $matches[3] : null,
                'QueryString' => isset($matches[4]) ? $matches[4] : 'Index',
            );
            foreach ($referer as $k => $v) {
                if ($v[0] === '/') {
                    $referer[$k] = substr($v, 1);
                }
            }
            $k = 'QueryString';
            if ($referer[$k][0] === '?') {
                $referer[$k] = substr($referer[$k], 1);
            }

            $controllerNS = explode('\\', ArticlesController::class);
            $controllerNS[count($controllerNS) - 1] = $referer['ControllerName'] . 'Controller';
            $requiredController = implode('\\', $controllerNS);
            $referer['Controller'] = $requiredController;
        }
    }
    return $referer;
}