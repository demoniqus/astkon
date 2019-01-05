<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class BuildObjectPartial extends Model {
	const DataTable = 'build_object';
	const PrimaryColumnName = 'IdBuildObject';
	const PrimaryColumnKey = 'id_build_object';

/** @var array */
protected static $fieldsInfo = array (
  'IdBuildObject' => 
  array (
    'table_name' => 'build_object',
    'column_name' => 'id_build_object',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'BuildObjectName' => 
  array (
    'table_name' => 'build_object',
    'column_name' => 'build_object_name',
    'data_type' => 'varchar',
    'max_length' => '500',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'Comment' => 
  array (
    'table_name' => 'build_object',
    'column_name' => 'comment',
    'data_type' => 'text',
    'max_length' => '65535',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
  ),
);
	/**
	* @database_column_name build_object_name
	* @caption Наименование объекта
	* @var string
	*/
	public $BuildObjectName;

	/**
	* @database_column_name comment
	* @caption Комментарий для объекта
	* @var string
	*/
	public $Comment;

	/**
	* @database_column_name id_build_object
	* @caption Идентификатор
	* @var int
	*/
	public $IdBuildObject;

}
