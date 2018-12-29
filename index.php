<?PHP
namespace Astkon;

use Astkon\View\View;

session_start();

require_once  '.' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'main_require.php';

//\Astkon\Model\Model::UpdateModelPhpCode();

$key = 'CurrentUser';
$user = null;
unset ($GLOBALS[$key]);
if (!isset($_SESSION[$key]) || !isset($GLOBALS[$key]) || !$_SESSION[$key] || !$GLOBALS[$key]) {

    $_SESSION[$key] = $GLOBALS[$key] = array(
        'Role' => 'Guest',
    );
}


/**
 * Функция дополняет части запрошенного пути соответствующими им обозначениями типа Controller, Action
 * @param $pathPart
 * @param $type
 * @return string
 */
function setPathPartType($pathPart, $type) {
    $pos = mb_strpos(mb_strtolower($pathPart), $type);
    if ($pos !== false) {
        $pathPart = substr($pathPart, 0, $pos + 1);
    }
    return ucfirst(mb_strtolower($pathPart)) . ucfirst($type);
}

$requestUri = $_SERVER['REQUEST_URI'];
if (mb_strpos($requestUri, '?') !== false) {
    $requestUri = explode('?', $requestUri)[0];
}

if (strpos( $requestUri, '/') === 0) {
    $requestUri = mb_substr($requestUri, 1);
}
$requestUri = explode('/', $requestUri);
$requestUri = (new linq($requestUri))
    ->where(function($item){return !preg_match('/\.php/i', $item);})
    ->getData();
if (count($requestUri) > 3) {
    //Ошибка bad request, поскольку PHP_SELF не содержит параметров, в которых могли бы встречаться слеши
}

while (count($requestUri) < 3) {
    $requestUri[] = '';
}


$index = 0;
$controller = $requestUri[$index] === '' ? 'index' : $requestUri[$index];
$controller = setPathPartType($controller, 'controller');

$index = 1;
$action = $requestUri[$index] === '' ? 'index' : $requestUri[$index];
$action = setPathPartType($action, 'action');

$index = 2;
$requestUri[$index] = $requestUri[$index] === '' ? null : $requestUri[$index];

//var_dump((new linq(get_declared_classes()))->where(function($item){ return strpos($item, 'stkon')!== false;})->getData());
/*Получаем имя контроллера и его метода для вызова*/
$controllerNamespace = 'Astkon\Controllers\\';

$controller = $controllerNamespace . $controller;

if (class_exists($controller)) {
    $controller::Run($action, array('id' => $requestUri[2]));
}
else {
    (new View())->error(404);
}


//$reflectClass = new \ReflectionClass('Astkon\Model\Partial\UserPartial');
//foreach ($reflectClass->getProperties() as $prop) {
//    $reflectProp = new \ReflectionProperty(UserPartial::class, 'IdUser');
//    echo $reflectProp->getDocComment() . PHP_EOL;
//}


//(function () {
//    $key = 'CurrentUser';
//    $user = null;
//    if (isset($_SESSION[$key])) {
//        $user = $_SESSION[$key];
//    }
//    else {
//        $user = new User(array(
//            'Role' => 'Guest',
//        ));
//        $_SESSION[$key];
//    }
//    return $user;
//
//})()