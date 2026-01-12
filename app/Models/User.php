<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\Traits\HelperTrait;

class User extends Authenticatable
{
    use SoftDeletes , HasApiTokens, HasFactory, Notifiable, HelperTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public $incrementing = false;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'experience',
        'email_verified_at',
        'password',
        'remember_token',
        'is_active',
        'accessibility',
        'machine_updation_permission'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */

    protected $hidden = [
        'password',
        'remember_token',
        'pivot'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [

        'email_verified_at' => 'datetime',

    ];

    public function role(){

        return $this->belongsTo(Role::class);

    }

}
