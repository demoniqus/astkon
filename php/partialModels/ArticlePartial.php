<?php 

/** Generated automaticaly. Don't change this file manually! */
/*test update*/

namespace Astkon\Model\Partial;

use Astkon\Model\Model;

abstract class ArticlePartial extends Model {
	const DataTable = 'article';
/** @var array */
protected $fieldsInfo = array (
  'id_article' => 
  array (
    'table_name' => 'article',
    'column_name' => 'id_article',
    'data_type' => 'bigint',
    'max_length' => NULL,
    'num_prec' => 20,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'PRI',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'article_name' => 
  array (
    'table_name' => 'article',
    'column_name' => 'article_name',
    'data_type' => 'varchar',
    'max_length' => 300,
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'id_measure' => 
  array (
    'table_name' => 'article',
    'column_name' => 'id_measure',
    'data_type' => 'int',
    'max_length' => NULL,
    'num_prec' => 10,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => 'MUL',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
    'foreign_key' => 
    array (
      'model' => 'measure',
      'field' => 'id_measure',
    ),
  ),
  'balance' => 
  array (
    'table_name' => 'article',
    'column_name' => 'balance',
    'data_type' => 'double',
    'max_length' => NULL,
    'num_prec' => 22,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
  'vendor_code' => 
  array (
    'table_name' => 'article',
    'column_name' => 'vendor_code',
    'data_type' => 'varchar',
    'max_length' => 50,
    'num_prec' => NULL,
    'dtime_prec' => NULL,
    'char_set' => 'utf8',
    'column_key' => 'MUL',
    'is_nullable' => 'YES',
    'privileges' => 'select,insert,update,references',
  ),
  'is_archive' => 
  array (
    'table_name' => 'article',
    'column_name' => 'is_archive',
    'data_type' => 'bit',
    'max_length' => NULL,
    'num_prec' => 1,
    'dtime_prec' => NULL,
    'char_set' => NULL,
    'column_key' => '',
    'is_nullable' => 'NO',
    'privileges' => 'select,insert,update,references',
  ),
);
	/** @var string */
	public $ArticleName;

	/** @var float */
	public $Balance;

	/** @var int */
	public $IdArticle;

	/** @var int */
	public $IdMeasure;

	/** @var bool */
	public $IsArchive;

	/** @var string */
	public $VendorCode;

}
