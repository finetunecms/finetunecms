<modal id="showModelRoles" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">Create / Update a Role</h4>
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
        'vmodel' => 'roleObj.name',
        'vvalidate' => 'required|alpha_dash',
        'placeholder' => 'Name',
        'title' => 'Name'])

        <div class="form-group">
            <label class="control-label">Parent</label>
            <v-select :value.sync="roleObj.parent_id" label="name" :options="roles"></v-select>
        </div>

        <div class="form-group">
            <label class="control-label">Permissions</label>
            <v-select multiple :value.sync="roleObj.perms" label="display_name" :options="permissions"></v-select>
        </div>

    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
        <button type="button" class="btn btn-default" @click="$broadcast('hide::modal', 'showModelRoles')">Exit</button>
            <button @click='rolesPost()' class="btn btn-success":disabled="saving"><span v-if="!saving">{{ trans('finetune::users.roles.save') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
        </div>
    </div>
</modal>