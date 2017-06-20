<!DOCTYPE html>
{{-- This layout is for users that are logged in --}}
<html lang="en">

@include('finetune::partials.head')

<body class="layout {{ ($route == 'media') ? 'media-main' : '' }}" id="{{ $route }}">

@yield('formOpen')

<div id="app" class="container-fluid wrapper" :class="mainWrapClass">
    <div class="row wrapper">
        @include('finetune::partials.sidemenu')
        <div class="col-md-10 interface">
            @include('finetune::partials.topnav')
            <div class="controls">
                @yield('breadcrumb')
                @yield('controls')
            </div>

            <div class="manage">
                @include('finetune::partials.notifications')
                @yield('manage')
            </div>
            @yield('actions')
        </div>
    </div>
</div>

@yield('formClose')
@yield('modals')


@section('scripts')
    @include('finetune::partials.scripts')
@show

</body>
</html>