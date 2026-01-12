<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
class Tool extends Model
{
    use HasFactory, HelperTrait;
    protected $guarded = ['id'];

    public function events(){
        return $this->belongsToMany(Event::class, 'event_tools', 'tool_id', 'event_id');
    }
}
