<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/OperationStatePartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\OperationStatePartial;
use Astkon\Traits\FullModelMethods;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class OperationState extends OperationStatePartial {

    use FullModelMethods;

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}
}
