<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/UserPartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\UserPartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class User extends UserPartial {

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}

    /**
     * @nodisplay true
     * @data_type string
     * @form_edit_order 2.1
     * @alias Подтверждение пароля
     * @var string
     */
	public $PasswordConfirm;
}
