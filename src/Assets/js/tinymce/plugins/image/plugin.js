/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2015 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */





tinymce.PluginManager.add('image', function (editor) {

    function imageString(media){
        var string = '';
        $.each(media, function(key, value){
            if(value.type == 'image'){
                string = string + '<a class="imageBlock" href="#" id="'+value.id+'"><div class="inner">';
                string = string +  '<img data-original="'+value.thumb+'" alt="'+value.title+'" id="'+value.id+'"/>';
                if(value.title != '' && value.title != null){
                    string = string + '<span>' + truncate(value.title, 20) + '</span>';
                }else{
                    string = string + '<span>' + truncate(value.filename, 20) + '</span>';
                }
                string = string + '<div class="mce-btn mce-primary image-insert">Insert</div></div></a>';
            }
        })
        return string;
    }

    function truncate(string, length){

        var newString = string.substring(0,length);
        if(string != newString){
            newString = newString + '...';
        }
        return newString;
    }

    function lazyLoad(){
        var imageManager = document.getElementById('imageManager');
        if(typeof(imageManager) != 'undefined' && imageManager != null){
            window.myLazyLoad = new LazyLoad({
                container: imageManager,
                show_while_loading: true,
            });
        }
    }

    function insertImage(image){
        var string = '<img src="'+image.external+'/600" alt="'+image.title+'" id="'+image.id+'"/>'
        return string;
    }


    var templateImage = '<div class="form-group"><label for="folders">Folders</label>' +
        '<select name="folders" id="folders" class="form-control"></select></div>' +
        '<div class="form-group searchterm">' +
        '<input type="text" name="searchterm" id="searchTerm" placeholder="Search" class="form-control"/></div>' +
        '<div id="imageManager" class="image-manager"></div>';

    function InsertImage() {
        var getJSON = function (url, callback) {
            var xhr = new XMLHttpRequest();
            xhr.open("get", url, true);
            xhr.responseType = "json";
            xhr.onload = function () {
                var status = xhr.status;
                if (status == 200) {
                    callback(null, xhr.response);
                } else {
                    callback(status);
                }
            };
            xhr.send();
        };
        var media = [];
        var image = {};
        var folder = {};
        var folders = [];

        getJSON("/admin/api/folders", function (err, data) {

            if (err != null) {
                alert("Something went wrong: " + err);
            } else {
                var object = {};
                for (var i = 0; i < data.length; i++) {
                    object = {title: data[i].title, tag: data[i].tag, media: data[i].media}
                    folders.push(object);
                }
                folders.unshift({tag: 'all', title: 'All', media: []})
                editor.windowManager.open({
                    title: 'Insert Image',
                    width: 700,
                    height: 500,
                    html: templateImage
                });
                for (var i = 0; i < folders.length; i++) {
                    $('#folders').append($('<option>', {value: folders[i].tag, text: folders[i].title}));
                }
                $('.chosen').chosen("destroy");
                $(".chosen").chosen();
                $('#folders').chosen().change(function () {
                    var tag = $(this).val();
                    $('#imageManager').html('');
                    if (tag == 'all') {
                        getJSON("/admin/api/media", function (err, data) {
                            if (err != null) {
                                alert("Something went wrong: " + err);
                            } else {
                                media = data;
                                $('#imageManager').html(imageString(media));
                                lazyLoad();
                                $('#imageManager a').unbind();
                                $('#imageManager a').click(function () {
                                    var id = $(this).attr('id');
                                    for (var x = 0; x < media.length; x++) {
                                        if (media[x].id == id) {
                                            var string = insertImage(media[x]);
                                            editor.execCommand('mceInsertContent', false, string);
                                            editor.windowManager.close();
                                        }
                                    }
                                });
                            }
                        });
                    } else {
                        for (var i = 0; i < folders.length; i++) {

                            if (folders[i].tag == tag) {
                                media = folders[i].media;
                                $('#imageManager').html(imageString(media));
                                lazyLoad();
                                $('#imageManager a').unbind();
                                $('#imageManager a').click(function () {
                                    var id = $(this).attr('id');
                                    for (var x = 0; x < media.length; x++) {
                                        if (media[x].id == id) {
                                            var string = insertImage(media[x]);
                                            editor.execCommand('mceInsertContent', false, string);
                                            editor.windowManager.close();
                                        }
                                    }
                                });
                            }
                        }
                    }
                });

                $('#searchTerm').keyup(function () {
                    var searchTerm = $(this).val();
                    $('#imageManager').html('');
                    getJSON("/admin/api/media", function (err, data) {
                        if (err != null) {
                            alert("Something went wrong: " + err);
                        } else {
                            media = [];
                            for (var i = 0; i < data.length; i++) {
                                if(data[i].title != null){
                                    if (data[i].title.toLowerCase().indexOf(searchTerm.toLowerCase()) >= 0) {
                                        media.push(data[i]);
                                    } else {
                                        if (data[i].filename.toLowerCase().indexOf(searchTerm.toLowerCase()) >= 0) {
                                            media.push(data[i]);
                                        }
                                    }
                                }else{
                                    if (data[i].filename.toLowerCase().indexOf(searchTerm.toLowerCase()) >= 0) {
                                        media.push(data[i]);
                                    }
                                }
                            }
                            $('#imageManager').html(imageString(media));
                            lazyLoad();
                            $('#imageManager a').unbind();
                            $('#imageManager a').click(function () {
                                var id = $(this).attr('id');
                                for (var x = 0; x < media.length; x++) {
                                    if (media[x].id == id) {
                                        var string = insertImage(media[x]);
                                        editor.execCommand('mceInsertContent', false, string);
                                        editor.windowManager.close();
                                    }
                                }
                            });
                        }
                    });
                });

                getJSON("/admin/api/media", function (err, data) {
                    media = [];
                    if (err != null) {
                        alert("Something went wrong: " + err);
                    } else {
                        media = data;
                        $('#imageManager').html(imageString(media));
                        lazyLoad();
                        $('#imageManager a').unbind();
                        $('#imageManager a').click(function () {
                            var id = $(this).attr('id');
                            for (var x = 0; x < media.length; x++) {
                                if (media[x].id == id) {
                                    var string = insertImage(media[x]);
                                    editor.execCommand('mceInsertContent', false, string);
                                    editor.windowManager.close();
                                }
                            }
                        });
                    }
                });
            }
        });
    }

    editor.addMenuItem('image', {
        icon: 'image',
        text: 'Insert Image',
        onclick: InsertImage,
        context: 'insert',
        prependToContext: true
    });
});
