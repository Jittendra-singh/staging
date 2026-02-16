@php
    $outPages = isset($outPages) ? true : false;
@endphp
@extends('layouts.frontend')
@section('content')
    <div class="form-wrapper">
        <div class="app-form">
            <div class="app-form-sidebar">
                <div class="sidebar-sign-logo">
                    {{-- <img src="images/sign-logo.svg" alt=""> --}}
                </div>
                <div class="sign_sidebar_text">
                    <h1>The Easiest Way to Create Events and Sell More Tickets Online</h1>
                </div>
            </div>
            <div class="app-form-content justify-content-center">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-7 col-lg-10 col-md-7">
                            <div class="registration">
                                <h2>Reset Password</h2>
                                <form class="resetPasswordValidateform" action="{{route('updatePassword')}}" method="post">
                                    @csrf
                    	            <x-reusables.frontend.alert />
                                    <div class="col-12 mb-3">
                                        <input type="hidden" name="token" value="{{$token}}">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control h_50" name="password" id="password" placeholder="Password" required />
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control h_50" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required />
                                    </div>
                                    <input type="submit" value="Reset Password" class="primary-btn w-fit " >
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- <section class="bg-white">
    <section class="sponsorship-banner-section">
        <div class="container-fluid wrapper ">
            <div class="breadcrumb  pt-3">
                <ul class="d-inline-flex">
                    <li><a href="{{route('home')}}" class="nav-primary">Home</a></li>
                    <li class="px-2">></li>
                    <li>Register</li>
                </ul>
            </div>
            <h1>Reset Password</h1>
        </div>
    </section>
    <section class="py-5 signup">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-5">
                    <div class="row align-items-center">
                        <form class="validate-form" action="{{route('updatePassword')}}" method="post">
                            @csrf
                            <div class="col-12 mb-3">
                                <input type="hidden" name="token" value="{{$token}}">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required />
                            </div>
                            <div class="col-12 mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required />
                            </div>
                            <input type="submit" value="Reset Password" class="btn btn-primary">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section> -->
@endsection
@push('script')
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/validation.js')}}"></script>
@endpush