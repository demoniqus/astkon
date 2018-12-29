<?php 

/** 
 * Файл генерируется автоматически.
 * Не допускаются произвольные изменения вручную.
 * Допускается вручную только расширять doc-блок публичный полей класса. 
 * При этом разделы @var и @database_column_name будут автоматически перезаписываться. */


namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class ArticleBalancePartial extends Model {
	const DataTable = 'article_balance';
	const PrimaryColumnName = 'IdArticleBalance';

/** @var array */
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
    'privileges' => 'select,insert,update',
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
    'privileges' => 'select,insert,update',
    'foreign_key' => 
    array (
      'model' => 'article',
      'field' => 'id_article',
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
    'privileges' => 'select,insert,update',
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
    'num_prec' => '10',
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update',
  ),
);
	/**
	* @database_column_name balance
	* @alias
	* @var float
	*/
	public $Balance;

	/**
	* @database_column_name id_article
	* @alias
	* @var int
	*/
	public $IdArticle;

	/**
	* @database_column_name id_article_balance
	* @alias
	* @var int
	*/
	public $IdArticleBalance;

	/**
	* @database_column_name id_user_group
	* @alias
	* @var int
	*/
	public $IdUserGroup;

}
