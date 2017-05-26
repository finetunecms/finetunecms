<ol class="breadcrumb">
    <?php
    $arrayKeys = array_keys($array);
    // Fetch last array key
    $lastArrayKey = array_pop($arrayKeys);
    ?>
    @foreach($array as $index => $value)
        @if($index == $lastArrayKey)
            <li class="breadcrumb-item active">{{ $value }}</li>
        @else
            <li class="breadcrumb-item"><a title="Link to {{ $value }}" href="/{{ $index }}">{{ $value }}</a></li>
        @endif
    @endforeach
</ol>
