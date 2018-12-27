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
use function Astkon\Lib\Redirect;
use Astkon\Model\Article;
use Astkon\Model\Model;
use Astkon\Model\OperationType;
use Astkon\Traits\ListView;
use Astkon\View\View;

class ArticlesController extends Controller
{
    use ListView;
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

    public function ArticlesListAction($context) {
        $view = new View();
        $this->ListViewAction($view, Article::class);
        $view->generate();
    }

    public function ArticlesDictAction($context) {
        $view = new View();
        $condition = null;
        $substitution = null;
//        $pageId = isset($context['id']) ? intval($context['id']) : 0;
//        $pageSize = 5;
        if (isset($_GET['operation'])) {
            $dataTableName = OperationType::DataTable;
            $operationType = (new DataBase())->$dataTableName->getFirstRow('operation_name = :operation_name', null, array('operation_name' => $_GET['operation']));
            if (!$operationType) {
                $view->trace = 'Запрошена недопустимая операция';
                $view->error(ErrorCode::PROGRAMMER_ERROR);
                die();
            }
            switch ($operationType['operation_name']) {
                case 'Income':
                    break;
                default:
                    $condition = 'balance > 0 && is_archive <> 1';
                    break;
            }

        }
        $this->DictViewAction($view, Article::class, $condition, null, $substitution);
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $article = array();
        if (array_key_exists('submit', $_POST)) {
            $inputValues = array_filter($_POST, function($v, $k){ return $k !== 'submit'; }, ARRAY_FILTER_USE_BOTH);
            $res = Article::SaveInstance($inputValues);
            if (isset($res['@error'])) {
                //Заполняем все значения обратно
                $article = $inputValues;
                //Выделяем поля, в которых возникла ошибка, либо выводим общее сообщение об ошибке, если не удалось определить конктретное поле
                $options['validation'] = array(
                    'state' => Model::ValidStateError,
                    'message' => 'Ошибка при сохранении данных'
                );
                if (isset($res['expected_error_column_name'])) {
                    $message = isset($res['err_code_explain']) ? $res['err_code_explain'] : 'Недопустимое значение';
                    $options['validation']['fields'] =  array();
                    $errorColumns = explode(',', $res['expected_error_column_name']);
                    foreach ($errorColumns as $errorColumn) {
                        $options['validation']['fields'][$errorColumn] = array(
                            'state' => Model::ValidStateError,
                            'message' => $message
                        );
                    }
                    foreach (array_keys($article) as $fieldName) {
                        if (!array_key_exists($fieldName, $options['validation']['fields'])) {
                            $options['validation']['fields'][$fieldName] = array(
                                'state' => Model::ValidStateOK
                            );
                        }
                    }
                }
            }
            else  {
                if ($_POST[Article::PrimaryColumnName] == 0) {
                    /*Нужно сменить URL на вновь созданный элемент*/
                    list($controller, $action) = self::ThisAction();
                    Redirect(
                        $controller, $action, $res[DataBase::camelCaseToUnderscore(Article::PrimaryColumnName)]
                    );
                }
                else {
                    $options['validation'] = array(
                        'state' => Model::ValidStateOK,
                        'message' => 'Данные успешно сохранены'
                    );
                    $article = array_keys_CameCase(
                        (new DataBase())->
                        article->
                        getFirstRow('id_article = :id_article', null, array('id_article' => $context['id']))
                    );
                }
            }

        }
        else {
            $article = array_keys_CameCase(
                (new DataBase())->
                article->
                getFirstRow('id_article = :id_article', null, array('id_article' => $context['id']))
            );
        }
        $controllerName = self::ThisAction()[0];
        $options['backToList'] = '/' . $controllerName . '/' . $controllerName . 'List';
        $view = new View();
        $view->Article = $article;
        $view->options = $options;
        $view->generate();
    }
}