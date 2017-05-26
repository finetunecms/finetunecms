<?php
$user = Auth::user();
?>

<div id="vue-topnav" class="top-bar">

    <h1 class="title">{{ $route }} @if(null !== session('site')) <span><a title="View Sites" href="/admin/sites" class="btn btn-sm" style="text-transform: none"> {{ session('site')->domain }}</a></span> @endif</h1>

    <nav>
        <div class="btn-group" role="group" aria-label="...">

                @impersonating
                <a href="/admin/users/stop" class="btn btn-sm">Stop Impersonating</a>
                @endImpersonating
            <a title="View Users" href="/admin/users" class="btn btn-sm" style="text-transform: none">Logged in as {{ auth()->user()->username }}</a>

            <a title="Logout" href="/auth/logout" class="btn btn-sm" >{{ trans('finetune::node.logout') }}</a>

        </div>
    </nav>

    @include('finetune::partials.preview')
</div>