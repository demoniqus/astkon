<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/OperationItemPartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\OperationItemPartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class OperationItem extends OperationItemPartial {

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}
}
