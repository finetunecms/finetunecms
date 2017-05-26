<modal id="showModalMoveFiles" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::media.move.togroups') }}</h4>
    </div>
    <div slot="modal-body" class="modal-body">
        <!-- only show when ready, fileProgress is a percent -->
        <div class="alert alert-danger" v-show="errorsShow">
            <li v-for="(index, error) in errors">
                <div v-for="(index, value) in error">
                    <p>@{{ value }}</p>
                </div>

            </li>
        </div>
        <div class="form-group">
            <label for='folder'>Choose a Group</label>
            <v-select :value.sync="foldersSelected" label="title"
                      :multiple="true"
                      :options="folders"
                      placeholder="{{ trans('finetune::media.placeholder.groups') }}"></v-select>

        </div>

    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
            <button type="button" class="btn btn-default" @click="$broadcast('hide::modal','showModalMoveFiles')">{{ trans('finetune::media.move.cancel') }}</button>
            <button type="button" class="btn btn-success" @click='addMediaFolder()'>{{ trans('finetune::media.move.groups') }}</button>
        </div>
    </div>
</modal>
