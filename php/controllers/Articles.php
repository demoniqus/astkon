<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 17.12.18
 * Time: 15:47
 */

namespace Astkon\Controllers;

use Astkon\Controller\Controller;
use Astkon\DataBase;
use Astkon\ErrorCode;
use function Astkon\Lib\array_keys_CameCase;
use Astkon\linq;
use Astkon\Model\Article;
use Astkon\Model\ArticleCategory;
use Astkon\Model\Measure;
use Astkon\Model\OperationType;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\View\View;

class ArticlesController extends Controller
{
    use ListView;
    use EditAction;
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    public function IndexAction() {
        (new View())->generate();
    }

    public function ImportAction(array $context) {
        $view = new View();
        $key = 'importedItems';
        if (
            array_key_exists($key, $_POST) &&
            is_array($_POST[$key]) &&
            count($_POST[$key]) &&
            CURRENT_USER['IsAdmin']
        ) {
            $db = new DataBase();
            $db->beginTransaction();
            /*
             * Считаем, что часть необходимых проверок и фильтраций произведена на стороне клиента и, поскольку пользователь
             * обладает правами админа, то он не станет вредить системе и передавать в метод некорректные данные в обход.
             *
             */
            $articles = $_POST[$key];

            foreach (array(Measure::class => 'measure_name', ArticleCategory::class => 'category_name') as $model => $nameFieldKey) {
                $modelName = $model::Name();

                $listNewItems = array();

                $values = array_map(
                    function($article) use ($modelName){ return $article[$modelName];},
                    $articles
                );

                $values = array_flip(array_flip($values));

                $listExistsValues = $model::getRows($db);

                $dictExistsValues = (new linq($listExistsValues))
                ->toAssoc(
                    function($modelRow) use ($nameFieldKey){
                        return $modelRow[$nameFieldKey];
                    }
                )
                ->getData();

                foreach ($values as $value) {
                    if (!array_key_exists($value, $dictExistsValues)) {
                        switch ($modelName) {
                            case ArticleCategory::Name():
                                $substitution = array(
                                    $nameFieldKey => $value,
                                    'is_writeoff' => false,
                                    'is_saleable' => true,
                                );
                                break;
                            case Measure::Name():
                                $substitution = array(
                                    $nameFieldKey => $value,
                                    'is_split' => false,
                                    'precision' => 0,
                                );
                                break;
                        }

                        $listNewItems[] = $dictExistsValues[$value] = $model::Create(
                            $substitution,
                            $db,
                            true
                        );
                    }
                }

                foreach ($articles as &$article) {
                    $article[$modelName] = $dictExistsValues[$article[$modelName]][$model::PrimaryColumnKey];
                }
                $view->$modelName = array_map(
                    function($newItem){ return array_keys_CameCase($newItem);},
                    $listNewItems
                );
            }

            $listExistsArticles = Article::getRows($db);
            $dictExistsArticles = (new linq($listExistsArticles))
                ->toAssoc(function($existsArticle){
                    return $existsArticle['article_name'] . '-' . $existsArticle[ArticleCategory::PrimaryColumnKey] . '-' . $existsArticle[Measure::PrimaryColumnKey];
                })
                ->getData();

            $listNewItems = array();
            foreach ($articles as $article) {
                $articleName = $article[Article::Name()];
                $articleCategory = $article[ArticleCategory::Name()];
                $articleMeasure = $article[Measure::Name()];
                $key = $articleName . '-' . $articleCategory . '-' . $articleMeasure;
                if (!array_key_exists($key, $dictExistsArticles)) {
                    $listNewItems[] = Article::Create(
                        array(
                            'article_name'                    => $articleName,
                            ArticleCategory::PrimaryColumnKey => $articleCategory,
                            Measure::PrimaryColumnKey         => $articleMeasure,
                            'vendor_code'                     => isset($article['VendorCode']) ? $article['VendorCode'] : null,
                        ),
                        $db,
                        true
                    );
                }
            }

            $dictMeasures = (new linq(Measure::getRows($db)))
                ->toAssoc(function($measure){
                    return $measure[Measure::PrimaryColumnKey];
                })
                ->getData();

            $dictCategories = (new linq(ArticleCategory::getRows($db)))
                ->toAssoc(function($articleCategory){
                    return $articleCategory[ArticleCategory::PrimaryColumnKey];
                })
                ->getData();

            $view->Article = array_map(
                function($article) use ($dictCategories, $dictMeasures) {
                    return array(
                        'Article'         => $article['article_name'],
                        'ArticleCategory' => $dictCategories[$article[ArticleCategory::PrimaryColumnKey]]['category_name'],
                        'Measure'         => $dictMeasures[$article[Measure::PrimaryColumnKey]]['measure_name'],
                        'VendorCode'      => $article['vendor_code'],
                    );
                },
                $listNewItems
            );
            $view->success = true;
            $db->rollback();
            $view->JSONView();
        }
        $view->generate();
    }

    public function ArticlesListAction($context) {
        $view = new View();
        $options = array();
        static::editOption($options, __CLASS__);
        $this->ListViewAction(
            $view,
            Article::class,
            $options
        );
        $view->generate();
    }

    public function ArticlesDictAction($context) {
        $view = new View();
        $condition = null;
        $substitution = null;
//        $pageId = isset($context['id']) ? intval($context['id']) : 0;
//        $pageSize = 5;

        $this->DictViewAction($view, Article::class, $condition, null, $substitution);
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $entity = array();
        $model = Article::class;
        if (array_key_exists('submit', $_POST)) {
            $this->processPostData($entity, $options, $model, $context);

        }
        else {
            $dataTable = $model::DataTable;
            $entity = array_keys_CameCase(
                (new DataBase())->
                $dataTable->
                getFirstRow(
                    $model::PrimaryColumnKey . ' = :' . $model::PrimaryColumnKey,
                    null, array(
                        $model::PrimaryColumnKey => $context['id']
                    )
                )
            );
        }
        $controllerName = self::ThisAction()[0];
        $options['backToList'] = '/' . $controllerName . '/' . $controllerName . 'List';
        $view = new View();
        $view->Entity = $entity;
        $view->options = $options;
        $view->Model = $model;
        $view->generate();
    }
}