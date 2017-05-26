<!DOCTYPE html>
{{-- This layout is for the live area --}}
<html lang="en">

@include('finetune::partials.head')

<body class="layout live">
<div id="live" class="wrapper">
    <div class="livewrap @if(session('small') != 'false') large @endif">
        <nav id="vue-topnav" class="navbar navbar-default navbar-static-top" role="navigation">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="/admin/content/{{ $node->id }}/edit" @click="loadPreview()">back</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
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