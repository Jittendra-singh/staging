@extends('layouts.baselayout' , ['title' => 'SIGN IN'])
@section('css')
<style>
.auth-bg{
    background-image: url(../assets/images/auth-bg.jpg);
}
</style>
@endsection
@section('content')
<div class="row g-0">
    <div class="col-xxl-3 col-lg-4 col-md-5">
        <div class="auth-full-page-content d-flex p-sm-5 p-4">
            <div class="w-100">
                <div class="d-flex flex-column h-100">
                    <div class="mb-3 mb-md-5 text-center">
                        <a href="{{route('home')}}" class="d-block auth-logo">
                            <img src="{{asset($settings->logo ?? 'assets_front/images/logo.webp')}}" alt="" height="70" width="auto">
                        </a>
                    </div>
                    <div class="auth-content my-auto">
                        <div class="text-center">
                            <p class="text-muted mt-2">Sign in to continue</p>
                        </div>
                        <form class="mt-4 pt-2" method="POST">
                          @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input name="email" type="text" class="form-control" id="email" placeholder="Enter email">
                                @error('email')
                                    <span class="text-danger mx-2">Please enter valid email.</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <label class="form-label">Password</label>
                                    </div>
                                    {{-- <div class="flex-shrink-0">
                                        <div class="">
                                            <a href="auth-recoverpw.html" class="text-muted">Forgot password?</a>
                                        </div>
                                    </div> --}}
                                </div>
                                <div class="input-group auth-pass-inputgroup">
                                    <input name="password" type="password" class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                                </div>
                                <span class="text-danger mx-1">{{session('error')}}</span>
                            </div>
                            {{-- <div class="row mb-4">
                                <div class="col">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember-check">
                                        <label class="form-check-label" for="remember-check">
                                            Remember me
                                        </label>
                                    </div>  
                                </div>
                            </div> --}}
                            <x-reusables.frontend.alert />
                            <div class="mb-3">
                                <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Log In</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end auth full page content -->
    </div>
    <!-- end col -->
    <div class="col-xxl-9 col-lg-8 col-md-7">
        <div class="auth-bg pt-md-5 p-4 d-flex">
            <div class="bg-overlay bg-primary"></div>
            <ul class="bg-bubbles">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
    </div>
    <!-- end col -->
</div>
@endsection
