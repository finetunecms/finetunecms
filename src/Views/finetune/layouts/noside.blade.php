<!DOCTYPE html>
{{-- This layout is for the login area --}}
<html lang="en">

@include('finetune::partials.head')

<body class="layout auth">

<div id="app" class="wrapper">
    <div class="main @if(session('small') != 'false') large @endif">
        <nav id="vue-topnav" class="navbar navbar-default navbar-static-top" role="navigation">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" @click="loadPreview()">preview</a></li>
                <li>
                    <?php
                    $user = Auth::user();
                    ?>
                    <dropdown>
                        <a href="#" data-toggle="dropdown" class="dropdown">
                            {{ $user->firstname }}
                            {{ $user->lastname }}
                            <span class="caret"></span>
                        </a>
                        <ul name="dropdown-menu" class="dropdown-menu">
                            <li><a href="#">User Settings</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="/auth/logout">Logout</a></li>
                        </ul>
                    </dropdown>
                </li>
            </ul>
            @include('finetune::partials.preview')
        </nav>
        <div class="scroll-area">
            @yield('manage')
        </div>

    </div>
</div>
@section('scripts')
    @include('finetune::partials.scripts')
@show

</body>
</html>