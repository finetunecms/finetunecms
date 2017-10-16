<modal id="showModalFile" size="lg" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::content.filePopup.title') }}</h4>
    </div>

    <div slot="modal-body">
        @include('finetune::partials.formvalidation')
        <div class="image-popup-header">
            <div class="row">
                <div class="col-md-6">
                    <label for="groups">Groups</label>
                    <v-select :on-change="changeFolder" :value.sync="folder" label="title"
                              :multiple="false"
                              :options="folders"
                              placeholder="{{ trans('finetune::media.placeholder.groups') }}"></v-select>
                </div>
                <div class="col-md-6">
                    <label for="search">Search</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search files and documents"
                               v-model="searchTerm" @keyup.enter="search"/>
                        <span class="input-group-btn">
                <button class="btn"><i class="fa fa-search"></i></button>
                </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="image-popup" v-if="media.length > 0">
            <div class="row">
                <div class="col-md-2" v-for="item in search('files')">
                    <div class="insert-image">
                        <p class="tiny" title="@{{ item.filename }}">@{{ item.filename.substring(0,10) }}
                            <span v-id="item.filename.length > 20">...</span></p>
                        <a href="#" class="btn btn-success btn-block" @click="insertFile(item.id)">{{ trans('finetune::content.filePopup.insert') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="no-content">
            <h3 v-if="media.length == 0">{{ trans('finetune::content.filePopup.empty') }}</h3>
        </div>
    </div>
    <div slot="modal-footer">
        <button type="button" class="btn btn-default btn-block" @click="$broadcast('hide::modal', 'showModalFile')">{{ trans('finetune::content.filePopup.close') }}</button>
    </div>
</modal>
