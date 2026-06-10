<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
/**iCal  */
use Eluceo\iCal\Component\Calendar as ICal;
use Eluceo\iCal\Component\Event as ICalEvent;
use Illuminate\Http\Response;
/**System  */
use App\Models\User;
use App\Models\CohostPermission;
use App\Models\Event;
use App\Models\EventCoHost;
use App\Models\Volunteer;
use App\Models\EventCategory;
use App\Models\EventOrderTicket;
use App\Models\City;
use App\Models\EventOrder;
use App\Models\EventTicket;
use App\Models\VolunteerAlert;
use App\Models\Setting;
use App\Models\OrganiserLead;
use App\Models\CityStateZip;
use App\Models\AlertLog;
use App\Models\TicketNotificationLog;
use App\Models\VolunteerWidget;
use App\Models\VolunteerWidgetLog;
use App\Models\EventTicketNotification;
use App\Models\EventFinalizeEmailLog;
use App\Models\EventRescheduleLog;
use App\Models\TicketResendWaiverLog;
use App\Models\ReleaseNote;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
class FrontController extends Controller
{
    public function viewEvents(){
        $events = Event::with('category')->listactive()->where('is_private',0)->paginate(21)->withQueryString();
        //echo '<pre>';print_r($events->toArray());die();
        //$events_clone = clone collect($events->items());

        $mapEvents = Event::listactive()
        ->where('is_private', 0)
        //->select('id', 'event_name', 'latitude', 'longitude', 'start_datetime', 'end_datetime', 'venue')
        ->get();
        //echo '<pre>';
        //print_r($mapEvents->toArray());die();
        
        //$location_array = $events_clone->map(function ($event) {
        $location_array = $mapEvents->map(function ($event) {
                            if (!$event->latitude || !$event->longitude) 
                                return null; // Skip invalid entries
                            
                            return [
                                'lat' => (float) $event->latitude,
                                'lng' => (float) $event->longitude,
                                'title' => '<b>Name:</b> '.$event->event_name.'<br/><b>Date Time:</b> '.$event->startTimeAndEndTime.'<br/><b>Address:</b> '.$event->venue.'<br/><b>Link:</b> <a target="_blank" href="'.route('view-event-details', ['slug' => $event->slug]).'">View Event</a>',
                            ];
                        })->filter()->values()->all();

                       //print_r($events->toArray());die();
                        
        return view('pages.frontend.event-lists', [
            'timezonescode'     => config('app.timezonescode'),
            'events'            => $events,
            'location_array'    => $location_array ,
            'categories'        => EventCategory::all(),
            'seo_details'       => (object)[
                                    'title' => 'Upcoming Events',
                                    'description' => 'Browse the list of upcoming volunteer events and opportunities.',
                                    'keywords' => 'volunteer, events, upcoming, opportunities',
                                ]
        ]);
    }
    public function viewInternationalEvents(){
        if (!request()->filled('search')) {
            request()->merge(['search' => 'ICC2025']);
        }
        $events = Event::with('category')->listactive()->where('is_private',0)->paginate(21)->withQueryString();
        //$events_clone = clone collect($events->items());
        $mapEvents = Event::listactive()
        ->where('is_private', 0)
        ->select('id', 'event_name', 'latitude', 'longitude', 'start_datetime', 'end_datetime', 'venue')
        ->get();
        
        //$location_array = $events_clone->map(function ($event) {
        $location_array = $mapEvents->map(function ($event) {
                            if (!$event->latitude || !$event->longitude) 
                                return null; // Skip invalid entries
                            
                            return [
                                'lat' => (float) $event->latitude,
                                'lng' => (float) $event->longitude,
                                'title' => '<b>Name:</b> '.$event->event_name.'<br/><b>Date Time:</b> '.$event->start_datetime->format(config('app.dateTimeFormat')).' - '.$event->end_datetime->format(config('app.dateTimeFormat')).'<br/><b>Address:</b> '.$event->venue,
                            ];
                        })->filter()->values()->all();

                       //print_r($events->toArray());die();
                        
        return view('pages.frontend.international-event-lists', [
            'timezonescode'     => config('app.timezonescode'),
            'events'            => $events,
            'location_array'    => $location_array ,
            'categories'        => EventCategory::all(),
            'seo_details'       => (object)[
                                    'title' => 'International Events',
                                    'description' => 'Browse the list of international events and opportunities.',
                                    'keywords' => 'volunteer, events, international, opportunities',
                                ]
        ]);
    }
    public function sendRsvpEmails($slug){
        request()->validate([
            'subject' => 'required',
            'content' => 'required'
        ]);

        $event          = Event::where('slug', $slug)->detailactive()->firstOrFail();
        $email_address  = $event->attandee()->where('ticket_status', 'approved')->get()->pluck('email_address');
        $today_date     = now();
        foreach ($email_address as $email) {
            if(!$email)
                continue;

            try {
                Mail::raw(request()->content, function ($message) use($email) {
                    $message->to($email)
                            ->subject(request()->subject);
                });
                Storage::append('logs/email_trigger.log', "\n{$today_date} Success for {$email} - {$event->event_name}");
            } catch (\Exception $e)  {
                $errorMessage = $e->getMessage();
                Storage::append('logs/email_trigger.log', "\n{$today_date} Failed for {$email} - {$event->event_name}, ".$errorMessage);
            }
        } 

        $this->setFlashSession(true, "Emails has been trigger to the attendies of the system");
        return redirect()->route('front-cleanup-hosting', ['type' => 'organizer']);
    
    }
    public function viewEventPage($slug){
        //$event = Event::where('slug', $slug)->detailactive()->first();
        $event = Event::with(['creator', 'category'])->where('slug', $slug)->first();
        if(!isset($event->id)){
            abort(404, 'Event not found.');
        }
        // EVENT COUNTER
        $event->click_views = $event->click_views + 1; 
        $seo_details = (object)[
            'title'       => $event->meta_title ? :  $event->event_name ,
            'description' => $event->meta_description ? :  strip_tags($event->description),
            'keywords'    => $event->meta_focus_keyword ?? '',
        ];
        //print_r($event->toArray());
        //print_r($event->eventTickets->toArray());
        return  view('pages.frontend.event-detail',[
                    'event' => $event,
                    'seo_details' => $seo_details
                ]);
    }
    public function verifyTicketPasscode($ticket){
        $request = request();
        $validated = $request->validate([
            'passcode'    => 'required',
        ]);
        $ticket = EventTicket::where('id', decrypt($ticket))->first();
        if(!isset($ticket->id)){
            abort(404, 'Event not found.');
            return $this->generateJsonResponse(false,'Ticket not found.');
        }
        if($ticket->pass_code==$validated['passcode']){
            return $this->generateJsonResponse(true,'Pass code matched.');
        } else {
            return $this->generateJsonResponse(false,'Pass code not matched.');
        }
    }
    public function saveTicketNotify($ticket){
        if(auth()->guard('front')->user() && isset(auth()->guard('front')->user()->id)){
            $ticket = EventTicket::where('id', decrypt($ticket))->first();
            if(isset($ticket->id)){
                $user = auth()->guard('front')->user();
                $exists = $user->ticketNotifications()->where('ticket_id', $ticket->id)->exists();
                if (!$exists) {
                    $user->ticketNotifications()->create([
                        'ticket_id' => $ticket->id,
                    ]);
                    $this->setFlashSession(true, 'Notify request added successfully.');
                } else {
                    $this->setFlashSession(false, 'You have already requested for this ticket.');
                }
                return redirect()->back();
            } else {
                $this->setFlashSession(false , 'Ticket not found.');
                return redirect()->back();
            }
        } else {
            $this->setFlashSession(false , 'Please login to add notify request.');
            return redirect()->back();
        }
    }
    public function dashboard(){
        $data['seo_details'] = (object)['title' => 'Dashboard'];
        return view('pages.frontend.dashboard', $data);
    }
    public function releaseNotes()
    {
        $releaseNotes = ReleaseNote::where('status', 1)
            ->where(function ($q) {
                if (request()->search) {
                    $q->where('title', 'like', '%' . request()->search . '%')
                        ->orWhere('version', 'like', '%' . request()->search . '%')
                        ->orWhere('description', 'like', '%' . request()->search . '%');
                }
            })
            ->orderByDesc('release_date')
            ->orderByDesc('published_at')
            ->paginate(10)
            ->withQueryString();

        auth()->guard('front')->user()->update([
            'release_note_last_seen_at' => now(),
        ]);

        return view('pages.frontend.release_notes', [
            'releaseNotes' => $releaseNotes,
            'seo_details' => (object)['title' => 'Release Notes'],
        ]);
    }

    public function cleanupAttending(){
        //$attending_events = auth()->guard('front')->user()->attendingEvents()
        $attending_events = auth()->guard('front')->user()->attendingEventsWithoutActive()
                                ->with('category')
                                ->where(function($q){
                                    if(request()->search){
                                        $q->where("event_name","LIKE", "%".request()->search."%");
                                    }
                                    if (request()->event_type) {
                                        $now = now();
                                        if(request()->event_type==1){
                                            $q->where("start_datetime" ,">",$now);
                                        }elseif(request()->event_type==2){
                                            $q->where("end_datetime" , "<",$now);
                                        }elseif(request()->event_type==3){
                                            $q->where("start_datetime", "<=", $now)
                                            ->where("end_datetime", ">=", $now);
                                        }
                                    }
                                })->latest()->paginate()->withQueryString();
        return view('pages.frontend.cleanup-attending' , [
            'attending_events' => $attending_events , 
            'seo_details'      => (object)['title' => 'Attending Events'] ,
        ]);
    }
    public function cleanupHosting($type)
    {
        if(auth()->guard('front')->user()->is_organiser == 0){
            $this->setFlashSession(false , 'Please organizer atleast 1 event to view this page.');
            return redirect()->route('front-dashboard');
        }
        if($type == 'cohost'){
            $hosting_events_builder = auth()->guard('front')->user()->cohostingEvents();
        }else{
            $hosting_events_builder = auth()->guard('front')->user()->events(); 
        }
        $cohostEventCount = auth()->guard('front')->user()->cohostingEvents()->active()->count();
        $hostingEventCount = auth()->guard('front')->user()->events()->active()->count();

        //$hosting_events = $hosting_events_builder->orderBy('start_datetime','desc')->active()->paginate(20)->withQueryString();
        $hosting_events = $hosting_events_builder->with('category')->orderBy('start_datetime','desc')->paginate(20)->withQueryString();
        //echo $hostingEventCount;
        //print_r($hosting_events->toArray());
        //die();
        return view('pages.frontend.cleanup-hosting', [
            'cohost_event_count'=>$cohostEventCount,
            'hosting_event_count'=>$hostingEventCount,
            'hosting_events' => $hosting_events,
            'seo_details' => (object)['title' => 'Cleanups I\'m Hosting'],
        ]);
    }
    public function cleanupOngoing()
    {
        $user = auth()->guard('front')->user();

        if ($user->is_organiser == 0) {
            $this->setFlashSession(false, 'Please organizer atleast 1 event to view this page.');
            return redirect()->route('front-dashboard');
        }

        $hosting_events = Event::with('cohostRecords')
            ->withCount([
                'tickets as volunteer_count' => function ($q) {
                    $q->active();
                },
                'tickets as checked_in_count' => function ($q) {
                    $q->active()->where('checkin', 1);
                },
            ])
            ->present()
            ->where('status', 'published')
            ->whereNull('cancelled_at')
            ->where(function ($q) {
                $q->whereNull('is_completed')
                    ->orWhere('is_completed', '!=', 1);
            })
            ->where(function ($q) use ($user) {
                $q->where('creator_id', $user->id)
                    ->orWhereHas('cohosts', function ($subQ) use ($user) {
                        $subQ->where('volunteers.id', $user->id)
                            ->where('event_cohost.request_status', 'approved');
                    });
            })
            ->orderBy('start_datetime', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('pages.frontend.cleanup-ongoing', [
            'hosting_events' => $hosting_events,
            'seo_details' => (object)['title' => 'Ongoing Cleanups'],
        ]);
    }
    public function volunteerCheckin($id)
    {
        $authUser = auth()->guard('front')->user();

        $event = Event::with('creator')->findOrFail(decrypt($id));

        $cohostEventIds = $authUser->cohostingEvents()->pluck('events.id');
        $hostingEventIds = $authUser->events()->pluck('events.id');
        $allowedEventIds = $cohostEventIds->merge($hostingEventIds);

        if (!$allowedEventIds->contains($event->id)) {
            $this->setFlashSession(false, 'Unauthorized action.');
            return redirect()->route('front-cleanup-ongoing');
        }

        $registrations = EventOrderTicket::with(['volunteer', 'ticket'])
            ->where('event_id', $event->id)
            ->where('rsvp_confirmation', 1)
            ->where(function ($q) {
                if (request()->filled('search')) {
                    $search = request()->search;
                    $q->whereHas('volunteer', function ($subQ) use ($search) {
                        $subQ->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                            ->orWhere('email_address', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
                }

                if (request()->status == 'signed') {
                    $q->whereNotNull('waiver_filled_at');
                } elseif (request()->status == 'unsigned') {
                    $q->whereNull('waiver_filled_at');
                } elseif (request()->status == 'checked_in') {
                    $q->where('checkin', 1);
                } elseif (request()->status == 'not_checked_in') {
                    $q->where(function ($subQ) {
                        $subQ->whereNull('checkin')->orWhere('checkin', 0);
                    });
                } elseif (request()->status == 'checked_in_signed') {
                    $q->where('checkin', 1)->whereNotNull('waiver_filled_at');
                }
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('pages.frontend.volunteer-check-in', [
            'event' => $event,
            'event_id' => $id,
            'registrations' => $registrations,
            'seo_details' => (object)['title' => 'Volunteer Check-in'],
        ]);
    }
    public function exportVolunteerCheckinPdf($id)
    {
        $authUser = auth()->guard('front')->user();

        $event = Event::with('creator')->findOrFail(decrypt($id));

        $cohostEventIds = $authUser->cohostingEvents()->pluck('events.id');
        $hostingEventIds = $authUser->events()->pluck('events.id');
        $allowedEventIds = $cohostEventIds->merge($hostingEventIds);

        if (!$allowedEventIds->contains($event->id)) {
            $this->setFlashSession(false, 'Unauthorized action.');
            return redirect()->route('front-cleanup-ongoing');
        }

        $registrations = EventOrderTicket::with(['volunteer', 'ticket'])
            ->where('event_id', $event->id)
            ->where('rsvp_confirmation', 1)
            ->orderBy('id', 'asc')
            ->get();

        $html = view('pages.frontend.volunteer-check-in-pdf', [
            'event' => $event,
            'registrations' => $registrations,
        ])->render();

        $pdf = \PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ])->loadHTML($html)->setPaper('a4', 'landscape');

        $filename = 'volunteer_check_in_' . $event->id . '_' . time() . '.pdf';

        return $pdf->download($filename);
    }

    public function volunteerSignAndCheckin($id)
    {
        request()->validate([
            'signature' => 'required|string',
        ], [
            'signature.required' => 'Please provide volunteer signature.',
        ]);

        try {
            $orderTicket = EventOrderTicket::with(['event', 'volunteer'])->findOrFail(decrypt($id));
            $authUser = auth()->guard('front')->user();

            $cohostEventIds = $authUser->cohostingEvents()->pluck('events.id');
            $hostingEventIds = $authUser->events()->pluck('events.id');
            $allowedEventIds = $cohostEventIds->merge($hostingEventIds);

            if (!$allowedEventIds->contains($orderTicket->event_id)) {
                $this->setFlashSession(false, 'Unauthorized action.');
                return redirect()->back();
            }

            if ($orderTicket->rsvp_confirmation != 1) {
                $this->setFlashSession(false, 'You can not check-in before RSVP confirmation.');
                return redirect()->back();
            }

            if (empty($orderTicket->waiver_filled_at)) {
                $orderTicket->waiver_filled_at = now();
                $orderTicket->signature = request()->signature;
                $orderTicket->save();
            }

            return $this->ticketMarkCheckin($id, true);
        } catch (\Exception $e) {
            $this->setFlashSession(false, 'Issues occured while signing waiver and marking check-in.');
            return redirect()->back();
        }
    }
    public function volunteerWaiverQr($id)
    {
        try {
            $orderTicket = EventOrderTicket::with(['event', 'volunteer'])->findOrFail(decrypt($id));
            $authUser = auth()->guard('front')->user();

            $cohostEventIds = $authUser->cohostingEvents()->pluck('events.id');
            $hostingEventIds = $authUser->events()->pluck('events.id');
            $allowedEventIds = $cohostEventIds->merge($hostingEventIds);

            if (!$allowedEventIds->contains($orderTicket->event_id)) {
                return response()->json($this->generateResponse(false, 'Unauthorized action.'), 403);
            }

            $qrCheckinToken = encrypt('volunteer_checkin_qr:'.$orderTicket->id);

            $waiverUrl = route('view-waiver-form', [
                'ticket' => encrypt($orderTicket->id),
                'qr_checkin' => $qrCheckinToken,
            ]);

            $qrHtml = QrCode::size(280)
                ->margin(2)
                ->generate($waiverUrl);

            return response()->json($this->generateResponse(true, 'QR code generated successfully.', [
                'qr_html' => (string) $qrHtml,
                'waiver_url' => $waiverUrl,
            ]));
        } catch (\Exception $e) {
            return response()->json($this->generateResponse(false, 'Unable to generate QR code.'), 500);
        }
    }

    public function volunteerMarkCheckin($id)
    {
        return $this->ticketMarkCheckin($id, true);
    }
    public function cleanupHostingDelete($id){
        $event = Event::where('creator_id', auth()->guard('front')->user()->id)->where("id", $id)->firstOrFail();
        if($event->orders->count())
            return $this->generateJsonResponse(false , 'Opps! Order are connected with this event, we can\'t delete this.');    
        $res = $event->delete(); 
        return $this->generateJsonResponse($res);
    }
    public function emailAlerts()
    {
        $data['seo_details'] = (object)['title' => 'Email Alerts'];
        //$data['listing_data'] = auth()->guard()->user()->alerts;
        $data['listing_data'] = auth()->guard()->user()->alerts()->with('statusUpdatedByAdmin')->get();
        return view('pages.frontend.cleanup-alerts', $data);
    }
    public function unsubscribeAlert($user,$alert=null){
        $data = [];
        $data['seo_details'] = (object)['title' => 'Unsubscribe Alert']; 
        $data['url'] = route('confirmed-unsubscribe-alert',['user'=>$user,'alert'=>$alert]); 
        if(!empty($alert)){
            $data['alert'] = VolunteerAlert::where('id', decrypt($alert))->firstOrFail();
        }
        return view('pages.frontend.alert-unsubscribed', $data);
    }
    public function confirmedUnsubscribeAlert($user,$alert=null){
        if(!empty($alert)){
            $emailAlert = VolunteerAlert::where('id', decrypt($alert))->firstOrFail();
            $emailAlert->status = 0;
            $emailAlert->save();
            $msg = 'Alert unsubscribed successfully.';
        } else {
            VolunteerAlert::where('volunteer_id', decrypt($user))->update(['status' => 0]);
            $msg = 'All alerts unsubscribed successfully.';
        }
        $data = [];
        $data['seo_details'] = (object)['title' => 'Unsubscribe Alert']; 
        $data['msg'] = $msg; 
        return view('pages.frontend.alert-unsubscribed', $data);
    }
    /* public function disableEmailAlerts($id){
        $emailAlert = VolunteerAlert::where('volunteer_id', auth()->guard('front')->user()->id)
                        ->where('id', $id)->firstOrFail();
        $emailAlert->status = !$emailAlert->status;
        $emailAlert->save();
        $this->setFlashSession(true);
        return redirect()->back();
    } */
    public function disableEmailAlerts($id)
    {
        $user = auth()->guard('front')->user();

        $emailAlert = VolunteerAlert::where('volunteer_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($emailAlert->status == 0 && $emailAlert->status_updated_by_role == 2) {
            $this->setFlashSession(false, 'This alert was disabled by admin, so you cannot enable it.');
            return redirect()->back();
        }

        $emailAlert->status = !$emailAlert->status;
        $emailAlert->status_updated_by = $user->id;
        $emailAlert->status_updated_by_role = 1;
        $emailAlert->save();

        $this->setFlashSession(true);

        return redirect()->back();
    }


    public function deleteAlerts($id){
        $emailAlert = VolunteerAlert::where('volunteer_id', auth()->guard('front')->user()->id)
                        ->where('id', $id)->firstOrFail();
        $res = $emailAlert->delete();
        return $this->generateJsonResponse($res);
    }
    public function triggerEmailAlert(){

    }
    public function saveEmailAlerts(){
        $request = request();
        $validated = $request->validate([
            'alert_name' => 'required|string|max:255',
            'city_id'    => 'required',
        ]);
        
        $pincode = '';
        $cityStateZip = CityStateZip::find($validated['city_id']);
        if(isset($cityStateZip->id)){
            $pincode = $cityStateZip->zipcode;
        }

        $user = auth()->guard('front')->user(); 
        (new FrontOrderController)->createWeeklyAlert(
            $user->id,
            $validated['alert_name'],
            $pincode
        );
        /* \App\Models\VolunteerAlert::create([
            'volunteer_id'    => $user->id,
            'alert_name'      => $validated['alert_name'],
            'csz_id'         => $validated['city_id'],
            'email_frequency' => 'weekly',
            'status'          => 1,
        ]); */

        $this->setFlashSession(true, 'Email alert created successfully.');
        return redirect()->back();
    }
    public function manageTicketTypeHtml($id=null){
        $request = request();
        $validated = $request->validate([
            'event_id' => 'required',
        ]);
        $event = Event::where('id', decrypt($validated['event_id']))->first();
        if(!isset($event->id)){
            return $this->generateJsonResponse(false, 'Event not found');
        }
        $setting = Setting::first();
        $html = '';
        $eventTicket = null;
        $event_id = $validated['event_id'];
        $ticket_id = '';
        $ticket_name = '';
        $ticket_type = 'free';
        $ticket_price = 0;
        $available_quantity = '';
        $max_qty_allowed_per_order = '';
        $sell_start_time = '';
        $sell_end_time = '';
        $pass_code = '';
        $ticket_description = '';
        $charge_bear_by = $event->charge_bear_by;
        $fee = 0;
        $est_fee = 0;
        $b_total = 0;
        $b_fee_text = 'Platform Fee Included';
        $net_org_total = 0;
        if(!empty($id)){
            $eventTicket = EventTicket::where('id', decrypt($id))->first();
            if(isset($eventTicket->id)){
                $ticket_id = encrypt($eventTicket->id);
                $ticket_name = $eventTicket->ticket_name;
                $ticket_type = $eventTicket->ticket_type;
                $ticket_price = formatAmt($eventTicket->ticket_price);
                $available_quantity = $eventTicket->available_quantity;
                $max_qty_allowed_per_order = $eventTicket->max_qty_allowed_per_order;
                $sell_start_time = $eventTicket->sell_start_time;
                $sell_end_time = $eventTicket->sell_end_time;
                $pass_code = $eventTicket->pass_code;
                $ticket_description = $eventTicket->ticket_description;
                $fee = formatAmt(($eventTicket->ticket_price*$setting->event_commission)/100);
                $est_fee = formatAmt((($eventTicket->ticket_price*3)/100)+0.3);
                $b_total = $charge_bear_by==1?(formatAmt($eventTicket->ticket_price+$fee)):formatAmt($eventTicket->ticket_price);
                $b_fee_text = $charge_bear_by==1?'Platform Fee':'Platform Fee Included';
                $net_org_total = $charge_bear_by==1?(formatAmt($eventTicket->ticket_price)):formatAmt($eventTicket->ticket_price-$fee);
            }
        }
        $allowPaidTickets = true;
        if(!(!empty(auth()->guard('front')->user()->stripe_email) && !empty(auth()->guard('front')->user()->stripe_account_id)))
        {
            $allowPaidTickets = false;
        }
        $html = '
        <form method="post" id="ticketTypeForm" action="'.route('save-ticket-type').'">
        <div class="row mt-2 mb-3">
            <input type="hidden" name="event_id" value="'.$event_id.'">
            <input type="hidden" class="form-ele" name="ticket_id" value="'.$ticket_id.'">
            <input type="hidden" id="p_charge_bear_by" name="p_charge_bear_by" value="'.$charge_bear_by.'">
            <input type="hidden" id="p_fee_per" name="p_fee_per" value="'.$setting->event_commission.'">
            <div class="col-md-8">
                <div class="form-group">
                    <div class="row mb-2">
                        <label for="ticket_name" class="pt-3 col-sm-9">
                            Ticket Name*
                        </label>
                    </div>
                    <div class="d-flex">
                        <input type="text" name="ticket_name" value="'.$ticket_name.'" class="form-control form-ele onlyTextNumberSplaceMask" required="">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 mt-3">
                        <label for="ticket_type">Type*</label>
                        <select class="form-select form-ele mt-1" id="p_ticket_type_select" required="" name="ticket_type">
                            <option value="free" '.($ticket_type=='free'?'selected':'').'>Free</option>
                            <option value="paid" '.($ticket_type=='paid'?'selected':'').' '.(!$allowPaidTickets?'disabled':'').'>Paid</option>
                        </select>
                    </div>
                    <div class="col-sm-6 mt-3">
                        <label for="ticket_price">Ticket Price*</label>
                        <input type="number" fee_per="10" name="ticket_price" id="p_ticket_price" value="'.$ticket_price.'" class="form-control form-ele mt-1" '.($ticket_type=='free'?'readonly':'').' required="" min="0">
                    </div>
                    <div class="col-sm-6 mt-3">
                        <label for="available_quantity">Total Tickets*</label>
                        <input type="number" step="1" oninput="this.value = this.value.replace(/[^0-9]/g, \'\')" name="available_quantity" value="'.$available_quantity.'" class="form-control form-ele mt-1" min="0" required="">
                    </div>
                    <div class="col-sm-6 mt-3">
                        <label for="max_qty_allowed_per_order">Max Tickets Per Order*</label>
                        <input type="number" oninput="this.value = this.value.replace(/[^0-9]/g, \'\')" name="max_qty_allowed_per_order" value="'.$max_qty_allowed_per_order.'" class="form-control form-ele mt-1" min="1" required="">
                    </div>
                    <div class="col-sm-6 mt-3">
                        <label for="ticket_sell_start_time">Sell Start Date*</label>
                        <div class="loc-group position-relative">
                            <input autocomplete="off" type="text" class="form-control mt-1 form-ele tk_sell_start_date" name="sell_start_time" value="'.$sell_start_time.'" required="">
                            <span class="absolute-icon" style="top:0;"><i class="fa-solid fa-calendar-days"></i></span>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-3">
                        <label for="ticket_sell_end_time">Sell End Date*</label>
                        <div class="loc-group position-relative">
                            <input autocomplete="off" type="text" class="form-control mt-1 form-ele tk_sell_end_date" name="sell_end_time" value="'.$sell_end_time.'" required="">
                            <span class="absolute-icon" style="top:0;"><i class="fa-solid fa-calendar-days"></i></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="form-group">
                            <label for="pass_code">Pass Code</label>
                            <input type="text" name="pass_code" value="'.$pass_code.'" class="form-control form-ele mt-1">
                        </div>
                        <smal style="font-size: 12px;">Want to keep this ticket private? Enter a passcode here (passcodes as case sensitive). If you want it open to all volunteers, just leave it blank.</smal>
                    </div>
                    <div class="col-md-12 mt-3">
                        <div class="form-group">
                            <label for="ticket_description">Ticket Description</label>
                            <textarea name="ticket_description" class="form-control form-ele " rows="5">'.$ticket_description.'</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <h6 class="mb-3">Price Breakdown</h6>
                        <p class="mb-2"><small>Base price</small> <small class="float-end">$<span name="pb_base_price_" id="pb_base_price" class="form-ele">'.$ticket_price.'</span></small></p>
                        <p><small><span name="pb_fee_text_" id="pb_fee_text">'.$b_fee_text.'</span> ('.$setting->event_commission.'%)</small> <small class="float-end">$<span name="pb_platform_fee_" id="pb_platform_fee" class="form-ele">'.$fee.'</span></small></p>
                        <p><small>Estimated Processing Fee<br/>(~3% + $0.30)</small> <small class="float-end">$<span name="pb_est_fee_" id="pb_est_fee" class="form-ele">'.$est_fee.'</span></small></p>
                        <hr class="my-2" />
                        <h6 class="mt-3 mb-3"><span>Total to Attendee</h6span> <span class="float-end h5 text-primary">$<span name="pb_final_price_" id="pb_final_price" class="form-ele">'.$b_total.'</span></span></h6>
                        <p class="mb-3" style="color: #717171;font-style: italic;">Net to Organizer: $<span id="pb_net_org_price">'.$net_org_total.'</span> (after platform fee)</p>
                        <p style="color: #717171;font-style: italic;">Processing fees is estimated (~3% + $0.30) and may vary based on payment method and card type. Final amount is determined by Stripe.</p>
                        <small style="color: #717171;font-style: italic;">Based on your last saved "Charge Bear By" setting in your Ticket Settings section.</small>
                    </div>
                </div>
            </div>
        </div>
        </form>
        ';
        return $this->generateJsonResponse(true, '', $html);
    }
    public function postEvent($id = null){
        $eventObj = $id ? Event::findOrFail(decrypt($id)) : null ;
        $setting = Setting::first();
        $open_tab = request()->has('open_tab') ? request()->open_tab : '' ;
        return view('pages.frontend.cleanup-post', [
            'form_data'     => $eventObj,
            'open_tab'     => $open_tab,
            'setting'     => $setting,
            'is_updating'   => $eventObj ? true : false ,
            'seo_details'   => (object)['title' => "Post A Cleanup"],
            'countries'     => \App\Models\Country::orderBy('country_name')->get(),
            'bring_tools'   => \App\Models\Tool::orderBy('name')->get(),
            'provided_tools'=> \App\Models\Tool::orderBy('name')->get(),
            'categories'    => \App\Models\EventCategory::orderBy('name')->get(),
            /* 'event_tags'    => \App\Models\Tag::where('type', 'event')->orderBy('name')->get(),
            'volunteer_tags'=> \App\Models\Tag::where('type', 'volunteer')->orderBy('name')->get(), */
            'event_tags'    => \App\Models\Tag::orderBy('name')->get(),
            'volunteer_tags'=> \App\Models\Tag::orderBy('name')->get(),
            'timezones'     => config('app.timezones'),
            'reoccurring'   => config('app.reoccurring'),
            'volunteers'    => \App\Models\Volunteer::orderBy('first_name')->get(),
        ]);
    }

    public function duplicateAnEvent($slug){
        $event = Event::where('slug' , $slug)->first();
        $today_dt = date('Y-m-d H:i:s');
        $new_event = $event->replicate();
        $new_event->event_name  = $event->event_name . ' (Clone)';
        $new_event->slug        = $this->generateSlug(Event::class, 'slug', $event->slug , 0); 
        $new_event->creator_id  = auth()->guard('front')->user()->id;
        $new_event->created_at  = $today_dt;
        $new_event->updated_at  = $today_dt;
        $new_event->status  = 'draft';
        $new_event->waste_collected  = null;
        $new_event->area_cleaned  = null;
        //$new_event->team_name  = null;
        $new_event->volunteer_hours  = null;
        $new_event->post_event_notes  = null;
        $new_event->click_views  = 0;
        $new_event->start_datetime = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($today_dt)));
        $new_event->end_datetime = date('Y-m-d H:i:s', strtotime('+2 day', strtotime($today_dt)));
        $new_event->site_publish_date = date('Y-m-d', strtotime($today_dt));
        $new_event->site_depublish_date = date('Y-m-d', strtotime('+3 day', strtotime($today_dt)));
        $new_event->wp_id = null;  
        $new_event->wp_meta = null;  
        $new_event->save();
        $new_event_id = $new_event->id;
        $new_event->eventTickets()->createMany(
            $event->eventTickets->map(function ($ticket) use ($new_event_id, $today_dt) {
                $newTicket = $ticket->replicate();
                $newTicket->event_id = $new_event_id;
                $newTicket->sell_start_time = $today_dt;  
                $newTicket->sell_end_time = date('Y-m-d', strtotime('+1 day', strtotime($today_dt)));  
                $newTicket->wp_id = null;
                $newTicket->wp_meta = null;
                $newTicket->created_at = $today_dt;
                $newTicket->updated_at = $today_dt;
                return $newTicket->toArray();
            })->toArray()
        );
        $new_event->tags()->attach($event->tags()->pluck('tag_id')->toArray());
        $new_event->volunteertags()->attach($event->volunteertags()->pluck('tag_id')->toArray());
        $new_event->bringTools()->attach(
            $event->bringTools()->pluck('tool_id')->mapWithKeys(function ($toolId) {
                return [
                    $toolId => ['should_bring' => 1]
                ];
            })->toArray()
        );
        $new_event->providedTools()->attach(
            $event->providedTools()->pluck('tool_id')->mapWithKeys(function ($toolId) {
                return [
                    $toolId => ['should_bring' => 0]
                ];
            })->toArray()
        );

        return redirect()->route('front-post-a-event', encrypt($new_event->id));
    }
    public function duplicateAnEventOLD($slug){
        $event              = Event::where('slug' , $slug)->first();
        $event->event_name  = $event->event_name . ' (Clone)';
        $event->slug        = $this->generateSlug(Event::class, 'slug', $event->slug , 0);
        $event->tickets     = $event->eventTickets->map(function($ticket){
                                $ticket->id = 0;    
                                $today_dt = date('Y-m-d');
                                $ticket->sell_start_time = $today_dt;  
                                $ticket->sell_end_time = date('Y-m-d', strtotime('+1 day', strtotime($today_dt)));  
                                return $ticket; 
                            });  
        $event->tags        = $event->tags()->pluck('event_tags.tag_id')->toArray(); 
        $event->volunteer_tags  = $event->volunteertags()->pluck('event_volunteer_tags.tag_id')->toArray(); 
        $event->bring_tools     = $event->bringTools()->pluck('tools.id')->toArray(); 
        $event->provided_tools  = $event->providedTools()->pluck('tools.id')->toArray();
        $event->id              = null; // Reset ID to create a new record

        $today_dt = date('Y-m-d H:i:s');
        $event->start_datetime = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($today_dt)));
        $event->end_datetime = date('Y-m-d H:i:s', strtotime('+2 day', strtotime($today_dt)));
        $event->site_publish_date = date('Y-m-d', strtotime($today_dt));
        $event->site_depublish_date = date('Y-m-d', strtotime('+3 day', strtotime($today_dt)));


        session()->flashInput($event->toArray());
        return redirect()->route('front-post-a-event');
    }
    public function saveCleanUpBasicDetails(){
        $request = request();
        $start_datetime = request()->start_datetime;
        $end_datetime = request()->end_datetime;
        $site_publish_date = request()->site_publish_date;
        $site_depublish_date = request()->site_depublish_date;
        if(!empty($start_datetime) && !empty($site_publish_date) && (strtotime($start_datetime) < strtotime($site_publish_date)) ){
            return $this->generateJsonResponse(false, 'Event start date must be greater than or equal to site publish date.');
        }

        $isUpdating = request()->event_id ? true : false;
        if($isUpdating){
            $eventObj = Event::where('id', decrypt(request()->event_id))->firstOrFail();
            if(request()->participation_type!=1){
                if(isset($eventObj->eventTickets[0])){
                    return $this->generateJsonResponse(false, 'Remove ticket types before changing participation type.');
                }
            }
        }else{
            $is_org = auth()->guard('front')->user()->is_organiser==1?1:0;
            $eventObj = new Event();
            $eventObj->creator_id = auth()->guard('front')->user()->id;
            $eventObj->creator_type = "App\\Models\\Volunteer";
            /**declare volunteer as the organiser */
            $generated_organiser_slug = auth()->guard('front')->user()->organisation_landing_slug ? :
                                                $this->generateSlug(Volunteer::class, 'organisation_landing_slug', auth()->guard('front')->user()->name, $ignoreId = 0);
            auth()->guard('front')->user()->is_organiser = 1 ;
            auth()->guard('front')->user()->organisation_landing_slug = $generated_organiser_slug;

            auth()->guard('front')->user()->save();

            // hit the wp api to update the user 
            //if(!$is_org){
                $this->syncWithWP(auth()->guard('front')->user());
            //}

        }
        if(request()->participation_type==4){
            if(empty(request()->participation_type_external_link)){
                return $this->generateJsonResponse(false, 'Please enter external link.');
            }
        }
        $validatedObj = request()->validate([
                            'event_name'      => 'required|string|max:255',
                            'event_category_id' => 'required|exists:event_categories,id', 
                            'description'     => 'required|string',
                            'start_datetime'  => 'required|date',
                            'end_datetime'    => 'required|date|after:start_datetime',
                            'site_publish_date'   => 'required|date',
                            'site_depublish_date' => 'required|date|after_or_equal:site_publish_date',
                            'event_timezone'  => 'required|string',
                            'country_id'      => 'required|exists:countries,country_id',
                            'venue'           => 'required|string|max:255',
                            'pincode'         => 'required|string|max:20',
                            'latitude'         => 'required|string|max:20',
                            'longitude'         => 'required|string|max:20',
                            'allow_community_service_form' => 'nullable|boolean',
                            'is_private'        => 'nullable|boolean',
                            'slug'            => 'nullable|string|max:255|unique:events,slug,'.$eventObj->id ? : 0 ,
                            'participation_type' => 'required',
                            'participation_type_external_link' => 'nullable',
                        ]);
        
        $eventObj->fill($validatedObj);
        $eventObj->is_private = $request->has('is_private') ? 1 : 0;
        $eventObj->allow_community_service_form = $validatedObj['allow_community_service_form'] ?? 0;
        $eventObj->slug = $this->generateSlug(Event::class, 'slug', $validatedObj['slug'] ? : $validatedObj['event_name'], $eventObj->id ?? 0);
        /**location handling */
        /* $coordinate = $this->getGoogleCoordinates(request()->venue);
        $eventObj->latitude = $coordinate['latitude'];
        $eventObj->longitude = $coordinate['longitude']; */
        /**save the event object */
        $res = $eventObj->save();
        return $this->generateJsonResponse($res, 'Event details saved successfully.', [
            'event_instance' => encrypt($eventObj->id) ,
            'section_html'   => [] ,
            'event'          => $eventObj                                                    
        ]);
    }

