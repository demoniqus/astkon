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

abstract class ArticleCategoryPartial extends Model {
	const DataTable = 'article_category';
	const PrimaryColumnName = 'IdArticleCategory';
	const PrimaryColumnKey = 'id_article_category';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
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
    'privileges' => 'select,insert,update,references',
    'external_link' => 
    array (
      'article' => 
      array (
        'model' => 'article',
        'field' => 'id_article_category',
      ),
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
    'privileges' => 'select,insert,update,references',
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
    'privileges' => 'select,insert,update,references',
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
    'privileges' => 'select,insert,update,references',
  ),
);
	/**
	* @foreign_key_display_value
	* @database_column_name category_name
	* @caption Наименование категории
	* @var string
	*/
	public $CategoryName;

	/**
	* @database_column_name id_article_category
	* @caption Идентификатор
	* @var int
	*/
	public $IdArticleCategory;

	/**
	* @database_column_name is_saleable
	* @caption Расходуется
	* @var bool
	*/
	public $IsSaleable;

	/**
	* @database_column_name is_writeoff
	* @caption Списывается
	* @var bool
	*/
	public $IsWriteoff;

}
