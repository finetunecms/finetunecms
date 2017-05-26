<modal id="showModelDelete" size="md" :fade="false">
    <div slot="modal-header">
        <h4 class="modal-title">{{ trans('finetune::destroy.delete') }}</h4>
    </div>
    <div slot="modal-body">
        {{ trans('finetune::destroy.desc') }}
    </div>
    <div slot="modal-footer">
        <div class="footer-btns" >
            <button type="button" class="btn btn-default" id="no" data-dismiss="modal" @click="this.$broadcast('hide::modal', 'showModelDelete')">{{ trans('finetune::destroy.no') }}</button>
            <button type="button" class="btn btn-success" id="yes" data-dismiss="modal" @click='submitDestroy'>{{ trans('finetune::destroy.yes') }}</button>
        </div>
    </div>
</modal>
