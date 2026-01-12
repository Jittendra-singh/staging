<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class AuthController extends Controller
{
    public function loginView()
    {
        //return view('login');
        return view('auth-login');
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users',
            'password' => 'required'
        ]);
        $validated = $request;
        if (\Auth::guard('web')->attempt(['email' => $validated['email'], 'password' => $validated['password'] ,'is_active' => 1])) {
            $row = User::findOrFail(Auth::guard('web')->user()->id);
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('login')->with("error" , "Opps! Seems like Credentials are not Correct.");
        }
    }
    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }
    public function registerView()
    {
        return view('register');
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:companies'],
            'password' => ['required', 'confirmed', Password::min(7)],
        ]);
        $validated = $validator->validated();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        auth()->login($user);
        return redirect()->route('index');
    }
    public function forgotPasswordView(){
        return view('forgot-password');
    }
    public function forgotPasswordEmail(){
        request()->validate([
            'recover_email' => 'required|email|exists:users',
        ]);
        $this->setFlashSession(true , 'Great! Please follow the Email that We have just Sent You.');
        return redirect()->route('forgotPassword');
    }
    public function resetPasswordView($id){
        return view('reset-password');
    }
    public function resetPassword($id){
        request()->validate([
            'password' => 'required|confirmed',
        ]);
        $user_id = decrypt($id);
        $user = User::findOrFail($user_id);
        $user->password = Hash::make(request()->password);
        $res = $user->save();
        $this->setFlashSession($res);
        if($res){
            return redirect()->route('login');
        }
        return redirect()->route('reset-password' , ['id' => $id]);
    }
}