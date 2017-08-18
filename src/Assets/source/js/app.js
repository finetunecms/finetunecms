window._ = require('lodash');
window.Vue = require('vue');
window.VueResource = require('vue-resource');
window.Sortable = require('vue-sortable');
window.VueStrap = require('vuestrap-base-components');
var $ = require('jquery');
window.jQuery = $;
window.$ = $;
window.chosen = require('chosen-js');
window.datePicker = require('./datepicker.vue');

import vSelect from "vue-select"
window.vSelect = vSelect;
Vue.component('v-select', vSelect);

import VueLazyload from 'vue-lazyload'
window.VueLazyLoad = VueLazyload;

Vue.use(VueLazyload, {
    preLoad: 1.3,
    error: 'dist/error.png',
    loading: 'dist/loading.gif',
    attempt: 1
})

window.FileUpload = require('vue-upload-component');

window.helper = require('./helper.js');
window.pacakges = require('./packages.js');

window.Vue.use(window.VueResource);
window.Vue.use(Sortable);

window.Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('value');

import VeeValidate from 'vee-validate';

const Veeconfig = {
    errorBagName: 'veeErrors', // change if property conflicts.
    fieldsBagName: 'fields',
    delay: 0,
    locale: 'en',
    dictionary: {
        en: {
            messages: {
                alpha: () => 'This field is only aplha numeric characters',
                alpha_dash: () => 'This field is only aplha numeric characters, underscores and dashes',
                required: () => 'This field is required',
            }
        },
    },
    strict: true,
    enableAutoClasses: false,
    classNames: {
        touched: 'touched', // the control has been blurred
        untouched: 'untouched', // the control hasn't been blurred
        valid: 'valid', // model is valid
        invalid: 'invalid', // model is invalid
        pristine: 'pristine', // control has not been interacted with
        dirty: 'dirty' // control has been interacted with
    }
};


window.Vue.use(VeeValidate, Veeconfig);

window.FinetuneVue = window.Vue.extend({
    data: function () {
        return {
            destroyModal: false,
            event: {},
            selectedItems: [],
            alert: false,
            alertType: '',
            alertMessage: '',
            loading: true,
            color: '#AAAAAA',
            size: '5px',
            errors: [],
            saving: false,
            errorsShow: false,
            hiddenSidemenu: false,
            mainWrapClass: 'container-fluid wrapper'
        }
    },
    ready: function () {

    },
    components: {
        'popover': window.VueStrap.popover,
        'modal': window.VueStrap.modal,
        'tooltip': window.VueStrap.tooltip,
        'alert': window.VueStrap.alert,
    },
    methods: {
        alertBox: function alertBox(alert, type, message) {
            var that = this;
            this.alert = alert;
            this.alertType = type;
            this.alertMessage = message;

            setTimeout(function () {
                that.alert = false;
            }, 2000);
        },
        selectRow: function selectRow(item, $event) {
            if (window.helper.containsObject(item, this.selectedItems)) {
                var i = window.helper.indexOfObject(item, this.selectedItems);
                this.selectedItems.splice(i, 1);
                var mainEle = $event.target;
            } else {
                this.selectedItems.push(item);
            }
        },
        renderSelected: function renderSelected(item){
            if (window.helper.containsObject(item, this.selectedItems)) {
                return ['fa', 'fa-check-square']
            }else{
                return ['fa', 'fa-square']
            }
        },
        renderSelectedRow: function renderSelectedRow(item){
            if (window.helper.containsObject(item, this.selectedItems)) {
                return ['selected']
            }else{
                return []
            }
        },
        splitter: function splitter(values) {
            var split = values.split(',');
            var array = [];
            for (var i = 0; i < split.length; i++) {
                var splitted = split[i].split(':');
                var object = {'label': splitted[1], 'value': splitted[0]};
                array.push(object)
            }
            return array;
        },
        parseDate: function parseDate(date) {
            var moment = require('moment');
            return moment(date, 'YYYY-MM-DD hh:mm:ss').format('Do MMMM YYYY, hh:mm');
        },
        parseHuman: function parseDate(date) {
            var moment = require('moment');
            var a = moment(date);
            var b = moment();
            return a.from(b);
        },
        getValue: function getValue(splitter, value, multiple) {
            var i;
            if (multiple) {
                var array = [];
                console.log(splitter, value);
                for (i = 0; i < splitter.length; i++) {
                    for (var x = 0; x < value.length; x++) {
                        if (splitter[i].value == value[x]) {
                            array.push(splitter[i]);
                        }
                    }
                }
                return array;
            } else {
                for (i = 0; i < splitter.length; i++) {
                    if (splitter[i].value == value) {
                        return splitter[i];
                    }
                }
            }

        },
        renderError: function renderError(response){
            var that = this;
            if(response.status != 422){
                that.alertBox(true, 'danger', 'Server responded with an error');
                console.log(response);
            } else{
                that.errors = response.data;
                that.errorsShow = true;
            }
        },
        cleanErrors: function cleanError(){
            this.errors = [];
            this.errorsShow = false;
        },
        clearItems: function clearItems() {
            this.selectedItems = [];
        },
        reducer:function reducer(url, width,height = 0){
            url = url+'/'.width;
            if(height != 0){
                url = url +'x'+height;
            }
            return url;
        },
        hideSidemenu: function hideSidemenu(e) {
            this.hiddenSidemenu = !this.hiddenSidemenu;
            if(this.hiddenSidemenu){
                this.mainWrapClass = 'container-fluid wrapper closed';
            }else{
                this.mainWrapClass = 'container-fluid wrapper';
            }

        },
    }, filters: {
        truncate: function (string, value) {
            if (typeof(string) != 'undefined' && string != null) {
                return string.substring(0, value) + '...';
            } else {
                return string;
            }
        }
    }
});

