<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class VolunteerWidget extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];

    public function volunteer(){
        return $this->belongsTo(Volunteer::class , 'volunteer_id');
    }
    public function view_logs(){
        return $this->hasMany(VolunteerWidgetLog::class , 'widget_id')->where('log_type', 1);
    }
    public function click_logs(){
        return $this->hasMany(VolunteerWidgetLog::class , 'widget_id')->where('log_type', 2);
    }
    public function order_logs(){
        return $this->hasMany(VolunteerWidgetLog::class , 'widget_id')->where('log_type', 3);
    }
}
