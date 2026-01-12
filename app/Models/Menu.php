<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;

class Menu extends Model
{
    use HasFactory, HelperTrait;

    protected $fillable = [
        'menu_name', 
        'menu_href',
        'menu_parent',
        'menu_icon',
        'menu_placeholder',
        'menu_permissions',
        'is_active'
    ];

    protected $with = ['childMenus'];

    public function parentMenu(){
            return $this->belongsTo(Self::class , 'menu_parent' , 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class , 'role_menu_permission' , 'menu_id' , 'role_id' );
    }

    public function childMenus(){

        if(auth()->user()->role_id == 1){
            return $this->hasMany(Self::class , 'menu_parent' , 'id');
        }

        return $this->hasMany(Self::class , 'menu_parent' , 'id')->whereHas('permission');
    }

    public function permission(){
        if(auth()->user()->role_id == 1){
            return $this->hasMany(RoleMenuPermission::class , 'menu_id');
        }
        else{
            return $this->hasMany(RoleMenuPermission::class , 'menu_id')->where('permission_id' , '1')->where('role_id' , auth()->user()->role_id);
        }
    }

    protected function menuPermissions(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? explode(',' , $value) : null ,
        );
    }
    
}