@php
    $tableHead = ['Name', 'Email', 'Role', 'Created Date', 'Status', 'Action'];
@endphp
@extends('layouts.backend_new', ['pageName' => 'Backend Users', 'parentPageName' => 'Backend Users'])
@section('css')
    <x-reusables.datatablecss />
@endsection
@section('js')
    <x-reusables.datatablejs />
@endsection
@section('buttons')
    <x-reusables.fab-button title="Backend User" module="user" url="{{ route('create-user') }}" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <x-reusables.frontend.alert />
            <!-- end row -->
            <div class="table-responsive">
                <table id="custom_datatable" sort="3" class="table table-bordered nowrap w-100" dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @if (isset($listingData) && isset($listingData[0]))
                            @foreach ($listingData as $data)
                                <tr>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->email }}</td>
                                    <td>{{ $data->role->role_name }}</td>
                                    <td><span class="d-none">{{ $data->createdDateTimeStr }}</span>
                                        {{ $data->created_at }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                        <x-reusables.list-switch :data="$data" field="is_active"
                                            url="{{ route('toggle-user-status', ['user_placeholder' => $data->id]) }}" />
                                    </td>
                                    <td>
                                        <x-reusables.action-buttons :id="$data->id" module="user" :name="$data->name" />
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    {{-- Reset password modal end --}}
    <x-reusables.reset-password />
@endsection
