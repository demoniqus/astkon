<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/OperationStatePartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\OperationStatePartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class OperationState extends OperationStatePartial {

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}
}
