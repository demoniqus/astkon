<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/ArticlePartial.php';

use  Astkon\DataBase;

use  Astkon\Model\Partial\ArticlePartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class Article extends ArticlePartial {

    public function __construct (array $fields = array()) {
        parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
    }



}
