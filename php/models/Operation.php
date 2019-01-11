<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/OperationPartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\OperationPartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class Operation extends OperationPartial {

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}

    public static function getItems($operation, ?array $requiredFields = null, ?Database $db = null) {
        return OperationItem::getRows(
            $db,
            '`' . static::PrimaryColumnKey . '` = ' . (is_array($operation) ? $operation[Operation::PrimaryColumnKey] : $operation),
            $requiredFields
        );
    }
}
