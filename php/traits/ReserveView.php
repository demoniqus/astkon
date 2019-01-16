<?php
/**
 * Created by PhpStorm.
 * User: demoniqus
 * Date: 14.01.19
 * Time: 17:05
 */

namespace Astkon\Traits;


use Astkon\ErrorCode;
use Astkon\Model\Operation;
use Astkon\Model\OperationItem;
use Astkon\Model\OperationState;
use Astkon\Model\OperationType;
use Astkon\QueryConfig;
use Astkon\View\View;

trait ReserveView
{

    private function ReservesList(
        array $context,
        View $view,
        string $targetModel,
        string $targetOperationName
    ) {
        $targetItem = $targetModel::GetByPrimaryKey(intval($context['id']));

        if (!$targetItem) {
            $view->error(ErrorCode::NOT_FOUND);
            die();
        }

        $opStateNew = OperationState::getFirstRow(
            null,
            new QueryConfig(
                '`state_name` = \'new\'',
                array(OperationState::PrimaryColumnKey)
            )
        );

        $opTypeReserving = OperationType::getFirstRow(
            null,
            new QueryConfig(
                '`operation_name` = \'' . $targetOperationName . '\'',
                array(OperationType::PrimaryColumnKey)
            )
        );

        $substitution = array(
            OperationState::PrimaryColumnKey => $opStateNew[OperationState::PrimaryColumnKey],
            OperationType::PrimaryColumnKey  => $opTypeReserving[OperationType::PrimaryColumnKey],
        );

        $view->item = $targetItem;

        $view->targetModel = $targetModel;

        $rows = OperationItem::getRows(
            null,
            new QueryConfig(
                implode(
                    ' AND ',
                    array(
                        '`' . Operation::DataTable . '`.`' . OperationState::PrimaryColumnKey . '` = :' . OperationState::PrimaryColumnKey,
                        '`' . Operation::DataTable . '`.`' . OperationType::PrimaryColumnKey . '` = :' . OperationType::PrimaryColumnKey,
                    )
                ),
                array(
                    'operation_count',
                    'measure_name',
                    'linked_data',
                    'article_name',
                    Operation::PrimaryColumnKey,
                ),
                $substitution
            ),
            2
        );
        $model = $targetModel::Name();
        $rows = array_filter(
            $rows,
            function($row) use ($targetItem, $targetModel, $model){
                return is_array($row['linked_data']) &&
                    array_key_exists($model, $row['linked_data']) &&
                    is_array($row['linked_data'][$model]) &&
                    in_array($targetItem[$targetModel::PrimaryColumnKey], $row['linked_data'][$model]);
            }
        );

        $view->rows = $rows;

    }
}