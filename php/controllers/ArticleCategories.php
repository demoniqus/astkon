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
use function Astkon\Lib\array_keys_CamelCase;
use Astkon\Model\ArticleCategory;
use Astkon\QueryConfig;
use Astkon\Traits\EditAction;
use Astkon\Traits\ListView;
use Astkon\View\View;

class ArticleCategoriesController extends Controller
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

    public function ArticleCategoriesListAction($context) {
        $view = new View();
        $options = array();
        if (CURRENT_USER['IsAdmin']) {
            static::editOption($options, __CLASS__);
        }

        $queryConfig = new QueryConfig();
        $queryConfig->OrderBy = $this->getDefaultOrder();

        $this->ListViewAction(
            $view,
            ArticleCategory::class,
            $options,
            $queryConfig
        );
        $view->generate();
    }

    public function ArticleCategoriesDictAction($context) {
        $view = new View();

        $queryConfig = new QueryConfig();
        $queryConfig->OrderBy = $this->getDefaultOrder();

        $this->DictViewAction(
            $view,
            ArticleCategory::class,
            $queryConfig,
            array(
                'CategoryName',
                'IsSaleable',
                'IsWriteoff',
            )
        );
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $entity = array();
        $model = ArticleCategory::class;
        if (array_key_exists('submit', $_POST)) {
            $this->processPostData($entity, $options, $model, $context);

        }
        else {
            $dataTable = $model::DataTable;
            $entity = array_keys_CamelCase(
                (new DataBase())->
                    $dataTable->
                    getFirstRow(
                        new QueryConfig(
                            $model::PrimaryColumnKey . ' = :' . $model::PrimaryColumnKey,
                            null,
                            array(
                                $model::PrimaryColumnKey => $context['id'],
                            )
                        )
                )
            );
        }
        $controllerName = self::Name();
        $options['backToList'] = '/' . $controllerName . '/' . $controllerName . 'List';
        $view = new View();
        $view->Entity = $entity;
        $view->options = $options;
        $view->Model = $model;
        $view->generate();
    }

    private function getDefaultOrder() {
        return array('CategoryName');
    }
}