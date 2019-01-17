<?php
/**
 * Created by PhpStorm.
 * User: demoniqus
 * Date: 16.01.19
 * Time: 13:19
 */

namespace Astkon\Traits;


trait FullModelMethods
{
    public static function getFieldAlias(string $fieldName) : string {
        return parent::getFieldAlias($fieldName);
    }

    public static function getRows(
        $db = null,
        $queryConfig = null,
        $deepDecodeForeignKeys = 0
    ) : array {
        return parent::getRows($db, $queryConfig, $deepDecodeForeignKeys);
    }

    public static function getCount(
        $db = null,
        $queryConfig = null,
        $deepDecodeForeignKeys = 0
    ) : int {
        return parent::getCount($db, $queryConfig, $deepDecodeForeignKeys);
    }

    public static function getFirstRow(
        $db = null,
        $queryConfig = null,
        $deepDecodeForeignKeys = 0
    ) : array {
        return parent::getFirstRow($db, $queryConfig, $deepDecodeForeignKeys);
    }

    public static function EditForm($item = array(), $options = array()) {
        parent::EditForm($item, $options);
    }

    public static function ReferenceDisplayedKeys() : array {
        return parent::ReferenceDisplayedKeys();
    }

    public static function SaveInstance($values) {
        return parent::SaveInstance($values);
    }

    public static function EmptyEntity($substitution) : array {
        return parent::EmptyEntity($substitution);
    }

    public static function Create($substitution, $db = null, $return = false) {
        return parent::Create($substitution, $db, $return);
    }

    public static function Update(array $substitution, $db = null, $return = false) {
        return parent::Update($substitution, $db, $return);
    }

    public static function GetByPrimaryKey($pk, $db = null) : ?array{
        return parent::GetByPrimaryKey($pk, $db);
    }

    public static function Delete($listId, $db = null) {
        parent::Delete($listId, $db);
    }

    public static function getConfigForListView($excludeFields = null) {
        return parent::getConfigForListView($excludeFields);
    }

}