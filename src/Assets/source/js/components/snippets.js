var snippet = document.getElementById('snippet');
if (typeof(snippet) != 'undefined' && snippet != null) {
    window.vmSnippet = new window.FinetuneVue({
        el: '#snippet',
        filters: {},
        data: {
            images: [],
            groupId: 0,
            external: false,
            nodes: [],
            selected: null,
            link: null,
            snippet: {
                body: '',
                title: '',
                media: {id: ''},
                id: 0,
                link_type: 0,
                link_internal: null,
                link_external: null,
                node: null
            },
            media: [],
            allMedia: [],
            mediaId: '',
            imageId: '',
            folders: [],
            folderId: '',
            folder: {'tag': 'all', 'title': ' All Media'},
            files: [],
            fileId: '',
            searchTerm: '',
            snippetId: 0,
            showModelImageSelector: false
        },
        ready: function () {
            var that = this;
            this.$http.get('/admin/api/nodes/links').then(function (response) {
                that.nodes = response.data;
            }, function (response) {
                this.renderError(response);
            });
            this.groupId = document.getElementById('groupIdField').value;
            var snippetField = document.getElementById('snippetIdField');
            if (typeof(snippetField) != 'undefined' && snippetField != null) {
                this.snippetId = document.getElementById('snippetIdField').value;
                this.$http.get('/admin/api/snippets/' + that.snippetId).then(function (response) {
                    that.loading = false;
                    that.snippet = response.data;
                }, function (response) {
                    that.loading = false;
                    this.renderError(response);
                });
            }
            this.getFolders();
            this.link = this.selected;
            this.$http.get('/admin/api/nodes/links').then(function (response) {
                window.links = response.data;
            }, function (response){
                this.renderError(response);
            });
        },
        methods: {

            changeFolder: function changeFolder() {
                this.media = [];
                if(this.folder.length > 0){
                    this.media = this.allMedia;
                }else{
                    if (this.folder.tag == 'all') {
                        this.media = this.allMedia;
                    } else {
                        this.media = this.folder.media;
                    }
                }
            },
            getFolders: function getFolders() {
                var that = this;
                this.$http.get('/admin/api/folders').then(function (response) {
                    that.folders = response.data;
                    that.folders.unshift({'tag': 'all', 'title': ' All Media', 'id': 0})
                }, function (response) {
                    this.renderError(response);
                });
            },
            getMedia: function getMedia() {
                var that = this;
                this.$http.get('/admin/api/media').then(function (response) {
                    that.media = response.data;
                    that.allMedia = that.media;
                }, function (response) {
                    this.renderError(response);
                });
            },
            search: function search(filter) {
                var that = this;
                var items = [];
                if (this.searchTerm.length === 0) {
                    items = this.media;
                } else {
                    items = this.allMedia.filter(function (media) {
                        if (that.searchTerm.length === 0) {
                            return true;
                        } else {
                            if (media.filename.indexOf(that.searchTerm) !== -1) {
                                return true;
                            } else {
                                return media.title.indexOf(that.searchTerm) !== -1;
                            }
                        }
                    });
                }
                if (filter == 'all') {
                    return items;
                } else {
                    if (filter == 'images') {
                        var mediaImages = [];
                        for (var i = 0; i < items.length; i++) {
                            if (items[i].type == 'image') {
                                mediaImages.push(items[i]);
                            }
                        }
                        return mediaImages;
                    } else {
                        var mediaFiles = [];
                        for (var i = 0; i < items.length; i++) {
                            if (items[i].type == 'file') {
                                mediaFiles.push(items[i]);
                            }
                        }
                        return mediaFiles;
                    }
                }
            },


            saveSnippet: function saveContent(publish) {
                var that = this;
                this.snippet.publish = publish;
                this.snippet.group_id = this.groupId;
                that.saving = true;
                if (this.snippet.id != 0) {
                    this.$http.put('/admin/api/snippets/' + this.snippet.id, this.snippet).then(function (response) {
                        that.snippet = response.data.snippet;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        that.saving = false;
                    }, function (response) {
                        that.saving = false;
                        this.renderError(response);
                    });
                } else {
                    this.$http.post('/admin/api/snippets', this.snippet).then(function (response) {
                        that.snippet = response.data.snippet;
                        that.saving = false;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response) {
                        that.saving = false;
                        this.renderError(response);
                    });
                }
            },
            insert: function insert(itemId) {
                for (var i = 0; i < this.media.length; i++) {
                    if (this.media[i].id == itemId) {
                        this.snippet.media = this.media[i];
                    }
                }
                this.$broadcast('hide::modal', 'showModalImage')
            },
            updateSelected: function (newSelected) {
                this.selected = newSelected;
                this.link = this.selected.id
            },
            titleWithUrl: function (node) {
                return node.title + ' - [' + node.url_slug + ']';
            },
            linkChange: function linkChange(val) {
                this.snippet.link_internal = val;
            },
            changeImage: function changeImage() {
                this.cleanErrors();
                this.getMedia();
                this.$broadcast('show::modal', 'showModalImage')
            },
            removeImage: function removeImage() {
                this.snippet.media = null;
            },

        },
        components: {
            'tabs': window.VueStrap.tabs,
            'tab': window.VueStrap.tab,
            'v-select': window.vSelect,
        }
    });
}

