<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventRescheduleLog extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];
    protected $table = 'event_reschedule_logs';

    public function user(){
        return $this->belongsTo(Volunteer::class , 'user_id');
    }
    public function event(){
        return $this->belongsTo(Event::class , 'event_id');
    }
}
