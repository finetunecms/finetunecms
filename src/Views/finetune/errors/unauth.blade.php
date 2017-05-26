@extends('finetune::layouts.auth')

@section('content')

            <h1>You are unauthorised to view this page</h1>
            <p>Please speak to your website administrator if this is an error</p>
            <p>This unauthorised access has been logged</p>
            <a href="/" class="btn btn-success">Return back to the site</a>
            <a href="/auth/logout" class="btn btn-danger">Logout and back to admin login</a>
@stop