@php
    $name_counter = 0;
    $is_waiver_allowed = $event->waiver_form_type ? true : false;
@endphp

@foreach ($ticketbooking as $booking)
    @php
        $ticket_id    = $booking['id'];
        $ticket_count = $booking['count'];
        $ticket_name  = $booking['name'];
        $ticket_price = $booking['price'];
    @endphp

    <div class="ticket-box mb-1 border-bottom">
        <div class="col-12 text-left mt-3 mb-1 border-bottom bp-title">
            <h4 class="px-0">Attendee Information ({{ $ticket_name }})</h4>
        </div>

        @for ($i = 1; $i <= $ticket_count; $i++)
            @php
                $name_counter++;
            @endphp

            <div class="pb-4 parent-container" 
                 data-ticket="1" 
                 data-ticketid="{{ $ticket_id }}" 
                 data-ticket-index="{{ $name_counter }}">

                {{-- Form Type Dropdown --}}
                <div class="row mb-3 pb-2 border-bottom">
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-bold">Ticket Attendee Type</label>
                        <select class="form-select attendee-form-type" 
                                name="ticket[{{ $name_counter }}][form_type]" 
                                data-ticket-index="{{ $name_counter }}">
                            <option value="1" selected>Adult (18+ years)</option>
                            @if($is_waiver_allowed)
                                <option value="2">Youth (Under 18 with Guardian)</option>
                            @endif
                            <option value="3">Child (Under 18 years)</option>
                        </select>
                    </div>
                </div>

                {{-- Hidden ticket_id: Always submitted --}}
                <input type="hidden" 
                       name="ticket[{{ $name_counter }}][id]" 
                       class="ticket_id" 
                       value="{{ $ticket_id }}">

                {{-- SECTION A: Standard Attendee Fields (Form Type 1 & 2) --}}
                <div class="standard-attendee-fields" id="standard-fields-{{ $name_counter }}">
                    <div class="row">
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group mt-3">
                                <label class="form-label">Email Address*</label>
                                <input class="form-control checkout-email email_address attendee-email" 
                                       name="ticket[{{ $name_counter }}][email_address]" 
                                       type="email" 
                                       placeholder="Enter email" 
                                       required>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group mt-3">
                                <label class="form-label">First Name*</label> 
                                <input class="form-control first_name" 
                                       name="ticket[{{ $name_counter }}][first_name]" 
                                       type="text" 
                                       placeholder="Enter first name" 
                                       required>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group mt-3">
                                <label class="form-label">Last Name*</label>
                                <input class="form-control last_name" 
                                       name="ticket[{{ $name_counter }}][last_name]" 
                                       type="text" 
                                       placeholder="Enter last name" 
                                       required>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group mt-3">
                                <label class="form-label">Attendee Phone*</label>
                                <input class="form-control phone usphonemask" 
                                       name="ticket[{{ $name_counter }}][phone]" 
                                       type="text" 
                                       placeholder="Enter phone" 
                                       required>
                            </div>
                        </div>

                        @if($event->ticket_questions)
                            <div class="col-12">
                                <div class="form-group mt-3">
                                    <label class="form-label">{{ strip_tags($event->ticket_questions) }}</label>
                                    <input class="form-control answer" 
                                           name="ticket[{{ $name_counter }}][answer]" 
                                           type="text" 
                                           placeholder="Enter answer here">
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- SECTION B: Guardian Fields (Form Type 2 Youth) --}}
                    <div class="row guardian-fields-div d-none mt-2" id="guardian-fields-{{ $name_counter }}">
                        <div class="col-12">
                            <h6 class="text-primary mt-2">Parent / Guardian Information</h6>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-label">Guardian Name*</label>
                                <input class="form-control guardian_name"
                                       name="ticket[{{ $name_counter }}][waiver_handler_name]"
                                       type="text"
                                       placeholder="Enter guardian name"
                                       disabled>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-label">Guardian Email*</label>
                                <input class="form-control guardian_email"
                                       name="ticket[{{ $name_counter }}][waiver_handler_email]"
                                       type="email"
                                       placeholder="Enter guardian email"
                                       disabled>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION C: Child Fields (Form Type 3 Child) --}}
                <div class="child-attendee-fields d-none mt-3" id="child-fields-{{ $name_counter }}">
                    <div class="row">
                        <div class="col-lg-4 col-md-4">
                            <div class="form-group">
                                <label class="form-label">Child Full Name*</label>
                                <input class="form-control child_name"
                                       name="ticket[{{ $name_counter }}][child_name]"
                                       type="text"
                                       placeholder="Enter child's name"
                                       disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-3">
                            <div class="form-group">
                                <label class="form-label">Child Age (under 18)*</label>
                                <input class="form-control child_age"
                                       name="ticket[{{ $name_counter }}][child_age]"
                                       type="number"
                                       min="0"
                                       max="17"
                                       placeholder="e.g. 8"
                                       disabled>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-5">
                            <div class="form-group">
                                <label class="form-label">Accompanying Guardian / Adult*</label>
                                <select class="form-select accompanying-guardian-select" 
                                        name="ticket[{{ $name_counter }}][accompanying_guardian]"
                                        disabled>
                                    <option value="" disabled selected>Select Accompanying Adult</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endfor
    </div>
@endforeach