<?php
if (empty($value)) {
    $value = old($name);
    if(isset($multiple)){
        $value = [];
    }
}
?>

@if(in_array($type, ['text', 'password', 'select', 'textarea']))
    <div class="form-group  @if(!empty($errors->first($name))) has-error has-feedback @endif  @if($required) has-feedback @endif">
        <label class="control-label" for="{{ isset($id) ? $id : $name }}">{{ ucfirst($title) }}

            @if(!empty($errors->first($name)) || $required)
                <span class="required" aria-hidden="true">*</span>
            @endif

            @if(isset($popover))
            <popover title="{{ $title }}" :triggers="['hover']" text="{{ $popover }}" position="{{ $popoverPos }}">
                <a class="btn btn-plain btn-sm btn-info">?</a>
            </popover>
            @endif
        </label>


        @if(isset($icon))
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1"><i class="fa {{ $icon }}"></i></span>
                @endif
                @if($type == 'select')
                    <select class="form-control @if(isset($class)) {{ $class }} @endif"
                            @if(isset($vvalidate)) v-validate="'{{ $vvalidate }}'" @endif
                            id="{{ isset($id) ? $id : $name }}"
                            @if(isset($multiple)) multiple @endif
                            name="{{ $name }}"
                            @if(isset($vmodel)) v-model="{{ $vmodel }}" @endif
                            @if(isset($von)) {!! $von !!} @endif
                            aria-describedby="input{{$name}}Status" @if(isset($disable)) disabled @endif>
                        @foreach($items as $index => $item)
                            <option value="{{ $index }}"
                                    @if(isset($multiple))
                                    @if(in_array($index, $value)) selected @endif
                                    @else
                                        @if($value == $index) selected @endif
                                    @endif
                            >{{ $item }}</option>
                        @endforeach
                    </select>
                @elseif($type == 'textarea')
                    <textarea class="form-control @if(isset($class)) {{ $class }} @endif"
                              id="{{ isset($id) ? $id : $name }}"
                              name="{{ $name }}"
                              @if(isset($vmodel)) v-model="{{ $vmodel }}" @endif
                              @if(isset($von)) {!! $von !!} @endif
                              placeholder="{{ $placeholder }}" @if(isset($disable)) disabled @endif
                              aria-describedby="input{{$name}}Status" @if(isset($disable)) disabled @endif @if(isset($vvalidate)) v-validate data-vv-rules="{{ $vvalidate }}" @endif>{{ $value }}</textarea>
                @else
                    <input type="{{ $type }}" class="form-control @if(isset($class)) {{ $class }} @endif"
                           id="{{ isset($id) ? $id : $name }}"
                           name="{{ $name }}"
                           value="{{ $value }}" aria-describedby="input{{$name}}Status"
                           @if(isset($vmodel)) v-model="{{ $vmodel }}" @endif
                           @if(isset($von)) {!! $von !!} @endif
                           placeholder="{{ $placeholder }}" @if(isset($disable)) disabled @endif @if(isset($vvalidate)) v-validate data-vv-rules="{{ $vvalidate }}" @endif/>
                @endif
                @if(isset($icon))
            </div>
        @endif
        @if(isset($vvalidate))
            <span v-show="veeErrors.has('{{ $name }}')" class="help is-danger"><?php echo "{{ veeErrors.first('".$name."') }}"  ?></span>
        @endif
        <span id="input{{$name}}Status" class="sr-only"> @if(empty($errors->first($name))) (success) @else (error) @endif</span>
    </div>
@elseif(in_array($type, ['checkbox', 'radio']))
    <div class="@if($type == 'radio') radio @else checkbox @endif @if(isset($class)) {{ $class }} @endif">
        @if($type == 'radio')
            <input type="radio" id="{{ isset($id) ? $id : $name }}" name="{{ $name }}" aria-describedby="input{{$name}}Status"
                   @if(isset($disable)) disabled
                   @if(isset($vmodel)) v-model="{{ $vmodel }}" @endif
                   @if(isset($von)) {!! $von !!} @endif
                   @endif @if($value == 1 || $value == 'on') checked @endif value="{{ $value }}">
        @else
            <input type="checkbox" id="{{ isset($id) ? $id : $name }}" name="{{ $name }}" aria-describedby="input{{$name}}Status"
                   @if(isset($vmodel)) v-model="{{ $vmodel }}" @endif
                   @if(isset($von)) {!! $von !!} @endif
                   @if(isset($disable)) disabled @endif @if($value == 1 || $value == 'on') checked @endif>
        @endif
        <label for="{{ isset($id) ? $id : $name }}">
            {{ $title }}
        </label>
            @if(isset($popover))
                <popover title="{{ $title }}" :triggers="['hover']" text="{{ $popover }}" position="{{ $popoverPos }}">
                    <a class="btn btn-plain btn-sm btn-info">?</a>
                </popover>
            @endif
        <span id="input{{$name}}Status" class="sr-only"> @if(empty($errors->first($name)))
                (success) @else (error) @endif</span>
    </div>
@elseif(in_array($type, ['node']))
    @include('finetune::partials.fields',[
    'name' => $name,
    'value' => (isset($value) ? $value : 0 ),
    'required' => false,
    'type' => 'select',
    'items' => $nodeList,
    'id' => (isset($id) ? $id : $name),
    'title' => $title])
@elseif(in_array($type, ['file']))
    <?php
       $collection = Media::getList($site, 'file', 'filename')
    ?>
    @include('finetune::partials.fields',[
    'name' => $name,
    'value' => (isset($value) ? $value : 0 ),
    'required' => false,
    'type' => 'select',
    'items' => $collection,
    'id' => (isset($id) ? $id : $name),
    'title' => $title])


@elseif(in_array($type, ['icons']))
    <table>
    <?php
        $icons = config('fields');
        if(isset($icons[$name])){
            $count = count($icons[$name]);
            $index = 0;
            $itemsPerRow = ($count / 5);
            foreach($icons[$name] as $index => $icon){
                if($index == $itemsPerRow){
                    echo '<tr>';
                    }
  ?>
        <td>
            <i class="fa {{ $index }}"></i>
            <input type="radio" name="{{ $name }}" aria-describedby="input{{$name}}Status"
                   @if(isset($vmodel)) v-model="{{ $vmodel }}" @endif
                   @if(isset($von)) {!! $von !!} @endif
                   @if($value == $index) checked @endif />
        </td>


    <?php
            if($index == $itemsPerRow){
                echo '</tr>';
                $index = 0;
            }else{
                $index++;
            }
         }
        }

    ?>
    </table>
@endif
