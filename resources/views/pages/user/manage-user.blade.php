@php
    $is_update_form = $formData ? true : false;
    $action_url     = $is_update_form ? route("update-user" , ['user_placeholder' => $formData->id]) : route("create-user");
    $role_id        = old('role_id', $formData->role_id ?? '');
    $name           = old('name', $formData->name ?? '');
    $email          = old('email', $formData->email ?? '');
    $phone          = old('phone', $formData->phone ?? '');
    $pageName       = "Manage User";
@endphp
@extends('layouts.backend_new', ['pageName' => "Manage Backend User" , 'parentPageName' => "Users", 'parentRoute' => "users",'breadcrumb' => [
    ['name' => 'Backend Users', 'route' => route('index-user')],
    ['name' => 'Manage Backend User', 'route' => ''],
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
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input class="form-control textonlymask" type="text" value="{{$name}}" name="name" id="name" required>
                        </div>
                        <div class="col-sm-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input class="form-control" type="email" value="{{$email}}" name="email" id="email" required autocomplete="off">
                        </div>
                        <div class="col-sm-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input class="form-control usphonemask" type="text" value="{{$phone}}"  name="phone" id="mobile" >
                        </div>
                        @if (!$is_update_form)   
                        <div class="col-sm-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input autocomplete="new-password" required class="form-control" type="password" value="" name="password" id="password"> 
                        </div> 
                        @endif
                        <div class="col-sm-3 mt-2">
                            <label for="role_id" class="form-label font-size-13 text-muted">Role</label>
                            <select class="form-select" data-trigger name="role_id" id="role_id" placeholder="This is a search placeholder">
                                @foreach ($roles as $role)
                                    <option value="{{$role->id}}" {{decrypt($role->id) == $role_id ? "selected" : ""}}> {{ucfirst($role->role_name)}} </option>
                                @endforeach
                            </select>
                        </div>
                        <x-reusables.form-footer route="index-user" action="{{$is_update_form ? 'update' : 'create'}}" module="user" />
                    </div>
                </form>
            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection
