<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/PeoplePartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\PeoplePartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class People extends PeoplePartial {

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}
}
