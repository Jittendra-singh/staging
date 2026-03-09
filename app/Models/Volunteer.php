<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
//use Illuminate\Database\Eloquent\SoftDeletes;
class Volunteer extends Authenticatable
{
    use HasFactory, HelperTrait; //SoftDeletes
    protected $guarded = ['id'];
    protected $hidden = [
        'password',
        'remember_token'
    ];
    public function scopeActive($query){
        $query->where('status', '=', 1);
    }
    public function city_state_zip(){
        return $this->belongsTo(CityStateZip::class, 'city_id' , 'id');
    }
    /**Relations */
    public function city(){
        return $this->belongsTo(City::class, 'city_id' , 'city_id');
    }
    
    public function alerts(){
        return $this->hasMany(VolunteerAlert::class, 'volunteer_id' );
    }
    
    public function widgets(){
        return $this->hasMany(VolunteerWidget::class, 'volunteer_id' );
    }

    public function state(){
        return $this->hasOneThrough(
            State::class,   // Final model
            City::class,    // Intermediate model
            'city_id',      // Foreign key on City table...
            'state_id',     // Foreign key on State table...
            'city_id',      // Local key on Volunteer table...
            'state_id'      // Local key on City table...
        );
    }
    public function events(){
        //return $this->morphMany(Event::class , 'creator');
        return $this->hasMany(Event::class , 'creator_id');
    }
    public function approvedAttendingEvents(){
        return $this->approvedParticipatedEvents()->active()
        ->withPivot([
            'id',
            'order_id',
            'event_id',
            'volunteer_id',
            'ticket_number',
            'price',
            'ticket_id',
            'status',
            'extra_notes',
            'checkin',
            'ticket_answer',
            'created_at',
            'updated_at',
            'community_form_status',
            'community_form_pdf',
            'hours_completed',
            'activity_description',
            'participation_proof',
            'additional_notes',
            'waiver_filled_at',
            'waiver_handler_email',
            'waiver_handler_name',
            'rsvp_confirmation',
            'rsvp_filled_at',
            'is_under_18',
            'signature',
            'ticket_status'
        ])
        ->using(EventOrderTicket::class , function($q){
            $q->active();
        });
    }
    public function approvedParticipatedEvents(){
        return $this->belongsToMany(Event::class, 'event_order_tickets', 'volunteer_id', 'event_id')->where('rsvp_confirmation',1);
    }
    
    public function attendingEvents(){
        return $this->participatedEvents()->active()
        ->withPivot([
            'id',
            'order_id',
            'event_id',
            'volunteer_id',
            'ticket_number',
            'price',
            'ticket_id',
            'status',
            'extra_notes',
            'checkin',
            'ticket_answer',
            'created_at',
            'updated_at',
            'community_form_status',
            'community_form_pdf',
            'hours_completed',
            'activity_description',
            'participation_proof',
            'additional_notes',
            'waiver_filled_at',
            'waiver_handler_email',
            'waiver_handler_name',
            'rsvp_confirmation',
            'rsvp_filled_at',
            'is_under_18',
            'signature',
            'ticket_status'
        ])
        ->using(EventOrderTicket::class , function($q){
            $q->active();
        });
    }
    public function participatedEvents(){
        return $this->belongsToMany(Event::class, 'event_order_tickets', 'volunteer_id', 'event_id');
    }

    public function cohostingEvents(){
        return $this->belongsToMany(Event::class , 'event_cohost', 'volunteer_id', 'event_id')->where('request_status','approved');
    }

    public function ticketNotifications(){
        return $this->hasMany(EventTicketNotification::class , 'volunteer_id');
    }

    public function allControlEvents(){
        $cohost_events = $this->cohostingEvents()->active()->orderBy('start_datetime','desc')->get();
        $organising_events = $this->events()->active()->orderBy('start_datetime','desc')->get();
        return $cohost_events->merge($organising_events);
    }
    public function organisation(){
        return $this->hasOne(VolunteerOrganisation::class, 'volunteer_id' , 'id');
    }

    public function cohosts(){
        return $this->hasMany(EventCoHost::class , 'volunteer_id');
    }
    /**Attributes */
    protected function name(): Attribute{
        return Attribute::make(
            get: fn () => ucfirst($this->first_name) . ' ' . ucfirst($this->last_name),
        );
    }
    public function profilePicture() : Attribute{ 
        return Attribute::make(
            get : fn($value) => json_decode($value ? : null ) , 
        );
    }
    public function organisationLogo() : Attribute{
        return Attribute::make(
            get : fn($value) => json_decode($value ? : null ) , 
        );
    }

    public function isOrganisationProfileSetup() : Attribute{
        $isSetup = $this->organisation_name && $this->organisation_landing_slug ; 
        return Attribute::make(
            get : fn() => $isSetup  , 
        );
    }

    public function fullAddress():Attribute{
        //$add_array = [$this->address_line_1 , $this->address_line_2, $this->pincode, @$this->city?->city_name, @$this->state?->state_name, @$this->country?->country_name];
        $add_array = [$this->address_line_1 , $this->address_line_2, @$this->city_state_zip?->city, @$this->city_state_zip?->state, @$this->country?->country_name, $this->pincode];
        return Attribute::make(
            get: fn () => implode(', ', array_filter($add_array, fn($v) => !empty($v))),
        );
    }
    public function formattedImage($param){
        /* if (!$this->$param) {
            $path = asset('assets_front/images/profile-imgs/img-13.jpg');
        }else{
            $path = $this->$param->path;
        }
        return asset($path); */

        // Dummy image (always exists)
        $dummy = asset('assets_front/images/profile-imgs/img-13.jpg');

        // If DB field / relation not set
        if (!$this->$param) {
            return $dummy;
        }
        $imagePath = $this->$param->path;
        // Full system path
        $fullPath = public_path($imagePath);

        // Check file exists
        if (file_exists($fullPath)) {
            return asset($imagePath);
        }

        // Fallback
        return $dummy;
    }


}
