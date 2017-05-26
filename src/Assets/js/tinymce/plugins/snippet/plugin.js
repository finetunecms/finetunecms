tinymce.PluginManager.add('snippet', function (editor) {
    editor.addCommand('InsertSnippet', function () {
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
        var snippet = '';
        var group = '';
        var snippetTemplate = '<div class="form-group"><label for="snippetGroups">Snippet Group</label>' +
            '<select name="snippetGroups" id="snippetGroups" class="form-control chosen"><option value="">Please select a snippet group</option></select></div>' +
            '<div class="form-group"><label for="snippet">Snippet</label>' +
            '<select name="snippet" id="snippet" class="form-control chosen"><option value="">Please select a snippet</option></select></div>';
        editor.windowManager.open({
            title: 'Insert Group or Snippet',
            height: 200,
            width: 600,

            html: snippetTemplate,
            buttons: [{
                text: 'Submit',
                classes: 'btn primary',
                onclick: 'submit'
            }, {
                text: 'Cancel',
                onclick: 'close'
            }],
            onsubmit: function (e) {
                var string = '';

                if (group.length > 0) {
                    string = '@group("' + group + '")';
                }
                if (snippet.length > 0) {
                    string = '@snippet("' + snippet + '")';
                }
                if (string.length > 0) {
                    editor.execCommand('mceInsertContent', false, string);
                }
            }
        });
        getJSON("/admin/api/snippetgroups", function (err, data) {
            if (err != null) {
                alert("Something went wrong: " + err);
            } else {
                for (var i = 0; i < data.length; i++) {
                    $('#snippetGroups').append($('<option>', {text: data[i].title, value: data[i].tag}));
                    for (var j = 0; j < data[i].snippet.length; j++) {
                        $('#snippet').append($('<option>', {text: data[i].snippet[j].title, value: data[i].snippet[j].tag}));
                    }
                }
                $('.chosen').chosen("destroy");
                $(".chosen").chosen();
            }
            $('#snippetGroups').change(function(){
                group = $(this).val();
            })
            $('#snippet').change(function(){
                snippet = $(this).val();
            })


        });
    });

    editor.addButton('snippet', {
        icon: 'fa-plus-square-o',
        tooltip: 'Insert Snippet',
        cmd: 'InsertSnippet'
    });

    editor.addMenuItem('Snippet', {
        icon: 'fa-plus-square-o',
        text: 'Horizontal line',
        cmd: 'InsertSnippet',
        context: 'insert'
    });
});
