@php
$outPages = true;
@endphp
@extends('layouts.frontend')

@section('content')
<!-- Body Start-->
<div class="wrapper" style="background-color: #f5f5f5cf">
	<div class="event-dt-block p-80">
		<div class="container">
			<div class="row justify-content-center">
                <h3 class="text-center py-3">{{ $event->event_name }}</h3>
            </div>
			<div class="row justify-content-center">
                @if(isset($event) && isset($event->id) && isset($event->after_event_images) && !empty($event->after_event_images))
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
                    @else 
                        <div class="col-sm-6">
                            <div class="booking-confirmed-content">
                                <div class="main-card">
                                    <div class="booking-confirmed-top text-center p_30">
                                        <h5>No image uploaded for the cleanup report.</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else 
                    <div class="col-sm-6">
                        <div class="booking-confirmed-content">
                            <div class="main-card">
                                <div class="booking-confirmed-top text-center p_30">
                                    <h5>No image uploaded for the cleanup report.</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
			</div>
		</div>
	</div>
</div>
<!-- Body End-->
@endsection