<modal id="showModelCustomField" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::type.modal.customTitle') }}</h4>
    </div>

    <div slot="modal-body">
        <alert
                :show="errorsShow"
                state="danger"
                dismissible>
            <ul class="list-unstyled">
                <li v-for="(index, error) in errors">
                    <div v-for="(index, value) in error">
                        <p>@{{ value }}</p>
                    </div>
                </li>
            </ul>

        </alert>

        <input type="hidden" name="type_id" value="{{ $type->id }}" v-model="field.type_id" />

        @include('finetune::partials.fields',[
         'name' => 'label',
         'required' => true,
         'type' => 'text',
         'placeholder' => 'label',
          'vvalidate' => 'required',
         'vmodel' => 'field.label',
         'title' => 'Label'])

        @include('finetune::partials.fields',[
         'name' => 'name',
         'required' => true,
         'type' => 'text',
         'placeholder' => 'name',
          'vvalidate' => 'required',
         'vmodel' => 'field.name',
         'title' => 'Name'])
        @include('finetune::partials.fields',[
           'name' => 'type',
           'required' => true,
           'type' => 'select',
           'items' => config('fields.fields'),
           'placeholder' => 'type',
           'vmodel' => 'field.type',
           'title' => 'Type'])

        @include('finetune::partials.fields',[
            'name' => 'values',
            'required' => false,
            'type' => 'text',
            'placeholder' => 'values',
            'vmodel' => 'field.values',
            'title' => 'Values'])
        @include('finetune::partials.fields',[
        'name' => 'placeholder',
        'required' => false,
        'type' => 'text',
        'placeholder' => 'placeholder',
        'vmodel' => 'field.placeholder',
        'title' => 'Placeholder'])
        @include('finetune::partials.fields',[
         'name' => 'checked',
         'required' => false,
         'type' => 'checkbox',
         'placeholder' => 'checked',
         'vmodel' => 'field.checked',
         'title' => 'Checked'])

        @include('finetune::partials.fields',[
       'name' => 'multiple',
       'required' => false,
       'type' => 'checkbox',
       'placeholder' => 'multiple',
       'vmodel' => 'field.multiple',
       'title' => 'Multiple'])
    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
            <button type="button" class="btn btn-default" @click="$broadcast('hide::modal', 'showModelCustomField')">
            {{ trans('finetune::type.modal.exit') }}</button>
            <button @click='postField()' class="btn btn-success":disabled="saving"><span v-if="!saving">{{ trans('finetune::type.modal.saveCustom') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
        </div>

    </div>
</modal>