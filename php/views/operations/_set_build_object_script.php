<?php

use Astkon\Model\BuildObject;

?>
<script type="text/javascript">
    function select<?= BuildObject::Name(); ?>(/*DOM.form-group*/ selectedBuildObjectsContainer, /*string*/ extReferencePKName, /*array*/ objectsData, /*array*/ fields) {
        if (!('operationLinkedData' in window)) {
            window.operationLinkedData = {};
        }
        let linkedObjectType = '<?= BuildObject::Name(); ?>';
        if (!(linkedObjectType in window.operationLinkedData)) {
            window.operationLinkedData[linkedObjectType] = {};
        }
        linq(objectsData).foreach(function(objectData){
            if (objectData.IdBuildObject in window.operationLinkedData[linkedObjectType]) {
                return;
            }
            let tail = $('<div class="tail-item lightgray m-2 p-2 col-md-3"></div>');
            tail.get(0).dataset.item = JSON.stringify(objectData);

            let textContainer = $('<div></div>');
            textContainer.text(objectData.BuildObjectName);

            let optionsContainer = $('<div></div>');
            optionsContainer.addClass('text-right');

            <?php
                if (isset($editable)) {
            ?>
                let optionDelete = $('<a href="javascript: void(0)"><img src="/trash-empty-icon.png" class="action-icon"  title="Удалить"/></a>');
                optionsContainer.append(optionDelete);
                optionDelete.click(function () {
                    tail.remove();
                    delete window.operationLinkedData[linkedObjectType][objectData.IdBuildObject];
                });
            <?php
                }
            ?>

            tail.append(optionsContainer);
            tail.append(textContainer);

            $(selectedBuildObjectsContainer).append(tail);

            window.operationLinkedData[linkedObjectType][objectData.IdBuildObject] = objectData;
        })
    }
</script>