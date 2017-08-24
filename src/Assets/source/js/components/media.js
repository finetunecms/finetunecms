 var cropper = document.getElementById('cropper');
    if(typeof(cropper) != 'undefined' && cropper != null){
        require('./cropper.js');
    }else {
        var media = document.getElementById('media');
        if (typeof(media) != 'undefined' && media != null) {
            window.vmMedia = new window.FinetuneVue({
                el: '#media',
                filters: {},
                data: {
                    uploadFiles: [],
                    selectedItems: [],
                    uploader: false,
                    media: [],
                    mediaAll: [],
                    mediaItem: {'title': '', 'filename': ''},
                    showUpdate: true,
                    showNodes: false,
                    searchTerm: '',
                    folders: [],
                    folder: {'tag': 'all', 'title': ' All Media'},
                    folderUpdate: {'tag': '', 'title': ''},
                    folderId: '',

                    foldersSelected: [],
                    filter: 'All',
                    filterLabel: 'Filter',
                    accept: 'image/jpeg,image/gif,image/jpg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    size: 1024 * 1024 * 10,
                    multiple: true,
                    extensions: 'gif,jpg,png,pdf,doc,docx,jpeg',
                    file: {'title': '', 'filename': ''},
                    files: [],
                    upload: {},
                    drop: true,
                    auto: true,
                    loading: true
                },
                compiled() {
                    this.upload = this.$refs.upload;
                    this.upload.request = {
                        headers: {
                            "X-Csrf-Token": document.querySelector('#token').getAttribute('value'),
                        },
                        data: {
                            "csrf_token": document.querySelector('#token').getAttribute('value'),
                        },
                    };
                    this.files = this.$refs.upload.files;
                },
                ready: function () {
                    this.getOptions();
                    this.getMedia();
                    this.getFolders();

                },
                methods: {
                    getOptions: function getOptions(){
                        let that = this;
                        this.$http.get('/admin/api/media/options').then(function (response) {
                            that.size = response.data.size;
                            that.accept = response.data.accept;
                            that.extensions = response.data.extensions;
                        }, function (response){
                            this.renderError(response);
                        });
                    },
                    getMedia: function getMedia() {
                        let that = this;
                        this.media = [];
                        this.mediaAll = [];
                        this.$http.get('/admin/api/media').then(function (response) {
                            that.mediaAll = response.data;
                            that.loading = false;
                            that.changeFolder();
                        }, function (response){
                            this.renderError(response);
                        });
                    },

                    selectMedia: function selectImage(image) {
                        this.mediaItem = image;
                    },
                    saveMedia: function saveMedia(){
                        var that = this;
                        this.$http.put('/admin/api/media/' + that.mediaItem.id, {
                            media: that.mediaItem
                        }).then(function (response) {
                            that.getMedia();
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response){
                            this.renderError(response);
                        });
                    },

                    selected: function selected(id) {
                        var index = this.selectedItems.indexOf(id);
                        if (index > -1) {
                            this.selectedItems.$set(index, '');
                            this.selectedItems.$remove(index);
                        } else {
                            this.selectedItems.push(id);
                        }
                    },
                    search: function search() {
                        var that = this;
                        var items = [];

                        if (this.searchTerm.length === 0) {
                            items = this.media;
                        } else {
                            items = this.mediaAll.filter(function (media) {
                                if (media.filename.toLowerCase().indexOf(that.searchTerm.toLowerCase()) !== -1) {
                                    return true;
                                } else {
                                    if(media.title){
                                        return media.title.toLowerCase().indexOf(that.searchTerm.toLowerCase()) !== -1;
                                    }else{
                                        return false;
                                    }
                                }
                            });
                        }
                        if (this.filter == 'All') {
                            return items;
                        } else {
                            if (this.filter == 'Images') {
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

                    getFolders: function getFolders() {
                        let that = this;
                        this.$http.get('/admin/api/folders').then(function (response) {
                            that.folders = response.data;
                        }, function (response){
                            this.renderError(response);
                        });
                    },
                    getSelectFolders: function getSelectFolders() {
                        var folders = [];

                        for (var i = 0; i < this.folders.length; i++) {
                            if (this.folders[i].id != this.folder.id) {
                                folders.push(this.folders[i]);
                            }
                        }

                        if (this.folder.tag != 'all') {
                            folders.unshift({'tag': 'all', 'title': ' All Media'})
                        }

                        return folders;
                    },
                    addFolder: function addFolder(type) {
                        if (type == 'edit') {
                            this.folderUpdate = this.folder;
                        } else {
                            this.folderUpdate = {'tag': '', 'title': '', 'id': 0};
                        }
                        this.$broadcast('show::modal', 'showModalAddFolder');
                    },
                    submitFolder: function submitFolder() {
                        var that = this;
                        this.cleanErrors();
                        if (that.folderId > 0) {
                            this.$http.put('/admin/api/folders/' + that.folderId, this.folderUpdate).then(function (response) {
                                that.folders = response.data.folders;
                                that.clearFolder();
                                this.$broadcast('hide::modal', 'showModalAddFolder');
                                that.alertBox(true, response.data.alertType, response.data.alertMessage);
                            }, function (response){
                                this.renderError(response);
                            });
                        } else {
                            this.$http.post('/admin/api/folders', this.folderUpdate).then(function (response) {
                                that.folders = response.data.folders;
                                that.clearFolder();
                                this.$broadcast('hide::modal', 'showModalAddFolder');
                                that.alertBox(true, response.data.alertType, response.data.alertMessage);
                            }, function (response){
                                this.renderError(response);
                            });
                        }
                    },
                    clearFolder: function clearFolder() {
                        this.folder = {'tag': 'all', 'title': ' All Media', 'id': 0};
                        this.folderUpdate = {'tag': '', 'title': '', 'id': 0};
                        this.folderId = 0;
                        this.cleanErrors();
                        this.clearItems();
                    },

                    moveMedia: function moveMedia() {
                        this.$broadcast('show::modal', 'showModalMoveFiles');
                    },
                    addMediaFolder: function addMediaFolder() {
                        this.$broadcast('hide::modal', 'showModelDelete');
                        var that = this;
                        if (this.selectedItems.length > 0) {
                            this.$http.post('/admin/api/media/move', {
                                media: that.selectedItems,
                                folders: that.foldersSelected,
                            }).then(function (response) {
                                that.getMedia();
                                that.alertBox(true, response.data.alertType, response.data.alertMessage);
                            }, function (response){
                                this.renderError(response);
                            });
                            this.selectedItems = [];
                        } else {
                            that.alertBox(true, 'danger', 'Please make sure you have selected some items');
                        }
                    },
                    changeFolder: function changeFolder() {
                        if (this.folder != null) {
                            if (this.folder.tag == 'all') {
                                this.folderId = '';
                                this.media = this.mediaAll;
                                this.mediaItem = this.media[0];
                            } else {
                                this.folderId = this.folder.id;
                                this.media = [];
                                for (var i = 0; i < this.mediaAll.length; i++) {
                                    for (var x = 0; x < this.mediaAll[i].folders.length; x++) {
                                        if (this.mediaAll[i].folders[x].id == this.folderId) {
                                            this.media.push(this.mediaAll[i])
                                        }
                                    }
                                }
                                function compare(a,b) {
                                    if (a.order < b.order)
                                        return -1;
                                    if (a.order > b.order)
                                        return 1;
                                    return 0;
                                }
                                this.media = this.media.sort(compare);
                                this.mediaItem = this.media[0];
                            }
                        } else {
                            this.media = this.mediaAll;
                            this.mediaItem = this.media[0];
                            this.folder = {'tag': 'all', 'title': ' All Media', 'id': 0};
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
                        this.$http.delete('/admin/api/media/destroy', {media: items}).then(function (response) {
                            that.destroyModal = false;
                            that.getMedia();
                            this.cleanErrors();
                            this.clearItems();
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response){
                            this.renderError(response);
                        });
                    },
                    orderUpdate: function (event) {
                        this.media.splice(event.newIndex, 0, this.media.splice(event.oldIndex, 1)[0])
                    },
                    orderSave: function (event) {
                        let that = this;
                        this.$http.post('/admin/api/media/order', {
                            media: this.media,
                            folder: that.folderId
                        }).then(function (response) {
                            that.alertBox(true, response.data.alertType, response.data.alertMessage);
                        }, function (response){
                            this.renderError(response);
                        });
                    },

                    // Uploader
                    uploadMedia: function uploadMedia() {
                        this.uploads = [];
                        this.uploader = !this.uploader;
                    },
                    filterLoad: function filterLoad(filter){
                        this.filter = filter.filtertag;
                        this.filterLabel = filter.filterlabel;
                    },
                    renderImage: function renderImage(id, file) {
                        if (file.errno.length > 0) {
                            return null;
                        } else {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                document.getElementById(id).setAttribute('src', e.target.result);
                            };
                            reader.readAsDataURL(file);
                        }

                    },

                    // CRUD
                    remove: function remove(file) {
                        this.$refs.upload.files.$remove(file);
                    },
                },
                events: {
                    addFileUpload(file, component) {
                        this.files = this.$refs.upload.files;
                        if (this.auto) {
                            component.active = true;
                        }
                        this.uploader = true;
                    },
                    fileUploadProgress(file, component) {

                    },
                    afterFileUpload(file, component) {
                        var that = this;
                        for (var x = 0; x < this.files.length; x++) {
                            if (this.files[x].id == file.id) {
                                this.files.splice(x, 1);
                            }
                        }
                        if(that.folder.tag != "all"){
                            this.$http.post('/admin/api/media/move', {
                                media: [file.data.media],
                                folders: [that.folder],
                            }).then(function (response) {
                                that.alertBox(true, response.data.alertType, response.data.alertMessage);
                                if (this.files.length == 0) {
                                    this.getMedia();
                                }
                            }, function (response){
                                this.renderError(response);
                            });
                        }else{
                            if (this.files.length == 0) {
                                this.getMedia();
                            }
                        }

                    },
                    beforeFileUpload(file, component) {

                    },

                },
                components: {
                    FileUpload: window.FileUpload,
                    'tabs': window.VueStrap.tabs,
                    'tab': window.VueStrap.tab,
                    'vs-progress': window.VueStrap.progress,
                    'dropdown': window.VueStrap.dropdown
                }
            });
        }

    }