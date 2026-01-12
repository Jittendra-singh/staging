<?php
namespace App\Models;

use App\Models\Traits\HelperTrait;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StripeLog extends Model
{
    use HasFactory, HelperTrait;
    //protected $guarded = ['id'];
    protected $table = 'stripe_logs';

    public function order(){
        return $this->belongsTo(EventOrder::class , 'order_id');
    }
}
