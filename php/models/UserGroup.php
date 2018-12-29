<?php
namespace Astkon\Model;

use  Astkon\GlobalConst;
use  Astkon\DataBase;
use  Astkon\Model\Partial\UserGroupPartial;

require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::PartialModelsDirectory . DIRECTORY_SEPARATOR . 'UserGroupPartial.php';

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class UserGroup extends UserGroupPartial {

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}
}
