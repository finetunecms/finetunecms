@extends('finetune::layouts.auth')

@section('content')

            @include('finetune::partials.formopen', ['url' => '/auth/', 'method' => 'post'])
            @include('finetune::partials.alerts')
            @include('finetune::partials.fields',[
            'name' => 'identity',
            'value' => '',
            'required' => false,
            'type' => 'text',
            'placeholder' => trans('finetune::passwords.user_login'),
            'title' => trans('finetune::passwords.user_login')])

            @include('finetune::partials.fields',[
            'name' => 'password',
            'value' => '',
            'required' => false,
            'type' => 'password',
            'placeholder' => trans('finetune::passwords.pass'),
            'title' => trans('finetune::passwords.pass')])
            <div class="form-group">
                <button type="submit" id="submit" class="btn btn-block btn-success">{{ trans('finetune::passwords.signin') }}</button>
            </div>
            <a href="/auth/forget" class="small forgot">{{ trans('finetune::passwords.forgot') }}</a>

            @include('finetune::partials.formclose')

@stop
