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

abstract class BuildObjectPartial extends Model {
	const DataTable = 'build_object';
	const PrimaryColumnName = 'IdBuildObject';
	const PrimaryColumnKey = 'id_build_object';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdBuildObject' => 
  array (
    'table_name' => 'build_object',
    'column_name' => 'id_build_object',
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
  ),
  'BuildObjectName' => 
  array (
    'table_name' => 'build_object',
    'column_name' => 'build_object_name',
    'data_type' => 'varchar',
    'max_length' => '255',
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
  'Comment' => 
  array (
    'table_name' => 'build_object',
    'column_name' => 'comment',
    'data_type' => 'text',
    'max_length' => '65535',
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
	* @database_column_name build_object_name
	* @caption Наименование объекта
	* @var string
	*/
	public $BuildObjectName;

	/**
	* @database_column_name comment
	* @caption Комментарий для объекта
	* @var string
	*/
	public $Comment;

	/**
	* @database_column_name id_build_object
	* @caption Идентификатор
	* @var int
	*/
	public $IdBuildObject;

}
