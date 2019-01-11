<script type="text/javascript">
    Measures = <?= json_encode($Measures); ?>;
    OperationType = <?= json_encode($operationType); ?>;
    Operation = <?= json_encode($operation); ?>;
</script>
<div class="row">
    <div class="col alert alert-info">
        <?= $title; ?>
    </div>
</div>
<div class="row">
    <div class="col text-left">
        Дата создания <?= $operation['CreateDatetime'] ? $operation['CreateDatetime']->format('d.m.Y') : ''; ?>
<!--        <input type="date" />-->
    </div>
    <div class="col">
        Дата фиксации <?= $operation['FixDatetime'] ? $operation['FixDatetime']->format('d.m.Y') : ''; ?>
<!--        <input type="date" />-->
    </div>
</div>
<div id="saving-result" class="alert"></div>