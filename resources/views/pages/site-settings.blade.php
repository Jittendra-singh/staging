
@php
    $formData = [];
    $is_update_form = $formData ? true : false;
    $action_url = $is_update_form ? url(config('app.admin_prefix') . "manage-role/$formData->id") : url(config('app.admin_prefix') . 'manage-role');
    $role_name = old('role_name', $formData->role_name ?? '');
    $is_active = old('is_active', $formData->is_active ?? '');
    $pageName = "Site Settings";

@endphp

@extends('layouts.backend' , ['pageName' => "Site Settings"])

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
                            <label class="form-label" >Class Fees</label>
                            <input class="form-control"
                                value="$90" name="role_name" value="{{ $role_name }}" type="text" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" >Date Format</label>
                            <input class="form-control"
                                value="d M,Y" name="role_name" value="{{ $role_name }}" type="text" required>
                        </div>
                    </div>
                    
                    <div class="row justify-content-center mt-4">
                        <div class="col-6">
                            <label class="form-label" >Site logo</label>
                            <input class="form-control"
                                 type="file" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label" >Site Name</label>
                            <input class="form-control"
                                value="BYOD" name="role_name" value="{{ $role_name }}" type="text" required>
                        </div>
                    </div>
                    

                    <div class="d-flex justify-content-center space-x-2 mt-4">
                        <a href-remove="{{ url(config('app.admin_prefix') . 'roles') }}"
                            class="btn min-w-[7rem] border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                            Cancel
                        </a>
                        <button type="button"
                            class="btn min-w-[7rem] bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                            Save
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
