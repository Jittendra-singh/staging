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
				<div class="col-xl-5 col-lg-7 col-md-10">
					<div class="booking-confirmed-content">
						<div class="main-card">
							<div class="booking-confirmed-top text-center p_30">
								<div class="booking-confirmed-img mt-4">
									<div class="calendar-icon">
										<i class="fas fa-calendar-check"></i>
									</div>
								</div>
								@if(isset($url))
									<h4>Are you sure, you want to unsubscribe this alert?</h4>
									@if(isset($alert) && isset($alert->id))
									<div class="social-share mt-4 mb-0">
										<span class="share-label">Alert Name: {{ $alert->alert_name }}</span>
										<span class="share-label mb-0">Pincode: {{ $alert->zipcode }}</span>
									</div>
									@endif 
								@elseif(isset($msg)) 
									<h4>{{$msg}}</h4>
								@endif 
							</div>
							@if(isset($url))
							<div class="booking-confirmed-bottom">
								<div class="booking-confirmed-bottom-bg p_30">
									<a href="{{$url}}" class="primary-btn mt-0">Yes, unsubscribe now</a>
								</div>
							</div>
							@elseif(isset($msg))
							<div class="booking-confirmed-bottom">
								<div class="booking-confirmed-bottom-bg p_30">
									<a href="{{route('front-email-alerts')}}" class="primary-btn mt-0"><i class="fa-solid fa-bell me-1"></i>Weekly Email Alerts</a>
								</div>
							</div>
							@endif 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Body End-->
@endsection