    public function saveCleanUpMediaDetails(){
            $request = request();
            $validated = $request->validate([
                'event_id'          => 'required',
                'video_url'         => 'nullable|url',
                'event_reoccurance' => 'nullable|string|max:50',
                'cropped_image'     => 'nullable|string',
                'banner_image'      => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'flyer_image'       => 'nullable|image|mimes:jpeg,png,jpg,gif',
                'volunteer_tags'    => 'nullable|array',
                'volunteer_tags.*'  => 'exists:tags,id',
                'tags'              => 'nullable|array',
                'tags.*'            => 'exists:tags,id',
                'meeting_spot_details' => 'nullable|string',
                'parking_info_details' => 'nullable|string',
                
                'bring_tools'       => 'nullable|array',
                'bring_tools.*'     => 'exists:tools,id',
                'provided_tools'    => 'nullable|array',
                'provided_tools.*'  => 'exists:tools,id',
                'is_send_reminder_email' => 'nullable|boolean',
                'reminder_custom_note' => 'nullable|string',
            ]);

            try {
                $event = \App\Models\Event::findOrFail(decrypt($validated['event_id']));
                
                // Handle file uploads
                if ($request->hasFile('banner_image')) {
                    $uploadedBanner = $this->uploadFiles('banner_image', 'events/banners');
                    $event->banner_image = json_encode($uploadedBanner);
                } else if(isset(request()->banner_image_data) && !empty(request()->banner_image_data)){
                    $event->banner_image = request()->banner_image_data;
                }
                if ($request->hasFile('flyer_image')) {
                    $uploadedFlyer = $this->uploadFiles('flyer_image', 'events/flyers');
                    $event->flyer_image = json_encode($uploadedFlyer);
                }
                if(request()->filled('cropped_image')) {
                    $base64 = preg_replace('/^data:image\/\w+;base64,/', '', request()->input('cropped_image'));
                    $imageData = base64_decode($base64);

                    // Mirror exactly what moveFiles returns
                    $originalName = 'event_banner.jpg';
                    $fileName = str_replace(' ', '', date('dmyhis') . $originalName);
                    $destinationPath = 'events/banners';

                    // Save directly — no temp file, no UploadedFile
                    file_put_contents(public_path($destinationPath) . '/' . $fileName, $imageData);

                    $event->banner_image = json_encode([
                        'path'          => "$destinationPath/" . $fileName,
                        'original_name' => $originalName,
                        'extension'     => 'jpg',
                    ]);
                }else{
                    unset($validated['cropped_image']);
                }

                // Update event fields
                $event->video_url = $validated['video_url'] ?? null;
                $event->event_reoccurance = $validated['event_reoccurance'] ?? null;
                $event->meeting_spot_details = $validated['meeting_spot_details'] ?? null;
                $event->parking_info_details = $validated['parking_info_details'] ?? null;$event->is_send_reminder_email = $request->has('is_send_reminder_email') ? 1 : 0;
                $event->reminder_custom_note = $validated['reminder_custom_note'] ?? null;

                // Save event
                $event->save();

                // Sync relationships
                $tools = [];
                foreach (request()->bring_tools ?? [] as $toolId) {
                    $tools[$toolId] = ['should_bring' => 1];
                }
                foreach (request()->provided_tools ?? [] as $toolId) {
                    $tools[$toolId] = ['should_bring' => 0];
                }
                // dd($tools);
                $event->tools()->sync($tools);

                $event->volunteertags()->sync($validated['volunteer_tags'] ?? []);
                
                $event->tags()->sync($validated['tags'] ?? []);
                
            } catch (\Exception $e) {

                dd($e);
                return $this->generateJsonResponse(false, 'Failed to save media & other information: ' . $e->getMessage());
            }

            return $this->generateJsonResponse(true, 'Media & other information saved successfully.', [
                'event_instance' => encrypt($event->id),
                'event' => $event
            ]);
    }

    public function saveCleanUpTicketDetails(){

        $request = request();
        $event   = \App\Models\Event::findOrFail(decrypt(request()->event_id));

        $start_datetime = $event->start_datetime;
        if(!empty($start_datetime) && !empty($request->ticket_sell_start_time) && (strtotime($start_datetime) < strtotime($request->ticket_sell_start_time)) ){
            return $this->generateJsonResponse(false, 'Ticket sell start date must be lower than event start date.');
        }
        if(!empty($start_datetime) && !empty($request->ticket_sell_end_time) && (strtotime(date('Y-m-d',strtotime($start_datetime))) < strtotime($request->ticket_sell_end_time)) ){
            return $this->generateJsonResponse(false, 'Ticket sell end date must be lower than or equal to event start date.');
        }

        $obj     = $event;
        $validated = $request->validate([
            'event_id'                    => 'required',
            'ticket_sell_start_time'   => 'nullable',
            'ticket_sell_end_time'   => 'nullable',
            //'ticket_sell_start_time'      => 'required|date',
            //'ticket_sell_end_time'        => 'required|date|after:ticket_sell_start_time',
            'ticket_questions'             => 'nullable|string|max:1000',
            'show_ticket_description'     => 'nullable|boolean',
            'show_remaining_tickets'      => 'nullable|boolean',
            'allow_donations'      => 'nullable|boolean',
            'sell_tickets_individually'   => 'nullable|boolean',

            'waiver_form_type' => 'nullable',
            'waiver_form_content' => ['nullable',
                function($attribute, $value, $fail) use ($request, $obj) {
                    
                    $type = $request->waiver_form_type;
                    if ($type === 'text') {
                        if (empty($value) || !is_string($value)) {
                            $fail('The waiver form content is required and must be text when waiver form type is text.');
                        }
                    }
                    if ($type === 'external_link') {
                        if (empty($value) || !filter_var($value, FILTER_VALIDATE_URL)) {
                            $fail('The waiver form content must be a valid URL when waiver form type is external_link.');
                        }
                    }
                    if ($type === 'pdf') {
                        // Only required on creation
                        $event = $obj;
                        if (empty($event->waiver_form_content) && !$request->hasFile('waiver_form_content')) {
                            $fail('The waiver form PDF is required when waiver form type is pdf.');
                        } elseif (!empty($event->waiver_form_content) && is_string($event->waiver_form_content)) {
                            // If it's a string, check if it's a valid URL ending with .pdf or a file path ending with .pdf
                            $isPdfUrl = filter_var($event->waiver_form_content, FILTER_VALIDATE_URL) && str_ends_with(strtolower($event->waiver_form_content), '.pdf');
                            $isPdfPath = str_ends_with(strtolower($event->waiver_form_content), '.pdf');
                            if (!$isPdfUrl && !$isPdfPath && !$request->hasFile('waiver_form_content')) {
                                $fail('The waiver form PDF must be a valid PDF file or a URL ending with .pdf.');
                            }
                        }
                        if ($request->hasFile('waiver_form_content')) {
                            $file = $request->file('waiver_form_content');
                            if ($file->getClientOriginalExtension() !== 'pdf') {
                                $fail('The waiver form PDF must be a file of type: pdf.');
                            }
                            if ($file->getSize() > 5 * 1024 * 1024) { // 5MB max
                                $fail('The waiver form PDF may not be greater than 5MB.');
                            }
                        }
                    }
                }
            ],
        ]);

        try {
            
            
            /**************************************************************************
            * Manage Waiver *
            ***************************************************************************/ 
            $waiver_form_pdf = $request->waiver_form_content;
            $event->waiver_form_type = request()->waiver_form_type ?? null;
            if(request()->waiver_form_type  == 'pdf'){
                if(request()->hasFile('waiver_form_content')){
                    $waiver_form_pdf = $this->uploadFiles('waiver_form_content', 'events/waiver_pdf');
                    $event->waiver_form_content = json_encode($waiver_form_pdf);
                } else if(isset(request()->waiver_form_content_helper)){
                    if(str_contains(request()->waiver_form_content_helper, '"path"')){
                        $event->waiver_form_content = request()->waiver_form_content_helper;
                    } else {
                        $event->waiver_form_content = null;
                    }
                } else if(isset($event->id) && !str_contains($event->waiver_form_content, '"path"')){
                    $event->waiver_form_content = null;
                }
            }elseif(request()->waiver_form_type == 'default'){ 
                $settings = Setting::first();
                $event->waiver_form_content = $settings->waiver_text;
            }elseif(!request()->waiver_form_type){
                $event->waiver_form_content =  null;
            }else{
                $event->waiver_form_content = request()->waiver_form_content;
            }

            /* if(isset($request->ticket) && is_array($request->ticket) && count($request->ticket)>0){} else {
                throw new \Exception("Please add tickets to event.");
            } */


            $event->ticket_sell_start_time = $validated['ticket_sell_start_time'] ?? null;
            $event->ticket_sell_end_time = $validated['ticket_sell_end_time'] ?? null;
            $event->ticket_questions = $validated['ticket_questions'] ?? null; 
            $event->show_ticket_description = $request->has('show_ticket_description') ? 1 : 0;
            $event->show_remaining_tickets = $request->has('show_remaining_tickets') ? 1 : 0;
            $event->allow_donations = $request->has('allow_donations') ? 1 : 0;
            $event->allow_refund = $request->has('allow_refund') ? 1 : 0;
            $event->charge_bear_by = $request->charge_bear_by ?? 0; 
            $event->sell_tickets_individually = $request->has('sell_tickets_individually') ? 1 : 0;
            /**************************************************************************
            * tickets system with constrait of changing number of ticket after orders *
            ***************************************************************************/ 
            /* $tickets = [];
            if ($event->exists) { 
                $existingTickets = $event->eventTickets()->get()->keyBy('id');
                $submittedTicketIds = collect($request->ticket)->pluck('ticket_id')->filter()->toArray();
                $event->eventTickets()->whereNotIn('id', $submittedTicketIds)->get()->each(function($ticket) {
                    $orderTicketCount = EventOrderTicket::where('ticket_id', $ticket->id)->count(); 
                    if((int)$orderTicketCount > 0){
                        throw new \Exception("Ticket ID {$ticket->id} has already been sold and cannot be deleted.");
                    } 
                    $ticket->delete();
                });
                if(isset($request->ticket) && is_array($request->ticket)){               
                    foreach ($request->ticket as $ticketData) {
                        if($ticketData['ticket_type']=='free' && floatval($ticketData['ticket_price'])>0){
                            throw new \Exception("Ticket price should be $0 for free type of ticket.");
                        }
                        if($ticketData['ticket_type']=='paid' && !floatval($ticketData['ticket_price'])>0){
                            throw new \Exception("Ticket price should be greater than $0 for paid type of ticket.");
                        }
                        if(empty($ticketData['sell_start_time'])){
                            throw new \Exception("Ticket sell start time is required.");
                        }
                        if(empty($ticketData['sell_end_time'])){
                            throw new \Exception("Ticket sell end time is required.");
                        }
                        if($ticketData['max_qty_allowed_per_order']==0 || empty($ticketData['max_qty_allowed_per_order'])){
                            throw new \Exception("Max tickets allowed per order should be greater than 0.");
                        }
                        $ticketData['is_active'] = isset($ticketData['is_active'])?$ticketData['is_active']:0;
                        if (isset($ticketData['ticket_id']) && $existingTickets->has($ticketData['ticket_id'])) {
                            // Constraint for sold tickets
                            $alreadySoldTickets = EventOrderTicket::active()->where('ticket_id', $ticketData['ticket_id'])->count();
                            if($alreadySoldTickets > $ticketData['available_quantity']){
                                throw new \Exception("Ticket ID {$ticketData['ticket_id']} has already been sold more than the available quantity.");
                            }
                            $ticket = $existingTickets[$ticketData['ticket_id']];
                            $ticketData['id'] = $ticketData['ticket_id']; // Ensure the ID is set for update
                            $ticket->fill($ticketData);
                            $ticket->save();
                        } else {
                            // Create new ticket
                            $ticket = new EventTicket($ticketData);
                            $event->eventTickets()->save($ticket);
                        }
                    }
                }
            } else {
                // Create new tickets for new event
                $tickets = [];
                if(isset($request->ticket) && is_array($request->ticket)){  
                    foreach ($request->ticket as $ticketData) {
                        if($ticketData['ticket_type']=='free' && floatval($ticketData['ticket_price'])>0){
                            throw new \Exception("Ticket price should be $0 for free type of ticket.");
                        }
                        if($ticketData['ticket_type']=='paid' && !floatval($ticketData['ticket_price'])>0){
                            throw new \Exception("Ticket price should be greater than $0 for paid type of ticket.");
                        }
                        if(empty($ticketData['sell_start_time'])){
                            throw new \Exception("Ticket sell start time is required.");
                        }
                        if(empty($ticketData['sell_end_time'])){
                            throw new \Exception("Ticket sell end time is required.");
                        }
                        if($ticketData['max_qty_allowed_per_order']==0 || empty($ticketData['max_qty_allowed_per_order'])){
                            throw new \Exception("Max tickets allowed per order should be greater than 0.");
                        }
                        $ticketData['is_active'] = isset($ticketData['is_active'])?$ticketData['is_active']:0;
                        $tickets[] = new EventTicket($ticketData); 
                    } 
                }
                //$obj->eventTickets()->saveMany($tickets);
                $event->eventTickets()->saveMany($tickets);
            } */
            $event->save();
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to save ticket & waiver setting: ' . $e->getMessage());
        }

        $ticket_container_section = '';
        /* $counter = 0; 
        $event->eventTickets->map(function($ticket) use(&$ticket_container_section, &$counter){ 
            $ticket_container_section .= view('components.reusables.frontend.ticket-form', [
                                            'frontend' => 1,
                                            'ticket' => $ticket,
                                            'counter' => $counter
                                        ])->render();
            $counter++;
        }); */

        return $this->generateJsonResponse(true, 'Tickets & waiver setting saved successfully.', [
            'event_instance' => encrypt($event->id),
            'event' => $event , 
            'section_html'   => [
                // 'ticket-container-section' => $ticket_container_section
            ] ,
        ]);
    }
    public function saveCleanUpTicketDetailsOld(){

        $request = request();
        $event   = \App\Models\Event::findOrFail(decrypt(request()->event_id));

        $start_datetime = $event->start_datetime;
        if(!empty($start_datetime) && !empty($request->ticket_sell_start_time) && (strtotime($start_datetime) < strtotime($request->ticket_sell_start_time)) ){
            return $this->generateJsonResponse(false, 'Ticket sell start date must be lower than event start date.');
        }
        if(!empty($start_datetime) && !empty($request->ticket_sell_end_time) && (strtotime(date('Y-m-d',strtotime($start_datetime))) < strtotime($request->ticket_sell_end_time)) ){
            return $this->generateJsonResponse(false, 'Ticket sell end date must be lower than or equal to event start date.');
        }

        $obj     = $event;
        $validated = $request->validate([
            'event_id'                    => 'required',
            'ticket_sell_start_time'   => 'nullable',
            'ticket_sell_end_time'   => 'nullable',
            //'ticket_sell_start_time'      => 'required|date',
            //'ticket_sell_end_time'        => 'required|date|after:ticket_sell_start_time',
            'ticket_questions'             => 'nullable|string|max:1000',
            'show_ticket_description'     => 'nullable|boolean',
            'show_remaining_tickets'      => 'nullable|boolean',
            'allow_donations'      => 'nullable|boolean',
            'sell_tickets_individually'   => 'nullable|boolean',

            'waiver_form_type' => 'nullable',
            'waiver_form_content' => ['nullable',
                function($attribute, $value, $fail) use ($request, $obj) {
                    
                    $type = $request->waiver_form_type;
                    if ($type === 'text') {
                        if (empty($value) || !is_string($value)) {
                            $fail('The waiver form content is required and must be text when waiver form type is text.');
                        }
                    }
                    if ($type === 'external_link') {
                        if (empty($value) || !filter_var($value, FILTER_VALIDATE_URL)) {
                            $fail('The waiver form content must be a valid URL when waiver form type is external_link.');
                        }
                    }
                    if ($type === 'pdf') {
                        // Only required on creation
                        $event = $obj;
                        if (empty($event->waiver_form_content) && !$request->hasFile('waiver_form_content')) {
                            $fail('The waiver form PDF is required when waiver form type is pdf.');
                        } elseif (!empty($event->waiver_form_content) && is_string($event->waiver_form_content)) {
                            // If it's a string, check if it's a valid URL ending with .pdf or a file path ending with .pdf
                            $isPdfUrl = filter_var($event->waiver_form_content, FILTER_VALIDATE_URL) && str_ends_with(strtolower($event->waiver_form_content), '.pdf');
                            $isPdfPath = str_ends_with(strtolower($event->waiver_form_content), '.pdf');
                            if (!$isPdfUrl && !$isPdfPath && !$request->hasFile('waiver_form_content')) {
                                $fail('The waiver form PDF must be a valid PDF file or a URL ending with .pdf.');
                            }
                        }
                        if ($request->hasFile('waiver_form_content')) {
                            $file = $request->file('waiver_form_content');
                            if ($file->getClientOriginalExtension() !== 'pdf') {
                                $fail('The waiver form PDF must be a file of type: pdf.');
                            }
                            if ($file->getSize() > 5 * 1024 * 1024) { // 5MB max
                                $fail('The waiver form PDF may not be greater than 5MB.');
                            }
                        }
                    }
                }
            ],
        ]);

        try {
            
            
            /**************************************************************************
            * Manage Waiver *
            ***************************************************************************/ 
            $waiver_form_pdf = $request->waiver_form_content;
            $event->waiver_form_type = request()->waiver_form_type ?? null;
            if(request()->waiver_form_type  == 'pdf'){
                if(request()->hasFile('waiver_form_content')){
                    $waiver_form_pdf = $this->uploadFiles('waiver_form_content', 'events/waiver_pdf');
                    $event->waiver_form_content = json_encode($waiver_form_pdf);
                } else if(isset(request()->waiver_form_content_helper)){
                    if(str_contains(request()->waiver_form_content_helper, '"path"')){
                        $event->waiver_form_content = request()->waiver_form_content_helper;
                    } else {
                        $event->waiver_form_content = null;
                    }
                } else if(isset($event->id) && !str_contains($event->waiver_form_content, '"path"')){
                    $event->waiver_form_content = null;
                }
            }elseif(request()->waiver_form_type == 'default'){ 
                $settings = Setting::first();
                $event->waiver_form_content = $settings->waiver_text;
            }elseif(!request()->waiver_form_type){
                $event->waiver_form_content =  null;
            }else{
                $event->waiver_form_content = request()->waiver_form_content;
            }

            if(isset($request->ticket) && is_array($request->ticket) && count($request->ticket)>0){} else {
                throw new \Exception("Please add tickets to event.");
            }


            $event->ticket_sell_start_time = $validated['ticket_sell_start_time'] ?? null;
            $event->ticket_sell_end_time = $validated['ticket_sell_end_time'] ?? null;
            $event->ticket_questions = $validated['ticket_questions'] ?? null; 
            $event->show_ticket_description = $request->has('show_ticket_description') ? 1 : 0;
            $event->show_remaining_tickets = $request->has('show_remaining_tickets') ? 1 : 0;
            $event->allow_donations = $request->has('allow_donations') ? 1 : 0;
            $event->allow_refund = $request->has('allow_refund') ? 1 : 0;
            $event->charge_bear_by = $request->charge_bear_by ?? 0; 
            $event->sell_tickets_individually = $request->has('sell_tickets_individually') ? 1 : 0;
            /**************************************************************************
            * tickets system with constrait of changing number of ticket after orders *
            ***************************************************************************/ 
            $tickets = [];
            if ($event->exists) { 
                $existingTickets = $event->eventTickets()->get()->keyBy('id');
                $submittedTicketIds = collect($request->ticket)->pluck('ticket_id')->filter()->toArray();
                $event->eventTickets()->whereNotIn('id', $submittedTicketIds)->get()->each(function($ticket) {
                    $orderTicketCount = EventOrderTicket::where('ticket_id', $ticket->id)->count(); 
                    if((int)$orderTicketCount > 0){
                        throw new \Exception("Ticket ID {$ticket->id} has already been sold and cannot be deleted.");
                    } 
                    $ticket->delete();
                });
                if(isset($request->ticket) && is_array($request->ticket)){               
                    foreach ($request->ticket as $ticketData) {
                        if($ticketData['ticket_type']=='free' && floatval($ticketData['ticket_price'])>0){
                            throw new \Exception("Ticket price should be $0 for free type of ticket.");
                        }
                        if($ticketData['ticket_type']=='paid' && !floatval($ticketData['ticket_price'])>0){
                            throw new \Exception("Ticket price should be greater than $0 for paid type of ticket.");
                        }
                        if(empty($ticketData['sell_start_time'])){
                            throw new \Exception("Ticket sell start time is required.");
                        }
                        if(empty($ticketData['sell_end_time'])){
                            throw new \Exception("Ticket sell end time is required.");
                        }
                        if($ticketData['max_qty_allowed_per_order']==0 || empty($ticketData['max_qty_allowed_per_order'])){
                            throw new \Exception("Max tickets allowed per order should be greater than 0.");
                        }
                        $ticketData['is_active'] = isset($ticketData['is_active'])?$ticketData['is_active']:0;
                        if (isset($ticketData['ticket_id']) && $existingTickets->has($ticketData['ticket_id'])) {
                            // Constraint for sold tickets
                            $alreadySoldTickets = EventOrderTicket::active()->where('ticket_id', $ticketData['ticket_id'])->count();
                            if($alreadySoldTickets > $ticketData['available_quantity']){
                                throw new \Exception("Ticket ID {$ticketData['ticket_id']} has already been sold more than the available quantity.");
                            }
                            $ticket = $existingTickets[$ticketData['ticket_id']];
                            $ticketData['id'] = $ticketData['ticket_id']; // Ensure the ID is set for update
                            $ticket->fill($ticketData);
                            $ticket->save();
                        } else {
                            // Create new ticket
                            $ticket = new EventTicket($ticketData);
                            $event->eventTickets()->save($ticket);
                        }
                    }
                }
            } else {
                // Create new tickets for new event
                $tickets = [];
                if(isset($request->ticket) && is_array($request->ticket)){  
                    foreach ($request->ticket as $ticketData) {
                        if($ticketData['ticket_type']=='free' && floatval($ticketData['ticket_price'])>0){
                            throw new \Exception("Ticket price should be $0 for free type of ticket.");
                        }
                        if($ticketData['ticket_type']=='paid' && !floatval($ticketData['ticket_price'])>0){
                            throw new \Exception("Ticket price should be greater than $0 for paid type of ticket.");
                        }
                        if(empty($ticketData['sell_start_time'])){
                            throw new \Exception("Ticket sell start time is required.");
                        }
                        if(empty($ticketData['sell_end_time'])){
                            throw new \Exception("Ticket sell end time is required.");
                        }
                        if($ticketData['max_qty_allowed_per_order']==0 || empty($ticketData['max_qty_allowed_per_order'])){
                            throw new \Exception("Max tickets allowed per order should be greater than 0.");
                        }
                        $ticketData['is_active'] = isset($ticketData['is_active'])?$ticketData['is_active']:0;
                        $tickets[] = new EventTicket($ticketData); 
                    } 
                }
                //$obj->eventTickets()->saveMany($tickets);
                $event->eventTickets()->saveMany($tickets);
            }
            $event->save();
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to save ticket & waiver setting: ' . $e->getMessage());
        }

        $ticket_container_section = '';
        $counter = 0; 
        $event->eventTickets->map(function($ticket) use(&$ticket_container_section, &$counter){ 
            $ticket_container_section .= view('components.reusables.frontend.ticket-form', [
                                            'frontend' => 1,
                                            'ticket' => $ticket,
                                            'counter' => $counter
                                        ])->render();
            $counter++;
        });

        return $this->generateJsonResponse(true, 'Tickets & waiver setting saved successfully.', [
            'event_instance' => encrypt($event->id),
            'event' => $event , 
            'section_html'   => [
                // 'ticket-container-section' => $ticket_container_section
            ] ,
        ]);
    }

