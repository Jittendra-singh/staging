<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Casts\Attribute;

use App\Models\Traits\HelperTrait;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use SoftDeletes , HasApiTokens, HasFactory, Notifiable, HelperTrait, Impersonate;

    public function canImpersonate(): bool
    {
        return $this->role_id == 1 && $this->is_active == 1;
    }

    public function canBeImpersonated(): bool
    {
        return false;
    }
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
