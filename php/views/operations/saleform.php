<div class="row mx-0">
    <?php
    use Astkon\GlobalConst;
    use Astkon\Model\Article;
    use Astkon\Model\BuildObject;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php';
    ?>
    <script type="text/javascript">
        function selectBuildObjects(/*DOM.form-group*/ selectedBuildObjectsContainer, /*string*/ extReferencePKName, /*array*/ objectsData, /*array*/ fields) {
            if (!('selectedBuildObjects' in window)) {
                window.selectedBuildObjects = {};
            }
            linq(objectsData).foreach(function(objectData){
                if (objectData.IdBuildObject in window.selectedBuildObjects) {
                    return;
                }
                let tail = $('<div class="tail-item lightgray m-2 p-2 col-md-3"></div>');
                tail.get(0).dataset.item = JSON.stringify(objectData);

                let textContainer = $('<div></div>');
                textContainer.text(objectData.BuildObjectName);

                let optionsContainer = $('<div></div>');
                optionsContainer.addClass('text-right');

                let optionDelete = $('<a href="javascript: void(0)"><img src="/trash-empty-icon.png" class="action-icon"  title="Удалить"/></a>');
                optionsContainer.append(optionDelete);
                optionDelete.click(function(){
                    tail.remove();
                    delete window.selectedBuildObjects[objectData.IdBuildObject];
                });

                tail.append(optionsContainer);
                tail.append(textContainer);

                $(selectedBuildObjectsContainer).append(tail);

                window.selectedBuildObjects[objectData.IdBuildObject] = objectData;
            })
        }
    </script>
    <div class="col-lg text-center" id="operation-form">
        <?php
            require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_operation_form_header.php';
        ?>
        <div class="row mt-3">
            <button
                type="button"
                class="btn btn-light"
                onclick="new DictionaryField({
                        targetContainer: $('#BuildObjectsListItems'),
                        extReferencePKName: '<?= BuildObject::PrimaryColumnName; ?>',
                        dataSourceUrl: 'BuildObjects/BuildObjectsDict?mode=multiple',
                        title: '',
                        setValueCallback: 'selectBuildObjects',
                        mode: 'multiple'
                        })">Выбрать объекты...</button>
            <button
                type="button"
                class="btn btn-light ml-3"
                onclick="new DictionaryField({
                    targetContainer: $('#OperationListItems').find('.container-fluid:first'),
                    extReferencePKName: '<?= Article::PrimaryColumnName; ?>',
                    dataSourceUrl: '<?= $dictionaryAction . '?mode=multiple'; ?>',
                    title: '',
                    setValueCallback: 'setSelectedArticles',
                    mode: 'multiple'
                    })">Добавить элементы...</button>
        </div>
        <div class="row" id="BuildObjectsListItems">

        </div>
        <div class="row" id="OperationListItems">
            <div class="container-fluid">

            </div>
        </div>
        <div class="row mt-3">

            <button type="button" class="btn btn-primary" onclick="saveOperation(false)">Сохранить</button>
            <button type="button" class="btn btn-primary ml-2" onclick="saveOperation(true)">Зафиксировать</button>
        </div>
    </div>
</div>

