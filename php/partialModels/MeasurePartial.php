<?php 

/** Generated automaticaly. Don't change this file manually! */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class MeasurePartial extends Model {
	const DataTable = 'measure';
/** @var array */
protected static $fieldsInfo = array (
  'id_measure' => 
  array (
    'table_name' => 'measure',
    'column_name' => 'id_measure',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => 10,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'external_link' => 
    array (
      'model' => 'article',
      'field' => 'id_measure',
    ),
  ),
  'measure_name' => 
  array (
    'table_name' => 'measure',
    'column_name' => 'measure_name',
    'data_type' => 'varchar',
    'max_length' => 50,
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'is_split' => 
  array (
    'table_name' => 'measure',
    'column_name' => 'is_split',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => 1,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'precision' => 
  array (
    'table_name' => 'measure',
    'column_name' => 'precision',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => 10,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
  ),
);
	/**
	* @database_column_name id_measure
	* @alias Идентификатор
	* @var int
	*/

	public $IdMeasure;

	/**
	* @database_column_name is_split
	* @alias Признак делимости
	* @var bool
	*/

	public $IsSplit;

	/**
	* @database_column_name measure_name
	* @alias Обозначение
	* @var string
	*/

	public $MeasureName;

	/**
	* @database_column_name precision
	* @alias Точность деления (дес. зн)
	* @var int
	*/

	public $Precision;

}
