<modal id="showModelPermissions" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">Create / Update a Permission</h4>
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
        'name' => 'name',
        'required' => true,
        'type' => 'text',
        'vmodel' => 'permissionObj.name',
        'vvalidate' => 'required|alpha_dash',
        'placeholder' => 'Name',
        'title' => 'Name'])

        @include('finetune::partials.fields',[
      'name' => 'display_name',
      'required' => true,
      'type' => 'text',
      'vvalidate' => 'required|alpha_spaces',
      'vmodel' => 'permissionObj.display_name',
      'placeholder' => 'Display Name',
      'title' => 'Display Name'])


    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
         <button type="button" class="btn btn-default" @click="$broadcast('hide::modal', 'showModelPermissions')">Exit</button>
         <button @click='permissionsPost()' class="btn btn-success":disabled="saving"><span v-if="!saving">{{ trans('finetune::users.permissions.save') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
        </div>
    </div>
</modal>