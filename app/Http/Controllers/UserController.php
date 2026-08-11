<?php
namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\User;
use App\Models\Machine;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    public function index()
    {
        Gate::authorize('view', 'user');
        /* return view('pages.user.view-user-old', [
            'listingData' => User::whereNotIn('role_id', [1])
                            ->order()
                            ->get()
                            ->map(function ($user) {
                                $user->id = encrypt($user->id);
                                return $user;
                            }),
        ]); */
        return view('pages.user.view-user', [
            'listingData' => User::whereNotIn('role_id', [1])
                            ->orderBy('created_at','desc')
                            ->get()
                            ->map(function ($user) {
                                $user->id = encrypt($user->id);
                                return $user;
                            }),
        ]);
    }
    public function showForm($user_placeholder = null)
    {
        if ($user_placeholder) {
            Gate::authorize('view', 'user');
            $data = User::findOrFail(decrypt($user_placeholder));
            $data->id = encrypt($data->id);
        } else {
            Gate::authorize('create', 'user');
        }
        return view('pages.user.manage-user', [
            'formData' => $data ?? null,
            'roles' =>  Role::orderBy('role_name')->get()->except([1])->map(function ($role) {
                                $role->id = encrypt($role->id);
                                return $role;
                        }),
        ]);
    }
    public function toggleStatus($user_placeholder){
        if(Gate::allows('update' , 'user')){
            $user = User::findOrFail(decrypt($user_placeholder));
            $user->is_active = !$user->is_active ; 
            $this->setFlashSession($user->save());
            if(!$user->is_active){
                    DB::table('personal_access_tokens')->where("tokenable_id" , $user->id)->delete();
            }
        }
        return redirect()->route('index-user');
    }
    public function manage(Request $request, $id = null)
    {
        if (!$id) {
            $user = new User();
            request()->validate([
                'password' => 'required',
                'role_id' => 'required',
                'name' => 'required',
                'email' => 'required|unique:users,email',
            ]);
            Gate::authorize('create' ,  'user');
            $user->is_active = 1;
            $user->password = Hash::make(request()->password);
            $user->created_by = Auth::guard('web')->user()->id; 
        }else{
            $user = User::findOrFail(decrypt($id));
            $user_id = decrypt($id);
            request()->validate([
                'role_id' => 'required',
                'name' => 'required',
                'email' => "required|unique:users,email,{$user_id}",
            ]);
            Gate::authorize('update' ,  'user');
            // $user->updated_by = Auth::guard('web')->user()->id; 
        }
        try{
            DB::beginTransaction();
                $role_id                         = decrypt(request()->role_id);
                $user->name                      = request()->name;
                $user->email                     = request()->email;
                // $user->phone                     = request()->phone;
                $user->role_id                   = $role_id;
                $res                = $user->save();
                $sffected_user      = $id ? $user : User::latest()->first();
            DB::commit();
            $this->setFlashSession(true);            
            return redirect()->route('index-user'); 
        }catch(Exception $e){
            $this->setFlashSession(false);
            return redirect()->route('show-user' ,  ['user_placeholder' => $id]);
        }
    }
    public function updatePassword($placeholder)
    {
        request()->validate([
            'password' => 'required|confirmed',
        ]);
        Gate::authorize('update', 'user');
        $user = User::findOrFail(decrypt($placeholder));
        $user->password = Hash::make(request()->password);
        $this->setFlashSession($user->save());
        return redirect()->route('index-user');
    }
    public function delete($user_placeholder)
    {
        Gate::authorize('delete', 'user');
        $user = User::findOrFail(decrypt($user_placeholder));
        $res = $user->delete();
        return $this->generateJsonResponse($res);
    }
    public function  viewUpdatePassword(){
        Gate::authorize('update-profile' , 'user');
        return view('pages.user.update-profile');
    }
    public function  updateProfile(){
        Gate::authorize('update-profile' , 'user');
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.auth()->user()->email,
        ]);
        auth()->user()->name = request()->name;
        auth()->user()->email = request()->email;
        if(request()->password)
            auth()->user()->password = Hash::make(request()->password);
        $this->setFlashSession(auth()->user()->save());
        return redirect()->route('index-user');
    }
    public function settings()
    {
        Gate::authorize('view', 'settings');
        $data = Setting::findOrFail(1);
        return view('pages.settings', [
            'formData' => $data ?? null,
        ]);
    }
    public function update_settings()
    { 
        request()->validate([
            'payment_processing_fee_per' => 'required',
            'event_commission' => 'required',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        Gate::authorize('update', 'settings');
        $data = Setting::findOrFail(1);
        $data->payment_processing_fee_per = request()->payment_processing_fee_per;
        $data->event_commission = request()->event_commission;
        $data->waiver_text = request()->waiver_text;
        // if(request()->hasFile('logo')){
        //     $logo = $this->uploadFiles('logo', 'assets_front/images');
        //     $data->logo = $logo['path'];
        // }
        $data->save();
        $this->setFlashSession($data->save());
        return redirect()->route('settings');
    }
}
