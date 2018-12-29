<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class PeoplePartial extends Model {
	const DataTable = 'people';
	const PrimaryColumnName = 'IdPeople';

/** @var array */
protected static $fieldsInfo = array (
  'IdPeople' => 
  array (
    'table_name' => 'people',
    'column_name' => 'id_people',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'PeopleName' => 
  array (
    'table_name' => 'people',
    'column_name' => 'people_name',
    'data_type' => 'varchar',
    'max_length' => '200',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'PostName' => 
  array (
    'table_name' => 'people',
    'column_name' => 'post_name',
    'data_type' => 'varchar',
    'max_length' => '200',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update',
  ),
);
	/**
	* @database_column_name id_people
	* @alias Идентификатор
	* @var int
	*/
	public $IdPeople;

	/**
	* @database_column_name people_name
	* @alias ФИО
	* @var string
	*/
	public $PeopleName;

	/**
	* @database_column_name post_name
	* @alias Должность
	* @var string
	*/
	public $PostName;

}
