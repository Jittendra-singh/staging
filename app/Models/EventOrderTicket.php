<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;
class EventOrderTicket extends Pivot
{
    use HasFactory, HelperTrait;
    protected $guarded    = ['id'];
    
    protected $primaryKey = 'id'; // Explicitly define the primary key
    public $incrementing  = true;  // Enable auto-incrementing for the primary key
    protected $keyType    = 'int';
    
    // Override getKeyName to ensure it returns the correct column
    public function getKeyName(){
        return 'id';
    }

    // Ensure getKey returns the correct value
    public function getKey(){
        return $this->getAttribute($this->getKeyName());
    }

    protected $table = 'event_order_tickets';
    protected $casts = [
                        'created_at' => 'datetime',
                        'updated_at' => 'datetime',
                        'waiver_filled_at'=> 'datetime',
                    ];
    /**scopes */
    public function scopeActive($query){
        //return $query->where('ticket_status','approved');
        return $query->where('rsvp_confirmation',1);
    }
    public function scopeCancelled($query){
        //return $query->where('ticket_status','cancelled');
        return $query->where('rsvp_confirmation',0);
    }

    public function scopeNew($query){
        return $query->whereBetween('created_at', [now()->subDays(7), now()]);
    }
    public function scopeFree($query){
        return $query->whereHas('ticket', function($q){
            $q->where('ticket_type', 'free');
        });
    }
    public function scopePaid($query){
        return $query->whereHas('ticket', function($q){
            $q->where('ticket_type', 'paid');
        });
    }
    /**Builder */
    public function scopeGetVolunteerRegistrationLists($query){
        return $query->where(function($q){
                    if (request()->event) {
                        $q->where('event_order_tickets.event_id', request()->event);
                    }
                    if (request()->status) {
                        $status = request()->status;
                        $q->$status();
                    }
                    if (request()->search) {
                        $q->whereHas('volunteer', function($subQ){
                            $search = request()->search;
                            $subQ->where("first_name" , "LIKE" ,"%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email_address', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                    }
                    if (request()->event_type) {
                        $q->whereHas('event', function($subQ){
                            $now = now();
                            if(request()->event_type==1){
                                $subQ->where("start_datetime" ,">",$now);
                            }elseif(request()->event_type==2){
                                $subQ->where("end_datetime" , "<",$now);
                            }elseif(request()->event_type==3){
                                $subQ->where("start_datetime", "<=", $now)
                                ->where("end_datetime", ">=", $now);
                            }
                        });
                    }
                });
    }
    /**relations */
    public function eventOrder()
    {
        return $this->belongsTo(EventOrder::class, 'order_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'volunteer_id');
    }
    public function certificateApproverRejectBy()
    {
        return $this->belongsTo(Volunteer::class, 'certificate_approve_reject_by');
    }
    public function ticket()
    {
        return $this->belongsTo(EventTicket::class, 'ticket_id');
    }
    public function notes(){
        return $this->hasMany(OrderTicketNotes::class, 'order_ticket_id');
    }
    public function resendWaiverLogs(){
        return $this->hasMany(TicketResendWaiverLog::class, 'ticket_id');
    }
    /**attributes */
    public function communityFormPdf() : Attribute{
        return new Attribute(
            get : fn($value) => $value ? asset($value) : null , 
        );
    }
    
}
