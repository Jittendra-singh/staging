<?php
namespace App\Http\Controllers;
use App\Models\RoleMenuPermission;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Menu;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
class MenuController extends Controller
{
    public function permissionPage(){
        Gate::authorize('view' ,  'permission');
        $data = [];
        $data['menus'] = Menu::all();
        $data['roles'] = Role::all()->except(1);
        return view('pages.menu.permission', $data);
    }
    public function setPermissions(){
        Gate::authorize('update' ,  'permission');
        $permissions = request()->permission ?? [];
        $insert_array = [];
        RoleMenuPermission::truncate();
        try{
            DB::beginTransaction();
                foreach ($permissions as $permission=>$value) {
                    $exploded = explode('_' , $permission);
                    RoleMenuPermission::create([
                        'role_id'       => $exploded[0],
                        'menu_id'       => $exploded[1],
                        'permission_id' => $exploded[2],
                    ]);
                }
            DB::commit();
            $this->setFlashSession(true);
        }catch(\Exception $e){
            $this->setFlashSession(false);
        }
        return redirect(config('app.admin_prefix').'permissions');
    }
}