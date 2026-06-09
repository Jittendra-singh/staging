@php
    $pageName = $title;
    $createUrl = route('show-global-type' ,  ['slug' => $global->slug , 'placeholder' => null]);
    /*$tableHead = $global->slug == 'tags' 
                    ? ['Name', 'Type',  'Created Date' , 'Status', 'Action']
                    : ['Name', 'Created Date' , 'Status', 'Action'];*/
    $tableHead = $global->slug == 'categories'
    ? ['Name', 'Image', 'Created Date' , 'Status', 'Action']
    : ['Name', 'Created Date' , 'Status', 'Action'];
@endphp
@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])
@section('css')
    <x-reusables.datatablecss />
@endsection
@section('js')
    <x-reusables.datatablejs />
@endsection
@section('buttons')
    <x-reusables.fab-button :title="$pageName" :module="$module" :url="$createUrl" />
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <x-reusables.frontend.alert />
            <div class="table-responsive">
                <table id="custom_datatable" sort="1" class="table table-bordered nowrap w-100" dt-responsive>
                    <x-reusables.table-header :tableHead="$tableHead" />
                    <tbody>
                        @forelse ($listingData as $data)
                            <tr>
                                <td>{{ $data->name }}</td>
                                @if($global->slug == 'categories')
                                    <td>
                                        @if($data->default_banner_img)
                                            <a href="{{ $data->formattedDefaultBannerImg() }}" target="_blank">
                                                <img src="{{ $data->formattedDefaultBannerImg() }}"
                                                    alt="{{ $data->name }}"
                                                    style="width:80px;height:52px;object-fit:cover;border-radius:4px;">
                                            </a>
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </td>
                                @endif
                                @if($global->slug == 'tags')
                                    <!-- <td>{{ ucfirst($data->type ? : 'event') }}</td> -->
                                @endif
                                <td>{{ $data->created_at }}</td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                    <x-reusables.list-switch :data="$data" 
                                        url="{{route('toggle-type-status' , ['slug' => $global->slug ,'placeholder' => $data->encrypted_id])}}"/>
                                </td>
                                <td>
                                    @php
                                        $deleteurl =  "global-types/{$global->slug}" ;
                                        $editurl = route('show-global-type' , ['slug' => $global->slug , 'placeholder' => $data->encrypted_id]);
                                    @endphp
                                    <x-reusables.action-buttons :deleteurl="$deleteurl" :editurl="$editurl" :id="$data->encrypted_id" :module="$module" :name="$data->name" /> 
                                </td>
                            </tr>
                        @empty
                            <x-reusables.empty-data :colspan="count($tableHead)" />
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-2" data-aos="fade-up" data-aos-duration="800">
                    {{ $listingData->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
