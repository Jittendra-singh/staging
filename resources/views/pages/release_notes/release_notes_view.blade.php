@php
    $pageName = 'Release Notes';
    $createUrl = route('show-release-note');
    $tableHead = ['Version', 'Title', 'Release Date', 'Publish Status', 'Action'];
@endphp

@extends('layouts.backend_new', ['pageName' => $pageName, 'parentPageName' => $pageName])

@section('css')
    <x-reusables.datatablecss />
@endsection

@section('js')
    <x-reusables.datatablejs />
@endsection

@section('buttons')
    <x-reusables.fab-button :title="'Release Note'" :module="$module" :url="$createUrl" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <x-reusables.frontend.alert />

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET">
                    <div class="d-flex flex-wrap justify-content-start gap-3 align-items-end">
                        <div class="form-group">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search release notes...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Version</label>
                            <input type="text" class="form-control" name="version" value="{{ request('version') }}" placeholder="v2.4.0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Release Date</label>
                            <input type="date" class="form-control" name="release_date" value="{{ request('release_date') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('index-release-notes') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table id="custom_datatable" sort="2" class="table table-bordered nowrap w-100 volunteer-records" dt-responsive>
                <x-reusables.table-header :tableHead="$tableHead" />
                <tbody>
                    @forelse ($listingData as $data)
                        @php
                            $typeLabels = [1 => 'Feature', 2 => 'Improvement', 3 => 'Maintenance'];
                            $shortDescription = \Illuminate\Support\Str::limit(strip_tags($data->description), 70);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('show-release-note', ['placeholder' => $data->encrypted_id]) }}" class="fw-semibold">
                                    {{ $data->version }}
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $data->title }}</div>
                                <small class="text-muted d-block">{{ $shortDescription }}</small>
                                <small class="badge bg-light text-dark mt-1">{{ $typeLabels[$data->type] ?? 'Feature' }}</small>
                            </td>
                            <td>
                                {{ $data->release_date ? \Carbon\Carbon::parse($data->release_date)->format(config('app.dateFormat')) : 'N/A' }}
                            </td>
                            <td>
                                <!-- @if($data->status)
                                    <span class="badge bg-success-subtle text-success">Published</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                @endif -->
                                <x-reusables.list-switch :data="$data" field="status" id="status" url="{{ route('toggle-release-note-status', ['placeholder' => $data->encrypted_id]) }}"/>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-start action-button">
                                    <a href="{{ route('show-release-note', ['placeholder' => $data->encrypted_id]) }}">
                                        <i class="fs-5 mdi mdi-pencil"  title="Edit Record"></i>
                                    </a>
                                    <a data-id="{{ $data->encrypted_id }}" data-module="release-notes" data-name="{{ $data->title }}" class="delete-record js-swal-confirm" href="javascript:void(0);">
                                        <i class="fs-5 mdi mdi-delete" title="Delete Record"></i>
                                    </a>
                                </div>
                                <!-- <div class="dropdown dropstart">
                                    <button class="btn btn-link font-size-16 text-primary py-0 px-0 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu drop-menu-end">
                                        @can('update', $module)
                                            <li class="dropdown-item">
                                                <a href="{{ route('show-release-note', ['placeholder' => $data->encrypted_id]) }}">
                                                    <i class="mdi mdi-pencil fs-5"></i>
                                                    <span class="text-muted">Edit</span>
                                                </a>
                                            </li>
                                            <li class="dropdown-item">
                                                <a href="{{ route('toggle-release-note-status', ['placeholder' => $data->encrypted_id]) }}">
                                                    <i class="mdi {{ $data->status ? 'mdi-eye-off' : 'mdi-eye' }} fs-5"></i>
                                                    <span class="text-muted">{{ $data->status ? 'Unpublish' : 'Publish' }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('delete', $module)
                                            <li class="dropdown-item">
                                                <a data-id="{{ $data->encrypted_id }}" data-module="release-notes" data-name="{{ $data->title }}" class="delete-record js-swal-confirm text-danger" href="javascript:void(0);">
                                                    <i class="mdi mdi-delete fs-5"></i>
                                                    <span>Delete</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div> -->
                            </td>
                        </tr>
                    @empty
                        <x-reusables.empty-data :colspan="count($tableHead)" />
                    @endforelse
                </tbody>
            </table>

            <div class="mt-2">
                {{ $listingData->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
