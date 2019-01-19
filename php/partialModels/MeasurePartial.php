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

abstract class MeasurePartial extends Model {
	const DataTable = 'measure';
	const PrimaryColumnName = 'IdMeasure';
	const PrimaryColumnKey = 'id_measure';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdMeasure' => 
  array (
    'table_name' => 'measure',
    'column_name' => 'id_measure',
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
      'article' => 
      array (
        'model' => 'article',
        'field' => 'id_measure',
      ),
    ),
  ),
  'MeasureName' => 
  array (
    'table_name' => 'measure',
    'column_name' => 'measure_name',
    'data_type' => 'varchar',
    'max_length' => '50',
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
  'IsSplit' => 
  array (
    'table_name' => 'measure',
    'column_name' => 'is_split',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => '1',
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'Precision' => 
  array (
    'table_name' => 'measure',
    'column_name' => 'precision',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
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
	* @list_view_order 1
	* @database_column_name id_measure
	* @caption Идентификатор
	* @var int
	*/
	public $IdMeasure;

	/**
	* @list_view_order 3
	* @database_column_name is_split
	* @caption Признак делимости
	* @var bool
	*/
	public $IsSplit;

	/**
	* @list_view_order 2
	* @foreign_key_display_value
	* @database_column_name measure_name
	* @caption Единица измерения
	* @var string
	*/
	public $MeasureName;

	/**
	* @list_view_order 4
	* @database_column_name precision
	* @caption Точность деления (дес. зн)
	* @var int
	*/
	public $Precision;

}
