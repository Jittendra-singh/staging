@php 
$tableHead = ['Phone' ,'Name' ,'Email' , 'Created Date' ,  'Action'] ; 
@endphp
@extends('layouts.backend_new', ['pageName' => "Customers" , 'parentPageName' => "Customers"])
@section('css')
<x-reusables.datatablecss />
@endsection
@section('js')
<x-reusables.datatablejs />
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm">
                        <div class="mb-4">
                            <a href="{{route("create-customer")}}" class="btn btn-light waves-effect waves-light"><i class="bx bx-plus me-1"></i> Add Customer</a>
                        </div>
                    </div>
                </div>
                <!-- end row -->
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered   nowrap w-100" dt-responsive>
                        <x-reusables.table-header :tableHead="$tableHead"/>
                        <tbody>
                            @if(isset($listingData) && isset($listingData[0]))
                            @foreach ($listingData as $data)
                            <tr>
                                <td>{{$data->phone}}{{!empty($data->sec_phone)?', '.$data->sec_phone:''}} {!!$data->is_phone_verified?'<i title="Phone Verified" class="fa fa-check-circle text-warning"></i>':''!!} {!!$data->is_aadhar_verified?'<i title="Aadhar Verified" class="fa fa-check-circle text-secondary"></i>':''!!} {!!$data->is_dl_verified?'<i title="DL Verified" class="fa fa-check-circle text-success"></i>':''!!}</td>
                                <td>{{$data->name}}</td>
                                <td>{{$data->email}}</td>
                                <td><span class="d-none">{{$data->createdDateTimeStr}}</span>{{$data->createdDateTime}}</td>
                                <td>
                                    {{-- <a href="{{ url(config('app.admin_prefix')."manage-customer/$data->id") }}" class="btn btn-outline-secondary btn-sm edit" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a data-id="{{ $data->id }}" data-module="customer" data-name="{{ $data->name }}" href="javascript:void(0);" class="btn btn-outline-danger btn-sm delete-record js-swal-confirm " title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a> --}}
                                    <x-reusables.action-buttons :id="$data->id" module="customer"
                                        :name="$data->name" />
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
{{-- add vehicle modal end --}}
<div id="verify-customer-modal" class="modal fade" tabindex="-1" aria-labelledby="verify-customerLabel" aria-hidden="true" data-bs-scroll="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verify-customerLabel">Verify Customer Phone</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 ">
                    <div class="col-sm-8 offset-sm-2">
                        <label for="" class="form-label">Phone Verification OTP</label>
                        <input class="form-control" type="text" value="" name="" id="customer_otp">
                    </div>
                </div>
                <div class="row mb-3 ">
                    <input type="hidden" name="" id="verify_customer_id">
                    <div class="col-sm-12 mt-3 text-center">
                        <button type="button" onclick="send_otp_customer_phone()" class="btn btn-primary waves-effect waves-light me-1">Send Verification OTP</button>
                        <button type="button" onclick="verify_customer_phone()" class="btn btn-primary waves-effect waves-light me-1">Verify & Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
