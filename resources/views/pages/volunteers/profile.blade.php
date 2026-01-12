@php
    $pageName = 'Profile Details';
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])
@section('css')
    <x-reusables.datatablecss />
@endsection
@section('js')
    <x-reusables.datatablejs />
@endsection
@section('content')