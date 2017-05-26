<modal :show.sync="showPreviewModal" large="true" title="{{ $site->domain }}" class="modal-preview" style="display: none">

    <div slot="modal-body" class="modal-body">
        <div id="preview-area" rel="{{ config('finetune.protocol') }}{{ $site->domain }}">
                    <div class="btn-group device-buttons clearfix">
                        <a href="#" @click='preview("mobile")' class="btn">{{ trans('finetune::main.preview.mobile') }}</a>
                        <a href="#" @click='preview("tablet")' class="btn">{{ trans('finetune::main.preview.tablet') }}</a>
                        <a href="#" @click='preview("screen")' class="btn">{{ trans('finetune::main.preview.screen') }}</a>
                    </div>

            <div class="device" id="preview-device">
                <div class="preview">
                    @{{{ iframe }}}
                </div>
            </div>
        </div>
    </div>
    <div slot="modal-footer" class="modal-footer">
        <button type="button" class="btn btn-default" @click='showPreviewModal = false'>{{ trans('finetune::main.close') }}</button>
    </div>
</modal>