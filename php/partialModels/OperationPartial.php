<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class OperationPartial extends Model {
	const DataTable = 'operation';
	const PrimaryColumnName = 'IdOperation';
/** @var array */
protected static $fieldsInfo = array (
  'IdOperation' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'id_operation',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'external_link' => 
    array (
      'model' => 'operation_item',
      'field' => 'id_operation',
    ),
  ),
  'OperationDatetime' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'operation_datetime',
    'data_type' => 'datetime',
    'max_length' => NULL,
    'num_prec' => NULL,
    'dtime_prec' => '0',
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'IdOperationType' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'id_operation_type',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'foreign_key' => 
    array (
      'model' => 'operation_type',
      'field' => 'id_operation_type',
    ),
  ),
  'OperationInfo' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'operation_info',
    'data_type' => 'json',
    'max_length' => NULL,
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'IdOperationState' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'id_operation_state',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'foreign_key' => 
    array (
      'model' => 'operation_state',
      'field' => 'id_operation_state',
    ),
  ),
);
	/**
	* @database_column_name id_operation
	* @alias Идентификатор
	* @var int
	*/
	public $IdOperation;

	/**
	* @database_column_name id_operation_state
	* @alias Статус документа
	* @var int
	*/
	public $IdOperationState;

	/**
	* @noeditable
	* @database_column_name id_operation_type
	* @alias Тип документа
	* @var int
	*/
	public $IdOperationType;

	/**
	* @database_column_name operation_datetime
	* @alias Дата
	* @var DateTime
	*/
	public $OperationDatetime;

	/**
	* @noeditable
	* @database_column_name operation_info
	* @alias Расширенная информация
	* @var array
	*/
	public $OperationInfo;

}