    public function saveTicketType(){
        $request = request();
        $validated = $request->validate([
            'event_id'                  => 'required',
            'ticket_id'                 => 'nullable',
            'ticket_name'               => 'required',
            'ticket_type'               => 'required',
            'ticket_price'              => 'required',
            'available_quantity'        => 'required',
            'max_qty_allowed_per_order' => 'required',
            'sell_start_time'           => 'required',
            'sell_end_time'             => 'required',
            'pass_code'                 => 'nullable',
            'ticket_description'        => 'nullable',
        ],[
            'event_id.required'         => 'Event is required.',
            'ticket_name.required'      => 'Ticket name is required.',
            'ticket_type.required'      => 'Ticket type is required.',
            'ticket_price.required'     => 'Ticket price is required.',
            'available_quantity.required' => 'Available quantity is required.',
            'max_qty_allowed_per_order.required' => 'Max qty allowed per order is required.',
            'sell_start_time.required'  => 'Sell start time is required.',
            'sell_end_time.required'    => 'Sell end time is required.',
        ]);
        try {
            $event = \App\Models\Event::findOrFail(decrypt(request()->event_id));
            if($validated['ticket_type']=='free' && floatval($validated['ticket_price'])>0){
                throw new \Exception("Ticket price should be $0 for free type of ticket.");
            }
            if($validated['ticket_type']=='paid' && !floatval($validated['ticket_price'])>0){
                throw new \Exception("Ticket price should be greater than $0 for paid type of ticket.");
            }
            if(empty($validated['sell_start_time'])){
                throw new \Exception("Ticket sell start time is required.");
            }
            if(empty($validated['sell_end_time'])){
                throw new \Exception("Ticket sell end time is required.");
            }
            if($validated['max_qty_allowed_per_order']==0 || empty($validated['max_qty_allowed_per_order'])){
                throw new \Exception("Max tickets allowed per order should be greater than 0.");
            }
            if(strtotime($validated['sell_start_time']) >= strtotime($validated['sell_end_time'])){
                throw new \Exception("Ticket sell end date must be greater than the ticket sell start date.");
            }
            $ticketData = [
                'ticket_name' => $validated['ticket_name'],
                'ticket_type' => $validated['ticket_type'],
                'ticket_price' => (isset($validated['ticket_price']) && !empty($validated['ticket_price']))?$validated['ticket_price']:0,
                'available_quantity' => (isset($validated['available_quantity']) && !empty($validated['available_quantity']))?$validated['available_quantity']:0,
                'max_qty_allowed_per_order' => (isset($validated['max_qty_allowed_per_order']) && !empty($validated['max_qty_allowed_per_order']))?$validated['max_qty_allowed_per_order']:0,
                'sell_start_time' => (isset($validated['sell_start_time']) && !empty($validated['sell_start_time']))?$validated['sell_start_time']:null,
                'sell_end_time' => (isset($validated['sell_end_time']) && !empty($validated['sell_end_time']))?$validated['sell_end_time']:null,
                'pass_code' => (isset($validated['pass_code']) && !empty($validated['pass_code']))?$validated['pass_code']:null,
                'ticket_description' => (isset($validated['ticket_description']) && !empty($validated['ticket_description']))?$validated['ticket_description']:null,
            ];
            if(isset($validated['ticket_id']) && !empty($validated['ticket_id'])){
                $ticket = EventTicket::findOrFail(decrypt($validated['ticket_id']));
                $alreadySoldTickets = EventOrderTicket::active()->where('ticket_id', $ticket->id)->count();
                if($alreadySoldTickets > $validated['available_quantity']){
                    throw new \Exception("This ticket type's tickets has already been sold more than the given available quantity.");
                }
                $ticket->update($ticketData);
            } else {
                $ticket = new EventTicket($ticketData);
                $event->eventTickets()->save($ticket);
            }
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to save ticket type: ' . $e->getMessage());
        }
        return $this->generateJsonResponse(true, 'Ticket type has been saved successfully.');
    }

    public function saveCleanUpSeoDetails(){
        $request = request();

        $validated = $request->validate([
            'event_id'           => 'required',
            'meta_title'         => 'required|string|max:255',
            'meta_focus_keyword' => 'required|string|max:255',
            'meta_description'   => 'required|string|max:1000',
        ]);

        try {
            $event = \App\Models\Event::findOrFail(decrypt($validated['event_id']));
            $event->meta_title = $validated['meta_title'];
            $event->meta_focus_keyword = $validated['meta_focus_keyword'];
            $event->meta_description = $validated['meta_description'];
            $event->save();
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to save SEO details: ' . $e->getMessage());
        }
        /* if(request()->is_create){
            return $this->generateJsonResponse(true, 'SEO details saved successfully.', [
                'event_instance' => encrypt($event->id),
                'event' => $event , 
                'redirect_href' => route('front-cleanup-hosting', ['type' => 'organizer']) , 
            ]);
        } else { */
            // SEO details saved successfully.
            // Your event has been published successfully.
            $addedMsg = '';
            if($event->status=='draft'){
                $addedMsg = ' Publish event from the hosting section to make it live.';
            }
            return $this->generateJsonResponse(true, 'Event details saved successfully.'.$addedMsg, [
                'event_instance' => encrypt($event->id),
                'event' => $event , 
                //'redirect_href' => route('front-cleanup-hosting', ['type' => 'organizer']) , 
            ]);
        //}

    }

