<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class OperationTypePartial extends Model {
	const DataTable = 'operation_type';
	const PrimaryColumnName = 'IdOperationType';

/** @var array */
protected static $fieldsInfo = array (
  'IdOperationType' => 
  array (
    'table_name' => 'operation_type',
    'column_name' => 'id_operation_type',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'external_link' => 
    array (
      'model' => 'operation',
      'field' => 'id_operation_type',
    ),
  ),
  'OperationName' => 
  array (
    'table_name' => 'operation_type',
    'column_name' => 'operation_name',
    'data_type' => 'varchar',
    'max_length' => '25',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'OperationLabel' => 
  array (
    'table_name' => 'operation_type',
    'column_name' => 'operation_label',
    'data_type' => 'varchar',
    'max_length' => '30',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
);
	/**
	* @database_column_name id_operation_type
	* @alias
	* @var int
	*/
	public $IdOperationType;

	/**
	* @foreign_key_display_value
	* @database_column_name operation_label
	* @alias
	* @var string
	*/
	public $OperationLabel;

	/**
	* @database_column_name operation_name
	* @alias
	* @var string
	*/
	public $OperationName;

}
