@if(isset($errors))
@if(!$errors->isEmpty())
    <div class="alert alert-danger">
        <h4>Username or password not valid, try again.</h4>
    </div>
@endif
@endif

<!-- check for login errors flash var -->
@if (Session::has('login_errors'))
    <div class="form-group alert alert-danger">
        <p>{{ trans('finetune::passwords.incorrect') }}</p>
    </div>
@endif

@if(Session::has('message'))
    <alert type="{{ Session::get('class') }}" width="400px" placement="top" dismissable>
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