    public function saveCleanUpAfterAction(){
        $request = request();
        $validated = $request->validate([
            'event_id'           => 'required',
            'waste_collected'    => 'required|max:255',
            'area_cleaned'       => 'required|max:255',
            //'team_name'          => 'required|string',
            'volunteer_hours'    => 'required|numeric|min:0',
            'post_event_notes'   => 'required|string',
        ]);

        try {
            $event = \App\Models\Event::findOrFail(decrypt($validated['event_id']));
            $event->waste_collected = $validated['waste_collected'];
            $event->area_cleaned = $validated['area_cleaned'];
            //$event->team_name = $validated['team_name'];
            $event->volunteer_hours = ceil($validated['volunteer_hours']);
            $event->post_event_notes = $validated['post_event_notes'];
            if (request()->hasFile('after_event_images')) {
                $files = request()->file('after_event_images');
                // Wrap in array if single file, so loop works for both cases
                if (!is_array($files)) {
                    $files = [$files];
                }
                $existingImages = [];
                if (!empty($event->after_event_images)) {
                    $existingImages = json_decode($event->after_event_images, true) ?? [];
                }
                /* if (count($files) > 4) {
                    return $this->generateJsonResponse(false,'You can only upload a maximum of 4 images');
                } */
                if (count($existingImages) + count($files) > 4) {
                    return $this->generateJsonResponse(false, 'You can only upload a maximum of 4 images');
                }
                $newPhotoPaths = [];
                foreach ($files as $photo) {
                    $newPhotoPaths[] = $this->moveFiles($photo, 'events/after_event_images');
                }
                $allPhotos = array_merge($existingImages, $newPhotoPaths);
                $event->after_event_images = json_encode($allPhotos);
            }
            $event->save();
            if(isset(request()->send_cleanup_report_email) && request()->send_cleanup_report_email==1){
                return $this->sendWrapupEmail($validated['event_id'],true);
            }
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to after event actions: ' . $e->getMessage());
        }

        return $this->generateJsonResponse(true, 'Cleanup report data saved successfully.', [
            'event_instance' => encrypt($event->id),
            'event' => $event , 
            'redirect_href' => route('front-cleanup-hosting', ['type' => 'organizer']) , 
        ]);
    }
    public function removeEventAfterImage($index, $id){
        $row = Event::findOrFail(decrypt($id));
        $photos = json_decode($row->after_event_images, true) ?? [];
        if (!isset($photos[$index])) {
            return $this->generateJsonResponse(false, 'Image not found.');
        }
        $photo = $photos[$index];
        $photoPath = isset($photo['path']) ? $photo['path'] : $photo;
        if(!empty($photoPath)){
            $filePath = public_path($photoPath);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // Remove from array and re-index
        array_splice($photos, $index, 1);
        $new_photos = array_values($photos);
        $row->after_event_images = !empty($new_photos)?json_encode($new_photos):null;
        $row->save();
        $html = '';
        if(!empty($new_photos)){
            $html = '';
            foreach ($new_photos as $key => $image) {
                $imagePath = isset($image['path']) ? $image['path'] : $image;
                $html .=' 
                <div class="position-relative me-3 mt-2" id="saved-img-'.$key.'">
                    <img style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover;" src="'.asset($imagePath).'">
                    <button divid="saved-img-'.$key.'" url="'.route('front-remove-event-after-image', ['index' => $key, 'id' => $row->encrypted_id]).'"
                        type="button" 
                        class="remove-event-img-btn remove-new-img">✕
                    </button>
                </div>';
            }
        }
        return $this->generateJsonResponse(true, 'Image removed.', $html);

    }

    public function addCoHost(){
        $request = request();
        $validated = $request->validate([
            'event_id'   => 'required',
            'email'      => 'required',
            'name'       => 'required',
            'extra_notes'=> 'nullable',
            'permission' => 'required',
        ]);

        try {
            $event = Event::findOrFail(decrypt($validated['event_id']));
            $existingCohost = Volunteer::where('email_address', strtolower(trim($validated['email'])))->first();
            $cohostName = $validated['name'];
            if (isset($existingCohost->id)) {

                // check if already cohost
                $alreadyCohost = EventCoHost::where('event_id', $event->id)
                                    ->where('volunteer_id', $existingCohost->id)
                                    ->first();
                if (isset($alreadyCohost->id)) {
                    return $this->generateJsonResponse(false, 'This volunteer is already a co-host for this event.');
                }

                $existingCohost->is_organiser = 1;
                $existingCohost->save();
                $cohost_id = $existingCohost->id;
                $cohostName = $existingCohost->first_name.' '.$existingCohost->last_name;
            } else {
                $newVolunteer = new Volunteer();
                $newVolunteer->first_name = $validated['name'];
                $newVolunteer->email_address = strtolower(trim($validated['email']));
                $newVolunteer->is_organiser = 1;
                $newVolunteer->save();
                $cohost_id = $newVolunteer->id;
            }
            $cohost = new EventCoHost();
            $cohost->event_id = $event->id;
            $cohost->volunteer_id = $cohost_id;
            $cohost->invited_at = now();
            $cohost->request_status = 'pending';
            $cohost->extra_notes = $validated['extra_notes'];
            $cohost->save();
            $event_cohost_id = $cohost->id;

            foreach ($validated['permission'] as $perm) {
                $permission = new CohostPermission();
                $permission->cohost_record_id = $event_cohost_id;
                $permission->permission = $perm;
                $permission->save();
            }
            // send email to cohost
            $email_content = [
                'is_new_user' => !isset($existingCohost->id),
                'organizerName' => $event->creator->first_name.' '.$event->creator->last_name,
                'eventName' => $event->event_name,
                'cohostName' => $cohostName,
                /* 'acceptLink' => route('response.cohost.invitaion',['response'=>'accept','event'=>encrypt($event->id),'volunteer'=>encrypt($cohost_id)]),
                'cancelLink' => route('response.cohost.invitaion',['response'=>'cancel','event'=>encrypt($event->id),'volunteer'=>encrypt($cohost_id)]), */
                'acceptLink' => $this->mailUrl('response.cohost.invitaion',['response'=>'accept','event'=>encrypt($event->id),'volunteer'=>encrypt($cohost_id)]),
                'cancelLink' => $this->mailUrl('response.cohost.invitaion',['response'=>'cancel','event'=>encrypt($event->id),'volunteer'=>encrypt($cohost_id)]),
            ];
            if(!isset($existingCohost->id)){
                $sub = ' Create your VolunteerCleanup.org account to co-host '.$email_content['eventName'];
            } else {
                $sub = 'You’ve been invited to co-host '.$email_content['eventName'];
            }
            $this->sendEmail($validated['email'], $sub, $email_content, "invite-cohost");

            return $this->generateJsonResponse(true, 'Co-Host added successfully.');

        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to save Co-Host details: ' . $e->getMessage());
        }
    }
    public function updateCoHost($id){
        $request = request();
        $validated = $request->validate([
            'permission' => 'required',
            'extra_notes' => 'nullable',
        ]);

        try {
            $cohost = EventCoHost::where('id', decrypt($id))->firstOrFail();
            CohostPermission::where('cohost_record_id', $cohost->id)->delete();
            foreach ($validated['permission'] as $perm) {
                $permission = new CohostPermission();
                $permission->cohost_record_id = $cohost->id;
                $permission->permission = $perm;
                $permission->save();
            }
            $cohost->extra_notes = $validated['extra_notes'];
            $cohost->save();

            // send email to cohost about permission update
            $event = $cohost->event;
            $volunteer = $cohost->volunteer;
            $email_content = [
                'is_deleted'=>false,
                'organizer_name' => $event->creator->first_name.' '.$event->creator->last_name,
                'event_name' => $event->event_name,
                'cohost_first_name' => $volunteer->first_name,
                //'dashboardLink'=>route('front-dashboard'),
                'dashboardLink'=>$this->mailUrl('front-dashboard'),
                'permissions'=>$cohost->permissions
            ];
            $sub = 'Your co-host permissions were updated for '.$email_content['event_name'];
            $this->sendEmail($volunteer->email_address, $sub, $email_content, "cohost-update");

            return $this->generateJsonResponse(true, 'Co-Host permissions updated successfully.');
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to update Co-Host permissions: ' . $e->getMessage());
        }
    }
    public function removeCoHost($id){
        $cohost = EventCoHost::where('id', decrypt($id))->firstOrFail();

        CohostPermission::where('cohost_record_id', $cohost->id)->delete();

        $event = $cohost->event;
        $volunteer = $cohost->volunteer;
        $email_content = [
            'is_deleted'=>true,
            'organizer_name' => $event->creator->first_name.' '.$event->creator->last_name,
            'organizer_email' => $event->creator->email_address,
            'event_name' => $event->event_name,
            'cohost_first_name' => $volunteer->first_name,
        ];

        $res = $cohost->delete();

        $sub = 'You’ve been removed as a co-host from '.$email_content['event_name'];
        $this->sendEmail($volunteer->email_address, $sub, $email_content, "cohost-update");
        return $this->generateJsonResponse(true,'Co-Host removed successfully.');
    }
    public function reinviteCoHost($id){
        try {
            $cohost = EventCoHost::where('id', decrypt($id))->firstOrFail();
            $event = $cohost->event;
            $volunteer = $cohost->volunteer;

            $email_content = [
                'is_new_user' => false,
                'organizerName' => $event->creator->first_name.' '.$event->creator->last_name,
                'eventName' => $event->event_name,
                'cohostName' => $volunteer->first_name.' '.$volunteer->last_name,
                /* 'acceptLink' => route('response.cohost.invitaion',['response'=>'accept','event'=>encrypt($event->id),'volunteer'=>encrypt($volunteer->id)]),
                'cancelLink' => route('response.cohost.invitaion',['response'=>'cancel','event'=>encrypt($event->id),'volunteer'=>encrypt($volunteer->id)]), */
                'acceptLink' => $this->mailUrl('response.cohost.invitaion',['response'=>'accept','event'=>encrypt($event->id),'volunteer'=>encrypt($volunteer->id)]),
                'cancelLink' => $this->mailUrl('response.cohost.invitaion',['response'=>'cancel','event'=>encrypt($event->id),'volunteer'=>encrypt($volunteer->id)]),
            ];
            $this->sendEmail($volunteer->email_address, 'Reminder: You’ve been invited to co-host '.$email_content['eventName'], $email_content, "invite-cohost");
            return $this->generateJsonResponse(true, 'Co-Host invitation re-sent successfully.');
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to re-send Co-Host invitation: ' . $e->getMessage());
        }
    }
    public function checkEventForWrapup($id,$submitFromAdmin=false){
        $event = Event::with('creator')->findOrFail(decrypt($id));
        if($event->is_completed==1){
            return $this->generateJsonResponse(false, 'Event is already marked as completed so no wrap-up email can be sent.');
        }
        $availTickets = $event->tickets()->where('rsvp_confirmation', 1)->get();
        $notCheckedIntickets = $event->tickets()->where('checkin', 0)->where('rsvp_confirmation', 1)->get();
        if($availTickets->count() > 0){
            if($notCheckedIntickets->count() > 0){
                if($submitFromAdmin){
                    $link = 'check-ins';
                } else {
                    $link = '<a target="_blank" href="'.route('front-registration-list',['event' => $event->id]).'">check-ins</a>';
                }
                $message = '<div class="text-left fw-bold mb-4 text-center">You have <span class="text-danger">'.$notCheckedIntickets->count().' attendees</span> who are not checked-in. Only checked-in attendees will receive certificates.</div>
                <div class="alert-innter-ul p-4">
                    <div class="text-left mb-2">
                    You can review and update '.$link.' before submitting. Once you submit:</div>
                    <ul class="text-left ps-4">
                        <li>Certificates will be generated.</li>
                        <li>Emails will be sent immediately.</li>
                    </ul>
                </div>';
            } else {
                $checkedIntickets = $event->tickets()->where('checkin', 1)->where('rsvp_confirmation', 1)->get();
                $message = '<div class="text-left fw-bold mb-4 text-center">You have <span class="text-success">'.$checkedIntickets->count().' checked-in attendees</span> and they will receive certificates.</div>
                <div class="alert-innter-ul p-4">
                    <div class="text-left mb-2">
                    You can proceed to submit the wrap-up. Once submitted:</div>
                    <ul class="text-left ps-4">
                        <li>Certificates will be generated.</li>
                        <li>Emails will be sent immediately.</li>
                    </ul>
                </div>';
            }
        } else {
            $message = '<div class="text-left fw-bold mb-4 text-center">There are no registered or checked-in attendees for this event.</div>
            <div class="alert-innter-ul p-4">
                <div class="text-left mb-2">
                You can still proceed to submit the wrap-up. Once submitted:
                </div>
                <ul class="text-left ps-4">
                    <li>Your cleanup impact will be recorded and contribute to overall community impact.</li>
                    <li>No certificates will be generated.</li>
                    <li>No emails will be sent.</li>
                </ul>
            </div>';
        }
        return $this->generateJsonResponse(true, 'Event is ready for wrap-up email.', $message);
    }
    public function sendWrapupEmail($id,$submitAsForm=false){
        $event = Event::with('creator')->findOrFail(decrypt($id));
        if($event->is_active!=1){
            return $this->generateJsonResponse(false, 'Event is not active, contact admin for more details.');
        }
        if($event->is_completed==1){
            return $this->generateJsonResponse(false, 'Event is already marked as completed and wrap-up email has been sent.');
        }
        if(empty($event->waste_collected) || empty($event->area_cleaned) || empty($event->volunteer_hours)){
            return $this->generateJsonResponse(false, 'Waste Collected, Area Cleaned, and Volunteer Hours should be saved before sending the email.');
        }
        $event->is_completed = 1;
        $event->wrapup_email_sent_by = auth()->guard('front')->user()->id;
        $event->save();
        $tickets = $event->tickets()->where('checkin', 1)->whereNotNull('waiver_filled_at')->get();
        $cohostText = '';
        $activeCohostsIds = $event->cohostRecords->where('request_status', 'approved')->pluck('volunteer_id');
        if ($activeCohostsIds->isNotEmpty() && $activeCohostsIds->contains($event->wrapup_email_sender->id)) {
            $cohostText = ' (Co-Host)';
        } else if($event->wrapup_email_sender->id==$event->creator_id){
            $cohostText = ' (Organizer)';
        }
        if($tickets->count() == 0){
            if($submitAsForm){
                $this->setFlashSession(true, 'Event marked as completed. Only volunteers who have checked-in and signed the waiver will receive the email. Since no attendees have been found yet, the email has not been sent to anyone.');
                return $this->generateJsonResponse(true, 'Event marked as completed. Only volunteers who have checked-in and signed the waiver will receive the email. Since no attendees have been found yet, the email has not been sent to anyone.', [
                    'event_instance' => encrypt($event->id),
                    'event' => $event , 
                    'redirect_href' => route('front-cleanup-hosting', ['type' => 'organizer']) , 
                ]);
            } else {
                return $this->generateJsonResponse(true, 'Event marked as completed. Only volunteers who have checked-in and signed the waiver will receive the email. Since no attendees have been found yet, the email has not been sent to anyone.', '<p class="float-end text-warning">Wrap-Up Email sent by: '.auth()->guard('front')->user()->first_name.' '.auth()->guard('front')->user()->last_name.$cohostText.'</p>');
            }
        }
        if($tickets->isNotEmpty()){
            $to_arr = [];
            $log_arr = [];
            $to_arr2 = [];
            foreach ($tickets as $key => $value) {
                if(!isset($value->volunteer)){
                    continue;
                } else if(!isset($value->volunteer->id)){
                    continue;
                }
                $email = $value->volunteer->email_address;
                $name = $value->volunteer->first_name.' '.$value->volunteer->last_name;
                if(!empty(trim($email))){
                    $to_arr[] = 
                    [
                        "to" => [
                            ["email" => trim($email)]
                            //["email" => 'learningdcm@gmail.com']
                        ],
                        "dynamic_template_data" => [
                            "volunteer_name" => $name,
                            "volunteer_email" => $email,
                            "event_name" =>  $event->event_name,
                            "event_date" => $event->startTimeAndEndTime,
                            "event_location" => $event->fullAddress,
                            "organizer_name" => $event->creator->first_name.' '.$event->creator->last_name,
                            "total_volunteers" => $tickets->count(),
                            "waste_collected" => $event->waste_collected,
                            "area_cleaned" => $event->area_cleaned,
                            "service_hours" => $event->volunteer_hours ?? 'N/A',
                            'highlight_link' => $this->mailUrl('cleanup-highlights', ['event' => encrypt($event->id)]),
                            'certificate_download_link' => $this->mailUrl('show-community-service-pdf', ['ticket' => encrypt($value->id)]),
                        ],
                    ];
                    $log_arr[] = [
                        "id" => $value->id,
                        "email" => trim($email)
                    ];
                    /* if(isset($value->community_form_pdf) && !empty($value->community_form_pdf)){
                        $to_arr2[] = 
                        [
                            "to" => [
                                ["email" => trim($email)]
                                //["email" => 'learningdcm@gmail.com']
                            ],
                            "dynamic_template_data" => [
                                "volunteer_name" => $name,
                                "volunteer_email" => $email,
                                "event_name" =>  $event->event_name,
                                'certificate_download_link' => $this->mailUrl('show-community-service-pdf', ['ticket' => encrypt($value->id)]),
                                'dashboard_link' => $this->mailUrl('front-cleanup-attending')
                            ],
                        ];
                        $event->tickets()->where('id', $value->id)->update([
                            'community_form_status' => 'approved'
                        ]);
                    } */
                }
            }
            if(!empty($to_arr) && count($to_arr)>0){
                $to_arr_child = array_chunk($to_arr, 300);
                $log_arr_child = array_chunk($log_arr, 300);
                foreach ($to_arr_child as $key => $value) {
                    $this->sendSendgridTemplateEmailWithoutLog($value,"d-84b101582dbb408aa6f90cf53b2be2d0");
                }
            }
            /* if(!empty($to_arr2) && count($to_arr2)>0){
                $to_arr2_child = array_chunk($to_arr2, 300);
                foreach ($to_arr2_child as $key => $value) {
                    $this->sendSendgridTemplateEmailWithoutLog($value,"d-3badeebd9cfe40458f55935dd25aa41a");
                }
            } */
        }
        if($submitAsForm){
            $this->setFlashSession(true, 'Event marked as completed. Wrap-Up email and certificate email has been sent successfully.');
            return $this->generateJsonResponse(true, 'Event marked as completed. Wrap-Up email and certificate email has been sent successfully.', [
                'event_instance' => encrypt($event->id),
                'event' => $event , 
                'redirect_href' => route('front-cleanup-hosting', ['type' => 'organizer']), 
            ]);
        } else {
            return $this->generateJsonResponse(true, 'Event marked as completed. Wrap-Up email and certificate email has been sent successfully.', '<p class="float-end text-warning">Wrap-Up Email sent by: '.auth()->guard('front')->user()->first_name.' '.auth()->guard('front')->user()->last_name.$cohostText.'</p>');
        }
    }
    public function sendThankyouRequestEmailFront(){
        $request = request();
        $validated = $request->validate([
            'event_id'           => 'required'
        ]);

        $event = Event::with('creator')->findOrFail(decrypt($validated['event_id']));
        if(empty($event->waste_collected) || empty($event->area_cleaned) || empty($event->volunteer_hours)){
            return $this->generateJsonResponse(false, 'Waste Collected, Area Cleaned, and Volunteer Hours should be saved before sending the email.');
        }
        $tickets = $event->tickets()->where('checkin', 1)->where('community_form_status', 'pending')->get();
        if($tickets->count() == 0){
            return $this->generateJsonResponse(false, 'Only volunteers who have checked in can get the email, but since no one has checked in yet, the email hasn’t been sent to anyone.');
        }
        foreach ($tickets as $ticket) {
                $email_content = [
                    'eventName' => $event->event_name,
                    'volunteerName' => $ticket->volunteer->name,
                    'date' => $event->start_datetime->format(config('app.dateTimeFormat')),
                    'volunteersCount' => $event->tickets()->where('checkin', 1)->count(),
                    'wasteCollected' => $event->waste_collected ?? 'N/A',
                    'areaCleaned' => $event->area_cleaned ?? 'N/A',
                    'communityServiceFormLink' => $event->allow_community_service_form ? $this->mailUrl('community-form', ['placeholder' => $event->encrypted_id]) : null,
                    //'feedbackFormLink' => route('event-feedback', ['placeholder' => $event->encrypted_id]),
                    'feedbackFormLink' => $this->mailUrl('event-feedback', ['placeholder' => $event->encrypted_id]),
                    //'teamName' => $event->team_name,
                    'volunteerHours' => $event->volunteer_hours ?? 'N/A',
                    'instaLink' => $event->creator->organisation_instagram,
                    'facebookLink' => $event->creator->organisation_facebook,
                    'linkedinLink' => $event->creator->organisation_linkedin,
                    'twitterLink' => $event->creator->organisation_twitter,
                    'contactEmail' => config('mail.from.address'),
                ];
                $this->sendEmail($ticket->volunteer->email_address, EVENT_SUMMARY_MAIL_SUB, $email_content, "thankyou-message-after-event");
        }
        return $this->generateJsonResponse(true, 'Thank-You email has been sent successfully.');
    }

    public function updateEventPublishStatus(){
        $request = request();
        $validated = $request->validate([
            'event_id'           => 'required',
            'status'             => 'required|in:published,draft',
        ]);
        try {
            $event = Event::findOrFail(decrypt($validated['event_id']));
            if($event->is_active!=1){
                return $this->generateJsonResponse(false, 'You can not change the publish status of a non-active event.');
            }
            if($validated['status']=='draft'){
                $tickets = $event->tickets()->where('rsvp_confirmation', 1)->get();
                if($tickets->count() > 0){
                    return $this->generateJsonResponse(false, 'An event cannot be unpublished once an attendee has registered.',$this->mailUrl('view-event-details', ['slug' => $event->slug]));
                }
            }
            $event->status = $validated['status'];
            $event->save();
            if($validated['status']=='published'){
                $email_content = [
                    'eventName' => $event->event_name,
                    'date' => $event->start_datetime->format(config('app.dateTimeFormat')),
                    'end_date' => $event->end_datetime->format(config('app.dateTimeFormat')),
                    'location' => $event->fullAddress,
                    'description' => $event->description,
                    'is_meeting_private' => $event->is_private==1?'Yes':'No',
                    'eventUrl' => $this->mailUrl('view-event-details',['slug'=>$event->slug]),
                    'orgName' => $event->creator->first_name.' '.$event->creator->last_name,
                    'orgEmail' => $event->creator->email_address,
                    'orgPhone' => $event->creator->phone,
                    'eventEditUrl' => $this->mailUrl('show-event',['placeholder'=>encrypt($event->id)]),
                ];
                $this->sendEmail(SUPPORT_EMAIL, EVENT_PUBLISH_MAIL_SUB.$event->event_name, $email_content, "email-to-admin-for-publish");
            }
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to update event publish status.',$this->mailUrl('view-event-details', ['slug' => $event->slug]));
            //return $this->generateJsonResponse(false, 'Failed to update event publish status: ' . $e->getMessage());
        }
        return $this->generateJsonResponse(true, 'Event publish status updated successfully.',$this->mailUrl('view-event-details', ['slug' => $event->slug]));
    }
    public function updateTicketStatus($id){
        try {
            $eventTicket = EventTicket::findOrFail(decrypt($id));
            $eventTicket->is_active = $eventTicket->is_active==1?0:1;
            $eventTicket->save();
        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to update event ticket status.');
        }
        return $this->generateJsonResponse(true, 'Event ticket status updated successfully.');
    }

    public function contactLists(){
        $leads = OrganiserLead::where('organiser_id',auth()->guard('front')->user()->id)->latest()->get();
        return view('pages.frontend.contact-lists', [
                                                        'leads' => $leads , 
                                                        'seo_details' => (object)['title' => 'Contact List'] 
                                                    ]);
    }
    public function orders(){
        $orderEvents = EventOrder::where('creator_id',auth()->guard('front')->user()->id)->latest()->get();
        $events = [];
        $event_ids = [];
        if(isset($orderEvents[0]) && isset($orderEvents[0]->id)){
            foreach ($orderEvents as $key => $value) {
                if(in_array($value->event->id, $event_ids)){
                    continue;
                }
                $event_ids[] = $value->event->id;
                $events[] = [
                    'name' => $value->event->event_name,
                    'id' => $value->event->id
                ];
            }
        }

        $orders = EventOrder::where(function($q){
            if(request()->event_id){
                $q->where("event_id" , request()->event_id);
            }
            if(request()->start_date)
                $q->whereDate("created_at" , ">=" , request()->start_date);
            if(request()->end_date)
                $q->whereDate("created_at" , "<=" , request()->end_date);
        });
        $orders = $orders->where('creator_id',auth()->guard('front')->user()->id)->latest()->get();
        //$orders = EventOrder::where('creator_id',auth()->guard('front')->user()->id)->latest()->get();
        return view('pages.frontend.orders', ['events'=>$events,'orders' => $orders , 'seo_details' => (object)['title' => 'Orders']]);
    }
    public function orderDetails($placeholder){
        $order = EventOrder::findOrFail(decrypt($placeholder));
        return view('pages.frontend.order-details', ['order' => $order, 'seo_details' => (object)['title' => 'Order Details']]);
    }
    public function exportRegistration(){
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RegistrationExport, 
            'registrations.csv', 
            \Maatwebsite\Excel\Excel::CSV 
        );
    }
    public function salesReport(){
        $event = (request()->event)?request()->event:'';
        $startDate = (request()->start_date)?request()->start_date:date('Y-m-01');
        $endDate = (request()->end_date)?request()->end_date:date('Y-m-d');
        $orders = EventOrder::whereHas('event', function ($q) {
            $q->where('creator_id', auth()->guard('front')->user()->id);
        })->where(function($q) use ($startDate,$endDate,$event){
            $q->whereDate("created_at" , ">=" , $startDate);
            $q->whereDate("created_at" , "<=" , $endDate);
            $q->whereDate("payment_status" , "=" , "paid");
            if(!empty($event)){
                $q->where('event_id', $event);
            }
        });
        $orders = $orders->latest()->get();
        $organising_events = auth()->guard('front')->user()->allControlEvents();
        return view('pages.frontend.sales-report', ['orders'=>$orders,'start_date'=>$startDate,'end_date'=>$endDate,'events'=>$organising_events, 'seo_details' => (object)['title' => 'Sales Report']]);
    }
    public function stripeConnect(){
        return view('pages.frontend.connect-stripe', ['seo_details' => (object)['title' => 'Connect Stripe']]);
    }
    public function disconnectStripeConnect(){
        $user_id = auth()->guard('front')->user()->id;
        // i need to udpate these field of volunteer stripe_email=null, stripe_account_id=null, stripe_payouts_enabled=0, stripe_charges_enabled=0, stripe_raw=null, stripe_connect_dt=null
        Volunteer::where('id', $user_id)->update([
            'stripe_email' => null,
            'stripe_account_id' => null,
            'stripe_payouts_enabled' => 0,
            'stripe_charges_enabled' => 0,
            'stripe_raw' => null,
            'stripe_connect_dt' => null,
        ]);
        return $this->generateJsonResponse(true, 'Stripe account disconnected successfully.');
    }
    public function registrationList(){
        $organising_events = auth()->guard('front')->user()->allControlEvents();
        
        $registration_builder = EventOrderTicket::where(function($query) use($organising_events){
                                    $query->whereIn('event_id', $organising_events->pluck('id'))
                                    ->getVolunteerRegistrationLists();
                                });
        $registration_builder_p = EventOrderTicket::join('events', 'events.id', '=', 'event_order_tickets.event_id')->select('event_order_tickets.*')->where(function($query) use($organising_events){
                                    $query->whereIn('event_id', $organising_events->pluck('id'))
                                    ->getVolunteerRegistrationLists();
                                });
        
        if (request()->filter === 'oldest') {
            //$registrations = (clone $registration_builder)->get();
            $registrations = (clone $registration_builder_p)->paginate(50)->withQueryString();
        } else {
            //$registrations = (clone $registration_builder)->latest()->get();
            $registrations = (clone $registration_builder_p)->orderBy('events.start_datetime','desc')->paginate(50)->withQueryString();
        }
        $new = (clone $registration_builder)->new()->count();
        $cancelled = (clone $registration_builder)->cancelled()->count();
        $approved = (clone $registration_builder)->active()->count();
        $free = (clone $registration_builder)->free()->count();
        $paid = (clone $registration_builder)->paid()->count();
 
        return view('pages.frontend.registration-list', [
                                                        'registrations' => $registrations,
                                                        'seo_details' => (object)['title' => 'Registration List'],
                                                        'reports' => [
                                                            //'registration' => $registrations->count(),
                                                            'registration' => $registrations->total(),
                                                            'new' => $new,
                                                            'cancelled' => $cancelled,
                                                            'approved' => $approved,
                                                            'free' => $free,
                                                            'paid' => $paid,
                                                        ],
                                                        'events' => $organising_events , 
                                                    ]);
    }
    public function requestCommunityForm($ticket){
        $orderTicket = EventOrderTicket::findOrFail(decrypt($ticket));
        if (!isset($orderTicket->id)) {
            return $this->generateJsonResponse(false, 'Ticket not found.');
        }
        if ($orderTicket->community_form_status === 'approved') {
            return $this->generateJsonResponse(false, 'Cannot request for an already approved certificate.');
        }
        if ($orderTicket->community_form_status === 'rejected') {
            return $this->generateJsonResponse(false, 'Cannot request for an already rejected certificate.');
        }
        $event = $orderTicket->event;
        $volunteer = $orderTicket->volunteer;
        if($orderTicket->checkin!=1 && $orderTicket->community_form_status=='pending'){
            try{
                $emailData = [
                    'org_name'=>$event->creator->first_name.' '.$event->creator->last_name,
                    'volunteer'=>$volunteer->first_name.' '.$volunteer->last_name,
                    'event_name'=>$event->event_name,
                    //'dashboard_link'=>route('front-cleanup-attending'),
                    'dashboard_link'=>$this->mailUrl('front-certificate-volunteer-requests'),
                ];
                $sub = 'Community Service Request – '.$emailData['volunteer'].' for '.$event->event_name;
                $this->sendEmail($event->creator->email_address, $sub, $emailData, "request-community-service-form");
                $orderTicket->community_form_status = 'requested';
                $orderTicket->checkin = 0;
                $orderTicket->certificate_requested_at = date('Y-m-d H:i:s');
                if($event->is_completed==1){
                    $orderTicket->is_late_checkin = 1;
                }
                $orderTicket->save();
                return $this->generateJsonResponse(true, 'Request email has been sent to organizer for your community service form.');
            }catch(\Exception $e){
                return $this->generateJsonResponse(false, "Issues occured while sending emails.");
            }
        }
        return $this->generateJsonResponse(true, 'Issue occured while sending request email.');
    }
    public function submitCertificateRequest(){
        request()->validate([
            'registration_id' => 'required|string',
            'request_reason' => 'required|string',
            'proof_images'     => 'required|array|min:1|max:4',
            'proof_images.*'   => 'required|image|mimes:jpeg,png,jpg,gif|max:3072',
        ],[
            'proof_images.required'    => 'Please upload at least 1 proof image.',
            'proof_images.min'         => 'Please upload at least 1 proof image.',
            'proof_images.max'         => 'You can upload a maximum of 4 images.',
            'proof_images.*.image'     => 'Each file must be a valid image.',
            'proof_images.*.mimes'     => 'Only jpeg, png, jpg, and gif images are allowed.',
            'proof_images.*.max'       => 'Each image must not exceed 3MB.',
            'request_reason.required'  => 'Please enter a reason for your certificate request.',
        ]);
        $orderTicket = EventOrderTicket::findOrFail(decrypt(request()->registration_id));
        if (!isset($orderTicket->id)) {
            $this->setFlashSession(false, 'Ticket not found.');
            return redirect()->back();
        }
        if ($orderTicket->community_form_status === 'approved') {
            $this->setFlashSession(false, 'Cannot request for an already approved certificate.');
            return redirect()->back();
        }
        if ($orderTicket->community_form_status === 'rejected') {
            $this->setFlashSession(false, 'Cannot request for an already rejected certificate.');
            return redirect()->back();
        }
        $event = $orderTicket->event;
        $volunteer = $orderTicket->volunteer;
        if($orderTicket->checkin!=1 && $orderTicket->community_form_status=='pending'){
            try{
                $emailData = [
                    'org_name'=>$event->creator->first_name.' '.$event->creator->last_name,
                    'volunteer'=>$volunteer->first_name.' '.$volunteer->last_name,
                    'event_name'=>$event->event_name,
                    //'dashboard_link'=>route('front-cleanup-attending'),
                    'dashboard_link'=>$this->mailUrl('front-certificate-volunteer-requests'),
                ];
                $sub = 'Community Service Request – '.$emailData['volunteer'].' for '.$event->event_name;
                $this->sendEmail($event->creator->email_address, $sub, $emailData, "request-community-service-form");
                $orderTicket->community_form_status = 'requested';
                $orderTicket->checkin = 0;
                $orderTicket->certificate_requested_at = date('Y-m-d H:i:s');
                $orderTicket->certificate_request_text = request()->request_reason;
                if($event->is_completed==1){
                    $orderTicket->is_late_checkin = 1;
                }
                if (request()->hasFile('proof_images')) {
                    $files = request()->file('proof_images');
                    // Wrap in array if single file, so loop works for both cases
                    if (!is_array($files)) {
                        $files = [$files];
                    }
                    $existingImages = [];
                    if (!empty($event->proof_images)) {
                        $existingImages = json_decode($event->proof_images, true) ?? [];
                    }
                    /* if (count($files) > 4) {
                        return $this->generateJsonResponse(false,'You can only upload a maximum of 4 images');
                    } */
                    if (count($existingImages) + count($files) > 4) {
                        $this->setFlashSession(false, 'You can only upload a maximum of 4 images');
                        return redirect()->back();
                    }
                    $newPhotoPaths = [];
                    foreach ($files as $photo) {
                        $newPhotoPaths[] = $this->moveFiles($photo, 'events/certificate_requests');
                    }
                    $allPhotos = array_merge($existingImages, $newPhotoPaths);
                    $orderTicket->certificate_request_images = json_encode($allPhotos);
                }
                $orderTicket->save();
                $this->setFlashSession(true, 'Request email has been sent to organizer for your community service form.');
                return redirect()->back();
            }catch(\Exception $e){
                $this->setFlashSession(false, "Issues occured while sending emails.");
                return redirect()->back();
            }
        }
        $this->setFlashSession(false, 'Issue occured while requesting certificate.');
        return redirect()->back();
    }
    public function ticketMarkCheckinTest($ticket){
        $orderTicket = EventOrderTicket::findOrFail(decrypt($ticket));
        $event = $orderTicket->event;
        $volunteer = $orderTicket->volunteer;

        $data = [
            'event'            => $event,
            'volunteerName'    => $volunteer->name,
            //'hours'            => $orderTicket->hours_completed,
            'hours'            => $event->volunteer_hours,
            'organization_name'   => $event->creator->organisation_name,
            'organizer_name'   => $event->creator->first_name.' '.$event->creator->last_name,
            'organization_email'   => $event->creator->email_address,
            'organizer_link'   => route('organizer-detail', ['slug' => $event->creator->organisation_landing_slug]),
        ];
        try{
            $html = view('pages.events.community-form-new', $data)->render();
            $pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true,'isRemoteEnabled' => true])->loadHTML($html);
            //$filename = 'community_form_' . $orderTicket->id . '_' . time() . '.pdf';
            $filename = 'community_form_123.pdf';
            $path = 'events/community_forms/' . $filename;
            $publicPath = public_path($path);
            if (!file_exists(dirname($publicPath))) {
                mkdir(dirname($publicPath), 0755, true);
            }
            file_put_contents($publicPath, $pdf->output());
            $orderTicket->community_form_pdf = $path;
            $orderTicket->save();
            $community_form = url('/').'/'.$path;
        }catch(\Exception $e){
            if($callFromBackend){
                $this->setFlashSession(false , "Issues occured while making generating PDF");
                return redirect()->back();
            } else {
                return $this->generateJsonResponse(false, "Issues occured while making generating PDF",[
                    'error' => $e->getMessage(),
                    'line'  => $e->getLine(),
                    'file'  => $e->getFile(),
                    // 'trace' => $e->getTraceAsString() // uncomment if needed
                ]);
            }
        }
        return $this->generateJsonResponse(true, "DONE");

    }
    public function ticketMarkCheckin($ticket,$callFromBackend=false){
        $orderTicket = EventOrderTicket::findOrFail(decrypt($ticket));
        $event = $orderTicket->event;
        $volunteer = $orderTicket->volunteer;
        if($orderTicket->community_form_pdf){
            $community_form = $orderTicket->community_form_pdf; 
        }else{
            $data = [
                'event'            => $event,
                'volunteerName'    => $volunteer->name,
                //'hours'            => $orderTicket->hours_completed,
                'hours'            => $event->volunteer_hours,
                'organization_name'   => $event->creator->organisation_name,
                'organizer_name'   => $event->creator->first_name.' '.$event->creator->last_name,
                'organization_email'   => $event->creator->email_address,
                'organizer_link'   => route('organizer-detail', ['slug' => $event->creator->organisation_landing_slug]),
            ];
            try{
                $html = view('pages.events.community-form-new', $data)->render();
                $pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true,'isRemoteEnabled' => true])->loadHTML($html);
                $filename = 'community_form_' . $orderTicket->id . '_' . time() . '.pdf';
                $path = 'events/community_forms/' . $filename;
                $publicPath = public_path($path);
                if (!file_exists(dirname($publicPath))) {
                    mkdir(dirname($publicPath), 0755, true);
                }
                file_put_contents($publicPath, $pdf->output());
                $orderTicket->community_form_pdf = $path;
                $orderTicket->save();
                $community_form = url('/').'/'.$path;
            }catch(\Exception $e){
                if($callFromBackend){
                    $this->setFlashSession(false , "Issues occured while making generating PDF");
                    return redirect()->back();
                } else {
                    return $this->generateJsonResponse(false, "Issues occured while making generating PDF",[
                        'error' => $e->getMessage(),
                        'line'  => $e->getLine(),
                        'file'  => $e->getFile(),
                        // 'trace' => $e->getTraceAsString() // uncomment if needed
                    ]);
                }
            }
        }
        try{
            if($orderTicket->checkin==1){
                $orderTicket->community_form_status = 'pending';
                $orderTicket->checkin = 0;
                $orderTicket->save();
                if($callFromBackend){
                    $this->setFlashSession(true , "Volunteer has been checked out successfully.");
                    return redirect()->back();
                } else {
                    return $this->generateJsonResponse(true, 'Volunteer has been checked out successfully.');
                }
            } else {
                if(!empty($orderTicket->waiver_filled_at)){
                    /* $emailData = [
                        'volunteer' => $volunteer->first_name.' '.$volunteer->last_name,
                        'event_title' => $event->event_name,
                        //'certificate_download_link' => $community_form,
                        'certificate_download_link' => $this->mailUrl('show-community-service-pdf', ['ticket' => encrypt($orderTicket->id)]),
                        'dashboard_link' => $this->mailUrl('front-cleanup-attending')
                    ];
                    $sub = 'Your Community Service Certificate for '.$event->event_name.' Is Ready';
                    $this->sendEmail($volunteer->email_address, $sub, $emailData, "community-service-form");
                    $orderTicket->community_form_status = 'approved'; */
                    $orderTicket->checkin = 1;
                    $orderTicket->checkin_dt = date('Y-m-d H:i:s');
                    if($event->is_completed==1){
                        $orderTicket->community_form_status = 'approved';
                        $orderTicket->is_late_checkin = 1;
                    }
                    $orderTicket->save();
                    if($event->is_completed==1){
                        $to_arr = [];
                        $log_arr = [];
                        if (isset($orderTicket->volunteer) && isset($orderTicket->volunteer->id)) {
                            $tickets = $event->tickets()->where('checkin', 1)->whereNotNull('waiver_filled_at')->get();
                            $email = $orderTicket->volunteer->email_address;
                            $name = $orderTicket->volunteer->first_name.' '.$orderTicket->volunteer->last_name;
                            if(!empty(trim($email))){
                                $to_arr[] = 
                                [
                                    "to" => [
                                        ["email" => trim($email)]
                                        //["email" => 'learningdcm@gmail.com']
                                    ],
                                    "dynamic_template_data" => [
                                        "volunteer_name" => $name,
                                        "volunteer_email" => $email,
                                        "event_name" =>  $event->event_name,
                                        "event_date" => $event->startTimeAndEndTime,
                                        "event_location" => $event->fullAddress,
                                        "organizer_name" => $event->creator->first_name.' '.$event->creator->last_name,
                                        "total_volunteers" => $tickets->count(),
                                        "waste_collected" => $event->waste_collected,
                                        "area_cleaned" => $event->area_cleaned,
                                        "service_hours" => $event->volunteer_hours ?? 'N/A',
                                        'highlight_link' => $this->mailUrl('cleanup-highlights', ['event' => encrypt($event->id)]),
                                        'certificate_download_link' => $this->mailUrl('show-community-service-pdf', ['ticket' => encrypt($orderTicket->id)]),
                                    ],
                                ];
                                $log_arr[] = [
                                    "id" => $orderTicket->id,
                                    "email" => trim($email)
                                ];
                            }
                        }
                        if(!empty($to_arr)){
                            $this->sendSendgridTemplateEmailWithoutLog($to_arr,"d-84b101582dbb408aa6f90cf53b2be2d0");
                        }
                    }
                    if($callFromBackend){
                        $this->setFlashSession(true , "Volunteer has been marked as check-in successfully.");
                        return redirect()->back();
                    } else {
                        return $this->generateJsonResponse(true, 'Volunteer has been marked as check-in successfully.');
                    }
                } else {
                    if($callFromBackend){
                        $this->setFlashSession(true , "Waiver should be signed before marking the volunteer checked-in.");
                        return redirect()->back();
                    } else {
                        return $this->generateJsonResponse(true, 'Waiver should be signed before marking the volunteer checked-in.');
                    }
                }
            }
        }catch(\Exception $e){
            if($callFromBackend){
                $this->setFlashSession(false , "Issues occured while updating the status.");
                return redirect()->back();
            } else {
                return $this->generateJsonResponse(false, "Issues occured while updating the status.");
            }
        }
    }
    public function signWaiverMarkCheckingManually($id)
    {
        request()->validate([
            'mark_as_signed' => 'required|accepted',
            'waiver_signed_external_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'mark_as_signed.required' => 'Please confirm Mark As Signed.',
            'mark_as_signed.accepted' => 'Please confirm Mark As Signed.',
            'waiver_signed_external_document.mimes' => 'Only PDF, JPG, and PNG files are allowed.',
            'waiver_signed_external_document.max' => 'The document must not be greater than 5MB.',
        ]);

        try {
            $orderTicket = EventOrderTicket::findOrFail(decrypt($id));
            $authUser = auth()->guard('front')->user();

            $cohostEventIds = $authUser->cohostingEvents()->pluck('events.id');
            $hostingEventIds = $authUser->events()->pluck('events.id');
            $allowedEventIds = $cohostEventIds->merge($hostingEventIds);

            if (!$allowedEventIds->contains($orderTicket->event_id)) {
                $this->setFlashSession(false, 'Unauthorized action.');
                return redirect()->back();
            }

            if ($orderTicket->rsvp_confirmation != 1) {
                $this->setFlashSession(false, 'You can not check-in before RSVP confirmation.');
                return redirect()->back();
            }

            if ($orderTicket->checkin == 1) {
                $this->setFlashSession(false, 'This attendee is already checked-in.');
                return redirect()->back();
            }

            if (!empty($orderTicket->waiver_filled_at)) {
                $this->setFlashSession(false, 'Waiver is already signed for this attendee.');
                return redirect()->back();
            }

            /* if ($orderTicket->event && $orderTicket->event->is_completed == 1) {
                $this->setFlashSession(false, 'You cannot check-in an attendee for a completed event.');
                return redirect()->back();
            } */

            $orderTicket->waiver_filled_at = now();
            $orderTicket->signature = null;
            $orderTicket->is_manual_checkin = 1;

            if (request()->hasFile('waiver_signed_external_document')) {
                $document = $this->moveFiles(request()->file('waiver_signed_external_document'), 'events/manual_waivers');
                //$orderTicket->waiver_signed_external_document = json_encode($document);
                $orderTicket->waiver_signed_external_document = $document['path'];
            }

            $orderTicket->save();

            return $this->ticketMarkCheckin($id, true);
        } catch (\Exception $e) {
            $this->setFlashSession(false, 'Issues occured while marking waiver signed and check-in.');
            return redirect()->back();
        }
    }
    public function showCommunityServicePdf($ticket){
        $orderTicket = EventOrderTicket::findOrFail(decrypt($ticket));
        if($orderTicket->rsvp_confirmation==1 && $orderTicket->checkin==1){
            $pdfUrl = $orderTicket->community_form_pdf;
            //$relativePath = str_replace(url('/'), '', $pdfUrl);
            $exp = explode('events/', $pdfUrl);
            $relativePath = 'events/'.$exp[1];
            $filePath = public_path($relativePath);
            if (!file_exists($filePath)) {
                $seo_details = (object)['title' => 'Community Service PDF'];
                return view('pages.frontend.show-community-service-pdf', ['seo_details' => $seo_details]);
            }
            // load pdf on current url/page
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf'
            ]);
        }
        $seo_details = (object)['title' => 'Community Service PDF'];
        return view('pages.frontend.show-community-service-pdf', ['seo_details' => $seo_details]);
    }
    /* public function ticketMarkCheckin($id){
        $registration = EventOrderTicket::findOrFail(decrypt($id));
        $registration->checkin = 1;
        $registration->save();
        
        $mail_content = [
            //'org_name'         => $organiser->first_name.' '.$organiser->last_name,
        ];
        $this->sendEmail($registration->volunteer->email_address, CONTACT_ORG_MAIL_SUB, $mail_content, "community-service-confirmed");

        return $this->generateJsonResponse(true, 'Volunteer has been marked as check-in successfully.');
    } */
    public function postRegistrationNotes(){
        $request = request();
       
        $validated = $request->validate([
            'ticket_order_id' => 'required|integer|exists:event_order_tickets,id',
            'ticket_order_notes' => 'required|string|max:2000',
        ]);

        $user = auth()->guard('front')->user();
        $organising_events = $user->allControlEvents()->pluck('id')->toArray();

        $ticket = EventOrderTicket::where('id', $validated['ticket_order_id'])
            ->whereIn('event_id', $organising_events)
            ->firstOrFail();

        // Assuming 'notes' is a hasOne or morphOne relationship
        $ticket->notes()->create([
            'added_by'  => $user->id ,
            'note_type' => 'organiser',
            'notes'   => $validated['ticket_order_notes']
        ]);

        $this->setFlashSession(true, 'Notes updated successfully.');
        return redirect()->back();
    }
    public function profile(){
        $seo_details = (object)['title' => 'Profile'];
        return view('pages.frontend.volunteer-profile', [
                                    'seo_details' => $seo_details , 
                                    'cities' => City::all() ,
                                    'user' => auth()->guard('front')->user()]);
    }
    public function saveProfile(){
        $request = request();
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'phone'         => ['nullable', 'string' , new \App\Rules\ValidUSPhoneNumber()],
            'city_id'       => 'nullable',//'nullable|exists:cities,city_id',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            //'zip_code'      => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cropped_image' => 'nullable|string',
        ]);

        if(request()->hasFile('profile_picture')) {
            $uploadedFiles = $this->uploadFiles('profile_picture' , 'uploads/volunteer_profile_pictures');
            $validated['profile_picture']= json_encode($uploadedFiles);
            
        }else{
            unset($validated['profile_picture']);
        }
        if(request()->filled('cropped_image')) {
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', request()->input('cropped_image'));
            $imageData = base64_decode($base64);

            // Mirror exactly what moveFiles returns
            $originalName = 'profile_picture.jpg';
            $fileName = str_replace(' ', '', date('dmyhis') . $originalName);
            $destinationPath = 'uploads/volunteer_profile_pictures';

            // Save directly — no temp file, no UploadedFile
            file_put_contents(public_path($destinationPath) . '/' . $fileName, $imageData);

            $validated['profile_picture'] = json_encode([
                'path'          => "$destinationPath/" . $fileName,
                'original_name' => $originalName,
                'extension'     => 'jpg',
            ]);
            
        }else{
            unset($validated['cropped_image']);
        }
        $pincode = '';
        if(isset($validated['city_id']) && !empty($validated['city_id'])){
            $cityStateZip = CityStateZip::find($validated['city_id']);
            if(isset($cityStateZip->id)){
                $pincode = $cityStateZip->zipcode;
            }
        }
        $validated['pincode']= $pincode;
        $validated['phone'] = formatPhoneNumber($validated['phone']);
        $user = auth()->guard('front')->user();
        $user->update($validated);

        $this->setFlashSession(true, 'Profile updated successfully.');
        return redirect()->back();
    }
    public function saveOrganisationDetails(){
        $request = request();

        $validated = $request->validate([
            'organisation_name' => 'required|string|max:255',
            'organisation_website' => 'nullable|string|max:255',
            'organisation_landing_slug' => 'nullable|string|max:255|unique:volunteers,organisation_landing_slug,' . auth()->guard('front')->id(),
            'organisation_short_description' => 'nullable|string|max:1000',
            'organisation_facebook' => 'nullable|string|max:255',
            'organisation_instagram' => 'nullable|string|max:255',
            'organisation_twitter' => 'nullable|string|max:255',
            'organisation_linkedin' => 'nullable|string|max:255',
            'organisation_prev_cleanups_count' => 'nullable|integer',
            'organisation_prev_volunteers_count' => 'nullable|integer',
            'organisation_prev_waste_collected_pounds_count' => 'nullable|integer',
            'organisation_prev_volunteer_hours_count' => 'nullable|integer',
            'organisation_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cropped_image' => 'nullable|string',
        ]);

        $user = auth()->guard('front')->user();

        if ($request->hasFile('organisation_logo')) {
            $uploadedFiles = $this->uploadFiles('organisation_logo', 'uploads/organisation_logos');
            $validated['organisation_logo'] = json_encode($uploadedFiles);
        } else {
            unset($validated['organisation_logo']);
        }
        if(request()->filled('cropped_image')) {
            $base64 = preg_replace('/^data:image\/\w+;base64,/', '', request()->input('cropped_image'));
            $imageData = base64_decode($base64);

            // Mirror exactly what moveFiles returns
            $originalName = 'organisation_logo.jpg';
            $fileName = str_replace(' ', '', date('dmyhis') . $originalName);
            $destinationPath = 'uploads/organisation_logos';

            // Save directly — no temp file, no UploadedFile
            file_put_contents(public_path($destinationPath) . '/' . $fileName, $imageData);

            $validated['organisation_logo'] = json_encode([
                'path'          => "$destinationPath/" . $fileName,
                'original_name' => $originalName,
                'extension'     => 'jpg',
            ]);
            
        }else{
            unset($validated['cropped_image']);
        }
        $validated['organisation_landing_slug'] = $this->generateSlug(Volunteer::class, 'organisation_landing_slug', request()->organisation_landing_slug ? : request()->input('organisation_name'), $user->id ? : 0);
        $user->update($validated);

        $this->setFlashSession(true, 'Organization details updated successfully.');
        return redirect()->back();
    }
    public function certificateRequests(){
        $data = [];
        $data['events'] = auth()->guard('front')->user()->participatedEvents()->distinct()->get();
        //$data['requests'] = auth()->guard('front')->user()->attendingEventsWithoutActive()->wherePivotNotNull('certificate_requested_at')->get();
        $query = auth()->guard('front')->user()->attendingEventsWithoutActive()
        ->wherePivotNotNull('certificate_requested_at');

        // Filter by Event
        if (request()->filled('event')) {
            $query->where('events.id', request()->event);
        }

        // Filter by Organizer (name, email, phone)
        if (request()->filled('organizer_search')) {
            $search = request()->organizer_search;
            $query->whereHas('creator', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('email_address', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by Date (certificate_requested_at date)
        if (request()->filled('by_date')) {
            $rawDate = request()->by_date;
        
            // Try parsing with Carbon's flexible parse (handles m/d/Y, d/m/Y, Y-m-d etc.)
            $date = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
            $query->wherePivot('certificate_requested_at', 'LIKE', "%{$date}%");
        }

        $data['requests'] = $query->get();
        $data['by_date']  = request()->by_date;
        $data['seo_details'] = (object)['title' => 'Certificate Requests'];
        return view('pages.frontend.certificate-requests', $data); 
    }
    public function certificateRequestDetail($id){
        $orderTicket = EventOrderTicket::with(['volunteer', 'event', 'event.creator'])->findOrFail(decrypt($id));
        
        $authUser = auth()->guard('front')->user();
        $cohostCreatorIds = $orderTicket->event->cohosts()->where('request_status', 'approved')->pluck('volunteer_id')->toArray();
        $cohostCreatorIds[] = $orderTicket->event->creator_id;
        if(!in_array($authUser->id, array_merge($cohostCreatorIds, [$orderTicket->volunteer_id])) || empty(trim($orderTicket->certificate_requested_at))){
            abort(403, 'Unauthorized action.');
        }

        $data['cohostCreatorIds']      = $cohostCreatorIds;
        $data['authUser']      = $authUser;
        $data['ticket']      = $orderTicket;
        $data['event']       = $orderTicket->event;
        $data['volunteer']   = $orderTicket->volunteer;
        $data['seo_details'] = (object)['title' => 'Certificate Request Detail'];
        return view('pages.frontend.certificate-request-detail', $data); 
    }
    public function certificateManagement(){
        $data = [];
        $authUser = auth()->guard('front')->user();
        $cohost_events     = $authUser->cohostingEvents()->orderBy('start_datetime', 'desc')->get();
        $organising_events = $authUser->events()->orderBy('start_datetime', 'desc')->get();
        $all_events        = $cohost_events->merge($organising_events);
        $event_ids = $all_events->pluck('id');
        $query = EventOrderTicket::with(['volunteer', 'event'])
            ->whereIn('event_id', $event_ids)
            ->whereNotNull('certificate_requested_at');

        // Filter by Event
        if (request()->filled('event')) {
            $query->where('event_id', request()->event);
        }

        // Filter by Volunteer (name, email, phone)
        if (request()->filled('volunteer_search')) {
            $search = request()->volunteer_search;
            $query->whereHas('volunteer', function($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                ->orWhere('email_address', 'LIKE', "%{$search}%")
                ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // Filter by Status
        if (request()->filled('status')) {
            $query->where('community_form_status', request()->status);
        }

        // Filter by Date
        if (request()->filled('by_date')) {
            try {
                $date = \Carbon\Carbon::parse(request()->by_date)->format('Y-m-d');
                $query->whereDate('certificate_requested_at', $date);
            } catch (\Exception $e) {}
        }

        $data['requests']  = $query->latest('certificate_requested_at')->get();
        $data['events']    = $organising_events;
        $data['by_date']   = request()->by_date;
        $data['seo_details'] = (object)['title' => 'Certificate Management'];
        return view('pages.frontend.certificate-management', $data);
    }
    public function approveCertificateRequest($ticket_id,$callFromBackend=false){
        try {
            $orderTicket = EventOrderTicket::findOrFail(decrypt($ticket_id));

            if(empty(trim($orderTicket->waiver_filled_at))){
                return $this->generateJsonResponse(false, 'A waiver must be signed before the request can be approved.');
            }
            
            if(!$callFromBackend){
                $authUser = auth()->guard('front')->user();
                // Security: make sure this ticket belongs to organizer's event
                $cohost_events     = $authUser->cohostingEvents()->pluck('events.id');
                $organising_events = $authUser->events()->pluck('events.id');
                $all_event_ids     = $cohost_events->merge($organising_events);

                if (!$all_event_ids->contains($orderTicket->event_id)) {
                    return $this->generateJsonResponse(false, 'Unauthorized action.');
                }
            }

            if ($orderTicket->community_form_status === 'approved') {
                return $this->generateJsonResponse(false, 'This certificate is already approved.');
            }

            if (!(isset($orderTicket->volunteer) && isset($orderTicket->volunteer->id))) {
                return $this->generateJsonResponse(false, 'Invalid volunteer information.');
            }

            // Role: 1 = organizer (event creator), 2 = cohost
            if(!$callFromBackend){
                $role = $orderTicket->event->creator_id == $authUser->id ? 1 : 2;
            } else {
                $role = 3;
            }

            $orderTicket->community_form_status             = 'approved';
            $orderTicket->certificate_approve_reject_at     = now();
            $orderTicket->certificate_approve_reject_by     = ($callFromBackend)?auth()->user()->id:$authUser->id;
            $orderTicket->certificate_approve_reject_by_role = $role;
            $orderTicket->checkin = 1;
            $orderTicket->checkin_dt = date('Y-m-d H:i:s');
            $orderTicket->save();
            $event = $orderTicket->event;
            $volunteer = $orderTicket->volunteer;
            if($orderTicket->community_form_pdf){
                $community_form = $orderTicket->community_form_pdf; 
            }else{
                $data = [
                    'event'            => $event,
                    'volunteerName'    => $volunteer->name,
                    //'hours'            => $orderTicket->hours_completed,
                    'hours'            => $event->volunteer_hours,
                    'organization_name'   => $event->creator->organisation_name,
                    'organizer_name'   => $event->creator->first_name.' '.$event->creator->last_name,
                    'organization_email'   => $event->creator->email_address,
                    'organizer_link'   => route('organizer-detail', ['slug' => $event->creator->organisation_landing_slug]),
                ];
                try{
                    $html = view('pages.events.community-form-new', $data)->render();
                    $pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true,'isRemoteEnabled' => true])->loadHTML($html);
                    $filename = 'community_form_' . $orderTicket->id . '_' . time() . '.pdf';
                    $path = 'events/community_forms/' . $filename;
                    $publicPath = public_path($path);
                    if (!file_exists(dirname($publicPath))) {
                        mkdir(dirname($publicPath), 0755, true);
                    }
                    file_put_contents($publicPath, $pdf->output());
                    $orderTicket->community_form_pdf = $path;
                    $orderTicket->save();
                    $community_form = url('/').'/'.$path;
                }catch(\Exception $e){
                    return $this->generateJsonResponse(false, "Issues occured while making generating PDF",[
                        'error' => $e->getMessage(),
                        'line'  => $e->getLine(),
                        'file'  => $e->getFile(),
                        // 'trace' => $e->getTraceAsString() // uncomment if needed
                    ]);
                }
            }
            try{
                if($event->is_completed==1){
                    $to_arr = [];
                    $log_arr = [];
                    if (isset($volunteer) && isset($volunteer->id)) {
                        $tickets = $event->tickets()->where('checkin', 1)->whereNotNull('waiver_filled_at')->get();
                        $email = $volunteer->email_address;
                        $name = $volunteer->first_name.' '.$volunteer->last_name;
                        if(!empty(trim($email))){
                            $to_arr[] = 
                            [
                                "to" => [
                                    ["email" => trim($email)]
                                    //["email" => 'learningdcm@gmail.com']
                                ],
                                "dynamic_template_data" => [
                                    "volunteer_name" => $name,
                                    "volunteer_email" => $email,
                                    "event_name" =>  $event->event_name,
                                    "event_date" => $event->startTimeAndEndTime,
                                    "event_location" => $event->fullAddress,
                                    "organizer_name" => $event->creator->first_name.' '.$event->creator->last_name,
                                    "total_volunteers" => $tickets->count(),
                                    "waste_collected" => $event->waste_collected,
                                    "area_cleaned" => $event->area_cleaned,
                                    "service_hours" => $event->volunteer_hours ?? 'N/A',
                                    'highlight_link' => $this->mailUrl('cleanup-highlights', ['event' => encrypt($event->id)]),
                                    'certificate_download_link' => $this->mailUrl('show-community-service-pdf', ['ticket' => encrypt($orderTicket->id)]),
                                ],
                            ];
                            $log_arr[] = [
                                "id" => $orderTicket->id,
                                "email" => trim($email)
                            ];
                        }
                    }
                    if(!empty($to_arr)){
                        $this->sendSendgridTemplateEmailWithoutLog($to_arr,"d-84b101582dbb408aa6f90cf53b2be2d0");
                    }
                }
            }catch(\Exception $e){
                return $this->generateJsonResponse(false, "Issues occured while sending the email.");
            }

            return $this->generateJsonResponse(true, 'Certificate approved successfully.');

        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to approve certificate: ' . $e->getMessage());
        }
    }

    public function rejectCertificateRequest($ticket_id,$callFromBackend=false){
        request()->validate([
            'reject_reason' => 'required|string',
        ]);

        try {
            $orderTicket = EventOrderTicket::findOrFail(decrypt($ticket_id));
            if(!$callFromBackend){
                $authUser = auth()->guard('front')->user();

                // Security: make sure this ticket belongs to organizer's event
                $cohost_events     = $authUser->cohostingEvents()->pluck('events.id');
                $organising_events = $authUser->events()->pluck('events.id');
                $all_event_ids     = $cohost_events->merge($organising_events);

                if (!$all_event_ids->contains($orderTicket->event_id)) {
                    return $this->generateJsonResponse(false, 'Unauthorized action.');
                }
            }

            if ($orderTicket->community_form_status === 'approved') {
                return $this->generateJsonResponse(false, 'Cannot reject an already approved certificate.');
            }

            if ($orderTicket->community_form_status === 'rejected') {
                return $this->generateJsonResponse(false, 'This certificate is already rejected.');
            }

            // Role: 1 = organizer (event creator), 2 = cohost
            if(!$callFromBackend){
                $role = $orderTicket->event->creator_id == $authUser->id ? 1 : 2;
            } else {
                $role = 3;
            }

            $orderTicket->community_form_status              = 'rejected';
            $orderTicket->certificate_reject_reason          = request()->reject_reason;
            $orderTicket->certificate_approve_reject_at      = now();
            $orderTicket->certificate_approve_reject_by      = ($callFromBackend)?auth()->user()->id:$authUser->id;
            $orderTicket->certificate_approve_reject_by_role = $role;
            $orderTicket->save();

            if (isset($orderTicket->volunteer) && isset($orderTicket->volunteer->id) && isset($orderTicket->event) && isset($orderTicket->event->id)) {
                $email = $orderTicket->volunteer->email_address;
                $name = $orderTicket->volunteer->first_name.' '.$orderTicket->volunteer->last_name;
                if(!empty(trim($email))){
                    $to_arr = [];
                    $to_arr[] = 
                    [
                        "to" => [
                            ["email" => trim($email)]
                            //["email" => 'learningdcm@gmail.com']
                        ],
                        "dynamic_template_data" => [
                            "volunteer_name" => $name,
                            "volunteer_email" => $email,
                            "event_name" =>  $orderTicket->event->event_name,
                            "reject_reason" => request()->reject_reason,
                            'request_page_link' => $this->mailUrl('front-certificate-my-requests'),
                        ],
                    ];
                    if(!empty($to_arr)){
                        $this->sendSendgridTemplateEmailWithoutLog($to_arr,"d-d730056de6fd472ab482a9f8de33078d");
                    }
                }
            }

            return $this->generateJsonResponse(true, 'Certificate request rejected successfully.');

        } catch (\Exception $e) {
            return $this->generateJsonResponse(false, 'Failed to reject certificate: ' . $e->getMessage());
        }
    }
    public function embeddedWidget()
    {
        $data['categories']        = EventCategory::all();
        $data['seo_details'] = (object)['title' => 'Embedded Widget'];
        $data['listing_data'] = auth()->guard()->user()->widgets()->orderBy('id', 'desc')->get();
        return view('pages.frontend.embedded-widget', $data); 
    }
    public function manageWidget($id=null)
    {
        $widget = null;
        if(!empty($id)){
            $widget = VolunteerWidget::where('id', decrypt($id))->first();
        }
        $data['widget'] = $widget;
        $data['categories'] = EventCategory::all();
        $data['organizers'] = Volunteer::where('is_organiser', 1)->get();
        $data['event_tags'] = \App\Models\Tag::orderBy('name')->get();
        $data['seo_details'] = (object)['title' => 'Manage Embedded Widget'];
        return view('pages.frontend.manage-embedded-widget', $data); 
    }
    public function embeddedWidgetReport()
    {
        $from_date = request()->from_date ? request()->from_date : date('Y-m-01');
        $to_date = request()->to_date ? request()->to_date : date('Y-m-d');
        $data['seo_details'] = (object)['title' => 'Embedded Widget Report'];
        $data['listing_data'] = VolunteerWidget::with(['view_logs' => function($query) use ($from_date, $to_date) {
            if(!empty($from_date)){
                $query->whereDate('created_at', '>=', $from_date);
            }
            if(!empty($to_date)){
                $query->whereDate('created_at', '<=', $to_date);
            }
        }, 'click_logs' => function($query) use ($from_date, $to_date) {
            if(!empty($from_date)){
                $query->whereDate('created_at', '>=', $from_date);
            }
            if(!empty($to_date)){
                $query->whereDate('created_at', '<=', $to_date);
            }
        }, 'order_logs' => function($query) use ($from_date, $to_date) {
            if(!empty($from_date)){
                $query->whereDate('created_at', '>=', $from_date);
            }
            if(!empty($to_date)){
                $query->whereDate('created_at', '<=', $to_date);
            }
        }])->where('volunteer_id', auth()->guard('front')->user()->id)->get();
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        return view('pages.frontend.embedded-widget-report', $data); 
    }
    public function saveWidget($id=null){
        $request = request();
        $validated = $request->validate([
            'title'          => 'required',
            'location'       => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required',
            'distance'       => 'required',
            'type'           => 'required',
            'ftr_tag_ids'          => 'nullable|array',
            'ftr_tag_ids.*'        => 'exists:tags,id',
            'ftr_event_title'      => 'nullable|string|max:255',
            'ftr_event_start_dt'   => 'nullable|date',
            'ftr_event_end_dt'     => 'nullable|date',
            'custom_widget_header' => 'nullable|string|max:255',
            'event_per_page'       => 'nullable|integer|min:1|max:100',
        ]);
        if(!empty($id)){
            $widget = VolunteerWidget::where('id', decrypt($id))->first();
            if(!isset($widget->id)){
                $this->setFlashSession(false, 'Widget not found.');
                return redirect()->route('front-embedded-widget');
            }
        } else {
            $widget = new VolunteerWidget();
            $widget->wid_number = $this->generateRandomWidgetNumber();
            $widget->volunteer_id = auth()->guard('front')->user()->id;
        }    
        $widget->fill($validated);
        $widget->location_lat = $validated['latitude'];
        $widget->location_long = $validated['longitude'];
        $widget->website = request()->website;

        $widget->org_ids = request()->filled('organizer_id')
            ? implode(',', request()->organizer_id)
            : null;

        $widget->cat_ids = request()->filled('category_id')
            ? implode(',', request()->category_id)
            : null;

        $widget->ftr_tag_ids = request()->filled('ftr_tag_ids')
            ? implode(',', request()->ftr_tag_ids)
            : null;

        $widget->ftr_event_title = request()->ftr_event_title;
        $widget->ftr_event_start_dt = request()->ftr_event_start_dt;
        $widget->ftr_event_end_dt = request()->ftr_event_end_dt;
        $widget->custom_widget_header = request()->custom_widget_header;
        $widget->event_per_page = request()->event_per_page;

        /* if(request()->has('organizer_id') && !empty(request()->organizer_id)){
            $widget->org_ids = implode(',', request()->organizer_id);
        }
        if(request()->has('category_id') && !empty(request()->category_id)){
            $widget->cat_ids = implode(',', request()->category_id);
        } */
        $widget->save();

        $this->setFlashSession(true, 'Embedded widget created successfully. You can copy the embed code from the “View Code & Details” button.');
        return redirect()->route('front-embedded-widget');
    }
    public function previewWidget($id){
        $widget = VolunteerWidget::find(decrypt($id));
        if(!$widget){
            return $this->generateJsonResponse(false, 'Widget not found.');
        }
        $html = '
        <div class="model-content main-form">
            <div class="container-fluid">
                <div class="row mt-4 mb-4">
                    <iframe width="100%" height="500" src="'.(route('front-show-widget', ['id' => encrypt($widget->id)])).'"></iframe>
                </div>
            </div>
        </div>
        ';
        return $this->generateJsonResponse(true, 'Widget found.', $html);
    }
    public function viewWidgetDetail($id){
        $widget = VolunteerWidget::find(decrypt($id));
        if(!$widget){
            return $this->generateJsonResponse(false, 'Widget not found.');
        }
        $cat_html = '';
        if(!empty($widget->cat_ids)){
            $cat_ids = explode(',', $widget->cat_ids);
            $cat_html = EventCategory::whereIn('id', $cat_ids)->pluck('name')->implode(', ');
        }
        $org_html = '';
        if(!empty($widget->org_ids)){
            // need to show name like "first_name last_name (email_address)" means we need to contact these 3 columns in one string 
            $org_ids = explode(',', $widget->org_ids);
            $vols = Volunteer::whereIn('id', $org_ids)->get();
            foreach($vols as $key => $vol){ 
                $org_html .= $vol->first_name.' '.$vol->last_name.' ('.$vol->email_address.')';
                if($key < count($vols) - 1){
                    $org_html .= ', ';
                }
            }
        }
        $html = '
        <div class="model-content main-form">
            <div class="container-fluid">
                <div class="row mt-4 mb-4">
                    <div class="col-12">
                        <h5 class=" mb-3">Widget Information:</h5>
                        <hr class="mt-0 mb-3">
                        <div class="row mb-2">
                            <div class="col-md-3 fw-semibold text-muted">Title :</div>
                            <div class="col-md-9 registration-full-name">'.$widget->title.'</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-semibold text-muted">Location :</div>
                            <div class="col-md-9 registration-email-address">'.$widget->location.'</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-semibold text-muted">Distance :</div>
                            <div class="col-md-9 registration-phone-number">'.$widget->distance.' Miles</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-semibold text-muted">How would you like the Widget to Display? :</div>
                            <div class="col-md-9 registration-event-name">'.($widget->type==1?'List View':'Map View').'</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-semibold text-muted">Embed Location :</div>
                            <div class="col-md-9 registration-event-name">'.(!empty($widget->website)?$widget->website:'-').'</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-semibold text-muted">Status :</div>
                            <div class="col-md-9 registration-event-name">'.($widget->is_active==1?'Active':'Inactive').'</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-semibold text-muted">Categories :</div>
                            <div class="col-md-9 registration-event-name">'.$cat_html.'</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3 fw-semibold text-muted">Organizers :</div>
                            <div class="col-md-9 registration-event-name">'.$org_html.'</div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-4">
                    <div class="col-12">
                        <h5>Widget Code:</h5>
                        <small class=" mb-3">Copy below code and paste it where you want to show widget.</small>
                        <hr class="mt-0 mb-3">
                        <div class="row mb-2">
                            <code id="widgetCode">
                                &lt;iframe style="width:100%; height:80vh; min-height:650px; border:0; display:block;" loading="lazy" src="'.(route('front-show-widget', ['id' => encrypt($widget->id)])).'">&lt;/iframe>
                            </code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ';
        return $this->generateJsonResponse(true, 'Widget found.', $html);
    }
    public function showWidget($id){
        $widget = VolunteerWidget::where('status',1)->where('is_active',1)->find(decrypt($id));
        $events = null;
        $location_array = null;
        if(isset($widget->id)){
            // add view log
            $this->addWidgetLog($id, 1);

            $pData = [];
            if(!empty($widget->org_ids)){
                $pData['organizer_id'] = explode(',', $widget->org_ids);
            }
            if(!empty($widget->cat_ids)){
                $pData['event_category_id'] = explode(',', $widget->cat_ids);
            }
            $pData['location'] = $widget->location;
            $pData['distance'] = $widget->distance;
            $pData['latitude'] = $widget->location_lat;
            $pData['longitude'] = $widget->location_long;
            $request = request();
            $request->merge($pData);
            if($widget->type==1){
                //$events = Event::with('category')->listactive()->where('is_private',0)->paginate(3)->withQueryString();
                $eventPerPage = !empty($widget->event_per_page) ? (int) $widget->event_per_page : 3;

                $events = Event::with('category')
                    ->listactive()
                    ->where('is_private', 0)
                    ->when(!empty($widget->ftr_tag_ids), function ($query) use ($widget) {
                        $tagIds = array_filter(explode(',', $widget->ftr_tag_ids));
                        $query->whereHas('tags', function ($tagQuery) use ($tagIds) {
                            $tagQuery->whereIn('tags.id', $tagIds);
                        });
                    })
                    ->when(!empty($widget->ftr_event_title), function ($query) use ($widget) {
                        $query->where('event_name', 'like', '%' . $widget->ftr_event_title . '%');
                    })
                    ->when(!empty($widget->ftr_event_start_dt), function ($query) use ($widget) {
                        $query->whereDate('events.start_datetime', '>=', $widget->ftr_event_start_dt);
                    })
                    ->when(!empty($widget->ftr_event_end_dt), function ($query) use ($widget) {
                        $query->whereDate('events.end_datetime', '<=', $widget->ftr_event_end_dt);
                    })
                    ->paginate($eventPerPage)
                    ->withQueryString();
            } else {
                $mapEvents = Event::listactive()
                ->where('is_private', 0)
                ->get();
                $location_array = $mapEvents->map(function ($event) use ($widget) {
                    if (!$event->latitude || !$event->longitude) 
                        return null;
                    
                    return [
                        'lat' => (float) $event->latitude,
                        'lng' => (float) $event->longitude,
                        'title' => '<b>Name:</b> '.$event->event_name.'<br/><b>Date Time:</b> '.$event->startTimeAndEndTime.'<br/><b>Address:</b> '.$event->venue.'<br/><b>Link:</b> <a href="javascript:void(0);" ajaxurl="'.route('front-add-widget-log',['widget_id'=>encrypt($widget->id),'type'=>2,'ref_id'=>$event->id]).'" url="'.route('view-event-details', ['slug' => $event->slug,'widget_id'=>encrypt($widget->id)]).'" title="'.$event->event_name.'" class="widget-event-click"><b>View Event</b></a>',
                    ];
                })->filter()->values()->all();
            }
        }
        
        $data['widget'] = $widget;
        $data['events'] = $events;
        $data['location_array'] = $location_array;
        $data['seo_details'] = (object)[
            'title' => 'Volunteer Cleanup',
            'description' => 'Browse the list of upcoming cleanups.',
            'keywords' => 'volunteer, cleanups, events, opportunities',
        ];
        return view('pages.frontend.show-widget', $data); 
    }
    public function addWidgetLog($widget_id, $type, $ref_id=null){
        // we need to prevent that if user accidently clicked the button twice and hit the ajax twice then multiple logs should not be created so update below code accordingly 
        $widget = VolunteerWidget::find(decrypt($widget_id));
        if(!$widget){
            return response()->json([
                'status' => false,
                'message' => 'Widget not found.',
            ]);
        }
        $existingLog = VolunteerWidgetLog::where('widget_id', $widget->id)
            ->where('log_type', $type)
            ->where('reference_id', $ref_id)
            ->where('created_at', '>=', now()->subSeconds(5))
            ->first();

        if($existingLog){
            return response()->json([
                'status' => true,
                'message' => 'Widget log already exists.',
            ]);
        }

        $widget->view_logs()->create([
            'widget_id' => $widget->id,
            'log_type' => $type,
            'reference_id' => $ref_id,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Widget log added successfully.',
        ]);
    }
    public function activeInactiveWidget($id){
        $widget = VolunteerWidget::find(decrypt($id));
        if(!$widget){
            abort(404, 'Widget not found.');
        }
        $widget->is_active = $widget->is_active==1 ? 0 : 1;
        $widget->save();
        $this->setFlashSession(true, 'Widget status updated successfully.');
        return redirect()->back();
    }
    public function deleteWidget($id){
        $widget = VolunteerWidget::where('id', decrypt($id))->firstOrFail();
        $res = $widget->delete();
        $widgetLog = VolunteerWidgetLog::where('widget_id', decrypt($id))->delete();
        return $this->generateJsonResponse($res);
    }
    function getOrganizerOptions(){
        $search = request()->search;
        $options = [];
        if (!empty($search)) {
            $volunteers = Volunteer::where('status', 1)
                ->where('is_organiser', 1)
                ->where(function($query) use ($search) {
                    // concat the first_name and last_name and search 
                    $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                        ->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email_address', 'like', '%' . $search . '%');
                })->get();
            if (!$volunteers->isEmpty()) {
                foreach ($volunteers as $volunteer) {
                    $options[] = [
                        'id' => $volunteer->id,
                        'text' => $volunteer->first_name . ' ' . $volunteer->last_name.' ('.$volunteer->email_address.')',
                    ];
                }
            }
        }
        return response()->json(['results' => $options]);
    }
    
    public function bookingConfirmed($order_id)
    {
        $data = [];
            $order = EventOrder::where('id', decrypt($order_id))->first();
            if(!$order){
                abort(404, 'Order not found.');
            }
            $data['order'] = $order;
            $data['event'] = $order->event; 
            $data['ticket'] = $order->ticket;
        
    
        $data['seo_details'] = (object)['title' => 'Booking Confirmed'];
        return view('pages.frontend.booking-confirmed', $data);
    }
    
    public function rsvpWaiverConfirmed($id)
    {
        $data = [];
        $orderTicket = EventOrderTicket::findOrFail(decrypt($id));
        $event = $orderTicket->event;
        if(!$id || !$event){
            abort(404, 'Event not found.');
        }
        $data['event'] = $event; 
        $data['ticket'] = $orderTicket;
        $data['seo_details'] = (object)['title' => 'RSVP Confirmation Form'];
        return view('pages.frontend.rsvp-confirmed', $data);
    }
    
    public function organizerDetail($slug = null)
    {
        if(!$slug){
            abort(404, 'Organizer not found.');
        }

        $organiser = Volunteer::where('organisation_landing_slug', $slug)->first();
        if(!isset($organiser->id)){
            abort(404, 'Organizer not found.');
        }
        $seo_details = (object)[
                            'title' => "Organizer ".$organiser->name, 
                            'description' => $organiser->orgranisation_short_description,                
                        ];
        //$past_events = Event::where('creator_id', $organiser->id)->past()->get();
        $past_events = Event::with('category')->where('creator_id', $organiser->id)->where('end_datetime', '<', now())
            ->orderBy('start_datetime', 'desc') 
            ->orderBy('end_datetime', 'desc')
            ->orderBy('site_publish_date', 'asc')
            ->orderBy('id', 'asc')->paginate(20, ['*'], 'past_page')->withQueryString()->appends(['tab' => 'past_event']);
        $present_events = Event::with('category')->where('creator_id', $organiser->id)->present()
            ->orderBy('start_datetime', 'asc') 
            ->orderBy('end_datetime', 'asc')
            ->orderBy('site_publish_date', 'desc')
            ->orderBy('id', 'desc')->paginate(20, ['*'], 'ongoing_page')->withQueryString()->appends(['tab' => 'ongoing_event']);
        $future_events = Event::with('category')->where('creator_id', $organiser->id)->future()
            ->orderBy('start_datetime', 'asc') 
            ->orderBy('end_datetime', 'asc')
            ->orderBy('site_publish_date', 'desc')
            ->orderBy('id', 'desc')->paginate(20, ['*'], 'upcoming_page')->withQueryString()->appends(['tab' => 'upcoming_event']);
        return view('pages.frontend.organizer-detail' , [
                            'organiser' => $organiser, 
                            'seo_details' => $seo_details, 
                            'past_events' => $past_events,
                            'present_events' => $present_events,
                            'future_events' => $future_events
        ]);
    }

    public function saveOrganizerLead(){
        $request = request();

        $validated = $request->validate([
            'organiser_id' => 'required|exists:volunteers,id',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:20',
            'message'      => 'required|string',
        ]);

        $validated['phone'] = formatPhoneNumber($validated['phone']);
        \App\Models\OrganiserLead::create($validated);

        $organiser = Volunteer::find($validated['organiser_id']);
        $mail_content = [
            'org_name'         => $organiser->first_name.' '.$organiser->last_name,
            'c_volunteer'         => $validated['name'],
            'c_email'             => $validated['email'],
            'c_phone'             => $validated['phone'],
            'c_message'             => $validated['message']
        ];
        
        $this->sendEmail($organiser->email_address, CONTACT_ORG_MAIL_SUB, $mail_content, "contact-organizer");
        
        $this->setFlashSession(true, "Thank you for contacting the organizer. We will get back to you soon.");
        return redirect()->back();
    }
    public function waiverForm($ticket){
        $request = request();
        
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
                'name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
            ]);
            
            // Process waiver form submission
            $this->setFlashSession(true, 'Waiver form submitted successfully.');
            return redirect()->back();
        }
        
        // Display waiver form
        return view('pages.frontend.waiver-form', [
            'ticket' => $ticket,
            'seo_details' => (object)['title' => 'Waiver Form']
        ]);
    }

    public function generateIcs($slug)
    {
        // Create a new calendar
        $event  = Event::where('slug' , $slug)->firstOrFail();
        $eventName = $event->event_name;
        $timezoneMap = [
                    'eastern' => 'America/New_York',
                    'central' => 'America/Chicago',
                    'mountain' => 'America/Denver',
                    'pacific' => 'America/Los_Angeles',
                ];
        $ianaTimezone = $timezoneMap[strtolower($event->event_timezone)] ?? 'America/New_York';
        $startDateTime = new \DateTime($event->start_datetime, new \DateTimeZone($ianaTimezone));
        $endDateTime = new \DateTime($event->end_datetime, new \DateTimeZone($ianaTimezone));
        
        $startDate   = $startDateTime->format('Ymd\THis');
        $endDate     = $endDateTime->format('Ymd\THis');
        $description = $event->event_name;
        $location    = $event->venue;
        $filename    = str()->slug($event->event_name, '_');

        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//YourApp//EN\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "UID:" . md5(uniqid()) . "\r\n";
        $icsContent .= "DTSTART:$startDate\r\n";
        $icsContent .= "DTEND:$endDate\r\n";
        $icsContent .= "SUMMARY:$eventName\r\n";
        $icsContent .= "DESCRIPTION:$description\r\n";
        $icsContent .= "LOCATION:$location\r\n";
        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR\r\n";

        $headers = [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'.ics"',
        ];

        return new Response($icsContent, 200, $headers);
    }
    public function getFocusKeywordsFront(){
        request()->validate([
            'meta_title' => 'required',
            'meta_description' => 'required',
        ]); 
        $meta_title = request()->meta_title;
        $meta_description = request()->meta_description;
        $content = $this->getAiFocusKeyword($meta_title,$meta_description);
        return $this->generateJsonResponse(true, '', [
            'keywords' => $content,
        ]);
    }
    public function getEventMetaData(){
        $request = request();
        $event = [];
        $event['name'] = $request['event_name'];
        $event['description'] = $request['description'];
        $event['location'] = $request['venue'].' '.$request['pincode'];
        $event['start_date'] = $request['start_datetime'];
        $event['end_date'] = $request['end_datetime'];
        $content = $this->getEventMetaDataByAi($event);
        return $this->generateJsonResponse(true, '', json_decode($content));
    }
    public function sendCustomEmailAttendee($id)
    {
        request()->validate([
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        $ticket = EventOrderTicket::with(['volunteer', 'event.creator'])->findOrFail(decrypt($id));

        if (!$ticket->event || $ticket->event->creator_id != auth()->guard('front')->user()->id) {
            abort(403);
        }

        if (!$ticket->volunteer || empty(trim($ticket->volunteer->email_address))) {
            $this->setFlashSession(false, 'Attendee email address is not available.');
            return redirect()->back();
        }

        $templateId = 'd-32906ded89e44bacbaa331f49cef43f0';

        $to_arr[] = [
            "to" => [
                ["email" => trim($ticket->volunteer->email_address)]
            ],
            "dynamic_template_data" => [
                "subject" => request()->subject,
                "content" => request()->content,
                "attendee_name" => $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name,
                "attendee_email" => $ticket->volunteer->email_address,
            ],
        ];

        $this->sendSendgridTemplateEmailWithoutLog($to_arr, $templateId);

        $this->setFlashSession(true, 'Email sent successfully.');
        return redirect()->back();
    }
    public function sendCustomEmailEventAttendees($id)
    {
        request()->validate([
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $event = Event::with('creator')->findOrFail(decrypt($id));

        if ($event->creator_id != auth()->guard('front')->user()->id) {
            abort(403);
        }

        $tickets = $event->tickets()->with('volunteer')->where('rsvp_confirmation', 1)->get();

        if ($tickets->count() == 0) {
            $this->setFlashSession(false, 'No RSVP confirmed attendees found.');
            return redirect()->back();
        }

        $templateId = 'd-32906ded89e44bacbaa331f49cef43f0';

        $to_arr = [];
        $sentEmails = [];

        foreach ($tickets as $ticket) {
            if (!$ticket->volunteer || empty(trim($ticket->volunteer->email_address))) {
                continue;
            }

            $email = trim($ticket->volunteer->email_address);

            if (in_array($email, $sentEmails)) {
                continue;
            }

            $to_arr[] = [
                "to" => [
                    ["email" => $email]
                ],
                "dynamic_template_data" => [
                    "subject" => request()->subject,
                    "content" => request()->content,
                    "attendee_name" => $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name,
                    "attendee_email" => $email,
                ],
            ];

            $sentEmails[] = $email;
        }

        if (empty($to_arr)) {
            $this->setFlashSession(false, 'No attendee email address found.');
            return redirect()->back();
        }

        foreach (array_chunk($to_arr, 300) as $chunk) {
            $this->sendSendgridTemplateEmailWithoutLog($chunk, $templateId);
        }

        $this->setFlashSession(true, 'Email sent successfully to all RSVP confirmed attendees.');
        return redirect()->back();
    }
    /* public function resendWaiver($ticket){
        $ticket = EventOrderTicket::find(decrypt($ticket));
        if(!$ticket){
            return $this->generateJsonResponse(false, 'Ticket not found.');
        }
        if($ticket->waiver_filled_at){
            return $this->generateJsonResponse(false, 'Waiver has already been filled for this ticket.');
        }
        $volunteer = $ticket->volunteer;
        if(!isset($volunteer->id)){
            return $this->generateJsonResponse(false, 'Volunteer not found.');
        }
        $event = $ticket->event;
        if(!$event){
            return $this->generateJsonResponse(false, 'Event not found for this ticket.');
        }
        // send waiver email to volunteer
        $email = $ticket->volunteer->email_address;
        if(empty(trim($email))){
            return $this->generateJsonResponse(false, 'Volunteer email address is not available.');
        }
        $to_arr = [];
        if($ticket->is_under_18==1){
            $email = $ticket->waiver_handler_email;
            $name = $ticket->waiver_handler_name;
        } else {
            $email = $volunteer->email_address;
            $name = $volunteer->first_name.' '.$volunteer->last_name;
        }
        if(!empty(trim($email))){
            $subject = "Action Required : Sign Waiver for ".$event->event_name;
            if($ticket->is_under_18==1){
                $subject = "Action Required: ".$name.", Please Sign Waiver for ".$volunteer->first_name." – ".$event->event_name;
            }
            $to_arr[] = 
            [
                "to" => [
                    ["email" => trim($email)]
                    //["email" => 'learningdcm@gmail.com']
                ],
                "dynamic_template_data" => [
                    "subject" => $subject,
                    "is_under_18" => $ticket->is_under_18 == 1 ? true : false,
                    "volunteer_name" => $volunteer->first_name.' '.$volunteer->last_name,
                    "volunteer_email" => $volunteer->email_address,
                    "guardian_name" => $ticket->waiver_handler_name,
                    "guardian_email" => $ticket->waiver_handler_email,
                    "event_name" =>  $event->event_name,
                    'event_date' => $event->startTimeAndEndTime,
                    'event_location' => $event->fullAddress,
                    'organizer_name' => $event->creator->first_name.' '.$event->creator->last_name,
                    'waiver_link' => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($ticket->id)]),
                    'event_link' => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                ],
            ];
        }
        if(!empty($to_arr)){
            $this->sendSendgridTemplateEmailWithoutLog($to_arr,"d-1ef71dffbc644dce914ab333526c922d");
            return $this->generateJsonResponse(true, 'Waiver email sent successfully.');
        }
        return $this->generateJsonResponse(false, 'Failed to send waiver email.');
    } */
    public function resendWaiver(Request $request, $ticket, $callByAdmin=false)
    {
        $ticket = EventOrderTicket::find(decrypt($ticket));
        if (!$ticket) {
            return $this->generateJsonResponse(false, 'Ticket not found.');
        }
        if ($ticket->waiver_filled_at) {
            return $this->generateJsonResponse(false, 'Waiver has already been filled for this ticket.');
        }
        $volunteer = $ticket->volunteer;
        if (!isset($volunteer->id)) {
            return $this->generateJsonResponse(false, 'Volunteer not found.');
        }
        $event = $ticket->event;
        if (!$event) {
            return $this->generateJsonResponse(false, 'Event not found for this ticket.');
        }

        // Determine send type from request
        $resendWaiverType = $request->input('resend_waiver_type', 1); // 1 = original, 2 = alternative
        $alternativeEmail = $request->input('alternative_waiver_email');

        // Validate alternative email if type is 2
        if ($resendWaiverType == 2) {
            if (empty(trim($alternativeEmail))) {
                return $this->generateJsonResponse(false, 'Please enter alternative email address.');
            }
            if (!filter_var(trim($alternativeEmail), FILTER_VALIDATE_EMAIL)) {
                return $this->generateJsonResponse(false, 'Please enter a valid alternative email address.');
            }
        }

        // Determine original recipient based on age
        if ($ticket->is_under_18 == 1) {
            $originalEmail = $ticket->waiver_handler_email;
            $name          = $ticket->waiver_handler_name;
        } else {
            $originalEmail = $volunteer->email_address;
            $name          = $volunteer->first_name . ' ' . $volunteer->last_name;
        }

        // Decide which email to actually send to
        if ($resendWaiverType == 2) {
            $sendToEmail    = trim($alternativeEmail);
            $linkSendType   = 2; // alternative
        } else {
            $sendToEmail    = $originalEmail;
            $linkSendType   = 1; // original
        }

        if (empty(trim($sendToEmail))) {
            return $this->generateJsonResponse(false, 'Email address is not available.');
        }

        $subject = "Action Required : Sign Waiver for " . $event->event_name;
        if ($ticket->is_under_18 == 1) {
            $subject = "Action Required: " . $name . ", Please Sign Waiver for " . $volunteer->first_name . " – " . $event->event_name;
        }

        $to_arr = [
            [
                "to" => [
                    ["email" => $sendToEmail]
                ],
                "dynamic_template_data" => [
                    "subject"          => $subject,
                    "is_under_18"      => $ticket->is_under_18 == 1 ? true : false,
                    "volunteer_name"   => $volunteer->first_name . ' ' . $volunteer->last_name,
                    "volunteer_email"  => $volunteer->email_address,
                    "guardian_name"    => $ticket->waiver_handler_name,
                    "guardian_email"   => $ticket->waiver_handler_email,
                    "event_name"       => $event->event_name,
                    'event_date'       => $event->startTimeAndEndTime,
                    'event_location'   => $event->fullAddress,
                    'organizer_name'   => $event->creator->first_name . ' ' . $event->creator->last_name,
                    'waiver_link'      => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($ticket->id)]),
                    'event_link'       => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                ],
            ]
        ];

        $sent = $this->sendSendgridTemplateEmailWithoutLog($to_arr, "d-1ef71dffbc644dce914ab333526c922d");

        // Save log after successful send
        $log = TicketResendWaiverLog::create([
            'ticket_id'         => $ticket->id,
            'link_send_type'    => $linkSendType,   // 1 = original, 2 = alternative
            'link_send_to_email'=> $sendToEmail,
            'link_send_by'      => $callByAdmin?auth()->user()->id:auth()->guard('front')->user()->id,
        ]);

        return $this->generateJsonResponse(true, 'Waiver email sent successfully.', $log);
    }
    public function checkResendWaiver($ticket){
        $registration = EventOrderTicket::find(decrypt($ticket));
        if(isset($registration->id)){
            $responseArr = [];
            $responseArr['log'] = [];
            $responseArr['max_resend_limit_exceed'] = false;
            $responseArr['wait_before_resend'] = false;
            $totalResendCount = $registration->resendWaiverLogs()->count();
            // Check if max limit exceeded (5 times)
            if($totalResendCount >= 5){
                $responseArr['max_resend_limit_exceed'] = true;
                return $this->generateJsonResponse(false, 'Maximum resend limit exceeded. You cannot resend the waiver email more than 5 times.', $responseArr);
            }

            $lastResendWaiverLog = $registration->resendWaiverLogs()->latest('created_at')->first();
            if(isset($lastResendWaiverLog->id)){
                // Check 60 second gap
                $secondsSinceLastSend = now()->diffInSeconds(\Carbon\Carbon::parse($lastResendWaiverLog->createdDateTimeStr));
                if($secondsSinceLastSend < 60){
                    $responseArr['wait_before_resend'] = true;
                    $responseArr['wait_seconds'] = 60 - $secondsSinceLastSend;
                    return $this->generateJsonResponse(false, 'Please wait ' . (60 - $secondsSinceLastSend) . ' seconds before resending the waiver email.', $responseArr);
                }
                $lastResendWaiverLog->createdDateTime = $lastResendWaiverLog->createdDateTime;
                $responseArr['log'] = $lastResendWaiverLog->toArray();
            }
            return $this->generateJsonResponse(true, 'Waiver email sent successfully.', $responseArr);
        }
        return $this->generateJsonResponse(false, 'No registration found.');
    }
    public function markBulkCheckinWithEmail($event_id){
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $event = Event::with('creator')->findOrFail(decrypt($event_id));
        if($event->is_completed==1){
            return $this->generateJsonResponse(false, 'You cannot check-in an attendee for a completed event.');
        }
        $tickets = $event->tickets()->where('rsvp_confirmation', 1)->whereNotNull('waiver_filled_at')->where('checkin',0)->get();
        if($tickets->count() == 0){
            return $this->generateJsonResponse(false, 'No waiver-signed volunteers found to mark as checked-in.');
        }
        if($tickets->isNotEmpty()){
            $to_arr = [];
            $log_arr = [];
            foreach ($tickets as $key => $value) {
                if($value->community_form_pdf){
                    $community_form = $value->community_form_pdf; 
                }else{
                    $data = [
                        'event'            => $event,
                        'volunteerName'    => $value->volunteer->first_name.' '.$value->volunteer->last_name,
                        //'hours'            => $value->hours_completed,
                        'hours'            => $event->volunteer_hours,
                        'organization_name'   => $event->creator->organisation_name,
                        'organizer_name'   => $event->creator->first_name.' '.$event->creator->last_name,
                        'organization_email'   => $event->creator->email_address,
                        'organizer_link'   => route('organizer-detail', ['slug' => $event->creator->organisation_landing_slug]),
                    ];
                    try{
                        $html = view('pages.events.community-form-new', $data)->render();
                        $pdf = \PDF::setOptions(['isHtml5ParserEnabled' => true,'isRemoteEnabled' => true])->loadHTML($html);
                        $filename = 'community_form_' . $value->id . '_' . time() . '.pdf';
                        $path = 'events/community_forms/' . $filename;
                        $publicPath = public_path($path);
                        if (!file_exists(dirname($publicPath))) {
                            mkdir(dirname($publicPath), 0755, true);
                        }
                        file_put_contents($publicPath, $pdf->output());
                        $value->update([
                            'community_form_pdf' => $path,
                        ]);
                        $community_form = url('/').'/'.$path;
                    }catch(\Exception $e){
                        return $this->generateJsonResponse(false, "Issues occured while generating PDF",[
                            'error' => $e->getMessage(),
                            'line'  => $e->getLine(),
                            'file'  => $e->getFile(),
                            // 'trace' => $e->getTraceAsString() // uncomment if needed
                        ]);
                    }
                }
                $value->update([
                    'checkin' => 1,
                    'checkin_dt' => date('Y-m-d H:i:s')
                ]);
                /* $email = $value->volunteer->email_address;
                $name = $value->volunteer->first_name.' '.$value->volunteer->last_name;
                if(!empty(trim($email))){
                    $to_arr[] = 
                    [
                        "to" => [
                            ["email" => trim($email)]
                            //["email" => 'learningdcm@gmail.com']
                        ],
                        "dynamic_template_data" => [
                            "volunteer_name" => $name,
                            "volunteer_email" => $email,
                            "event_name" =>  $event->event_name,
                            'event_date' => $event->startTimeAndEndTime,
                            'event_location' => $event->fullAddress,
                            'event_link' => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                        ],
                    ];
                    $log_arr[] = [
                        "id" => $value->id,
                        "email" => trim($email)
                    ];
                } */
            }
            /* if(!empty($to_arr) && count($to_arr)>0){
                $to_arr_child = array_chunk($to_arr, 300);
                $log_arr_child = array_chunk($log_arr, 300);
                foreach ($to_arr_child as $key => $value) {
                    $this->sendSendgridTemplateEmailWithoutLog($value,"d-3badeebd9cfe40458f55935dd25aa41a");
                }
            } */
            return $this->generateJsonResponse(true, $tickets->count().' ' . ' volunteer(s) have been successfully checked-in.');
        }
    }
    public function sendWaiverBulkEmail($id){
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $event = Event::with('creator')->findOrFail(decrypt($id));
        if($event->is_completed==1){
            return $this->generateJsonResponse(false, 'Waiver email cannot be sent for a completed event.');
        }
        $tickets = $event->tickets()->where('rsvp_confirmation', 1)->whereNull('waiver_filled_at')->get();
        if($tickets->count() == 0){
            return $this->generateJsonResponse(false, 'No volunteer found to send waiver email.');
        }
        if($tickets->isNotEmpty()){
            $to_arr = [];
            $log_arr = [];
            foreach ($tickets as $key => $value) {
                if(!isset($value->volunteer)){
                    continue;
                } else if(!isset($value->volunteer->id)){
                    continue;
                }
                if($value->is_under_18==1){
                    $email = $value->waiver_handler_email;
                    $name = $value->waiver_handler_name;
                } else {
                    $email = $value->volunteer->email_address;
                    $name = $value->volunteer->first_name.' '.$value->volunteer->last_name;
                }
                if(!empty(trim($email))){
                    $subject = "Action Required : Sign Waiver for ".$event->event_name;
                    if($value->is_under_18==1){
                        $subject = "Action Required: ".$name.", Please Sign Waiver for ".$value->volunteer->first_name." – ".$event->event_name;
                    }
                    $to_arr[] = 
                    [
                        "to" => [
                            ["email" => trim($email)]
                            //["email" => 'learningdcm@gmail.com']
                        ],
                        "dynamic_template_data" => [
                            "subject" => $subject,
                            "is_under_18" => $value->is_under_18 == 1 ? true : false,
                            "volunteer_name" => $value->volunteer->first_name.' '.$value->volunteer->last_name,
                            "volunteer_email" => $value->volunteer->email_address,
                            "guardian_name" => $value->waiver_handler_name,
                            "guardian_email" => $value->waiver_handler_email,
                            "event_name" =>  $event->event_name,
                            'event_date' => $event->startTimeAndEndTime,
                            'event_location' => $event->fullAddress,
                            'organizer_name' => $event->creator->first_name.' '.$event->creator->last_name,
                            'waiver_link' => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($value->id)]),
                            'event_link' => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                        ],
                    ];
                    $log_arr[] = [
                        "id" => $value->id,
                        "email" => trim($email)
                    ];
                }
            }
            if(!empty($to_arr) && count($to_arr)>0){
                $to_arr_child = array_chunk($to_arr, 300);
                $log_arr_child = array_chunk($log_arr, 300);
                foreach ($to_arr_child as $key => $value) {
                    $this->sendSendgridTemplateEmailWithoutLog($value,"d-1ef71dffbc644dce914ab333526c922d");
                }
            }
            return $this->generateJsonResponse(true, 'Waiver emails sent successfully to all attendees.');
        }
    }
    public function ticketConvertToDonation($id){
        $orderTicket = EventOrderTicket::find(decrypt($id));
        if(isset($orderTicket->id) && isset($orderTicket->eventOrder)){
            $order = $orderTicket->eventOrder;
            $total_donation_amount = !empty($order->donation_amount)?$order->donation_amount:0;
            $orderTicket->update(['is_amt_donated'=>1,'rsvp_confirmation'=>0]);
            $total_donation_amount += $orderTicket->price; 
            $order->donation_amount = $total_donation_amount;
            $order->save();
            $email = $orderTicket->volunteer->email_address;
            if($orderTicket->is_under_18==1){
                $email = $orderTicket->waiver_handler_email;
            }
            if(!empty(trim($email))){
                $event = $orderTicket->event;
                $subject = "Thank you for donating your ticket amount for ".$event->event_name;
                $to_arr = [];
                $to_arr[] = 
                [
                    "to" => [
                        ["email" => trim($email)]
                        //["email" => 'learningdcm@gmail.com']
                    ],
                    "dynamic_template_data" => [
                        "subject" => $subject,
                        "is_under_18" => $orderTicket->is_under_18 == 1 ? true : false,
                        "volunteer_name" => $orderTicket->volunteer->first_name.' '.$orderTicket->volunteer->last_name,
                        "volunteer_email" => $orderTicket->volunteer->email_address,
                        "guardian_name" => $orderTicket->waiver_handler_name,
                        "guardian_email" => $orderTicket->waiver_handler_email,
                        "event_name" =>  $event->event_name,
                        'event_date' => $event->startTimeAndEndTime,
                        'event_location' => $event->fullAddress,
                        'organizer_name' => $event->creator->first_name.' '.$event->creator->last_name,
                    ],
                ];
                // need to change the template id for this email 
                //$this->sendSendgridTemplateEmailWithoutLog($to_arr,"d-1ef71dffbc644dce914ab333526c922d");
            }
            $this->setFlashSession(true, "Ticket amount has been donated successfully.");
            return redirect()->back();
        }
        $this->setFlashSession(true, "Ticket not found.");
        return redirect()->back();
    }
    public function cancelEvent($id){
        $event = Event::find(decrypt($id));
        if(!$event){
            abort(404, 'Event not found.');
        }
        if($event->is_completed==1){
            $this->setFlashSession(false, 'Completed events cannot be cancelled.');
            return redirect()->back();
        }
        if(strtotime($event->end_datetime) < strtotime(now())){
            $this->setFlashSession(false, 'Event cannot be cancelled after it has ended.');
            return redirect()->back();
        }
        $event->update([
            'cancelled_at' => date('Y-m-d H:i:s'),
        ]);
        // update the tickets status for cancellation
        $event->tickets()
        ->where('rsvp_confirmation', 1)
        ->where('price', '>', 0)
        ->update([
            'rsvp_confirmation' => 0,
            'cancelled_at' => date('Y-m-d H:i:s'),
            'refund_status'       => 1,
            'cancel_ticket_error' => null,
        ]);
        $event->tickets()
        ->where('rsvp_confirmation', 1)
        ->where('price', '<=', 0)
        ->update([
            'rsvp_confirmation' => 0,
            'cancelled_at' => date('Y-m-d H:i:s'),
            'refund_status'       => 4,
            'cancel_ticket_error' => null,
        ]);
        $this->setFlashSession(true, 'Event cancelled successfully.');
        return redirect()->back();
    }
    public function cronSendEventCancelEmail(){        
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        /* $tickets = EventOrderTicket::with([
                'volunteer:id,first_name,last_name,email_address',
                'event:id,event_name,start_datetime,end_datetime,venue,pincode,creator_id,slug',
                'event.creator:id,first_name,last_name,email_address',
            ])
            ->where('rsvp_confirmation', 0)
            ->where('is_cancel_email_sent', 0)
            ->whereHas('event', function ($q) {
                $q->whereNotNull('cancelled_at');
            })
            ->whereHas('volunteer', function ($q) {
                $q->whereNotNull('email_address')->where('email_address', '!=', '');
            })
            ->limit(500)
            ->get(); */
        $tickets = EventOrderTicket::with([
            'volunteer:id,first_name,last_name,email_address',
            'event:id,event_name,start_datetime,end_datetime,venue,pincode,creator_id,slug,cancelled_at',
            'event.creator:id,first_name,last_name,email_address',
        ])
        ->join('events', 'events.id', '=', 'event_order_tickets.event_id')
        ->select('event_order_tickets.*')
        ->where('event_order_tickets.rsvp_confirmation', 0)
        ->where('event_order_tickets.is_cancel_email_sent', 0)
        ->whereHas('event', function ($q) {
            $q->whereNotNull('cancelled_at');
        })
        ->whereHas('volunteer', function ($q) {
            $q->whereNotNull('email_address')->where('email_address', '!=', '');
        })
        ->orderBy('events.cancelled_at', 'asc')
        ->limit(500)
        ->get();

        if ($tickets->isEmpty()) {
            return false;
        }

        $to_arr  = [];
        $log_arr = [];
        $updateIds = [];

        foreach ($tickets as $ticket) {
            if (!isset($ticket->volunteer) || !isset($ticket->volunteer->id)) {
                continue;
            }

            if ($ticket->is_under_18 == 1) {
                $email = $ticket->waiver_handler_email;
                $name  = $ticket->waiver_handler_name;
            } else {
                $email = $ticket->volunteer->email_address;
                $name  = $ticket->volunteer->first_name . ' ' . $ticket->volunteer->last_name;
            }

            if (empty(trim($email))) {
                continue;
            }

            $to_arr[] = [
                "to" => [
                    ["email" => trim($email)],
                ],
                "dynamic_template_data" => [
                    "is_under_18" => $ticket->is_under_18 == 1 ? true : false,
                    "volunteer_name" => $ticket->volunteer->first_name.' '.$ticket->volunteer->last_name,
                    "volunteer_email" => $ticket->volunteer->email_address,
                    "guardian_name" => $ticket->waiver_handler_name,
                    "guardian_email" => $ticket->waiver_handler_email,
                    "event_name" =>  $ticket->event->event_name,
                    'event_date' => $ticket->event->startTimeAndEndTime,
                    'event_location' => $ticket->event->fullAddress,
                    'organizer_name' => $ticket->event->creator->first_name.' '.$ticket->event->creator->last_name,
                    "organizer_email" => $ticket->event->creator->email_address,
                    'is_free_ticket' => $ticket->price>0?false:true,
                    'convert_to_donation_link' => $this->mailUrl('front-cleanup-attending'),
                ],
            ];

            $log_arr[] = [
                "id"       => $ticket->id,
                "email"    => trim($email),
                "event_id" => $ticket->event_id,
            ];

            $updateIds[] = $ticket->id;
        }

        if (!empty($to_arr) && count($to_arr) > 0) {
            $this->runCronSendEventCancelEmail($to_arr, $log_arr, $updateIds);
        }
    }
    public function runCronSendEventCancelEmail($to_arr,$log_arr,$updateIds){
        $jayParsedAry = [
            "personalizations" => $to_arr, 
            "from" => [
                        "name" => "VolunteerCleanup.Org" ,
                        "email" => "outreach@volunteercleanup.org" 
                    ], 
            "template_id" => "d-90ce664df7e04903aca3bf79264b7db8" 
        ]; 

        // Convert the post data to a string
        $postDataString = http_build_query($jayParsedAry);
        // Calculate the size of the post data in bytes
        $postDataSizeBytes = strlen($postDataString);
        // Convert bytes to megabytes
        $postDataSizeMB = round($postDataSizeBytes / (1024 * 1024),2);

        $response = $this->SendSendgridEmail('','','',$jayParsedAry);

        //print_r($response);
        $is_success = 1;
        $response_code = 202;
        if(isset($response) && !empty($response)){
            if(isset($response['response']) && !empty($response['response'])){
                if(isset($response['response']['code']) && !empty($response['response']['code']) && ($response['response']['code']==202 || $response['response']['code']==200)){
                    $is_success = 1;
                    $response_code = $response['response']['code'];
                    //echo 'SUCCESS '.$resUnjson['response']['code'];
                }
                else{
                    $is_success = 0;
                    $response_code = $response['response']['code'];
                    //echo 'FAILED '.$resUnjson['response']['code'];
                }
            }
            else{
                //echo 'FAILED';
            }
        }
        else{
            //echo 'FAILED';
        }
        if (!empty($updateIds)) {
            EventOrderTicket::whereIn('id', $updateIds)->update(['is_cancel_email_sent' => 1]);
        }
    }
    public function cronCancelRefundEventTickets(){
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        // Fetch 25 distinct order_ids from event_order_tickets
        // where refund_status = 1 (not initiated) and ticket is cancelled
        // and the event is cancelled
        // 25 orders × 4 Stripe API calls × ~1.5s = ~37 seconds safely under limit
        // grouped by order_id so we process one Stripe call per order
        $orderIds = EventOrderTicket::join('events', 'events.id', '=', 'event_order_tickets.event_id')
            ->join('event_orders', 'event_orders.id', '=', 'event_order_tickets.order_id')
            ->select('event_order_tickets.order_id')
            ->where('event_order_tickets.refund_status', 1)    // 1 = not initiated
            ->where('event_order_tickets.is_amt_donated', 0) // skip donated tickets
            ->where('event_order_tickets.rsvp_confirmation',0)    // ticket must be cancelled
            //->where('event_orders.payment_status', 'paid')     // only paid orders
            ->whereNotNull('event_order_tickets.cancelled_at') // ticket must be cancelled
            ->whereNotNull('events.cancelled_at')              // event must be cancelled
            ->where('events.cancelled_at', '<=', now()->subHours(48))               // 48 hours must have passed
            ->orderBy('events.cancelled_at', 'asc')
            ->orderBy('event_order_tickets.order_id', 'asc')
            ->groupBy('event_order_tickets.order_id')
            ->limit(25)
            ->pluck('event_order_tickets.order_id')
            ->toArray();

        if (empty($orderIds)) {
            return response()->json($this->generateResponse(true, 'No pending orders found.'));
        }

        foreach ($orderIds as $orderId) {

            $order = EventOrder::find($orderId);
            if (!$order) {
                continue;
            }

            // Mark only refund_status = 1 tickets of this order as initiated
            // before hitting Stripe so if cron overlaps next run
            // same order will not be picked again
            // refund_status = 2 means initiated
            EventOrderTicket::where('order_id', $orderId)
                ->where('refund_status', 1)
                ->where('is_amt_donated', 0)
                ->where('rsvp_confirmation', 0)
                ->whereNotNull('cancelled_at')
                ->update([
                    'refund_status'       => 2,
                    'cancel_ticket_error' => null,
                ]);

            // Fetch ONLY tickets marked as initiated by above update (refund_status = 2)
            // Tickets already cancelled before event cancellation
            // will have refund_status = 3 or 4 so they will NOT be fetched here
            // This ensures we only refund amount of remaining active tickets
            $tickets = EventOrderTicket::where('order_id', $orderId)
                ->where('refund_status', 2)
                ->where('is_amt_donated', 0)
                ->where('rsvp_confirmation', 0)
                ->whereNotNull('cancelled_at')
                ->get();

            if ($tickets->isEmpty()) {
                continue;
            }

            // Sum ONLY the remaining tickets amount
            // Previously cancelled tickets are excluded automatically
            // so this total is correct for partial refund on the order
            $total_amount = $tickets->sum('price');
            $total_fee    = $tickets->sum('ticket_platform_fee');

            try {
                if ($total_amount <= 0) {
                    // Free tickets — no Stripe call needed
                    // refund_status = 4 means taken by organizer
                    EventOrderTicket::where('order_id', $orderId)
                        ->where('refund_status', 2)
                        ->where('is_amt_donated', 0)
                        ->where('rsvp_confirmation', 0)
                        ->whereNotNull('cancelled_at')
                        ->update([
                            'refund_status'       => 4,
                            'cancel_ticket_error' => null,
                        ]);

                } else {
                    // Paid tickets — hit Stripe once for remaining tickets of this order
                    // Stripe supports multiple partial refunds on same payment_intent
                    // so even if some tickets were already refunded individually before,
                    // this will correctly refund only the remaining amount
                    $stripeCon = new StripeController();
                    $res = $stripeCon->refundCharge(
                        $order->id,
                        $order->payment_intent,
                        $total_amount,
                        $total_fee
                    );

                    if ($res['status'] == true) {
                        // refund_status = 3 means refunded successfully
                        EventOrderTicket::where('order_id', $orderId)
                            ->where('refund_status', 2)
                            ->where('is_amt_donated', 0)
                            ->where('rsvp_confirmation', 0)
                            ->whereNotNull('cancelled_at')
                            ->update([
                                'refund_status'       => 3,
                                'cancel_ticket_error' => null,
                            ]);

                    } else {
                        // refund_status = 5 means Stripe call failed
                        // save the error response in cancel_ticket_error for manual review
                        EventOrderTicket::where('order_id', $orderId)
                            ->where('refund_status', 2)
                            ->where('is_amt_donated', 0)
                            ->where('rsvp_confirmation', 0)
                            ->whereNotNull('cancelled_at')
                            ->update([
                                'refund_status'       => 5,
                                'cancel_ticket_error' => json_encode([
                                    'type'            => 'stripe_refund_failed',
                                    'order_id'        => $order->id,
                                    'payment_intent'  => $order->payment_intent,
                                    'total_amount'    => $total_amount,
                                    'total_fee'       => $total_fee,
                                    'stripe_response' => $res,
                                    'failed_at'       => date('Y-m-d H:i:s'),
                                ]),
                            ]);
                    }
                }
            } catch (\Exception $e) {
                // refund_status = 5 means failed — needs manual review
                // save full exception details in cancel_ticket_error
                EventOrderTicket::where('order_id', $orderId)
                    ->where('refund_status', 2)
                    ->where('is_amt_donated', 0)
                    ->where('rsvp_confirmation', 0)
                    ->whereNotNull('cancelled_at')
                    ->update([
                        'refund_status'       => 5,
                        'cancel_ticket_error' => json_encode([
                            'type'           => 'exception',
                            'order_id'       => $order->id,
                            'payment_intent' => $order->payment_intent,
                            'total_amount'   => $total_amount,
                            'total_fee'      => $total_fee,
                            'error'          => $e->getMessage(),
                            'line'           => $e->getLine(),
                            'file'           => $e->getFile(),
                            'failed_at'      => date('Y-m-d H:i:s'),
                        ]),
                    ]);
            }
        }

        return response()->json($this->generateResponse(true, 'Refund cron executed successfully.'));
    }
    public function rescheduleEvent($id){
        $event = Event::find(decrypt($id));
        if(!$event){
            abort(404, 'Event not found.');
        }
        $data['event'] = $event;
        $data['seo_details'] = (object)['title' => 'Reschedule Event'];
        return view('pages.frontend.reschedule-event', $data);
    }
    public function submitRescheduleEventForm(Request $request, $id, $callFrom = null){
        $eventId = decrypt($id);
        $event = Event::with('eventTickets')->findOrFail($eventId);
        if($event->is_completed==1){
            $this->setFlashSession(false, 'Completed events cannot be rescheduled.');
            return redirect()->back();
        }
        $request = request();
        $validated = $request->validate([
            'start_datetime'      => ['required', 'after:now'],
            'end_datetime'        => ['required', 'after:start_datetime'],
            'site_publish_date'   => ['required', 'before_or_equal:start_datetime'],
            'site_depublish_date' => ['required', 'after:end_datetime'],
            'sell_start_time'     => ['required', 'array'],
            'sell_start_time.*'   => ['required'],
            'sell_end_time'       => ['required', 'array'],
            'sell_end_time.*'     => ['required'],
            'reschedule_note'     => ['nullable'],
        ], [
            'start_datetime.required'           => 'Start date & time is required.',
            'start_datetime.after'              => 'Start date must be in the future.',
            'end_datetime.after'                => 'End date must be after the start date.',
            'site_publish_date.before_or_equal' => 'Publish date must be on or before the event start date.',
            'site_depublish_date.after'         => 'Expiry date must be after the event end date.',
            'sell_start_time.*.required'        => 'Sell start date is required for all tickets.',
            'sell_end_time.*.required'          => 'Sell end date is required for all tickets.',
        ]);
        DB::beginTransaction();

        try {
            // ── Snapshot old values for the log ───────────────────────────────
            $oldData = [
                'start_datetime'      => $event->start_datetime,
                'end_datetime'        => $event->end_datetime,
                'site_publish_date'   => $event->site_publish_date,
                'site_depublish_date' => $event->site_depublish_date,
            ];

            // ── Update the Event ──────────────────────────────────────────────
            $event->update([
                'start_datetime'      => $validated['start_datetime'],
                'end_datetime'        => $validated['end_datetime'],
                'site_publish_date'   => $validated['site_publish_date'],
                'site_depublish_date' => $validated['site_depublish_date'],
            ]);

            // ── Update each Ticket's sell dates ───────────────────────────────
            $sellStartTimes = $request->input('sell_start_time');
            $sellEndTimes   = $request->input('sell_end_time');

            foreach ($event->eventTickets as $index => $ticket) {
                $ticket->update([
                    'sell_start_time' => $sellStartTimes[$index],
                    'sell_end_time'   =>$sellEndTimes[$index],
                ]);
            }

            // ── Send reschedule notification email ────────────────────────────
            //$emailResponse   = $this->sendRescheduleNotification($event, $validated['reschedule_note'] ?? null);

            // ── Write to the reschedule log ───────────────────────────────────
            $rescheduleLog = EventRescheduleLog::create([
                'event_id'               => $event->id,
                'user_id'                => !empty($callFrom)?null:auth()->guard('front')->user()->id,

                // Old values snapshot
                'old_start_datetime'     => $oldData['start_datetime'],
                'old_end_datetime'       => $oldData['end_datetime'],
                'old_site_publish_date'  => $oldData['site_publish_date'],
                'old_site_depublish_date'=> $oldData['site_depublish_date'],

                // New values
                'new_start_datetime'     => $event->start_datetime,
                'new_end_datetime'       => $event->end_datetime,
                'new_site_publish_date'  => $event->site_publish_date,
                'new_site_depublish_date'=> $event->site_depublish_date,

                // Note
                'note'                    => $validated['reschedule_note'] ?? null,

                // Email fields default to null — will be updated after sending
                'email_count'             => null,
                'is_success'              => null,
                'response'                => null,
                'response_code'           => null,
                'request_data'            => null,
                'request_data_size'       => null,
            ]);

            DB::commit();

            // ── Send notification AFTER commit & update the log ───────────────
            // Done outside the transaction so a mail failure won't rollback event data
            //$this->sendRescheduleNotification($event, $validated['reschedule_note'] ?? null, $rescheduleLog->id);
            $tickets = $event->tickets()->where('rsvp_confirmation', 1)->get();
            if($tickets->isNotEmpty()){
                $to_arr = [];
                $log_arr = [];
                foreach ($tickets as $key => $value) {
                    if(!isset($value->volunteer)){
                        continue;
                    } else if(!isset($value->volunteer->id)){
                        continue;
                    }
                    $email = $value->volunteer->email_address;
                    $name = $value->volunteer->first_name.' '.$value->volunteer->last_name;
                    if(!empty(trim($email))){
                        $to_arr[] = 
                        [
                            "to" => [
                                ["email" => trim($email)]
                                //["email" => 'learningdcm@gmail.com']
                            ],
                            "dynamic_template_data" => [
                                "subject" => "New date confirmed for ".$event->event_name." — see you soon 🌊",
                                "volunteer_name" => $name,
                                "volunteer_email" => $email,
                                'organizer_name' => $event->creator->name,
                                "reschedule_reason" => !empty($validated['reschedule_note']) ? $validated['reschedule_note'] : false,
                                "event_name" =>  $event->event_name,
                                'event_date' => $event->startTimeAndEndTime,
                                'event_location' => $event->fullAddress,
                                'cancel_registration_link' => $this->mailUrl('front-cleanup-attending'),
                                'add_to_calendar_link' => $this->mailUrl('front-registration-calendar', ['slug' => $event->slug]), 
                                'event_link' => $this->mailUrl('view-event-details', ['slug' => $event->slug]),
                                'is_under_18'    => $value->is_under_18==1?true:false,
                                'waiver_signed'  => !empty($value->rsvp_filled_at)?true:false,
                                'waiver_link'    => $this->mailUrl('view-waiver-form', ['ticket' => encrypt($value->id)]),
                            ],
                        ];
                        $log_arr[] = [
                            "id" => $value->id,
                            "email" => trim($email)
                        ];
                    }
                }
                if(!empty($to_arr) && count($to_arr)>0){
                    $to_arr_child = array_chunk($to_arr, 300);
                    $log_arr_child = array_chunk($log_arr, 300);
                    foreach ($to_arr_child as $key => $value) {
                        $this->runRescheduleEventEmail($value,$log_arr_child[$key],$rescheduleLog->id);
                    }
                }
            }

            $this->setFlashSession(true, 'Event rescheduled successfully. Volunteers will be notified.');
            if(!empty($callFrom)){
                return redirect()->route('index-events');
            } else {
                return redirect()->route('front-cleanup-hosting', ['type' => 'organizer']);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->setFlashSession(false, 'Something went wrong while rescheduling. Please try again.');
            return redirect()->back()->withInput();
        }
    }
    public function runRescheduleEventEmail($to_arr,$log_arr,$rescheduleLogId){
        if(!SEND_MAIL){
            return false;
        }
        $jayParsedAry = [
            "personalizations" => $to_arr, 
            "from" => [
                        "name" => "VolunteerCleanup.Org" ,
                        "email" => "outreach@volunteercleanup.org" 
                    ], 
            "template_id" => "d-05bdce4a79464eb294ac6be3fa0f6ee3" 
        ]; 

        // Convert the post data to a string
        $postDataString = http_build_query($jayParsedAry);
        // Calculate the size of the post data in bytes
        $postDataSizeBytes = strlen($postDataString);
        // Convert bytes to megabytes
        $postDataSizeMB = round($postDataSizeBytes / (1024 * 1024),2);

        $response = $this->SendSendgridEmail('','','',$jayParsedAry);

        //print_r($response);
        $is_success = 1;
        $response_code = 202;
        if(isset($response) && !empty($response)){
            if(isset($response['response']) && !empty($response['response'])){
                if(isset($response['response']['code']) && !empty($response['response']['code']) && ($response['response']['code']==202 || $response['response']['code']==200)){
                    $is_success = 1;
                    $response_code = $response['response']['code'];
                    //echo 'SUCCESS '.$resUnjson['response']['code'];
                }
                else{
                    $is_success = 0;
                    $response_code = $response['response']['code'];
                    //echo 'FAILED '.$resUnjson['response']['code'];
                }
            }
            else{
                //echo 'FAILED';
            }
        }
        else{
            //echo 'FAILED';
        }
        /* EventRescheduleLog::create([
            'email_count'       => count($log_arr),
            'is_success'        => $is_success,
            'response'          => json_encode($response),
            'response_code'     => $response_code,
            'request_data'      => json_encode($log_arr),
            'request_data_size' => $postDataSizeMB,
        ]); */
        EventRescheduleLog::where('id', $rescheduleLogId)->update([
            'email_count'       => count($log_arr),
            'is_success'        => $is_success,
            'response'          => json_encode($response),
            'response_code'     => $response_code,
            'request_data'      => json_encode($log_arr),
            'request_data_size' => $postDataSizeMB,
        ]);
    }
    public function cronEventFinalize(){
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $records = Event::with('creator:id,first_name,last_name,email_address')->where('is_completed',0)->whereDate('end_datetime', '=', now()->subDay()->toDateString())->get();
        if($records->isNotEmpty()){
            $to_arr = [];
            $log_arr = [];
            foreach ($records as $key => $value) {
                if(isset($value->creator) && isset($value->creator->email_address) && !empty(trim($value->creator->email_address))){
                    $to_arr[] = 
                    [
                        "to" => [
                            ["email" => trim($value->creator->email_address)]
                            //["email" => 'learningdcm@gmail.com']
                        ],
                        "dynamic_template_data" => [
                            "is_reminder" => false,
                            "org_name" => $value->creator->first_name.' '.$value->creator->last_name,
                            "event_name" =>  $value->event_name,
                            'event_date' => $value->startTimeAndEndTime,
                            'event_location' => $value->fullAddress,
                            "cleanup_report_link" => $this->mailUrl('front-post-a-event', ['id'=> $value->encrypted_id,'open_tab'=>'step5']),
                            "reschedule_event_link" => $this->mailUrl('view-event-details',['slug'=>$value->slug]),
                        ],
                    ];
                    $log_arr[] = [
                        "event_id" => $value->id,
                        "email" => trim($value->creator->email_address)
                    ];
                }
                $activeCohosts = $value->cohostRecords->where('request_status', 'approved');
                if ($activeCohosts->isNotEmpty()) {
                    foreach ($activeCohosts as $key => $cohost) {
                        if(isset($cohost->volunteer) && isset($cohost->volunteer->email_address) && !empty(trim($cohost->volunteer->email_address))){
                            $to_arr[] = 
                            [
                                "to" => [
                                    ["email" => trim($cohost->volunteer->email_address)]
                                    //["email" => 'learningdcm@gmail.com']
                                ],
                                "dynamic_template_data" => [
                                    "is_reminder" => false,
                                    "org_name" => $cohost->volunteer->first_name.' '.$cohost->volunteer->last_name,
                                    "event_name" =>  $value->event_name,
                                    'event_date' => $value->startTimeAndEndTime,
                                    'event_location' => $value->fullAddress,
                                    "cleanup_report_link" => $this->mailUrl('front-post-a-event', ['id'=> $value->encrypted_id,'open_tab'=>'step5']),
                                    "reschedule_event_link" => $this->mailUrl('view-event-details',['slug'=>$value->slug]),
                                ],
                            ];
                            $log_arr[] = [
                                "event_id" => $value->id,
                                "email" => trim($cohost->volunteer->email_address)
                            ];

                        }
                    }
                }
            }
            if(!empty($to_arr) && count($to_arr)>0){
                $to_arr_child = array_chunk($to_arr, 300);
                $log_arr_child = array_chunk($log_arr, 300);
                foreach ($to_arr_child as $key => $value) {
                    $this->runCronEventFinalize($value,$log_arr_child[$key]);
                }
            }
        }
    }
    public function runCronEventFinalize($to_arr,$log_arr){
        $jayParsedAry = [
            "personalizations" => $to_arr, 
            "from" => [
                        "name" => "VolunteerCleanup.Org" ,
                        "email" => "outreach@volunteercleanup.org" 
                    ], 
            "template_id" => "d-3badeebd9cfe40458f55935dd25aa41a" 
        ]; 

        // Convert the post data to a string
        $postDataString = http_build_query($jayParsedAry);
        // Calculate the size of the post data in bytes
        $postDataSizeBytes = strlen($postDataString);
        // Convert bytes to megabytes
        $postDataSizeMB = round($postDataSizeBytes / (1024 * 1024),2);

        $response = $this->SendSendgridEmail('','','',$jayParsedAry);

        //print_r($response);
        $is_success = 1;
        $response_code = 202;
        if(isset($response) && !empty($response)){
            if(isset($response['response']) && !empty($response['response'])){
                if(isset($response['response']['code']) && !empty($response['response']['code']) && ($response['response']['code']==202 || $response['response']['code']==200)){
                    $is_success = 1;
                    $response_code = $response['response']['code'];
                    //echo 'SUCCESS '.$resUnjson['response']['code'];
                }
                else{
                    $is_success = 0;
                    $response_code = $response['response']['code'];
                    //echo 'FAILED '.$resUnjson['response']['code'];
                }
            }
            else{
                //echo 'FAILED';
            }
        }
        else{
            //echo 'FAILED';
        }
        EventFinalizeEmailLog::create([
            'email_count'       => count($log_arr),
            'is_success'        => $is_success,
            'response'          => json_encode($response),
            'response_code'     => $response_code,
            'request_data'      => json_encode($log_arr),
            'request_data_size' => $postDataSizeMB,
        ]);
        if(!empty($log_arr)){
            $eventIds = array_column($log_arr, 'event_id');
            Event::whereIn('id', $eventIds)->increment('finalize_email_count');
        }
    }
    public function cronEventFinalizeReminder(){ // call after first finalize email with 72 hours duration
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $records = Event::with('creator:id,first_name,last_name,email_address')
        ->where('is_completed', 0)
        ->where('finalize_email_count', '>', 0)
        ->whereRaw("DATE_ADD(DATE(end_datetime), INTERVAL (finalize_email_count * 3) DAY) = CURDATE()")
        ->get();
        if($records->isNotEmpty()){
            $to_arr = [];
            $log_arr = [];
            foreach ($records as $key => $value) {
                if(isset($value->creator) && isset($value->creator->email_address) && !empty(trim($value->creator->email_address))){
                    $to_arr[] = 
                    [
                        "to" => [
                            ["email" => trim($value->creator->email_address)]
                            //["email" => 'learningdcm@gmail.com']
                        ],
                        "dynamic_template_data" => [
                            "is_reminder" => true,
                            "org_name" => $value->creator->first_name.' '.$value->creator->last_name,
                            "event_name" =>  $value->event_name,
                            'event_date' => $value->startTimeAndEndTime,
                            'event_location' => $value->fullAddress,
                            "cleanup_report_link" => $this->mailUrl('front-post-a-event', ['id'=> $value->encrypted_id,'open_tab'=>'step5']),
                            "reschedule_event_link" => $this->mailUrl('view-event-details',['slug'=>$value->slug]),
                        ],
                    ];
                    $log_arr[] = [
                        "event_id" => $value->id,
                        "email" => trim($value->creator->email_address)
                    ];
                }
                $activeCohosts = $value->cohostRecords->where('request_status', 'approved');
                if ($activeCohosts->isNotEmpty()) {
                    foreach ($activeCohosts as $key => $cohost) {
                        if(isset($cohost->volunteer) && isset($cohost->volunteer->email_address) && !empty(trim($cohost->volunteer->email_address))){
                            $to_arr[] = 
                            [
                                "to" => [
                                    ["email" => trim($cohost->volunteer->email_address)]
                                    //["email" => 'learningdcm@gmail.com']
                                ],
                                "dynamic_template_data" => [
                                    "is_reminder" => true,
                                    "org_name" => $cohost->volunteer->first_name.' '.$cohost->volunteer->last_name,
                                    "event_name" =>  $value->event_name,
                                    'event_date' => $value->startTimeAndEndTime,
                                    'event_location' => $value->fullAddress,
                                    "cleanup_report_link" => $this->mailUrl('front-post-a-event', ['id'=> $value->encrypted_id,'open_tab'=>'step5']),
                                    "reschedule_event_link" => $this->mailUrl('view-event-details',['slug'=>$value->slug]),
                                ],
                            ];
                            $log_arr[] = [
                                "event_id" => $value->id,
                                "email" => trim($cohost->volunteer->email_address)
                            ];

                        }
                    }
                }
            }
            if(!empty($to_arr) && count($to_arr)>0){
                $to_arr_child = array_chunk($to_arr, 300);
                $log_arr_child = array_chunk($log_arr, 300);
                foreach ($to_arr_child as $key => $value) {
                    $this->runCronEventFinalize($value,$log_arr_child[$key]);
                }
            }
        }
    }
    public function cronTicketNotifications(){
        if(env('SEND_TICKET_NOTIFY_MAIL')!=true){
            return false;
        }
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        $request = request();
        $records = EventTicketNotification::with(['volunteer:id,first_name,last_name,email_address','ticket:id,event_id,ticket_name,ticket_type,ticket_price,sell_start_time,sell_end_time','ticket.event:id,event_name,slug,creator_id,start_datetime,end_datetime,charge_bear_by,venue,pincode'])->where('is_sent', 0)->whereHas('ticket', function ($q) {
            $q->whereDate('sell_start_time', date('Y-m-d'));
        })->get();
        //echo '<pre>';
        //print_r($records->toArray());
        if($records->isNotEmpty()){
            $data = [];
            $to_arr = [];
            $log_arr = [];
            $updateIds = [];
            foreach ($records as $key => $record) {
                $data[$record->ticket_id][] = $record;
            }
            foreach ($data as $key1 => $value1) {
                if(isset($value1[0]->ticket) && isset($value1[0]->ticket->id)){
                    $ticket = $value1[0]->ticket;
                    foreach ($value1 as $key => $value) {
                        if(isset($value->volunteer) && isset($value->volunteer->email_address) && !empty(trim($value->volunteer->email_address))){
                            //print_r($value->toArray());
                            $updateIds[] = $value->id;
                            $to_arr[] = 
                            [
                                "to" => [
                                    ["email" => trim($value->volunteer->email_address)]
                                    //["email" => 'learningdcm@gmail.com']
                                ],
                                "dynamic_template_data" => [
                                    "volunteer_name" => $value->volunteer->first_name.' '.$value->volunteer->last_name,
                                    "event_url" => $this->mailUrl('view-event-details',['slug'=>$ticket->event->slug]),
                                    "event_name" =>  $ticket->event->event_name,
                                    "event_date" =>  $ticket->event->startTimeAndEndTime,
                                    "event_location" =>  $ticket->event->fullAddress,
                                    "organizer" =>  $ticket->event->creator->first_name.' '.$ticket->event->creator->last_name,
                                ],
                            ];
                            $log_arr[] = [
                                "id" => $value->id,
                                "ticket_id" => $value->ticket_id,
                                "volunteer_id" => $value->volunteer_id,
                                "email" => trim($value->volunteer->email_address)
                            ];
                        }
                    }
                }
            }
            //print_r($to_arr);
            //print_r($log_arr);
            //print_r($updateIds);
            //die();
            if(!empty($to_arr) && count($to_arr)>0){
                $to_arr_child = array_chunk($to_arr, 300);
                $log_arr_child = array_chunk($log_arr, 300);
                $updateIds_child = array_chunk($updateIds, 300);
                //print_r($to_arr_child[14]);
                //die();
                foreach ($to_arr_child as $key => $value) {
                    $this->runCronTicketNotifications($value,$log_arr_child[$key],$updateIds_child[$key]);
                }
            }
        }
    }
    public function runCronTicketNotifications($to_arr,$log_arr,$updateIds){
        if(env('SEND_TICKET_NOTIFY_MAIL')!=true){
            return false;
        }
        $jayParsedAry = [
            "personalizations" => $to_arr, 
            "from" => [
                        "name" => "VolunteerCleanup.Org" ,
                        "email" => "outreach@volunteercleanup.org" 
                    ], 
            "template_id" => "d-8b60d30487624ad48181d0db07175923" 
        ]; 

        // Convert the post data to a string
        $postDataString = http_build_query($jayParsedAry);
        // Calculate the size of the post data in bytes
        $postDataSizeBytes = strlen($postDataString);
        // Convert bytes to megabytes
        $postDataSizeMB = round($postDataSizeBytes / (1024 * 1024),2);

        $response = $this->SendSendgridEmail('','','',$jayParsedAry);

        //print_r($response);
        $is_success = 1;
        $response_code = 202;
        if(isset($response) && !empty($response)){
            if(isset($response['response']) && !empty($response['response'])){
                if(isset($response['response']['code']) && !empty($response['response']['code']) && ($response['response']['code']==202 || $response['response']['code']==200)){
                    $is_success = 1;
                    $response_code = $response['response']['code'];
                    //echo 'SUCCESS '.$resUnjson['response']['code'];
                }
                else{
                    $is_success = 0;
                    $response_code = $response['response']['code'];
                    //echo 'FAILED '.$resUnjson['response']['code'];
                }
            }
            else{
                //echo 'FAILED';
            }
        }
        else{
            //echo 'FAILED';
        }
        TicketNotificationLog::create([
            'email_count'       => count($log_arr),
            'is_success'        => $is_success,
            'response'          => json_encode($response),
            'response_code'     => $response_code,
            'request_data'      => json_encode($log_arr),
            'request_data_size' => $postDataSizeMB,
        ]);
        EventTicketNotification::whereIn('id', $updateIds)->update(['is_sent' => 1]);
    }
    public function cronWeeklyAlerts(){
        //die();
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        //echo '<pre>';
        $request = request();
        $alerts = VolunteerAlert::with(['volunteer:id,first_name,last_name,email_address'])->where('status', 1)->where('email_frequency','weekly')->orderBy('id','ASC')->get(); //->limit(8)
        //print_r($alerts->toArray());die();
        if(isset($alerts[0]) && isset($alerts[0]->id)){
            $locations = [];
            $locations_helper = [];
            foreach ($alerts as $key => $alert) {
                if(!isset($locations[$alert->zipcode])){
                    $locations[$alert->zipcode] = [
                        'location_lat'=>$alert->location_lat,
                        'location_long'=>$alert->location_long,
                        'event_count'=>0,
                        'event_content'=>''
                    ];
                    $locations_helper[$alert->zipcode] = 0;
                    $request->merge([
                        'location' => $alert->zipcode,
                        'distance' => 15,
                        'latitude' => $alert->location_lat,
                        'longitude' => $alert->location_long,
                    ]);
                    $events = Event::with('category')->listactive()->where('is_private',0)->limit(10)->get();
                    if($events->count()>0){
                        $locations_helper[$alert->zipcode] = $events->count();
                        $locations[$alert->zipcode]['event_count'] = $events->count();
                        $locations[$alert->zipcode]['event_content'] = $this->cronWeeklyAlertsEventHtml($events);
                    }
                }
            }
            //print_r($locations);die();
            //print_r($locations);
            $to_arr = [];
            $log_arr = [];
            $counter = 0;
            $counter_e = [
                'learningdcm@gmail.com',
            ];
            foreach ($alerts as $key => $alert) {
                if(isset($locations_helper[$alert->zipcode]) && $locations_helper[$alert->zipcode]>0 && isset($alert->volunteer) && isset($alert->volunteer->email_address) && !empty(trim($alert->volunteer->email_address))){
                    $to_arr[] = 
                    [
                        "to" => [
                            ["email" => trim($alert->volunteer->email_address)]
                            //["email" => 'learningdcm@gmail.com']
                            //["email" => $counter_e[$counter]]
                        ],
                        "dynamic_template_data" => [
                            "display_name" => $alert->volunteer->first_name.' '.$alert->volunteer->last_name,
                            //"alert_name" =>  $alert->alert_name,
                            "alert_name" =>  $alert->zipcode,
                            "events" => $locations[$alert->zipcode]['event_content'],
                            "base_url" => config('app.laravelurl'),
                            "disable_cur_alert" => route('unsubscribe-alert',['user'=>encrypt($alert->volunteer->id),'alert'=>encrypt($alert->id)]),
                            "disable_all_alert" => route('unsubscribe-alert',['user'=>encrypt($alert->volunteer->id)]),
                        ],
                    ];
                    $log_arr[] = array(
                        "email" => $alert->volunteer->email_address,
                        "alert_id" => $alert->id, 
                    );
                    $counter++;
                }
            }
            //print_r(count($locations_helper));
            //print_r($locations_helper);
            //print_r($to_arr);
            //die();
            if(count($to_arr)>0){
                $to_arr_child = array_chunk($to_arr, 300);
                $log_arr_child = array_chunk($log_arr, 300);
                //print_r($to_arr_child[14]);
                //die();
                foreach ($to_arr_child as $key => $value) {
                    $this->cronSendWeeklyAlertMail($value,$log_arr_child[$key]);
                }
            }
        }
    }
    public function cronSendWeeklyAlertMail($to_arr,$log_arr){
        //print_r($to_arr);
        //return false;
        //echo 'sendgrid mail function fit but die'; die();
        //die();
        if(env('SEND_WEEKLY_MAIL')!=true){
            return false;
        }
        $jayParsedAry = [
            "personalizations" => $to_arr, 
            "from" => [
                        "email" => "outreach@volunteercleanup.org" 
                    ], 
            "template_id" => "d-913d0fbed7de496289f80de87da8d8a6" 
        ]; 

        // Convert the post data to a string
        $postDataString = http_build_query($jayParsedAry);
        // Calculate the size of the post data in bytes
        $postDataSizeBytes = strlen($postDataString);
        // Convert bytes to megabytes
        $postDataSizeMB = round($postDataSizeBytes / (1024 * 1024),2);
        // Output the size of the post data in MB
        //echo "Size of post data: " . $postDataSizeMB . " MB\n\n";
        //return true;
        
        //print_r(count($jayParsedAry));
        //echo  "<br>";

        $response = $this->SendSendgridEmail('','','',$jayParsedAry);

        /* $data = json_encode($jayParsedAry);
        //print_r(strlen($data));die();
        $url = 'https://api.sendgrid.com/v3/mail/send';
        //$url = 'https://wordpress-1220099-4339390.cloudwaysapps.com/testfile.php';
        $response = wp_remote_post($url, array(
            'body'    => $data,
            'method'    => 'POST',
            'headers' => array(
                'Content-Type'   => 'application/json',
                'Authorization' => 'Bearer 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'
            ),
        ));    */

        //old - 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'

        //print_r($response);
        $is_success = 1;
        $response_code = 202;
        if(isset($response) && !empty($response)){
            if(isset($response['response']) && !empty($response['response'])){
                if(isset($response['response']['code']) && !empty($response['response']['code']) && ($response['response']['code']==202 || $response['response']['code']==200)){
                    $is_success = 1;
                    $response_code = $response['response']['code'];
                    //echo 'SUCCESS '.$resUnjson['response']['code'];
                }
                else{
                    $is_success = 0;
                    $response_code = $response['response']['code'];
                    //echo 'FAILED '.$resUnjson['response']['code'];
                }
            }
            else{
                //echo 'FAILED';
            }
        }
        else{
            //echo 'FAILED';
        }
        AlertLog::create([
            'email_count'       => count($log_arr),
            'is_success'        => $is_success,
            'response'          => json_encode($response),
            'response_code'     => $response_code,
            'request_data'      => json_encode($log_arr),
            'request_data_size' => $postDataSizeMB,
        ]);

        $myfile = fopen("sendGrid_code.txt", "a+");
        $txt = "\n\nRESPONSE (POST DATA: ".$postDataSizeMB." MB)-------------------------Time" . date('Y-m-d H:i')."\n";
        $txt .= json_encode($response);
        fwrite($myfile, $txt);
        fclose($myfile);     
    }
    public function cronWeeklyAlertsEventHtml($events){
        $html  = '';
        if(isset($events[0]) && isset($events[0]->id)){
            foreach ($events as $key => $event) {
                //$link = route('view-event-details', ['slug' => $event->slug]);
                //$link = 'https://volunteercleanup.org/event/'.$event->slug;
                $link = config('app.laravelurl').$event->slug;
                $title = $event->event_name;
                $baseurl = config('app.laravelurl');
                $html .= '<tr style="display: flex;border-bottom: 1px solid #f5f5f5; padding: 20px;">
                    <td>
                        <div class="wpem-event-banner-img" style="background-image: url('.($event->formattedBanner()).'); height: 90px;
                        width: 90px;
                        background-size: cover!important;
                        background-position: center!important;
                        border-radius: 4px;
                        background-color:#e4e4e4">
                        </div>
                    </td>
                    <td id="dateHide">
                        <div class="wpem-event-information" style="float: left;font-size: 15px;line-height: 20px;width: 100%; padding: 0px 15px;">
                            <div class="wpem-event-date" style="width: 80px; font-size: 15px; line-height: 20px;">
                                <div class="wpem-event-date-type" style="display: flex;">';
                                if (!empty($event->start_datetime)) { 
                                    $html .= '<div class="wpem-from-date" style="width: 40px;">
                                        <div class="wpem-date" style="font-size: 29px; line-height: 30px; font-weight: 600; color:#555555">'.(date('d', strtotime($event->start_datetime))).'</div>
                                        <div class="wpem-month" style="font-size: 13px; text-transform: uppercase; font-weight: 400; line-height: 15px;  color: #555555;">'.(date('M', strtotime($event->start_datetime))).'</div>
                                    </div>';
                                } 
                                if (date('Y-m-d',strtotime($event->start_datetime)) != date('Y-m-d',strtotime($event->end_datetime)) && !empty($event->end_datetime)) { 
                                    $html .= '<div class="wpem-to-date" style="display: flex; float: left; padding-left: 10px;  position: relative; ">
                                        <div class="wpem-date-separator" style="position: absolute; left: 0; top: 50%;  transform: translate(0,-50%);  font-size: 20px;  color: #555555">-</div>
                                        <div style="padding-left: 10px; padding-top: 2px;">
                                            <div class="wpem-date" style="font-size: 15px; line-height: 15px; font-weight: 500; color: #555555">'.(date('d', strtotime($event->end_datetime))).'</div>
                                            <div class="wpem-month" style="font-size: 9px; text-transform: uppercase; font-weight: 400; line-height: 12px; color:#555555">'.(date('M', strtotime($event->end_datetime))).'</div>
                                        </div>
                                    </div>';
                                }
                        $html .= '</div>
                            </div>
                        </div>
                        <div class="clear:both"></div>
                    </td>
                    <td class="addFlex">
                        <table width="100%" cellpadding="0" cellspacing="0" style="box-sizing: border-box; font-family: Arial, \'Helvetica Neue\', Helvetica, sans-serif; margin: 0; padding: 0; width: 100%;">
                            <tr>
                                <h3 class="wpem-heading-text" style="font-family: Lexend, Sans-serif; font-size: 22px; line-height: 30px; font-weight: 700; color: #111111; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0; text-transform: capitalize;"><a style="color: #111111;" href="'.($link).'">'.($title).'</a></h3>
                            </tr>
                            <tr>
                                <div class="wpem-event-date-time" style="margin: 5px 0px;  min-height: 22px;  color: #555555;  display: flex;">
                                
                                        <img width="20" height="20" src="'.($baseurl.'assets/images/clock.png').'" alt="-" /><span class="wpem-event-date-time-text" style="font-size: 15px; line-height: 20px; padding-left: 5px; font-family: Roboto, Sans-serif;"> '.$event->startTimeAndEndTime.'</span>
                                </div>
                            </tr>
                            <tr>
                                <div class="wpem-event-location" style="margin: 5px 0px;  min-height: 22px;  color: #555555;  display: flex;">
                                    
                                    <img width="15" height="20" src="'.($baseurl.'assets/images/location-pin.png').'" alt="-" /><span class="wpem-event-location-text" style="font-size: 15px; line-height: 20px; padding-left: 5px; font-family: Roboto, Sans-serif;">'.$event->fullAddress.'</span>
                                </div>
                            </tr>
                            <tr>
                                <div class="wpem-event-ticket-type" style="margin: 5px 0px;  min-height: 22px;  color: #555555;">';
                                $totalTicketCount = 0;
                                $totalBookedTicket = 0;
                                if($event->eventActiveTickets->count()>0){
                                    foreach($event->eventActiveTickets as $eventTicket){
                                        $totalTicketCount += $eventTicket->available_quantity;
                                        $totalBookedTicket += $eventTicket->approvedSold->count();
                                    }
                                }
                                //$totalBookedTicket = $event->tickets->where('rsvp_confirmation',1)->count();
                                if($totalTicketCount>0){ 
                                    $html .= '<div style="display:block;width: 100%;margin:10px 0px">
                                        <span class="wpem-ticket-price" style="font-size: 15px; line-height: 20px; padding-left: 5px; font-family: Roboto, Sans-serif;"></span>';
                                        if($totalBookedTicket>0 && $totalTicketCount>0 && $totalBookedTicket>=$totalTicketCount){
                                            $html .=  '<span class="wpem-ticket-quantity wpem-form-group wpem-ticket-sold-out" style="background-color: rgb(255 0 0 / 80%);color: white; padding: 4px 10px;border-radius: 4px; text-align: center;">
                                            Sold Out</span>';
                                        }else{ 
                                            $html .=  '<span class="wpem-event-ticket-type-text" style="background: #f5f5f5 ; color: #111111; padding: 5px 7px; display: inline-block; line-height: 15px;  font-weight: 500; font-size: 14px; border-radius: 4px;margin-left:5px;"><span class="remaining-tickets-counter">'.( 'Remaining tickets '.($totalTicketCount-$totalBookedTicket).' out of '.$totalTicketCount).'</span></span>';
                                        }
                                    $html .=    '</div>';
                                }
                            
                            $html .=    '</div>
                            </tr>
                        </table>
                    </td>
                </tr>';
            }
        }
        return $html;
    }
    public function syncAllOrganizer(){
        $organizers = Volunteer::where('is_organiser', 1)
            ->limit(100)
            ->offset(0)
            ->get();
        foreach ($organizers as $organizer) {
            $this->syncWithWP($organizer);
        }
    }
    public function sendCancelEMail(){
        $event = Event::find(1790);
        $tickets = $event->tickets()->where('rsvp_confirmation', 1)->get();
        echo '<pre>';
        //echo $tickets->count();
        //print_r($event->toArray());
        if($tickets->isNotEmpty()){
            $to_arr = [];
            $log_arr = [];
            foreach ($tickets as $key => $value) {
                $email = $value->volunteer->email_address;
                $name = $value->volunteer->first_name.' '.$value->volunteer->last_name;
                if(!empty(trim($email)) && !in_array($email, $log_arr)){
                    $to_arr[] = 
                    [
                        "to" => [
                            ["email" => trim($email)]
                        ],
                        "dynamic_template_data" => [
                            "volunteer_name" => $name,
                        ],
                    ];
                    /* $log_arr[] = [
                        "id" => $value->id,
                        "email" => trim($email)
                    ]; */
                    $log_arr[] = trim($email);
                }
            }
            $to_arr[] = 
            [
                "to" => [
                    ["email" => 'learningdcm@gmail.com']
                ],
                "dynamic_template_data" => [
                    "volunteer_name" => 'DEV USER',
                ],
            ];
            print_r($log_arr);die();
            /* if(!empty($to_arr) && count($to_arr)>0){
                $to_arr_child = array_chunk($to_arr, 300);
                $log_arr_child = array_chunk($log_arr, 300);
                foreach ($to_arr_child as $key => $value) {
                    $this->sendSendgridTemplateEmailWithoutLog($value,"d-90ce664df7e04903aca3bf79264b7db8");
                }
            } */
            echo 'Email sent to all volunteers.';
        }
    }
    public function cronEventReminderForVolunteers()
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $templateId = 'd-d9575886bc2d4f89bf0d333128e09f85';

        $events = Event::with([
            'creator:id,first_name,last_name,email_address',
            'bringTools:id,name',
        ])
        ->where('is_send_reminder_email', 1)
        ->whereNull('deleted_at')
        ->whereNull('cancelled_at')
        ->where('is_active', 1)
        ->where('status', 'published')
        ->whereDate('start_datetime', now()->addDays(2)->toDateString())
        ->get();

        $to_arr = [];

        foreach ($events as $event) {
            $tickets = $event->tickets()
                ->with('volunteer:id,first_name,last_name,email_address')
                ->where('rsvp_confirmation', 1)
                ->get();

            $whatToBring = $event->bringTools->pluck('name')->implode(', ');
            $organizerNote = trim(strip_tags($event->reminder_custom_note ?? ''));

            foreach ($tickets as $ticket) {
                if (!$ticket->volunteer) {
                    continue;
                }

                $isUnder18 = $ticket->is_under_18 == 1;

                $email = $isUnder18
                    ? trim($ticket->waiver_handler_email ?? '')
                    : trim($ticket->volunteer->email_address ?? '');

                if (empty($email)) {
                    continue;
                }

                $to_arr[] = [
                    'to' => [
                        ['email' => $email],
                    ],
                    'dynamic_template_data' => [
                        //'subject' => 'Reminder: '.$event->event_name.' is coming up soon',
                        'is_under_18' => $isUnder18 ? true : false,
                        'show_organizer_notes' => !empty($organizerNote) ? true : false,

                        'waiver_handler_name' => $ticket->waiver_handler_name,
                        'volunteer_name' => trim($ticket->volunteer->first_name.' '.$ticket->volunteer->last_name),
                        'event_name' => $event->event_name,
                        'event_date' => $event->startTimeAndEndTime,
                        'event_location' => $event->fullAddress,
                        'meeting_point' => $event->meeting_spot_details ?: '-',
                        'parking_information' => $event->parking_info_details ?: '-',
                        'what_to_bring' => $whatToBring ?: '-',
                        'organizer_notes' => $organizerNote,

                        'ical_event_link' => $this->mailUrl('front-registration-calendar', ['slug' => $event->slug]),
                        'cancel_registration_link' => $this->mailUrl('cancel-rsvp-get', [
                            'ticket' => encrypt($ticket->id),
                            'type' => 1,
                        ]),
                    ],
                ];
            }
        }

        foreach (array_chunk($to_arr, 300) as $chunk) {
            $this->sendSendgridTemplateEmailWithoutLog($chunk, $templateId);
        }

        return response()->json($this->generateResponse(true, 'Event reminder cron executed successfully.', [
            'email_count' => count($to_arr),
        ]));
    }
}
