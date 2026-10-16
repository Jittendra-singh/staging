<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\HelperTrait;
class EventOrder extends Model
{
   use HasFactory, SoftDeletes, HelperTrait;
    protected $guarded = ['id'];
    protected $table = 'event_orders';
    public function event(){
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function creator()
    {
        return $this->belongsTo(Volunteer::class, 'creator_id');
    }
    public function tickets()
    {
        return $this->hasMany(EventOrderTicket::class, 'order_id');
    }
    public function children()
    {
        return $this->hasMany(EventOrderTicketChild::class, 'order_id');
    }
}
