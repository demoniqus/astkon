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

abstract class ArticlePartial extends Model {
	const DataTable = 'article';
	const PrimaryColumnName = 'IdArticle';
	const PrimaryColumnKey = 'id_article';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdArticle' => 
  array (
    'table_name' => 'article',
    'column_name' => 'id_article',
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
      'article_balance' => 
      array (
        'model' => 'article_balance',
        'field' => 'id_article',
      ),
      'operation_item' => 
      array (
        'model' => 'operation_item',
        'field' => 'id_article',
      ),
    ),
  ),
  'IdArticleCategory' => 
  array (
    'table_name' => 'article',
    'column_name' => 'id_article_category',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'article_category',
    'ref_column_name' => 'id_article_category',
    'foreign_key' => 
    array (
      'model' => 'article_category',
      'field' => 'id_article_category',
      'display_mode' => 'decode_id_to_string',
      'old' => 
      array (
        'model' => 'article_category',
        'field' => 'id_article_category',
        'display_mode' => 'join_model',
        'joined_columns' => 
        array (
          0 => 
          array (
            'key' => 'category_name',
            'list_view_order' => 0,
          ),
          1 => 
          array (
            'key' => 'is_writeoff',
            'list_view_order' => 1,
          ),
          2 => 
          array (
            'key' => 'is_saleable',
            'list_view_order' => 2,
          ),
        ),
      ),
    ),
  ),
  'IdMeasure' => 
  array (
    'table_name' => 'article',
    'column_name' => 'id_measure',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'num_scale' => '0',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => 'measure',
    'ref_column_name' => 'id_measure',
    'foreign_key' => 
    array (
      'model' => 'measure',
      'field' => 'id_measure',
      'display_mode' => 'decode_id_to_string',
    ),
  ),
  'ArticleName' => 
  array (
    'table_name' => 'article',
    'column_name' => 'article_name',
    'data_type' => 'varchar',
    'max_length' => '255',
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'VendorCode' => 
  array (
    'table_name' => 'article',
    'column_name' => 'vendor_code',
    'data_type' => 'varchar',
    'max_length' => '50',
    'num_prec' => NULL,
    'num_scale' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'MUL',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
    'ref_table_name' => NULL,
    'ref_column_name' => NULL,
  ),
  'IsArchive' => 
  array (
    'table_name' => 'article',
    'column_name' => 'is_archive',
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
);
	/**
	* @foreign_key_display_value
	* @database_column_name article_name
	* @caption Наименование
	* @var string
	*/
	public $ArticleName;

	/**
	* @database_column_name id_article
	* @caption Идентификатор
	* @var int
	*/
	public $IdArticle;

	/**
	* @foreign_key_action ArticleCategories/ArticleCategoriesDict
	* @database_column_name id_article_category
	* @caption Категория
	* @var int
	*/
	public $IdArticleCategory;

	/**
	* @foreign_key_action Measures/MeasuresDict
	* @database_column_name id_measure
	* @caption Единица измерения
	* @var int
	*/
	public $IdMeasure;

	/**
	* @nodisplay
	* @noeditable
	* @database_column_name is_archive
	* @caption Архивный
	* @var bool
	*/
	public $IsArchive;

	/**
	* @database_column_name vendor_code
	* @caption Код поставщика
	* @var string
	*/
	public $VendorCode;

}
