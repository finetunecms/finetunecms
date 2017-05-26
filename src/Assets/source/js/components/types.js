var types = document.getElementById('type-list');
if (typeof(types) != 'undefined' && types != null) {
    window.vmTypes = new window.FinetuneVue({
        el: '#types',
        filters: {},
        data: {
            types: [],
            type: {
                title: '',
                outputs: '',
                layout: '',
                blocks: '',
                nesting: '',
                children: '',
                ordering: '',
                date: '',
                order_by:'',
                default_type: '',
                today_future: '',
                today_past: '',
                pagination: '',
                pagination_limit: '',
                access: '',
                rss: '',
                live: ''
            },
            typeId: 0,
        },
        ready: function () {
            var that = this;
            this.$http.get('/admin/api/types').then(function (response) {
                that.loading = false;
                that.types = response.data;
            }, function (response){
                that.loading = false;
                this.renderError(response);
            });
        },
        methods: {
            postType: function postField() {
                var that = this;
                this.saving = true;
                if (this.typeId != 0) {
                    this.$http.put('/admin/api/types/' + this.typeId, this.type).then(function (response) {
                        that.types = response.data.types;
                        that.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelTypeUpdate');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                } else {
                    this.$http.post('/admin/api/types', this.type).then(function (response) {
                        that.types = response.data.types;
                        that.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelTypeUpdate');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                }
            },

            destroy: function destroy(event) {
                this.$broadcast('show::modal', 'showModelDelete');
                this.event = event;
            },

            submitDestroy: function submitDestroy() {
                this.$broadcast('hide::modal', 'showModelDelete');
                let items = this.selectedItems;
                let that = this;
                    this.$http.delete('/admin/api/types/destroy', {types: items}).then(function (response) {
                        that.types = response.data.types;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        this.renderError(response);
                    });

            },

            clear: function clear() {
                this.type = {
                    title: '',
                    outputs: '',
                    layout: '',
                    blocks: '',
                    nesting: '',
                    ordering: '',
                    date: '',
                    order_by:'',
                    default_type: '',
                    today_future: '',
                    today_past: '',
                    pagination: '',
                    pagination_limit: '',
                    access: '',
                    rss: '',
                    live: ''
                };
                this.typeId = 0;
                this.cleanErrors();
            },

            create: function create() {
                this.clear();
                this.$broadcast('show::modal', 'showModelTypeUpdate');
            },

            edit: function edit(item) {
                this.clear();
                this.typeId = item.id;
                this.type.title = item.title;
                this.type.outputs = item.outputs;
                this.type.layout = item.layout;
                this.type.blocks = item.blocks;
                this.type.nesting = item.nesting;
                this.type.children = item.children;
                this.type.ordering = item.ordering;
                this.type.date = item.date;
                this.type.default_type = item.default_type;
                this.type.today_future = item.today_future;
                this.type.today_past = item.today_past;
                this.type.pagination = item.pagination;
                this.type.pagination_limit = item.pagination_limit;
                this.type.access = item.access;
                this.type.order_by = item.order_by;
                this.type.rss = item.rss;
                this.type.live = item.live;
                this.$broadcast('show::modal', 'showModelTypeUpdate')
            }
        }
    });
}

var customFields = document.getElementById('fields-list');
if (typeof(customFields) != 'undefined' && customFields != null) {
    window.vmFields = new window.FinetuneVue({
        el: '#types',
        filters: {},
        data: {
            fields: [],
            field: {label: '', name: '', type: '', values: '', placeholder: '', checked: 0, multiple: 0, type_id : 0},
            fieldId: 0,
            typeId : 0,
        },
        ready: function () {
            var that = this;
            var typeIdInput = document.getElementById('typeInput');
            if (typeIdInput !== null) {
                this.typeId = typeIdInput.value;
                this.$http.get('/admin/api/fields/' + this.typeId).then(function (response) {
                    that.loading = false;
                    that.fields = response.data;
                }, function (response){
                    that.loading = false;
                    this.renderError(response);
                });
            }
        },
        methods: {
            postField: function postField() {
                var that = this;
                that.saving = true;
                if (this.fieldId != 0) {
                    this.$http.put('/admin/api/fields/' + this.fieldId, this.field).then(function (response) {
                        that.fields = response.data.fields;
                        that.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelCustomField');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                } else {
                    this.$http.post('/admin/api/fields', this.field).then(function (response) {
                        that.clear();
                        that.saving = false;
                        that.fields = response.data.fields;
                        this.$broadcast('hide::modal', 'showModelCustomField');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                }
            },

            destroy: function destroy(event) {
                this.$broadcast('show::modal', 'showModelDelete');
                this.event = event;
            },

            submitDestroy: function submitDestroy() {
                this.$broadcast('hide::modal', 'showModelDelete');
                var items = this.selectedItems;
                var that = this;
                if (items.length > 0) {
                    this.$http.delete('/admin/api/fields/delete', {fields: items}).then(function (response) {
                        that.destroyModal = false;
                        that.fields = response.data.fields;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        this.renderError(response);
                    });
                }
            },

            clear: function clear() {
                this.cleanErrors();
                this.field = {label: '', name: '', type: '', values: '', placeholder: '', checked: 0, multiple: 0, type_id : this.typeId};
            },

            create: function create() {
                this.clear();
                this.$broadcast('show::modal', 'showModelCustomField');
            },

            edit: function edit(item) {
                this.clear();
                this.fieldId = item.id;
                this.field.label = item.label;
                this.field.name = item.name;
                this.field.type = item.type;
                this.field.values = item.values;
                this.field.placeholder = item.placeholder;
                this.field.checked = item.checked;
                this.field.multiple = item.multiple;
                this.field.type_id = this.typeId;
                this.$broadcast('show::modal', 'showModelCustomField')
            }
        }
    });
}