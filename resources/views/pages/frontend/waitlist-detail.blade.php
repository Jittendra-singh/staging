@extends('layouts.frontend')

@section('content')
<div class="container py-5" style="min-height: 60vh; margin-top: 100px;">
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-5 text-center">
            <h1 class="fw-bold mb-3">Waitlist Detail Page</h1>
            <p class="text-secondary mb-4">This is a placeholder page for event ID: {{ $event_id }}</p>
            <a href="{{ route('front-dashboard') }}" class="btn text-white px-4 py-2 fw-semibold" style="border-radius: 8px; background-color: #4f46e5;">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection
