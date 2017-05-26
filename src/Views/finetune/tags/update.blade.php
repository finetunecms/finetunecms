<modal id="showModalTagUpdater" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::tags.updateTitle') }}</h4>
    </div>
    <div slot="modal-body">
        <alert :show="errorsShow" state="danger" dismissible>
            <li v-for="(index, error) in errors">
                <p v-for="(index, value) in error">
                    @{{ value }}
                </p>
            </li>
        </alert>
        @include('finetune::partials.fields',[
             'name' =>  'title',
             'value' => '',
             'required' => true,
             'type' => 'text',
             'placeholder' => trans('finetune::tags.placeholder.title'),
             'vmodel' => 'tag.title',
             'title' => trans('finetune::tags.form.title'),
             'popover' => trans('finetune::tags.popover.title'),
             'popoverPos' => 'left',
             'vvalidate' => 'required|alpha'
             ])

        @include('finetune::partials.fields',[
             'name' => 'tag',
             'value' => '',
             'required' => true,
             'type' => 'text',
             'placeholder' => trans('finetune::tags.placeholder.tag'),
             'vmodel' => 'tag.tag',
             'title' => trans('finetune::tags.form.tag'),
             'vvalidate' => 'alpha_dash',
             'popover' => trans('finetune::tags.popover.tag'),
                  'popoverPos' => 'left',
             ])
    </div>
    <div slot="modal-footer">
        <div class="footer-btns" >
            <button type="button" class="btn btn-default" @click="$broadcast('hide::modal', 'showModalTagUpdater')">{{ trans('finetune::tags.form.exit') }}</button>
            <button @click='postTag()' class="btn btn-success":disabled="saving"><span v-if="!saving">{{ trans('finetune::tags.form.save') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
        </div>
    </div>
</modal>
