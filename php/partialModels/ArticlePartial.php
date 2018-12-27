<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class ArticlePartial extends Model {
	const DataTable = 'article';
	const PrimaryColumnName = 'IdArticle';
/** @var array */
protected static $fieldsInfo = array (
  'IdArticle' => 
  array (
    'table_name' => 'article',
    'column_name' => 'id_article',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'external_link' => 
    array (
      'model' => 'operation_item',
      'field' => 'id_article',
    ),
  ),
  'ArticleName' => 
  array (
    'table_name' => 'article',
    'column_name' => 'article_name',
    'data_type' => 'varchar',
    'max_length' => '300',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'IdMeasure' => 
  array (
    'table_name' => 'article',
    'column_name' => 'id_measure',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'foreign_key' => 
    array (
      'model' => 'measure',
      'field' => 'id_measure',
    ),
  ),
  'Balance' => 
  array (
    'table_name' => 'article',
    'column_name' => 'balance',
    'data_type' => 'double',
    'max_length' => NULL,
    'num_prec' => '22',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'VendorCode' => 
  array (
    'table_name' => 'article',
    'column_name' => 'vendor_code',
    'data_type' => 'varchar',
    'max_length' => '50',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'MUL',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update',
  ),
  'IsArchive' => 
  array (
    'table_name' => 'article',
    'column_name' => 'is_archive',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => '1',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
);
	/**
	* @foreign_key_display_value
	* @database_column_name article_name
	* @alias Наименование
	* @var string
	*/
	public $ArticleName;

	/**
	* @autocalc auto_calc_rule
	* @noeditable
	* @database_column_name balance
	* @alias Остаток
	* @var float
	*/
	public $Balance;

	/**
	* @database_column_name id_article
	* @alias Идентификатор
	* @var int
	*/
	public $IdArticle;

	/**
	* @foreign_key_action Measures/MeasuresDict
	* @database_column_name id_measure
	* @alias Единица измерения
	* @var int
	*/
	public $IdMeasure;

	/**
	* @database_column_name is_archive
	* @alias Архивный
	* @var bool
	*/
	public $IsArchive;

	/**
	* @database_column_name vendor_code
	* @alias Код поставщика
	* @var string
	*/
	public $VendorCode;

}
