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

abstract class ArticleBalancePartial extends Model {
	const DataTable = 'article_balance';
	const PrimaryColumnName = 'IdArticleBalance';
	const PrimaryColumnKey = 'id_article_balance';

	/** 
	* Параметр описывает свойства колонок таблиц БД. 
	* Все наименования колонок следует задавать в under_score стиле. 
	* В camelCase стиле задаются только ключи верхнего уровня. 
	* @var array
	*/
protected static $fieldsInfo = array (
  'IdArticleBalance' => 
  array (
    'table_name' => 'article_balance',
    'column_name' => 'id_article_balance',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'IdArticle' => 
  array (
    'table_name' => 'article_balance',
    'column_name' => 'id_article',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => '20',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'foreign_key' => 
    array (
      'model' => 'article',
      'field' => 'id_article',
      'display_mode' => 'join_model',
      'joined_columns' => 
      array (
        0 => 
        array (
          'key' => 'article_name',
          'list_view_order' => 1,
        ),
        1 => 
        array (
          'key' => 'id_measure',
          'list_view_order' => 2,
        ),
        2 => 
        array (
          'key' => 'id_article_category',
          'list_view_order' => 0,
        ),
      ),
    ),
  ),
  'IdUserGroup' => 
  array (
    'table_name' => 'article_balance',
    'column_name' => 'id_user_group',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'foreign_key' => 
    array (
      'model' => 'user_group',
      'field' => 'id_user_group',
    ),
  ),
  'Balance' => 
  array (
    'table_name' => 'article_balance',
    'column_name' => 'balance',
    'data_type' => 'decimal',
    'max_length' => NULL,
    'num_prec' => '30',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
);
	/**
	* @list_view_order 3
	* @database_column_name balance
	* @caption Остаток
	* @var float
	*/
	public $Balance;

	/**
	* @nodisplay
	* @database_column_name id_article
	* @caption
	* @var int
	*/
	public $IdArticle;

	/**
	* @nodisplay
	* @list_view_order 2
	* @database_column_name id_article_balance
	* @caption
	* @var int
	*/
	public $IdArticleBalance;

	/**
	* @nodisplay
	* @database_column_name id_user_group
	* @caption
	* @var int
	*/
	public $IdUserGroup;

}
