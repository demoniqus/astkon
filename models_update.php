<?PHP
namespace Astkon;

use Astkon\Model\Model;
use Astkon\View\View;

require_once  '.' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'main_require.php';

try {
    Model::UpdateModelPhpCode();
}
catch (\Exception $exception) {
    $view = new View();
    $view->trace = array(
        'errorCode' => $exception->getCode(),
        'errorMessage' => $exception->getMessage(),
        'trace' => $exception->getTrace()
    );

    $view->error(ErrorCode::PROGRAMMER_ERROR);
    exit();
}