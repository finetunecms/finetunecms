<modal id="showModelSites" size="xl" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::sites.update') }}</h4>
    </div>

    <div slot="modal-body">
        <alert :show="errorsShow"
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

        <div class="row">

            <div class="col-md-6">

                @if(Entrust::hasRole('Superadmin'))

                    @include('finetune::partials.fields',[
                     'name' => 'domain',
                     'value' => '',
                     'required' => true,
                     'type' => 'text',
                     'vvalidate' => 'required|alpha_dash',
                     'vmodel' => 'siteObj.domain',
                     'placeholder' => trans('finetune::sites.placeholder.domain'),
                     'title' => trans('finetune::sites.form.domain'),
                     'popover' => trans('finetune::sites.popover.domain'),
                     'popoverPos' => 'left',
                     ])

                    @include('finetune::partials.fields',[
                     'name' => 'theme',
                     'value' => '',
                     'required' => true,
                     'type' => 'text',
                     'vmodel' => 'siteObj.theme',
                     'vvalidate' => 'required|alpha',
                     'placeholder' => trans('finetune::sites.placeholder.theme'),
                     'title' => trans('finetune::sites.form.theme'),
                     'popover' => trans('finetune::sites.popover.theme'),
                      'popoverPos' => 'left',
                     ])

                    @include('finetune::partials.fields',[
                     'name' => 'tag',
                     'value' => '',
                     'required' => true,
                     'type' => 'text',
                     'vmodel' => 'siteObj.tag',
                     'vvalidate' => 'required|alpha',
                     'placeholder' => trans('finetune::sites.placeholder.tag'),
                     'title' => trans('finetune::sites.form.tag'),
                     'popover' => trans('finetune::sites.popover.tag'),
                      'popoverPos' => 'left',
                     ])

                    @include('finetune::partials.fields',[
                     'name' => 'key',
                     'value' => '',
                     'required' => true,
                     'type' => 'text',
                     'vmodel' => 'siteObj.key',
                     'vvalidate' => 'alpha_dash',
                     'placeholder' => trans('finetune::sites.placeholder.key'),
                     'title' => trans('finetune::sites.form.key'),
                     'popover' => trans('finetune::sites.popover.key'),
                      'popoverPos' => 'left',
                     ])

                @endif


                @include('finetune::partials.fields',[
                 'name' => 'title',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.title',
                 'vvalidate' => 'required',
                 'placeholder' => trans('finetune::sites.placeholder.title'),
                 'title' => trans('finetune::sites.form.title'),
                 'popover' => trans('finetune::sites.popover.title'),
                  'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'dscpn',
                 'value' => '',
                 'required' => true,
                 'type' => 'textarea',
                 'vmodel' => 'siteObj.dscpn',
                 'vvalidate' => 'required',
                 'placeholder' => trans('finetune::sites.placeholder.dscpn'),
                 'title' => trans('finetune::sites.form.dscpn'),
                 'popover' => trans('finetune::sites.popover.dscpn'),
                  'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'keywords',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.keywords',
                 'vvalidate' => 'required',
                 'placeholder' => trans('finetune::sites.placeholder.keywords'),
                 'title' => trans('finetune::sites.form.keywords'),
                 'popover' => trans('finetune::sites.popover.keywords'),
                  'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'person',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.person',
                 'vvalidate' => 'required',
                 'placeholder' => trans('finetune::sites.placeholder.person'),
                 'title' => trans('finetune::sites.form.person'),
                 'popover' => trans('finetune::sites.popover.person'),
                 'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'email',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.email',
                 'vvalidate' => 'required|email',
                 'placeholder' => trans('finetune::sites.placeholder.email'),
                 'title' => trans('finetune::sites.form.email'),
                 'popover' => trans('finetune::sites.popover.email'),
                  'popoverPos' => 'left',
                 ])
            </div>
            <div class="col-md-6">

                @include('finetune::partials.fields',[
                 'name' => 'tel',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.tel',
                 'placeholder' => trans('finetune::sites.placeholder.tel'),
                 'title' => trans('finetune::sites.form.tel'),
                 'popover' => trans('finetune::sites.popover.tel'),
                  'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'company',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                   'vmodel' => 'siteObj.company',
                 'placeholder' => trans('finetune::sites.placeholder.company'),
                 'title' => trans('finetune::sites.form.company'),
                 'popover' => trans('finetune::sites.popover.company'),
                  'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'street',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.street',
                 'placeholder' => trans('finetune::sites.placeholder.street'),
                 'title' => trans('finetune::sites.form.street'),
                 'popover' => trans('finetune::sites.popover.street'),
                  'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'town',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.town',
                 'placeholder' => trans('finetune::sites.placeholder.town'),
                 'title' => trans('finetune::sites.form.town'),
                 'popover' => trans('finetune::sites.popover.town'),
                  'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'region',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.region',
                 'placeholder' => trans('finetune::sites.placeholder.region'),
                 'title' => trans('finetune::sites.form.region'),
                 'popover' => trans('finetune::sites.popover.region'),
                  'popoverPos' => 'left',
                 ])

                @include('finetune::partials.fields',[
                 'name' => 'postcode',
                 'value' => '',
                 'required' => true,
                 'type' => 'text',
                 'vmodel' => 'siteObj.postcode',
                 'placeholder' => trans('finetune::sites.placeholder.postcode'),
                 'title' => trans('finetune::sites.form.postcode'),
                 'popover' => trans('finetune::sites.popover.postcode'),
                  'popoverPos' => 'left',
                 ])
            </div>
        </div>
    </div>
    <div slot="modal-footer">
        <div class="footer-btns">
            <button type="button" class="btn btn-default"
                    @click="$broadcast('hide::modal', 'showModelSites')">{{ trans('finetune::sites.form.exit') }}</button>
            <button @click='post' class="btn btn-success" :disabled="saving"><span
                        v-if="!saving">{{ trans('finetune::sites.form.save') }}</span><i v-else
                                                                                         class="fa fa-spinner fa-spin"></i>
            </button>
        </div>
    </div>
</modal>