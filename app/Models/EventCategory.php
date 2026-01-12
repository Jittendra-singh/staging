<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
class EventCategory extends Model
{
    use HasFactory , HelperTrait;
    protected $guarded = ['id'];
    protected $table = 'event_categories';

    public function events(){
        return $this->hasMany(Event::class, 'event_category_id');
    }
}
