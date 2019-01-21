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

abstract class OperationPartial extends Model {
	const DataTable = 'operation';
	const PrimaryColumnName = 'IdOperation';
	const PrimaryColumnKey = 'id_operation';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdOperation' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'id_operation',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
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
      'operation_item' => 
      array (
        'model' => 'operation_item',
        'field' => 'id_operation',
      ),
    ),
  ),
  'IdOperationState' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'id_operation_state',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'operation_state',
    'ref_column_name' => 'id_operation_state',
    'foreign_key' => 
    array (
      'model' => 'operation_state',
      'field' => 'id_operation_state',
      'display_mode' => 'decode_id_to_string',
    ),
  ),
  'IdOperationType' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'id_operation_type',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'operation_type',
    'ref_column_name' => 'id_operation_type',
    'foreign_key' => 
    array (
      'model' => 'operation_type',
      'field' => 'id_operation_type',
      'display_mode' => 'decode_id_to_string',
    ),
  ),
  'IdUserGroup' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'id_user_group',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'user_group',
    'ref_column_name' => 'id_user_group',
    'foreign_key' => 
    array (
      'model' => 'user_group',
      'field' => 'id_user_group',
      'display_mode' => 'decode_id_to_string',
    ),
  ),
  'CreateDatetime' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'create_datetime',
    'data_type' => 'datetime',
    'max_length' => NULL,
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => '0',
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'OperationInfo' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'operation_info',
    'data_type' => 'mediumtext',
    'max_length' => '16777215',
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
  'FixDatetime' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'fix_datetime',
    'data_type' => 'datetime',
    'max_length' => NULL,
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => '0',
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'LinkedData' => 
  array (
    'table_name' => 'operation',
    'column_name' => 'linked_data',
    'data_type' => 'mediumtext',
    'max_length' => '16777215',
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
);
	/**
	* @database_column_name create_datetime
	* @caption Дата создания операции
	* @var DateTime
	*/
	public $CreateDatetime;

	/**
	* @database_column_name fix_datetime
	* @caption Дата фиксации операции
	* @var DateTime
	*/
	public $FixDatetime;

	/**
	* @database_column_name id_operation
	* @caption Идентификатор
	* @var int
	*/
	public $IdOperation;

	/**
	* @database_column_name id_operation_state
	* @caption Статус документа
	* @var int
	*/
	public $IdOperationState;

	/**
	* @noeditable
	* @nodisplay
	* @database_column_name id_operation_type
	* @caption Тип документа
	* @var int
	*/
	public $IdOperationType;

	/**
	* @noeditable
	* @nodisplay
	* @database_column_name id_user_group
	* @caption
	* @var int
	*/
	public $IdUserGroup;

	/**
	* @noeditable
	* @nodisplay
	* @database_column_name linked_data
	* @caption Информация о связанных объектах
	* @var string
	*/
	public $LinkedData;

	/**
	* @noeditable
	* @nodisplay
	* @database_column_name operation_info
	* @caption Расширенная информация
	* @var string
	*/
	public $OperationInfo;

}
