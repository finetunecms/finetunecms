<modal id="showModelTypeUpdate" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::type.modal.createTitle') }}</h4>
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

        @include('finetune::partials.fields',[
 'name' => 'title',
 'value' => (isset($type->title) ? $type->title : ''),
 'required' => true,
 'type' => 'text',
  'vvalidate' => 'required',
 'placeholder' => 'Main Title',
      'vmodel' => 'type.title',
 'title' => 'title'])

        @include('finetune::partials.fields',[
        'name' => 'outputs',
        'value' => (isset($type->outputs) ? $type->outputs : ''),
        'required' => true,
        'type' => 'text',
         'vvalidate' => 'required',
        'placeholder' => 'ex: list:display',
        'vmodel' => 'type.outputs',
        'title' => 'Outputs'])

        @include('finetune::partials.fields',[
        'name' => 'layout',
        'value' => (isset($type->layout) ? $type->layout : ''),
        'required' => true,
        'type' => 'text',
         'vvalidate' => 'required',
        'placeholder' => 'ex: home',
              'vmodel' => 'type.layout',
        'title' => 'Layout'])

        @include('finetune::partials.fields',[
        'name' => 'blocks',
        'value' => (isset($type->blocks) ? $type->blocks : ''),
        'required' => false,
        'type' => 'text',
        'placeholder' => 'blockone:blocktwo',
              'vmodel' => 'type.blocks',
        'title' => 'Blocks'])

        @include('finetune::partials.fields',[
        'name' => 'order_by',
        'value' => (isset($type->order_by) ? $type->order_by : ''),
        'required' => false,
        'type' => 'text',
        'placeholder' => 'Order By',
         'vmodel' => 'type.order_by',
        'title' => 'Order By'])

        <div class="row">
            <div class="col-md-6">
                @include('finetune::partials.fields',[
             'name' => 'children',
             'value' => (isset($type->children) ? $type->children : ''),
             'type' => 'checkbox',
             'id' => 'children',
             'class' => 'checkbox-primary',
               'vmodel' => 'type.children',
             'title' => 'Children'])

                @include('finetune::partials.fields',[
              'name' => 'nesting',
              'value' => (isset($type->nesting) ? $type->nesting : ''),
              'type' => 'checkbox',
              'id' => 'nesting',
              'class' => 'checkbox-primary',
                'vmodel' => 'type.nesting',
              'title' => 'Nesting'])

                @include('finetune::partials.fields',[
                'name' => 'ordering',
                'value' => (isset($type->ordering) ? $type->ordering : ''),
                'type' => 'checkbox',
                'id' => 'ordering',
                'class' => 'checkbox-primary',
                  'vmodel' => 'type.ordering',
                'title' => 'Ordering'])

                @include('finetune::partials.fields',[
                'name' => 'date',
                'value' => (isset($type->date) ? $type->date : ''),
                'type' => 'checkbox',
                'class' => 'checkbox-primary',
                'id' => 'date',
                  'vmodel' => 'type.date',
                'title' => 'Date'])

                @include('finetune::partials.fields',[
              'name' => 'default_type',
              'value' => (isset($type->default_type) ? $type->default_type : ''),
              'type' => 'checkbox',
              'class' => 'checkbox-primary',
              'id' => 'default_type',
                'vmodel' => 'type.default_type',
              'title' => 'Default Type'])
            </div>
            <div class="col-md-6">

                @include('finetune::partials.fields',[
            'name' => 'today_future',
            'value' => (isset($type->today_future) ? $type->today_future : ''),
            'type' => 'checkbox',
            'id' => 'today_future',
            'class' => 'checkbox-primary',
              'vmodel' => 'type.today_future',
            'title' => 'Today & Future'])

                @include('finetune::partials.fields',[
                'name' => 'today_past',
                'value' => (isset($type->today_past) ? $type->today_past : ''),
                'type' => 'checkbox',
                'id' => 'today_past',
                'class' => 'checkbox-primary',
                  'vmodel' => 'type.today_past',
                'title' => 'Today & Past'])


                @include('finetune::partials.fields',[
                'name' => 'live',
                'value' => (isset($type->live) ? $type->live : ''),
                'type' => 'checkbox',
                'class' => 'checkbox-primary',
                'id' => 'live-edit',
                  'vmodel' => 'type.live',
                'title' => 'Live Edit'])

                @include('finetune::partials.fields',[
                'name' => 'pagination',
                'value' => (isset($type->pagination) ? $type->pagination : ''),
                'type' => 'checkbox',
                'class' => 'checkbox-primary',
                'id' => 'Pagination',
                  'vmodel' => 'type.pagination',
                'title' => 'Pagination'])
            </div>
        </div>

        @include('finetune::partials.fields',[
        'name' => 'pagination_limit',
        'value' => (isset($type->pagination_limit) ? $type->pagination_limit : ''),
        'required' => false,
        'type' => 'text',
        'placeholder' => 'ex 10',
          'vmodel' => 'type.pagination_limit',
        'title' => 'Pagination Limit'])

        <p> If you have changed the outputs field it could take along time to submit as its rendering the new url structure for the type's nodes</p>
    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
            <button type="button" class="btn btn-default" @click="$broadcast('hide::modal', 'showModelTypeUpdate')">{{ trans('finetune::type.modal.exit') }}</button>
            <button @click='postType()' class="btn btn-success":disabled="saving"><span v-if="!saving">{{ trans('finetune::type.modal.saveType') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
        </div>
    </div>
</modal>