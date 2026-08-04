<div class="modal fade" id="editRegistraionStatus" tabindex="-1" aria-labelledby="editRegistraionStatusLabel"
    aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRegistraionStatusLabel">Update Volunteer Status</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                        class="uil uil-multiply"></i></button>
            </div>
            <div class="modal-body">
                <div class="model-content main-form">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="form-group mt-4">
                                <label class="form-label">Registration status*</label>
                                <select name="ticket-status" class="form-select" id="">
                                    <option value="approved">Approved</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="primary-btn w-fit"
                    data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="primary-btn w-fit">Update</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade detailed-modal-popup" id="registrationNotes" tabindex="-1" aria-labelledby="registrationNotesLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationNotesLabel">Add Notes</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                        class="uil uil-multiply"></i></button>
            </div>
            <div class="modal-body">
                <div class="model-content main-form">
                    <div class="main-card mt-4 registration-notes-div">
                        
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <form action="{{route('front-post-registration-notes')}}" method="POST">
                                @csrf
                                <div class="form-group mt-4">
                                    <label class="form-label">Add Notes</label>
                                    <textarea name="ticket_order_notes" id="" cols="30" class="form-control" rows="10"></textarea>
                                    <input type="hidden" name="ticket_order_id" class="ticket-order-id">
                                </div>
                                <div class="d-flex mt-4 justify-content-center gap-4">
                                    <button type="button" class="primary-btn w-fit"
                                        data-bs-dismiss="modal">Cancel</button>
                                    <button class="primary-btn w-fit">Update</button> 
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>
<div class="modal fade detailed-modal-popup" id="registrationDetails" tabindex="-1" aria-labelledby="registrationDetailsLabel"
    aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrationDetailsLabel">Ticket Details</h5>
                <button type="button" class="close-model-btn" data-bs-dismiss="modal" aria-label="Close"><i
                        class="uil uil-multiply"></i></button>
            </div>
            <div class="modal-body">
                <div class="model-content main-form">
                    <div class="container-fluid">
                        <div class="row mt-4 mb-4">
                            <div class="col-12">
                                <h5 class=" mb-3">Attendee Information:</h5>
                                <hr class="mt-0 mb-3">
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Full name :</div>
                                    <div class="col-md-9 registration-full-name"> </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Email address :</div>
                                    <div class="col-md-9 registration-email-address"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Phone Number :</div>
                                    <div class="col-md-9 registration-phone-number"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Event :</div>
                                    <div class="col-md-9 registration-event-name"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Under 18 :</div>
                                    <div class="col-md-9 registration-is-under-18">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-12">
                                <h5 class=" mb-3">Ticket Information:</h5>
                                <hr class="mt-0 mb-3">
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Ticket Type :</div>
                                    <div class="col-md-9 registration-ticket-type"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Ticket Name :</div>
                                    <div class="col-md-9 registration-ticket-name"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Ticket Number :</div>
                                    <div class="col-md-9 registration-ticket-qty"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Ticket Price :</div>
                                    <div class="col-md-9">{{config('app.currency')}}<span class="registration-ticket-subtotal"></span></div>
                                </div>
                                <div class="row mb-2 ticket_question_div">
                                    <div class="col-md-3 fw-semibold text-muted "><span class="ticket_questions"></span> :</div>
                                    <div class="col-md-9 ticket_answer"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class=" mb-3">Order Information:</h5>
                                <hr class="mt-0 mb-3">
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Order ID :</div>
                                    <div class="col-md-9 registration-order-id">#</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Total Tickets :</div>
                                    <div class="col-md-9 registration-ticket-quantity"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Order Total :</div>
                                    <div class="col-md-9">{{config('app.currency')}}<span class="registration-order-total"></span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-3 fw-semibold text-muted">Buyer’s Name :</div>
                                    <div class="col-md-9 registration-buyer-name"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4 registration-children-section d-none">
                            <div class="col-12">
                                <h5 class="mb-3">Accompanying Children:</h5>
                                <hr class="mt-0 mb-3">

                                <ul class="registration-children-list mb-0"></ul>
                            </div>
                        </div>
                        <div class="row mb-2 waiver-div">
                            <div class="col-12">
                                <h5 class=" mb-3">Waiver Information:</h5>
                                <hr class="mt-0 mb-3">
                                <div class="row mb-2 waiver-div">
                                    <div class="col-md-3 fw-semibold text-muted">Waiver Signed By :</div>
                                    <div class="col-md-9"><span class="waiver_handled_by"></span></div>
                                </div>
                                <div class="row mb-2 waiver-div">
                                    <div class="col-md-3 fw-semibold text-muted">Waiver Signed At :</div>
                                    <div class="col-md-9"><span class="waiver_filled_at"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="primary-btn w-fit"
                    data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('style')
    <style>
        #contentop {
            width: 600px;
            height: 300px;
            border: 2px solid #333;
            border-radius: 10px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            font-family: Arial, sans-serif;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            margin: 20px auto;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 2px dashed #666;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .ticket-header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 24px;
        }
        .ticket-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 14px;
            color: #34495e;
        }
        .event-name{
            /* display: grid; */
            /* grid-template-columns: 1fr 1fr; */
            gap: 10px;
            font-size: 14px;
            text-align: center; 
            color: #34495e;
        }
        .ticket-details div, .event-name div {
            padding: 5px;
        }
        .ticket-details strong {
            color: #2c3e50;
        }
        .ticket-footer {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #7f8c8d;
        }
        /* .download-ticket {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .download-ticket:hover {
            background-color: #2980b9;
        } */
    </style>
@endpush
<div style="display: none">
<div id="contentop" >
    <div class="ticket-header">
        <h2>Event Ticket</h2>
    </div>
    <div class="event-name"> <h4 id="event-name">N/A</h4></div>
    <div class="ticket-details">
        <div><strong>Venue:</strong> <span id="event-venue">N/A</span></div>
        <div><strong>Date:</strong> <span id="event-date">N/A</span></div>
        
        <div><strong>Attendee:</strong> <span id="volunteer-name">N/A</span></div>
        <div><strong>Ticket Type:</strong> <span id="ticket-type">N/A</span></div>
        <div><strong>Ticket Number:</strong> <span id="registration-id">N/A</span></div>
        <div><strong>Order ID:</strong> <span id="order-id">N/A</span></div>
    </div>
    <div class="ticket-footer">
        <p>Please present this ticket at the event entrance</p>
    </div>
</div>
</div>