@extends('finetune::layouts.auth')

@section('content')

    @include('finetune::partials.formopen', ['url' => '/auth/password', 'method' => 'post'])
    @include('finetune::partials.alerts')
    @include('finetune::partials.fields',[
    'name' => 'email',
    'value' => '',
    'required' => false,
    'type' => 'text',
    'placeholder' => trans('finetune::passwords.insert'),
    'title' => trans('finetune::passwords.insert')])

    <div class="form-group">
    <button type="submit" id="submit"
            class="btn btn-block btn-success">{{ trans('finetune::passwords.reset_pre') }}</button>
    </div>
    <a href="{{ url('/admin') }}" class="small forgot">{{ trans('finetune::passwords.back') }}</a>

    @include('finetune::partials.formclose')

@stop
