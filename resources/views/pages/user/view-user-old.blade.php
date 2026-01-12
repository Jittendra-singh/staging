@php
    
    $pageName = 'Users';
    
    $tableHead = ['Full Name' , 'Employee Id /Email Id' , 'Role' ,  'Access Level', 'Created Date' , 'Status' , 'Action'];
    
@endphp

@extends('layouts.backend')
@section('content')
    <x-reusables.app-header pageName="{{ $pageName }}" :createButton="true" module="user" modulePlaceholder="User" />
    <div class="content  mx-0 w-100">
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter js-dataTable-full fs-sm">
                        <x-reusables.table-header :tableHead="$tableHead"/>
                        <tbody>
                            @foreach ($listingData as $data)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        {{ $data->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        {{ $data->email }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        {{ $data->role->role_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        {{ config('app.accessibility')[$data->accessibility] ??'' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        {{ $data->created_at }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        <a href = "{{route('toggle-user-status' , ['user_placeholder' => $data->id])}}" 
                                            class=" fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill cursor-pointer {{$data->is_active ? 'bg-success-light text-success' : 'bg-danger-light text-danger'  }}">
                                                {{$data->is_active ? 'Active' : 'Inactive'}}
                                        </a>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        <x-reusables.action-buttons :id="$data->id" module="user" :name="$data->name" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Password reset modal --}}
    <div class="modal fade" id="forgot-password-modal" tabindex="-1" aria-labelledby="forgot-password-modal"
        style="display: none" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="block block-rounded block-transparent mb-0">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Reset the Password</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm p-5">
                        <form action="{{ url(config('app.admin_prefix') . 'update-user-password') }}" method="POST">
                            @csrf
                            <input type="hidden" id="pass_user_id" name="user_id" value="">
                            <div class="row">
                                <div class="col">
                                    <label class="form-label">
                                        Password <span class="text-danger">*</span>
                                    </label>
                                        <input class="form-control" placeholder="Password" required name="password" type="password" />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col">
                                    <label class="form-label">
                                        Confirm Password <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        class="form-control"
                                        placeholder="Confirm Password" required name="password_confirmation"
                                        type="password" />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-alt-secondary me-1"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Reset password modal end --}}

@endsection