window.Vue.directive('tinymce', {
    twoWay: true,
    params: ['ided'],
    bind: function () {
        var that = this;
        Vue.nextTick(function () {
            tinymce.init({
                selector: '#' + that.params.ided,
                theme: 'modern',
                skin: 'finetune',
                convert_urls: false,
                plugins: [
                    'autolink link image hr lists advlist',
                    'searchreplace wordcount visualblocks code fullscreen',
                    'insertdatetime media table contextmenu directionality',
                    'paste', 'columnbreak', 'definitionlist', 'gallery', 'snippet', 'files', 'filebank'
                ],
                alignleft: {selector : 'p,h1,h2,h3,h4,h5,h6,ul,ol,li,table,img', classes : 'left'},
                aligncenter: {selector : 'p,h1,h2,h3,h4,h5,h6,ul,ol,li,table,img', classes : 'center'},
                alignright: {selector : 'p,h1,h2,h3,h4,h5,h6,ul,ol,li,table,img', classes : 'right'},
                alignjustify: {selector : 'p,h1,h2,h3,h4,h5,h6,ul,ol,li,table,img', classes : 'justify'},
                browser_spellcheck: true,
                toolbar1: 'undo redo | styleselect | bold italic | bullist numlist | alignleft aligncenter alignright alignjustify |  outdent indent | link | media image file | gallery filebank snippet ',
                menu: {
                    edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
                    insert:  {title: 'Insert', items: 'link media image | template hr columnbreak'},
                    view: {title: 'View', items: 'visualaid'},
                    format: {
                        title: 'Format',
                        items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'
                    },
                    table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},
                    plugins: {
                        title: 'Plugins',
                        items: 'spellchecker code columnbreak ToggleDefinitionList ToggleDefinitionItem'
                    }
                },
                image_advtab: true,
                link_list: that.params.nodes,
                setup: function (editor) {
                    // when typing keyup event
                    editor.on('keyup', function () {
                        var new_value = tinymce.get(that.params.ided).getContent(that.value);
                        // set model value
                        that.set(new_value)
                    });
                    editor.on('blur', function (e) {
                        var new_value = tinymce.get(that.params.ided).getContent(that.value);
                        // set model value
                        that.set(new_value)
                    });
                }
            });
        });
    },
    update: function (newVal, oldVal) {
        // set val and trigger event
        this.el.value = newVal;
        if (newVal.length > 0) {
            var tiny = tinymce.get(this.el.id);
            if (typeof(tiny) != 'undefined' && tiny != null) {
                Vue.nextTick(function () {
                    if (typeof(tiny.parser) != 'undefined' && tiny.parser != null) {
                        tiny.setContent(newVal);
                    }
                });
            }
        }
    }
});

require('./components/content');
require('./components/media');
require('./components/sidemenu');
require('./components/sites');
require('./components/snippets');
require('./components/tags');
require('./components/topnav');
require('./components/types');
require('./components/users');