<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class CityStateZip extends Model
{
    use HasFactory;
    protected $table = 'city_state_zips';    
    protected $guarded = ['id'];
}
