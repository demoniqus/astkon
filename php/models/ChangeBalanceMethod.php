<?php
namespace Astkon\Model;

use Astkon\GlobalConst;
use Astkon\DataBase;
use Astkon\Model\Partial\ChangeBalanceMethodPartial;
use Astkon\Traits\FullModelMethods;

require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::PartialModelsDirectory . DIRECTORY_SEPARATOR . 'ChangeBalanceMethodPartial.php';

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class ChangeBalanceMethod extends ChangeBalanceMethodPartial {

    use FullModelMethods;

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}
}
