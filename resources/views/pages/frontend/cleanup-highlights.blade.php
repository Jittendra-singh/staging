@php
$outPages = true;
@endphp
@extends('layouts.frontend')

@push('style')
<style>
    .title {
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 5px;
    }

    .meta {
        color: #718096;
        font-size: 14px;
    }

    .share-btn {
        background: #eef1f5;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 14px;
        border: none;
    }

    /* LEFT PANEL */
    .left-card {
        background: #fff;
        padding: 18px;
        border-radius: 12px;
    }

    .highlight-title {
        font-size: 13px;
        font-weight: 700;
        color: #5a67d8;
        margin-bottom: 15px;
    }

    .highlight-item {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #f8f8f8;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .icon {
        width: 36px;
        height: 36px;
        background: #edf2ff;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 16px;
    }

    .label {
        font-size: 11px;
        color: #a3a3a3;
        font-weight: 600;
    }

    .value {
        font-size: 16px;
        font-weight: 700;
        color: #2d3748;
    }

    /* IMAGE GRID */
    .image-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .img-box {
        border-radius: 12px;
        overflow: hidden;
        height: 170px;
    }

    .img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* NARRATIVE */
    .narrative h5 {
        font-weight: 700;
        margin-bottom: 10px;
    }

    .narrative p {
        color: #718096;
        font-size: 14px;
        line-height: 1.6;
    }
    .booking-confirmed-content .main-card {
        border-radius: 12px;
    }
</style>
@endpush
@section('content')
<!-- Body Start-->
<div class="wrapper" style="background-color: #f5f5f5cf">
	<div class="event-dt-block p-80">

        <div class="container py-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="title">{{ $event->event_name }}</h2>
                    <div class="meta mt-3">
                        <span><i class="fa fa-calendar me-1 text-primary"></i> {{$event->eventStartDateTime}}</span>
                        <span class="mx-2">•</span>
                        <span><i class="fa fa-map-marker me-1 text-primary"></i> {{$event->fullAddress}}</span>
                    </div>
                </div>

                <button type="button" class="btn btn-primary mt-3" onclick="copyText('{{ route('cleanup-highlights', $event->encrypted_id) }}')">
                    <i class="fa fa-link me-1"></i> Share
                </button>
            </div>

            <div class="row g-4 pt-2">

                <!-- LEFT PANEL -->
                <div class="col-lg-4">
                    <div class="left-card">

                        <div class="highlight-title">CLEANUP HIGHLIGHTS</div>

                        <div class="highlight-item">
                            <div class="icon"><i class="fa fa-trash text-primary"></i></div>
                            <div>
                                <div class="label">TOTAL DEBRIS</div>
                                <div class="value">{{$event->waste_collected}} lbs</div>
                            </div>
                        </div>

                        <div class="highlight-item">
                            <div class="icon"><i class="fa fa-thumbs-up text-primary"></i></div>
                            <div>
                                <div class="label">AREA CLEANED</div>
                                <div class="value">{{$event->area_cleaned}} yards</div>
                            </div>
                        </div>

                        <div class="highlight-item">
                            <div class="icon"><i class="fa fa-users text-primary"></i></div>
                            <div>
                                <div class="label">VOLUNTEERS</div>
                                <div class="value">{{$event->tickets()->active()->where('checkin',1)->count()}} Active</div>
                            </div>
                        </div>

                        @if(!empty($event->team_name))
                        <div class="highlight-item">
                            <div class="icon"><i class="fa fa-flag text-primary"></i></div>
                            <div>
                                <div class="label">TEAM NAME</div>
                                <div class="value">{{ $event->team_name }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT CONTENT -->
                <div class="col-lg-8">
                    <!-- Narrative -->
                    <div class="narrative">
                        <h5>Post Event Narrative</h5>
                        <p>{{$event->post_event_notes}}</p>
                    </div>
                    @if(isset($event) && isset($event->id) && isset($event->after_event_images) && !empty($event->after_event_images))
                    <div class="row justify-content-center my-4">
                        @php 
                        $images = !empty($event->after_event_images) ? json_decode($event->after_event_images) : null;
                        @endphp 
                        @if(!empty($images))
                            @foreach($images as $key => $image)
                                @php
                                    $imagePath = is_object($image) ? $image->path : $image;
                                @endphp
                                <div class="col-sm-6 mb-3">
                                    <div class="booking-confirmed-content">
                                        <div class="main-card" style="height: 300px; overflow: hidden; background-color: #f8f9fa;">
                                            <img src="{{ asset($imagePath) }}" 
                                                alt="" 
                                                class="w-100 h-100" 
                                                style="object-fit: contain; object-position: center;">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @endif


                </div>

            </div>

        </div>
	</div>
</div>
<!-- Body End-->
@endsection