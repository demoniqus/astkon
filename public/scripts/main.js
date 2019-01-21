baseInstansablePrototype = function(){
    var __static_data = {
        instances: {}
    };
    var o = Object.create(null);
    o.getID = function(){
        var id;
        while ((id = Math.ceil(Math.random() * 1000000000)) in __static_data.instances) {
            true;
        }
        __static_data.instances[id] = this;
        return id;
    };

    o.registerId = function(id){
        __static_data.instances[id] = this;
    };

    o.unregisterId = function(id){
        if (id in __static_data.instances) {
            delete __static_data.instances[id];
        }
    };
    o.instance = function(id){
        return (id in __static_data.instances ? __static_data.instances[id] : null);
    };

    return o;
};

DictionarySelector = (function(){
    var DS = function(/*object*/options){
        var _self = this;
        var __data = {
            id: this.getID(),
        };
        var url = options.url;
        if (url[1] !== '/') {
            url = '/' + url;
        }
        var dialogButtons = [];
        if (options.dialogButtons) {
            for (let i in options.dialogButtons) {
                let dlgBtn = options.dialogButtons[i];
                dlgBtn.click = dlgBtn.click.bind({this: this, storage: __data});
                dialogButtons.push(dlgBtn);
            }
        }
        dialogButtons.push({
            'class': 'btn btn-light',
            text: 'Закрыть',
            click: function (){
                if (typeof function(){} === typeof options.onClose) {
                    options.onClose();
                }
                this.this.close(this.storage);
            }.bind({this: this, storage: __data})
        });

        $.post(
            url,
            {dialogId : __data.id},
            function(request){
                if (request) {
                    __data.modalWindow = $('<div></div>');
                    __data.modalWindow.html(request);
                    __data.modalWindow.css('display', 'none');
                    $('body').append(__data.modalWindow);
                    __data.dialog = $(__data.modalWindow).dialog({
                        title: options.title || '',
                        height: document.body.clientHeight * .8,
                        width: document.body.clientWidth * .8,
                        maxHeight: document.body.clientHeight * .8,
                        maxWidth: document.body.clientWidth * .8,
                        modal: true,
                        buttons: dialogButtons
                    })
                }

            }
        );

        this.setValue = function(objectsData, fields){
            options.onSelect(objectsData, fields);
            _self.close(__data);
        };

    };
    DS.setValue = function(DictionarySelectorId, objectsData, fields) {
        DS.prototype.instance(DictionarySelectorId).setValue(objectsData, fields);
    };

    DS.prototype = new baseInstansablePrototype();

    DS.prototype.close = function(__data){
        __data.dialog.dialog('close');
        __data.dialog.dialog('destroy');
        $(__data.modalWindow).remove();
        this.unregisterId(__data.id)
    };



    return DS;
})();


DictionaryField = (function(){

    var DF = function(options){
        var __data = {
            id: this.getID(),
        };
        this.setValue = function(objectsData, fields){
            var setValueCallback = options.setValueCallback;
            if (typeof setValueCallback === typeof 'aaa') {
                setValueCallback = linq(setValueCallback.split('.')).reduce(function(o, key){ return o[key]; }, window)
            }
            setValueCallback(options.targetContainer, options.extReferencePKName, objectsData, fields);
            this.unregisterId(__data.id);
        };

        var dsOptions = {
            url: options.dataSourceUrl,
            title: $(options.targetContainer).find('.col-form-label:first').text(),
            onSelect: this.setValue.bind(this),
            onClose: function(){ this.unregisterId(__data.id);}.bind(this)
        };

        if (options.mode === 'multiple') {
            dsOptions.dialogButtons = [
                {
                    'class': 'btn btn-light',
                    text: 'Добавить отмеченные',
                    click: function(){
                        let objectsData = linq(this.storage.modalWindow.find('tr[data-checked_state=checked]'))
                            .select(
                                function(tr){
                                    return JSON.parse(tr.dataset.item);
                                }
                            ).collection;
                        this.this.setValue(objectsData);
                    }
                }
            ]
        }

        __data.DictionarySelector = new DictionarySelector(dsOptions);
    };

    DF.prototype = baseInstansablePrototype();
    return DF;
})();

function setSingleReferenceValue (/*DOM.form-group*/ formGroupElement, /*string*/ extReferencePKName, /*array*/ objectsData, /*array*/ fields){
    var objectData = objectsData[0];
    var visibleValue = linq(fields)
        .select(function(fieldName){
            return objectData[fieldName] === undefined || objectData[fieldName] === null ? '': objectData[fieldName];
        })
        .collection.join(' ');

    $(formGroupElement).find('.visible-value:first').html(visibleValue);
    $(formGroupElement).find('.form-control[type=hidden]:first').val(objectData[extReferencePKName]);
}

