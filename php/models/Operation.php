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
}
