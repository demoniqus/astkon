<div class="row mx-0">
    <?php
    use Astkon\GlobalConst;
    use Astkon\Model\Article;
    use Astkon\Model\People;

    require_once getcwd() . DIRECTORY_SEPARATOR . GlobalConst::ViewsDirectory . DIRECTORY_SEPARATOR . 'left_menu.php';
    ?>
    <script type="text/javascript">
        function selectPeople(/*DOM.form-group*/ selectedPeopleContainer, /*string*/ extReferencePKName, /*array*/ objectsData, /*array*/ fields) {
            if (!('selectedPeople' in window)) {
                window.selectedPeople = {};
            }
            linq(objectsData).foreach(function(objectData){
                if (objectData.IdPeople in window.selectedPeople) {
                    return;
                }
                let tail = $('<div class="tail-item lightgray m-2 p-2 col-md-3"></div>');
                tail.get(0).dataset.item = JSON.stringify(objectData);

                let textContainer = $('<div></div>');
                textContainer.text(objectData.PeopleName + (objectData.PostName ? ' (' + objectData.PostName + ')' : ''));

                let optionsContainer = $('<div></div>');
                optionsContainer.addClass('text-right');

                let optionDelete = $('<a href="javascript: void(0)"><img src="/trash-empty-icon.png" class="action-icon"  title="Удалить"/></a>');
                optionsContainer.append(optionDelete);
                optionDelete.click(function(){
                    tail.remove();
                    delete window.selectedPeople[objectData.IdPeople];
                });

                tail.append(optionsContainer);
                tail.append(textContainer);

                $(selectedPeopleContainer).append(tail);

                window.selectedPeople[objectData.IdPeople] = objectData;
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
                            targetContainer: $('#PeopleListItems'),
                            extReferencePKName: '<?= People::PrimaryColumnName; ?>',
                            dataSourceUrl: 'People/PeopleDict?mode=multiple',
                            title: '',
                            setValueCallback: 'selectPeople',
                            mode: 'multiple'
                            })">Выбрать людей...</button>
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
        <div class="row" id="PeopleListItems">

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

