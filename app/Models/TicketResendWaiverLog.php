<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketResendWaiverLog extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];
    protected $table = 'ticket_resend_waiver_logs';

    public function ticket(){
        return $this->belongsTo(EventOrderTicket::class , 'ticket_id');
    }
}