function setSelectedArticlesAsEditable(/*DOM.form-group*/ selectedArticlesContainer, /*string*/ extReferencePKName, /*array*/ objectsData, /*array*/ fields) {
    if (!('selectedArticles' in window)) {
        window.selectedArticles = {};
    }
    linq(objectsData).foreach(function(articleData){
        if (articleData.IdArticle in window.selectedArticles) {
            return;
        }
        let row = $('<div class="row mb-2 text-left"></div>');
        row.get(0).dataset.item = JSON.stringify(articleData);
        let cell = $('<div class="col">' + articleData.ArticleName + '</div>');
        row.append(cell);

        cell = $('<div class="col-md-1">' + Measures[articleData.IdMeasure].MeasureName + '</div>');
        row.append(cell);

        cell = $('<div class="col-md-2"><input type="number" class="form-control" value="' + (articleData.Count ? articleData.Count : '') + '" /></div>');
        row.append(cell);
        cell.find('input:first').blur(function(){
            let val = $(this).val();
            let measure = Measures[articleData.IdMeasure];
            if (measure.IsSplit) {
                val = val.replace(',', '.');
                val = val.match(new RegExp('^-?\\d+(?:\\.\\d{0,' + measure.Precision + '})?'));
                val = val ? val[0] : 0;
            }
            else {
                val = val.match(/^-?\d+/);
                val = val ? val[0] : 0;
            }
            $(this).val(val);

        });

        cell = $('<div class="col-md-2 option-cell"></div>');
        row.append(cell);
        let optionDelete = $('<img src="/trash-empty-icon.png" class="action-icon"  title="Удалить" style="cursor: pointer;"/>');
        cell.append(optionDelete);
        optionDelete.click(function(){
            row.remove();
            delete window.selectedArticles[articleData.IdArticle];
        });

        $(selectedArticlesContainer).append(row);

        window.selectedArticles[articleData.IdArticle] = articleData;
    })
}

function DictionaryItemChangeCheckedState(/*DOM img*/img) {
    let tr = $(img).parents('tr:first').get(0);
    let state = tr.dataset.checked_state || 'unchecked';
    state = state === 'unchecked' ? 'checked' : 'unchecked';
    tr.dataset.checked_state = state;
    $(img).attr('src', '/checkbox-' + state + '.png');

}

function setSelectedArticles(/*DOM.form-group*/ selectedArticlesContainer, /*string*/ extReferencePKName, /*array*/ objectsData, /*array*/ fields) {
    if (!('selectedArticles' in window)) {
        window.selectedArticles = {};
    }
    linq(objectsData).foreach(function(articleData){
        if (articleData.IdArticle in window.selectedArticles) {
            return;
        }
        let row = $('<div class="row mb-2 text-left"></div>');
        row.get(0).dataset.item = JSON.stringify(articleData);
        let cell = $('<div class="col">' + articleData.ArticleName + '</div>');
        row.append(cell);

        cell = $('<div class="col-md-1">' + Measures[articleData.IdMeasure].MeasureName + '</div>');
        row.append(cell);

        cell = $('<div class="col-md-2"><input type="number"  class="form-control disabled" disabled="disabled" value="' + (articleData.Count || 0) + '" /></div>');
        row.append(cell);

        $(selectedArticlesContainer).append(row);

        window.selectedArticles[articleData.IdArticle] = articleData;
    })
}


function saveOperation(/*bool*/ setFixedState) {
    $('#btn-save,#btn-save-fixed').attr('disabled', 'disabled').addClass('disabled');
    let errCount = 0;
    var setFieldState = function(row, state){
        let input = row.find('input[type=number]:first');
        // Сначала очищаем статус поля на случай, если проверка уже повторная и статус был установлен,
        // а затем проставляем статус для полей с ошибкой
        input.removeClass('is-invalid')
        input.parent().find('.invalid-feedback').remove();
        if (state === 'invalid') {
            input.addClass('is-invalid');
            input.parent().append('<div class="invalid-feedback">Недопустимое количество</div>');
        }
    };
    let selectedItems = linq($('#OperationListItems').find('.row[data-item]')).select(function(row){
        let state = 'valid';
        let item = JSON.parse(row.dataset.item);
        let result = {
            IdArticle: item.IdArticle,
            count: +$(row).find('input[type=number]:first').val()
        };
        if (OperationType.OperationName === 'Inventory' ? result.count < 0 : result.count <= 0) {
            errCount++;
            state = 'invalid';
        }
        setFieldState($(row), state);
        return result;
    }).collection;

    if (!errCount) {
        let data = {
            selectedItems: selectedItems,
            operation: Operation,
            setFixedState: !!setFixedState,
            linkedData: window.operationLinkedData || null
        };
        $.ajax({
            url: '/Operations/Save',
            data: data,
            type: 'POST',
            success: function(response){
                if (response.errors) {
                    $('#saving-result').html(response.errors.join('<br />'));
                    $('#saving-result').addClass('alert-danger');
                    $('#btn-save,#btn-save-fixed').removeAttr('disabled').removeClass('disabled');
                }
                else if (response.success) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                    else {
                        $('#btn-save,#btn-save-fixed').removeAttr('disabled').removeClass('disabled');
                        $('#saving-result').html('Данные успешно сохранены');
                        $('#saving-result').addClass('alert-success');
                        window.Operation = response.operation;
                    }
                }
            }
        });
    }
    else {
        $('#btn-save,#btn-save-fixed').removeAttr('disabled').removeClass('disabled');
    }

}