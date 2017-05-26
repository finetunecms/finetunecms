@extends('finetune::layouts.admin')


@section('manage')

    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>
    <div class="content row" id="cropper" v-cloak>
        <div class="col-md-8">
            <vue-cropper
                    v-ref:cropper
                    :guides="true"
                    :view-mode="2"
                    drag-mode="crop"
                    :auto-crop-area="0.5"
                    :min-container-width="250"
                    :min-container-height="180"
                    :background="true"
                    :rotatable="true"
                    preview="#preview"
                    :style="{'max-height':'500px'}"
                    src="{{ $media->external }}"
                    :cropmove="cropImage">
            </vue-cropper>
        </div>
        <div class="col-md-4" style="height:100%;">
            <div class="crop-item">
                <h4>Preview</h4>
                <div class="preview-image-wrap">
                    <div class="preview-image" id="preview"></div>
                </div>
            </div>
            <div class="crop-item">
                <h4>Options</h4>
                <h5>Aspect Ratios</h5>
                @foreach(config("image.aspectratio") as $crop => $ratio)
                    <button @click="setAspectRatio({{ $ratio }})" class="btn btn-success">{{ $crop }}</button>
                @endforeach
                <button @click="this.custom = !this.custom" class="btn btn-success">Custom</button>
                <!--
                <button @click="freeCrop()" class="btn btn-success">Free</button>
                -->
            </div>
            <div class="crop-item">

                <div v-if="custom">
                    <h5>Custom:</h5>
                    <div class="form-group">
                        <label class="sr-only" for="inlineFormInputGroup">xAspect</label>
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon">X</div>
                            <input type="text" class="form-control" id="inlineFormInputGroupX" placeholder="X"
                                   v-model="xAspect">
                        </div>
                        <label class="sr-only" for="inlineFormInputGroup">yAspect</label>
                        <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                            <div class="input-group-addon">Y</div>
                            <input type="text" class="form-control" id="inlineFormInputGroupY" placeholder="Y"
                                   v-model="yAspect">
                        </div>
                        <button @click="updateCrop()" class="btn btn-primary btn-block"><i class="fa fa-crop"></i> Change Aspect
                        Ratio</button>
                    </div>
                </div>
            </div>
            <div class="crop-item">
                <h5>Image Actions</h5>
                <div class="btn-group">
                    <button @click="rotate()" class="btn btn-primary"><i class="fa fa-repeat"></i> Rotate</button>
                    <button @click="scaleVert()" class="btn btn-primary"><i
                            class="fa fa-arrows-v"></i> Vertically</button>
                    <button @click="scaleHoriz()" class="btn btn-primary"><i
                            class="fa fa-arrows-h"></i> Horizontal</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('controls')
    <div class="actions">
        <form method="post" action="/admin/media/{{ $media->id }}/crop">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="crop" v-model="crop | json" value=""/>
            <button type="submit" class="btn btn-success"><i class="fa fa-crop"></i> Save Crop</button>
        </form>

    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=>'admin', 'admin/media'=>'media', 'content' => 'crop image']])
@stop
