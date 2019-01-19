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

abstract class PeoplePartial extends Model {
	const DataTable = 'people';
	const PrimaryColumnName = 'IdPeople';
	const PrimaryColumnKey = 'id_people';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdPeople' => 
  array (
    'table_name' => 'people',
    'column_name' => 'id_people',
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
  'PeopleName' => 
  array (
    'table_name' => 'people',
    'column_name' => 'people_name',
    'data_type' => 'varchar',
    'max_length' => '200',
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
  'PostName' => 
  array (
    'table_name' => 'people',
    'column_name' => 'post_name',
    'data_type' => 'varchar',
    'max_length' => '200',
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
	* @database_column_name id_people
	* @caption Идентификатор
	* @var int
	*/
	public $IdPeople;

	/**
	* @database_column_name people_name
	* @caption ФИО
	* @var string
	*/
	public $PeopleName;

	/**
	* @database_column_name post_name
	* @caption Должность
	* @var string
	*/
	public $PostName;

}
