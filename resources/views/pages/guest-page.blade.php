@php
    $pageName = config('app.app_name');
@endphp

@extends('layouts.backend')

@section('content')

    <x-reusables.app-header pageName="{{ $pageName }}" :createButton="false" module=""  />

    <!-- Page Content -->
    <div class="content">
        <h4 class="text-center text-capitalize">Hello {{auth()->user()->name}} ! Hope you are doing great.</h4>
    </div>
    <!-- END Page Content -->

@endsection
