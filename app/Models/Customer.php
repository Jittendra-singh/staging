<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;

class Customer extends Authenticatable
{
    use HasFactory , SoftDeletes, HelperTrait;

    public $incrementing = false;
    
    protected $fillable = [
        'referral_id',
        'name',
        'phone',
        'email',
        'password',
        'photo',
        'email_verified_at',
        'address',
        'city',
        'state',
        'country',
        'zipcode',
        'is_active'
    ];
    public function bookings(){
        return $this->hasMany(Booking::class,'customer_id');
    }
    public function luggage_bookings(){
        return $this->hasMany(LuggageBooking::class,'customer_id');
    }

}