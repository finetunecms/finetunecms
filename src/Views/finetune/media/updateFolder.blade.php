<modal id="showModalAddFolder" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::media.groups.createUpdateTitle') }}</h4>
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

        @include('finetune::partials.fields',[
             'name' => 'title',
             'value' => '',
             'required' => true,
             'type' => 'text',
             'placeholder' => trans('finetune::media.groups.title'),
             'vmodel' => 'folderUpdate.title',
             'vvalidate' => 'required|alpha_dash',
             'title' => trans('finetune::media.groups.title')])

        @include('finetune::partials.fields',[
             'name' => 'tag',
             'value' => '',
             'required' => true,
             'type' => 'text',
             'placeholder' => trans('finetune::media.groups.tag'),
             'vmodel' => 'folderUpdate.tag',
             'vvalidate' => 'alpha_dash',
             'title' => trans('finetune::media.groups.tag')])
    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
            <button type="button" class="btn btn-default" @click='$broadcast("hide::modal","showModalAddFolder")'>{{ trans('finetune::media.groups.cancel') }}</button>
            <button type="button" class="btn btn-success" @click='submitFolder()'>{{ trans('finetune::media.groups.save') }}</button>
        </div>
    </div>
</modal>
