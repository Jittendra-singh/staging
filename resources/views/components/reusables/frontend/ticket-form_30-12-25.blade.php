@php
    $counter = $counter ?? 'counter_placeholder';
    $setting = \App\Models\Setting::first();
@endphp
<div class="row ticket-form mb-3 border-bottom pb-3">
    <div class="col-md-12">
        <div class="form-group">
            <div class="row mb-2" >
                <label for="ticket_name" class="pt-3 col-sm-9">
                    Ticket Name <sup>*</sup>
                    @isset($ticket)
                            @if(isset($event) && !empty($event->id))
                            <span class="text-success ms-3">
                                Sold: {{ $ticket->approvedSold->count() ?? 0 }}
                            </span>
                            <span class="text-primary ms-3">
                                Remaining: {{ $ticket->remainingTickets() }}
                            </span>
                            @endif
                    @endisset
                </label>
                <div class="col-sm-3 row text-end">
                    <div class="col-9">
                        <div class="icon-box float-end">
                            <p class="m-0 pe-3">Status</p>
                            <div class="form-group">
                                <div class="d-flex align-items-start">
                                    <label class="btn-switch m-0 me-3">
                                        <input type="checkbox" {{ isset($ticket->is_active)?($ticket->is_active==1 ? 'checked' : ''):'checked' }} class="form-ele" name="ticket[{{$counter}}][is_active]" value="1">
                                        <span class="checkbox-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(!isset($ticket) || !$ticket->approvedSold->count())
                    <div class="col-3">
                        <button class="btn btn-dark btn-sm delete-ticket mt-3" title="Remove" type="button">
                            <i class="{{isset($frontend) ? 'fa-solid fa-trash' : 'mdi mdi-trash-can' }}"></i> 
                        </button>
                    </div>
                    @endif
                </div>
            </div>
            <div class="d-flex">
                <input type="text" name="ticket[{{$counter}}][ticket_name]" value="{{@$ticket->ticket_name}}" class="form-control form-ele onlyTextNumberSplaceMask" required />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2 mt-3">
                <label for="ticket_type">Type <i data-bs-toggle="tooltip" title="Stripe Account should be connected to show paid tickets on front-end." class="fa fa-info-circle"></i></label>
                <select class="form-select form-ele mt-1 ticket_type_select" required name="ticket[{{$counter}}][ticket_type]" >
                    <option value="free" @selected(@$ticket->ticket_type == 'free')>Free</option>
                    <option value="paid" @selected(@$ticket->ticket_type == 'paid')>Paid</option>
                </select>
            </div>
            <div class="col-sm-2 mt-3">
                <label for="ticket_price">Ticket Price</label>
                <input type="number" fee_per="{{ $setting->event_commission }}" name="ticket[{{$counter}}][ticket_price]" value="{{@$ticket->ticket_price ?? 0}}" class="form-control form-ele mt-1 ticket_base_price_f"  min="0" required  {{ isset($ticket->ticket_type)?($ticket->ticket_type=='free'?'readonly':''):'readonly' }}>
            </div>
            <div class="col-sm-2 mt-3">
                <label for="available_quantity">Total Tickets*</label>
                <input type="number" step="1" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="ticket[{{$counter}}][available_quantity]" value="{{@$ticket->available_quantity}}" class="form-control form-ele mt-1" min="0" required>
            </div>
            <div class="col-sm-2 mt-3">
                <label for="max_qty_allowed_per_order">Max Tickets Per Order</label>
                <input type="number" name="ticket[{{$counter}}][max_qty_allowed_per_order]" value="{{@$ticket->max_qty_allowed_per_order}}" class="form-control form-ele mt-1" min="1" required>
            </div>
            <div class="col-sm-2 mt-3">
                <label for="ticket_sell_start_time">Sell Start Date*</label>
                <div class="loc-group position-relative">
                    <input type="text" class="form-control mt-1 form-ele tk_sell_start_date" name="ticket[{{$counter}}][sell_start_time]" value="{{@$ticket->sell_start_time}}" required />
                    <span class="absolute-icon" style="top:0;"><i class="fa-solid fa-calendar-days"></i></span>
                </div>
            </div>
            <div class="col-sm-2 mt-3">
                <label for="ticket_sell_end_time">Sell End Date*</label>
                <div class="loc-group position-relative">
                    <input type="text" class="form-control mt-1 form-ele tk_sell_end_date" name="ticket[{{$counter}}][sell_end_time]" value="{{ @$ticket->sell_end_time }}" required />
                    <span class="absolute-icon" style="top:0;"><i class="fa-solid fa-calendar-days"></i></span>
                </div>
            </div>
            <!-- <div class="form-group">
                <label for="minimum_quantity_to_sell">Min. Tickets</label>
                <input type="number" name="ticket[{{$counter}}][minimum_quantity_to_sell]" value="{{@$ticket->minimum_quantity_to_sell}}" class="form-control form-ele mt-1" min="1" required>
            </div> -->
        </div>
        <div class="row">
            <div class="col-md-4 mt-3">
                <div class="form-group">
                    <label for="pass_code">Pass Code</label>
                    <input type="text" name="ticket[{{$counter}}][pass_code]" value="{{@$ticket->pass_code}}" class="form-control form-ele mt-1" >
                </div>
                <smal style="font-size: 12px;">Volunteers must enter the Pass Code to add ticket into the cart. Leave this field blank to remove pass code lock.</smal>
            </div>
            <div class="col-md-4 mt-3">
                <div class="form-group">
                    <label for="ticket_description">Ticket Description</label>
                    <textarea name="ticket[{{$counter}}][ticket_description]" class="form-control form-ele " rows="5">{{@$ticket->ticket_description}}</textarea>
                    <input type="hidden" class="form-ele" name="ticket[{{$counter}}][ticket_id]" value="{{@$ticket->id}}" >
                    <input type="hidden" class="current_counter " value="{{$counter}}" >
                </div>
            </div>
            <div class="col-md-4 mt-3">
                <div class="main-card mt-4 pb-0">
                    <div class="bp-content pt-3 pb-0">
                        <div class="row parent-container">
                            <h6 class="m-0 p-0">Price Breakdown</h6>
                            <table class="table m-0">
                                <tr>
                                    <th><small>Base Price</small></th>
                                    <td>$<span class="form-ele" name="pb_base_price_{{ $counter }}">0</span></td>
                                </tr>
                                <tr>
                                    <th><small><span name="pb_fee_text_{{ $counter }}">Platform Fee</span> ({{$setting->event_commission}}%)</small></th>
                                    <td>$<span class="form-ele" name="pb_platform_fee_{{ $counter }}">0</span></td>
                                </tr>
                                <tr>
                                    <th><small>Total to Attendee</small></th>
                                    <td>$<span class="form-ele" name="pb_final_price_{{ $counter }}">0</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
