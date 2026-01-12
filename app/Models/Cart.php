<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Cart extends Model
{
    use HasFactory, HelperTrait;
    //protected $guarded = ['id'];
    protected $table = 'carts';
    protected $fillable = [
        'event_id',
        'request_data',
        'order_details',
        'ticket_data',
    ];

    public function event(){
        return $this->belongsTo(Event::class , 'event_id');
    }
}
