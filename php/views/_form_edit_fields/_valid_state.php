<?php
use Astkon\Model\Model;

$feedback = '';
$validClass = '';
switch($validState) {
    case Model::ValidStateOK:
        $validClass = 'is-valid';
        break;
    case Model::ValidStateError:
        $validClass = 'is-invalid';
        break;
}
if ($validMessage && $validState !== Model::ValidStateUndefined) {
    $feedback = '<div class="' . ($validState === Model::ValidStateOK ? 'valid-feedback' : 'invalid-feedback') . '">' . $validMessage . '</div>';
}