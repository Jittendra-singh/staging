<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Volunteer;
use App\Models\City;
use App\Models\Event;
use App\Models\CityStateZip;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Models\VolunteerOrganisation;
class VolunteerController extends Controller
{
    private $gateHandler = 'volunteers';
    private $viewDirectory = 'pages.volunteers.';
    private $modelClass;
    public function __construct()
    {
        $this->modelClass = Volunteer::class;
    }
    public function index(){           
        Gate::authorize('view', $this->gateHandler);
        $listingData = $this->modelClass::with('city_state_zip')->where(function($q){
                        if(request()->event_id){
                            $q->whereHas("participatedEvents" , function($q){
                                $q->where("events.id" , request()->event_id);
                            });
                        }
                        if(request()->name){
                            $q->where( function($q){
                                $q->where('organisation_name', 'like', '%'.request()->name.'%')->orWhere('first_name', 'like', '%'.request()->name.'%')->orWhere('last_name' , 'like' , '%'.request()->name.'%')->orWhere('email_address' , 'like' , '%'.request()->name.'%')->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%".request()->name."%"]);
                            });
                        }
                        if(request()->role){
                            if(in_array( request()->role, ['organiser','volunteer'])){
                                $q->where("is_organiser", request()->role == "organiser" ? 1 : 0);
                            }else{
                                $q->whereHas('cohosts');
                            }
                        }
                        if(request()->start_date)
                            $q->whereDate("created_at" , ">=" , request()->start_date);
                        if(request()->end_date)
                            $q->whereDate("created_at" , "<=" , request()->end_date);

                    //})->order()->skip(0)->take(10)->get();
                    });
        if(request()->sort_by){
            $listingData = $listingData->orderBy('created_at', request()->sort_by == 1 ? 'desc' : 'asc');
        } else {
            $listingData = $listingData->orderBy('created_at','desc');
        }
        $listingData = $listingData->paginate(50)->withQueryString();
        $selected_event = request()->event_id ? Event::find(request()->event_id) : null;
        return view($this->viewDirectory.'view', [
            'listingData' => $listingData,
            'module' => $this->gateHandler,
            'selected_event' => $selected_event
        ]);
    }
    public function updatePassword($placeholder)
    {
        request()->validate([
            'password' => 'required|confirmed',
        ]);
        Gate::authorize('update', 'user');
        $user = $this->modelClass::findOrFail(decrypt($placeholder));
        $user->password = Hash::make(request()->password);
        $this->setFlashSession($user->save());
        return redirect()->route('index-volunteers');
    }
    public function showForm($placeholder = null)
    {
        if($placeholder){
            $data       =  $this->modelClass::with('city_state_zip')->findOrFail(decrypt($placeholder));
            Gate::authorize('view' , $this->gateHandler);
        }else{
            Gate::authorize('create' ,  $this->gateHandler);
        }
        $cities    = City::orderBy('city_name')->get();
        return view($this->viewDirectory.'manage', ['formData' => $data ?? null , 'module' => $this->gateHandler, 'cities' => $cities]);
    }
    public function manage($placeholder = null){
        $placeholder = $placeholder ? decrypt($placeholder) : null;
        request()->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email_address' => ['required', 'email', 'max:255', 'unique:volunteers,email_address,' . ($placeholder ? $placeholder : '0'), new \App\Rules\StrictEmail()],
            'phone'         => ['nullable', 'string', new \App\Rules\ValidUSPhoneNumber()],
            'address_line_1'=> 'nullable|string|max:255',
            'address_line_2'=> 'nullable|string|max:255',
            // 'city_id'       => 'nullable|string|max:255',
            //'pincode'       => 'nullable|string|max:10' ,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],[
            'email_address.unique' => 'This email address is already registered. Please use a different email.',
            'email_address.email' => 'Please enter a valid email address.',
        ]);
        if (!$placeholder) {
            Gate::authorize('create' ,  $this->gateHandler);
            $obj            = new $this->modelClass;
            $obj->created_by = auth()->user()->id; 
            $obj->password = Hash::make(request()->password);
        }else{
            Gate::authorize('update' ,  $this->gateHandler);
            $obj    = $this->modelClass::findOrFail($placeholder);
        }
        $obj->first_name      = request()->input('first_name');
        $obj->last_name       = request()->input('last_name');
        $obj->email_address   = request()->input('email_address');
        $obj->phone           = formatPhoneNumber(request()->input('phone'));
        $obj->address_line_1  = request()->input('address_line_1');
        $obj->address_line_2  = request()->input('address_line_2');
        $obj->city_id         = request()->input('city_id');
        if(request()->has('city_id') && !empty(request()->input('city_id'))){
            $cityStateZip = CityStateZip::find(request()->input('city_id'));
            if(isset($cityStateZip->id)){
                $obj->pincode = $cityStateZip->zipcode;
            }
        }
        /**organisation details */
        if (request()->is_organiser == 1 || @$obj->is_organiser == 1) {
            $obj->is_organiser    = 1;
            request()->validate([
                'organisation_name' => 'required|string|max:255',
                'organisation_short_description' => 'nullable|string',
                'organisation_website' => 'nullable|url|max:255',
                'organisation_instagram' => 'nullable|url|max:255',
                'organisation_facebook' => 'nullable|url|max:255',
                'organisation_linkedin' => 'nullable|url|max:255',
                'organisation_twitter' => 'nullable|url|max:255',
                'organisation_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'organisation_landing_slug' => 'nullable|string|max:255',
                'organisation_updated_at' => 'nullable|date',
            ]);
            $obj->organisation_name = request()->input('organisation_name');
            $obj->organisation_short_description = request()->input('organisation_short_description');
            $obj->organisation_website           = request()->input('organisation_website');
            $obj->organisation_instagram         = request()->input('organisation_instagram');
            $obj->organisation_facebook          = request()->input('organisation_facebook');
            $obj->organisation_linkedin          = request()->input('organisation_linkedin');
            $obj->organisation_twitter           = request()->input('organisation_twitter');
            $obj->organisation_landing_slug      = $this->generateSlug(Volunteer::class, 'organisation_landing_slug', request()->organisation_landing_slug ? : request()->input('organisation_name'), $obj->id ? : 0);
            $obj->updated_at = request()->input('organisation_updated_at');
            if (request()->hasFile('organisation_logo')) {
                $uploadedFiles = $this->uploadFiles('organisation_logo', 'uploads/organisation_logos');
                $obj->organisation_logo = json_encode($uploadedFiles);
            }
        }
        if(request()->hasFile('profile_picture')) {
            $uploadedFiles = $this->uploadFiles('profile_picture' , 'uploads/volunteer_profile_pictures');
            $obj->profile_picture = json_encode($uploadedFiles);
        }
        $res = $obj->save();
        if($res){
            if (!$placeholder) {
                if (request()->is_organiser == 1) {
                    $this->setFlashSession($res,'Great! Organizer has been created. You can now view their details in the list or assign them to events.');
                } else {
                    $this->setFlashSession($res,'Great! Volunteer has been created. You can now view their details in the list or assign them to events.');
                }
                $data = [
                    'volunteer' => $obj->first_name.' '.$obj->last_name,
                    'email' => $obj->email_address,
                    'password' => request()->password,
                    'login_link' => route('front-login'),
                ];
                $this->sendEmail($obj->email_address, VOLUNTEER_REG_BY_BACKEND_SUB, $data, "volunteer-registration-backend");
            } else {
                $this->setFlashSession($res);
            }   
            return redirect()->route('index-volunteers');
        }
        $this->setFlashSession($res);
        return redirect()->back();
    }
    public function toggleStatus($placeholder){
        if(Gate::allows('update' , $this->gateHandler)){
            $obj = $this->modelClass::findOrFail(decrypt($placeholder));
            $obj->status = !$obj->status ; 
            $this->setFlashSession($obj->save());
        }
        return redirect()->route('index-volunteers');
    }
    public function manageVolunteerOrganisation(){
        $request = request();
        $request->validate([
            'volunteer_id' => 'required',
            'organisation_name' => 'required|string|max:255',
            'organisation_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'short_description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
        ]);
        Gate::authorize('update', $this->gateHandler);
        $volunteerId = decrypt($request->input('volunteer_id'));
        $volunteerOrganisation = VolunteerOrganisation::where('volunteer_id', $volunteerId)->first() ? : new VolunteerOrganisation;
        $volunteerOrganisation->organisation_name  = $request->input('organisation_name');
        $volunteerOrganisation->short_description  = $request->input('short_description');
        $volunteerOrganisation->website            = $request->input('website');
        $volunteerOrganisation->instagram          = $request->input('instagram');
        $volunteerOrganisation->facebook           = $request->input('facebook');
        $volunteerOrganisation->linkedin           = $request->input('linkedin');
        $volunteerOrganisation->twitter            = $request->input('twitter');
        $volunteerOrganisation->landing_slug       = $this->generateSlug(VolunteerOrganisation::class, 'landing_slug', $request->input('organisation_name'), ($volunteerOrganisation->id ?? 0));
        $volunteerOrganisation->volunteer_id       = $volunteerId;
        if($request->hasFile('organisation_logo')) {
            $uploadedFiles = $this->uploadFiles('organisation_logo' , 'uploads/organisation_logos');
            $volunteerOrganisation->organisation_logo = json_encode($uploadedFiles);
        }
        $res = $volunteerOrganisation->save();
        $this->setFlashSession($res);
        return redirect()->back();
    }
    public function delete($placeholder){
        Gate::authorize('delete', $this->gateHandler);
        $obj = $this->modelClass::withCount('events', 'participatedEvents' , 'cohostingEvents')->findOrFail(decrypt($placeholder));
        if($obj->events_count || $obj->participated_events_count || $obj->cohosting_events_count){
            return $this->generateJsonResponse(false , 'Opps! Events are connected with this user, we can\'t delete this.');    
        }
        $res = $obj->delete(); 
        return $this->generateJsonResponse($res);
    }
    function getOrganizerOptions(){
        $search = request()->search;
        $options = [];
        if (!empty($search)) {
            $volunteers = Volunteer::where('status', 1)
                ->where('is_organiser', 1)
                ->where(function($query) use ($search) {
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
    function getVolunteerOptions(){
        $search = request()->search;
        $options = [];
        if (!empty($search)) {
            $volunteers = Volunteer::where('status', 1)
                ->where('is_organiser', 0)
                ->where(function($query) use ($search) {
                    $query->where('first_name', 'like', '%' . $search . '%')
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
    function getAllVolunteerOptions(){
        $search = request()->search;
        $options = [];
        if (!empty($search)) {
            $volunteers = Volunteer::where('status', 1)
                ->where(function($query) use ($search) {
                    $query->where('first_name', 'like', '%' . $search . '%')
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
}
