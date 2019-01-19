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

abstract class UserPartial extends Model {
	const DataTable = 'user';
	const PrimaryColumnName = 'IdUser';
	const PrimaryColumnKey = 'id_user';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdUser' => 
  array (
    'table_name' => 'user',
    'column_name' => 'id_user',
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
  ),
  'Login' => 
  array (
    'table_name' => 'user',
    'column_name' => 'login',
    'data_type' => 'varchar',
    'max_length' => '255',
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
  'IdUserGroup' => 
  array (
    'table_name' => 'user',
    'column_name' => 'id_user_group',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'user_group',
    'ref_column_name' => 'id_user_group',
    'foreign_key' => 
    array (
      'model' => 'user_group',
      'field' => 'id_user_group',
      'display_mode' => 'decode_id_to_string',
    ),
  ),
  'Password' => 
  array (
    'table_name' => 'user',
    'column_name' => 'password',
    'data_type' => 'varchar',
    'max_length' => '45',
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'Config' => 
  array (
    'table_name' => 'user',
    'column_name' => 'config',
    'data_type' => 'json',
    'max_length' => NULL,
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'HasAccount' => 
  array (
    'table_name' => 'user',
    'column_name' => 'has_account',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => '1',
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'UserName' => 
  array (
    'table_name' => 'user',
    'column_name' => 'user_name',
    'data_type' => 'varchar',
    'max_length' => '100',
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'IsAdmin' => 
  array (
    'table_name' => 'user',
    'column_name' => 'is_admin',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => '1',
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'IsDelete' => 
  array (
    'table_name' => 'user',
    'column_name' => 'is_delete',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => '1',
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
);
	/**
	* @nodisplay true
	* @noeditable true
	* @list_view_order 6
	* @database_column_name config
	* @caption Системная конфигурация
	* @var array
	*/
	public $Config;

	/**
	* @form_edit_order 4
	* @list_view_order 4
	* @database_column_name has_account
	* @caption Пользователь имеет аккаунт
	* @var bool
	*/
	public $HasAccount;

	/**
	* @database_column_name id_user
	* @caption Идентификатор
	* @var int
	*/
	public $IdUser;

	/**
	* @foreign_key_action UserGroups/UserGroupsDict
	* @form_edit_order 3
	* @list_view_order 3
	* @database_column_name id_user_group
	* @caption Группа
	* @var int
	*/
	public $IdUserGroup;

	/**
	* @form_edit_order 5
	* @list_view_order 5
	* @database_column_name is_admin
	* @caption Администратор
	* @var bool
	*/
	public $IsAdmin;

	/**
    * @nodisplay
    * @noeditable
	* @database_column_name is_delete
	* @caption Удаленный пользователь
	* @var bool
	*/
	public $IsDelete;

	/**
	* @form_edit_order 1
	* @list_view_order 1
	* @database_column_name login
	* @caption Логин
	* @var string
	*/
	public $Login;

	/**
	* @password true
	* @nodisplay true
	* @form_edit_order 2
	* @list_view_order 2
	* @save_wrapper password
	* @database_column_name password
	* @caption Пароль
	* @var string
	*/
	public $Password;

	/**
	* @form_edit_order 0
	* @list_view_order 0
	* @database_column_name user_name
	* @caption Имя пользователя
	* @var string
	*/
	public $UserName;

}
