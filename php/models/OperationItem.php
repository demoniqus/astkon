<?php
namespace Astkon\Model;

require_once getcwd() . '//php/partialModels/OperationItemPartial.php';

use  Astkon\DataBase;

use Astkon\linq;
use  Astkon\Model\Partial\OperationItemPartial;

/**
* В этом классе реализуются все особенности поведения и строения соответствующего типа
*/

class OperationItem extends OperationItemPartial {

	public function __construct (array $fields = array()) {
		parent::__construct($fields, DataBase::camelCaseToUnderscore(__CLASS__));
	}

	public static function AddToBalance($listOperationItems, DataBase $db) {
        $dictOperationItems = (new linq($listOperationItems))
            ->toAssoc(
                function($operationItem){ return $operationItem[Article::PrimaryColumnKey];},
                function($operationItem){ return $operationItem['operation_count'];}
            )
            ->getData();

        $listArticleBalance = ArticleBalance::getRows(
            $db,
            '`' . UserGroup::PrimaryColumnKey . '` = ' . CURRENT_USER[UserGroup::PrimaryColumnName] . ' AND ' .
            '`' . Article::PrimaryColumnKey . '` in (' . implode(',', array_keys($dictOperationItems)) . ')'
        );

        $dictArticleBalance = (new linq($listArticleBalance))
            ->toAssoc(function($articleBalance){ return $articleBalance[Article::PrimaryColumnKey];})
            ->getData();

        foreach ($dictOperationItems as $idArticle => $count) {
            if (array_key_exists($idArticle, $dictArticleBalance)) {
                $articleBalance = $dictArticleBalance[$idArticle];
                ArticleBalance::Update(
                    array(
                        ArticleBalance::PrimaryColumnKey => $articleBalance[ArticleBalance::PrimaryColumnKey],
                        'balance'                        => $articleBalance['balance'] + $dictOperationItems[$articleBalance[Article::PrimaryColumnKey]]
                    ),
                    $db
                );
            }
            else  {
                ArticleBalance::Create(
                    array(
                        Article::PrimaryColumnKey   => $idArticle,
                        UserGroup::PrimaryColumnKey => CURRENT_USER[UserGroup::PrimaryColumnName],
                        'balance'                   => $dictOperationItems[$idArticle],
                    ),
                    $db
                );
            }
        }
    }

	public static function InventoryBalance($listOperationItems, DataBase $db) {
        $dictOperationItems = (new linq($listOperationItems))
            ->toAssoc(
                function($operationItem){ return $operationItem[Article::PrimaryColumnKey];},
                function($operationItem){ return $operationItem['operation_count'];}
            )
            ->getData();

        $listArticleBalance = ArticleBalance::getRows(
            $db,
            '`' . UserGroup::PrimaryColumnKey . '` = ' . CURRENT_USER[UserGroup::PrimaryColumnName] . ' AND ' .
            '`' . Article::PrimaryColumnKey . '` in (' . implode(',', array_keys($dictOperationItems)) . ')'
        );

        $dictArticleBalance = (new linq($listArticleBalance))
            ->toAssoc(function($articleBalance){ return $articleBalance[Article::PrimaryColumnKey];})
            ->getData();

        foreach ($dictOperationItems as $idArticle => $count) {
            if (array_key_exists($idArticle, $dictArticleBalance)) {
                $articleBalance = $dictArticleBalance[$idArticle];
                ArticleBalance::Update(
                    array(
                        ArticleBalance::PrimaryColumnKey => $articleBalance[ArticleBalance::PrimaryColumnKey],
                        'balance'                        => $dictOperationItems[$articleBalance[Article::PrimaryColumnKey]]
                    ),
                    $db
                );
            }
            else  {
                ArticleBalance::Create(
                    array(
                        UserGroup::PrimaryColumnKey => CURRENT_USER[UserGroup::PrimaryColumnName],
                        'balance'                   => $dictOperationItems[$idArticle],
                    ),
                    $db
                );

            }
        }
    }

    /**
     * Метод обеспечивает возврат зарезервированного количества артикулов на баланс.
     * При этом записи о резерве удаляются
     * @param int|array     $listId
     * @param DataBase|null $db
     */
	public static function ReturnOnBalance($listId, ?DataBase $db) {
	    if (is_numeric($listId)) {
	        $listId = array($listId);
        }
	    $db = $db ?? new DataBase();
	    $operationItems = static::getRows(
	        $db,
            '`' . static::PrimaryColumnKey . '` in (' . implode(',', $listId) . ')',
            null,
            null,
            null,
            count($listId)
        );
	    $listArticleBalance = ArticleBalance::getRows(
            $db,
            '`' . Article::PrimaryColumnKey . '` in (' . implode(
                ',',
                array_map(
                    function($operationItem){ return $operationItem[Article::PrimaryColumnKey];},
                    $operationItems
                )
            ) . ') AND `' . UserGroup::PrimaryColumnKey . '` = ' . CURRENT_USER[UserGroup::PrimaryColumnName],
            null,
            null,
            null,
            count($operationItems)
        );
	    $dictArticleBalance = (new linq($listArticleBalance))
            ->toAssoc(
                function($articleBalance){ return $articleBalance[Article::PrimaryColumnKey];}
            )
            ->getData();
	    foreach ($operationItems as $operationItem) {
	        $articleBalance = $dictArticleBalance[$operationItem[Article::PrimaryColumnKey]];
	        $newBalance = $articleBalance['balance'] + $operationItem['operation_count'];
	        ArticleBalance::Update(
	            array(
	                ArticleBalance::PrimaryColumnKey => $articleBalance[ArticleBalance::PrimaryColumnKey],
                    /*Ручная проверка показала, что PHP адекватно складывает числа, т.е. 0,7+0,1 =0,8, а не 0,7999999*/
                    'balance' => $newBalance
                ),
                $db
            );
        }
	    static::Delete($listId, $db);
    }

    /**
     * Метод резервирует с запаса по списку OperationItem
     *
     * @param array         $listOperationItems
     * @param DataBase|null $db
     */
    public static function ReserveFromBalance(array $listOperationItems, ?DataBase $db) {
	    $articlesDict = (new linq($listOperationItems))
            ->toAssoc(
                function($operationItem){ return $operationItem[Article::PrimaryColumnKey];},
                function($operationItem){ return $operationItem['operation_count'];}
            )
            ->getData();

        $listArticleBalance = ArticleBalance::getRows(
            $db,
            '`' . Article::PrimaryColumnKey . '` in (' . implode(
                ',',
                array_keys($articlesDict)
            ) . ') AND `' . UserGroup::PrimaryColumnKey . '` = ' . CURRENT_USER[UserGroup::PrimaryColumnName],
            null,
            null,
            null,
            count($articlesDict)
        );

        $dictArticleBalance = (new linq($listArticleBalance))
            ->toAssoc(
                function($articleBalance){ return $articleBalance[Article::PrimaryColumnKey];}
            )
            ->getData();

	    foreach ($articlesDict as $articleId => $count) {
            $articleBalance = $dictArticleBalance[$articleId];
            $newBalance = $articleBalance['balance'] - $count;
            ArticleBalance::Update(
                array(
                    ArticleBalance::PrimaryColumnKey => $articleBalance[ArticleBalance::PrimaryColumnKey],
                    /*Ручная проверка показала, что PHP адекватно складывает числа, т.е. 0,7+0,1 =0,8, а не 0,7999999*/
                    'balance' => $newBalance
                ),
                $db
            );
        }
    }
}