var sites = document.getElementById('sites');
if (typeof(sites) != 'undefined' && sites != null) {
    window.vmSites = new window.FinetuneVue({
        el: '#sites',
        data: {
            sites: [],
            siteObj: {
                title: '',
                domain: '',
                dscpn: '',
                keywords: '',
                theme: '',
                company: '',
                person: '',
                email: '',
                street: '',
                town: '',
                postcode: '',
                region: '',
                tel: '',
                tag: '',
                key: ''
            },
            siteId: 0
        },
        ready: function () {
            var that = this;
            this.$http.get('/admin/api/sites').then(function (response) {
                that.loading = false;
                that.sites = response.data;
            }, function (response){
                that.loading = false;
                this.renderError(response);
            });
        },
        methods: {
            'create': function create() {
                this.clear();
                this.$broadcast('show::modal', 'showModelSites');
            },
            'clear': function clear() {
                this.siteObj = {
                    title: '',
                    domain: '',
                    dscpn: '',
                    keywords: '',
                    theme: '',
                    company: '',
                    person: '',
                    email: '',
                    street: '',
                    town: '',
                    postcode: '',
                    region: '',
                    tel: '',
                    tag: '',
                    key: ''
                },
                    this.siteId = 0;
                this.cleanErrors();
            },
            'edit': function edit(site) {
                this.clear();
                this.siteId = site.id;
                this.siteObj = {
                    title: site.title,
                    domain: site.domain,
                    dscpn: site.dscpn,
                    keywords: site.keywords,
                    theme: site.theme,
                    company: site.company,
                    person: site.person,
                    email: site.email,
                    street: site.street,
                    town: site.town,
                    region: site.region,
                    postcode: site.postcode,
                    tel: site.tel,
                    tag: site.tag,
                    key: site.key
                };
                this.$broadcast('show::modal', 'showModelSites');
            },
            'post': function post() {
                var that = this;
                that.saving = true;
                if (that.siteId > 0) {
                    this.$http.put('/admin/api/sites/' + that.siteId, this.siteObj).then(function (response) {
                        that.sites = response.data.sites;
                        this.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelSites');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                } else {
                    this.$http.post('/admin/api/sites', this.siteObj).then(function (response) {
                        that.sites = response.data.sites;
                        this.clear();
                        that.saving = false;
                        this.$broadcast('hide::modal', 'showModelSites');
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        that.saving = false;
                        this.renderError(response);
                    });
                }
            },
            'destroy': function destroy(event) {
                this.destroyModal = true;
                this.event = event;
            },

            'submitDestroy': function submitDestroy() {
                var items = this.selectedItems;
                var that = this;
                this.$broadcast('hide::modal', 'showModelDelete');
                if (items.length > 0) {
                    this.$http.delete('/admin/api/sites/delete', {sites: items}).then(function (response) {
                        that.destroyModal = false;
                        that.sites = response.data;
                        that.alertBox(true, response.data.alertType, response.data.alertMessage);
                    }, function (response){
                        this.renderError(response);
                    });
                }
            }
        }
    });
}