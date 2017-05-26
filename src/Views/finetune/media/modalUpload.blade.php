<modal id="showModalUpload" size="lg" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">Upload Images And Files</h4>
    </div>
    <div slot="modal-body">
        <alert :show="errorsShow" state="danger" dismissible>
            <li v-for="(index, error) in errors">
                <div v-for="(index, value) in error">
                    <p>@{{ value }}</p>
                </div>
            </li>
        </alert>
        <ul>
            <li v-for="file in files" :class="file.active">
                <img v-bind:src="renderImage(file.id, this.upload._files[file.id].file)" :id="file.id" width="100px"/>
                <h5>@{{file.name}}</h5>
                <vs-progress
                        variant="success"
                        :animated="true"
                        :value="parseInt(file.progress)"
                        striped>
                    @{{file.progress}}
                </vs-progress>
                <a class="btn btn-warning" @click="file.active = false">Cancel</a>
                <a class="btn btn-danger" @click="remove(file)"><i class="fa fa-trash"></i></a>
            </li>
        </ul>
        <file-upload
                title="<div class='btn btn-primary'>Add upload files</strong>"
                class="file-upload"
                name="file"
                post-action="/admin/api/media"
                :extensions="extensions"
                :accept="accept"
                :multiple="multiple"
                :size="size"
                v-ref:upload
                :drop="drop">
        </file-upload>

    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
            <button type="button" class="btn btn-default" @click="$broadcast('hide::modal', 'showModalUpload')">close</button>
            <button v-if="upload.active" class="btn btn-danger" type="submit" @click.prevent="$refs.upload.active = !$refs.upload.active">Stop upload</button>
            <button v-else  class="btn btn-success" type="submit" @click.prevent="$refs.upload.active = !$refs.upload.active">Start upload</button>
        </div>
    </div>
</modal>
