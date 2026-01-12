<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
class RoleController extends Controller
{
    public function index()
    {
        Gate::authorize('view','role');
        return view('pages.role.view-role', [
            'listingData' => Role::order()->get()->except(1)->map(function($role){
                                $role->id = encrypt($role->id);
                                return $role;    
                            }),
        ]);
    }
    public function showForm($role_placeholder = null)
    {
        if($role_placeholder){
            $data       =  Role::findOrFail(decrypt($role_placeholder));
            $data->id   =  encrypt($data->id);
            Gate::authorize('view' , 'role');
        }else{
            Gate::authorize('create' ,  'role');
        }
        return view('pages.role.manage-role', ['formData' => $data ?? null]);
    }
    public function manage($role_placeholder = null){
        if (!$role_placeholder) {
            $role = new Role();
            request()->validate([
                'role_name' => 'required|string|unique:roles,role_name',
            ]);
            Gate::authorize('create' ,  'role');
            $role->is_active = 1;
            $role->created_by = Auth::guard('web')->user()->id; 
        }else{
            $role = Role::findOrFail(decrypt($role_placeholder));
            request()->validate([
                'role_name' => "required|string|unique:roles,role_name,$role->id",
            ]);
            Gate::authorize('update' ,  'role');
            $role->updated_by = Auth::guard('web')->user()->id; 
        }
        $role->role_name = request()->role_name;
        $res = $role->save();
        $this->setFlashSession($res);
        if($res)
            return redirect()->route('index-role');
        return redirect()->route('manage-role' , ['role' => $role_placeholder]);
    }
    public function toggleStatus($role_placeholder){
        if(Gate::allows('update' , 'role')){
            $role = Role::findOrFail(decrypt($role_placeholder));
            $role->is_active = !$role->is_active ; 
            $role->updated_by = Auth::guard('web')->user()->id; 
            $this->setFlashSession($role->save());
        }
        return redirect()->route('index-role');
    }
    public function delete($role_placeholder){
        Gate::authorize('delete','role');
        $role = Role::withCount('users')->findOrFail(decrypt($role_placeholder));
        if($role->users_count)
            return $this->generateJsonResponse(false , 'Opps! Some users are connected with this role, we can\'t delete this.');    
        $res = $role->delete();
        return $this->generateJsonResponse($res);
    }
}