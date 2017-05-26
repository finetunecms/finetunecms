@if(!empty($packages))
@foreach($packages as $package)
    @if(is_array($package))
        @include($package['view'])
    @else
        @include($package)
    @endif
@endforeach
    @endif