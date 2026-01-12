<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
class Event extends Model
{
    use HasFactory, HelperTrait;  //SoftDeletes
    protected $guarded = ['id'];
    protected $table = 'events';
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];
    /**scopes and attributes */
    public function startTimeAndEndTime():Attribute{
        $timezoneCode = $this->event_timezone?'('.config('app.timezonescode')[$this->event_timezone].')':'';
        return Attribute::make(
            get: fn () => ($this->start_datetime ? $this->start_datetime->format(config('app.eventDateTimeFormat')).' '.$timezoneCode : null) . ($this->end_datetime ? ' - '.$this->end_datetime->format(config('app.eventDateTimeFormat')).' '.$timezoneCode : null),
        );
    }
    public function eventStartDateTime():Attribute{
        $timezoneCode = $this->event_timezone?'('.config('app.timezonescode')[$this->event_timezone].')':'';
        return Attribute::make(
            get: fn () => (isset($this->start_datetime) ? $this->start_datetime->format(config('app.eventDateTimeFormat')).' '.$timezoneCode : null),
        );
    }
    public function eventEndDateTime():Attribute{
        $timezoneCode = $this->event_timezone?'('.config('app.timezonescode')[$this->event_timezone].')':'';
        return Attribute::make(
            get: fn () => ($this->end_datetime ? $this->end_datetime->format(config('app.eventDateTimeFormat')).' '.$timezoneCode : null),
        );
    }
    public function waiverFormContentFilter(){
        $content = "";
        if($this->waiver_form_content){
            if($this->waiver_form_type == null)
                $content = '';
            elseif($this->waiver_form_type == 'pdf'){
                $decoded_content = json_decode($this->waiver_form_content, true);
                $content = $decoded_content['path'];
            }else
                $content = $this->waiver_form_content;
        }
        return $content;
    }
    public function fullAddress():Attribute{
        $add_array = [$this->venue , $this->pincode, $this->country?->country_name];
        return Attribute::make(
            get: fn () => implode(', ', array_filter($add_array, fn($v) => !empty($v))),
        );
    }
    public function country(){
        return $this->belongsTo(Country::class, 'country_id' , 'country_id');
    }
    public function scopeDrafted($query): void{
        $query->where('status', '=', 'draft');
    }
    public function scopePublished($query): void{
        $query->where('status', '=', 'published');
    }
    public function scopeArchived($query): void{
        $query->where('status', '=', 'archived');
    }
    public function scopeListactive($query): void{
        // Apply active status and only events whose end date >= today
        $query->detailActive()->where('status', 'published');
            // ->whereDate('end_datetime', '>=', now());

        // Apply filter box queries if present
        if (request()->filled('event_category_id')) {
            $query->whereIn('event_category_id', request('event_category_id'));
        }
        
        // Distance filter (if present)
        
        if (request()->filled('location') && request()->filled('distance') && request()->filled('latitude') && request()->filled('longitude')) {
            
            $unit = request('unit', 'km');
            $radius = request('distance');
            $lat = request('latitude');
            $lng = request('longitude');
            $earthRadius = ($unit === 'km') ? 6371 : 3956; // km or miles

            $query->selectRaw("*, (
                    $earthRadius * acos(
                        cos(radians(?)) * cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )
                ) AS distance", [$lat, $lng, $lat])
            ->having('distance', '<=', $radius);
            //->orderBy('distance');


            /* $distance = request('distance');
            // Get latitude and longitude from request
            $latitude = request('latitude');
            $longitude = request('longitude');

            $unit = request('unit', 'km'); // 'km' for kilometers, 'mi' for miles
            // Earth's radius in kilometers (6371) or miles (3959)
            $earthRadius = ($unit === 'mi') ? 3959 : 6371;



            $haversine = "($earthRadius * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude))))";
            $query->selectRaw("*, $haversine AS distance")
                ->having('distance', '<=', $distance)
                ->orderBy('distance'); */
        }
    

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
            $q->where('event_name', 'like', "%$search%")
              ->orWhere('venue', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%");
            });
        }

        if (request()->filled('start_date')) {
            $query->whereDate('start_datetime', '>=', request('start_date'));
        }
        if (request()->filled('end_date')) {
            $query->whereDate('end_datetime', '<=', request('end_date'));
        }

        if (request()->filled('sort_by_date')) {
            if(request('sort_by_date')==1){
                // Sorting logic as per requirements
                $query->orderBy('start_datetime', 'asc');
            } else {
                // Sorting logic as per requirements
                $query->orderBy('start_datetime', 'desc');
            }
        } else {
            // Sorting logic as per requirements
            $query->orderBy('start_datetime', 'asc');
        }
        /* if (request()->filled('sort_by_date')) {
            if(request('sort_by_date')==1){
                // Sorting logic as per requirements
                $query->orderBy('start_datetime', 'asc')      // Earliest upcoming start date/time first
                ->orderBy('end_datetime', 'asc')        // Shorter duration (earlier end time) first
                ->orderBy('site_publish_date', 'desc')  // Most recently published first
                ->orderBy('id', 'desc');                // Newest event if all else equal
            } else {
                // Sorting logic as per requirements
                $query->orderBy('start_datetime', 'desc')      // Earliest upcoming start date/time first
                ->orderBy('end_datetime', 'desc')        // Shorter duration (earlier end time) first
                ->orderBy('site_publish_date', 'asc')  // Most recently published first
                ->orderBy('id', 'asc');                // Newest event if all else equal
            }
        } else {
            // Sorting logic as per requirements
            $query->orderBy('start_datetime', 'asc')      // Earliest upcoming start date/time first
            ->orderBy('end_datetime', 'asc')        // Shorter duration (earlier end time) first
            //->orderBy('site_publish_date', 'desc')  // Most recently published first
            ->orderBy('id', 'desc');                // Newest event if all else equal
        } */
        
    }
    public function scopeDetailactive($query): void{
        $query->active()->where(function($q){
            $q->whereDate('site_publish_date', '<=', date('Y-m-d'))
                ->whereDate('site_depublish_date', '>=', date('Y-m-d'));
        });
    }
    public function isDetailActive(){
        return $this->is_active && $this->site_publish_date <= date('Y-m-d') && $this->site_depublish_date >= date('Y-m-d'); 
    }
    public function scopePast($query){
        $query->detailactive()->where('end_datetime', '<', now());
    }
    public function scopePresent($query){
        $query->detailactive()->where('start_datetime', '<=', now())
              ->where('end_datetime', '>=', now());
    }
    public function scopeFuture($query){
        $query->detailactive()->where('start_datetime', '>', now());
    }
    public function afterActionDone(){
        return $this->area_cleaned && $this->waste_collected;
    }
    public function creator(){
        //return $this->morphTo();
        return $this->belongsTo(Volunteer::class, 'creator_id' , 'id');
    }
    public function category(){
        return $this->belongsTo(EventCategory::class, 'event_category_id', 'id');
    }
    public function bannerImage() : Attribute{
        return Attribute::make(
            get : fn($value) => json_decode($value ? : null ) , 
        );
    }
    public function formattedBanner(){
        if (!$this->banner_image) {
            $path = 'assets_front/images/event-imgs/big-2.jpg';
        }else{
            $path = $this->banner_image->path;
        }
        
        return asset($path);
    }
    public function flyerImage() : Attribute{ 
        return Attribute::make(
            get : fn($value) => json_decode($value ? : null ) , 
        );
    }

    public function formattedFlyerImage(){
        if (!$this->flyer_image) {
            $path = 'assets_front\images\event-imgs\big-2.jpg';
        }else{
            $path = $this->flyer_image->path;
        }
        
        return asset($path);
    }
    public function runningStatus() : Attribute{
        $now = now();
        $running_status = '';
        if ($this->start_datetime > $now) {
            $running_status = 'Upcoming';
        } elseif ($this->end_datetime < $now) {
            //$running_status = 'completed';
            //$running_status = 'Expired';
            $running_status = 'Ended';
        } else {
            $running_status = 'Ongoing';
        }
        return Attribute::make(
            get : fn() => $running_status , 
        );
    }
    
    /**order tickets */
    public function orders(){
        return $this->hasMany(EventOrder::class, 'event_id');
    }
    public function tickets(){
        return $this->hasManyThrough(
            EventOrderTicket::class,
            EventOrder::class,
            'event_id',        // Foreign key on EventOrder table...
            'order_id',  // Foreign key on EventOrderTicket table...
        );
    }
    public function attendees(){
        return $this->belongsToMany(
            Volunteer::class,
            'event_order_tickets', // Pivot table name
            'event_id',            // Foreign key on pivot table for Event
            'volunteer_id',        // Foreign key on pivot table for Volunteer
        )->withPivot(['id','order_id','event_id','volunteer_id','price','ticket_id','waiver_handler_name','waiver_handler_email','waiver_filled_at','rsvp_filled_at','rsvp_confirmation','signature','is_under_18', 'status','extra_notes','checkin', 'ticket_answer','created_at','updated_at', 'ticket_number', 'community_form_status','hours_completed','activity_description','participation_proof','additional_notes','community_form_pdf'])
        ->using(EventOrderTicket::class);
    } 
    
    /**event tickets */
    public function eventTickets(){
        return $this->hasMany(EventTicket::class, 'event_id');
    }
    public function eventActiveTickets(){
        return $this->hasMany(EventTicket::class, 'event_id')->where('is_active' , 1)->whereDate('sell_start_time', '<=', date('Y-m-d'))->whereDate('sell_end_time', '>=', date('Y-m-d'));
    }
    public function eventActiveFreeTickets(){
        return $this->hasMany(EventTicket::class, 'event_id')->where('is_active' , 1)->whereDate('sell_start_time', '<=', date('Y-m-d'))->whereDate('sell_end_time', '>=', date('Y-m-d'))->where('ticket_type' , 'free');
    }
    public function eventActivePaidTickets(){
        return $this->hasMany(EventTicket::class, 'event_id')->where('is_active' , 1)->whereDate('sell_start_time', '<=', date('Y-m-d'))->whereDate('sell_end_time', '>=', date('Y-m-d'))->where('ticket_type' , 'paid');
    }
    public function freeEventTickets(){
        return $this->eventTickets()->where('ticket_type' , 'free');
    }
    public function paidEventTickets(){
        return $this->eventTickets()->where('ticket_type' , 'paid');
    }
    public function sellTicketBooking(){
        return (strtotime($this->ticket_sell_start_time) <= strtotime(date('Y-m-d')) && strtotime($this->ticket_sell_end_time) >= strtotime(date('Y-m-d')));
        /* return (
                // !$this->is_private_ticket && 
                $this->ticket_sell_start_time <= date('Y-m-d h:i:s') && $this->ticket_sell_end_time >= date('Y-m-d h:i:s')); */
    }
    /**other details */
    public function tags(){
        return $this->belongsToMany(Tag::class , 'event_tags' , 'event_id' , 'tag_id');
    }
    public function volunteertags(){
        return $this->belongsToMany(Tag::class , 'event_volunteer_tags' , 'event_id' , 'tag_id');
    }
    public function tools(){
        return $this->belongsToMany(Tool::class, 'event_tools', 'event_id', 'tool_id')->withPivot(['should_bring']);
    }
    public function cohosts(){
        return $this->belongsToMany(Volunteer::class , 'event_cohost', 'event_id' , 'volunteer_id' )->withPivot(['id','volunteer_id', 'extra_notes', 'created_at', 'request_status']);
    }
    public function cohostRecords(){
        return $this->hasMany(EventCoHost::class, 'event_id');
    }
    public function attandee(){
        return $this->belongsToMany(Volunteer::class , 'event_order_tickets', 'event_id' , 'volunteer_id' );
    }
    public function bringTools(){
        return $this->tools()->where('event_tools.should_bring' , 1);
    }
    public function providedTools(){
        return $this->tools()->where('event_tools.should_bring' , 0);
    }
    public function eventPermissions(){
        return $this->hasManyThrough(
            CohostPermission::class, // Final model
            EventCoHost::class,      // Intermediate model
            'event_id',              // Foreign key on EventCoHost table...
            'cohost_record_id',       // Foreign key on CohostPermission table...
        );
    }
}

