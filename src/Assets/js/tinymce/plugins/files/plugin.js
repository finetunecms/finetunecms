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


function fileString(media){
    var string = '<table class="table" style="width:100%;float:left;"><thead><tr><th>Name</th><th>Actions</th></tr></thead><tbody>';
    if(media.length > 0){
        $.each(media, function(key, value){
            if(value.type == 'file'){
                string = string + '<tr class="file-row">'
                if(value.title != ''){
                    string = string + '<td>' + truncate(value.title, 20) + '</td>';
                }else{
                    string = string + '<td>' + truncate(value.filename, 20) + '</td>';
                }
                string = string + '<td><button class="file" id="'+ value.id + '">Add</button>';
                string = string + '</tr>';
            }
        })
    }else{
        string = string + '<tr><td colspan="2">No Files Found</td></tr>'
    }
    string = string + '</tbody></table>';
    return string;
}

function truncate(string, length){
    var newString = string.substring(0,length);
    if(string != newString){
        newString = newString + '...';
    }
    return newString;
}


function insertFile(file){
    var string = '';
    if(file.title != ''){
        string = string + '<a href="'+file.external+'">' + truncate(file.title, 20) + '</a>';
    }else{
        string = string + '<a href="'+file.external+'">' + truncate(file.filename, 20) + '</a>';
    }
    return string;
}


var template= '<div class="form-group"><label for="folders">Folders</label>' +
    '<select name="folders" id="folders" class="form-control chosen"></select></div>' +
    '<div class="form-group"><label for="searchTerm">Search</label>' +
    '<input type="text" name="searchterm" id="searchTerm" class="form-control"/></div>' +
    '<div id="fileManager" class="file-manager"></div>';


tinymce.PluginManager.add('files', function (editor) {

    editor.addCommand('InsertFile', function () {
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
        var file = {};
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
                    title: 'Insert File',
                    width: 700,
                    height: 500,
                    html: template
                });
                for(var i = 0; i < folders.length; i++){
                    $('#folders').append($('<option>', {value:folders[i].tag, text:folders[i].title}));
                }
                $('.chosen').chosen("destroy");
                $(".chosen").chosen();
                $('#folders').chosen().change(function(){
                    var tag = $(this).val();
                    $('#fileManager').html('');
                    if(tag == 'all'){
                        getJSON("/admin/api/media", function (err, data) {
                            if (err != null) {
                                alert("Something went wrong: " + err);
                            } else {
                                media = data;
                                $('#fileManager').html(fileString(media));
                                $('#fileManager .file').unbind();
                                $('#fileManager .file').click(function(){
                                    var id = $(this).attr('id');
                                    for(var x = 0; x < media.length; x++){
                                        if(media[x].id == id){
                                            var string = insertFile(media[x]);
                                            editor.execCommand('mceInsertContent', false, string);
                                            editor.windowManager.close();
                                        }
                                    }
                                });
                            }
                        });
                    }else{
                        for(var i = 0; i < folders.length; i++){

                            if(folders[i].tag == tag){
                                media = folders[i].media;
                                $('#fileManager').html(fileString(media));
                                $('#fileManager .file').unbind();
                                $('#fileManager .file').click(function(){
                                    var id = $(this).attr('id');
                                    for(var x = 0; x < media.length; x++){
                                        if(media[x].id == id){
                                            var string = insertFile(media[x]);
                                            editor.execCommand('mceInsertContent', false, string);
                                            editor.windowManager.close();
                                        }
                                    }
                                });
                            }
                        }
                    }
                });

                $('#searchTerm').keyup(function(){
                    var searchTerm = $(this).val();
                    $('#fileManager').html('');
                    getJSON("/admin/api/media", function (err, data) {
                        if (err != null) {
                            alert("Something went wrong: " + err);
                        } else {
                            media = [];
                            for(var i=0;i < data.length; i++){
                                if(data[i].title.toLowerCase().indexOf(searchTerm.toLowerCase()) >= 0){
                                    media.push(data[i]);
                                }else{
                                    if(data[i].filename.toLowerCase().indexOf(searchTerm.toLowerCase()) >= 0){
                                        media.push(data[i]);
                                    }
                                }

                            }
                            $('#fileManager').html(fileString(media));
                            $('#fileManager .file').unbind();
                            $('#fileManager .file').click(function(){
                                var id = $(this).attr('id');
                                for(var x = 0; x < media.length; x++){
                                    if(media[x].id == id){
                                        var string = insertFile(media[x]);
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
                        $('#fileManager').html(fileString(media));
                        $('.chosen').chosen("destroy");
                        $(".chosen").chosen();
                        $('#fileManager .file').unbind();
                        $('#fileManager .file').click(function(){
                            var id = $(this).attr('id');
                            console.log(id);
                            for(var x = 0; x < media.length; x++){
                                if(media[x].id == id){
                                    var string = insertFile(media[x]);
                                    editor.execCommand('mceInsertContent', false, string);
                                    editor.windowManager.close();
                                }
                            }
                        });
                    }
                });
            }
        });
    });
    editor.addButton('file', {
        icon: 'fa-file',
        tooltip: 'Insert File',
        cmd: 'InsertFile'
    });

});
