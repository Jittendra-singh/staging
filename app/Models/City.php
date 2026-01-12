<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class City extends Model
{
    use HasFactory;
    protected $primaryKey = 'city_id';
    protected $guarded = ['city_id'];
    protected $table = 'cities';    
    public function state(){
        return $this->belongsTo(State::class , 'state_id', 'state_id');
    }
}
