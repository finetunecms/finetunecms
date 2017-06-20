tinymce.PluginManager.add('filebank', function (editor) {
    editor.addCommand('InsertFileBank', function () {
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
        var filebankTemplate = '<div class="form-group"><label for="groups">Group</label>' +
            '<select name="groups" id="groups" class="form-control"><option value="">Please select a group</option></select></div>';
        editor.windowManager.open({
            title: 'Insert File Group',
            height: 100,
            width: 450,
            html: filebankTemplate,
            buttons: [{
                text: 'Submit',
                classes: 'btn primary',
                onclick: 'submit'
            }, {
                text: 'Cancel',
                onclick: 'close'
            }],
            onsubmit: function (e) {
                if (tag.length > 0) {
                    var string = '@filebank("' + tag + '")';
                    editor.execCommand('mceInsertContent', false, string);
                }
            }
        });
        getJSON("/admin/api/folders", function (err, data) {
            if (err != null) {
                alert("Something went wrong: " + err);
            } else {
                for (var i = 0; i < data.length; i++) {
                    $('#groups').append($('<option>', {value:data[i].tag, text:data[i].title}));
                }
            }
            $('#groups').change(function(){
                tag = $(this).val();
            })
        });
    });

    editor.addButton('filebank', {
        icon: 'fa-files-o',
        tooltip: 'Insert File Group',
        cmd: 'InsertFileBank'
    });

    editor.addMenuItem('filebank', {
        icon: 'fa-files-o',
        text: 'Horizontal line',
        cmd: 'InsertFileBank',
        context: 'insert'
    });
});
