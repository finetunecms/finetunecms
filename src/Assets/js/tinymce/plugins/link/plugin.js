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

tinymce.PluginManager.add('link', function (editor) {
    function createLinkList(callback) {
        return function () {
            var linkObj = window.links;
            var linkList = [];

            if (typeof(linkObj) != 'undefined' && linkObj != null) {
                linkObj.forEach(function (element, index) {
                    var json = {title: element.title + ' - ' + element.url_slug, value: element.url_slug};
                    linkList.push(json);
                });
            }
            if (typeof linkList == "string") {
                tinymce.util.XHR.send({
                    url: linkList,
                    success: function (text) {
                        callback(tinymce.util.JSON.parse(text));
                    }
                });
            } else {
                callback(linkList);
            }
        };
    }

    function showDialog() {
        var data = {}, selection = editor.selection, dom = editor.dom, selectedElm, anchorElm, initialText;
        var win, onlyText, value;
        var page = '';
        var url = '';
        var title = '';
        var display = '';
        var target = '';

        selectedElm = selection.getNode();
        anchorElm = dom.getParent(selectedElm, 'a[href]');

        function isOnlyTextSelected() {

            var html = selection.getContent();

            // Partial html and not a fully selected anchor element
            if (/</.test(html) && (!/^<a [^>]+>[^<]+<\/a>$/.test(html) || html.indexOf('href=') == -1)) {
                return false;
            }

            if (anchorElm) {
                var nodes = anchorElm.childNodes, i;

                if (nodes.length === 0) {
                    return false;
                }

                for (i = nodes.length - 1; i >= 0; i--) {
                    if (nodes[i].nodeType != 3) {
                        return false;
                    }
                }
            }

            return true;
        }

        onlyText = isOnlyTextSelected();

        var linkTemplate = '<div class="form-group"><label for="pages">Page</label><select id="pages" class="form-control chosen"><option value="">Please select a page</option></select></div>' +
            '<div class="form-group"><label for="url">URL</label><input type="text" id="url" placeholder="Url" class="form-control"/></div>';

        if (onlyText) {
            linkTemplate = linkTemplate + '<div class="form-group"><label for="display">Display</label><input type="text" id="display" placeholder="Display" class="form-control"/></div>';
        }
        linkTemplate = linkTemplate + '<div class="form-group"><label for="title">Title</label><input type="text" id="title" placeholder="Title" class="form-control"/></div>' +
            '<div class="form-group"><label for="target">Target</label><select id="target" class="form-control"><option value="">Normal Link</option><option value="_blank">New Tab</option></select></div>' +
            '</div>';

        editor.windowManager.open({
            title: 'Insert link',
            height: 400,
            html: linkTemplate,
            buttons: [{
                text: 'Submit',
                classes: 'btn primary',
                onclick: 'submit'
            }, {
                text: 'Cancel',
                onclick: 'close'
            }],
            onSubmit: function (e) {
                /*eslint dot-notation: 0*/
                var href;

                href = url;

                // Delay confirm since onSubmit will move focus
                function delayedConfirm(message, callback) {
                    var rng = editor.selection.getRng();
                    tinymce.util.Delay.setEditorTimeout(editor, function () {
                        editor.windowManager.confirm(message, function (state) {
                            editor.selection.setRng(rng);
                            callback(state);
                        });
                    });
                }

                function createLink() {
                    var linkAttrs = {
                        href: href,
                        target: target ? target : '',
                        "class": data["class"] ? data["class"] : '',
                        title: title ? title : ''
                    };

                    if (anchorElm) {
                        editor.focus();

                        if (onlyText && display != initialText) {
                            if ("innerText" in anchorElm) {
                                anchorElm.innerText = display;
                            } else {
                                anchorElm.textContent = display;
                            }
                        }
                        dom.setAttribs(anchorElm, linkAttrs);

                        selection.select(anchorElm);
                        editor.undoManager.add();
                    } else {
                        if (onlyText) {
                            editor.insertContent(dom.createHTML('a', linkAttrs, dom.encode(display)));
                        } else {
                            editor.execCommand('mceInsertLink', false, linkAttrs);
                        }
                    }
                }

                function insertLink() {
                    editor.undoManager.transact(createLink);
                }


                if (!href) {
                    editor.execCommand('unlink');
                    return;
                }

                // Is email and not //user@domain.com
                if (href.indexOf('@') > 0 && href.indexOf('//') == -1 && href.indexOf('mailto:') == -1) {
                    delayedConfirm(
                        'The URL you entered seems to be an email address. Do you want to add the required mailto: prefix?',
                        function (state) {
                            if (state) {
                                href = 'mailto:' + href;
                            }
                            insertLink();
                        }
                    );

                    return;
                }

                // Is not protocol prefixed
                if ((editor.settings.link_assume_external_targets && !/^\w+:/i.test(href)) ||
                    (!editor.settings.link_assume_external_targets && /^\s*www[\.|\d\.]/i.test(href))) {
                    delayedConfirm(
                        'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?',
                        function (state) {
                            if (state) {
                                href = 'http://' + href;
                            }

                            insertLink();
                        }
                    );

                    return;
                }
                insertLink();
            }
        });

        for (var i = 0; i < window.links.length; i++) {
            $('#pages').append($('<option>', {
                text: window.links[i].title + ' - ' + window.links[i].url_slug,
                value: i
            }));
        }

        if (anchorElm) {
            page = dom.getAttrib(anchorElm, 'href');
            $('#target').val(dom.getAttrib(anchorElm, 'target'));
            $('#url').val(dom.getAttrib(anchorElm, 'href'));
            $('#display').val(anchorElm ? (anchorElm.innerText || anchorElm.textContent) : selection.getContent({format: 'text'}));
            $('#title').val(dom.getAttrib(anchorElm, 'title'));
            for (var i = 0; i < window.links.length; i++) {
              if(window.links[i].url_slug == page){
                  $('#pages').val(i);
              }
            }

            title = $('#title').val();
            display = $('#display').val();
            url = $('#url').val();
            target = $('#target').val();
        }else{
            $('#display').val(selection.getContent({format: 'text'}));
            $('#title').val(selection.getContent({format: 'text'}));
            title = $('#title').val();
            display = $('#display').val();
        }

        $('.chosen').chosen("destroy");
        $(".chosen").chosen();

        $('#pages').chosen().change(function () {
            page = window.links[$(this).val()].url_slug;
            $('#url').val(window.links[$(this).val()].url_slug);
            var text = window.links[$(this).val()].title;
            $('#display').val(text);
            $('#title').val(text);
            title = text;
            display = text;
            url = window.links[$(this).val()].url_slug;
        });
        $('#url').change(function () {
            url = $(this).val();
        });
        $('#display').change(function () {
            display = $(this).val();
        });
        $('#title').change(function () {
            title = $(this).val();
        });
        $('#target').change(function () {
            target = $(this).val();
        });
    }

    editor.addButton('link', {
        icon: 'link',
        tooltip: 'Insert/edit link',
        shortcut: 'Meta+K',
        onclick: createLinkList(showDialog),
        stateSelector: 'a[href]'
    });

    editor.addButton('unlink', {
        icon: 'unlink',
        tooltip: 'Remove link',
        cmd: 'unlink',
        stateSelector: 'a[href]'
    });

    editor.addShortcut('Meta+K', '', createLinkList(showDialog));
    editor.addCommand('mceLink', createLinkList(showDialog));

    this.showDialog = showDialog;

    editor.addMenuItem('link', {
        icon: 'link',
        text: 'Insert/edit link',
        shortcut: 'Meta+K',
        onclick: createLinkList(showDialog),
        stateSelector: 'a[href]',
        context: 'insert',
        prependToContext: true
    });
});
