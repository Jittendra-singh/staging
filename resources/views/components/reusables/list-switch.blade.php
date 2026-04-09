@php
    $fieldName = $field ?? "status";
    //$disabled = (isset($data->checkin) && $data->checkin==1)?'disabled':'';
    $disabled = '';
    $uId = (isset($id))?$id:'switch';
@endphp
<div class="square-switch">
    <input {{ $disabled }} url="{{ $url }}" class="statusswitch" 
        type="checkbox" id="{{$uId}}{{$data->id}}" switch="none" {{$data->$fieldName ? 'checked' : ''}} />
    <label for="{{$uId}}{{$data->id}}" data-on-label="{{isset($onlabel)?$onlabel:'On'}}" data-off-label="{{isset($offlabel)?$offlabel:'Off'}}"></label>
</div>
