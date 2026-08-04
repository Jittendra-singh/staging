<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;
class EventOrderTicketChild extends Pivot
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

    protected $table = 'event_order_ticket_childs';
    protected $casts = [
                        'created_at' => 'datetime',
                        'updated_at' => 'datetime',
                    ];
    /**relations */
    public function eventOrder()
    {
        return $this->belongsTo(EventOrder::class, 'order_id');
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function parent_ticket()
    {
        return $this->belongsTo(EventOrderTicket::class, 'event_order_ticket_id');
    }
    
}
