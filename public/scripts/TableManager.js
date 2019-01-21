if (!('TableManager' in window)) {
    window.TableManager = (function(){
        let self = function(/*string*/ id){
            if (this.instance(id)) {
                return this.instance(id);
            }
            let localStorage = {
                id: id,
                pageSize: 15,
                currentPage: 0,
                filters: {},
                orderBy: [],
                baseURL: '',
                mode: 'relocation',
                queryParams: {}
            };
            let instance = this;

            instance.setPage = function(/*int*/ pageNum, /*bool*/ isInitValue) {
                localStorage.currentPage = pageNum;
                if (!isInitValue){
                    this.updateTableData();
                }
                return this;
            };

            instance.setPageSize = function(/*int*/ pageSize, /*bool*/ isInitValue) {
                localStorage.pageSize = pageSize;
                if (!isInitValue){
                    this.updateTableData();
                }
                return this;
            };

            instance.setFilter = function(/*string*/key, value, /*bool*/ isInitValue){
                localStorage.filters[key] = value;
                if (!isInitValue){
                    this.updateTableData();
                }
                return this;
            };

            instance.setBaseURL = function(/*string*/value){
                localStorage.baseURL = value;
                return this;
            };

            instance.setGETParams = function(/*object*/ params){
                if (params) {
                    for (let key in params) {
                        localStorage.queryParams[key] = params[key];
                    }
                }
                return this;
            };

            instance.setMode = function(/*string*/value){
                localStorage.mode = value.toLowerCase() === 'reload' ? 'reload' : 'relocation';
                return this;
            };

            instance.unsetFilter = function(/*string*/key){
                delete localStorage.filters[key];
                this.updateTableData();
            };

            instance.setOrder = function(/*string*/key, /*bool*/ isDesc, /*bool*/ isInitValue){
                for (let index = 0; index < localStorage.orderBy.length; index++) {
                    if (localStorage.orderBy[index][0] === key) {
                        if (localStorage.orderBy[index][1] !== !!isDesc) {
                            localStorage.orderBy[index][1] !== isDesc;
                        }
                        else {
                            return;
                        }
                    }
                }
                if (!isInitValue){
                    this.updateTableData();
                }
                return this;
            };

            instance.unsetOrder = function(/*string*/key){
                let newOrder = [];
                for (let index = 0; index < localStorage.orderBy.length; index++) {
                    if (localStorage.orderBy[index][0] !== key) {
                        newOrder.push(localStorage.orderBy[index]);
                    }
                }
                localStorage.orderBy = newOrder;
                this.updateTableData();
            };

            instance.updateTableData = function(){
                let dataURL = localStorage.baseURL;
                let queryParams = [];
                for (let key in localStorage.queryParams) {
                    queryParams.push(key + '=' + localStorage.queryParams[key]);
                }
                queryParams.push('offset=' + (localStorage.pageSize * localStorage.currentPage));

                if (queryParams.length) {
                    dataURL += '?' + queryParams.join('&');
                }

                if (localStorage.mode === 'relocation') {
                    window.location.href = dataURL;
                }
                else {
                    $.ajax({
                        url: dataURL,
                        type: 'GET',
                        success: function(response){
                            $('#' + localStorage.id).parents('.table-view-container:first').parent().empty().html(response);
                        }.bind(this)
                    });
                }
            };

            globalStorage.instances[id] = instance;
            return instance;
        };

        self.prototype = new baseInstansablePrototype();

        self.instance = self.prototype.instance;

        return self;
    })();
}
