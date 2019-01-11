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

abstract class ChangeBalanceMethodPartial extends Model {
	const DataTable = 'change_balance_method';
	const PrimaryColumnName = 'IdChangeBalanceMethod';
	const PrimaryColumnKey = 'id_change_balance_method';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdChangeBalanceMethod' => 
  array (
    'table_name' => 'change_balance_method',
    'column_name' => 'id_change_balance_method',
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
      'operation_type' => 
      array (
        'model' => 'operation_type',
        'field' => 'id_change_balance_method',
      ),
    ),
  ),
  'MethodName' => 
  array (
    'table_name' => 'change_balance_method',
    'column_name' => 'method_name',
    'data_type' => 'varchar',
    'max_length' => '30',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
);
	/**
	* @database_column_name id_change_balance_method
	* @caption
	* @var int
	*/
	public $IdChangeBalanceMethod;

	/**
	* @database_column_name method_name
	* @caption
	* @var string
	*/
	public $MethodName;

}
