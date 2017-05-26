tinymce.PluginManager.add('gallery', function (editor) {
    editor.addCommand('InsertGallery', function () {
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
        var tag = '';
        var type = 'grid';
        var galleryTemplate = '<div class="form-group"><label for="groupsGallery">Group</label>' +
            '<select name="groups" id="groupsGallery" class="form-control chosen"><option value="">Please select a group</option></select>' +
            '<label for="types">Type</label>' +
            '<select name="types" id="types" class="form-control"><option value="grid">Grid</option><option value="scroll">Scroll</option> </select>' +
            '</div>';

        editor.windowManager.open({
            title: 'Insert Gallery',
            height: 200,
            width: 450,
            html: galleryTemplate,
            buttons: [{
                text: 'Submit',
                classes: 'btn primary',
                onclick: 'submit'
            }, {
                text: 'Cancel',
                onclick: 'close'
            }],
            onsubmit: function (e) {
                console.log('submitted:');
                console.log(tag);
                if (tag.length > 0) {
                    var string = '@gallery("' + tag + ','+type+'")';
                    editor.execCommand('mceInsertContent', false, string);
                }

            }
        });
        getJSON("/admin/api/folders", function (err, data) {
            if (err != null) {
                alert("Something went wrong: " + err);
            } else {
                for (var i = 0; i < data.length; i++) {
                    $('#groupsGallery').append($('<option>', {value:data[i].tag, text:data[i].title}));
                }
            }
            $('.chosen').chosen("destroy");
            $(".chosen").chosen();
            $('#groupsGallery').chosen().change(function(){
                console.log($(this).val());
                tag = $(this).val();
            });
            $('#types').change(function(){
                type = $(this).val();
            })
        });
    });

    editor.addButton('gallery', {
        icon: 'fa-camera-retro',
        tooltip: 'Insert Gallery',
        cmd: 'InsertGallery'
    });

    editor.addMenuItem('gallery', {
        icon: 'fa-camera-retro',
        text: 'Horizontal line',
        cmd: 'InsertGallery',
        context: 'insert'
    });
});
