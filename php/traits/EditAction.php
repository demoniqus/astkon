<?php
/**
 * Created by PhpStorm.
 * User: demonius
 * Date: 29.12.18
 * Time: 17:50
 */

namespace Astkon\Traits;

use Astkon\DataBase;
use function Astkon\Lib\array_keys_CamelCase;
use function Astkon\Lib\Redirect;
use Astkon\Model\Model;
use Astkon\QueryConfig;

trait EditAction
{

    protected function processErrors(array &$entity, array &$options, array $errorsInfo) {
        //Выделяем поля, в которых возникла ошибка, либо выводим общее сообщение об ошибке, если не удалось определить конктретное поле
        $options['validation'] = array(
            'state' => Model::ValidStateError,
            'message' => 'Ошибка при сохранении данных'
        );
        foreach ($errorsInfo as $errInfo) {
            if (isset($errInfo['expected_error_column_name'])) {
                $message = isset($errInfo['err_code_explain']) ? $errInfo['err_code_explain'] : 'Недопустимое значение';
                $options['validation']['fields'] = array();
                $errorColumns = explode(',', $errInfo['expected_error_column_name']);
                foreach ($errorColumns as $errorColumn) {
                    $options['validation']['fields'][$errorColumn] = array(
                        'state' => Model::ValidStateError,
                        'message' => $message
                    );
                }
                foreach (array_keys($entity) as $fieldName) {
                    if (!array_key_exists($fieldName, $options['validation']['fields'])) {
                        $options['validation']['fields'][$fieldName] = array(
                            'state' => Model::ValidStateOK
                        );
                    }
                }
            }

        }
    }

    /**
     * @param array      $entity
     * @param array      $options
     * @param string     $model
     * @param array      $context
     * @param array|null $inputValues
     * @throws \Exception
     */
    protected function processPostData(array &$entity, array &$options, string $model, array $context, ?array $inputValues = null) {
        $inputValues = $inputValues ?? array_filter($_POST, function($v, $k){ return $k !== 'submit'; }, ARRAY_FILTER_USE_BOTH);
        $saveResult = $model::SaveInstance($inputValues);
        if (isset($saveResult['@error'])) {
            //Заполняем все значения обратно
            $entity = $inputValues;
            $this->processErrors($entity, $options, $saveResult['errors']);
        }
        else  {
            if ($_POST[$model::PrimaryColumnName] == 0) {
                /*Нужно сменить URL на вновь созданный элемент*/
                list($controller, $action) = self::ThisAction(debug_backtrace(2, 2)[1]);
                Redirect(
                    $controller, $action, $saveResult[DataBase::camelCaseToUnderscore($model::PrimaryColumnName)]
                );
            }
            else {
                $options['validation'] = array(
                    'state' => Model::ValidStateOK,
                    'message' => 'Данные успешно сохранены'
                );
                $dataTable = $model::DataTable;

                $pkName = DataBase::camelCaseToUnderscore($model::PrimaryColumnName);

                $queryConfig = new QueryConfig(
                    $pkName . ' = :' . $pkName,
                    null,
                    array($pkName => $context['id'])
                );

                $entity = array_keys_CamelCase(
                    (new DataBase())->
                    $dataTable->
                    getFirstRow($queryConfig)
                );
            }
        }
    }

}