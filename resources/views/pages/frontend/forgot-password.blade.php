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
                        <div class="col-xl-7 col-lg-6 col-md-7">
                            <div class="registration py-5">
                                <form class="js-validation-signin validateform" action="{{ route('reset.password.email') }}" method="POST">
                                    @csrf
                                    <h1>Forgot Password</h1>
                                    <h6 class="mt-0">Forgot your password ? Don't worry we will provide you a way to reset
                                        it. </h6>
                    	            <x-reusables.frontend.alert />
                                    <div class="py-3">
                                        <div class="row gap-2 mt-4">
                                            <div class="col-12">
                                                <div class="form-group w-100">
                                                    <label class="form-label">Email*</label>
                                                    <input class="form-control h_50" name="email_address" type="email"
                                                        placeholder="Enter your email" required />
                                                </div>
                                            </div>
                                            <div class="col-12">
                                              <div class="form-group w-100">
                                                  <button class="primary-btn w-fit mt-4" type="submit">
                                                      <i class="fas fa-paper-plane me-2"></i>
                                                      Get Reset Link
                                                  </button>
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
    <script src="{{asset('assets/js/validation.js')}}"></script>
@endpush