@php
    $action_url       = route("update_settings");
    $event_commission = old('event_commission', $formData->event_commission ?? '');
    $waiver_text      = old('waiver_text', $formData->waiver_text ?? '');
    $logo             = $formData->logo ?? '';
    $pageName         = "Manage Settings";
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName , 'parentPageName' => "Settings", 'parentRoute' => "settings"])
@section('css')
<x-reusables.advanceformcss />
@endsection
@section('js')
<x-reusables.advanceformjs /> 
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <x-reusables.frontend.alert/>
                <form action="{{ $action_url }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="event_commission" class="form-label">Commissions <span class="text-danger">*</span></label>
                            <input class="form-control" type="number" value="{{$event_commission}}" name="event_commission" id="event_commission" required>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="" class="form-label">Waiver Content</label>
                            <textarea name="waiver_text" class="form-control ckeditor-classic" id="" >{{$waiver_text}}</textarea>
                        </div>
                    </div>
                    @if(false)
                    <div class="row justify-content-center mt-3">
                        <div class="col-sm-4">
                            <label for="event_commission" class="form-label">Site Logo <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="logo" id="logo" >
                            @if($logo)
                                <div class="mt-2">
                                    <img src="{{ asset($logo) }}" alt="Logo" class="img-fluid" width="40" height="40"> 
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="row mt-2">
                        <x-reusables.form-footer route="settings" action="update" module="settings" />
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection
