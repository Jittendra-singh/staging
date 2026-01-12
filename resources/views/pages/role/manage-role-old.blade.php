
@php
    
    $is_update_form = $formData ? true : false;
    
    $action_url = $is_update_form ? route("update-role" , ['role_placeholder' => $formData->id  ])  : route("create-role");
    
    $role_name = old('role_name', $formData->role_name ?? '');
    
    $is_active = old('is_active', $formData->is_active ?? '');
    
    $pageName = "Manage Role";

@endphp

@extends('layouts.backend' , ['pageName' => "Manage Role" , 'parentRoute' => "roles"])

{{-- @section('js')
    @vite(['resources/js/pages/datatables.js'])
@endsection --}}

@section('content')
    <x-reusables.app-header pageName="{{$pageName}}" :createButton="false" module="role" />
    <div class="content  mx-0 w-100">
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                <form action="{{ $action_url }}" method="post">
                    @csrf
                    @if ($is_update_form)
                        @method('PUT')
                    @endif

                    <div class="row justify-content-center">
                        <div class="col-6">
                            <label class="form-label" >Role Name<span class="text-danger">*</span></label>
                            <input class="form-control"
                                placeholder-m="Role Name" name="role_name" value="{{ $role_name }}" type="text" required>
                        </div>
                    </div>
                    <!-- <div class="row justify-content-center">

                        <div class="col-6 mt-4 text-center">
                            
                            <input  id="is_active" type="checkbox"
                            placeholder="Status" name="is_active" value="1" {{ $is_active ? "checked" : "" }} type="text" > 
                            <label class="form-label" for="is_active"> Active</label>
                            
                        </div>

                    </div> -->

                    <x-reusables.form-footer route="index-role" action="{{$is_update_form ? 'update' : 'create'}}" module="role" />

                </form>

            </div>
        </div>
    </div>
@endsection
