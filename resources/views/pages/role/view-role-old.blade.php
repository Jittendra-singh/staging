@php 
$pageName = "Roles";
$tableHead = ['Role Name' , 'Created Date' ,  'Action'] ; 
@endphp

@extends('layouts.backend' , ['pageName' => "Roles"])

{{-- @section('js')
  @vite(['resources/js/pages/datatables.js'])
@endsection --}}

@section('content')
  <x-reusables.app-header pageName="{{$pageName}}" :createButton="true" module="role" />
  
  <!-- Page Content -->
  <div class="content  mx-0 w-100">
    <!-- Info -->

    <!-- END Info -->

    <!-- Dynamic Table Full -->
    <div class="block block-rounded">

      <div class="block-content block-content-full">
        <div class="table-responsive">

            <!-- DataTables init on table by adding .js-dataTable-full class, functionality is initialized in js/pages/tables_datatables.js -->
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full fs-sm">

                <x-reusables.table-header :tableHead="$tableHead"/>

                <tbody>
                    @foreach ($listingData as $data)
                        <tr>
                            <td class="whitespace-nowrap ">
                                {{ $data->role_name }}
                            </td>
                            <td class="whitespace-nowrap ">
                                {{ $data->created_at }}
                            </td>
                            {{-- <td class="whitespace-nowrap ">

                                <a href = "{{route('toggle-role-status' , ['role_placeholder' => $data->id])}}" 
                                    class=" fs-xs fw-semibold d-inline-block py-1 px-3 rounded-pill cursor-pointer {{$data->is_active ? 'bg-success-light text-success' : 'bg-danger-light text-danger'  }}">
                                        {{$data->is_active ? 'Active' : 'Inactive'}}
                                </a>
                            </td> --}}
                            <td class="whitespace-nowrap">
                                <x-reusables.action-buttons :id="$data->id" module="role"
                                    :name="$data->role_name" />
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            
        </div>
      </div>
    </div>
    <!-- END Dynamic Table Full -->
  </div>
  <!-- END Page Content -->
@endsection
