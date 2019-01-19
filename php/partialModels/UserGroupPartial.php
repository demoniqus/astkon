<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться.
 * Допускается вручную расширять foreign_key в $fieldsInfo. 
 * При этом ключи model и field изменять не допускается - при обновлении модели в случае их изменения может быть утрачена прочая информация */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class UserGroupPartial extends Model {
	const DataTable = 'user_group';
	const PrimaryColumnName = 'IdUserGroup';
	const PrimaryColumnKey = 'id_user_group';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdUserGroup' => 
  array (
    'table_name' => 'user_group',
    'column_name' => 'id_user_group',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
    'external_link' => 
    array (
      'article_balance' => 
      array (
        'model' => 'article_balance',
        'field' => 'id_user_group',
      ),
      'operation' => 
      array (
        'model' => 'operation',
        'field' => 'id_user_group',
      ),
      'user' => 
      array (
        'model' => 'user',
        'field' => 'id_user_group',
      ),
    ),
  ),
  'UserGroupName' => 
  array (
    'table_name' => 'user_group',
    'column_name' => 'user_group_name',
    'data_type' => 'varchar',
    'max_length' => '100',
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'Comment' => 
  array (
    'table_name' => 'user_group',
    'column_name' => 'comment',
    'data_type' => 'varchar',
    'max_length' => '3500',
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
);
	/**
	* @form_edit_order 2
	* @list_view_order 2
	* @database_column_name comment
	* @caption Комментарий
	* @var string
	*/
	public $Comment;

	/**
	* @database_column_name id_user_group
	* @caption Идентификатор
	* @var int
	*/
	public $IdUserGroup;

	/**
	* @form_edit_order 1
	* @list_view_order 1
	* @foreign_key_display_value
	* @database_column_name user_group_name
	* @caption Наименование группы
	* @var string
	*/
	public $UserGroupName;

}
