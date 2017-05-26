var users = document.getElementById('users');
if (typeof(users) != 'undefined' && users != null) {
    window.vmUsers = new window.FinetuneVue({
        el: '#users', // Element it is set on
        filters: {},
        data: {
            users: [],
            userId: 0,
            userObj: {
                username: '',
                email: '',
                firstname: '',
                lastname: '',
                password_confirmation: '',
                roles: 0,
                sites: 0
            },
            roles: [],
            roleId: 0,
            roleObj: {name: '', parent_id: '', perms: ''},
            permissions: [],
            permissionId: 0,
            permissionObj: {name: '', parent: ''},
            user: true,
            role: false,
            permission: false,
        },

        ready: function () {
            var that = this;
            this.$http.get('/admin/api/users').then(function (response) {
                that.loading = false;
                that.users = response.data;
            });
            this.$http.get('/admin/api/roles').then(function (response) {
                that.roles = response.data;
            });
            this.$http.get('/admin/api/permissions').then(function (response) {
                that.permissions = response.data;
            });
        },
        methods: {


            'usersCreate': function usersCreate() {
                this.usersClear();
                this.$broadcast('show::modal', 'showModelUsers');
            },
            'usersClear': function usersClear() {
                this.userObj = {
                    username: '',
                    email: '',
                    firstname: '',
                    lastname: '',
                    password: '',
                    password_confirmation: '',
                    roles: 0,
                    sites: 0
                };
                this.cleanErrors();
                this.userId = 0;
            },
            'usersEdit': function usersEdit(user) {
                this.usersClear();
                this.userId = user.id;
                this.userObj = {
                    username: user.username,
                    email: user.email,
                    firstname: user.firstname,
                    lastname: user.lastname,
                    password: user.password,
                    password_confirmation: user.password_confirmation,
                    roles: user.roles[0],
                    sites: user.sites,
                };
                this.$broadcast('show::modal', 'showModelUsers');
            },
            'usersPost': function usersPost() {
                var that = this;
                that.saving = true;
                if (that.userId > 0) {
                    this.$http.put('/admin/api/users/' + that.userId, this.userObj).then(function (response) {
                        that.users = response.data.users;
                        this.usersClear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelUsers');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                } else {
                    this.$http.post('/admin/api/users', this.userObj).then(function (response) {
                        that.users = response.data.users;
                        this.usersClear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelUsers');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                }
            },
            'usersDelete': function usersDelete(){
                this.$broadcast('show::modal', 'showModelDelete');
            },



            'rolesCreate': function rolesCreate() {
                this.rolesClear();
                this.$broadcast('show::modal', 'showModelRoles');
            },
            'rolesClear': function rolesClear() {
                this.roleObj = {name: '', parent_id: '', perms: ''};
                this.roleId = 0;
                this.cleanErrors();
            },
            'rolesEdit': function rolesEdit(role) {
                this.rolesClear();
                this.roleObj = {
                    name: role.name,
                    parent_id: role.parent_id,
                    perms: role.perms
                };
                this.roleId = role.id;
                this.$broadcast('show::modal', 'showModelRoles');
            },
            'rolesPost': function rolesPost() {
                var that = this;
                that.saving = true;
                if (that.roleId > 0) {
                    this.$http.put('/admin/api/roles/' + that.roleId, this.roleObj).then(function (response) {
                        that.roles = response.data.roles;
                        this.rolesClear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelRoles');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                } else {
                    this.$http.post('/admin/api/roles', this.roleObj).then(function (response) {
                        that.roles = response.data.roles;
                        this.rolesClear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModalRoles');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                }
            },
            'rolesDelete': function rolesDelete(){
                this.$broadcast('show::modal', 'showModelDelete');
            },

            'permissionsCreate': function permissionsCreate() {
                this.permissionsClear();
                this.$broadcast('show::modal', 'showModelPermissions');
            },
            'permissionsClear': function permissionsClear() {
                this.permissionObj = {name: '', display_name: ''};
                this.permissionId = 0;
                this.cleanErrors();
            },
            'permissionsEdit': function permissionsEdit(permission) {
                this.permissionsClear();
                this.permissionObj = {
                    name: permission.name,
                    display_name: permission.display_name,
                };
                this.permissionId = permission.id;
                this.$broadcast('show::modal', 'showModelPermissions');
            },
            'permissionsPost': function permissionsPost() {
                var that = this;
                that.saving = true;
                if (that.permissionId > 0) {
                    this.$http.put('/admin/api/permissions/' + that.permissionId, this.permissionObj).then(function (response) {
                        that.permissions = response.data.permissions;
                        this.permissionsClear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelPermissions');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                } else {
                    this.$http.post('/admin/api/permissions', this.permissionObj).then(function (response) {
                        that.permissions = response.data.permissions;
                        this.permissionsClear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelPermissions');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                }
            },
            'permissionsDelete': function permissionsDelete(){
                this.$broadcast('show::modal', 'showModelDelete');
            },
            submitDestroy: function submitDestroy() {
                this.$broadcast('hide::modal', 'showModelDelete');
                let items = this.selectedItems;
                let that = this;
                if(this.user){
                    this.$http.delete('/admin/api/users/destroy', {users: items}).then(function (response) {
                        that.destroyModal = false;
                        that.users = response.data.users;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        this.renderError(response);
                    });
                }

                if(this.role){
                    this.$http.delete('/admin/api/roles/destroy', {roles: items}).then(function (response) {
                        that.destroyModal = false;
                        that.roles = response.data.roles;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        this.renderError(response);
                    });
                }
                if(this.permission){
                    this.$http.delete('/admin/api/permissions/destroy', {permissions: items}).then(function (response) {
                        that.destroyModal = false;
                        that.permissions = response.data.permissions;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        this.renderError(response);
                    });
                }

            },

        },
        components: {
            'tabs': window.VueStrap.tabs,
            'tab': window.VueStrap.tab,
            'v-select': window.vSelect
        },
        events: {
            'changed::tab': function (tab) {
                if (tab == 'users') {
                    this.role = false;
                    this.permission = false;
                    this.user = true;
                }
                if (tab == 'roles') {
                    this.role = true;
                    this.permission = false;
                    this.user = false;
                }
                if (tab == 'permissions') {
                    this.role = false;
                    this.permission = true;
                    this.user = false;
                }
            }
        }
    });
}