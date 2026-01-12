<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
class Tag extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];
    protected $table = 'tags';

    public function events(){
        return $this->belongsToMany(Event::class, 'event_tags', 'tag_id', 'event_id');
    }
    public function events2(){
        return $this->belongsToMany(Event::class, 'event_volunteer_tags', 'tag_id', 'event_id');
    }
}