var snippets = document.getElementById('snippets');
if (typeof(snippets) != 'undefined' && snippets != null) {
    window.vmSnippets = new window.FinetuneVue({
        el: '#snippets',
        filters: {},
        data: {
            groupId: 0,
            group: {
                title: 'Title',
                tag: 'Tag',
                dscpn: 'Description'
            },
            groups: [],
            snippets: [],
        },
        ready: function () {
            var that = this;
            var groupIdInput = document.getElementById('groupInput');
            if (groupIdInput == null) {
                // This is the ajax request to get the tags
                this.$http.get('/admin/api/snippetgroups').then(function (response) {
                    that.loading = false;
                    that.groups = response.data;
                }, function (response) {
                    that.loading = false;
                    this.renderError(response);
                });
            } else {
                this.groupId = groupIdInput.value;
                // This is the ajax request to get the tags
                this.$http.get('/admin/api/snippetgroups/' + this.groupId).then(function (response) {
                    that.loading = false;
                    that.snippets = response.data;
                }, function (response) {
                    that.loading = false;
                    this.renderError(response);
                });
            }
        },
        methods: {
            togglePublish: function (item) {
                var that = this;
                this.$http.post('/admin/api/snippets/publish', {
                    group: that.groupId,
                    snippet: item
                }).then(function (response) {
                    that.clear();
                    that.snippets = response.data.snippets;
                    that.alertBox(true, response.data.alertType, response.data.alertMessage);
                }, function (response) {
                    this.renderError(response);
                });
            },
            orderUpdate: function (event) {
                this.snippets.splice(event.newIndex, 0, this.snippets.splice(event.oldIndex, 1)[0])
            },
            orderSave: function () {
                var that = this;
                this.$http.post('/admin/api/snippets/order', {
                    group: that.groupId,
                    snippets: this.snippets
                }).then(function (response) {
                    that.clear();
                    that.alertBox(true, response.data.alertType, response.data.alertMessage);
                }, function (response) {
                    this.renderError(response);
                });
            },
            edit: function edit(group) {
                this.groupId = group.id;
                this.group.title = group.title;
                this.group.tag = group.tag;
                this.group.dscpn = group.dscpn;
                this.cleanErrors();
                this.$broadcast('show::modal', 'showModalGroupUpdater');
            },
            createGroup: function createGroup() {
                this.clear();
                this.$broadcast('show::modal', 'showModalGroupUpdater');

            },
            postGroup: function postGroup(event) {
                var that = this;
                this.cleanErrors();
                that.saving = true;
                if (that.groupId > 0) {
                    this.$http.put('/admin/api/snippetgroups/' + that.groupId, this.group).then(function (response) {
                        that.groups = response.data.groups;
                        that.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModalGroupUpdater');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response) {
                        this.renderError(response);
                        that.saving = false;
                    });
                } else {
                    this.$http.post('/admin/api/snippetgroups', this.group).then(function (response) {
                        that.groups = response.data.groups;
                        that.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModalGroupUpdater');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response) {
                        this.renderError(response);
                        that.saving = false;
                    });
                }
            },
            clear: function clear() {
                this.group.title = '';
                this.group.tag = '';
                this.group.dscpn = '';
                this.groupId = 0;
                this.cleanErrors();
                this.clearItems();
            },
            destroy: function destroy(event) {
                this.$broadcast('show::modal', 'showModelDelete');
                this.event = event;
            },
            submitDestroy: function submitDestroy() {
                this.$broadcast('hide::modal', 'showModelDelete');
                var items = this.selectedItems;
                var that = this;
                if (this.snippets.length > 0) {
                    if (items.length > 0) {
                        this.$http.delete('/admin/api/snippets/delete', {
                            snippets: items,
                            group: this.groupId
                        }).then(function (response) {
                            that.destroyModal = false;
                            that.snippets = response.data.snippets;
                            this.clear();
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response) {
                            this.renderError(response);
                        });
                    }
                } else {
                    if (items.length > 0) {
                        this.$http.delete('/admin/api/snippetgroups/delete', {groups: items}).then(function (response) {
                            that.destroyModal = false;
                            this.clear();
                            that.groups = response.data.groups;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response) {
                            this.renderError(response);
                        });
                    }
                }
            },
        },
        components: {
            'dropdown': window.VueStrap.dropdown
        }
    });
}