@extends($site->theme.'::layouts.layout')

@section('content')
    <div class="hero">
        <div class="container">
            <div class="column-full">
                <h1 class="headline">{{ $body->title }}</h1>
            </div>
        </div>

    </div>
    <div class="band">
        <div class="container">
            <aside class="col-m-3">
                @include($site->theme.'::partials.subnav', ['ignore' => []])
            </aside>
            <div class="col-m-8 push-m-1">
                @if($body->image)
                    <img class="full-width" src="{{ $body->image }}"
                         alt="{{ $body->title }}"/>
                @endif

                {!! $body->content !!}

            </div>
        </div>
    </div>
@endsection