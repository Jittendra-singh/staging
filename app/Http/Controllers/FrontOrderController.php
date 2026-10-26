<?php

namespace App\Http\Controllers;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\EventOrder;
use App\Models\EventTicket;
use App\Models\EventOrderTicket;
use App\Models\Volunteer;
use App\Models\Cart;
use App\Models\Tag;
use App\Models\OrderCart;
use App\Models\OrderHelper;
use App\Models\CityStateZip;
use App\Models\VolunteerAlert;
use App\Models\VolunteerWidgetLog;
use App\Support\TicketFeeCalculator;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\StripeController;
use Illuminate\Validation\ValidationException;
class FrontOrderController extends Controller
{
    public function landToCheckout(){
        request()->validate([
            'order_ticket' => 'required|array',
            'order_ticket.*' => 'required|min:0',
            'event'       => 'required',
        ]);

        /**event should be active and of future */
        $event = Event::where('id', decrypt(request()->event))
                ->active()->whereNot(function($q){
                    $q->past();
                })->first();

        if(!$event){
            $this->setFlashSession(false, "Either this event does not exist or it is not active or it is past event.");
            return redirect()->route('view-events');
        }

        try{

            /**event ticket availability */
            /* if(!$event->sellTicketBooking()){
                throw new \Exception("Booking for the event {$event->event_name} is closed now.", 408);
            } */
            if($event->eventActiveTickets()->count() <= 0){
                throw new \Exception("No ticket available for booking.", 408);
            }
            $ticket_booking = [];
            $subtotal = 0;
            $total_tickets = 0;
            /**check ticket availablity */
            foreach (request()->order_ticket as $ticket_id => $ticket_count) {
                if($ticket_count <= 0)
                continue;
                $ticket_obj = EventTicket::findOrfail($ticket_id);
                $remaining_tickets = $ticket_obj->remainingTickets();
                if($remaining_tickets >= (int)$ticket_count){
                    //$ticket_price  = $ticket_obj->type == 'free' ? 0 : $ticket_obj->ticket_price ;
                    $all_price  = $ticket_obj->all_price ;
                    //$ticket_price  = $ticket_obj->payable_price ;
                    $ticket_price  = $all_price['ticket_price'] ;
                    $subtotal      = $subtotal + ($ticket_price * $ticket_count) ;
                    $total_tickets = $total_tickets + $ticket_count;
                    /* $ticket_booking[] = [
                        'id'    => $ticket_id,
                        'count' => $event->sell_tickets_individually ? 1 : $ticket_count ,
                        'name'  => $ticket_obj->ticket_name ,
                        'price' => $ticket_price ,
                        'base_price' => $all_price['ticket_base_price'] ,
                    ]; */
                    $ticket_booking[] = [
                        'id'                    => $ticket_id,
                        'count'                 => $event->sell_tickets_individually ? 1 : $ticket_count,
                        'name'                  => $ticket_obj->ticket_name,
                        'price'                 => $all_price['ticket_price'],
                        'base_price'            => $all_price['ticket_base_price'],
                        'platform_fee'          => $all_price['ticket_platform_fee'],
                        'processing_fee'        => $all_price['ticket_processing_fee'],
                        'application_fee'       => $all_price['ticket_total_application_fee'],
                        'organizer_payout'      => $all_price['tikcet_org_payout'],
                    ];

                }else{
                    throw new \Exception("Opps! {$ticket_obj->ticket_name} ticket has only {$remaining_tickets} allocations left.", 408);
                }
            }
            if(empty($ticket_booking)){
                throw new \Exception("Please select at least one ticket before proceeding to checkout.", 408);
            }
            Session::put('ticket_booking', $ticket_booking);
            $totalticket = $total_tickets;
            $finaltotal  = $subtotal; /**as we are not calculating any disount or tax here. */
        }catch(\Exception $e){
            $this->setFlashSession(false, $e->getMessage());
            return redirect()->route('view-event-details' , ['slug' => $event->slug]);
        }


        /**check user availablity */
        /**check book the ticket */
        $setting = Setting::firstOrFail();
        Session::put('checkout_event', $event);
        /* Session::put('checkout_order_details', [
                                        'sub_total' => $subtotal ,
                                        'final_total' => $finaltotal ,
                                        'total_ticket' => $totalticket
        ]); */
        Session::put('checkout_order_details', [
            'sub_total'            => $subtotal,
            'final_total'          => $finaltotal,
            'total_ticket'         => $totalticket,
            'platform_fee_per'     => (float) $setting->event_commission,
            'processing_fee_per'   => (float) $setting->payment_processing_fee_per,
            'buyer_pays_fees'      => (int) $event->charge_bear_by === 1,
        ]);
        return redirect()->route('front-checkout');

    }

    public function checkout(){

        $event            = session()->get('checkout_event');
        $order_total_details = session()->get('checkout_order_details');
        $ticket_booking = session()->get('ticket_booking');
        $setting = Setting::first();

        /* $feeAmt = 0;
        if($ticket_booking){
            foreach ($ticket_booking as $key => $ticekt) {
                $eTicket = EventTicket::where('id',$ticekt['id'])->first();
                if(isset($eTicket->id)){
                    $allPrice = $eTicket->all_price;
                    $feeAmt += $allPrice['ticket_platform_fee']*$ticekt['count'];
                }
            }
        } */
        $platformFeeAmt = 0;
        $processingFeeAmt = 0;

        foreach ($ticket_booking ?? [] as $ticket) {
            $quantity = (int) ($ticket['count'] ?? 1);

            $platformFeeAmt += ((float) ($ticket['platform_fee'] ?? 0)) * $quantity;
            $processingFeeAmt += ((float) ($ticket['processing_fee'] ?? 0)) * $quantity;
        }

        if(!$event || !$order_total_details ){
            $this->setFlashSession(false , "To access this page, please choose your tickets from the event details page.");
            return redirect()->route('view-events');
        }

        $order_total_view = view('components.reusables.frontend.order-total', [
            'setting' => $setting,
            'event' => $event ,
            //'ticket_fees' => $feeAmt ,
            'ticket_booking' => $ticket_booking ,
            'sub_total' => $order_total_details['sub_total'] ,
            'final_total' => $order_total_details['final_total'] ,
            'total_ticket' => $order_total_details['total_ticket'] ,

            'platform_fee_total' => round($platformFeeAmt, 2),
            'processing_fee_total' => round($processingFeeAmt, 2),
            'processing_fee_per' => $order_total_details['processing_fee_per'] ?? $setting->payment_processing_fee_per,
            'buyer_pays_fees' => (bool) ($order_total_details['buyer_pays_fees'] ?? false),
        ])->render();

        return view('pages.frontend.checkout' , [
            'seo_details' => (object) ['title' => 'Ticket Checkout'],
            'event'       => $event ,
            'order_total_view' => $order_total_view ,
        ]);
    }

