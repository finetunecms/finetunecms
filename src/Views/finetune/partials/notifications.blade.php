{{--
@if(Session::has('message'))
    <alert type="{{ Session::get('class') }}" width="400px" placement="top-right" dismissable>
        @if(Session::get('class') == 'success')
            <span class="fa fa-check-circle-o alert-icon-float-left"></span>
        @elseif(Session::get('class') == 'info')
            <span class="fa fa-info-circle alert-icon-float-left"></span>
        @elseif(Session::get('class') == 'warning')
            <span class="fa fa-exclamation-triangle alert-icon-float-left"></span>
        @elseif(Session::get('class') == 'danger')
            <span class="fa fa-times-circle-o alert-icon-float-left"></span>
        @endif
        {{ Session::get('message') }}
    </alert>
@endif

@if(Session::has('csrf_error'))
    <alert type="danger" width="400px" placement="top-right" dismissable>
        <span class="fa fa-times-circle-o alert-icon-float-left"></span>
        {{ Session::get('csrf_error') }}
    </alert>
@endif
--}}

<div v-if="alert" transition="fade" class="alert-holder">
    <alert :show="alert" :state="alertType" v-cloak>
        <p>
            <span class="fa fa-check-circle" v-if="alertType == 'success'"></span>
            <span class="fa fa-times-circle-o" v-if="alertType == 'danger'"></span>
            <span class="fa fa-check-circle" v-if="alertType == 'warning'"></span>
            <span class="fa fa-info-circle" v-if="alertType == 'info'"></span>
            @{{ alertMessage }}</p>
    </alert>
</div>

