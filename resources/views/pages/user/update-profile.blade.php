
@php
    $pageName       =  'Update Profile';
    $action_url     =  route("update-profile");
    
@endphp

@extends('layouts.backend' , ['pageName' => $pageName])

@section('content')
    <x-reusables.app-header pageName="{{$pageName}}" :createButton="false" module="user"  />
    <div class="content  mx-0 w-100">
        <div class="block block-rounded">
            <div class="block-content block-content-full">
                
                    <x-reusables.frontend.alert />

                <form action="{{ $action_url }}" method="post">
                    @csrf

                    <div class="row mb-4">
                        <div class="col">
                            <label class="form-label" >Full Name<span class="text-danger">*</span></label>
                            <input class="form-control " placeholder-m="User Name" name="name" value="{{ auth()->user()->name }}" type="text" required>
                        </div>
                        <div class="col">
                            <label class="form-label" >Employee Id / Email Id<span class="text-danger">*</span></label>
                            <input class="form-control" placeholder-m="Email Id" name="email" value="{{ auth()->user()->email }}" type="text" required>
                        </div>
                        
                        <div class="col">
                            <label class="form-label" >Password<span class="text-danger">*</span></label>
                            <input class="form-control " placeholder-m="Password" name="password" type="password" required>
                        </div>
                        
                    </div>

                    <x-reusables.form-footer route="dashboard" action="{{'update'}}" module="user" />

                </form>

            </div>
        </div>
    </div>
@endsection
