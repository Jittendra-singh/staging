<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventTicketNotification extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];
    protected $table = 'event_ticket_notifications';

    public function volunteer(){
        return $this->belongsTo(Volunteer::class , 'volunteer_id');
    }
    public function ticket(){
        return $this->belongsTo(EventTicket::class , 'ticket_id');
    }
}
