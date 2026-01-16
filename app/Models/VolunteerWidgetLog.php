<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class VolunteerWidgetLog extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];

    public function widget(){
        return $this->belongsTo(VolunteerWidget::class , 'widget_id');
    }

    public function events(){
        return $this->belongsTo(Event::class , 'reference_id');
    }

    public function orders(){
        return $this->belongsTo(EventOrder::class , 'reference_id');
    }
}
