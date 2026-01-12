@php
    $is_update_form = $formData ? true : false;
    $action_url = $is_update_form ? route("update-role" , ['role_placeholder' => $formData->id  ])  : route("create-role");
    $role_name = old('role_name', $formData->role_name ?? '');
    $is_active = old('is_active', $formData->is_active ?? '');
    $pageName = "Manage Role";
@endphp
@extends('layouts.backend_new', ['pageName' => "Manage Role" , 'parentPageName' => "Roles", 'parentRoute' => "roles",'breadcrumb' => [
    ['name' => 'Roles', 'route' => route('index-role')],
    ['name' => 'Manage Role', 'route' => ''],
]])
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
                    <x-reusables.frontend.alert />
                <form action="{{ $action_url }}" method="post">
                    @csrf
                    @if ($is_update_form)
                        @method('PUT')
                    @endif
                    <div class="row justify-content-center">
                        <div class="col-sm-4">
                            <label for="role_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input class="form-control textonlymask" type="text" value="{{$role_name}}" name="role_name" id="role_name" required>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <x-reusables.form-footer route="index-role" action="{{$is_update_form ? 'update' : 'create'}}" module="role" />
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection
