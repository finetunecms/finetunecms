<modal id="showModelUsers" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">Create / Update a Users</h4>
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
        'name' => 'firstname',
        'required' => true,
        'type' => 'text',
         'vvalidate' => 'required',
        'placeholder' => 'First Name',
        'vmodel' => 'userObj.firstname',
        'title' => 'First Name'])

        @include('finetune::partials.fields',[
        'name' => 'lastname',
        'required' => true,
        'type' => 'text',
         'vvalidate' => 'required',
        'placeholder' => 'Last Name',
        'vmodel' => 'userObj.lastname',
        'title' => 'Last Name'])

        @include('finetune::partials.fields',[
        'name' => 'email',
        'required' => true,
        'type' => 'text',
         'vvalidate' => 'required|email',
        'placeholder' => 'Email',
        'vmodel' => 'userObj.email',
        'title' => 'Email'])

        @include('finetune::partials.fields',[
        'name' => 'username',
        'required' => true,
        'type' => 'text',
         'vvalidate' => 'required|alpha_dash',
        'vmodel' => 'userObj.username',
        'placeholder' => 'Username',
        'title' => 'Username'])

        @include('finetune::partials.fields',[
        'name' => 'password',
        'value' => '',
        'required' => true,
        'type' => 'password',
        'vmodel' => 'userObj.password',
        'placeholder' => 'Password',
        'title' => 'Password'])
        <p>Leave blank to not change password</p>
        @include('finetune::partials.fields',[
        'name' => 'password_confirmation',
        'value' => '',
        'required' => true,
        'type' => 'password',
        'vmodel' => 'userObj.password_confirmation',
        'placeholder' => 'Password Confirm',
        'title' => 'Password Confirm'])

        <div class="form-group">
            <label class="control-label">Role</label>
            <v-select :value.sync="userObj.roles" label="name" :options="roles"></v-select>
        </div>

        <div class="form-group">
            <label class="control-label">Sites</label>
            <v-select multiple :value.sync="userObj.sites" label="domain" :options="{{ $sites }}"></v-select>
        </div>

    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
        <button type="button" class="btn btn-default" @click="$broadcast('hide::modal', 'showModelUsers')">Exit</button>
            <button @click='usersPost()' class="btn btn-success":disabled="saving"><span v-if="!saving">{{ trans('finetune::users.user.save') }}</span><i v-else class="fa fa-spinner fa-spin"></i></button>
        </div>
    </div>
</modal>