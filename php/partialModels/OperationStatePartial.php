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

abstract class OperationStatePartial extends Model {
	const DataTable = 'operation_state';
	const PrimaryColumnName = 'IdOperationState';
	const PrimaryColumnKey = 'id_operation_state';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
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
	* @caption
	* @var int
	*/
	public $IdOperationState;

	/**
	* @database_column_name state_comment
	* @caption
	* @var string
	*/
	public $StateComment;

	/**
	* @foreign_key_display_value
	* @database_column_name state_label
	* @caption
	* @var string
	*/
	public $StateLabel;

	/**
	* @database_column_name state_name
	* @caption
	* @var string
	*/
	public $StateName;

}
