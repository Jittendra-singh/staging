@php
    $fieldName = $field ?? "status";
    $disabled = (isset($data->checkin) && $data->checkin==1)?'disabled':'';
@endphp
<div class="square-switch">
    <input {{ $disabled }} url="{{ $url }}" class="statusswitch" 
        type="checkbox" id="switch{{$data->id}}" switch="none" {{$data->$fieldName ? 'checked' : ''}} />
    <label for="switch{{$data->id}}" data-on-label="{{isset($onlabel)?$onlabel:'On'}}" data-off-label="{{isset($offlabel)?$offlabel:'Off'}}"></label>
</div>
