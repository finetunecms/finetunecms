var omit = require('lodash/omit');
import Cropper from 'cropperjs';

var CropperComponent = Vue.extend({
    template: `
        <div style="max-height:500px;" >
            <img
                v-el:img
                :src="src"
                :alt="[ alt === undefined ? 'image': alt ]"
                style="max-width: 100%;"
                :style="imgStyle"
            />
        </div>
    `,
    props: {
        'style': Object,
        'data': Object,
        'preview': String,
        'src': {
            type: String,
            required: true
        },
        'alt': String,
        'imgStyle': String,
        'dragMode': String,
        'responsive': {
            type: Boolean,
            default: true
        },
        'restore': {
            type: Boolean,
            default: true
        },
        'checkCrossOrigin': {
            type: Boolean,
            default: true
        },
        'checkOrientation': {
            type: Boolean,
            default: true
        },
        'cropBoxMovable': {
            type: Boolean,
            default: true
        },
        'cropBoxResizable': {
            type: Boolean,
            default: true
        },
        'toggleDragModeOnDblclick': {
            type: Boolean,
            default: true
        },
        'modal': {
            type: Boolean,
            default: true
        },
        'center': {
            type: Boolean,
            default: true
        },
        'highlight': {
            type: Boolean,
            default: true
        },
        'zoomOnTouch': {
            type: Boolean,
            default: true
        },
        'zoomOnWheel': {
            type: Boolean,
            default: true
        },
        'scalable': {
            type: Boolean,
            default: true
        },
        'zoomable': {
            type: Boolean,
            default: true
        },
        'guides': {
            type: Boolean,
            default: true
        },
        'background': {
            type: Boolean,
            default: true
        },
        'autoCrop': {
            type: Boolean,
            default: true
        },
        'movable': {
            type: Boolean,
            default: true
        },
        'rotatable': {
            type: Boolean,
            default: true
        },
        'viewMode': Number,
        'aspectRatio': Number,
        'autoCropArea': Number,
        'wheelZoomRatio': Number,

        // Size limitation
        'minCanvasWidth': Number,
        'minCanvasHeight': Number,
        'minCropBoxWidth': Number,
        'minCropBoxHeight': Number,
        'minContainerWidth': Number,
        'minContainerHeight': Number,

        // callbacks
        'ready': Function,
        'cropstart': Function,
        'cropmove': Function,
        'cropend': Function,
        'crop': function (e) {
            var previews = document.querySelectorAll('.preview');
            var data = e.detail;
            var cropper = this.cropper;
            var imageData = cropper.getImageData();
            var previewAspectRatio = data.width / data.height;
            each(previews, function (elem) {
                var previewImage = elem.getElementsByTagName('img').item(0);
                var previewWidth = elem.offsetWidth;
                var previewHeight = previewWidth / previewAspectRatio;
                var imageScaledRatio = data.width / previewWidth;
                elem.style.height = previewHeight + 'px';
                previewImage.style.width = imageData.naturalWidth / imageScaledRatio + 'px';
                previewImage.style.height = imageData.naturalHeight / imageScaledRatio + 'px';
                previewImage.style.marginLeft = -data.x / imageScaledRatio + 'px';
                previewImage.style.marginTop = -data.y / imageScaledRatio + 'px';
            });
        },
        'zoom': Function,
    },
    ready () {
        var data = omit(this.$options.props, ['style', 'src', 'alt', 'imgStyle']);
        var props = {};
        for (var key in data) {
            if (this[key] !== undefined) {
                props[key] = this[key];
            }
        }

        this.cropper = new Cropper(this.$els.img, props);
    },
    methods: {
        reset () {
            return this.cropper.reset();
        },
        clear () {
            return this.cropper.clear();
        },
        replace (url, onlyColorChanged) {
            return this.cropper.replace(url, onlyColorChanged);
        },
        enable () {
            return this.cropper.enable();
        },
        disable () {
            return this.cropper.disable();
        },
        destroy () {
            return this.cropper.destroy();
        },
        move (offsetX, offsetY) {
            return this.cropper.move(offsetX, offsetY);
        },
        moveTo (x, y) {
            return this.cropper.moveTo(x, y);
        },
        zoom (ratio, _originalEvent) {
            return this.cropper.zoom(ratio, _originalEvent);
        },
        zoomTo (ratio, _originalEvent) {
            return this.cropper.zoomTo(ratio, _originalEvent);
        },
        rotate (degree) {
            return this.cropper.rotate(degree);
        },
        rotateTo (degree) {
            return this.cropper.rotateTo(degree);
        },
        scale (scaleX, scaleY) {
            return this.cropper.scale(scaleX, scaleY);
        },
        scaleX (scaleX) {
            return this.cropper.scaleX(scaleX);
        },
        scaleY (scaleY) {
            return this.cropper.scaleY(scaleY);
        },
        getData (rounded) {
            return this.cropper.getData(rounded);
        },
        setData (data) {
            return this.cropper.setData(data);
        },
        getContainerData () {
            return this.cropper.getContainerData();
        },
        getImageData () {
            return this.cropper.getImageData();
        },
        getCanvasData () {
            return this.cropper.getCanvasData();
        },
        setCanvasData (data) {
            return this.cropper.setCanvasData(data);
        },
        getCropBoxData () {
            return this.cropper.getCropBoxData();
        },
        setCropBoxData (data) {
            return this.cropper.setCropBoxData(data);
        },
        getCroppedCanvas (options) {
            return this.cropper.getCroppedCanvas(options);
        },
        setAspectRatio (aspectRatio) {
            return this.cropper.setAspectRatio(aspectRatio);
        },
        setDragMode () {
            return this.cropper.setDragMode();
        }
    }
});

Vue.component('vue-cropper', CropperComponent);

window.vmMedia = new window.FinetuneVue({
    el: '#cropper',
    filters: {},

    data: {
        loading: true,
        cropImg: '',
        horizontal: 1,
        vertical: 1,
        xAspect: '100',
        yAspect: '100',
        custom: false,
        crop:{}
    },

    ready: function () {
        this.cropImage();
    },
    methods: {
        rotate () {
            this.$refs.cropper.rotate(90);
            this.cropImage();
        },
        setAspectRatio: function setAspectRatio(aspect) {
            this.$refs.cropper.setAspectRatio(aspect);
            this.cropImage();
        },
        scaleVert: function scaleVert() {
            if (this.vertical == 1) {
                this.vertical = -1;
            } else {
                this.vertical = 1;
            }
            this.$refs.cropper.scale(this.horizontal, this.vertical);
            this.cropImage();
        },
        scaleHoriz: function scaleVert() {
            if (this.horizontal == 1) {
                this.horizontal = -1;
            } else {
                this.horizontal = 1;
            }
            this.$refs.cropper.scale(this.horizontal, this.vertical);
            this.cropImage();
        },

        cropImage () {
            this.loading = false;
            this.crop = this.$refs.cropper.getData(true);
        },

        freeCrop: function freeCrop() {
            this.$refs.cropper.setAspectRatio(0);
            this.cropImage();
        },

        updateCrop: function updateCrop() {
            var aspect = (this.xAspect / this.yAspect);
            this.$refs.cropper.setAspectRatio(aspect);
            this.cropImage();
        }
    },
    components: {
        'dropdown': window.VueStrap.dropdown
    },
});