    public function autoPopulateAttandee(){
        request()->validate([
            'email_address' => ['required', 'email', new \App\Rules\StrictEmail()],
            'is_ticket' => 'required|boolean',
            'ticket_id' => 'required',
        ]);
        /**confirm the user */
        $email_address  = request()->email_address;
        $user           = Volunteer::select('id','first_name', 'last_name', 'email_address', 'pincode', 'organisation_name', 'phone', 'status', 'address_line_1', 'city_id')->with('city_state_zip')->where("email_address", $email_address)->first();

        $is_ticket_registration_request = request()->is_ticket ? 1 : 0;

        if($user){
            /**abort if user is inactive */
            if(!$user->status){
                throw new CustomException("You can not attempt to enter {$email_address} as email as this is blocked by admin.", 408);
            }
            /* if(isset($user->city_state_zip) && isset($user->city_state_zip->id)){
                $user->city_id_option = '<option>'.$user->city_state_zip->zipcode.'</option>';
            } */

            if($is_ticket_registration_request){
                $ticket         = EventTicket::where('id', request()->ticket_id)->firstOrFail();
                $event          = Event::findOrFail($ticket->event_id);
                /**abort if organizer */
                if($event->creator_id == $user->id){
                    throw new CustomException("Since you are the organizer of this event, you cannot be an attendee of the same event.", 408);
                }
                /**abort if already in any parallel event */
                $overlapping_event = $user->approvedAttendingEvents()->where(function($query) use($event){
                    $query->where('start_datetime', '<', $event->end_datetime)
                    ->where('end_datetime', '>', $event->start_datetime);
                })->count();
                /* $overlapping_event = $user->attendingEvents()->where(function($query) use($event){
                                    $query->where('start_datetime', '<', $event->end_datetime)
                                    ->where('end_datetime', '>', $event->start_datetime);
                                })->count(); */
                if($overlapping_event > 0){
                    //throw new CustomException("{$email_address} is already registered for {$overlapping_event} event on this daterange.", 408);
                    throw new CustomException("{$email_address} already registered for another cleanup that overlaps with this event's time. Please cancel your existing RSVP or choose a non-overlapping event.", 408);
                }
            }
            return $this->generateJsonResponse(true , '' , ['user' => $user]);
        }

        return $this->generateJsonResponse(true);
    }
    public function checkBeforeCreateOrder(){
        $formdata = request()->all();
        $tickets = $formdata['ticket'] ?? [];
        $emails = [];

        foreach ($tickets as $key => $value) {
            $formType = (int) ($value['form_type'] ?? 1);

            // Child attendees (Form Type 3) do not have email addresses
            if ($formType === 3) {
                continue;
            }

            $email = trim($value['email_address'] ?? '');
            if (!empty($email)) {
                $normalizedEmail = strtolower($email);
                if (in_array($normalizedEmail, $emails)) {
                    return $this->generateJsonResponse(false, "The email address '{$email}' is repeated in the tickets. Please use unique emails for each adult attendee.");
                } else {
                    $emails[] = $normalizedEmail;
                }
            }
        }

        $ticket_booking = session()->get('ticket_booking');
        foreach ($ticket_booking as $ticket_id => $ticket_row) {
            $ticket_obj = EventTicket::findOrfail($ticket_row['id']);
            $remaining_tickets = $ticket_obj->remainingTickets();
            if ($remaining_tickets < (int) $ticket_row['count']) {
                return $this->generateJsonResponse(false, "Opps! {$ticket_obj->ticket_name} ticket has only {$remaining_tickets} allocations left. Please reallocate tickets from event details page.");
            }
        }
        return $this->generateJsonResponse(true);
    }
    public function frontCreateOrder(StripeController $stripeController,$cart_id=null,$is_cart_delete=true){
        $s_session_id = '';
        $s_payment_intent_id = '';
        if($cart_id==null){
            $rules = [
                // Billing Information
                'checkout_email_address'     => ['required', 'email', new \App\Rules\StrictEmail()],
                'checkout_first_name'        => ['max:255', new \App\Rules\RequireFieldsIfEmailExists(request()->checkout_email_address, 'checkout_first_name')],
                'checkout_last_name'         => ['max:255', new \App\Rules\RequireFieldsIfEmailExists(request()->checkout_email_address, 'checkout_last_name')],
                'checkout_pincode'           => ['max:20', new \App\Rules\RequireFieldsIfEmailExists(request()->checkout_email_address, 'checkout_pincode')],
                'checkout_organisation_name' => 'nullable|string|max:255',
                'phone'                      => ['nullable', 'string', 'max:20', new \App\Rules\ValidUSPhoneNumber()],

                // Ticket Information
                'ticket'                     => 'required|array',
                'ticket.*.id'                => 'required|integer',
            ];

            $messages = [
                'checkout_email_address.required'     => 'The billing email address is required.',
                'checkout_email_address.email'        => 'The billing email address must be a valid email.',
                'checkout_first_name.required'        => 'The first name is required.',
                'checkout_last_name.required'         => 'The last name is required.',
                'checkout_pincode.required'           => 'The zip/post code is required.',
                'checkout_organisation_name.required' => 'The company name is required.',
                'phone.required'                      => 'The phone number is required.',
                'ticket.required'                     => 'Ticket information is required.',
                'ticket.*.id.required'                => 'The ticket ID is required.',
            ];

            $allAdultEmails = [];
            $isDuplicateEmail = false;

            // Collect all Form Type 1 (Adult) ticket indexes to validate accompanying guardians
            $adultTicketIndexes = [];
            foreach (request()->input('ticket', []) as $idx => $t) {
                if ((int) ($t['form_type'] ?? 1) === 1) {
                    $adultTicketIndexes[] = (string) $idx;
                }
            }

            foreach (request()->input('ticket', []) as $index => $ticket) {
                $formType = (int) ($ticket['form_type'] ?? 1);

                if ($formType === 3) {
                    // ==================== FORM TYPE 3: CHILD (UNDER 18) ====================
                    $rules["ticket.$index.child_name"] = ['required', 'string', 'max:255'];
                    $rules["ticket.$index.child_age"]  = ['required', 'integer', 'min:0', 'max:17'];
                    $rules["ticket.$index.accompanying_guardian"] = [
                        'required',
                        function ($attribute, $value, $fail) use ($adultTicketIndexes) {
                            if (!in_array((string) $value, $adultTicketIndexes)) {
                                $fail('An attending adult must be selected as the accompanying guardian for the child.');
                            }
                        }
                    ];

                    $messages["ticket.$index.child_name.required"] = "Child full name is required for ticket #$index.";
                    $messages["ticket.$index.child_age.required"]  = "Child age is required for ticket #$index.";
                    $messages["ticket.$index.child_age.integer"]   = "Child age must be an integer for ticket #$index.";
                    $messages["ticket.$index.child_age.min"]       = "Child age must be between 0 and 17 years for ticket #$index.";
                    $messages["ticket.$index.child_age.max"]       = "Child age must be between 0 and 17 years for ticket #$index.";
                    $messages["ticket.$index.accompanying_guardian.required"] = "Please select an accompanying adult for ticket #$index.";

                } else {
                    // ==================== FORM TYPE 1 & 2: ADULT & YOUTH ====================
                    $email = $ticket['email_address'] ?? null;

                    $rules["ticket.$index.email_address"] = ['required', 'email', new \App\Rules\StrictEmail()];
                    $rules["ticket.$index.first_name"]    = ['required', 'string', 'max:255'];
                    $rules["ticket.$index.last_name"]     = ['required', 'string', 'max:255'];

                    // Check if attendee is an existing volunteer with a phone number on file
                    $existingVolunteer = !empty($email) ? Volunteer::where('email_address', strtolower(trim($email)))->first() : null;
                    $hasPhoneOnFile    = $existingVolunteer && !empty(trim($existingVolunteer->phone ?? ''));

                    if ($hasPhoneOnFile) {
                        $rules["ticket.$index.phone"] = ['nullable', 'string', 'max:20', new \App\Rules\ValidUSPhoneNumber()];
                    } else {
                        $rules["ticket.$index.phone"] = ['required', 'string', 'max:20', new \App\Rules\ValidUSPhoneNumber()];
                    }

                    $messages["ticket.$index.email_address.required"] = "Email address is required for ticket #$index.";
                    $messages["ticket.$index.email_address.email"]    = "Please enter a valid email for ticket #$index.";
                    $messages["ticket.$index.first_name.required"]    = "First name is required for ticket #$index.";
                    $messages["ticket.$index.last_name.required"]     = "Last name is required for ticket #$index.";
                    $messages["ticket.$index.phone.required"]         = "Attendee phone is required for ticket #$index.";

                    // Check duplicate email only across adults/youth
                    if (!empty($email)) {
                        $normalizedEmail = strtolower(trim($email));
                        if (in_array($normalizedEmail, $allAdultEmails)) {
                            $isDuplicateEmail = true;
                        } else {
                            $allAdultEmails[] = $normalizedEmail;
                        }
                    }

                    // Additional fields for Form Type 2 (Youth Under 18)
                    if ($formType === 2) {
                        $rules["ticket.$index.waiver_handler_name"]  = ['required', 'string', 'max:255'];
                        $rules["ticket.$index.waiver_handler_email"] = ['required', 'email', new \App\Rules\StrictEmail()];

                        $messages["ticket.$index.waiver_handler_name.required"]  = "Guardian name is required for youth attendee on ticket #$index.";
                        $messages["ticket.$index.waiver_handler_email.required"] = "Guardian email is required for youth attendee on ticket #$index.";
                        $messages["ticket.$index.waiver_handler_email.email"]    = "Please enter a valid guardian email for youth attendee on ticket #$index.";
                    }
                }
            }

            if ($isDuplicateEmail) {
                throw new CustomException("Duplicate emails found in the order. Please use unique emails for each adult attendee.");
            }

            request()->validate($rules, $messages);
            /**get session details */
            $event               = session()->get('checkout_event');
            $order_total_details = session()->get('checkout_order_details');
            $ticket_booking      = session()->get('ticket_booking');
        } else {
            $cart = Cart::find($cart_id);
            if(!isset($cart->id)){
                throw new CustomException("Something went wrong with your payment. Please contact us by email for assistance.",403,'front-checkout');
            }
            $eventOrderCheck = EventOrder::where('payment_session_id',$cart->stripe_session_id)->first();
            if(isset($eventOrderCheck->id)){
                Session::forget(['ticket_booking', 'checkout_event', 'checkout_order_details']);
                Cart::where('id',$cart_id)->delete();
                return redirect()->route('front-booking-confirmed', ['order' => encrypt($eventOrderCheck->id)]);
            }
            $event = Event::where('id', $cart->event_id)->first();
            $order_total_details = json_decode($cart->order_details, true);
            $ticket_booking = json_decode($cart->ticket_data, true);
            $request_data = json_decode($cart->request_data, true);
            request()->merge($request_data);
            $s_session_id = $cart->stripe_session_id;
            $s_payment_intent_id = $cart->stripe_payment_intent_id;
        }

        /**create or get the volunteer who is creating order */
        $creating_volunteer = Volunteer::where('email_address', request()->checkout_email_address)->first();

        //try{
            /**get event */
            $event = Event::where('id', $event->id)
                    ->active()->whereNot(function($q){
                        $q->past();
                    })->first();

            if(!$event)
                throw new CustomException("No booking for this event.");

            //DB::beginTransaction();
                $pincode = '';
                $cityStateZip = CityStateZip::find(@request()->checkout_pincode);
                if(isset($cityStateZip->id)){
                    $pincode = $cityStateZip->zipcode;
                }
                $newsletter = @request()->newsletter ? 1: 0;
                if(!$creating_volunteer){
                    $pass = @request()->checkout_password ? Hash::make(@request()->checkout_password): time();
                    $formattedPhone = @request()->phone ? formatPhoneNumber(@request()->phone): @request()->phone;
                    /**create the volunteer */
                    $creating_volunteer = Volunteer::create([
                                            'first_name'        => @request()->checkout_first_name ,
                                            'last_name'         => @request()->checkout_last_name ,
                                            'email_address'     => @request()->checkout_email_address ,
                                            'address_line_1'    => @request()->checkout_address,
                                            //'pincode'           => @request()->checkout_pincode ,
                                            'city_id'           => @request()->checkout_pincode ,
                                            'pincode'           => $pincode ,
                                            'organisation_name' => @request()->checkout_organisation_name ,
                                            'is_organiser'      => 0,
                                            'organisation_landing_slug' => null,
                                            'is_newsletter'     => $newsletter,
                                            'password'          => $pass,
                                            'phone'             => $formattedPhone
                                        ]);
                }elseif($creating_volunteer && !$creating_volunteer->status){
                            throw new CustomException("creator is already inactive by admin");
                }

                /**check if tickets are available or not */
                foreach ($ticket_booking as $ticket_id => $ticket_row) {
                    $ticket_obj = EventTicket::findOrfail($ticket_row['id']);
                    $remaining_tickets = $ticket_obj->remainingTickets();
                    if($remaining_tickets < (int)$ticket_row['count']){
                        throw new CustomException("Opps! {$ticket_obj->ticket_name} ticket has only {$remaining_tickets} allocations left. please reallocate tickets from event details page. ", 408);
                    }
                }

                $formattedPhone = @request()->phone ? formatPhoneNumber(@request()->phone): @request()->phone;
                $donation_amount = 0;
                if(request()->has('donation_amount')){
                    $donation_amount = floatval(preg_replace('/[^\d.]/', '', request()->donation_amount));
                }
                //$grand_total = round($order_total_details['final_total'] + $donation_amount, 2);

                // <newCalcBlock>
                $buyerPaysFees = (bool) ($order_total_details['buyer_pays_fees'] ?? false);
                $processingFeePer = (float) ($order_total_details['processing_fee_per'] ?? 0);

                $donationPricing = TicketFeeCalculator::donation(
                    $donation_amount,
                    $processingFeePer,
                    $buyerPaysFees
                );
                $grand_total = round(
                    (float) $order_total_details['final_total'] + (float) $donationPricing['attendee_total'],
                    2
                );
                // </newCalcBlock>

                // call stripe api to take payment before creating the order
                if($grand_total>0 && $cart_id==null){
                    $cart = new Cart();
                    $cart->event_id = $event->id;
                    $cart->request_data = json_encode(request()->all());
                    $cart->order_details = json_encode($order_total_details);
                    $cart->ticket_data = json_encode($ticket_booking);
                    $cart->save();

                    $customerData = [
                        'customer_email' => request()->checkout_email_address,
                        'customer_first_name' => request()->checkout_first_name,
                        'customer_last_name' => request()->checkout_last_name,
                    ];
                    //$stripeRes = $stripeController->createCheckout($customerData, $grand_total,$donation_amount,$cart->id);
                    $stripeRes = $stripeController->createCheckout(
                        $customerData,
                        $donationPricing,
                        $cart->id
                    );
                    //echo '<pre>';print_r($stripeRes);die();
                    if($stripeRes['status']==true){
                        $cart->stripe_session_id = $stripeRes['data']['id'];
                        $cart->save();
                        return redirect()->away($stripeRes['data']['url']);
                    } else {
                        throw new CustomException("We couldn’t start the payment process. Please try again in a moment.");
                    }
                    die();
                }
                $setting = Setting::first();
                $order_object = new EventOrder();
                $order_object->event_id         = $event->id;
                $order_object->order_number     = $this->generateRandomOrderNumber();
                $order_object->total_tickets    = $order_total_details['total_ticket'];
                $order_object->tax_pert         = 0;
                $order_object->tax_amount       = 0;
                $order_object->discount_pert    = 0;
                $order_object->discount_amount  = 0;
                $order_object->sub_total        = $order_total_details['sub_total'];
                //$order_object->donation_amount  = $donation_amount;

                $order_object->donation_amount    = $donationPricing['donation_amount'];
                $order_object->processing_fee_per = $processingFeePer;
                $order_object->platform_fee_per   = (float) ($order_total_details['platform_fee_per'] ?? 0);

                $order_object->payment_intent  = $s_payment_intent_id;
                $order_object->payment_session_id  = $s_session_id;
                //$order_object->grand_total      = $order_total_details['final_total'];
                $order_object->grand_total      = $grand_total;
                //$order_object->platform_fee_per = $setting->event_commission;
                $order_object->charge_bear_by = $event->charge_bear_by;
                $order_object->payment_type     = 'stripe';
                $order_object->payment_status   = !empty($s_session_id)?'paid':'pending';
                $order_object->creator_id       = $creating_volunteer->id;
                $order_object->billing_first_name = @request()->checkout_first_name;
                $order_object->billing_last_name = @request()->checkout_last_name;
                $order_object->billing_email = @request()->checkout_email_address;
                $order_object->billing_phone = $formattedPhone;
                $order_object->billing_company = @request()->checkout_organisation_name;
                $order_object->billing_pincode = $pincode;
                $order_object->billing_pincode_id = isset($cityStateZip->id)?$cityStateZip->id:null;
                $order_object->save();
                $order_object->refresh();
                $total_platform_fee = 0;

                // save widget log if widget_id is there
                if(request()->has('widget_id') && !empty(request()->widget_id)){
                    $widget_id = request()->widget_id;
                    $widget_log = VolunteerWidgetLog::create([
                        'widget_id' => decrypt($widget_id),
                        'log_type' => 3,
                        'reference_id' => $order_object->id,
                    ]);
                }

                /**we are keeping track from session so that in case noone can change data from input type hidden */
                $available_ticket_array = collect($ticket_booking)->pluck('id')->toArray();

                /**create ticket holders */
                $email_people_array = [];
                $waiver_people_array = [];
                $total_18_tickets = 0;
                $bookedTicketsById = collect($ticket_booking)->keyBy('id');
                $createdOrderTickets = [];
                $pendingChildTickets = [];

                // Phase 1: Create Adult (Form 1) and Youth (Form 2) tickets
                foreach (request()->ticket as $ticket_key => $ticket_arr) {
                    if(in_array($ticket_arr['id'], $available_ticket_array)){
                        $formType = (int) ($ticket_arr['form_type'] ?? 1);

                        if ($formType === 3) {
                            $pendingChildTickets[$ticket_key] = $ticket_arr;
                            continue; // Process in Phase 2
                        }

                        $savedPricing = $bookedTicketsById->get($ticket_arr['id']);
                        if (!$savedPricing) {
                            throw new CustomException('Ticket pricing data is missing. Please restart checkout.');
                        }
                        $total_platform_fee += (float) $savedPricing['platform_fee'];

                        $ticketEmail = !empty($ticket_arr['email_address'])
                            ? $ticket_arr['email_address']
                            : $creating_volunteer->email_address;

                        $ticket_volunteer = Volunteer::where('email_address', $ticketEmail)->first();
                        if(!$ticket_volunteer){
                            $ticket_volunteer = Volunteer::create([
                                                    'first_name'    => $ticket_arr['first_name'] ?? '',
                                                    'last_name'     => $ticket_arr['last_name'] ?? '',
                                                    'email_address' => $ticketEmail,
                                                    'phone'         => formatPhoneNumber($ticket_arr['phone'] ?? ''),
                                                    'is_newsletter' => $newsletter,
                                                ]);
                            $ticket_volunteer->refresh();
                        }elseif($ticket_volunteer && !$ticket_volunteer->status){
                                throw new CustomException("$ticketEmail is already inactive by admin");
                        } elseif ($ticket_volunteer && trim($ticket_volunteer->phone)==''){
                            $ticket_volunteer->phone = formatPhoneNumber($ticket_arr['phone'] ?? '');
                            $ticket_volunteer->save();
                            $ticket_volunteer->refresh();
                        }

                        $t_obj = EventTicket::findOrFail($ticket_arr['id']);
                        $waiver_handler_name = @$ticket_arr['waiver_handler_name'] ?: $ticket_volunteer->name;
                        $waiver_handler_email = @$ticket_arr['waiver_handler_email'] ?: $ticket_volunteer->email_address;
                        $is_under_18 = ($formType === 2 || isset($ticket_arr['is_under_18'])) ? 1 : 0;
                        if ($is_under_18) {
                            $total_18_tickets++;
                        }

                        $event_order_ticket_obj = EventOrderTicket::create([
                            'order_id'              => $order_object->id,
                            'volunteer_id'          => $ticket_volunteer->id,
                            'event_id'              => $event->id,
                            'ticket_id'             => $ticket_arr['id'],
                            'price'                 => $savedPricing['price'],
                            'ticket_base_price'     => $savedPricing['base_price'],
                            'ticket_platform_fee'   => $savedPricing['platform_fee'],
                            'ticket_processing_fee' => $savedPricing['processing_fee'],
                            'tikcet_org_payout'     => $savedPricing['organizer_payout'],
                            'ticket_number'         => $this->generateRandomTicketNumber(),
                            'ticket_status'         => 'approved',
                            'ticket_answer'         => @$ticket_arr['answer'],
                            'is_under_18'           => $is_under_18,
                            'waiver_handler_name'   => $waiver_handler_name,
                            'waiver_handler_email'  => $waiver_handler_email,
                            'rsvp_confirmation'     => 1,
                            'rsvp_filled_at'        => now(),
                        ]);

                        // Store for child lookup in Phase 2
                        $createdOrderTickets[$ticket_key] = $event_order_ticket_obj;

                        // Clear waitlist status if they were on it
                        \App\Models\EventWaitlist::where('event_id', $event->id)
                            ->where('ticket_id', $ticket_arr['id'])
                            ->where('email', $ticket_volunteer->email_address)
                            ->where('status', 'waiting')
                            ->update(['status' => 'purchased']);

                        if(SEND_MAIL){
                            $email_people_array[$ticket_volunteer->email_address] = [
                                'purchase_by_name'      => $creating_volunteer->first_name.' '.$creating_volunteer->last_name,
                                'purchase_by_email'     => $creating_volunteer->email_address,
                                'recipient_name'        => $ticket_volunteer->name,
                                'recipient_email'       => $ticket_volunteer->email_address,
                                'event_title'           => $event->event_name,
                                'event_date'            => $event->startTimeAndEndTime,
                                'event'                 => $event,
                                'event_location'        => $event->fullAddress,
                                'is_under_18'           => $is_under_18,
                                'waiver_link'           => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($event_order_ticket_obj->id)]),
                                'cancelRegistrationUrl' => $this->mailUrl('front-cleanup-attending'),
                                'waiver_name'           => $waiver_handler_name,
                                'waiver_email'          => $waiver_handler_email,
                                'event_pdf_link'        => $this->mailUrl('front-registration-calendar', ['slug' => $event->slug]),
                                'organizer_name'        => $event->creator->name,
                                'event_link'            => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                                'accompanying_children' => [], // Populated in Phase 2 for parents
                            ];

                            // Only Form 2 (Youth with external guardian) gets separate waiver invite to guardian
                            if($formType === 2 && $is_under_18 && $event->waiverFormContentFilter() && !empty($waiver_handler_email) && strtolower(trim($waiver_handler_email)) !== strtolower(trim($ticket_volunteer->email_address))){
                                $waiver_people_array[$waiver_handler_email] = [
                                    'purchase_by_name'      => $creating_volunteer->first_name.' '.$creating_volunteer->last_name,
                                    'name'                  => $waiver_handler_name,
                                    'childName'             => $ticket_volunteer->name,
                                    'childEmail'            => $ticket_volunteer->email_address,
                                    'isUnder18'             => 1,
                                    'event'                 => $event,
                                    'eventName'             => $event->event_name,
                                    'eventDate'             => $event->startTimeAndEndTime,
                                    'eventLocation'         => $event->fullAddress,
                                    'waiverUrl'             => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($event_order_ticket_obj->id)]),
                                    'cancelRegistrationUrl' => $this->mailUrl('cancel-registration-front', ['ticket' => encrypt($event_order_ticket_obj->id)]),
                                    'event_link'            => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                                ];
                            }
                        }

                        $tags = [];
                        if ($event) {
                            $eventTags = $event->volunteertags()
                                ->pluck('name')
                                ->filter()
                                ->toArray();

                            if (!empty($eventTags)) {
                                $tags = array_merge($tags, $eventTags);
                            }
                            if (!empty($tags)) {
                                $role = $ticket_volunteer->is_organiser ? 'Organizer': 'Volunteer';
                                $tags[] = $role;
                                $this->syncVolunteerWithWP($ticket_volunteer, $tags);
                            }
                        }
                    }
                }

                // Phase 2: Create Child Tickets (Form 3) — Stored EXCLUSIVELY in event_order_ticket_childs
                foreach ($pendingChildTickets as $ticket_key => $child_arr) {
                    $savedPricing = $bookedTicketsById->get($child_arr['id']);
                    if (!$savedPricing) {
                        throw new CustomException('Ticket pricing data is missing. Please restart checkout.');
                    }
                    $total_platform_fee += (float) $savedPricing['platform_fee'];

                    // Identify accompanying guardian ticket
                    $parentKey = $child_arr['accompanying_guardian'] ?? null;
                    $parentTicketObj = null;

                    if ($parentKey && isset($createdOrderTickets[$parentKey])) {
                        $parentTicketObj = $createdOrderTickets[$parentKey];
                    } else {
                        // Defaults to the first adult ticket on order, or null
                        $parentTicketObj = !empty($createdOrderTickets) ? reset($createdOrderTickets) : null;
                    }

                    $guardianTicketId = $parentTicketObj ? $parentTicketObj->id : null;
                    $childFullName = trim($child_arr['child_name'] ?? 'Child Attendee');
                    $childAge = (int) ($child_arr['child_age'] ?? 0);

                    $childRecord = \App\Models\EventOrderTicketChild::create([
                        'event_order_ticket_id' => $guardianTicketId,
                        'event_id'              => $event->id,
                        'order_id'              => $order_object->id,
                        'child_name'            => $childFullName,
                        'child_age'             => $childAge,
                        'ticket_answer'         => @$child_arr['answer'],
                        'is_participating'      => 1,
                        'price'                 => $savedPricing['price'],
                        'ticket_base_price'     => $savedPricing['base_price'],
                        'ticket_platform_fee'   => $savedPricing['platform_fee'],
                        'ticket_processing_fee' => $savedPricing['processing_fee'],
                        'tikcet_org_payout'     => $savedPricing['organizer_payout'],
                    ]);

                    // Consolidate child into accompanying parent's single email payload (NO separate email sent for child)
                    if (SEND_MAIL && $parentTicketObj && $parentTicketObj->volunteer) {
                        $parentEmail = $parentTicketObj->volunteer->email_address;
                        if (isset($email_people_array[$parentEmail])) {
                            $email_people_array[$parentEmail]['accompanying_children'][] = [
                                'name' => $childFullName,
                                'age'  => $childAge,
                            ];
                        }
                    }
                }

            // creating weekly alert if checkbox of weekly alert checked on the checkout page
            if(request()->has('weekly_alert') && request()->input('weekly_alert') == 1){
                $alertName = 'Weekly Cleanups Near '.$pincode;
                $this->createWeeklyAlert($creating_volunteer->id,$alertName,$pincode,4);
            }
            if(request()->has('newsletter') && request()->input('newsletter') == 1){
                $this->syncWithWP($creating_volunteer,'Checkout');
            }

            //DB::commit();
            /**emptying the sessions */
            Session::forget(['ticket_booking', 'checkout_event', 'checkout_order_details']);
            $totalChildren = count($pendingChildTickets);
            $this->setFlashSession(true, "Great! we have created order.");
            if(SEND_MAIL){
                $hasProcessingFee = !is_null($order_object->processing_fee_per)
                    && (float) $order_object->processing_fee_per > 0;

                $ticketProcessingFee = $hasProcessingFee
                    ? (float) ($order_object->tickets->sum('ticket_processing_fee') + $order_object->children->sum('ticket_processing_fee'))
                    : 0;

                $donationProcessingFee = $hasProcessingFee
                    ? round(
                        ((float) $order_object->donation_amount * (float) $order_object->processing_fee_per) / 100,
                        2
                    )
                    : 0;

                $totalProcessingFee = $ticketProcessingFee + $donationProcessingFee;

                $totalApplicationFee = $total_platform_fee + $totalProcessingFee;

                $organizerPayout = round(
                    (float) $order_object->grand_total - $totalApplicationFee,
                    2
                );
                // send email to organizer
                $emailData = [
                    'org_name'=>$event->creator->first_name.' '.$event->creator->last_name,
                    'org_email'=>$event->creator->email_address,
                    'org_phone'=>$event->creator->phone,
                    'buyer_name'=>$creating_volunteer->first_name.' '.$creating_volunteer->last_name,
                    'buyer_email'=>$creating_volunteer->email_address,
                    'buyer_phone'=>$creating_volunteer->phone,
                    //'volunteer_name'=>$creating_volunteer->first_name.' '.$creating_volunteer->last_name,
                    //'volunteer_email'=>$creating_volunteer->email_address,
                    'total_children' => $totalChildren,
                    'action_type'=>'registered',
                    'order'=>$order_object,
                    'event_name'=>$event->event_name,
                    //'event_date'=>$event->eventStartDateTime,
                    //'event_location'=>$event->fullAddress,
                    'event_tickets'=>$ticket_booking,
                    'platform_fee'=>$total_platform_fee,
                    'processing_fee' => $totalProcessingFee,
                    'total_application_fee' => $totalApplicationFee,
                    'organizer_payout' => $organizerPayout,
                    'has_processing_fee' => $hasProcessingFee,
                    'total_tickets'=>count(request()->ticket),
                    'total_18_tickets'=>$total_18_tickets,
                    'cleanup_hosting_link'=>$this->mailUrl('front-cleanup-hosting',['type'=>'organizer']),
                    'registration_list_link'=>$this->mailUrl('front-registration-list',['event'=>$event->id]),
                    //'event_url'=>route('view-event-details', ['slug' => $event->slug]),
                ];
                //$subject = "New registration for ".$event->event_name.' : '.$emailData['org_name'];
                $subject = "New Registration for your event : ".$event->event_name;
                $this->sendEmail($event->creator->email_address, $subject, $emailData, "email-to-org-for-register");

                // send same organizer registration email to approved co-hosts
                $activeCohosts = $event->cohostRecords()->where('request_status', 'approved')->with('volunteer')->get();

                foreach ($activeCohosts as $cohost) {
                    if (
                        isset($cohost->volunteer) &&
                        !empty(trim($cohost->volunteer->email_address)) &&
                        trim($cohost->volunteer->email_address) != trim($event->creator->email_address)
                    ) {
                        $cohostEmailData = $emailData;
                        $cohostEmailData['org_name'] = $cohost->volunteer->first_name.' '.$cohost->volunteer->last_name;
                        $cohostEmailData['org_email'] = $cohost->volunteer->email_address;
                        $cohostEmailData['org_phone'] = $cohost->volunteer->phone;
                        $cohostEmailData['cleanup_hosting_link'] = $this->mailUrl('front-cleanup-hosting', ['type' => 'cohost']);

                        $this->sendEmail($cohost->volunteer->email_address, $subject, $cohostEmailData, "email-to-org-for-register");
                    }
                }

                // send email to who purchase the tickets
                $purchaseByEmailData = [
                    'event_name'=>$event->event_name,
                    //'event_date'=>$event->startTimeAndEndTime,
                    //'event_location'=>$event->fullAddress,
                    //'event_url'=>route('view-event-details', ['slug' => $event->slug]),
                    //'total_tickets'=>count($email_people_array),
                    'name'=>$creating_volunteer->first_name.' '.$creating_volunteer->last_name,
                    'event_tickets'=>$ticket_booking,
                    'total_children' => $totalChildren,
                    'order'=>$order_object,
                    'order_url'=>$this->mailUrl('front-order-details', [encrypt($order_object->id)]),
                ];
                //$subject = "You're All Set for ".$event->event_name."!";
                $subject = "Ticket Summary for ".$event->event_name." on ".$event->startTimeAndEndTime;
                $this->sendEmail($creating_volunteer->email_address, $subject, $purchaseByEmailData, "thankyou-order-mail");

                foreach ($email_people_array as $email_address => $send_mail_object) {
                    //$sub = "🌿 You’re Invited to Join ".$event->event_name."!";
                    $sub = "You've been registerd to join the ".$event->event_name." on ".$event->eventStartDateTime;
                    if($email_address==$send_mail_object['purchase_by_email']){
                        $sub = "Waiver and Next Steps for ".$event->event_name." on ".$event->eventStartDateTime;
                    }
                    $this->sendEmail($email_address, $sub, $send_mail_object, "thankyou-for-order-mail");
                    /* \Mail::to($email_address) //$email_address
                        ->send($send_mail_object); */
                }
                if(count($waiver_people_array) > 0){
                    foreach ($waiver_people_array as $wemail_address => $wsend_mail_object) {
                        $this->sendEmail($wemail_address, "Action Needed: Sign Waiver for ".$wsend_mail_object['childName']."'s partication in {$event->event_name}", $wsend_mail_object, "waiver-invitation-form");
                        /* \Mail::to($wemail_address) //$wemail_address
                            ->send($wsend_mail_object); */
                    }
                }
            }
            if(!empty($cart_id) && $is_cart_delete==true){
                Cart::where('id',$cart_id)->delete();
            }
            return redirect()->route('front-booking-confirmed', ['order' => encrypt($order_object->id)]);
        /* }catch(CustomException $e){
            DB::rollBack();
            $this->setFlashSession(true, "error : ".$e->getMessage());
        }catch(\Exception $e){
            DB::rollBack();
            $this->setFlashSession(true, "error : ".$e->getMessage());
        }
        return redirect()->route('view-event-details', ['slug' => $event->slug]); */
    }
    public function createWeeklyAlert($volunteer_id, $alertName, $zipcode, $createdFrom=1){
        $existingAlert = VolunteerAlert::where('volunteer_id', $volunteer_id)
                            ->where('zipcode', $zipcode)
                            ->first();
        if(!$existingAlert){
            $csz_id = 0;
            $lat = null;
            $long = null;
            $cityStateZip = CityStateZip::where('zipcode', $zipcode)->first();
            if(isset($cityStateZip->id)){
                $csz_id = $cityStateZip->id;
                $lat = $cityStateZip->location_lat;
                $long = $cityStateZip->location_long;
            }
            VolunteerAlert::create([
                'volunteer_id'    => $volunteer_id,
                'alert_name'      => $alertName,
                'zipcode'         => $zipcode,
                'csz_id'          => $csz_id,
                'location_lat'    => $lat,
                'location_long'   => $long,
                'created_from'    => $createdFrom,
                'status'          => 1,
                'email_frequency' => 'weekly',
            ]);
        }

    }

    public function cleanupHighlights($event){
        $event = Event::findOrFail(decrypt($event));
        return view('pages.frontend.cleanup-highlights', [
            'seo_details' => (object) ['title' => 'Cleanup Highlights'],
            'event'      => $event,
        ]);
    }
    /* public function viewWaiverPdf($ticket){
        $ticket = EventOrderTicket::with('volunteer')->findOrFail(decrypt($ticket));
        $event  = $ticket->event;
        $waiver_form_content = $event->waiverFormContentFilter();
        if($event->waiver_form_type=='pdf'){
            $waiver_form_content = '<a target="_blank" href="'.asset($waiver_form_content).'">View Waiver Document</a>';
        }
        if($event->waiver_form_type=='external_link'){
            $waiver_form_content = '<a target="_blank" href="'.$waiver_form_content.'">Open Waiver External Link</a>';
        }
        $volunteerName = $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name.' ('.$ticket->volunteer->email_address.')';
        if($ticket->is_under_18==1){
            $volunteerName = $ticket->waiver_handler_name.' ('.$ticket->waiver_handler_email.')';
        }
        return view('pages.frontend.waiver-pdf', [
            'event'      => $event,
            'ticket'      => $ticket,
            'seo_details' => (object) ['title' => 'RSVP Confirmation & Waiver Form'],
            'waiverTerms' => $waiver_form_content,
            'signerName'  => $volunteerName,
            'eventName'   => $event->event_name,
            'signature'   => $ticket->signature,
            'eventDateTime'   => $event->start_datetime->format(config('app.dateTimeFormat')),
            'eventVenue'   => $event->venue.' '.$event->pincode,
            'signature_required' => $ticket->is_under_18 ? true : false,
        ]);
    } */
    public function viewTicketPdf($ticket){
        $ticket = EventOrderTicket::with(['volunteer', 'children', 'eventOrder'])->findOrFail(decrypt($ticket));
        $event  = $ticket->event;
        $volunteerName = $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name;

        $data = [
            'event'              => $event,
            'ticket'             => $ticket,
            'order'              => $ticket->eventOrder,
            'volunteerName'      => $volunteerName,
            'eventName'          => $event->event_name,
            'eventDate'          => $event->startTimeAndEndTime,
            'eventVenue'         => $event->fullAddress,
        ];

        try {
            $html = view('pages.frontend.ticket-pdf-view', $data)->render();

            $pdf = \PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'serif',
                'dpi'                  => 150,
                'defaultPaperSize'     => 'a4',
            ])->loadHTML($html);

            $filename = 'ticket_' . $ticket->id . '_' . time() . '.pdf';

            //return $pdf->stream($filename);
            return $pdf->download($filename);

        } catch(\Exception $e) {
            \Log::error('Ticket PDF generation failed', [
                'ticket_id' => $ticket->id,
                'error'     => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
            ]);
            return back()->with('error', 'Unable to generate ticket PDF. Please try again.');
        }
    }
    public function viewWaiverPdf($ticket){
        $ticket = EventOrderTicket::with(['volunteer', 'children'])->findOrFail(decrypt($ticket));
        if(empty($ticket->waiver_filled_at) || empty($ticket->signature)){
            abort(404, 'Waiver is not signed yet.');
        }
        $event  = $ticket->event;
        $waiver_form_content = $event->waiverFormContentFilter();

        if($event->waiver_form_type=='pdf'){
            $waiver_form_content = '<a target="_blank" href="'.asset($waiver_form_content).'">View Waiver Document</a>';
        }
        if($event->waiver_form_type=='external_link'){
            $waiver_form_content = '<a target="_blank" href="'.$waiver_form_content.'">Open Waiver External Link</a>';
        }

        $volunteerName = $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name;
        if($ticket->is_under_18 == 1){
            $volunteerName = $ticket->waiver_handler_name;
        }

        $data = [
            'event'              => $event,
            'ticket'             => $ticket,
            'waiverTerms'        => $waiver_form_content,
            'signerName'         => $volunteerName,
            'eventName'          => $event->event_name,
            'signature'          => $ticket->signature,
            'eventDateTime'      => $event->eventStartDateTime,
            'eventVenue'         => $event->fullAddress,
            'signature_required' => $ticket->is_under_18 ? true : false,
        ];

        try {
            $html = view('pages.frontend.waiver-pdf-view', $data)->render();

            $pdf = \PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'serif',
                'dpi'                  => 150,
                'defaultPaperSize'     => 'a4',
            ])->loadHTML($html);

            $filename = 'waiver_' . $ticket->id . '_' . time() . '.pdf';

            return $pdf->stream($filename);

        } catch(\Exception $e) {
            \Log::error('Waiver PDF generation failed', [
                'ticket_id' => $ticket->id,
                'error'     => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
            ]);
            return back()->with('error', 'Unable to generate waiver PDF. Please try again.');
        }
    }
    public function viewWaiverForm($ticket){

        $ticket = EventOrderTicket::with(['volunteer', 'children'])->findOrFail(decrypt($ticket));
        $event  = $ticket->event;
        $waiver_form_content = $event->waiverFormContentFilter();
        if($event->waiver_form_type=='pdf'){
            $waiver_form_content = '<a target="_blank" href="'.asset($waiver_form_content).'">View Waiver Document</a>';
        }
        if($event->waiver_form_type=='external_link'){
            $waiver_form_content = '<a target="_blank" href="'.$waiver_form_content.'">Open Waiver External Link</a>';
        }

        /* if(!$waiver_form_content){
            throw new CustomException("This ticket does not have any waiver form to fill.", 404 , 'view-events');
        } */
       $volunteerName = $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name.' ('.$ticket->volunteer->email_address.')';
       if($ticket->is_under_18==1){
            $volunteerName = $ticket->waiver_handler_name.' ('.$ticket->waiver_handler_email.')';
       }
        return view('pages.frontend.waiver-form', [
            'event'      => $event,
            'ticket'      => $ticket,
            'seo_details' => (object) ['title' => 'RSVP Confirmation & Waiver Form'],
            'waiverTerms' => $waiver_form_content,
            'signerName'  => $volunteerName,
            'eventName'   => $event->event_name,
            'eventDateTime'   => $event->start_datetime->format(config('app.dateTimeFormat')),
            'eventVenue'   => $event->venue.' '.$event->pincode,
            'signature_required' => $ticket->is_under_18 ? true : false,
        ]);
    }
    public function submitWaiverForm($ticket , Request $request){
        $orderTicket = EventOrderTicket::with(['event', 'children'])->find(decrypt($ticket));
        if(!$orderTicket)
            abort(404);

        $isExternalLink = $orderTicket->event->waiver_form_type === 'external_link';

        if(!$isExternalLink) {
            $request->validate([
                'signature' => 'required|string',
            ]);
        }

        if($orderTicket->waiver_filled_at != null && ($isExternalLink || !empty($orderTicket->signature)))
            throw new CustomException("This waiver is already filled and signed. if you have any doubt, please contact organizer.", 408);

        $orderTicket->update(['waiver_filled_at' => now(), 'signature' => $request->signature]);

        // Automatically update all associated children's waiver status
        if ($orderTicket->children()->exists()) {
            $orderTicket->children()->update([
                'waiver_filled_at' => now(),
                'signature'        => $request->signature,
            ]);
        }
        if ($request->filled('qr_checkin')) {
            try {
                $qrCheckinValue = decrypt($request->qr_checkin);

                if ($qrCheckinValue === 'volunteer_checkin_qr:'.$orderTicket->id && $orderTicket->checkin != 1) {
                    $frontController = new FrontController();
                    $frontController->ticketMarkCheckin(encrypt($orderTicket->id), true);

                    // Also check in accompanying children on QR check-in
                    if ($orderTicket->children()->exists()) {
                        $activeChildren = $orderTicket->children()->whereNull('cancelled_at')->get();
                        foreach ($activeChildren as $child) {
                            $pdfPath = $child->community_form_pdf;
                            if (empty($pdfPath) || !file_exists(public_path($pdfPath))) {
                                $pdfPath = $frontController->generateChildCommunityServicePdf($child, $orderTicket->event);
                            }
                            $child->update([
                                'checkin'               => 1,
                                'checkin_dt'            => now(),
                                'community_form_pdf'    => $pdfPath,
                                'community_form_status' => 'approved',
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Invalid QR check-in token. Waiver submission still remains valid.
            }
        }
        // send mail
        $this->sendRSVPandWaiverMail($orderTicket);
        $this->setFlashSession(true, "Waiver form has been successfully submitted and signed.");
        //return redirect()->route('view-events');
        return redirect()->route('front-rsvp-confirmation',['id'=>encrypt($orderTicket->id)]);
    }
    public function submitRSVPForm($ticket , Request $request){
        return redirect()->route('front-cleanup-attending');
        /* $request->validate([
            'rsvp_confirmation' => 'required',
        ]);
        $orderTicket = EventOrderTicket::find(decrypt($ticket));
        if(!$orderTicket)
            abort(404);

        $orderTicket->update(['rsvp_filled_at' => now(), 'rsvp_confirmation' => $request->rsvp_confirmation]);

        if($request->rsvp_confirmation){
            $this->sendRSVPandWaiverMail($orderTicket);
        } else {
            $this->sendRSVPCancelledMail($orderTicket);
        }
        $this->setFlashSession(true, "RSVP Confirmation form has been successfully submitted.");
        if(!empty($orderTicket->event->waiver_form_type)){
            return redirect()->route('view-waiver-form',['ticket'=>$ticket]);
        }
        return redirect()->route('front-rsvp-confirmation',['id'=>encrypt($orderTicket->id)]); */
    }
    public function cancelRSVP($ticket,$type=null){
        return redirect()->route('front-cleanup-attending');
        /* $orderTicket = EventOrderTicket::find(decrypt($ticket));
        if(!$orderTicket)
            abort(404);
        $orderTicket->update(['rsvp_confirmation' => 0]);
        $this->sendRSVPCancelledMail($orderTicket);
        if($type==1){
            $this->setFlashSession(true, "RSVP has been cancelled.");
            return redirect()->route('rsvp-cancelled-page',['event'=>encrypt($orderTicket->event_id)]);
            //return redirect()->back();
        } else {
            return $this->generateJsonResponse(true, 'RSVP has been cancelled.');
        } */
    }
    public function confirmCancelRegistrationFront($ticket,$type){
        return $this->cancelRefundPaidTicketAttendee($ticket,$type,0);
    }
    public function cancelRefundPaidTicketAttendee($ticket,$type,$is_ajax=1){
        $request = request();
        if($type==2){
            $orderTicket = EventOrderTicket::find(decrypt($ticket));
            $order = $orderTicket->eventOrder;
            if(!$orderTicket)
                abort(404);

            $orderTicket->update(['rsvp_confirmation' => 0,'refund_status' => 4,'cancelled_at'=>date('Y-m-d H:i:s')]);
            $this->sendRSVPCancelledMail($orderTicket);
            if($is_ajax!=1){
                return redirect()->back();
            }
            return $this->generateJsonResponse(true, 'Ticket cancelled successfully.');
        } else if($type==1){
            $orderTicket = EventOrderTicket::find(decrypt($ticket));
            if(!$orderTicket)
                abort(404);
            $order = $orderTicket->eventOrder;
            $total_amount = $orderTicket->price;
            //$total_fee = $orderTicket->ticket_platform_fee;
            $total_fee = (float) $orderTicket->ticket_platform_fee + (float) $orderTicket->ticket_processing_fee;

            if($total_amount<=0){
                $orderTicket->update(['rsvp_confirmation' => 0,'refund_status' => 3,'cancelled_at'=>date('Y-m-d H:i:s')]);
                $this->sendRSVPCancelledMail($orderTicket);
                if($is_ajax!=1){
                    return redirect()->back();
                }
                return $this->generateJsonResponse(true, 'Ticket cancelled successfully. Refund initiated. Depending on bank, it may take 5–10 business days for the amount to reflect in your account.');
            } else {
                $stripeCon = new StripeController();
                $res = $stripeCon->refundCharge($order->id,$order->payment_intent,$total_amount,$total_fee);
                //echo '<pre>';print_r($res);die();
                if($res['status']==true){
                    $orderTicket->update(['rsvp_confirmation' => 0,'cancelled_at'=>date('Y-m-d H:i:s'),'refund_status'=>3]);
                    $this->sendRSVPCancelledMail($orderTicket);
                    if($is_ajax!=1){
                        return redirect()->back();
                    }
                    return $this->generateJsonResponse(true, 'Ticket cancelled successfully. Refund initiated. Depending on bank, it may take 5–10 business days for the amount to reflect in your account.');
                } else {
                    if($is_ajax!=1){
                        $this->setFlashSession(false, "Something wen wrong. Please try again later.");
                        return redirect()->back();
                    }
                    return $this->generateJsonResponse(false, 'Something wen wrong. Please try again later');
                }
            }
        }
        if($is_ajax!=1){
            $this->setFlashSession(false, "Something wen wrong.");
            return redirect()->back();
        }
        return $this->generateJsonResponse(false, 'Something wen wrong. Please try again later');
    }
    public function cancelRefundPaidTicket(){
        $request = request();
        request()->validate([
            'ticket' => 'required|array|min:1',
            'ticket.*' => 'required',
            //'ticket' => 'required',
            'refund_type' => 'required',
        ],[
            'ticket.required' => 'Please select at least one ticket to cancel.',
            'ticket.min' => 'Please select at least one ticket to cancel.',
        ]);
        if($request->refund_type==3){
            $orderTicket = EventOrderTicket::find(decrypt($request->ticket[0]));
            $order = $orderTicket->eventOrder;
            foreach ($request->ticket as $key => $value) {
                $orderTicket = EventOrderTicket::find(decrypt($value));
                if(!$orderTicket)
                    abort(404);

                $orderTicket->update(['rsvp_confirmation' => 0,'refund_status' => 4,'cancelled_at'=>date('Y-m-d H:i:s')]);
                $this->sendRSVPCancelledMail($orderTicket,false,true);
            }
            $this->sendRSVPCancelledMail($orderTicket,true,false,['ticket_count'=>count($request->ticket),'ticket_refund_amount'=>0]);
            $this->setFlashSession(true, "Ticket has been cancelled.");
            return redirect()->back();
        } else if($request->refund_type==2){
            $total_amount = 0;
            $total_fee = 0;
            foreach ($request->ticket as $key => $value) {
                $orderTicket = EventOrderTicket::find(decrypt($value));
                if(!$orderTicket)
                    abort(404);

                $order = $orderTicket->eventOrder;
                $amount = $orderTicket->price;
                /* $fee_per = $order->platform_fee_per;
                $fee = 0;
                if($amount>0 && $fee_per>0){
                    $fee = ($amount*$fee_per)/100;
                }
                $total_fee += $fee; */
                $total_amount += $amount;
                //$total_fee += $orderTicket->ticket_platform_fee;
                $total_fee += (float) $orderTicket->ticket_platform_fee + (float) $orderTicket->ticket_processing_fee;
            }
            /* $orderTicket = EventOrderTicket::find(decrypt($request->ticket));
            if(!$orderTicket)
                abort(404);
            $event = $orderTicket->event;
            $order = $orderTicket->eventOrder;
            $fee_per = $order->platform_fee_per;
            $amount = $orderTicket->price;
            $fee = 0;
            if($amount>0 && $fee_per>0){
                $fee = ($amount*$order->platform_fee_per)/100;
            } */
            if($total_amount<=0){
                foreach ($request->ticket as $key => $value) {
                    $orderTicket = EventOrderTicket::find(decrypt($value));
                    $orderTicket->update(['rsvp_confirmation' => 0,'refund_status' => 3,'cancelled_at'=>date('Y-m-d H:i:s')]);
                    $this->sendRSVPCancelledMail($orderTicket,false,true);
                }
                $this->sendRSVPCancelledMail($orderTicket,true,false,['ticket_count'=>count($request->ticket),'ticket_refund_amount'=>0]);
                $this->setFlashSession(true, "Ticket cancelled successfully. Refund initiated. Depending on bank, it may take 5–10 business days for the amount to reflect in account.");
                return redirect()->back();
            } else {
                $stripeCon = new StripeController();
                $res = $stripeCon->refundCharge($order->id,$order->payment_intent,$total_amount,$total_fee);
                if($res['status']==true){
                    foreach ($request->ticket as $key => $value) {
                        $orderTicket = EventOrderTicket::find(decrypt($value));
                        $orderTicket->update(['rsvp_confirmation' => 0,'cancelled_at'=>date('Y-m-d H:i:s'),'refund_status'=>3]);
                        $this->sendRSVPCancelledMail($orderTicket,false,true);
                    }
                    $this->sendRSVPCancelledMail($orderTicket,true,false,['ticket_count'=>count($request->ticket),'ticket_refund_amount'=>$total_amount]);
                    $this->setFlashSession(true, "Ticket cancelled successfully. Refund initiated. Depending on bank, it may take 5–10 business days for the amount to reflect in account.");
                    return redirect()->back();
                } else {
                    $this->setFlashSession(false, "Something wen wrong. Please try again later");
                    return redirect()->back();
                }
            }
        } else {
            $orderTicket = EventOrderTicket::find(decrypt($request->ticket[0]));
            $order = $orderTicket->eventOrder;
            $total_donation_amount = !empty($order->donation_amount)?$order->donation_amount:0;
            foreach ($request->ticket as $key => $value) {
                $orderTicket = EventOrderTicket::find(decrypt($value));
                if(!$orderTicket)
                    abort(404);

                $orderTicket->update(['rsvp_confirmation' => 0,'is_amt_donated'=>1,'cancelled_at'=>date('Y-m-d H:i:s')]);
                $total_donation_amount += $orderTicket->price;
                $this->sendRSVPCancelledMail($orderTicket,false,true);
            }
            $order->donation_amount = $total_donation_amount;
            $order->save();
            $this->sendRSVPCancelledMail($orderTicket,true,false,['ticket_count'=>count($request->ticket),'ticket_refund_amount'=>0]);
            /* $orderTicket = EventOrderTicket::find(decrypt($request->ticket));
            if(!$orderTicket)
                abort(404);

            $orderTicket->update(['rsvp_confirmation' => 0,'is_amt_donated'=>1]);

            $order = $orderTicket->eventOrder;
            $donation_amt = !empty($order->donation_amount)?$order->donation_amount:0;
            $donation_amt += $orderTicket->price;
            $order->donation_amount = $donation_amt;
            $order->save();
            $this->sendRSVPCancelledMail($orderTicket); */
            $this->setFlashSession(true, "Ticket has been cancelled and amount has been donated.");
            return redirect()->back();
        }
    }
    public function cancelRegistrationFront($ticket){
        $orderTicket = EventOrderTicket::find(decrypt($ticket));
        if(!$orderTicket)
            abort(404);
        $data = [
            'registration' => $orderTicket,
            'event' => $orderTicket->event,
            'order' => $orderTicket->eventOrder,
        ];
        $data['seo_details'] = (object)['title' => 'Cancel Registration'];
        return view('pages.frontend.cancel-registration-front', $data);
    }
    public function cancelRefundPaidTicketOLD(){
        $request = request();
        request()->validate([
            'ticket' => 'required',
            'refund_type' => 'required',
        ]);
        if($request->refund_type==2){
            $orderTicket = EventOrderTicket::find(decrypt($request->ticket));
            if(!$orderTicket)
                abort(404);
            $event = $orderTicket->event;
            $order = $orderTicket->eventOrder;
            $fee_per = $order->platform_fee_per;
            $amount = $orderTicket->price;
            $fee = 0;
            if($amount>0 && $fee_per>0){
                $fee = ($amount*$order->platform_fee_per)/100;
            }
            if($amount<=0){
                $orderTicket->update(['rsvp_confirmation' => 0,'refund_status' => 3,'cancelled_at'=>date('Y-m-d H:i:s')]);
                $this->sendRSVPCancelledMail($orderTicket);
                $this->setFlashSession(true, "Ticket has been cancelled.");
                return redirect()->back();
            } else {
                $stripeCon = new StripeController();
                $res = $stripeCon->refundCharge($order->id,$order->payment_intent,$amount,$fee);
                if($res['status']==true){
                    $orderTicket->update(['rsvp_confirmation' => 0,'cancelled_at'=>date('Y-m-d H:i:s'),'refund_status'=>3]);
                    $this->sendRSVPCancelledMail($orderTicket);
                    $this->setFlashSession(true, "Ticket cancelled successfully. Refund initiated. Depending on your bank, it may take 5–10 business days for the amount to reflect in your account.");
                    return redirect()->back();
                } else {
                    $this->setFlashSession(false, "Something wen wrong. Please try again later");
                    return redirect()->back();
                }
            }

        } else {
            $orderTicket = EventOrderTicket::find(decrypt($request->ticket));
            if(!$orderTicket)
                abort(404);

            $orderTicket->update(['rsvp_confirmation' => 0,'is_amt_donated'=>1,'cancelled_at'=>date('Y-m-d H:i:s')]);

            $order = $orderTicket->eventOrder;
            $donation_amt = !empty($order->donation_amount)?$order->donation_amount:0;
            $donation_amt += $orderTicket->price;
            $order->donation_amount = $donation_amt;
            $order->save();
            $this->sendRSVPCancelledMail($orderTicket);
            $this->setFlashSession(true, "Ticket has been cancelled and amount has been donated.");
            return redirect()->back();
        }
    }
    public function rsvpCancelledPage($event_id){
        $data = [];
        $event = Event::where('id', decrypt($event_id))->first();
        if(!$event_id){
            abort(404, 'Event not found.');
        }
        $data['event'] = $event;
        $data['seo_details'] = (object)['title' => 'RSVP Cancelled'];
        return view('pages.frontend.rsvp-cancelled', $data);
    }
    public function sendRSVPCancelledMail($orderTicket,$sendToOrganizer=true,$sendToVolunteer=true,$orgData=[]){
        if($sendToVolunteer){
            // send mail
            $is_rsvp_confirmed = ($orderTicket->rsvp_confirmation==1 && !empty($orderTicket->rsvp_filled_at))?true:false;
            $is_waiver_signed = !empty($orderTicket->waiver_filled_at)?true:false;
            $emailContentData = [
                'event' => $orderTicket->event,
                'event_name' => $orderTicket->event->event_name,
                'event_date' => $orderTicket->event->startTimeAndEndTime,
                'event_location' => $orderTicket->event->fullAddress,
                'event_ticket_id' => $orderTicket->ticket_number,
                //'dashboard_link' => route('front-dashboard'),
                //'rsvp_waiver_link' => route('view-waiver-form', ['ticket' => encrypt($orderTicket->id)]),
                //'is_rsvp_confirmed' => $is_rsvp_confirmed,
                //'is_waiver_signed' => $is_waiver_signed,
                //'guardian_name' => $orderTicket->waiver_handler_name,
                //'guardian_email' => $orderTicket->waiver_handler_email,
                //'primary_user_name' => $orderTicket->eventOrder->creator->first_name.' '.$orderTicket->eventOrder->creator->last_name,
                'volunteer_name' => $orderTicket->volunteer->first_name.' '.$orderTicket->volunteer->last_name,
                //'volunteer_email' => $orderTicket->volunteer->email_address,
                'is_under18' => $orderTicket->is_under_18,
            ];
            $email_address = $orderTicket->volunteer->email_address;
            $subject = "Your registration for ".$orderTicket->event->event_name." has been cancelled.";
            /* if($orderTicket->is_under_18){
                $email_address = $orderTicket->waiver_handler_email;
                $subject = "🌿 RSVP Has Been Cancelled –".$orderTicket->event->event_name."!";
            } */
            $this->sendEmail($email_address, $subject, $emailContentData, "rsvp_cancelled");
        }

        if($sendToOrganizer){
            // send email to organizer
            $event = $orderTicket->event;
            $ticket_count = 1;
            $ticket_refund_amount = $orderTicket->price;
            if(!empty($orgData)){
                $ticket_count = $orgData['ticket_count'];
                $ticket_refund_amount = $orgData['ticket_refund_amount'];
            }
            $emailData = [
                'org_name'=>$event->creator->first_name.' '.$event->creator->last_name,
                //'volunteer_name'=>$orderTicket->volunteer->first_name.' '.$orderTicket->volunteer->last_name,
                //'volunteer_email'=>$orderTicket->volunteer->email_address,
                'action_type'=>'cancelled',
                'ticket_count'=>$ticket_count,
                'ticket_refund_amount'=>$ticket_refund_amount,
                'event_name'=>$event->event_name,
                //'event_date'=>$event->eventStartDateTime,
                //'event_location'=>$event->fullAddress,
                //'event_url'=>route('view-event-details', ['slug' => $event->slug]),
                'event_hosting_url'=>$this->mailUrl('front-cleanup-hosting', ['type' => 'organizer']),
            ];
            $subject = "Registration cancelled for ".$event->event_name;
            $this->sendEmail($event->creator->email_address, $subject, $emailData, "email-to-org-for-register");

            // send email to buyer
            $emailData = [
                'name'=>$orderTicket->eventOrder->creator->first_name.' '.$orderTicket->eventOrder->creator->last_name,
                'ticket_count'=>$ticket_count,
                'ticket_refund_amount'=>$ticket_refund_amount,
                'event_name'=>$event->event_name,
                'order_url'=>$this->mailUrl('front-order-details', [encrypt($orderTicket->eventOrder->id)]),
            ];
            $subject = "Your cancellation for ".$event->event_name." is confirmed";
            $this->sendEmail($orderTicket->eventOrder->creator->email_address, $subject, $emailData, "email-to-buyer-for-cancel");
        }

        $this->notifyWaitlistWhenAvailable($orderTicket->event_id, $orderTicket->ticket_id);
    }
    public function sendRSVPandWaiverMail($orderTicket){
        // send mail
        $is_rsvp_confirmed = ($orderTicket->rsvp_confirmation==1 && !empty($orderTicket->rsvp_filled_at))?true:false;
        $is_waiver_signed = !empty($orderTicket->waiver_filled_at)?true:false;
        $emailContentData = [
            'ticket' => $orderTicket,
            'event' => $orderTicket->event,
            'event_name' => $orderTicket->event->event_name,
            'event_date' => $orderTicket->event->startTimeAndEndTime,
            'event_location' => $orderTicket->event->fullAddress,
            'event_ticket_id' => $orderTicket->ticket_number,
            'dashboard_link' => $this->mailUrl('front-dashboard'),
            'rsvp_waiver_link' => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($orderTicket->id)]),
            'is_rsvp_confirmed' => $is_rsvp_confirmed,
            'is_waiver_signed' => $is_waiver_signed,
            'guardian_name' => $orderTicket->waiver_handler_name,
            'guardian_email' => $orderTicket->waiver_handler_email,
            'primary_user_name' => $orderTicket->eventOrder->creator->first_name.' '.$orderTicket->eventOrder->creator->last_name,
            'volunteer_name' => $orderTicket->volunteer->first_name.' '.$orderTicket->volunteer->last_name,
            'volunteer_email' => $orderTicket->volunteer->email_address,
            'is_under18' => $orderTicket->is_under_18,
        ];
        $email_address = $orderTicket->volunteer->email_address;
        $subject = "You're All Set for ".$orderTicket->event->event_name."! ✅ RSVP & Waiver Confirmed";
        if($orderTicket->is_under_18){
            $email_address = $orderTicket->waiver_handler_email;
            $subject = "✅ RSVP & Waiver Confirmed for ".$orderTicket->event->event_name."!";
        }
        if(!$is_rsvp_confirmed){
            $subject = "🚨 RSVP Pending for ".$orderTicket->event->event_name;
        } else if(!$is_waiver_signed){
            $subject = "Final Step Before ".$orderTicket->event->event_name." – Waiver Still Pending ❗";
        }
        $this->sendEmail($email_address, $subject, $emailContentData, "rsvp_waiver_confirmation");
    }
    public function createAlertPage()
    {
        // check if volunteer is logged in
        if (auth()->guard('front')->check()) {
            return redirect()->route('front-email-alerts');
        }
        $seo_details = (object)['title' => 'Create Weekly Alert'];
        return view('pages.frontend.create-alert', ['seo_details' => $seo_details]);
    }
    public function submitCreateWeeklyNewsletterAlert(){
        $request = request();
        request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email', new \App\Rules\StrictEmail()],
        ]);

        $is_weekly_alert = (isset($request->weekly_alert) && $request->weekly_alert==1)?true:false;
        $is_newsletter_alert = (isset($request->newsletter_alert) && $request->newsletter_alert==1)?true:false;

        if($is_weekly_alert){
            if(empty($request->checkout_pincode)){
                $this->setFlashSession(false, "Please enter a valid ZIP code to subscribe to weekly alerts.");
                return redirect()->back();
            }
            // create volunteer if not exists
            $volunteer = Volunteer::where('email_address', $request->email)->first();
            $pincode = '';
            $cityStateZip = CityStateZip::find($request->checkout_pincode);
            if(isset($cityStateZip->id)){
                $pincode = $cityStateZip->zipcode;
            }
            if (!$volunteer) {
                $volunteer = Volunteer::create([
                    'first_name'    => $request->first_name,
                    'last_name'     => $request->last_name,
                    'email_address' => $request->email,
                    'pincode' => $pincode,
                    'city_id' => $request->checkout_pincode,
                ]);
            } else {
                // check weekly alert already exists
                $existingAlert = VolunteerAlert::where('volunteer_id', $volunteer->id)
                                    ->where('csz_id', $request->checkout_pincode)
                                    ->first();
                if ($existingAlert) {
                    $this->setFlashSession(false, "You are already receiving alerts for this ZIP code.");
                    return redirect()->back();
                }
            }

            $alertName = 'Weekly Cleanups Near '.$pincode;
            $this->createWeeklyAlert($volunteer->id, $alertName, $pincode,2);
        }
        if($is_newsletter_alert){
            $volunteerData = [
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email_address' => $request->email,
            ];
            $this->syncWithWPData($volunteerData,'Alert Page');
        }
        if($is_weekly_alert && $is_newsletter_alert){
            $this->setFlashSession(true, "Thank you for subscribing to Weekly Cleanup Alerts & Quarterly Newsletter.");
        } else if ($is_weekly_alert){
            $this->setFlashSession(true, "Thank you for subscribing to Weekly Cleanup Alerts.");
        } else if ($is_newsletter_alert){
            $this->setFlashSession(true, "Thank you for subscribing to Quarterly Newsletter.");
        }
        return redirect()->route('front-create-alert');
    }
    public function submitCreateAlert(){
        $request = request();
        request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'checkout_pincode' => 'required',
            'email' => ['required', 'email', new \App\Rules\StrictEmail()],
        ]);

        // create volunteer if not exists
        $volunteer = Volunteer::where('email_address', $request->email)->first();
        $pincode = '';
        $cityStateZip = CityStateZip::find($request->checkout_pincode);
        if(isset($cityStateZip->id)){
            $pincode = $cityStateZip->zipcode;
        }
        if (!$volunteer) {
            $volunteer = Volunteer::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email_address' => $request->email,
                'pincode' => $pincode,
                'city_id' => $request->checkout_pincode,
            ]);
        } else {
            // check weekly alert already exists
            $existingAlert = VolunteerAlert::where('volunteer_id', $volunteer->id)
                                ->where('csz_id', $request->checkout_pincode)
                                ->first();
            if ($existingAlert) {
                $this->setFlashSession(false, "You are already receiving alerts for this ZIP code.");
                return redirect()->back();
            }
        }
        /* if(isset($request->newsletter) && $request->newsletter==1){
            $this->syncWithWP($volunteer,'Alert Page');
        } */

        $alertName = 'Weekly Cleanups Near '.$pincode;
        $createFrom  = 2;
        if ($request->has('create_from') && !empty($request->create_from)) {
            $createFrom  = $request->create_from;
        }
        $this->createWeeklyAlert($volunteer->id, $alertName, $pincode,$createFrom);
        $this->setFlashSession(true, "Weekly alert has been created successfully.");
        if ($request->has('from_widget') && $request->from_widget==1) {
            return redirect()->back();
        }
        return redirect()->route('front-create-alert');
    }
    public function submitCreateNewsletter(){
        $request = request();
        request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email', new \App\Rules\StrictEmail()],
        ]);

        $volunteer = Volunteer::where('email_address', $request->email)->first();
        if (isset($volunteer->id)) {
            $this->syncWithWP($volunteer,'Alert Page');
        } else {
            $volunteerData = [
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email_address' => $request->email,
            ];
            $this->syncWithWPData($volunteerData,'Alert Page');
        }

        $this->setFlashSession(true, "Newsletter has been subscribed successfully.");
        return redirect()->route('front-create-alert');
    }
    public function checkEventSlug(){
        $request = request();
        $event_id = $request->event_id ? decrypt($request->event_id) : null;
        if($event_id>0){
            $event  = Event::where('slug' , $request->slug)->where('id', '!=', $event_id)->first();
        } else {
            $event  = Event::where('slug' , $request->slug)->first();
        }
        if(isset($event->id)){
            return $this->generateJsonResponse(false, 'This slug is already taken. Try another one.');
        } else {
            return $this->generateJsonResponse(true, 'This slug is available!');
        }
    }
    public function downloadEventIcal($placeholder){
        $event = \App\Models\Event::findOrFail(decrypt($placeholder));
        /* $icalContent = "BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
DTSTART:" . \Carbon\Carbon::parse($event->start_datetime)->format('Ymd\THis\Z') . "
DTEND:" . \Carbon\Carbon::parse($event->end_datetime)->format('Ymd\THis\Z') . "
SUMMARY:" . $event->event_name . "
LOCATION:" . $event->fullAddress . "
END:VEVENT
END:VCALENDAR"; */
        $icalContent = getiCalData($event);
        $filename = $event->event_name . '.ics';
        return response($icalContent)
            ->header('Content-Type', 'text/calendar')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }
    public function downloadICS($id)
    {
        $event = Event::findOrFail(decrypt($id));
        $icalContent = getiCalData($event);
        // Safe filename
        $filename = $event->slug. '.ics';

        return response($icalContent, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'inline; filename="'.$filename.'"', // ✅ inline triggers iPhone calendar prompt
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
    public function updateTicketDates(){
        $events = Event::get();
        foreach($events as $event){
            if($event->eventTickets->count()>0){
                foreach($event->eventTickets as $ticket){
                    /* $ticket->update([
                        'sell_start_time' => $event->ticket_sell_start_time,
                        'sell_end_time' => $event->ticket_sell_end_time,
                    ]); */
                }
            }
        }
        return "done";
    }
    private function validatedChildrenByTicket(Event $event, array $tickets): array
    {
        $childrenByTicket = [];

        foreach ($tickets as $ticketKey => $ticket) {
            $hasChildren = filter_var(
                $ticket['has_children'] ?? false,
                FILTER_VALIDATE_BOOLEAN
            );

            $children = $ticket['children'] ?? [];

            if (!is_array($children)) {
                throw ValidationException::withMessages([
                    "ticket.$ticketKey.children" => 'Invalid children information submitted.',
                ]);
            }

            if (!$hasChildren && count($children) > 0) {
                throw ValidationException::withMessages([
                    "ticket.$ticketKey.children" => 'Children information is not allowed unless the children option is selected.',
                ]);
            }

            if (!$hasChildren) {
                continue;
            }

            if (!$event->allow_child_under13) {
                throw ValidationException::withMessages([
                    "ticket.$ticketKey.children" => 'This event does not allow children below 13 years.',
                ]);
            }

            if (!empty($ticket['is_under_18'])) {
                throw ValidationException::withMessages([
                    "ticket.$ticketKey.children" => 'An attendee under 18 years of age cannot be registered with children.',
                ]);
            }

            if (count($children) < 1 || count($children) > 4) {
                throw ValidationException::withMessages([
                    "ticket.$ticketKey.children" => 'Please add between 1 and 4 children.',
                ]);
            }

            foreach ($children as $childKey => $child) {
                $childName = trim($child['child_name'] ?? '');
                $childAge = $child['child_age'] ?? null;

                if ($childName === '') {
                    throw ValidationException::withMessages([
                        "ticket.$ticketKey.children.$childKey.child_name" => 'Child name is required.',
                    ]);
                }

                if (
                    !is_numeric($childAge) ||
                    (int) $childAge != $childAge ||
                    (int) $childAge < 0 ||
                    (int) $childAge > 12
                ) {
                    throw ValidationException::withMessages([
                        "ticket.$ticketKey.children.$childKey.child_age" => 'Child age must be between 0 and 12 years.',
                    ]);
                }

                $childrenByTicket[$ticketKey][] = [
                    'child_name' => $childName,
                    'child_age' => (int) $childAge,
                ];
            }
        }

        return $childrenByTicket;
    }



    public function importICCVolunteerByScript()
    {
        $tagName = 'ICC2026';

        echo "<pre>";

        // find tag
        $tag = Tag::where('name', $tagName)->first();

        if (!$tag) {
            echo "Tag not found: " . $tagName . "\n";
            echo "</pre>";
            return;
        }

        echo "TAG FOUND\n";
        echo "Tag Name : {$tag->name}\n";
        echo "Tag ID   : {$tag->id}\n\n";


        // find events having this tag
        $eventIds = DB::table('event_volunteer_tags')
            ->where('tag_id', $tag->id)
            ->pluck('event_id')
            ->unique()
            ->values();
        echo "TOTAL EVENT FOUND : ".count($eventIds)."\n";

        if ($eventIds->isEmpty()) {
            echo "No events found for tag: {$tagName}\n";
            echo "</pre>";
            return;
        }


        $events = Event::whereIn('id', $eventIds)
        ->skip(70)->take(5)->get();

        /* $attendeeIds = DB::table('event_order_tickets')
            ->whereIn('event_id', $eventIds)
            ->whereNotNull('volunteer_id')
            ->pluck('volunteer_id')
            ->unique()
            ->values();
            print_r(count($attendeeIds)); */
        //  loop events
        foreach ($events as $event) {

            if (!$event) {
                continue;
            }

            echo "========================================\n";
            echo "EVENT\n";
            echo "========================================\n";
            echo "Event ID   : {$event->id}\n";
            echo "Event Name : {$event->event_name}\n";


            // get all tags of this event
            $eventTags = $event->volunteertags()
                ->pluck('name')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            echo "Event Tags : " . implode(', ', $eventTags) . "\n\n";


            // get attendes of this event
            $attendeeIds = DB::table('event_order_tickets')
                ->where('event_id', $event->id)
                ->whereNotNull('volunteer_id')
                ->pluck('volunteer_id')
                ->unique()
                ->values();

            if ($attendeeIds->isEmpty()) {
                echo "No attendees found for this event.\n\n";
                continue;
            }

            echo "Total Attendees : {$attendeeIds->count()}\n\n";


            // get atndees
            $attendees = Volunteer::whereIn('id', $attendeeIds)->get();


            // process5 attendees at a time
            foreach ($attendees->chunk(5) as $chunkIndex => $attendeeChunk) {

                echo "----------------------------------------\n";
                echo "Processing Chunk : " . ($chunkIndex + 1) . "\n";
                echo "Chunk Count      : " . $attendeeChunk->count() . "\n";
                echo "----------------------------------------\n";


                foreach ($attendeeChunk as $attendee) {

                    // Event ke saare tags attendee ke liye
                    $tags = $eventTags;

                    // Add attendee role
                    $tags[] = $attendee->is_organiser
                        ? 'Organizer'
                        : 'Volunteer';

                    // Remove duplicate tags
                    $tags = array_values(array_unique($tags));


                    echo "Volunteer ID : {$attendee->id}\n";
                    echo "Name         : {$attendee->name}\n";
                    echo "Email        : {$attendee->email_address}\n";
                    echo "Tags         : " . implode(', ', $tags) . "\n";


                    // WP Sync
                    /* $this->syncVolunteerWithWP(
                        $attendee,
                        $tags
                    );
                    echo "WP Sync      : DONE\n";
                    echo "------------------------------\n"; */
                }

                echo "\n";
            }
        }

        echo "</pre>";
    }


    private function syncVolunteerWithWP($volunteer, $tags = [])
    {
        if (empty($tags)) {
            return;
        }

        $url = config('app.wp_sync_url');

        $data = [
            'first_name'          => $volunteer->first_name,
            'last_name'           => $volunteer->last_name,
            'full_name'           => $volunteer->first_name . ' ' . $volunteer->last_name,
            'email'               => $volunteer->email_address,
            'phone'               => $volunteer->phone,
            'address_line_1'      => $volunteer->address_line_1,
            'postal_code'         => $volunteer->pincode,
            'country'             => 'United States',
            'tags'                => $tags,
            //'fcrm_vol_newsletter' => 'Yes',
        ];

        $this->callCurl('POST', $url, json_encode($data));
    }
}
