<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class VolunteerAlert extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];

    public function volunteer(){
        return $this->belongsTo(Volunteer::class , 'volunteer_id');
    }
    public function location(){
        return $this->belongsTo(CityStateZip::class , 'csz_id');
    }
    public function statusUpdatedByVolunteer()
    {
        return $this->belongsTo(Volunteer::class, 'status_updated_by');
    }

    public function statusUpdatedByAdmin()
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }

}
