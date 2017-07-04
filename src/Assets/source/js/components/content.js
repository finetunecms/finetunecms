var content = document.getElementById('content');
if (typeof(content) != 'undefined' && content != null) {

    var contentList = document.getElementById('content-list');
    var contentShow = document.getElementById('content-show');
    var contentUpdate = document.getElementById('content-update');

    if (typeof(contentList) != 'undefined' && contentList != null) {
        window.vmContentList = new window.FinetuneVue({
            el: '#content',
            filters: {},
            data: {
                nodes: [],
                moveNodes: [],
                moveParent: 0,
                searchTerm: '',
                moveAction: false,
                canOrder: true,
                canMove: true,
                destroyBtn: true,
            },
            ready: function () {
                var that = this;
                this.$http.get('/admin/api/nodes/areas').then(function (response) {
                    that.loading = false;
                    that.nodes = response.data;
                }, function (response) {
                    this.renderError(response);
                });

                this.$http.get('/admin/api/nodes/movable').then(function (response) {

                    for (var i = 0; i < response.data.length; i++) {
                        that.moveNodes.push({
                            id: response.data[i].id,
                            title: response.data[i].title,
                            area: response.data[i].area,
                            type_id: response.data[i].type.id,
                            area_fk: response.data[i].area_fk
                        });
                    }
                    that.moveNodes.sort(function (a, b) {
                        var textA = a.title.toUpperCase();
                        var textB = b.title.toUpperCase();
                        return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
                    });

                    that.moveNodes.unshift({id: 0, title: "Top Level"})
                }, function (response) {
                    this.renderError(response);
                });
            },
            methods: {
                canShowManage: function canShowManage(item) {
                    if (item.type.nesting == 1) {
                        return true
                    } else {
                        if (item.area == 1) {
                            if (item.type.date == 1) {
                                return true
                            }
                        }
                    }
                    return false;
                },
                togglePublish: function (item) {
                    var that = this;
                    this.$http.post('/admin/api/nodes/publish', {
                        node: item, parent: 0
                    }).then(function (response) {
                        that.nodes = response.data.nodes;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response) {
                        that.renderError(response);
                    });
                },
                orderUpdate: function (event) {
                    this.nodes.splice(event.newIndex, 0, this.nodes.splice(event.oldIndex, 1)[0])
                },
                orderSave: function (event) {
                    var that = this;
                    this.$http.post('/admin/api/nodes/order', {
                        nodes: this.nodes,
                        parent: 0
                    }).then(function (response) {
                        that.nodes = response.data.nodes;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response) {
                        that.renderError(response);
                    });
                },
                destroy: function destroy(event) {
                    this.$broadcast('show::modal', 'showModelDelete');
                    this.event = event;
                },
                submitDestroy: function submitDestroy() {
                    var items = this.selectedItems;
                    this.$broadcast('hide::modal', 'showModelDelete');
                    var that = this;
                    if (items.length > 0) {
                        this.$http.delete('/admin/api/nodes/delete', {
                            nodes: items,
                            parent: 0
                        }).then(function (response) {
                            that.nodes = response.data.nodes;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response) {
                            that.renderError(response);
                        });
                        this.selectedItems = [];
                    } else {
                        that.alertBox(true, 'danger', 'Please select some items');
                    }
                },
                move: function move() {
                    this.moveAction = true;
                },

                filterMoveNodes: function filterMoveNodes() {
                    var array = [];
                    for (var i = 0; i < this.moveNodes.length; i++) {
                        var add = true;
                        for (var x = 0; x < this.selectedItems.length; x++) {
                            if (this.moveNodes[i].id == this.selectedItems[x].id) {
                                add = false;
                            }
                        }
                        if (add) {
                            array.push(this.moveNodes[i]);
                        }
                    }
                    return array;
                },
                closeMoveActon: function closeMoveActon() {
                    this.moveAction = false;
                },
                sendNodes: function sendNodes() {
                    var that = this,
                        items = this.selectedItems;
                    this.$http.post('/admin/api/nodes/move', {
                        nodes: items,
                        newparent: that.moveParent,
                        parent: 0
                    }).then(function (response) {
                        that.nodes = response.data.nodes;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        this.moveAction = false;
                    }, function (response) {
                        this.renderError(response);
                    });
                },
                searchNodes: function searchNodes() {
                    var that = this;
                    if (this.searchTerm.length > 0) {
                        this.$http.post('/admin/api/nodes/search', {
                            searchterm: that.searchTerm
                        }).then(function (response) {
                            that.nodes = response.data.nodes;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                            that.order = false;
                        }, function (response) {
                            this.renderError(response);
                        });
                    } else {
                        this.$http.get('/admin/api/nodes/areas').then(function (response) {
                            that.nodes = response.data;
                            that.order = true;
                        }, function (response) {
                            this.renderError(response);
                        });
                    }
                }
            },
            components: {
                'v-select': window.vSelect,
            }
        });
    }

    if (typeof(contentShow) != 'undefined' && contentShow != null) {
        window.vmContentShow = new window.FinetuneVue({
            el: '#content',
            filters: {},
            data: {
                nodes: [],
                moveNodes: [],
                node: {type: '', blocks: [], fields: [], body: '', title: '', area_fk: 0, children: []},
                areaId: '',
                moveParent: 0,
                searchTerm: '',
                moveAction: false,
                canOrder: true,
                canMove: true,
                destroyBtn: true
            },
            ready: function () {
                var that = this;
                this.areaId = document.getElementById('area').value;
                this.$http.get('/admin/api/nodes/' + that.areaId).then(function (response) {
                    that.loading = false;
                    that.node = response.data;
                    if (that.node.type.date == 1) {
                        this.order = false;
                    }
                    that.nodes = that.node.children;
                }, function (response) {
                    that.loading = false;
                    this.renderError(response);
                });

                this.$http.get('/admin/api/nodes/movable').then(function (response) {

                    for (var i = 0; i < response.data.length; i++) {
                        that.moveNodes.push({
                            id: response.data[i].id,
                            title: response.data[i].title,
                            area: response.data[i].area,
                            type_id: response.data[i].type.id,
                            area_fk: response.data[i].area_fk
                        });
                    }
                    that.moveNodes.sort(function (a, b) {
                        var textA = a.title.toUpperCase();
                        var textB = b.title.toUpperCase();
                        return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
                    });

                    that.moveNodes.unshift({id: 0, title: "Top Level"})
                }, function (response) {
                    that.renderError(response);
                });
            },
            methods: {
                togglePublish: function (item) {
                    var that = this;
                    this.$http.post('/admin/api/nodes/publish', {
                        node: item, parent: that.node.id
                    }).then(function (response) {
                        that.nodes = response.data.nodes;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response) {
                        that.renderError(response);
                    });
                },
                orderUpdate: function (event) {
                    this.nodes.splice(event.newIndex, 0, this.nodes.splice(event.oldIndex, 1)[0])
                },
                orderSave: function (event) {
                    var that = this;
                    this.$http.post('/admin/api/nodes/order', {
                        nodes: this.nodes,
                        parent: that.node.id
                    }).then(function (response) {
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response) {
                        that.renderError(response);
                    });
                },
                destroy: function destroy(event) {
                    this.$broadcast('show::modal', 'showModelDelete');
                    this.event = event;
                },
                submitDestroy: function submitDestroy() {
                    var items = this.selectedItems;
                    this.$broadcast('hide::modal', 'showModelDelete');
                    var that = this;
                    if (items.length > 0) {
                        this.$http.delete('/admin/api/nodes/delete', {
                            nodes: items,
                            parent: this.areaId
                        }).then(function (response) {
                            that.nodes = response.data.nodes;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response) {
                            that.renderError(response);
                        });
                        this.selectedItems = [];
                    } else {
                        that.alertBox(true, 'danger', 'Please select some items');
                    }
                },
                move: function move() {
                    this.moveAction = true;

                },
                filterMoveNodes: function filterMoveNodes() {
                    var array = [];
                    for (var i = 0; i < this.moveNodes.length; i++) {
                        var add = true;
                        for (var x = 0; x < this.selectedItems.length; x++) {
                            if (this.moveNodes[i].id == this.selectedItems[x].id) {
                                add = false;
                            }
                        }
                        if (add) {
                            array.push(this.moveNodes[i]);
                        }
                    }
                    return array;
                },
                closeMoveActon: function closeMoveActon() {
                    this.moveAction = false;
                },
                sendNodes: function sendNodes() {
                    var that = this,
                        items = this.selectedItems;
                    this.$http.post('/admin/api/nodes/move', {
                        nodes: items,
                        newparent: that.moveParent,
                        parent: that.areaId
                    }).then(function (response) {
                        that.nodes = response.data.nodes;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        this.moveAction = false;
                    }, function (response) {
                        that.renderError(response);
                    });
                },
                searchNodes: function searchNodes() {
                    var that = this;
                    if (this.searchTerm.length > 0) {
                        this.$http.post('/admin/api/nodes/search', {
                            area: that.areaId,
                            searchterm: that.searchTerm
                        }).then(function (response) {
                            that.nodes = response.data.nodes;
                            that.order = false;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response) {
                            that.renderError(response);
                        });
                    } else {
                        this.$http.get('/admin/api/nodes/' + that.areaId).then(function (response) {
                            that.loading = false;
                            that.node = response.data;
                            that.nodes = that.node.children;
                            that.order = true;
                        }, function (response) {
                            that.renderError(response);
                        });
                    }
                }
            },
            components: {
                'v-select': window.vSelect,
            }
        });
    }

    if (typeof(contentUpdate) != 'undefined' && contentUpdate != null) {
        window.vmContent = new window.FinetuneVue({
            el: '#content',
            filters: {},
            data: {
                loadTime: 1000,
                previewObj: '',
                previewActive: false,
                previewStyle: {
                    width: '100%'
                },
                node: {
                    type: '',
                    blocks: [],
                    fields: [],
                    body: '',
                    title: '',
                    packages: [],
                    area_fk: 0,
                    media: {id: 0},
                    values: [],
                    tags: [],
                    exclude: ''
                },
                saving: false,
                blocks: [],
                blockId: '',
                types: [],
                nodeId: 0,
                parentId: 0,
                errors: [],
                customFields: [],
                links: [],
                media: {id: 0},
                allMedia: [],
                mediaId: '',


                images: [],
                imageId: '',

                folders: [],
                folderId: '',
                folder: {'tag': 'all', 'title': ' All Media'},

                files: [],
                fileId: '',

                searchTerm: '',
                starttime: '',
                endtime: '2016-01-19',
                testTime: '',
                multiTime: '',
                option: {
                    type: 'min',
                    week: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                    month: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    format: 'DD-MM-YYYY HH:mm',
                    placeholder: 'Publish on',
                    inputStyle: {
                        'width': '100%',
                        'display': 'block',
                        'height': '34px',
                        'padding': '6px 12px',
                        'font-size': '14px',
                        'line-height': '1.428571429',
                        'color': '#555',
                        'background-color': '#fff',
                        'background-image': 'none',
                        'border': '1px solid #ccc',
                        'border-radius': '0px',
                        'box-shadow': 'inset 0 1px 1px rgba(0, 0, 0, 0.075)',
                        '-webkit-transition': 'border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s',
                        'transition': 'border-color ease-in-out 0.15s, box-shadow ease-in-out 0.15s'
                    },
                    color: {
                        header: '#5bc0de',
                        headerText: '#FFF'
                    },
                    buttons: {
                        ok: 'Ok',
                        cancel: 'Cancel'
                    },
                    overlayOpacity: 0.5, // 0.5 as default
                    dismissible: true // as true as default
                },
                date: false,
                errorsShow: false,
                hasOrphans: false,
                hasTags: false,
                tags: [],
                block: {},
                orphanBlock: [],
                blockChange: {},
                packages: [],
                hasPackages: false,
            },

            ready: function () {
                var that = this;
                this.$http.get('/admin/api/types').then(function (response) {
                    that.types = response.data;
                }, function (response) {
                    this.renderError(response);
                });
                var nodeField = document.getElementById('nodeIdField');
                var parentField = document.getElementById('parentIdField');
                if (typeof(nodeField) != 'undefined' && nodeField != null) {
                    this.nodeId = document.getElementById('nodeIdField').value;
                    this.$http.get('/admin/api/nodes/' + that.nodeId).then(function (response) {
                        var moment = require('moment');
                        that.loading = false;
                        that.node = response.data;
                        that.node.area_fk = 0;
                        that.starttime = moment(that.node.publish_on).format('DD-MM-YYYY HH:mm');
                        that.node.type = response.data.type;
                        if (that.node.type.date == 1) {
                            this.date = true;
                        }
                        that.customFieldsBuild(that.node.type);
                        that.parentId = that.node.parent;
                        that.$http.post('/admin/api/packages', {'area': 'content-update', 'node': that.node}).then(function (response) {
                            that.packages = response.data;
                        }, function () {
                            that.alertBox(true, 'danger', 'The server responded with an error, please try again');
                        })
                    }, function (response) {
                        that.loading = false;
                        this.renderError(response);
                    });
                } else {
                    if (typeof(parentField) != 'undefined' && parentField != null) {
                        this.parentId = parentField.value;
                        this.$http.get('/admin/api/nodes/' + that.parentId).then(function (response) {
                            that.loading = false;
                            that.node.type = response.data.type;
                            if (that.node.type.date == 1) {
                                this.date = true;
                            }
                            if (response.data.area == 1) {
                                that.node.areaId = response.data.id;
                            } else {
                                that.node.areaId = response.data.area_node.id
                            }
                            that.node.parent = response.data.id;
                            that.customFieldsBuild(that.node.type);
                            that.$http.post('/admin/api/packages', {'area': 'content-update', 'node': that.node}).then(function (response) {
                                that.packages = response.data;
                            }, function () {
                                that.alertBox(true, 'danger', 'The server responded with an error, please try again');
                            })
                        }, function (response) {
                            that.loading = false;
                            this.renderError(response);
                        });
                    } else {
                        that.loading = false;
                        this.date = false;
                    }
                }
                this.getTags();
                this.$http.get('/admin/api/nodes/links').then(function (response) {
                    window.links = response.data;
                }, function (response) {
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
                    let that = this;
                    this.$http.get('/admin/api/folders').then(function (response) {
                        that.folders = response.data;
                        that.folders.unshift({'tag': 'all', 'title': ' All Media', 'id': 0})
                    }, function (response) {
                        this.renderError(response);
                    });
                },
                getMedia: function getMedia() {
                    let that = this;
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
                                    if(media.title != null){
                                        return media.title.indexOf(that.searchTerm) !== -1;
                                    }else{
                                        return false;
                                    }

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

                insert: function insert(itemId) {
                    if (this.blockId == 'body') {
                        for (var i = 0; i < this.media.length; i++) {
                            if (this.media[i].id == itemId) {
                                this.node.media = this.media[i];
                            }
                        }
                    } else {
                        for (var x = 0; x < this.blocks.length; x++) {
                            if (this.blocks[x].name == this.blockId) {
                                for (var i = 0; i < this.media.length; i++) {
                                    if (this.media[i].id == itemId) {
                                        this.blocks[x].media = this.media[i];
                                    }
                                }
                            }
                        }
                    }
                    window.vmContent.$set('blocks', this.blocks);
                    this.$broadcast('hide::modal', 'showModalImage')
                },
                nodeBuild: function nodeBuild() {
                    this.blocks = [];
                    if (this.node.media == null) {
                        this.node.media = {id: 0};
                    }
                    this.orphanBlock = [];
                    if (this.node.type != '') {
                        var blocks = this.node.type.blocks.split(':');
                        var i;
                        var x;
                        for (i = 0; i < blocks.length; i++) {
                            for (x = 0; x < this.node.blocks.length; x++) {
                                if (blocks[i] == this.node.blocks[x].name) {
                                    if (this.node.blocks[x].media == null) {
                                        this.node.blocks[x].media = {id: 0};
                                    }
                                    this.blocks.push(this.node.blocks[x]);
                                }
                            }
                        }
                        for (i = 0; i < blocks.length; i++) {
                            var inObject = false;
                            for (x = 0; x < this.blocks.length; x++) {
                                if (blocks[i] == this.blocks[x].name) {
                                    if (this.blocks[x].media == null) {
                                        this.blocks[x].media = {id: 0};
                                    }
                                    inObject = true;
                                }
                            }
                            if (!inObject) {
                                if (blocks[i].length > 0) {
                                    this.blocks.push({
                                        'name': blocks[i],
                                        'content': '',
                                        'media': {id: 0},
                                        'image': '',
                                        'title': ''
                                    })
                                }
                            }
                        }
                    }

                    for (x = 0; x < this.node.blocks.length; x++) {
                        var add = true;
                        for (i = 0; i < blocks.length; i++) {
                            if (blocks[i] == this.node.blocks[x].name) {
                                add = false;
                            }
                        }
                        if (add) {
                            this.orphanBlock.push(this.node.blocks[x]);
                            this.hasOrphans = true;
                        }

                    }

                },
                customFieldsBuild: function customFieldsBuild(type) {
                    var that = this;
                    this.$http.get('/admin/api/fields/' + type.id).then(function (response) {
                        that.loading = false;
                        var customFields = response.data;
                        for (var z = 0; z < customFields.length > 0; z++) {
                            customFields[z]['value'] = '';
                        }
                        for (var y = 0; y < customFields.length > 0; y++) {
                            for (var x = 0; x < that.node.values.length > 0; x++) {

                                if (that.node.values[x]['field_id'] == customFields[y]['id']) {
                                    if (customFields[y]['type'] == 'select') {
                                        if (customFields[y]['multiple'] == 1) {
                                            customFields[y]['value'] = [];
                                            customFields[y]['value'].push(this.getValue(this.splitter(customFields[y]['values']), that.node.values[x]['value']));
                                        } else {
                                            customFields[y]['value'] = this.getValue(this.splitter(customFields[y]['values']), that.node.values[x]['value'])

                                        }
                                    } else {
                                        customFields[y]['value'] = that.node.values[x]['value'];
                                    }

                                }
                            }
                        }
                        for (var w = 0; w < customFields.length > 0; w++) {
                            that.customFields.$set(w, customFields[w]);
                        }
                    }, function (response) {
                        this.renderError(response);
                    });
                },
                changeImage: function changeImage(blockId) {
                    this.blockId = blockId;
                    this.cleanErrors();
                    this.getFolders();
                    this.getMedia();
                    this.$broadcast('show::modal', 'showModalImage')
                },
                removeImage: function removeImage(block) {
                    if (block == 'body') {
                        this.node.media = {id: 0};
                    } else {
                        for (var x = 0; x < this.blocks.length; x++) {
                            if (this.blocks[x].name == block) {
                                this.blocks[x].media = {id: 0};
                            }
                        }
                    }
                },
                typeChange: function typeChange(val) {
                    if (typeof val === 'object') {
                        var i;
                        for (i = 0; i < this.blocks.length; i++) {
                            tinymce.remove('#' + this.blocks[i].name);
                        }
                        this.node.type = val;
                        if (this.node.type.date == 1) {
                            this.date = true;
                        }
                        this.nodeBuild();
                        this.customFieldsBuild(this.node.type);
                    }
                },
                saveContent: function saveContent(publish) {
                    this.saving = true;
                    this.node.blocks = this.blocks;
                    var that = this;
                    this.node.publish = publish;
                    this.node.area_fk = this.node.areaId;
                    this.node.parent = that.parentId;
                    this.node.fields = this.customFields;
                    this.node.publish_on = this.starttime;
                    this.node.packages = this.packages;

                    if (this.nodeId != 0) {
                        this.$http.put('/admin/api/nodes/' + this.nodeId, this.node).then(function (response) {
                            that.node = response.data.node;
                            that.nodeId = that.node.id;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                            that.saving = false;
                            setTimeout(this.endSaving, this.loadTime);
                        }, function (response) {
                            that.renderError(response);
                            setTimeout(this.endSaving, this.loadTime);
                        });
                    } else {
                        this.$http.post('/admin/api/nodes', this.node).then(function (response) {
                            that.node = response.data.node;
                            that.nodeId = that.node.id;
                            that.node.areaId = that.node.area_fk;
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                            setTimeout(this.endSaving, this.loadTime);
                        }, function (response) {
                            that.renderError(response);
                            setTimeout(this.endSaving, this.loadTime);
                        });
                    }
                },
                mergeBlock: function mergeBlock(block) {
                    for (var x = 0; x < this.blocks.length; x++) {
                        if (this.blocks[x].name == this.blockChange.name) {
                            this.blocks[x].title = block.title;
                            this.blocks[x].content = this.blocks[x].content + ' ' + block.content;
                            this.blocks[x].image = block.image;
                            this.blocks[x].media = block.media;
                        }
                    }
                },
                getTags: function getTags() {
                    let that = this;
                    this.$http.get('/admin/api/tags').then(function (response) {
                        that.tags = response.data;
                        if (that.tags.length > 0) {
                            that.hasTags = true;
                        }
                    }, function (response) {
                        this.renderError(response);
                    });
                },
                tagsChange: function tagsChange(val) {
                    this.node.tags = val;
                },
                mergeChange: function mergeChange(val) {
                    if (typeof val === 'object') {
                        this.blockChange = val;
                    }
                },
                destroyOrphan: function destroyOrphan(block) {
                    this.block = block;
                    this.$broadcast('show::modal', 'showModelDelete');
                },
                submitDestroy: function submitDestroy() {
                    var that = this;
                    var type = this.node.type;
                    this.$broadcast('hide::modal', 'showModelDelete');
                    this.saving = true;
                    this.$http.delete('/admin/api/blocks/' + this.node.id + '/' + this.block.id).then(function (response) {
                        that.node = response.data;
                        that.node.type = type;
                        that.nodeBuild();
                        that.alertBox(true, 'success', 'Orphan Deleted');
                        setTimeout(this.endSaving, this.loadTime);
                    }, function (response) {
                        that.renderError(response);
                        setTimeout(this.endSaving, this.loadTime);
                    })

                },
                endSaving: function endSaving() {
                    this.saving = false;
                },
                preview: function preview() {
                    if (this.previewActive) {
                        this.previewActive = false;
                    } else {
                        this.previewActive = true;
                        var that = this;
                        this.$http.put('/admin/api/preview/' + this.nodeId, this.node).then(function (response) {
                            that.previewObj = response.data;
                            var doc = document.getElementById('previewFrame').contentWindow.document;
                            doc.open();
                            doc.write(that.previewObj);
                            doc.close();

                        }, function (response) {
                            that.renderError(response);
                            setTimeout(this.endSaving, this.loadTime);
                        });
                    }
                },
                changePreview: function changePreview(viewport) {
                    if (viewport == 'mobile') {
                        this.previewStyle.width = '320px';
                    }
                    if (viewport == 'tablet') {
                        this.previewStyle.width = '600px';
                    }
                    if (viewport == 'desktop') {
                        this.previewStyle.width = '1024px';
                    }
                    if (viewport == 'laptop') {
                        this.previewStyle.width = '1366px';
                    }
                    if (viewport == 'desktop-xl') {
                        this.previewStyle.width = '1600px';
                    }
                }
            },
            components: {
                // Vue strap components that we are loading in
                'tabs': window.VueStrap.tabs,
                'tab': window.VueStrap.tab,
                'v-select': window.vSelect,
                'date-picker': window.datePicker
            }
        });
    }

}