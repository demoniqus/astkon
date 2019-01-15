<?php
    if (strtolower($operationType['OperationName']) === 'reserving') {
        ?>
        <style type="text/css">
            @media print {
                * {
                    background-color:  transparent !important;
                    background-image: none !important;
                }

                #document-header,
                #document-footer,
                #operation-form > *,
                #operation-form .option-cell,
                .alert,
                .btn,
                .left-menu {
                    display: none !important;
                }
                img {
                    opacity: 0 !important;
                    visibility: hidden !important;
                }
                #operation-form #OperationListItems {
                    display: block !important;
                }
                #operation-form input[type=text],
                #operation-form input[type=number],
                #operation-form input[type=date],
                #operation-form select,
                #operation-form textarea {
                    background-color: transparent !important;
                    border-color: transparent !important;
                    padding-top: 0px !important;
                    padding-bottom: 0px !important;
                }

                #OperationListItems .row > * {
                    border: 1px solid darkgray;
                    border-left-color: transparent;
                    border-bottom-color: transparent;
                }
                #OperationListItems .row > *:first-child {
                    border-left-color: darkgray;
                }
                #OperationListItems .row:last-child > * {
                    border-bottom-color: darkgray;
                }
                #OperationListItems .row {
                    margin: 0px !important;
                }
                #document-body {
                    height: auto !important;
                    overflow: visible !important;
                }
                #sign-panel {
                    display: table !important;
                }

            }
        </style>
        <?php
    }
?>
