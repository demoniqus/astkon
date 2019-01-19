<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/OperationPartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\OperationPartial;
use Astkon\QueryConfig;
use Astkon\Traits\FullModelMethods;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class Operation extends OperationPartial {

    use FullModelMethods;

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}

    public static function getItems($operation, ?array $requiredFields = null, ?Database $db = null, int $deepDecodeForeignKeys = 0) {
	    $queryConfig = new QueryConfig(
            '`' . OperationItem::DataTable . '`.`' . static::PrimaryColumnKey . '` = ' . (is_array($operation) ? $operation[Operation::PrimaryColumnKey] : $operation),
            $requiredFields
        );
        return OperationItem::getRows($db, $queryConfig, $deepDecodeForeignKeys);
    }
}
