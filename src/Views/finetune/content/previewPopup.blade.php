<div v-show="previewActive" class="previewArea" >

    <div class="row">
        <div class="col-md-6">
            <div class="btn-group">
                <a href="#" class="btn btn-primary" @click="changePreview('mobile')">Mobile</a>
                <a href="#" class="btn btn-primary" @click="changePreview('tablet')">Tablet</a>
                <a href="#" class="btn btn-primary" @click="changePreview('desktop')">Desktop</a>
                <a href="#" class="btn btn-primary" @click="changePreview('laptop')">Laptop</a>
                <a href="#" class="btn btn-primary" @click="changePreview('desktop-xl')">Widescreen</a>
            </div>
        </div>
        <div class="col-md-2 push-md-4">
            <a href="#" class="btn btn-primary pull-right" @click="preview()" >Back to editing</a>
        </div>
    </div>

    <div class="previewContainer">
        <iframe src="about:blank" id="previewFrame" class="preview" v-bind:style="previewStyle"></iframe>
    </div>

</div>