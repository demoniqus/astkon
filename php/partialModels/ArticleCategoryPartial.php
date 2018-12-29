<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class ArticleCategoryPartial extends Model {
	const DataTable = 'article_category';
	const PrimaryColumnName = 'IdArticleCategory';

/** @var array */
protected static $fieldsInfo = array (
  'IdArticleCategory' => 
  array (
    'table_name' => 'article_category',
    'column_name' => 'id_article_category',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
    'external_link' => 
    array (
      'model' => 'article',
      'field' => 'id_article_category',
    ),
  ),
  'CategoryName' => 
  array (
    'table_name' => 'article_category',
    'column_name' => 'category_name',
    'data_type' => 'varchar',
    'max_length' => '100',
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'UNI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'IsWriteoff' => 
  array (
    'table_name' => 'article_category',
    'column_name' => 'is_writeoff',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => '1',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
  'IsSaleable' => 
  array (
    'table_name' => 'article_category',
    'column_name' => 'is_saleable',
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
	* @database_column_name category_name
	* @alias Наименование категории
	* @var string
	*/
	public $CategoryName;

	/**
	* @database_column_name id_article_category
	* @alias Идентификатор
	* @var int
	*/
	public $IdArticleCategory;

	/**
	* @database_column_name is_saleable
	* @alias Расходуется
	* @var bool
	*/
	public $IsSaleable;

	/**
	* @database_column_name is_writeoff
	* @alias Списывается
	* @var bool
	*/
	public $IsWriteoff;

}
