<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class OperationItemPartial extends Model {
	const DataTable = 'operation_item';
	const PrimaryColumnName = 'IdOperationItem';
/** @var array */
protected static $fieldsInfo = array (
  'IdOperationItem' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'id_operation_item',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'IdArticle' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'id_article',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'foreign_key' => 
    array (
      'model' => 'article',
      'field' => 'id_article',
    ),
  ),
  'BeforeOperationCount' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'before_operation_count',
    'data_type' => 'double',
    'max_length' => NULL,
    'num_prec' => '22',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'OperationCount' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'operation_count',
    'data_type' => 'double',
    'max_length' => NULL,
    'num_prec' => '22',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'AfterOperationCount' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'after_operation_count',
    'data_type' => 'double',
    'max_length' => NULL,
    'num_prec' => '22',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'IdOperation' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'id_operation',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'foreign_key' => 
    array (
      'model' => 'operation',
      'field' => 'id_operation',
    ),
  ),
);
	/**
	* @database_column_name after_operation_count
	* @alias
	* @var float
	*/
	public $AfterOperationCount;

	/**
	* @database_column_name before_operation_count
	* @alias
	* @var float
	*/
	public $BeforeOperationCount;

	/**
	* @database_column_name id_article
	* @alias
	* @var int
	*/
	public $IdArticle;

	/**
	* @database_column_name id_operation
	* @alias
	* @var int
	*/
	public $IdOperation;

	/**
	* @database_column_name id_operation_item
	* @alias
	* @var int
	*/
	public $IdOperationItem;

	/**
	* @database_column_name operation_count
	* @alias
	* @var float
	*/
	public $OperationCount;

}
