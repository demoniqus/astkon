<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/UserPartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\UserPartial;
use Astkon\Traits\FullModelMethods;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class User extends UserPartial {

    use FullModelMethods;

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}

    /**
     * @nodisplay true
     * @password true
     * @data_type string
     * @form_edit_order 2.1
     * @caption Подтверждение пароля
     * @var string
     */
	public $PasswordConfirm;
}
