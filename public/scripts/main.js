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
                        buttons: {
                            'Закрыть': function (){
                                if (typeof function(){} === typeof options.onClose) {
                                    options.onClose();
                                }
                                _self.close(__data);
                            }
                        }
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

    var DF = function(/*DOM.form-group*/ formGroupElement, /*string*/ extReferencePKName, /*string*/ dataSourceUrl){
        var __data = {
            id: this.getID(),
        };
        this.setValue = function(objectsData, fields){
            var objectData = objectsData[0];
            var visibleValue = linq(fields)
                .select(function(fieldName){
                    return objectData[fieldName] === undefined || objectData[fieldName] === null ? '': objectData[fieldName];
                })
                .collection.join(' ');

            $(formGroupElement).find('.visible-value:first').html(visibleValue);
            $(formGroupElement).find('.form-control[type=hidden]:first').val(objectData[extReferencePKName]);
            this.unregisterId(__data.id);
        };

        __data.DictionarySelector = new DictionarySelector({
            url: dataSourceUrl,
            title: $(formGroupElement).find('.col-form-label:first').text(),
            onSelect: this.setValue.bind(this),
            onClose: function(){ this.unregisterId(__data.id);}.bind(this)
        });
    };

    DF.prototype = baseInstansablePrototype();
    return DF;
})();




