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

abstract class OperationItemPartial extends Model {
	const DataTable = 'operation_item';
	const PrimaryColumnName = 'IdOperationItem';
	const PrimaryColumnKey = 'id_operation_item';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdOperationItem' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'id_operation_item',
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
  ),
  'IdArticle' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'id_article',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'article',
    'ref_column_name' => 'id_article',
    'foreign_key' => 
    array (
      'model' => 'article',
      'field' => 'id_article',
      'display_mode' => 'decode_id_to_string',
    ),
  ),
  'IdOperation' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'id_operation',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'operation',
    'ref_column_name' => 'id_operation',
    'foreign_key' => 
    array (
      'model' => 'operation',
      'field' => 'id_operation',
      'display_mode' => 'decode_id_to_string',
    ),
  ),
  'OperationCount' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'operation_count',
    'data_type' => 'decimal',
    'max_length' => NULL,
    'num_prec' => '30',
    'num_scale' => '15',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'ConsignmentBalance' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'consignment_balance',
    'data_type' => 'decimal',
    'max_length' => NULL,
    'num_prec' => '30',
    'num_scale' => '15',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'OperationItemInfo' => 
  array (
    'table_name' => 'operation_item',
    'column_name' => 'operation_item_info',
    'data_type' => 'json',
    'max_length' => NULL,
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
);
	/**
	* @database_column_name consignment_balance
	* @caption
	* @var float
	*/
	public $ConsignmentBalance;

	/**
	* @database_column_name id_article
	* @caption
	* @var int
	*/
	public $IdArticle;

	/**
	* @database_column_name id_operation
	* @caption
	* @var int
	*/
	public $IdOperation;

	/**
	* @database_column_name id_operation_item
	* @caption
	* @var int
	*/
	public $IdOperationItem;

	/**
	* @database_column_name operation_count
	* @caption
	* @var float
	*/
	public $OperationCount;

	/**
	* @database_column_name operation_item_info
	* @caption
	* @var array
	*/
	public $OperationItemInfo;

}
