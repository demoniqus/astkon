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
/** @var array */
protected static $fieldsInfo = array (
  'IdUser' => 
  array (
    'table_name' => 'user',
    'column_name' => 'id_user',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => 10,
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
    'max_length' => 255,
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
    'max_length' => 45,
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
);
	/**
	* @noeditable true
	* @database_column_name config
	* @alias Системная конфигурация
	* @var array
	*/
	public $Config;

	/**
	* @database_column_name id_user
	* @alias Идентификатор
	* @var int
	*/
	public $IdUser;

	/**
	* @database_column_name login
	* @alias Логин
	* @var string
	*/
	public $Login;

	/**
	* @database_column_name password
	* @alias Пароль
	* @var string
	*/
	public $Password;

}
