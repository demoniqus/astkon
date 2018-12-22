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
use function Astkon\Lib\array_keys_CameCase;
use function Astkon\Lib\Redirect;
use Astkon\Model\Measure;
use Astkon\Model\Model;
use Astkon\View\View;

class MeasuresController extends Controller
{
    /**
     * @param string $action - запрашиваемый метод
     * @param array $context - дополнительный контекст
     */
    public static function Run(string $action, array $context)
    {
        parent::Run($action, $context);
    }

    public function MeasuresListAction($context) {
        $view = new View();
//        $pageId = isset($context['id']) ? intval($context['id']) : 0;
//        $pageSize = 5;
        $view->listItemOptions = array(
            array(
            'action' => '/Measures/Edit',
            'click' => null,
            'icon' => '/icon-edit.png',
            'title' => 'Редактировать'
            )
        );
        $view->modelConfig = Measure::getConfigForListView();
        $view->listItems = array_map(
            function($row){
                return array_keys_CameCase($row);
            },
            (new DataBase())->measure->getRows()
        );
        $view->generate();
    }

    public function EditAction($context) {
        $options = array();
        $measure = array();
        if (array_key_exists('submit', $_POST)) {
            $inputValues = array_filter($_POST, function($v, $k){ return $k !== 'submit'; }, ARRAY_FILTER_USE_BOTH);
            $res = Measure::SaveInstance($inputValues);
            if (isset($res['@error'])) {
                //Заполняем все значения обратно
                $measure = $inputValues;
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
                    foreach (array_keys($measure) as $fieldName) {
                        if (!array_key_exists($fieldName, $options['validation']['fields'])) {
                            $options['validation']['fields'][$fieldName] = array(
                                'state' => Model::ValidStateOK
                            );
                        }
                    }
                }
            }
            else  {
                if ($_POST[Measure::PKName()] == 0) {
                    /*Нужно сменить URL на вновь созданный элемент*/
                    list($controller, $action) = self::ThisAction();
                    Redirect(
                        $controller, $action, $res[DataBase::camelCaseToUnderscore(Measure::PKName())]
                    );
                }
                else {
                    $options['validation'] = array(
                        'state' => Model::ValidStateOK,
                        'message' => 'Данные успешно сохранены'
                    );
                    $measure = array_keys_CameCase(
                        (new DataBase())->
                        measure->
                        getFirstRow('id_measure = :id_measure', null, array('id_measure' => $context['id']))
                    );
                }
            }

        }
        else {
            $measure = array_keys_CameCase(
                (new DataBase())->
                measure->
                getFirstRow('id_measure = :id_measure', null, array('id_measure' => $context['id']))
            );
        }
        $view = new View();
        $view->Measure = $measure;
        $view->options = $options;
        $view->generate();
    }
}