<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/MeasurePartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\MeasurePartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class Measure extends MeasurePartial {

public function __construct (array $fields = array()) {
	parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
}

}
