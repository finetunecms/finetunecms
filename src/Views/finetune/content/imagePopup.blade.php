<modal id="showModalImage" size="lg" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::content.imagePopup.title') }}</h4>
    </div>

    <div slot="modal-body">
        <alert :show="errorsShow" state="danger" dismissible>
            <li v-for="(index, error) in errors">
                <div v-for="(index, value) in error">
                    <p>@{{ value }}</p>
                </div>
            </li>
        </alert>
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
                        <input type="text" class="form-control" placeholder="Search images and documents"
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
                <div class="col-md-2" v-for="item in search('images')">
                    <div class="insert-image">
                        <img v-lazy="item.thumb" :srcset="'{{ config('finetune.protocol') }}{{ $site->domain }}'+ item.thumb + '?fit=true'"
                             v-if="item.type == 'image'"/>
                        <p class="tiny" title="@{{ item.filename }}">@{{ item.filename.substring(0,10) }}
                            <span v-id="item.filename.length > 20">...</span></p>
                        <a href="#" class="btn btn-success btn-block" @click="insert(item.id)">{{ trans('finetune::content.imagePopup.insert') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="no-content">
            <h3 v-if="media.length == 0">{{ trans('finetune::content.imagePopup.empty') }}</h3>
        </div>
    </div>
    <div slot="modal-footer">
        <button type="button" class="btn btn-default btn-block" @click="$broadcast('hide::modal', 'showModalImage')">{{ trans('finetune::content.imagePopup.close') }}</button>
    </div>
</modal>
