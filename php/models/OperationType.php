<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/OperationTypePartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\OperationTypePartial;
use Astkon\QueryConfig;
use Astkon\Traits\FullModelMethods;

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
