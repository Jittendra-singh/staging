@extends('layouts.email-body')

@section('content')
    @php 
    $icallData = [
    'event_name'=>$event_title ?? 'Event Title',
    'event_start_datetime'=>$event->start_datetime ?? 'Event Start DateTime',
    'event_end_datetime'=>$event->end_datetime ?? 'Event End DateTime',
    'event_address'=>$event_location ?? 'Event Location',
    ];
    @endphp 
    <p>Hi {{ $recipient_name ?? 'Participant' }},</p>

    @if($recipient_email==$purchase_by_email)
        <p>Thank you for signing up for <b>{{ $event_title ?? 'Event' }}</b> — we're thrilled to have you on board to help clean our shores!</p>
    @else
        <p>You’ve been registered by <strong>{{ $purchase_by_name ?? 'Primary User' }}</strong> to attend the <strong>{{ $event_title ?? 'Event Title' }}</strong>.</p>
    @endif

    @if($event->waiverFormContentFilter())
        @if($is_under_18)
        <p>Since you are under 18, your RSVP and waiver have been sent to {{$waiver_name}} for approval. Your guardian will need to confirm your participation by completing the RSVP & Waiver form.</p>
        @else
        <p>To complete your participation, please sign the waiver before the event using the link below:</p>
        <p><a href="{{ $waiver_link ?? '#' }}">Sign Waiver</a></p>
        @endif
    @endif
    
    @if($event->waiverFormContentFilter())
        @if(!$is_under_18)
        <p>If you are unable to attend, please use the link below to cancel your registration:</p>
        <p><a href="{{ $cancelRegistrationUrl }}">Cancel My Registration</a></p>
        @endif
    @else 
    <p>If you are unable to attend, please use the link below to cancel your registration:</p>
    <p><a href="{{ $cancelRegistrationUrl }}">Cancel My Registration</a></p>
    @endif
    <hr>
    <p>Event Details:</p>
    <ul>
        <li><strong>Event:</strong> <a href="{{ $event_link??'#' }}">{{ $event_title ?? 'Event Title' }}</a></li>
        <li><strong>Date & Time:</strong> {{ $event_date ?? 'Event Date' }}</li>
        <li><strong>Location:</strong> <a href="https://www.google.com/maps?q={{$event->latitude}},{{$event->longitude}}">{{ $event_location ?? 'Event Location' }}</a></li>
        @if($event->bringTools->count())
            <li><strong>What to Bring:</strong> {{$event->bringTools->pluck('name')->implode(', ')}}</li>
        @else 
            <li><strong>What to Bring:</strong> - </li>
        @endif
        <li><strong>Parking Instructions:</strong> {!! $event->parking_info_details ?? '-' !!}</li>
    </ul><br/>

    @if($event->waiverFormContentFilter() && $is_under_18)
    <p>Once your guardian confirms, you’ll be all set to join us in making a difference</p>
    @endif
    <p>
        <a href="{{route('download-event-ical',$event->encrypted_id)}}"
            style="background-color:#2196F3;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
            Download iCal Event
        </a>
    </p>

    <p>We’re excited to have you join the effort — every helping hand makes a difference!</p>

    <p>Warm regards,<br>
    {{ APPNAMEEMAIL }}</p>
@endsection