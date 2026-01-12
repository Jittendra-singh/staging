@php
$dtEx = explode(' ',$val); 
$dtDate = isset($dtEx[0])?$dtEx[0]:'';
$dtTime = isset($dtEx[1])?$dtEx[1]:'';
@endphp 
@if($type=='date')
<input type="text" class="form-control vehicle_date" type="text" value="{{$dtDate}}" name="{{$name}}" id="{{$id}}" placeholder="Date" {{$required}} style="{{isset($style)?$style:''}}">
@endif
@if($type=='time')
<select class="form-control" name="{{$name}}" id="{{$id}}" {{$required}}>
    <option value="">Time</option>
    <option {{($dtTime=='07:00'||$dtTime=='07:00:00')?'selected':''}} value="07:00:00">07:00</option>
    <option {{($dtTime=='07:30'||$dtTime=='07:30:00')?'selected':''}} value="07:30:00">07:30</option>
    <option {{($dtTime=='08:00'||$dtTime=='08:00:00')?'selected':''}} value="08:00:00">08:00</option>
    <option {{($dtTime=='08:30'||$dtTime=='08:30:00')?'selected':''}} value="08:30:00">08:30</option>
    <option {{($dtTime=='09:00'||$dtTime=='09:00:00')?'selected':''}} value="09:00:00">09:00</option>
    <option {{($dtTime=='09:30'||$dtTime=='09:30:00')?'selected':''}} value="09:30:00">09:30</option>
    <option {{($dtTime=='10:00'||$dtTime=='10:00:00')?'selected':''}} value="10:00:00">10:00</option>
    <option {{($dtTime=='10:30'||$dtTime=='10:30:00')?'selected':''}} value="10:30:00">10:30</option>
    <option {{($dtTime=='11:00'||$dtTime=='11:00:00')?'selected':''}} value="11:00:00">11:00</option>
    <option {{($dtTime=='11:30'||$dtTime=='11:30:00')?'selected':''}} value="11:30:00">11:30</option>
    <option {{($dtTime=='12:00'||$dtTime=='12:00:00')?'selected':''}} value="12:00:00">12:00</option>
    <option {{($dtTime=='12:30'||$dtTime=='12:30:00')?'selected':''}} value="12:30:00">12:30</option>
    <option {{($dtTime=='13:00'||$dtTime=='13:00:00')?'selected':''}} value="13:00:00">13:00</option>
    <option {{($dtTime=='13:30'||$dtTime=='13:30:00')?'selected':''}} value="13:30:00">13:30</option>
    <option {{($dtTime=='14:00'||$dtTime=='14:00:00')?'selected':''}} value="14:00:00">14:00</option>
    <option {{($dtTime=='14:30'||$dtTime=='14:30:00')?'selected':''}} value="14:30:00">14:30</option>
    <option {{($dtTime=='15:00'||$dtTime=='15:00:00')?'selected':''}} value="15:00:00">15:00</option>
    <option {{($dtTime=='15:30'||$dtTime=='15:30:00')?'selected':''}} value="15:30:00">15:30</option>
    <option {{($dtTime=='16:00'||$dtTime=='16:00:00')?'selected':''}} value="16:00:00">16:00</option>
    <option {{($dtTime=='16:30'||$dtTime=='16:30:00')?'selected':''}} value="16:30:00">16:30</option>
    <option {{($dtTime=='17:00'||$dtTime=='17:00:00')?'selected':''}} value="17:00:00">17:00</option>
    <option {{($dtTime=='17:30'||$dtTime=='17:30:00')?'selected':''}} value="17:30:00">17:30</option>
    <option {{($dtTime=='18:00'||$dtTime=='18:00:00')?'selected':''}} value="18:00:00">18:00</option>
    <option {{($dtTime=='18:30'||$dtTime=='18:30:00')?'selected':''}} value="18:30:00">18:30</option>
    <option {{($dtTime=='19:00'||$dtTime=='19:00:00')?'selected':''}} value="19:00:00">19:00</option>
    <option {{($dtTime=='19:30'||$dtTime=='19:30:00')?'selected':''}} value="19:30:00">19:30</option>
    <option {{($dtTime=='20:00'||$dtTime=='20:00:00')?'selected':''}} value="20:00:00">20:00</option>
    <option {{($dtTime=='20:30'||$dtTime=='20:30:00')?'selected':''}} value="20:30:00">20:30</option>
    <option {{($dtTime=='21:00'||$dtTime=='21:00:00')?'selected':''}} value="21:00:00">21:00</option>
</select>
@endif