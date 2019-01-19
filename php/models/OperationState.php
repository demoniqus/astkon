<?php
namespace Astkon\Model;

use Astkon\DataBase;
use Astkon\GlobalConst;
use Astkon\Model\Partial\OperationStatePartial;
use Astkon\Traits\FullModelMethods;

require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::PartialModelsDirectory . DIRECTORY_SEPARATOR . 'OperationStatePartial.php';

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class OperationState extends OperationStatePartial {

    use FullModelMethods;

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}
}
