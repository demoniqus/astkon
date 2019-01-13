
<div class="row mx-0">
    <?php

    use Astkon\DataBase;
    use Astkon\GlobalConst;
    use function Astkon\Lib\TileMenu;
    use Astkon\linq;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php';
    ?>
    <div class="col-md text-center">
        <div class="container-fluid">


            <?php
            $operationTypes = (new linq((new DataBase())->operation_type->getRows()))
                ->toAssoc(
                    function($operationType){return $operationType['operation_name'];},
                    function($operationType){return $operationType['id_operation_type'];}
                )
                ->getData();
            $menu = array(
                array(
                    'Action' => '/Operations/OperationsList/' . $operationTypes['Income'],
                    'Caption' => 'Поступление',
                    'Icon' => '/receipt.jpg',
                ),
                array(
                    'Action' => '/Operations/OperationsList/' . $operationTypes['Sale'],
                    'Caption' => 'Расход',
                    'Icon' => '/leaving.png',
                ),
                array(
                    'Action' => '/Operations/OperationsList/' . $operationTypes['WriteOff'],
                    'Caption' => 'Списание',
                    'Icon' => '/write-off.png',
                    'NewRow' => true,
                ),
                array(
                    'Action' => '/Operations/OperationsList/' . $operationTypes['Reserving'],
                    'Caption' => 'Временное пользование',
                    'Icon' => '/tools-pict-time.png',
                ),
                array(
                    'Action' => '/Operations/OperationsList/' . $operationTypes['Inventory'],
                    'Caption' => 'Инвентаризация',
                    'Icon' => '/invent.jpeg',
                ),
            );

            TileMenu($menu, 4, 2);
            ?>

        </div>
    </div>
</div>

