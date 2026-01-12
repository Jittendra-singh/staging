<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Traits\HelperTrait;
class Role extends Model
{
    use HasFactory , SoftDeletes, HelperTrait;
    protected $casts = [
        'id' => 'string',
    ];
    protected $fillable = [
        'role_name',
        'is_active'
    ];
    public function parentMenus(){
        return $this->belongsToMany(Menu::class , 'role_menu_permissions' , 'role_id' , 'menu_id' )->where('menu_parent' , '0')->where('permission_id' , 1)->orderBy('order');
    }
    public function users(){
        return $this->hasMany(User::class);
    }
    public function permissions(){
        return $this->belongsToMany(Menu::class , 'role_menu_permissions' , 'role_id' , 'menu_id');
    }
}