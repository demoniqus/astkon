<?php
namespace Astkon\Model;

use Astkon\DataBase;
use Astkon\GlobalConst;
use Astkon\Model\Partial\OperationTypePartial;
use Astkon\QueryConfig;
use Astkon\Traits\FullModelMethods;

require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::PartialModelsDirectory . DIRECTORY_SEPARATOR . 'OperationTypePartial.php';

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class OperationType extends OperationTypePartial {

    use FullModelMethods;

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}

	public static function getLabel(string $operationKey) {
	    $queryConfig = new QueryConfig(
            '`operation_name` = :operation_name',
            null,
            array('operation_name' => $operationKey)
        );
	    $operationType = (new DataBase())->operation_type->getFirstRow($queryConfig);
	    return isset($operationType) ? $operationType['operation_label'] : '';
    }
}
