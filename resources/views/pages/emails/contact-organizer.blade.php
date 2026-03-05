@extends('layouts.email-body')
@section('content')
    <p>Hi {{ $org_name ?? '' }},</p>
    <p>You’ve received a new contact request from a volunteer interested in connecting with you.</p>
    <hr>
    <p>Volunteer’s Contact Details:</p>
    <ul>
        <li><strong>Name:</strong> {{ $c_volunteer ?? 'Name' }}</li>
        <li><strong>Email:</strong> {{ $c_email ?? 'Email' }}</li>
        <li><strong>Phone:</strong> {{ $c_phone ?? 'Phone' }}</li>
        <li><strong>Message:</strong> {{ $c_message ?? 'Message' }}</li>
    </ul><br/>
    <p>Please do not reply back to this email. To see all your messages, visit the Inbox section on your dashboard:</p>
    <p>
        <a href="{{route('contact-lists')}}"
            style="background-color:#2196F3;color:white;padding:10px 15px;text-decoration:none;border-radius:5px;">
            Go to Inbox
        </a>
    </p>
    <p>Thank you for being part of our community!<br>
    {{ APPNAMEEMAIL }}</p>
@endsection