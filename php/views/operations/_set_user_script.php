<?php

use Astkon\Model\User;

?>
<script type="text/javascript">
    function select<?= User::Name(); ?>(/*DOM.form-group*/ selectedUsersContainer, /*string*/ extReferencePKName, /*array*/ objectsData, /*array*/ fields) {
        if (!('operationLinkedData' in window)) {
            window.operationLinkedData = {};
        }
        let linkedObjectType = '<?= User::Name(); ?>';
        if (!(linkedObjectType in window.operationLinkedData)) {
            window.operationLinkedData[linkedObjectType] = {};
        }
        linq(objectsData).foreach(function(objectData){
            if (objectData.IdUser in window.operationLinkedData[linkedObjectType]) {
                return;
            }
            let tail = $('<div class="tail-item lightgray m-2 p-2 col-md-3"></div>');
            tail.get(0).dataset.item = JSON.stringify(objectData);

            let userNameContainer = $('<div></div>');
            userNameContainer.text(objectData.UserName);

            let groupNameContainer = $('<div></div>');
            groupNameContainer.text(objectData.$fkIdUserGroup);

            let optionsContainer = $('<div></div>');
            optionsContainer.addClass('text-right');

            <?php
                if (isset($editable)) {
            ?>
                let optionDelete = $('<a href="javascript: void(0)"><img src="/trash-empty-icon.png" class="action-icon"  title="Удалить"/></a>');
                optionsContainer.append(optionDelete);
                optionDelete.click(function () {
                    tail.remove();
                    delete window.operationLinkedData[linkedObjectType][objectData.IdUser];
                });
            <?php
                }
            ?>

            tail.append(optionsContainer);
            tail.append(userNameContainer);
            tail.append(groupNameContainer);

            $(selectedUsersContainer).append(tail);

            window.operationLinkedData[linkedObjectType][objectData.IdUser] = objectData;
        })
    }
</script>