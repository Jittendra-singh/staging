@php
    $name_counter = 0 ;
    $is_waiver_allowed = $event->waiver_form_type ? true : false;
@endphp
@foreach ($ticketbooking as $booking)
    @php
        $ticket_id = $booking['id'];
        $ticket_count = $booking['count'];
        $ticket_name  = $booking['name'];
        $ticket_price = $booking['price'];
        
    @endphp
    <div class="ticket-box mb-1 border-bottom">
        <div class="col-12 text-left mt-3 mb-1 border-bottom bp-title">
            <!-- <h4 class="px-0">Ticket - {{$ticket_name}} - {{config('app.currency').$ticket_price}}/ per ticket</h4> -->
            <h4 class="px-0">Attendee Information ({{ $ticket_name }})</h4>
        </div>
        @for ($i = 1 ; $i <= $ticket_count ; $i++)
            @php
                $name_counter = $name_counter + 1 ;
            @endphp
            <div class=" pb-4 parent-container" data-ticket="1" data-ticketid="{{$ticket_id}}">
                <div class="row">
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group mt-4">
                            <label class="form-label">Email Address*</label>
                            <input class="form-control checkout-email email_address attendee-email" name="ticket[{{$name_counter}}][email_address]" type="text" placeholder="Enter email" required value="">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group mt-4">
                            <label class="form-label">First Name*</label> 
                            <input type="hidden" name="ticket[{{$name_counter}}][id]" class="ticket_id" value="{{$ticket_id}}">
                            <input class="form-control first_name" name="ticket[{{$name_counter}}][first_name]" type="text" placeholder="Enter first name" required value="">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group mt-4">
                            <label class="form-label">Last Name*</label>
                            <input class="form-control last_name" name="ticket[{{$name_counter}}][last_name]" type="text" placeholder="Enter last name" value="" required>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="form-group mt-4">
                            <label class="form-label">Attendee Phone* <span class="fs-7 text-info d-none">Start with +1</span></label>
                            <input class="form-control phone usphonemask" name="ticket[{{$name_counter}}][phone]" type="text" placeholder="Enter phone" value="" required>
                        </div>
                    </div>
                    @if($event->ticket_questions)
                        <div class="col-12">
                            <div class="form-group mt-4">
                                <label class="form-label">{{strip_tags($event->ticket_questions)}}</label>
                                <input class="form-control answer" name="ticket[{{$name_counter}}][answer]" type="text" placeholder="Enter answer here" value="" required>
                            </div>
                        </div>
                    @endif
                </div>
                @if($is_waiver_allowed)
                    <div class="row waiver-form-div">
                        <div class="col-lg-12 col-md-4">
                            <div class="form-group mt-4">
                                <label class="form-label mt-4" for="is-18-checkbox-{{$name_counter}}">
                                    <input type="checkbox" name="ticket[{{$name_counter}}][is_under_18]" class="form-check-input waiver_check_age is_under_18" id="is-18-checkbox-{{$name_counter}}" value="1"> This volunteer is under 18 years of age.
                                </label>
                            </div>  
                        </div>
                        <div class="col-lg-4 col-md-4 waiver-guardian d-none">
                            <div class="form-group mt-4">
                                <label class="form-label">Guardian Name*</label>
                                <input class="form-control guardian_name is_under_18" name="ticket[{{$name_counter}}][waiver_handler_name]" type="text" placeholder="Enter guardian name">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 waiver-guardian d-none">
                            <div class="form-group mt-4">
                                <label class="form-label">Guardian Email*</label>
                                <input class="form-control guardian_email is_under_18" name="ticket[{{$name_counter}}][waiver_handler_email]" type="text" placeholder="Enter guardian email">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endfor
    </div>
@endforeach