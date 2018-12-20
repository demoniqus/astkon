<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/BuildObjectPartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\BuildObjectPartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class BuildObject extends BuildObjectPartial {

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}
}
