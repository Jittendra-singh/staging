<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Event;
use App\Models\EventCoHost;
use App\Models\CohostPermission;
use App\Models\Volunteer;
use App\Models\EventOrderTicket;
use App\Models\Setting;
use App\Models\EventTicket;
use App\Models\CityStateZip;
use App\Http\Controllers\FrontOrderController;
use Carbon\Carbon;
class EventController extends Controller
{
    private $gateHandler = 'events';
    private $viewDirectory = 'pages.events.';
    private $modelClass;
    public function __construct()
    {
        $this->modelClass = Event::class;
    }
    public function index()
    {
        Gate::authorize('view', $this->gateHandler);
        $selected_organiser = request()->organiser_id ? Volunteer::find(request()->organiser_id) : null;
        $selected_volunteer = request()->volunteer_id ? Volunteer::find(request()->volunteer_id) : null;
        $selected_cohost = request()->cohost_id ? Volunteer::find(request()->cohost_id) : null;
        $listingData = $this->modelClass::withCount(['cohosts','attandee'])->where(function($q){
                    if(!empty(request()->event_type)){
                        if(request()->event_type == 1){
                            $q->whereDate("start_datetime" , ">" , date('Y-m-d'));
                        }elseif(request()->event_type == 2){
                            $q->whereDate("start_datetime" , "<=" , date('Y-m-d'));
                            $q->whereDate("end_datetime" , ">=" , date('Y-m-d'));
                            $q->where("is_completed", '!=' , 1);
                        }elseif(request()->event_type == 3){
                            $q->where("is_completed", '!=' , 1);
                            $q->whereDate("end_datetime" , "<" , date('Y-m-d'));
                        }elseif(request()->event_type == 4){
                            $q->where("is_completed" , 1);
                        }
                        request()->merge(['start_date' => null, 'end_date' => null]);
                    }
                    if(request()->event_name)
                        $q->where("event_name" , "LIKE" , "%".request()->event_name."%");
                    if(request()->start_date)
                        $q->whereDate("start_datetime" , ">=" , request()->start_date);
                    if(request()->end_date)
                        $q->whereDate("end_datetime" , "<=" , request()->end_date);
                    /* if(request()->organiser)
                        $q->where("creator_id" , "=" , request()->organiser);
                    if(request()->cohost)
                        $q->whereHas("cohosts" , function($q){
                            $q->where("volunteers.id" , request()->cohost);
                        });
                    if(request()->attendee)
                        $q->whereHas("attandee" , function($q){
                            $q->where("volunteers.id" , request()->attendee);
                        }); */
                    if(request()->organiser_id)
                        $q->where("creator_id" , "=" , request()->organiser_id);
                    if(request()->cohost_id)
                        $q->whereHas("cohosts" , function($q){
                            $q->where("volunteers.id" , request()->cohost_id);
                        });
                    if(request()->volunteer_id)
                        $q->whereHas("attandee" , function($q){
                            $q->where("volunteers.id" , request()->volunteer_id);
                        });
            });
        if(request()->sort_by){
            $listingData = $listingData->orderBy('start_datetime', request()->sort_by == 1 ? 'desc' : 'asc');
        } else {
            $listingData = $listingData->orderBy('start_datetime','desc');
        }
        $listingData = $listingData->paginate(50)->withQueryString();
        return view($this->viewDirectory.'view', [
            'selected_organiser'=>$selected_organiser,
            'selected_volunteer'=>$selected_volunteer,
            'selected_cohost'=>$selected_cohost,
            'listingData' => $listingData,
            'volunteers' => Volunteer::get(),
            'module' => $this->gateHandler,
        ]);
    }
    public function showForm($placeholder = null){
       if($placeholder){
            $data       =  $this->modelClass::findOrFail(decrypt($placeholder));
            Gate::authorize('view' , $this->gateHandler);
        }else{
            Gate::authorize('create' ,  $this->gateHandler);
        }
        $setting = Setting::first();
        return view($this->viewDirectory.'manage', 
                        [
                            'formData'      => $data ?? null ,
                            'setting'      => $setting, 
                            'module'        => $this->gateHandler, 
                            'countries'     => \App\Models\Country::orderBy('country_name')->get(),
                            'bring_tools'   => \App\Models\Tool::orderBy('name')->get(),
                            'provided_tools'=> \App\Models\Tool::orderBy('name')->get(),
                            'categories'    => \App\Models\EventCategory::orderBy('name')->get(),
                            /* 'event_tags'    => \App\Models\Tag::where('type', 'event')->orderBy('name')->get(),
                            'volunteer_tags'=> \App\Models\Tag::where('type', 'volunteer')->orderBy('name')->get(), */
                            'event_tags'    => \App\Models\Tag::orderBy('name')->get(),
                            'volunteer_tags'=> \App\Models\Tag::orderBy('name')->get(),
                            'timezones'     => config('app.timezones'),
                            'timezonescode'     => config('app.timezonescode'),
                            'reoccurring'   => config('app.reoccurring'),
                            'volunteers'    => \App\Models\Volunteer::where('is_organiser', 1)->orderBy('first_name')->get(),
                        ]);
    }
    public function getEventMetaDataBackend(){
        $request = request();
        $event = [];
        $event['name'] = $request['event_name'];
        $event['description'] = $request['description'];
        $event['location'] = $request['venue'].' '.$request['pincode'];
        $event['start_date'] = $request['start_datetime'];
        $event['end_date'] = $request['end_datetime'];
        $event['category'] = !empty(trim($request['event_category']))?trim($request['event_category']):'N/A';
        $content = $this->getEventMetaDataByAi($event);
        return $this->generateJsonResponse(true, '', json_decode($content));
    }
    public function getFocusKeywords(){
        request()->validate([
            'meta_title' => 'required',
            'meta_description' => 'required',
        ]); 
        $meta_title = request()->meta_title;
        $meta_description = request()->meta_description;
        /* $content = "
                        You are an SEO expert. Your task is to extract the most relevant SEO focus keywords from the given meta title and meta description.
                        Meta Title: {$meta_title}
                        Meta Description: {$meta_description}
                        Instructions:
                        - Return a comma-separated list of high-impact SEO keywords and key phrases directly derived from the title and description.
                        - Only include terms that users are likely to search for and that accurately reflect the content.
                        - If no relevant keywords or key phrases are found, return nothing. Do not write 'empty string' or 'nothing', 'none', or any explanation — just leave the response blank.
                        Output format (strict):
                        keyword1, keyword2, keyword3
                    ";
        $content = " You are an SEO expert. Extract the most relevant SEO focus keywords from the given meta title and meta description.
                        Meta Title: {$meta_title}
                        Meta Description: {$meta_description}
                        Rules:
                        - Return only a comma-separated list of keywords.
                        - Do not explain anything.
                        - Do not ask for more information.
                        - If nothing meaningful is provided or no keywords can be extracted, return exactly a blank string which literally empty.: 
                        Output: (Do not include labels or extra formatting — return only the keyword list or an empty string)
                    ";
        $content = $this->hitApiToAi($content); */
        $content = $this->getAiFocusKeyword($meta_title,$meta_description);
        return $this->generateJsonResponse(true, '', [
            'keywords' => $content,
        ]);
    }
    public function manage(Request $request, $placeholder = null)
    {
        if (!$placeholder) {
            Gate::authorize('create', $this->gateHandler);
            $obj = new $this->modelClass;
            $obj->created_by = auth()->user()->id;
        } else {
            Gate::authorize('update', $this->gateHandler);
            $obj = $this->modelClass::findOrFail(decrypt($placeholder));
        }

        $validatedData = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_category_id' => 'required|exists:event_categories,id',
            'description' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after_or_equal:start_datetime',
            'event_timezone' => 'required|string',
            'participation_type' => 'required',
            'participation_type_external_link' => 'nullable',
            'venue' => 'required|string|max:255',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'pincode' => 'required|string|max:10',
            'country_id' => 'required|exists:countries,country_id',
            'cropped_image' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'flyer_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video_url' => 'nullable|url',
            'bring_tools' => 'nullable|array',
            'bring_tools.*' => 'exists:tools,id',
            'provided_tools' => 'nullable|array',
            'provided_tools.*' => 'exists:tools,id',
            'event_reoccurance' => 'required|string',
            'meeting_spot_details' => 'nullable|string',
            'parking_info_details' => 'nullable|string',
            'admin_commission' => 'nullable|numeric|min:0',
            'show_ticket_description' => 'nullable|boolean',
            'allow_donations' => 'nullable|boolean',
            'is_private_ticket' => 'nullable|boolean',
            'show_remaining_tickets' => 'nullable|boolean',
            'sell_tickets_individually' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_private' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_send_reminder_email' => 'nullable|boolean',
            'reminder_custom_note' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_focus_keyword' => 'nullable|string|max:255',
            'site_publish_date'   => 'required|date',
            'site_depublish_date' => 'required|date|after_or_equal:site_publish_date',
            /**ticket conditions */
            'ticket_sell_start_time' => 'nullable',
            'ticket_sell_end_time' => 'nullable',
            /* 'ticket_sell_start_time' => [
                'required',
                'date',
                'before_or_equal:ticket_sell_end_time',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->start_datetime && strtotime($value) >= strtotime(date('Y-m-d',strtotime($request->start_datetime)))) {
                        //$fail('The ticket sell start time must be before the event start date.');
                        $fail('The ticket sell start date must be before the event start date.');
                    }
                },
            ],
            'ticket_sell_end_time' => [
                'required',
                'date',
                'after_or_equal:ticket_sell_start_time',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->start_datetime && strtotime($value) > strtotime(date('Y-m-d',strtotime($request->start_datetime)))) {
                        $fail('The ticket sell end date must be before or equal to the event start date.');
                    }
                },
            ], */
            'ticket_questions' => 'nullable|string|max:1000',
            /* 'ticket.*.ticket_name' => 'required|string|max:255',
            'ticket.*.ticket_type' => 'required|string',
            'ticket.*.sell_start_time' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->start_datetime && strtotime($value) >= strtotime(date('Y-m-d',strtotime($request->start_datetime)))) {
                        $fail('The ticket sell start date must be before the event start date.');
                    }
                },
            ],
            'ticket.*.sell_end_time' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->start_datetime && strtotime($value) > strtotime(date('Y-m-d',strtotime($request->start_datetime)))) {
                        $fail('The ticket sell end date must be before or equal to the event start date.');
                    }
                },
            ],
            'ticket.*.ticket_price' => 'required_if:ticket.*.ticket_type,paid|numeric|min:0',
            'ticket.*.available_quantity' => 'required|integer|min:1',
            //'ticket.*.minimum_quantity_to_sell' => 'required|integer|min:1',
            'ticket.*.ticket_description' => 'nullable|string', */
            'ticket' => [
                'required_if:participation_type,1',
                'array',
                'min:1',

                function ($attribute, $value, $fail) use ($request) {

                    if ($request->participation_type != 1) {
                        return;
                    }

                    foreach ($value ?? [] as $index => $ticket) {

                        $sellStart = $ticket['sell_start_time'] ?? null;
                        $sellEnd   = $ticket['sell_end_time'] ?? null;

                        if (!$sellStart || !$sellEnd) {
                            continue;
                        }

                        $ticketStart = Carbon::parse($sellStart);
                        $ticketEnd   = Carbon::parse($sellEnd);

                        if ($ticketEnd->lessThan($ticketStart)) {
                            $fail(
                                'The ticket sell end date must be after or equal to the ticket sell start date.'
                            );

                            return;
                        }

                    if ($request->start_datetime) {

                        $eventStart = Carbon::parse($request->start_datetime);

                        if ($ticketEnd->greaterThan($eventStart)) {
                            $fail(
                                'The ticket sell end date/time must be before or equal to the event start date/time.'
                            );

                                return;
                        }
                    }
                    }
                },
            ],

            'ticket.*.ticket_name' => [
                'required_if:participation_type,1',
                'string',
                'max:255',
            ],

            'ticket.*.ticket_type' => [
                'required_if:participation_type,1',
                'string',
            ],

            'ticket.*.sell_start_time' => [
                'required_if:participation_type,1',
                'date',
            ],

            'ticket.*.sell_end_time' => [
                'required_if:participation_type,1',
                'date',
            ],

            'ticket.*.ticket_price' => [
                'required_if:ticket.*.ticket_type,paid',
                'numeric',
                'min:0',
            ],

            'ticket.*.available_quantity' => [
                'required_if:participation_type,1',
                'integer',
                'min:1',
            ],

            'ticket.*.ticket_description' => [
                'nullable',
                'string',
            ],
            /** Waiver form validation */
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
        
        if($request->participation_type!=1){
            if(isset($obj->eventTickets[0])){
                $this->setFlashSession(false,"Remove ticket types before changing participation type."); 
                return redirect()->back();
            }
            if(isset($request->ticket) && is_array($request->ticket) && count($request->ticket)>0){
                $this->setFlashSession(false,"You can't add ticket type without selecting the VolunteerCleanup Tickets on Participation Type."); 
                return redirect()->back();
            }
        }
        if($request->participation_type==4){
            if(empty($request->participation_type_external_link)){
                $this->setFlashSession(false,"Please enter external link for participation type."); 
                return redirect()->back();
            }
        }

        // Tool managements
        $obj->fill($validatedData);
        $obj->slug = $this->generateSlug(Event::class, 'slug', request()->slug ? : $obj->event_name, $obj->id ? : 0);
        if ($request->hasFile('banner_image')) {
            $uploaded_banner = $this->uploadFiles('banner_image', 'events/banners');
            $obj->banner_image = json_encode($uploaded_banner);
        }
        if ($request->hasFile('flyer_image')) {
            $uploaded_flyer = $this->uploadFiles('flyer_image', 'events/flyers');
            $obj->flyer_image = json_encode($uploaded_flyer);
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

            $obj->banner_image = json_encode([
                'path'          => "$destinationPath/" . $fileName,
                'original_name' => $originalName,
                'extension'     => 'jpg',
            ]);
        }
        $obj->creator_type              = 'App\\Models\\Volunteer';
        $obj->creator_id                = $request->volunteer_id;
        $obj->is_private                = $request->is_private ? 1 : 0;
        $obj->is_online                 = $request->is_online ? 1 : 0;
        $obj->is_featured               = $request->is_featured ? 1 : 0;
        $obj->show_ticket_description   = $request->show_ticket_description ? 1 : 0;
        $obj->allow_donations           = $request->allow_donations ? 1 : 0;
        $obj->allow_refund              = $request->allow_refund ? 1 : 0;
        $obj->charge_bear_by            = $request->charge_bear_by ? 1 : 0;
        $obj->is_private_ticket         = $request->is_private_ticket ? 1 : 0;
        $obj->show_remaining_tickets    = $request->show_remaining_tickets ? 1 : 0;
        $obj->sell_tickets_individually = $request->sell_tickets_individually ? 1 : 0;
        $obj->allow_community_service_form = $request->allow_community_service_form ? 1 : 0;
        $obj->is_send_reminder_email    = $request->has('is_send_reminder_email') ? 1 : 0;
        $obj->reminder_custom_note      = $request->reminder_custom_note ?? null;
        $obj->site_publish_date         = $request->site_publish_date ?? null;
        $obj->site_depublish_date       = $request->site_depublish_date ?? null;
        $obj->ticket_sell_start_time    = $request->ticket_sell_start_time ?? null;
        $obj->ticket_sell_end_time      = $request->ticket_sell_end_time ?? null;
        $obj->latitude                  = $request->latitude ?? null;
        $obj->longitude                 = $request->longitude ?? null;
        /**location handling */
        /* $coordinate = $this->getGoogleCoordinates(request()->venue);
        $obj->latitude = $coordinate['latitude'];
        $obj->longitude = $coordinate['longitude']; */
        /***********************
        * waiver form sections *
        ************************/ 
        $waiver_form_pdf = $request->waiver_form_content;
        $obj->waiver_form_type = request()->waiver_form_type ?? null;
        if(request()->waiver_form_type && request()->hasFile('waiver_form_content')){
            $waiver_form_pdf = $this->uploadFiles('waiver_form_content', 'events/waiver_pdf');
            $obj->waiver_form_content = json_encode($waiver_form_pdf);
        }elseif(request()->waiver_form_type == 'default'){
            $settings = Setting::first();
            $obj->waiver_form_content = $settings->waiver_text;
        }elseif(!request()->waiver_form_type){
            $obj->waiver_form_content =  null;
        }else{
            $obj->waiver_form_content = request()->waiver_form_content;
        }
        try{
            //DB::beginTransaction();
                $obj->save();
                $tools = [];
                foreach (request()->bring_tools ?? [] as $toolId) {
                    $tools[$toolId]['should_bring'] = 1;
                }
                foreach (request()->provided_tools ?? [] as $toolId) {
                    $tools[$toolId]['should_bring'] = 0;
                }

                // dd($tools);
                $res = $obj->tools()->sync($tools);
                $obj->tags()->sync(request()->tags);
                $obj->volunteertags()->sync(request()->volunteer_tags ?? []);
                /**************************************************************************
                * tickets system with constrait of changing number of ticket after orders *
                ***************************************************************************/ 
                $tickets = [];
                if ($obj->exists) { 
                    $existingTickets = $obj->eventTickets()->get()->keyBy('id');
                    $submittedTicketIds = collect($request->ticket)->pluck('ticket_id')->filter()->toArray();
                    $obj->eventTickets()->whereNotIn('id', $submittedTicketIds)->get()->each(function($ticket) {
                        $orderTicketCount = EventOrderTicket::where('ticket_id', $ticket->id)->count(); 
                        if((int)$orderTicketCount > 0){
                            throw new \Exception("Ticket ID {$ticket->id} has already been sold and cannot be deleted.");
                        } 
                        $ticket->delete();
                    });
                    if(is_array($request->ticket)){
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
                                $obj->eventTickets()->save($ticket);
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
                    $obj->eventTickets()->saveMany($tickets);
                
                    /* // Create new tickets for new event
                    $tickets = [];
                    foreach ($request->ticket as $ticketData) {
                        $tickets[] = new EventTicket($ticketData); 
                    }
                    $obj->eventTickets()->saveMany($tickets); */
                }
            //DB::commit();
            if (!$placeholder) {
                $this->setFlashSession($res,'Your event is live. Thank you for making a difference!');
            } else {
                $this->setFlashSession($res,'Event has been updated successfully.');
            }
            return redirect()->route('index-events');
        }catch(\Exception $e){

            //DB::rollback();
            // dd($e->getMessage());
            $this->setFlashSession(false, $e->getMessage()); 
            return redirect()->back();
        }
    }
    public function manageCoHosts($placeholder){
        Gate::authorize('update', $this->gateHandler);
        $event = Event::findOrFail(decrypt($placeholder));
        return view($this->viewDirectory.'manage-cohost' , [
            'event'       => $event, 
            'module'      => $this->gateHandler,
            'volunteers'  => Volunteer::whereNotIn('id' , $event->cohosts->pluck('id')->toArray())->get(),  
        ]);
    }

    public function updateEventPermission($placeholder){
        Gate::authorize('update', $this->gateHandler);
        $event = Event::findOrFail(decrypt($placeholder));
        $event->eventPermissions()->delete();
        foreach(request()->manage_permission as $permissions){
            $permission_array = str()->of($permissions)->explode('-');
            $cohost_id = $permission_array[0];
            $permission_name = $permission_array[1];
            $cohost_record = EventCoHost::where('event_id', $event->id)->where('volunteer_id', $cohost_id)->first();
            CohostPermission::create([
                'cohost_record_id' =>  $cohost_record->id , 
                'permission_name'  =>  $permission_name 
            ]);
        }
        $this->setFlashSession(true);
        return redirect()->back();
    }
    public function inviteCoHosts($placeholder){
        Gate::authorize('update', $this->gateHandler);
        $request = request();
        $validated = $request->validate([
            'email'      => 'required',
            'name'       => 'required',
            'extra_notes'=> 'nullable',
            //'permission' => 'required',
        ]);

        try {
            $event = Event::findOrFail(decrypt($placeholder));
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

            if(isset($validated['permission']) && !empty($validated['permission'])){
                foreach ($validated['permission'] as $perm) {
                    $permission = new CohostPermission();
                    $permission->cohost_record_id = $event_cohost_id;
                    $permission->permission = $perm;
                    $permission->save();
                }
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

            $this->setFlashSession(true, 'Co-Host added successfully.');
            return redirect()->route('manage-event-hosts', ['placeholder' => $placeholder]);

        } catch (\Exception $e) {
            $this->setFlashSession(false, 'Failed to save Co-Host details: ' . $e->getMessage());
            return redirect()->route('manage-event-hosts', ['placeholder' => $placeholder]);
        }

        /* Gate::authorize('update', $this->gateHandler);
        $request = request();
        $validated = $request->validate([
            'volunteer_email_address' => ['required:volunteer_id', 'email', new \App\Rules\StrictEmail()],
        ]);
        $volunteer = Volunteer::where('email_address' , request()->volunteer_email_address)->first();
        $volunteer_password = '';
        $is_registered = false;
        if(!$volunteer){
            $is_registered = true;
            $volunteer_name     = strstr(request()->volunteer_email_address, '@', true);
            $volunteer_email    = request()->volunteer_email_address;
            $volunteer_password = str()->password(8, letters: true, numbers: false, symbols: false, spaces: false);
            $volunteer       = Volunteer::create([
                                    'first_name'    => $volunteer_name,
                                    'email_address' => $volunteer_email,
                                    'password'      => Hash::make($volunteer_password),
                                    'is_organiser'  => 1,
                                    'organisation_name' => $volunteer_name,
                                    'organisation_landing_slug' => $this->generateSlug(Volunteer::class, 'organisation_landing_slug', $volunteer_name, 0),
                                    'created_by'    => auth()->user()->id , 
                                    'status'        => 1 , 
                                    'is_newsletter' => 1 
                                ]);
        } 
        $event = Event::with('creator')->findOrFail(decrypt($placeholder));
        // Prevent duplicate co-hosts
        if ($event->cohosts()->where('volunteer_id', $volunteer->id)->exists()) {

            return redirect()->back()->withErrors(['volunteer_id' => 'This volunteer is already a co-host for this event.']);
        }else{
            $volunteer = Volunteer::find($volunteer->id);
            if($volunteer){           
                $org_name  = $volunteer->organisation_name ? : $volunteer->name;
                $volunteer->update([
                    'is_organiser' => 1,
                    'organisation_name' => $org_name,
                    'organisation_landing_slug' => $volunteer->organisation_landing_slug ? : $this->generateSlug(Volunteer::class, 'organisation_landing_slug', $org_name, 0),
                ]);
            }
        }
        // Attach co-host with optional notes
        $event->cohosts()->attach($volunteer->id, [
            'invited_at' => now(),
        ]);
        $this->sendCoHostInvitationMail($event, $volunteer,$is_registered, $volunteer_password);
        $this->setFlashSession(true, 'Co-host invited successfully.');
        return redirect()->route('manage-event-hosts', ['placeholder' => $placeholder]); */
    }
    public function reInviteCoHosts($placeholder){
        Gate::authorize('update', $this->gateHandler);
        try {
            $cohost = EventCoHost::where('id', decrypt($placeholder))->firstOrFail();
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
            $this->setFlashSession(true, 'Co-Host invitation re-sent successfully.');
            return redirect()->route('manage-event-hosts', ['placeholder' => encrypt($cohost->event_id)]);
        } catch (\Exception $e) {
            $this->setFlashSession(false, 'Failed to re-send Co-Host invitation: ' . $e->getMessage());
            return redirect()->route('manage-event-hosts', ['placeholder' => encrypt($cohost->event_id)]);
        }

        /* Gate::authorize('update', $this->gateHandler);
        $event = Event::with('creator')->findOrFail(decrypt($eventPlaceholder));
        $volunteer = Volunteer::findOrFail(decrypt($volunteerPlaceholder));
        EventCoHost::where('event_id', decrypt($eventPlaceholder))->where('volunteer_id', decrypt($volunteerPlaceholder))->update([
            'request_status' => 'pending',
            'invited_at' => now(),
        ]);
        $this->sendCoHostInvitationMail($event, $volunteer);
        $this->setFlashSession(true, 'Co-host invited successfully.');
        return redirect()->route('manage-event-hosts', ['placeholder' => $eventPlaceholder]); */
    }
    public function sendCoHostInvitationMail($event, $volunteer,$is_v_registered = false, $volunteer_password = null){
        
        $subject = COHOST_INVITE_SUB;
        $view = 'pages.emails.invite-cohost';
        $emaildata = [
            'cohostName' => $volunteer->name,
            'email' => $volunteer->email_address,
            'password' => $is_v_registered?$volunteer_password:null,
            'login_link' => route('front-login'),
            'acceptLink' => route('response.cohost.invitaion', ['response' => 'accept' ,'event'=> $event->encrypted_id , 'volunteer'=> $volunteer->encrypted_id]), 
            'cancelLink' => route('response.cohost.invitaion', ['response' => 'cancel' ,'event'=> $event->encrypted_id , 'volunteer'=> $volunteer->encrypted_id]), 
            'eventName' => $event->event_name, 
            'eventDate' => $event->start_datetime,
            'eventLocation' => $event->venue,
            'eventOrganizer' => $event->creator->first_name. ' ' . $event->creator->last_name,
        ];
        try {
            $this->sendEmail($volunteer->email_address, $subject, $emaildata, 'invite-cohost');
        } catch (\Exception $e) {
            throw new \Exception("Failed to send co-host invitation email: " . $e->getMessage());
        }
    }
    public function deleteCohost($placeholder){
        /* $obj = EventCoHost::findOrFail($placeholder);
        $res = $obj->delete();
        $this->setFlashSession(true, 'Co-host deleted successfully.');
        return redirect()->back(); */
        
        $cohost = EventCoHost::where('id', decrypt($placeholder))->firstOrFail();

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

        $email_content['subject'] = 'You’ve been removed as a co-host from '.$email_content['event_name'];
        $to_arr = [
            [
                "to" => [
                    ["email" => trim($volunteer->email_address)]
                ],
                "dynamic_template_data" => $email_content
            ]
        ];
        $this->sendSendgridTemplateEmailWithoutLog($to_arr, "d-ccfba74fdc894547a239d1e5824cbcf8");
        /* $this->setFlashSession(true,'Co-Host removed successfully.');
        return redirect()->back(); */
        return $this->generateJsonResponse(true,'Co-Host removed successfully.');
    }
    public function cloneEvent($placeholder){
        Gate::authorize('create', $this->gateHandler);
        $event = Event::findOrFail(decrypt($placeholder));
        $today_dt = date('Y-m-d H:i:s');
        $new_event = $event->replicate();
        $new_event->event_name  = $event->event_name . ' (Clone)';
        $new_event->slug        = $this->generateSlug(Event::class, 'slug', $event->slug , 0); 
        $new_event->created_at  = $today_dt;
        $new_event->updated_at  = $today_dt;
        $new_event->status  = 'draft';
        $new_event->waste_collected  = null;

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
        return redirect()->route('show-event', encrypt($new_event->id));
        /* $event                  = Event::findOrFail(decrypt($placeholder));
        $event->event_name      = $event->event_name . ' (Clone)'; 
        $event->slug            = $this->generateSlug(Event::class, 'slug', $event->slug , 0);
        $event->tickets         = $event->eventTickets->map(function($ticket){
                                    $ticket->id = 0;    
                                    return $ticket; 
                                });   
        $event->event_tags      = $event->tags()->pluck('event_tags.tag_id')->toArray(); 
        $event->volunteer_tags  = $event->volunteertags()->pluck('event_volunteer_tags.tag_id')->toArray(); 
        $event->bring_tools     = $event->bringTools()->pluck('tools.id')->toArray(); 
        $event->provided_tools  = $event->providedTools()->pluck('tools.id')->toArray();
        $event->id              = null; // Reset ID to create a new record
        session()->flashInput($event->toArray());
        return redirect()->route('show-event'); */
    }
    public function toggleStatus($placeholder){
        Gate::authorize('update', $this->gateHandler);
        $obj = $this->modelClass::findOrFail(decrypt($placeholder));
        $obj->is_active = !$obj->is_active; 
        $this->setFlashSession($obj->save());
        /* $startdate = $obj->start_datetime->format('Y-m-d');
        $enddate = $obj->end_datetime->format('Y-m-d');
        if($obj->is_active && ($startdate <= date('Y-m-d') && $enddate >= date('Y-m-d'))){
            $this->setFlashSession(false , 'Opps! this is an ongoing event today so it is not possible to inactive this.');
        }else{
            $obj->is_active = !$obj->is_active ; 
            $this->setFlashSession($obj->save());
        } */
        return redirect()->back();
    }
    
    public function togglePublish($placeholder){
        Gate::authorize('update', $this->gateHandler);
        try {
            $event = Event::findOrFail(decrypt($placeholder));
            $event->status = $event->status=='draft'?'published':'draft';
            $event->save();
            if($event->status=='published'){
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
            $this->setFlashSession(false, 'Failed to update event publish status.');
            return redirect()->back();
        }
        $this->setFlashSession(true, 'Event publish status updated successfully.');
        return redirect()->back();
    }
    public function delete($placeholder){
        Gate::authorize('delete', $this->gateHandler);
        $obj = $this->modelClass::withCount('orders')->findOrFail(decrypt($placeholder));
        if($obj->orders_count)
            return $this->generateJsonResponse(false , 'Opps! Order are connected with this event, we can\'t delete this.');    
        $res = $obj->delete(); 
        return $this->generateJsonResponse($res);
    }
    public function rescheduleEvent($id){
        Gate::authorize('update', $this->gateHandler);
        $event = Event::findOrFail(decrypt($id));
        return view($this->viewDirectory.'reschedule-event', [
            'event' => $event,
            'module' => 'orders',
        ]);
    }
    public function submitRescheduleEventForm(Request $request, $id){
        $frontController = new FrontController();
        return $frontController->submitRescheduleEventForm($request,$id,'admin');
    }
    public function postEventAction($placeholder){
        Gate::authorize('update', $this->gateHandler);
        $event = Event::findOrFail(decrypt($placeholder));
        $attendees = $event->attendees()->get();
        return view($this->viewDirectory.'post-event-action', [
            'event' => $event,
            'listingData' => $attendees,
            'module' => 'orders',
        ]);
    }
    public function saveAfterEventAction($placeholder){
        Gate::authorize('update', $this->gateHandler);
        $event = Event::findOrFail(decrypt($placeholder));
        $request = request();
        $validated = $request->validate([
            'waste_collected' => 'required|string|max:255',

            //'team_name' => 'nullable|string|max:1000',

            'post_event_notes' => 'nullable|string|max:1000',
        ]);
        $event->waste_collected     = $validated['waste_collected'] ?? null;

        //$event->team_name           = $validated['team_name'] ?? null;

        $event->post_event_notes    = $validated['post_event_notes'] ?? null;
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
        $res = $event->save();
        if(isset(request()->send_cleanup_report_email) && request()->send_cleanup_report_email==1){
            return $this->sendWrapupEmail($placeholder);
        }
        $this->setFlashSession($res, $res ? 'Post-event details saved.' : 'Failed to save post-event details.');
        return redirect()->route('post-event-action', ['placeholder' => $placeholder]);
    }
    public function removeAfterEventImage($index, $id){
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
            $html = '<span class="me-2">Images:</span>';
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
    public function sendCommunityForm($ticket){
        Gate::authorize('update', $this->gateHandler);
        // $attendee = Volunteer::findOrFail(decrypt($attendee));  
        $orderTicket = EventOrderTicket::findOrFail(decrypt($ticket));
        $event = $orderTicket->event;
        $volunteer = $orderTicket->volunteer;
        if($orderTicket->community_form_status == 'requested'){
            if($orderTicket->community_form_pdf){
                $community_form = $orderTicket->community_form_pdf; 
            }else{
                $data = [
                    'event'            => $event,
                    'volunteerName'    => $volunteer->name,
                    'hours'            => $orderTicket->hours_completed,
                    'activity'         => $orderTicket->activity_description,
                    'organizer_name'   => $event->creator->name,
                    'organizer_contact'=> $event->creator->email_address." | ".$event->creator->phone,
                    'notes'            => $orderTicket->additional_notes,
                    'proof_url'        => $orderTicket->participation_proof,
                    'date'             => $event->start_datetime." - ".$event->end_datetime,
                ];
                try{
                    $html = view('pages.events.community-form', $data)->render();
                    $pdf = \PDF::setOptions(['isRemoteEnabled' => true])->loadHTML($html);
                    $filename = 'community_form_' . $orderTicket->id . '_' . time() . '.pdf';
                    $path = 'events/community_forms/' . $filename;
                    $publicPath = public_path($path);
                    // dd($publicPath); // Removed debugging line
                    if (!file_exists(dirname($publicPath))) {
                        mkdir(dirname($publicPath), 0755, true);
                    }
                    file_put_contents($publicPath, $pdf->output());
                    $orderTicket->community_form_pdf = $path;
                    $orderTicket->save();
                    $community_form = $path;
                }catch(\Exception $e){
                    $this->setFlashSession(false , "Issues occured while making generating PDF");
                }
            }
            /**Mail The PDF */
            try{
                $emailData = [
                    'volunteer' => $volunteer->first_name.' '.$volunteer->last_name,
                    'event_title' => $event->event_name,
                    'certificate_download_link' => url('/').'/'.$community_form,
                    'dashboard_link' => route('front-cleanup-attending')
                ];
                $emailData['subject'] = 'Your Community Service Certificate for '.$event->event_name.' Is Ready';

                $to_arr = [
                    [
                        "to" => [
                            ["email" => trim($volunteer->email_address)]
                        ],
                        "dynamic_template_data" => $emailData
                    ]
                ];
                $this->sendSendgridTemplateEmailWithoutLog($to_arr, "d-e24f4c705af7498db1f5fb56b53d5327");
                $orderTicket->community_form_status = 'approved';
                $orderTicket->save();
            }catch(\Exception $e){
                $this->setFlashSession(false , "Issues occured while sending emails.");
            }
            return redirect()->back();
        }else{
            throw new \Exception("Community service form is not required for this user.");
        }
    }
    public function markCheckin($ticket){
        $frontController = new FrontController();
        $res = $frontController->ticketMarkCheckin($ticket,true);
        return $res;
    }
    public function markCheckinOLD2($ticket){
        Gate::authorize('update', $this->gateHandler);
        $orderTicket = EventOrderTicket::findOrFail(decrypt($ticket));
        $event = $orderTicket->event;
        $volunteer = $orderTicket->volunteer;
        if($orderTicket->community_form_pdf){
            $community_form = $orderTicket->community_form_pdf; 
        }else{
            /* $data = [
                'event'            => $event,
                'volunteerName'    => $volunteer->name,
                'hours'            => $orderTicket->hours_completed,
                'activity'         => $orderTicket->activity_description,
                'organizer_name'   => $event->creator->name,
                'organizer_contact'=> $event->creator->email_address." | ".$event->creator->phone,
                'notes'            => $orderTicket->additional_notes,
                'proof_url'        => $orderTicket->participation_proof,
                'date'             => $event->start_datetime." - ".$event->end_datetime,
            ]; */
            $data = [
                'event'            => $event,
                'volunteerName'    => $volunteer->name,
                //'hours'            => $orderTicket->hours_completed,
                'hours'            => ($event->start_datetime && $event->end_datetime) ? round($event->start_datetime->diffInMinutes($event->end_datetime) / 60, 2) : 0,
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
                // dd($publicPath); // Removed debugging line
                if (!file_exists(dirname($publicPath))) {
                    mkdir(dirname($publicPath), 0755, true);
                }
                file_put_contents($publicPath, $pdf->output());
                $orderTicket->community_form_pdf = $path;
                $orderTicket->save();
                $community_form = url('/').'/'.$path;
            }catch(\Exception $e){
                $this->setFlashSession(false , "Issues occured while making generating PDF");
            }
        }
        /**Mail The PDF */
        try{
            $emailData = [
                'volunteer' => $volunteer->first_name.' '.$volunteer->last_name,
                'event_title' => $event->event_name,
                'certificate_download_link' => $community_form,
                'dashboard_link' => route('front-cleanup-attending')
            ];
            $emailData['subject'] = 'Your Community Service Certificate for '.$event->event_name.' Is Ready';
            
            $to_arr = [
                [
                    "to" => [
                        ["email" => trim($volunteer->email_address)]
                    ],
                    "dynamic_template_data" => $emailData
                ]
            ];
            $this->sendSendgridTemplateEmailWithoutLog($to_arr, "d-e24f4c705af7498db1f5fb56b53d5327");
            $orderTicket->community_form_status = 'approved';
            $orderTicket->checkin = 1;
            $orderTicket->save();
            $this->setFlashSession(true , "Community service form has been sent to volunteer.");
        }catch(\Exception $e){
            $this->setFlashSession(false , "Issues occured while sending emails.");
        }
        return redirect()->back();
    }
    public function markCheckinOLD($ticket){
        Gate::authorize('update', $this->gateHandler);
        $orderTicket           = EventOrderTicket::findOrFail(decrypt($ticket));
        $orderTicket->checkin  = !$orderTicket->checkin ;
        // if($orderTicket->checkin == 1 && $orderTicket->community_form_status == 'pending'){ 
        //     // send pending thank you and email
        //     $event = $orderTicket->event;
        //     $volunteer = $orderTicket->volunteer;
        //     $email_content = [
        //         'eventName' => $event->event_name,
        //         'volunteerName' => $volunteer->name,
        //         'date' => $event->start_datetime->format('F j, Y'),
        //         'volunteersCount' => $event->attendees()->count(),
        //         'wasteCollected' => $event->waste_collected ?? 'N/A',
        //         'areaCleaned' => $event->area_cleaned ?? 'N/A',
        //         'communityServiceFormLink' => route('community.form', ['ticket' => encrypt($orderTicket->id)]),
        //         'feedbackFormLink' => route('event.feedback', ['event' => encrypt($event->id)]),
        //         'teamName' => config('app.name'),
        //         'contactEmail' => config('mail.from.address'),
        //     ];
        //     // Example: send email (implement actual mail logic as needed)
        //     \Mail::to($volunteer->email_address)->send(
        //     new \App\Mail\SendMail(
        //         "Thank You for Making a Difference at {$event->event_name}!",
        //         'emails.thank-you-volunteer',
        //         $email_content
        //     )
        //     );
        //     // Optionally update status
        //     $orderTicket->community_form_status = 'requested';
        //     $orderTicket->checkin_time = now();
        // } else {
        //     $orderTicket->checkin_time = null;
        // }
        $res = $orderTicket->save();
        $this->setFlashSession($res);
        return redirect()->back();
    }
    public function checkEventForWrapup($id){
        $frontController = new FrontController();
        $res = $frontController->checkEventForWrapup($id,true);
        return $res;
    }
    public function sendWrapupEmail($id){
        $event = Event::with('creator')->findOrFail(decrypt($id));
        if($event->is_completed==1){
            $this->setFlashSession(false,'Event is already marked as completed and wrap-up email has been sent.');
            return redirect()->route('post-event-action', ['placeholder' => $id]);
        }
        if(empty($event->waste_collected)){
            $this->setFlashSession(false,'Waste Collected should be saved before sending the email.');
            return redirect()->route('post-event-action', ['placeholder' => $id]);
        }
        $event->is_completed = 1;
        //$event->wrapup_email_sent_by = auth()->user()->id;
        $event->save();
        $tickets = $event->tickets()->where('checkin', 1)->whereNotNull('waiver_filled_at')->get();
        if($tickets->count() == 0){
            $this->setFlashSession(true,'Event marked as completed. Only volunteers who have checked-in and signed the waiver will receive the email. Since no attendees have been found yet, the email has not been sent to anyone.');
            return redirect()->route('post-event-action', ['placeholder' => $id]);
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
                        // $value->update(['community_form_status' => 'approved']);
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
        $this->setFlashSession(true, 'Event marked as completed. Wrap-Up email and certificate email has been sent successfully.');
        return redirect()->route('post-event-action', ['placeholder' => $id]);
    }
    public function sendThankyouRequestEmail($placeholder){
        Gate::authorize('update', $this->gateHandler);
        $event      = Event::with('creator')->findOrFail(decrypt($placeholder));
        $tickets    = $event->tickets()->where('checkin', 1)->where('community_form_status', 'pending')->get();
        if($tickets->count() == 0){
            $this->setFlashSession(false, 'Only volunteers who have checked in can get the email, but since no one has checked in yet, the email hasn’t been sent to anyone.');
            return redirect()->back();
        }
        foreach ($tickets as $ticket) {
                $email_content = [
                    'eventName' => $event->event_name,
                    'volunteerName' => $ticket->volunteer->name,
                    'date' => $event->start_datetime->format(config('app.dateTimeFormat')),
                    'volunteersCount' => $event->tickets()->where('checkin', 1)->count(),
                    'wasteCollected' => $event->waste_collected ?? 'N/A',

                    'volunteerHours' => $event->volunteer_hours ?? 'N/A',
                    'communityServiceFormLink' => $event->allow_community_service_form ? route('community-form', ['placeholder' => $event->encrypted_id]) : null,
                    'feedbackFormLink' => route('event-feedback', ['placeholder' => $event->encrypted_id]),
                    //'teamName' => $event->team_name,
                    'contactEmail' => config('mail.from.address'),
                ];
                $this->sendEmail($ticket->volunteer->email_address, EVENT_SUMMARY_MAIL_SUB, $email_content, "thankyou-message-after-event");
                /* \Mail::to($ticket->volunteer->email_address)->send(
                    new \App\Mail\SendMail(
                        "Thank You for Making a Difference at {$event->event_name}!",
                        "pages.emails.thankyou-message-after-event",
                        $email_content
                    )
                ); */
        }
        $this->setFlashSession(true, 'Thank you request emails sent successfully.');
        return redirect()->back();
    }
    public function reSendWaiverInvitation(Request $request, $ticket){
        $frontController = new FrontController();
        $res = $frontController->resendWaiver($request, $ticket, true);
        return $res;
    }
    public function checkResendWaiverAdmin($ticket){
        $frontController = new FrontController();
        $res = $frontController->checkResendWaiver($ticket);
        return $res;
    }
    public function reSendWaiverInvitationOld($ticket){
        $orderTicket = decrypt($ticket);
        $orderTicket = EventOrderTicket::findOrFail($orderTicket);

        $frontOrderController = new FrontOrderController();
        $frontOrderController->sendRSVPandWaiverMail($orderTicket);

        /* $volunteer = $orderTicket->volunteer;
        $event = $orderTicket->event;
        $data = [
            'name' => $orderTicket->is_under_18 ? $orderTicket->waiver_handler_name : $orderTicket->volunteer->name,
            'isUnder18' => $orderTicket->is_under_18,
            'eventName' => $orderTicket->event->event_name,
            'waiverUrl' => route('waiver.form', ['ticket' => encrypt($orderTicket->id)]),
        ];
        $targetEmailAddress = $orderTicket->is_under_18 ? $volunteer->email_address : $orderTicket->waiver_handler_email ;
        $this->sendEmail($targetEmailAddress, "Waiver Form Required for {$event->event_name}", $data, "waiver-invitation-form"); */
        /* \Mail::to($targetEmailAddress)->send(
            new \App\Mail\SendMail(
                "Waiver Form Required for {$event->event_name}",
                "pages.emails.waiver-invitation-form",
                $data
            )
        ); */


        $this->setFlashSession(true, 'Waiver invitation email resent successfully.');
        return redirect()->back();
        
    }
    function getEventOptions(){
        $search = request()->search;
        if (!empty($search)) {
            $events = Event::where('is_active', 1)
                ->where('event_name', 'like', '%' . $search . '%')
                ->get();
            if ($events->isEmpty()) {
                $options = [];
            } else {
                foreach ($events as $event) {
                    $options[] = [
                        'id' => $event->id,
                        'text' => $event->event_name,
                    ];
                }
            }
        } else {
            $options = [];
        }
        return response()->json(['results' => $options]);
    }
    public function sendWaiverBulkEmail($id){
        $frontController = new FrontController();
        return $frontController->sendWaiverBulkEmail($id);
    }
    public function markBulkCheckinWithEmail($id){
        $frontController = new FrontController();
        return $frontController->markBulkCheckinWithEmail($id);
    }
    public function certificateRequestDetail($id){
        $orderTicket = EventOrderTicket::with(['volunteer', 'event', 'event.creator'])->findOrFail(decrypt($id));
        $data['authUser']    = auth()->user();
        $data['ticket']      = $orderTicket;
        $data['event']       = $orderTicket->event;
        $data['volunteer']   = $orderTicket->volunteer;
        $data['module']      = 'certificate-requests';
        return view($this->viewDirectory.'certificate-request-detail', $data); 
    }
    public function approveCertificateRequest($id){
        $frontController = new FrontController();
        return $frontController->approveCertificateRequest($id,true);
    }
    public function rejectCertificateRequest($id){
        $frontController = new FrontController();
        return $frontController->rejectCertificateRequest($id,true);
    }
    public function certificateRequests()
    {
        Gate::authorize('view', 'certificate-requests');
        $selected_volunteer = request()->volunteer_id ? Volunteer::find(request()->volunteer_id) : null;
        $selected_organizer = request()->organizer_id ? Volunteer::find(request()->organizer_id) : null;
        $selected_event = request()->event_id ? Event::find(request()->event_id) : null;
        $listingData = EventOrderTicket::whereNotNull('certificate_requested_at')
            ->where(function($q){
                if(request()->volunteer_id){
                    $q->where('volunteer_id', request()->volunteer_id);
                }
                if(request()->organizer_id){
                    $q->whereHas('event', function($query){
                        $query->where('creator_id', request()->organizer_id);
                    });
                    // now EventOrderTicket don't have organizer_id column but it can event_id which is connected to Event modal/table and events table have creator_id which is actually organizer_id means organizer_id= creator_id so how to add this condition here 
                }
                if(request()->event_id){
                    $q->where('event_id', request()->event_id);
                }
                if(request()->start_date)
                    $q->whereDate('certificate_requested_at', '>=' , request()->start_date);
                if(request()->end_date)
                    $q->whereDate('certificate_requested_at', '<=' , request()->end_date);
            });
        if(request()->sort_by){
            $listingData = $listingData->orderBy('certificate_requested_at', request()->sort_by == 1 ? 'desc' : 'asc');
        } else {
            $listingData = $listingData->orderBy('certificate_requested_at','desc');
        }
        $listingData = $listingData->paginate(50)->withQueryString();
        return view($this->viewDirectory.'certificate-requests', [
            'selected_volunteer'=>$selected_volunteer,
            'selected_organizer'=>$selected_organizer,
            'selected_event'=>$selected_event,
            'listingData' => $listingData,
            'module' => 'certificate-requests',
        ]);
    }
}
