<!DOCTYPE html>
{{-- This layout is for the login area --}}
<html lang="en">

@include('finetune::partials.head')

<body class="layout auth">

<div id="app" class="wrapper">
    <div class="container">
        <div class="login">
            <div class="intro">
                <h1 class="identity large"><img class="logo" title="Finetune Logo" src="/finetune/assets/img/identity-white.svg"/></h1>
            </div>
            <div class="auth-form">
                @yield('content')
            </div>
        </div>
    </div>
</div>

@include('finetune::partials.scripts')
</body>
</html>
