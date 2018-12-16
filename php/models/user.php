<?php
namespace Astkon\Model;

require_once __DIR__ . '/../partialModels/userPartial.php';

use Astkon\DataBase;
use  Astkon\Model\Partial\UserPartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class User extends UserPartial {
    public function __construct(array $fields = array())
    {
        foreach ($fields as $fieldName => $fieldValue) {
            $this->$fieldName = $fieldValue;
        }
        parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
    }

    public $Role;
}
