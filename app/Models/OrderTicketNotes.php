<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
class OrderTicketNotes extends Model
{ 
    use HasFactory, HelperTrait;
    protected $table = "order_ticket_notes" ;
    protected $guarded = ['id'];
    public function ticket(){
        return $this->belongsTo(EventOrderTicket::class, 'order_ticket_id');
    }
}
