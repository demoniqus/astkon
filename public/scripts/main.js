DictionarySelector = (function(){
    var data = {
        refPKName: null,
        targetDOMElement: null,
        dialog: null,
        modalWindow: null
    };
    var DS = function(){};
    DS.setValue = function(objectData, fields){
        console.log(objectData, fields);
        var visibleValue = linq(fields)
            .select(function(fieldName){
                return objectData[fieldName] === undefined || objectData[fieldName] === null ? '': objectData[fieldName];
            })
            .collection.join(' ');

        $(data.targetDOMElement).find('.visible-value:first').html(visibleValue);
        $(data.targetDOMElement).find('.form-control[type=hidden]:first').val(objectData[data.refPKName]);
        DictionarySelector.close();
    };
    DS.dialog = function(formGroupDOM, refPKName, url){
        data.targetDOMElement = formGroupDOM;
        data.refPKName = refPKName;
        if (url[1] !== '/') {
            url = '/' + url;
        }
        $.post(
            url,
            null,
            function(request){
                if (request) {
                    data.modalWindow = $('<div></div>');
                    data.modalWindow.html(request);
                    data.modalWindow.css('display', 'none');
                    $('body').append(data.modalWindow);
                    data.dialog = $(data.modalWindow).dialog({
                        title: $(data.targetDOMElement).find('.col-form-label:first').text(),
                        height: document.body.clientHeight * .8,
                        width: document.body.clientWidth * .8,
                        maxHeight: document.body.clientHeight * .8,
                        maxWidth: document.body.clientWidth * .8,
                        modal: true,
                        buttons: {
                            'Закрыть': function (){
                                DictionarySelector.close();
                            }
                        }
                    })
                }

            }
        );
    };

    DS.close = function(){
        data.targetDOMElement = null;
        data.dialog.dialog('close');
        data.dialog.dialog('destroy');
        $(data.modalWindow).remove();
    };

    return DS;
})();