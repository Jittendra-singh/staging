<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;
use App\Traits\HasTranslations;
class Tool extends Model
{
    use HasFactory, HelperTrait, HasTranslations;

    protected $guarded = ['id'];

    protected $translatable = ['name'];

    public function events(){
        return $this->belongsToMany(Event::class, 'event_tools', 'tool_id', 'event_id');
    }
}
