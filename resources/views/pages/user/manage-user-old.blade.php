
@php

    $is_update_form = $formData ? true : false;

    $action_url     = $is_update_form ? route("update-user" , ['user_placeholder' => $formData->id]) : route("create-user");
    
    $role_id        = old('role_id', $formData->role_id ?? '');
    
    $name           = old('name', $formData->name ?? '');
    
    $email          = old('email', $formData->email ?? '');
    
    $pageName       = "Manage User";


@endphp

@extends('layouts.backend' , ['pageName' => "Manage Users" , 'parentRoute' => "users"])

@section('content')
    <x-reusables.app-header pageName="{{$pageName}}" :createButton="false" module="user" modulePlaceholder="User"  />
    <div class="content  mx-0 w-100">
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                
                <x-reusables.badge-alerts/>

                <form action="{{ $action_url }}" method="post">
                    @csrf
                    @if ($is_update_form)
                        @method('PUT')
                    @endif

                    <div class="row mb-4">
                        <div class="col">
                            <label class="form-label" >Full Name<span class="text-danger">*</span></label>
                            <input class="form-control " placeholder-m="User Name" name="name" value="{{ $name }}" type="text" required>
                        </div>
                        <div class="col">
                            <label class="form-label" >Employee Id / Email Id<span class="text-danger">*</span></label>
                            <input class="form-control" placeholder-m="Email Id" name="email" value="{{ $email }}" type="text" required>
                        </div>

                        @if (!$is_update_form)    
                            <div class="col">
                                <label class="form-label" >Password<span class="text-danger">*</span></label>
                                <input class="form-control " placeholder-m="Password" name="password" type="password" required>
                            </div>
                        @endif

                        <div class="col col-sm-12 col-md-4">
                            <label class="form-label" >Role Name<span class="text-danger">*</span></label>
                            <select name="role_id" class="form-select triggger-role" >
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}" is_manager="{{decrypt($role->id) == config('app.manager_role') ? '1' : '0'}}" {{decrypt($role->id) == $role_id ? "selected" : ""}}
                                             {{decrypt($role->id) == $role_id ? "selected" : ""}} > 
                                            {{ucfirst($role->role_name)}} 
                                        </option>
                                    @endforeach
                            </select>
                        </div>
                        
                    </div>

                    <x-reusables.form-footer route="index-user" action="{{$is_update_form ? 'update' : 'create'}}" module="user" />
                </form>
            </div>
        </div>
    </div>
@endsection
