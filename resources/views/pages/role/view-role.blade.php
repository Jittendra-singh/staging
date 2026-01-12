@php
    $pageName = 'Roles';
    $tableHead = ['Role Name', 'Created Date', 'Action'];
@endphp
@extends('layouts.backend_new', ['pageName' => 'Roles', 'parentPageName' => 'Roles'])
@section('css')
    <x-reusables.datatablecss />
@endsection
@section('js')
    <x-reusables.datatablejs />
@endsection
@section('buttons')
    <x-reusables.fab-button title="Role" module="role" url="{{ route('create-role') }}" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <x-reusables.frontend.alert />
            <!-- end row -->
            <div class="table-responsive">
                <table id="custom_datatable" sort="1" class="table table-bordered nowrap w-100" dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @if (isset($listingData) && isset($listingData[0]))
                            @foreach ($listingData as $data)
                                <tr>
                                    <td>{{ $data->role_name }}</td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>
                                        <x-reusables.action-buttons :id="$data->id" module="role" :name="$data->role_name" />
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
