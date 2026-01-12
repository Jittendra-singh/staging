<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CohostPermission extends Model
{
    use HasFactory;
    protected $table = "cohosts_permissions";
    protected $guarded = ['id'];
    public function eventCohostRecord(){
        return $this->belongsTo(EventCoHost::class, 'cohost_record_id');
    }
    public function cohost(){
        return $this->eventCohostRecord->belongsTo(Volunteer::class, 'volunteer_id');
    }
}
