@php
    $is_update_form = $formData ? true : false;
    $action_url = $is_update_form ? route("update-customer" , ['id' => $formData->id  ])  : route("create-customer");
    $name = old('name', $formData->name ?? '');
    $phone = old('phone', $formData->phone ?? '');
    $sec_phone = old('sec_phone', $formData->sec_phone ?? '');
    $email = old('email', $formData->email ?? '');
    $address = old('address', $formData->address ?? '');
    $city = old('city', $formData->city ?? '');
    $state = old('state', $formData->state ?? '');
    $country = old('country', $formData->country ?? '');
    $zipcode = old('zipcode', $formData->zipcode ?? '');
    $driving_licence_photo = old('driving_licence_photo', $formData->driving_licence_photo ?? '');
    $aadhar_front_photo = old('aadhar_front_photo', $formData->aadhar_front_photo ?? '');
    $aadhar_back_photo = old('aadhar_back_photo', $formData->aadhar_back_photo ?? '');
    $dl_number = old('dl_number', $formData->dl_number ?? '');
    $aadhar_number = old('aadhar_number', $formData->aadhar_number ?? '');
    $is_active = old('is_active', $formData->is_active ?? '');
    $id = old('id', $formData->id ?? '');
    $is_phone_verified = old('is_phone_verified', $formData->is_phone_verified ?? '');
    $is_dl_verified = old('is_dl_verified', $formData->is_dl_verified ?? '');
    $is_aadhar_verified = old('is_aadhar_verified', $formData->is_aadhar_verified ?? '');
@endphp

@extends('layouts.backend_new', ['pageName' => "Manage Customer" , 'parentPageName' => "Customers", 'parentRoute' => "customer"])
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
                <x-reusables.badge-alerts/>
                <form action="{{ $action_url }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if ($is_update_form)
                        @method('PUT')
                    @endif
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" value="{{$name}}" name="name" id="name" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" value="{{$phone}}" name="phone" id="mobile" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="email" class="form-label">Email</label>
                            <input class="form-control" type="email" value="{{$email}}" name="email" id="email" >
                        </div>
                        <div class="col-sm-3">
                            <label for="address" class="form-label">Address</label>
                            <input class="form-control" type="text" value="{{$address}}" name="address" id="address">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <label for="city" class="form-label">City</label>
                            <input class="form-control" type="text" value="{{$city}}" name="city" id="city">
                        </div>
                        <div class="col-sm-3">
                            <label for="state" class="form-label">State</label>
                            <input class="form-control" type="text" value="{{$state}}" name="state" id="state">
                        </div>
                        <div class="col-sm-3">
                            <label for="country" class="form-label">Country</label>
                            <input class="form-control" type="text" value="{{$country}}" name="country" id="country">
                        </div>
                        <div class="col-sm-3">
                            <label for="zipcode" class="form-label">Zipcode</label>
                            <input class="form-control" type="text" value="{{$zipcode}}" name="zipcode" id="zipcode">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <label for="" class="form-label">Driving Licence Photo</label>
                            <input class="form-control" type="file" value="" name="dl" id="dl">
                            <input name="photo_remove_dl" id="photo_remove_dl" value="0" type="hidden">
                            @if(isset($driving_licence_photo) && !empty($driving_licence_photo))
                                <small id="file_div_dl"><a download href="{{asset($driving_licence_photo)}}">Download Uploaded Image</a> <a class="remove_file_btn" filename="dl" href="javascript:void(0);" style="margin-left: 10px;" data-bs-toggle="tooltip" title="Remove File"><i class="fa fa-times text-danger " ></i></a></small>
                            @endif 
                        </div>
                        <div class="col-sm-3">
                            <label for="" class="form-label">Aadhar Front Photo</label>
                            <input class="form-control" type="file" value="" name="aadhar_front" id="af">
                            <input name="photo_remove_af" id="photo_remove_af" value="0" type="hidden">
                            @if(isset($aadhar_front_photo) && !empty($aadhar_front_photo))
                                <small id="file_div_af"><a download href="{{asset($aadhar_front_photo)}}">Download Uploaded Image</a> <a class="remove_file_btn" filename="af" href="javascript:void(0);" style="margin-left: 10px;" data-bs-toggle="tooltip" title="Remove File"><i class="fa fa-times text-danger " ></i></a></small>
                            @endif 
                        </div>
                        <div class="col-sm-3">
                            <label for="" class="form-label">Aadhar Back Photo</label>
                            <input class="form-control" type="file" value="" name="aadhar_back" id="ab">
                            <input name="photo_remove_ab" id="photo_remove_ab" value="0" type="hidden">
                            @if(isset($aadhar_back_photo) && !empty($aadhar_back_photo))
                                <small id="file_div_ab"><a download href="{{asset($aadhar_back_photo)}}">Download Uploaded Image</a> <a class="remove_file_btn" filename="ab" href="javascript:void(0);" style="margin-left: 10px;" data-bs-toggle="tooltip" title="Remove File"><i class="fa fa-times text-danger " ></i></a></small>
                            @endif 
                        </div>
                        <div class="col-sm-3">
                            <label for="aadhar_number" class="form-label">Aadhar Number</label>
                            <input class="form-control" type="text" value="{{$aadhar_number}}" name="aadhar_number" id="aadhar_number">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <label for="dl_number" class="form-label">Driving Licence Number</label>
                            <input class="form-control" type="text" value="{{$dl_number}}" name="dl_number" id="dl_number">
                        </div>
                        <div class="col-sm-3">
                            <label for="switchDl" class="form-label d-block">Verify Driving Licence</label>
                            <input name="verified_dl" class="verifiedswitch" type="checkbox" id="switchDl" switch="success" {{$is_dl_verified ? 'checked' : ''}} />
                            <label for="switchDl" data-on-label="Verified" data-off-label="Not Verified"></label>
                        </div>
                        <div class="col-sm-3">
                            <label for="switchA" class="form-label d-block">Verify Aadhar Number</label>
                            <input name="verified_ad" class="verifiedswitch" type="checkbox" id="switchA" switch="success" {{$is_aadhar_verified ? 'checked' : ''}} />
                            <label for="switchA" data-on-label="Verified" data-off-label="Not Verified"></label>
                        </div>
                        <div class="col-sm-3">
                            <label for="sec_phone" class="form-label">Secondary Phone</label>
                            <input class="form-control" type="text" value="{{$sec_phone}}" name="sec_phone" id="mobile_number" >
                        </div>
                    </div>
                    <div class="row mt-2">
                        <x-reusables.form-footer route="index-customer" action="{{$is_update_form ? 'update' : 'create'}}" module="customer" />
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection

