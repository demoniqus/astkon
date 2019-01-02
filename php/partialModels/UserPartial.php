<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class UserPartial extends Model {
	const DataTable = 'user';
	const PrimaryColumnName = 'IdUser';
	const PrimaryColumnKey = 'id_user';

/** @var array */
protected static $fieldsInfo = array (
  'IdUser' => 
  array (
    'table_name' => 'user',
    'column_name' => 'id_user',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'Login' => 
  array (
    'table_name' => 'user',
    'column_name' => 'login',
    'data_type' => 'varchar',
    'max_length' => '255',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'Password' => 
  array (
    'table_name' => 'user',
    'column_name' => 'password',
    'data_type' => 'varchar',
    'max_length' => '45',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'Config' => 
  array (
    'table_name' => 'user',
    'column_name' => 'config',
    'data_type' => 'json',
    'max_length' => NULL,
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
  ),
  'HasAccount' => 
  array (
    'table_name' => 'user',
    'column_name' => 'has_account',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => '1',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'IdUserGroup' => 
  array (
    'table_name' => 'user',
    'column_name' => 'id_user_group',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'foreign_key' => 
    array (
      'model' => 'user_group',
      'field' => 'id_user_group',
    ),
  ),
  'UserName' => 
  array (
    'table_name' => 'user',
    'column_name' => 'user_name',
    'data_type' => 'varchar',
    'max_length' => '100',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'IsAdmin' => 
  array (
    'table_name' => 'user',
    'column_name' => 'is_admin',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => '1',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
);
	/**
    * @nodisplay true
	* @noeditable true
	* @list_view_order 6
	* @database_column_name config
	* @alias Системная конфигурация
	* @var array
	*/
	public $Config;

	/**
	* @form_edit_order 4
	* @list_view_order 4
	* @database_column_name has_account
	* @alias Пользователь имеет аккаунт
	* @var bool
	*/
	public $HasAccount;

	/**
	* @database_column_name id_user
	* @alias Идентификатор
	* @var int
	*/
	public $IdUser;

	/**
    * @foreign_key_action UserGroups/UserGroupsDict
	* @form_edit_order 3
	* @list_view_order 3
	* @database_column_name id_user_group
	* @alias Группа
	* @var int
	*/
	public $IdUserGroup;

	/**
	* @form_edit_order 5
	* @list_view_order 5
	* @database_column_name is_admin
	* @alias Администратор
	* @var bool
	*/
	public $IsAdmin;

	/**
	* @form_edit_order 1
	* @list_view_order 1
	* @database_column_name login
	* @alias Логин
	* @var string
	*/
	public $Login;

	/**
    * @nodisplay true
	* @form_edit_order 2
	* @list_view_order 2
	* @database_column_name password
	* @alias Пароль
	* @var string
	*/
	public $Password;

	/**
	* @form_edit_order 0
	* @list_view_order 0
	* @database_column_name user_name
	* @alias Имя пользователя
	* @var string
	*/
	public $UserName;

}
