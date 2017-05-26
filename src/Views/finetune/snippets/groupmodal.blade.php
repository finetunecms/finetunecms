<modal id="showModalGroupUpdater" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">Create a group</h4>
    </div>

    <div slot="modal-body">

        <alert :show="errorsShow" state="danger" dismissible>
            <li v-for="(index, error) in errors">
                <div v-for="(index, value) in error">
                    <p>@{{ value }}</p>
                </div>
            </li>
        </alert>

        @include('finetune::partials.fields',[
             'name' => 'title',
             'value' => '',
             'required' => true,
             'type' => 'text',
             'placeholder' => trans('finetune::snippets.form.group.title'),
             'vmodel' => 'group.title',
             'vvalidate' => 'required',
             'title' => trans('finetune::snippets.form.group.title')])

        @include('finetune::partials.fields',[
             'name' => 'tag',
             'value' => '',
             'required' => false,
             'type' => 'text',
             'placeholder' => trans('finetune::snippets.form.group.tag'),
             'vmodel' => 'group.tag',
             'title' => trans('finetune::snippets.form.group.tag')])

        @include('finetune::partials.fields',[
             'name' => 'dscpn',
             'value' => '',
             'required' => false,
             'type' => 'textarea',
             'placeholder' => trans('finetune::snippets.form.group.dscpn'),
             'vmodel' => 'group.dscpn',
             'title' => trans('finetune::snippets.form.group.dscpn')])
    </div>
    <div slot="modal-footer">
        <div class="footer-btns" >
        <button type="button" class="btn btn-default" @click="$broadcast('hide::modal', 'showModalGroupUpdater')">Exit</button>
        <button type="button" class="btn btn-success" @click='postGroup'>Save Group</button>
         </div>
    </div>
</modal>
