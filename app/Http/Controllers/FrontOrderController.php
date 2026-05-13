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
use App\Models\OrderCart;
use App\Models\OrderHelper;
use App\Models\CityStateZip;
use App\Models\VolunteerAlert;
use App\Models\VolunteerWidgetLog;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\StripeController;
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
                    $ticket_booking[] = [
                                            'id'    => $ticket_id, 
                                            'count' => $event->sell_tickets_individually ? 1 : $ticket_count , 
                                            'name'  => $ticket_obj->ticket_name ,
                                            'price' => $ticket_price , 
                                            'base_price' => $all_price['ticket_base_price'] , 
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
        
        Session::put('checkout_event', $event);
        Session::put('checkout_order_details', [
                                        'sub_total' => $subtotal , 
                                        'final_total' => $finaltotal , 
                                        'total_ticket' => $totalticket 
        ]);
        return redirect()->route('front-checkout');
        
    }
    
    public function checkout(){
        
        $event            = session()->get('checkout_event');
        $order_total_details = session()->get('checkout_order_details');
        $ticket_booking = session()->get('ticket_booking');
        $setting = Setting::first();

        $feeAmt = 0;
        if($ticket_booking){
            foreach ($ticket_booking as $key => $ticekt) {
                $eTicket = EventTicket::where('id',$ticekt['id'])->first();
                if(isset($eTicket->id)){
                    $allPrice = $eTicket->all_price;
                    $feeAmt += $allPrice['ticket_platform_fee']*$ticekt['count'];
                }
            }
        }
         
        if(!$event || !$order_total_details ){
            $this->setFlashSession(false , "To access this page, please choose your tickets from the event details page.");
            return redirect()->route('view-events');
        }

        $order_total_view = view('components.reusables.frontend.order-total', [
                                'setting' => $setting,
                                'event' => $event , 
                                'ticket_fees' => $feeAmt , 
                                'ticket_booking' => $ticket_booking , 
                                'sub_total' => $order_total_details['sub_total'] , 
                                'final_total' => $order_total_details['final_total'] , 
                                'total_ticket' => $order_total_details['total_ticket'] , 
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
            if(in_array($value['email_address'], $emails)){
                return $this->generateJsonResponse(false , "The email address '{$value['email_address']}' is repeated in the tickets. Please use unique emails for every volunteers.");
            } else {
                $emails[] = $value['email_address'];
            }
        }
        //print_r($tickets);
        $ticket_booking = session()->get('ticket_booking');
        foreach ($ticket_booking as $ticket_id => $ticket_row) {
            $ticket_obj = EventTicket::findOrfail($ticket_row['id']);
            $remaining_tickets = $ticket_obj->remainingTickets();
            if($remaining_tickets < (int)$ticket_row['count']){
                return $this->generateJsonResponse(false , "Opps! {$ticket_obj->ticket_name} ticket has only {$remaining_tickets} allocations left. please reallocate tickets from event details page. ");
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
                'checkout_email_address' => ['required', 'email', new \App\Rules\StrictEmail()],
                'checkout_first_name' => [ 'max:255',
                                            new \App\Rules\RequireFieldsIfEmailExists(request()->checkout_email_address, 'checkout_first_name')
                                        ],
                'checkout_last_name' => [  'max:255',
                                            new \App\Rules\RequireFieldsIfEmailExists(request()->checkout_email_address, 'checkout_last_name')
                                        ],
                'checkout_pincode' =>   [  'max:20',
                                            new \App\Rules\RequireFieldsIfEmailExists(request()->checkout_email_address, 'checkout_pincode')
                                        ],
                'checkout_organisation_name' => 'nullable|string|max:255',
                'phone' => ['nullable', 'string',  'max:20', new \App\Rules\ValidUSPhoneNumber()],

                // Ticket Information (array validation for dynamic tickets)
                'ticket' => 'required|array',
                'ticket.*.email_address' => [
                                                'required','email', new \App\Rules\StrictEmail(),
                                                function ($attribute, $value, $fail){
                                                    $emails = collect(request()->tickets)->pluck('email');
                                                    if ($emails->duplicates()->contains($value)) 
                                                        $fail("The email address {$value} is repeated in the tickets.");
                                                }
                                            ],
                // 'ticket.*.first_name' => 'required|string|max:255',
                // 'ticket.*.last_name' => 'required|string|max:255',
                // 'ticket.*.phone' => ['required', 'string', 'max:20', new \App\Rules\ValidUSPhoneNumber()],
                'ticket.*.id' => 'required|integer',
            ];
            /**Dynamically Adding the Rules */
            $allEmails = [];
            $isDuplicateEmail = false;
            foreach (request()->input('ticket', []) as $index => $ticket) {
                $email = $ticket['email_address'] ?? null; 
                if(!empty($email)){
                    if(!in_array($email,$allEmails)){
                        $allEmails[] = $email;
                    } else {
                        $isDuplicateEmail = true;
                    }
                }

                $rules["ticket.$index.first_name"] = [
                    'max:255',
                    new \App\Rules\RequireFieldsIfEmailExists($email, 'ticket.$index.first_name')
                ];
                $rules["ticket.$index.last_name"] = [
                    'max:255',
                    new \App\Rules\RequireFieldsIfEmailExists($email, 'ticket.$index.last_name')
                ];
                $rules["ticket.$index.phone"] = [
                    'max:20',
                    new \App\Rules\RequireFieldsIfEmailExists($email, 'ticket.$index.phone')
                ];
            }
            if($isDuplicateEmail){
                throw new CustomException("Duplicate emails found in the order. Please use unique emails for every volunteers.");
            }

            $messages = [
                'checkout_email_address.required' => 'The billing email address is required.',
                'checkout_email_address.email' => 'The billing email address must be a valid email.',
                'checkout_first_name.required' => 'The first name is required.',
                'checkout_last_name.required' => 'The last name is required.',
                'checkout_pincode.required' => 'The zip/post code is required.',
                'checkout_organisation_name.required' => 'The company name is required.',
                'phone.required' => 'The phone number is required.',
                'ticket.required' => 'Ticket information is required.',
                'ticket.*.email_address.required' => 'The email address for each ticket is required.',
                'ticket.*.email_address.email' => 'The email address for each ticket must be a valid email.',
                'ticket.*.first_name.required' => 'The first name for each ticket is required.',
                'ticket.*.last_name.required' => 'The last name for each ticket is required.',
                'ticket.*.phone.required' => 'The phone number for each ticket is required.',
                'ticket.*.id.required' => 'The ticket ID is required.',
            ];

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
                $grand_total = round($order_total_details['final_total'] + $donation_amount, 2);

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
                    $stripeRes = $stripeController->createCheckout($customerData, $grand_total,$donation_amount,$cart->id);
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
                $order_object->donation_amount  = $donation_amount;
                $order_object->payment_intent  = $s_payment_intent_id;
                $order_object->payment_session_id  = $s_session_id;
                //$order_object->grand_total      = $order_total_details['final_total'];
                $order_object->grand_total      = $grand_total;
                $order_object->platform_fee_per = $setting->event_commission;
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
                // dd(request()->ticket);
                $total_18_tickets = 0;
                foreach (request()->ticket as $ticket_key => $ticket_arr) {
                    if(in_array($ticket_arr['id'], $available_ticket_array)){
                        $ticket_volunteer = Volunteer::where('email_address', $ticket_arr['email_address'])->first();
                        if(!$ticket_volunteer){ 
                            $ticket_volunteer = Volunteer::create([
                                                    'first_name'    => $ticket_arr['first_name'] , 
                                                    'last_name'     => $ticket_arr['last_name'] , 
                                                    'email_address' => $ticket_arr['email_address'] , 
                                                    'phone'         => formatPhoneNumber($ticket_arr['phone']),
                                                    'is_newsletter'     => $newsletter,
                                                ]);
                            $ticket_volunteer->refresh();
                        }elseif($ticket_volunteer && !$ticket_volunteer->status){
                                throw new CustomException("$ticket_arr[email_address] is alrady inactive by admin");
                        } elseif ($ticket_volunteer && trim($ticket_volunteer->phone)==''){
                            $ticket_volunteer->phone = formatPhoneNumber($ticket_arr['phone']);
                            $ticket_volunteer->save();
                            $ticket_volunteer->refresh();
                        }

                        $t_obj = EventTicket::findOrfail($ticket_arr['id']);
                        $waiver_handler_name = @$ticket_arr['waiver_handler_name'] ? : $ticket_volunteer->name;
                        $waiver_handler_email = @$ticket_arr['waiver_handler_email'] ? : $ticket_volunteer->email_address;
                        $is_under_18 = isset($ticket_arr['is_under_18']) ? 1 : 0;
                        $total_18_tickets = isset($ticket_arr['is_under_18'])?$total_18_tickets+1:$total_18_tickets;
                        $allPrice = $t_obj->all_price;
                        $total_platform_fee += $allPrice['ticket_platform_fee'];
                        $event_order_ticket_obj = EventOrderTicket::create([
                                                    'order_id'       => $order_object->id , 
                                                    'volunteer_id'   => $ticket_volunteer->id , 
                                                    'event_id'       => $event->id , 
                                                    'ticket_id'      => $ticket_arr['id'] , 
                                                    'price'          => $allPrice['ticket_price'], 
                                                    'ticket_base_price' => $allPrice['ticket_base_price'], 
                                                    'ticket_platform_fee' => $allPrice['ticket_platform_fee'], 
                                                    'tikcet_org_payout' => $allPrice['tikcet_org_payout'], 
                                                    //'price'          => $t_obj->ticket_type == 'free' ? 0 : $t_obj->ticket_price , 
                                                    'ticket_number'  => $this->generateRandomTicketNumber() , 
                                                    'ticket_status'  => 'approved' , /**ideally it should be pending */ 
                                                    'ticket_answer'  => @$ticket_arr['answer'] ,
                                                    'is_under_18'    => $is_under_18 , 
                                                    'waiver_handler_name'  => $waiver_handler_name  ,
                                                    'waiver_handler_email' => $waiver_handler_email ,
                                                    'rsvp_confirmation'    => 1, // RSVP values added here after RSVP Waiver flow changes 
                                                    'rsvp_filled_at'    => now(), 
                                                ]);
                        
                        if(SEND_MAIL){
                            $email_people_array[$ticket_volunteer->email_address] = [   
                                'purchase_by_name' => $creating_volunteer->first_name.' '.$creating_volunteer->last_name,
                                'purchase_by_email' => $creating_volunteer->email_address,
                                'recipient_name' => $ticket_volunteer->name,
                                'recipient_email' => $ticket_volunteer->email_address,
                                'event_title'    => $event->event_name,
                                'event_date'     => $event->startTimeAndEndTime,
                                'event'          => $event,
                                'event_location' => $event->fullAddress,
                                'is_under_18'    => $is_under_18 , // or true if applicable
                                'waiver_link'    => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($event_order_ticket_obj->id)]),
                                //'cancelRegistrationUrl' => route('cancel-rsvp-get', ['ticket' => encrypt($event_order_ticket_obj->id),'type'=>1]),
                                'cancelRegistrationUrl' => $this->mailUrl('front-cleanup-attending'),
                                'waiver_name'    => $waiver_handler_name, 
                                'waiver_email'   => $waiver_handler_email, 
                                'event_pdf_link' => $this->mailUrl('front-registration-calendar', ['slug' => $event->slug]), 
                                'organizer_name' => $event->creator->name,
                                'event_link'     => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                            ];
                            /* $email_people_array[$ticket_volunteer->email_address] = 
                                                new \App\Mail\SendMail(
                                                    "🌿 You’re Invited to Join ".$event->event_name.'!', 
                                                    'pages.emails.thankyou-for-order-mail', 
                                                    [   
                                                        'purchase_by_name' => request()->checkout_first_name.' '.request()->checkout_last_name,
                                                        'recipient_name' => $ticket_volunteer->name,
                                                        'event_title'    => $event->event_name,
                                                        'event_date'     => $event->startTimeAndEndTime,
                                                        'event'          => $event,
                                                        'event_location' => $event->fullAddress,
                                                        'is_under_18'    => $is_under_18 , // or true if applicable
                                                        'waiver_link'    => route('view-waiver-form', ['ticket' => encrypt($event_order_ticket_obj->id)]),
                                                        'waiver_name'    => $waiver_handler_name, 
                                                        'waiver_email'   => $waiver_handler_email, 
                                                        'event_pdf_link' => route('front-registration-calendar', ['slug' => $event->slug]), 
                                                        'organizer_name' => $event->creator->name,
                                                    ]
                                                ); */
                            if($is_under_18 && $event->waiverFormContentFilter()){
                                $waiver_people_array[$waiver_handler_email] = [ 
                                    'purchase_by_name' => $creating_volunteer->first_name.' '.$creating_volunteer->last_name,  
                                    'name'      => $waiver_handler_name,
                                    'childName'     => $ticket_volunteer->name,
                                    'childEmail'     => $ticket_volunteer->email_address,
                                    'isUnder18' => 1,
                                    'event'          => $event,
                                    'eventName' => $event->event_name,
                                    'eventDate'     => $event->startTimeAndEndTime,
                                    'eventLocation' => $event->fullAddress,
                                    'waiverUrl' => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($event_order_ticket_obj->id)]),
                                    //'cancelRegistrationUrl' => route('cancel-rsvp-get', ['ticket' => encrypt($event_order_ticket_obj->id),'type'=>1]),
                                    'cancelRegistrationUrl' => $this->mailUrl('cancel-registration-front', ['ticket' => encrypt($event_order_ticket_obj->id)]),
                                    'event_link' => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                                ];
                                /* $waiver_people_array[$waiver_handler_email] = 
                                                new \App\Mail\SendMail(
                                                    "📩 Action Needed: Confirm Participation for {$ticket_volunteer->name} in {$event->event_name}", 
                                                    'pages.emails.waiver-invitation-form', 
                                                    [ 
                                                        'purchase_by_name' => request()->checkout_first_name.' '.request()->checkout_last_name,  
                                                        'name'      => $waiver_handler_name,
                                                        'childName'     => $ticket_volunteer->name,
                                                        'childEmail'     => $ticket_volunteer->email_address,
                                                        'isUnder18' => 1,
                                                        'eventName' => $event->event_name,
                                                        'eventDate'     => $event->startTimeAndEndTime,
                                                        'eventLocation' => $event->fullAddress,
                                                        'waiverUrl' => route('view-waiver-form', ['ticket' => encrypt($event_order_ticket_obj->id)]),
                                                    ]
                                                ); */
                            }
                        
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
            
            $this->setFlashSession(true, "Great! we have created order.");
            if(SEND_MAIL){
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
                    'action_type'=>'registered',
                    'order'=>$order_object,
                    'event_name'=>$event->event_name,
                    //'event_date'=>$event->eventStartDateTime,
                    //'event_location'=>$event->fullAddress,
                    'event_tickets'=>$ticket_booking,
                    'platform_fee'=>$total_platform_fee,
                    'total_tickets'=>count(request()->ticket),
                    'total_18_tickets'=>$total_18_tickets,
                    'cleanup_hosting_link'=>$this->mailUrl('front-cleanup-hosting',['type'=>'organizer']),
                    'registration_list_link'=>$this->mailUrl('front-registration-list',['event'=>$event->id]),
                    //'event_url'=>route('view-event-details', ['slug' => $event->slug]),
                ];
                //$subject = "New registration for ".$event->event_name.' : '.$emailData['org_name'];
                $subject = "New Registration for your event : ".$event->event_name;
                $this->sendEmail($event->creator->email_address, $subject, $emailData, "email-to-org-for-register");

                // send email to who purchase the tickets
                $purchaseByEmailData = [
                    'event_name'=>$event->event_name,
                    //'event_date'=>$event->startTimeAndEndTime,
                    //'event_location'=>$event->fullAddress,
                    //'event_url'=>route('view-event-details', ['slug' => $event->slug]),
                    //'total_tickets'=>count($email_people_array),
                    'name'=>$creating_volunteer->first_name.' '.$creating_volunteer->last_name,
                    'event_tickets'=>$ticket_booking,
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
                        $sub = "🌿 Thank you for signing up for ".$event->event_name."!";
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
    public function viewWaiverPdf($ticket){
        $ticket = EventOrderTicket::with('volunteer')->findOrFail(decrypt($ticket));
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
        
        $ticket = EventOrderTicket::with('volunteer')->findOrFail(decrypt($ticket));
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
        $request->validate([
            'signature' => 'required|string',
        ]);
        $orderTicket = EventOrderTicket::with('event')->find(decrypt($ticket));
        if(!$orderTicket) 
            abort(404);

        if($orderTicket->waiver_filled_at != null && !empty($orderTicket->signature))
            throw new CustomException("This waiver is already filled and signed. if you have any doubt, please contact organizer.", 408);
            
        $orderTicket->update(['waiver_filled_at' => now(), 'signature' => $request->signature]);

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
            $total_fee = $orderTicket->ticket_platform_fee;
            
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
                $total_fee += $orderTicket->ticket_platform_fee;
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
        $this->createWeeklyAlert($volunteer->id, $alertName, $pincode,2);
        $this->setFlashSession(true, "Weekly alert has been created successfully.");
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
}
