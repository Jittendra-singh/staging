@php
    $outPages = true;
    $is_rsvp_submitted = (!empty($ticket->rsvp_confirmation) && !empty($ticket->rsvp_filled_at))?true:false;
    $is_waiver_submitted = (!empty($ticket->waiver_filled_at) && !empty($ticket->signature))?true:false;
    $is_event_datetime_exceed = (strtotime($event->start_datetime)<strtotime(date('Y-m-d H:i:s')))?true:false;
@endphp
@extends('layouts.frontend')
@section('content')
    <!-- Body Start-->
    <div class="wrapper">
        <main class="main">
            <div class="container">
                @if($event->waiverFormContentFilter())
                <div class="content-grid mt-5">
                    <x-reusables.frontend.alert />
                    <div class="waiver-form">
                        <h2>Event Waiver Form</h2>
                        <p>
                            Please read the following waiver carefully before signing.
                        </p>
                        <div class="waiver-terms mb-4 p-3 border rounded ">
                            {!! $waiverTerms !!}
                        </div>
                        <form id="waiver-form-id" method="POST" action="{{ route('submit-waiver-form', ['ticket' => encrypt($ticket->id)]) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="event_name" class="form-label">Event Name</label>
                                <div>{{ $eventName ?? '' }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Event Date</label>
                                <div>{{ $eventDateTime ?? '' }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Event Location</label>
                                <div>{{ $eventVenue ?? '' }}</div>
                            </div>
                            <div class="mb-3">
                                <label for="signer_name" class="form-label">Your Name</label>
                                <div>{{ $signerName ?? '' }}</div>
                            </div>
                            @if(isset($ticket->children) && $ticket->children->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label"><strong>Accompanying Children (Under 18):</strong></label>
                                    <ul>
                                        @foreach($ticket->children as $child)
                                            <li>{{ $child->child_name }} (Age {{ $child->child_age }})</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="date_signed" class="form-label">Date</label>
                                <div>{{ \Carbon\Carbon::now()->toDateString() }}</div>
                            </div>
                            @if($is_waiver_submitted)
                                <span class="badge text-bg-success">You have submitted the waiver form.</span>
                            @else 
                                <div class="mb-3">
                                    <label class="form-label">Signature</label>
                                    <br>
                                    <!-- <canvas id="signature-pad" width="400" height="150" style="border:1px solid #ccc; background:#fff;"></canvas>
                                    <input type="hidden" name="signature" id="signature">
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" id="clear-signature">Clear</button> -->

                                    <input type="hidden" name="signature" id="js-signature-input">
                                    <div class='js-signature' data-width="450" data-height="200"></div>
                                    <button onclick="clearSignature()" type="button" class="btn btn-secondary btn-sm mt-2" id="clear-js-signature">Clear</button>
                                </div>
                                <button type="button" onclick="submitWaiverForm()" {{ (!$is_rsvp_submitted)?'disabled':'' }} class="btn btn-primary mb-2">Submit Waiver</button>
                            @endif
                        </form>
                    </div>

@push('script')
<script src="{{ asset('assets_front/js/jq-signature.min.js') }}"></script>
<script>
    var is_signature_done = false;
    $('.js-signature').jqSignature();
    $('.js-signature').on('jq.signature.changed', function() {
        $('#js-signature-input').val($('.js-signature').jqSignature('getDataURL'));
        is_signature_done = true;
    });
    function clearSignature(){
        $('.js-signature').jqSignature('clearCanvas');
    }
    function submitWaiverForm(){
        if(!is_signature_done){
            showError('Please provide your signature before submitting the form.', 'error');
        } else {
            $('#waiver-form-id').submit();
        }
    }
</script>

                    <!-- <script>
                        const canvas = document.getElementById('signature-pad');
                        const signatureInput = document.getElementById('signature');
                        const clearBtn = document.getElementById('clear-signature');
                        let drawing = false;
                        let ctx = canvas.getContext('2d');

                        canvas.addEventListener('mousedown', function(e) {
                            drawing = true;
                            ctx.beginPath();
                            ctx.moveTo(e.offsetX, e.offsetY);
                        });

                        canvas.addEventListener('mousemove', function(e) {
                            if (drawing) {
                                ctx.lineTo(e.offsetX, e.offsetY);
                                ctx.stroke();
                            }
                        });

                        canvas.addEventListener('mouseup', function() {
                            drawing = false;
                            signatureInput.value = canvas.toDataURL();
                        });

                        canvas.addEventListener('mouseleave', function() {
                            drawing = false;
                        });

                        clearBtn.addEventListener('click', function() {
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            signatureInput.value = '';
                        });

                        // On form submit, ensure signature is captured
                        document.querySelector('form').addEventListener('submit', function(e) {
                            if (!signatureInput.value) {
                                signatureInput.value = canvas.toDataURL();
                            }
                        });
                    </script> -->
                    @endpush
                </div>
                @endif
            </div>
        </main>
    </div>
@endsection