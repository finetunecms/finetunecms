@extends('finetune::layouts.auth')

@section('control')
    @include('finetune::partials.formopen', ['url' => '/auth/passwordchange', 'method' => 'post'])
    @include('finetune::partials.alerts')

    <input type="hidden" name="key" value="{{ $key }}" />

    @include('finetune::partials.fields',[
           'name' => 'password',
           'value' => '',
           'required' => false,
           'type' => 'password',
           'placeholder' => trans('finetune::passwords.pass'),
           'title' => trans('finetune::passwords.pass')])

    @include('finetune::partials.fields',[
          'name' => 'password_confirmation',
          'value' => '',
          'required' => false,
          'type' => 'password',
          'placeholder' => trans('finetune::passwords.pass_confirm'),
          'title' => trans('finetune::passwords.pass_confirm')])

    <div class="form-group">
        <button type="submit" id="submit" class="btn btn-block btn-success">{{ trans('finetune::passwords.change_pass') }}</button>
    </div>

        @include('finetune::partials.formclose')
@stop
