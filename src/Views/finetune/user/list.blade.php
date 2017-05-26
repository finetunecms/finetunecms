@extends('finetune::layouts.admin')

@section('manage')
    <i v-if="loading" class="loading fa fa-circle-o-notch fa-spin fa-3x"></i>

    <div class="content-tabbed" v-cloak>
        <tabs size="md" :fade="false">
            <tab id="users" title="Users">

                <div class="table-responsive" v-if="users.length != 0">
                    <table class="table table-striped">
                        <thead>
                        <tr class="active">
                            <th class="th-select">Select</th>
                            <th class="th-title">Username</th>
                            <th class="th-actions">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in users" class="tr-@{{ item.tag }}" :class="renderSelectedRow(item)">
                        @include('finetune::partials.select')
                            <td class="title"><a href="#" @click="usersEdit(item)">@{{ item['username'] }}</a></td>
                            <td class="actions">
                            @if(\Entrust::hasRole(config('auth.superadminRole')))
                                <a href="/admin/users/impersonate/@{{ item['id'] }}">Impersonate</a>
                            @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else >
                    <p>{{ trans('finetune::users.empty') }}</p>
                    <a href="#" class="btn btn-success" @click="usersCreate()">{{ trans('finetune::users.create') }}</a>
                </div>
            </tab>
            <tab id="roles" title="Roles">
                <div class="table-responsive" v-if="roles.length != 0">
                    <table class="table table-striped">
                        <thead>
                        <tr class="active">
                            <th class="th-select">Select</th>
                            <th class="th-title">Title</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in roles" class="tr-@{{ item.tag }}" :class="renderSelectedRow(item)">
                        @include('finetune::partials.select')
                            <td class="title"><a href="#" @click="rolesEdit(item)">@{{ item['name'] }}</a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else >
                    <p>{{ trans('finetune::roles.empty') }}</p>
                    <a href="#" class="btn btn-success" @click="rolesCreate()">{{ trans('finetune::roles.create') }}</a>
                </div>
            </tab>
            @if(Entrust::ability('Superadmin','can_manage_types'))
            <tab id="permissions" title="Permissions">
                <div class="table-responsive" v-if="permissions.length != 0">
                    <table class="table table-striped">
                        <thead>
                        <tr class="active">
                            <th class="th-select">Select</th>
                            <th class="th-title">Title</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in permissions" class="tr-@{{ item.name }}" :class="renderSelectedRow(item)">
                        @include('finetune::partials.select')
                            <td class="title"><a href="#" @click="permissionsEdit(item)">@{{ item.display_name }}</a></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else>
                    <p>{{ trans('finetune::permissions.empty') }}</p>
                    <a href="#" class="btn btn-success" @click="permissionsCreate()">{{ trans('finetune::permissions.create') }}</a>
                </div>
            </tab>
                @endif
        </tabs>

        @include('finetune::partials.destroyModal')
        @include('finetune::user.usersModal')
        @include('finetune::user.rolesModal')
        @include('finetune::user.permissionsModal')
    </div>
@stop

@section('controls')
    <div class="actions" v-cloak>
        <div class="btn-group">
            <a href="#" @click="permissionsDelete()" class="btn" v-if="permission">Delete Permissions</a>
            <a href="#" @click="rolesDelete()" class="btn" v-if="role">Delete Role</a>
            <a href="#" @click="usersDelete()" class="btn" v-if="user">Delete User</a>
            <a href="#" @click="usersCreate()" class="btn btn-success" v-if="user">Create User</a>
            <a href="#" @click="rolesCreate()" class="btn btn-success" v-if="role">Create Roles</a>
            <a href="#" @click="permissionsCreate()" class="btn btn-success" v-if="permission">Create Permission</a>
        </div>
    </div>
@stop

@section('breadcrumb')
    @include('finetune::partials.breadcrumbs', ['array' => ['admin'=>'admin', 'content'=>'users']])
@stop