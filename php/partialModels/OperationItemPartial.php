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
  'ConsignmentBalance' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'consignment_balance',
    'data_type' => 'double',
    'max_length' => NULL,
    'num_prec' => '22',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'OperationItemInfo' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'operation_item_info',
    'data_type' => 'json',
    'max_length' => NULL,
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update',
  ),
);
	/**
	* @database_column_name consignment_balance
	* @alias
	* @var float
	*/
	public $ConsignmentBalance;

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

	/**
	* @database_column_name operation_item_info
	* @alias
	* @var array
	*/
	public $OperationItemInfo;

}
