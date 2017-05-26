<form class="form" method="post" action="{{ $url }}" @if($method == 'DELETE') v-on:submit.stop.prevent="destroy" @endif>
<input type="hidden" name="_token" value="{{ csrf_token() }}">
@if($method != 'POST')
        {{ method_field($method) }}
@endif