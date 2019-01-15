<?php
if (strtolower($operationType['OperationName']) === 'reserving') {
    require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . '_print_styles.php';
}
require_once OPERATION_VIEW_DIRECTORY . DIRECTORY_SEPARATOR . strtolower($operationType['OperationName']) . 'form.php';