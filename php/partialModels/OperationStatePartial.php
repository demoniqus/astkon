<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class OperationStatePartial extends Model {
	const DataTable = 'operation_state';
	const PrimaryColumnName = 'IdOperationState';
	const PrimaryColumnKey = 'id_operation_state';

/** @var array */
protected static $fieldsInfo = array (
  'IdOperationState' => 
  array (
    'table_name' => 'operation_state',
    'column_name' => 'id_operation_state',
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
        'field' => 'id_operation_state',
      ),
    ),
  ),
  'StateName' => 
  array (
    'table_name' => 'operation_state',
    'column_name' => 'state_name',
    'data_type' => 'varchar',
    'max_length' => '30',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'StateLabel' => 
  array (
    'table_name' => 'operation_state',
    'column_name' => 'state_label',
    'data_type' => 'varchar',
    'max_length' => '30',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
  ),
  'StateComment' => 
  array (
    'table_name' => 'operation_state',
    'column_name' => 'state_comment',
    'data_type' => 'varchar',
    'max_length' => '500',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
  ),
);
	/**
	* @database_column_name id_operation_state
	* @alias
	* @var int
	*/
	public $IdOperationState;

	/**
	* @database_column_name state_comment
	* @alias
	* @var string
	*/
	public $StateComment;

	/**
	* @database_column_name state_label
	* @alias
	* @var string
	*/
	public $StateLabel;

	/**
	* @database_column_name state_name
	* @alias
	* @var string
	*/
	public $StateName;

}
