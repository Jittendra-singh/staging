<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HelperTrait;  

class RoleMenuPermission extends Model
{
    use HasFactory, HelperTrait;

    protected $fillable = [
        'role_id' , 
        'menu_id' , 
        'permission_id'
    ];
}
