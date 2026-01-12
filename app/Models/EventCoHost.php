<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class EventCoHost extends Model
{
    use HasFactory;
    protected $table = "event_cohost";
    public function permissions(){
        return $this->hasMany(CohostPermission::class, 'cohost_record_id');
    }    
    public function event(){
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function volunteer(){
        return $this->belongsTo(Volunteer::class, 'volunteer_id');
    }
}
