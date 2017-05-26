var tags = document.getElementById('tags');
if (typeof(tags) != 'undefined' && tags != null) {
    window.vmTags = new window.FinetuneVue({
        el: '#tags',
        filters: {},
        data: {
            tags: [],
            tag: {title: 'My New Title', tag: 'My New Tag'},
            tagId: 0,
            nodes: [],
        },

        ready: function () {
            var that = this;
            var tagIdInput = document.getElementById('tagInput');
            if (tagIdInput == null) {
                this.$http.get('/admin/api/tags').then(function (response) {
                    that.loading = false;
                    that.tags = response.data;
                }, function (response){
                    that.loading = false;
                    this.renderError(response);
                });
            } else {
                this.tagId = tagIdInput.value;
                this.$http.get('/admin/api/tags/' + this.tagId).then(function (response) {
                    that.loading = false;
                    that.nodes = response.data;
                }, function (response){
                    that.loading = false;
                    this.renderError(response);
                });
            }
        },
        methods: {
            edit: function edit(tag) {
                this.tagId = tag.id;
                this.tag.title = tag.title;
                this.tag.tag = tag.tag;
                this.cleanErrors();
                this.$broadcast('show::modal', 'showModalTagUpdater')
            },
            postTag: function postTag() {
                var that = this;
                this.cleanErrors();
                that.saving = true;
                if (that.tagId > 0) {
                    this.$http.put('/admin/api/tags/' + that.tagId, this.tag).then(function (response) {
                        that.tags = response.data.tags;
                        that.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModalTagUpdater')
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                } else {
                    this.$http.post('/admin/api/tags', this.tag).then(function (response) {
                        that.tags = response.data.tags;
                        that.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModalTagUpdater')
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                }
            },

            clear: function clear() {
                this.tag.title = '';
                this.tag.tag = '';
                this.tagId = 0;
                this.cleanErrors();
            },
            createTag: function createTag() {
                this.clear();
                this.$broadcast('show::modal', 'showModalTagUpdater')
            },
            destroy: function destroy(event) {
                this.$broadcast('show::modal', 'showModelDelete');
                this.event = event;
            },
            submitDestroy: function submitDestroy() {
                this.$broadcast('hide::modal', 'showModelDelete');
                var items = this.selectedItems;
                var that = this;
                if (this.nodes.length > 0) {
                    if (items.length > 0) {
                        this.$http.delete('/admin/api/tags/node-delete', {
                            nodes: items,
                            tagId: this.tagId
                        }).then(function (response) {
                            that.destroyModal = false;
                            that.nodes = response.data.nodes;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response){
                            this.renderError(response);
                        });
                    }
                } else {
                    if (items.length > 0) {
                        this.$http.delete('/admin/api/tags/delete', {tags: items}).then(function (response) {
                            that.tags = response.data.tags;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response){
                            this.renderError(response);
                        });
                    }
                }
            },

        }
    });
}