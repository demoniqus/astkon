<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться.
 * Допускается вручную расширять foreign_key в $fieldsInfo. 
 * При этом ключи model и field изменять не допускается - при обновлении модели в случае их изменения может быть утрачена прочая информация */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class OperationTypePartial extends Model {
	const DataTable = 'operation_type';
	const PrimaryColumnName = 'IdOperationType';
	const PrimaryColumnKey = 'id_operation_type';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdOperationType' => 
  array (
    'table_name' => 'operation_type',
    'column_name' => 'id_operation_type',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
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
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'IdChangeBalanceMethod' => 
  array (
    'table_name' => 'operation_type',
    'column_name' => 'id_change_balance_method',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'change_balance_method',
    'ref_column_name' => 'id_change_balance_method',
    'foreign_key' => 
    array (
      'model' => 'change_balance_method',
      'field' => 'id_change_balance_method',
    ),
  ),
  'OperationLabel' => 
  array (
    'table_name' => 'operation_type',
    'column_name' => 'operation_label',
    'data_type' => 'varchar',
    'max_length' => '30',
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
);
	/**
	* @database_column_name id_change_balance_method
	* @caption
	* @var int
	*/
	public $IdChangeBalanceMethod;

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
