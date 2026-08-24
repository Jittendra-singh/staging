@extends('layouts.frontend')

@push('style')
<style>
    .release-timeline {
        position: relative;
    }

    .release-timeline:before {
        content: "";
        position: absolute;
        top: 8px;
        bottom: 8px;
        left: 9px;
        width: 2px;
        background: #e5e7f2;
    }

    .release-item {
        position: relative;
        padding-left: 42px;
        padding-bottom: 42px;
    }

    .release-item:last-child {
        padding-bottom: 0;
    }

    .release-dot {
        position: absolute;
        top: 6px;
        left: 3px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #5b4dff;
        border: 3px solid #f7f8fb;
        z-index: 1;
    }

    .release-item:not(:first-child) .release-dot {
        background: #d6d9e8;
    }

    .release-meta {
        font-size: 12px;
        color: #6b7280;
        gap: 14px;
    }

    .release-version {
        color: #5b4dff;
        font-weight: 700;
    }

    .release-type {
        font-size: 11px;
        border-radius: 999px;
        padding: 3px 9px;
        font-weight: 700;
    }

    .release-type-feature {
        background: #e8fff3;
        color: #0f9f67;
    }

    .release-type-improvement {
        background: #eef4ff;
        color: #2563eb;
    }

    .release-type-maintenance {
        background: #fff4e5;
        color: #d97706;
    }

    .release-content {
        color: #374151;
        line-height: 1.7;
        font-size: 14px;
    }

    .release-content p:last-child {
        margin-bottom: 0;
    }

    .release-search {
        width: 100%;
        max-width: 390px;
    }

    .release-search .input-group {
        height: 44px;
        border: 1px solid #d9dee8;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
    }

    .release-search .input-group-text,
    .release-search .form-control,
    .release-search .btn {
        height: 100%;
        border: 0;
        box-shadow: none;
    }

    .release-search .input-group-text {
        width: 44px;
        justify-content: center;
        background: #fff;
        color: #64748b;
    }

    .release-search .form-control {
        min-width: 0;
        padding-left: 0;
        font-size: 14px;
    }

    .release-search .btn-reset-search {
        width: 42px;
        padding: 0;
        background: #fff;
        color: #64748b;
        border-left: 1px solid #edf0f5;
    }

    .release-search .btn-reset-search:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .release-search .btn-search-submit {
        min-width: 78px;
        border-radius: 0;
    }


    @media (max-width: 767.98px) {
        .release-search {
            max-width: 100%;
            width: 100%;
        }

        .release-item {
            padding-left: 32px;
        }
    }
    /* if no data in $releaseNotes */
    @if($releaseNotes->isEmpty())
    .release-timeline:before {
        display: none;
    }
    @endif
</style>
@endpush

@section('content')
<div class="wrapper wrapper-body">
    <div class="dashboard-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-between">
                    <div class="d-main-title">
                        <h3><i class="fa-solid fa-bullhorn me-3 mb-3"></i>{{ __('messages.front_whats_new') }}</h3>
                        <p class="mb-2">{{ __('messages.front_release_notes_desc') }}</p>
                        @php
                            $breadcrumbs = [
                                [
                                    'name' => __('messages.release_notes'),
                                    'url' => route('front-release-notes')
                                ],
                            ];
                        @endphp
                        <x-reusables.frontend.breadcrumb :breadcrumbs="$breadcrumbs" />
                    </div>
                    <form method="GET" class="release-search mt-2 mt-md-0">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>

                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('messages.front_search_release_notes') }}">

                            @if(request('search'))
                                <a href="{{ route('front-release-notes') }}" class="btn btn-reset-search d-inline-flex align-items-center justify-content-center" title="{{ __('messages.front_reset_search') }}">
                                    <i class="fa fa-rotate-left"></i>
                                </a>
                            @endif

                            <button class="btn btn-primary btn-search-submit" type="submit">{{ __('messages.front_search') }}</button>
                        </div>
                    </form>
                </div>

                <div class="col-md-12">
                    <x-reusables.frontend.alert />

                    <div class="main-card mt-4">
                        <div class="dashboard-wrap-content p-4 p-lg-5">

                            <div class="release-timeline">
                                @forelse($releaseNotes as $releaseNote)
                                    @php
                                        $typeLabels = [
                                            1 => __('messages.front_feature'),
                                            2 => __('messages.front_improvement'),
                                            3 => __('messages.front_maintenance'),
                                        ];

                                        $typeClasses = [
                                            1 => 'release-type-feature',
                                            2 => 'release-type-improvement',
                                            3 => 'release-type-maintenance',
                                        ];
                                    @endphp

                                    <div class="release-item">
                                        <span class="release-dot"></span>

                                        <div class="d-flex flex-wrap align-items-center release-meta mb-2">
                                            <span class="release-version">{{ $releaseNote->version }}</span>
                                            <span>
                                                {{ $releaseNote->release_date ? \Carbon\Carbon::parse($releaseNote->release_date)->format('F d, Y') : '' }}
                                            </span>
                                            <span class="release-type {{ $typeClasses[$releaseNote->type] ?? 'release-type-feature' }}">
                                                {{ $typeLabels[$releaseNote->type] ?? __('messages.front_feature') }}
                                            </span>
                                        </div>

                                        <h4 class="fw-bold mb-3">{{ $releaseNote->title }}</h4>

                                        <div class="release-content">
                                            {!! $releaseNote->description !!}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="fa-solid fa-bullhorn fa-2x text-muted"></i>
                                        </div>
                                        <h4>{{ __('messages.front_no_release_notes_found') }}</h4>
                                        <p class="text-muted mb-0">
                                            @if(request('search'))
                                                {{ __('messages.front_try_another_search') }}
                                            @else
                                                {{ __('messages.front_published_updates_empty') }}
                                            @endif
                                        </p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="mt-4">
                                {{ $releaseNotes->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
</div>
@endsection
