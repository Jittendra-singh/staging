@php
    $is_update_form = $formData ? true : false;

    $action_url = $is_update_form
        ? route('update-release-note', ['placeholder' => $formData->encrypted_id])
        : route('create-release-note');

    $title = old('title', $formData->title ?? '');
    $version = old('version', $formData->version ?? '');
    $type = old('type', $formData->type ?? 1);
    $description = old('description', $formData->description ?? '');
    $status = old('status', $formData->status ?? 0);
    $release_date = old('release_date', !empty($formData->release_date) ? \Carbon\Carbon::parse($formData->release_date)->format('Y-m-d') : '');
@endphp

@extends('layouts.backend_new', [
    'pageName' => 'Manage Release Note',
    'parentPageName' => 'Release Notes',
    'parentRoute' => route('index-release-notes'),
    'breadcrumb' => [
        ['name' => 'Release Notes', 'route' => route('index-release-notes')],
        ['name' => 'Manage Release Note', 'route' => ''],
    ],
])

@section('css')
    <x-reusables.advanceformcss />
    <style>
        .release-option {
            border: 1px solid #d8deea;
            border-radius: .35rem;
            background: #fff;
            cursor: pointer;
            transition: all .15s ease;
        }

        .release-option:hover {
            border-color: #9aa7ff;
        }

        .btn-check:checked + .release-option {
            border-color: #635bff;
            background: #f3f1ff;
            box-shadow: inset 0 0 0 1px #635bff;
        }

        .release-option-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #f4f6fb;
            color: #6b7280;
        }

        .btn-check:checked + .release-option .release-option-icon {
            background: #e8e5ff;
            color: #635bff;
        }

        .release-option-subtitle {
            font-size: 10px;
            letter-spacing: .04em;
        }

        .release-description-toolbar {
            border: 1px solid #ced4da;
            border-bottom: 0;
            border-radius: .25rem .25rem 0 0;
            background: #f8f9fa;
        }

        .release-description-toolbar + textarea {
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
@endsection

@section('js')
    <x-reusables.advanceformjs />
    <script src="{{ asset('js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/validation.js') }}"></script>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-sm-12">
        <x-reusables.frontend.alert />

        <form action="{{ $action_url }}" method="post" class="manageReleaseNoteForm">
            @csrf
            @if($is_update_form)
                @method('PUT')
            @endif

            <div class="card">

                <div class="card-body">
                    <div class="row g-3 g-lg-4">
                        <div class="col-12 col-lg-6">
                            <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ $title }}" placeholder="e.g., Performance Overhaul & Map Enhancements" required>
                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="version" class="form-label fw-semibold">Version <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="version" name="version" value="{{ $version }}" placeholder="v2.4.0" required>
                            @error('version') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Release Type <span class="text-danger">*</span></label>

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="type" id="type_feature" value="1" @checked($type == 1) required>
                                    <label class="release-option d-flex align-items-center gap-2 h-100 p-3" for="type_feature">
                                        <span class="release-option-icon d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                            <i class="mdi mdi-star-outline"></i>
                                        </span>
                                        <span class="d-block overflow-hidden">
                                            <span class="d-block fw-bold text-dark text-truncate">Feature</span>
                                            <span class="release-option-subtitle d-block text-muted text-uppercase text-truncate">New functionality</span>
                                        </span>
                                    </label>
                                </div>

                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="type" id="type_improvement" value="2" @checked($type == 2) required>
                                    <label class="release-option d-flex align-items-center gap-2 h-100 p-3" for="type_improvement">
                                        <span class="release-option-icon d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                            <i class="mdi mdi-auto-fix"></i>
                                        </span>
                                        <span class="d-block overflow-hidden">
                                            <span class="d-block fw-bold text-dark text-truncate">Improvement</span>
                                            <span class="release-option-subtitle d-block text-muted text-uppercase text-truncate">Enhanced experience</span>
                                        </span>
                                    </label>
                                </div>

                                <div class="col-12 col-md-4">
                                    <input type="radio" class="btn-check" name="type" id="type_maintenance" value="3" @checked($type == 3) required>
                                    <label class="release-option d-flex align-items-center gap-2 h-100 p-3" for="type_maintenance">
                                        <span class="release-option-icon d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                            <i class="mdi mdi-tools"></i>
                                        </span>
                                        <span class="d-block overflow-hidden">
                                            <span class="d-block fw-bold text-dark text-truncate">Maintenance</span>
                                            <span class="release-option-subtitle d-block text-muted text-uppercase text-truncate">Fixes & security</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            @error('type') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Description & Changelog <span class="text-danger">*</span></label>
                            
                            <textarea class="form-control ckeditor-classic" id="description" name="description" rows="3" >{{ $description }}</textarea>
                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-semibold">Publish Status <span class="text-danger">*</span></label>

                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <input type="radio" class="btn-check" name="status" id="status_draft" value="0" @checked($status == 0) required>
                                    <label class="release-option d-flex align-items-center gap-2 h-100 p-3" for="status_draft">
                                        <span class="release-option-icon d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                            <i class="mdi mdi-file-outline"></i>
                                        </span>
                                        <span class="d-block overflow-hidden">
                                            <span class="d-block fw-bold text-dark text-truncate">Draft</span>
                                            <span class="release-option-subtitle d-block text-muted text-uppercase text-truncate">Internal only</span>
                                        </span>
                                    </label>
                                </div>

                                <div class="col-12 col-sm-6">
                                    <input type="radio" class="btn-check" name="status" id="status_published" value="1" @checked($status == 1) required>
                                    <label class="release-option d-flex align-items-center gap-2 h-100 p-3" for="status_published">
                                        <span class="release-option-icon d-inline-flex align-items-center justify-content-center flex-shrink-0">
                                            <i class="mdi mdi-send-outline"></i>
                                        </span>
                                        <span class="d-block overflow-hidden">
                                            <span class="d-block fw-bold text-dark text-truncate">Published</span>
                                            <span class="release-option-subtitle d-block text-muted text-uppercase text-truncate">Live to users</span>
                                        </span>
                                    </label>
                                </div>
                            </div>

                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="release_date" class="form-label fw-semibold">Release Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="release_date" name="release_date" value="{{ $release_date }}" required>
                            @error('release_date') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white">
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                        <a href="{{ route('index-release-notes') }}" class="btn btn-light waves-effect">Cancel</a>

                        @can($is_update_form ? 'update' : 'create', $module)
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-content-save-outline me-1"></i>
                                Save Release Note
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
