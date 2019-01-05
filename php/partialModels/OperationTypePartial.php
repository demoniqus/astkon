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
	const PrimaryColumnKey = 'id_operation_type';

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
    'privileges' => 'select,insert,update,references',
    'external_link' => 
    array (
      'operation' => 
      array (
        'model' => 'operation',
        'field' => 'id_operation_type',
      ),
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
    'privileges' => 'select,insert,update,references',
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
    'privileges' => 'select,insert,update,references',
  ),
);
	/**
	* @database_column_name id_operation_type
	* @caption
	* @var int
	*/
	public $IdOperationType;

	/**
	* @foreign_key_display_value
	* @database_column_name operation_label
	* @caption
	* @var string
	*/
	public $OperationLabel;

	/**
	* @database_column_name operation_name
	* @caption
	* @var string
	*/
	public $OperationName;

}
