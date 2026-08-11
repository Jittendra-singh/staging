@php
    $outPages = true;
@endphp

@extends('layouts.frontend')

@section('content')
	<div class="event-dt-block p-80">
<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center p-5">
                    <div class="text-success mb-3" style="font-size: 48px;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    @if($alreadyVerified)
                        <h2 class="fw-bold">Email Already Verified</h2>
                        <p class="text-muted mb-0">
                            Your email is already verified and will receive ticket availability notifications.
                        </p>
                    @else
                        <h2 class="fw-bold">Email Verified Successfully</h2>
                        <p class="text-muted mb-0">
                            You will be notified when tickets become available.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div></div>
@endsection