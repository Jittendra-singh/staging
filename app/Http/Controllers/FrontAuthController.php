<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

use App\Models\Volunteer;
use App\Models\PasswordReset;
use App\Models\CityStateZip;
use Illuminate\Support\Str;

class FrontAuthController extends Controller
{
    public function login()
    {
        $seo_details = (object)['title' => 'Login'];
        return view('pages.frontend.login', ['seo_details' => $seo_details]);
    }

    public function register()
    {
        $seo_details = (object)['title' => 'Registation'];
        return view('pages.frontend.register', ['seo_details' => $seo_details]);
    }

    public function forgotPassword(){
        $seo_details = (object)['title' => 'Forgot Password'];
        return view('pages.frontend.forgot-password', ['seo_details' => $seo_details]);
    }

    public function resetPasswordEmail(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email|exists:volunteers,email_address', 
        ]);

        $volunteer = Volunteer::where('email_address',$request->email_address)->first();
        if (!isset($volunteer->id)) {
            $this->setFlashSession(false, 'No account found with that email address.');
            return redirect()->back()->withInput();
        }
        if ($volunteer->status == 0) {
            $this->setFlashSession(false, 'You don\'t have permission to reset password. Please contact the admin.');
            return redirect()->back()->withInput();
        }
        if (empty($volunteer->email_verified_at)) {
            $this->sendActivationLink(encrypt($volunteer->id),true);
            $this->setFlashSession(false, 'Your account is not activated. We\'ve sent you a new activation link.');
            return redirect()->back()->withInput();
        }
        if($volunteer){

            PasswordReset::where('email',request()->email_address)->delete();

            $random = Str::random(64);
            $pwReset = new PasswordReset();
            $pwReset->email = $request->email_address;
            $pwReset->token = $random;
            $pwReset->save();
            
            
            $mail_content = [
                            'volunteer' => $volunteer->name,  
                            'link' => $this->mailUrl('resetPassword',['token'=>$random])
                        ];
                        
            $this->sendEmail($volunteer->email_address, FRONT_RESET_PASS_SUB, $mail_content, "forgot-password");
                
            $this->setFlashSession(true, 'We\'ve sent you a password reset link. Please check your email to reset your password.');  
            return redirect()->route('front-login');

        }else{
            $this->setFlashSession(false, 'No account found with that email address.');
            return redirect()->back()->withInput();
        }
    }

    public function registerVolunteer( Request $request )
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email_address' => ['required','email','unique:volunteers,email_address', new \App\Rules\StrictEmail()],
            'password' => 'required|min:4',
            'organization_association_name' => 'required_if:organization_association,1|max:255',
            'register_pincode' => 'required_if:weekly_alerts,1|max:255',
        ], [
            'email_address.unique' => 'This email is already registered. <a href="'.route('front-login').'">Log in here</a> to continue.',
        ]);

        $volunteer = new Volunteer();
        $volunteer->first_name = $request->first_name;
        $volunteer->last_name = $request->last_name;
        $volunteer->email_address = $request->email_address;
        $volunteer->password = Hash::make($request->password);
        $volunteer->is_organiser = $request->organization_association ?? 0;
        $volunteer->organisation_landing_slug = isset($request->organization_association) ? 
            $this->generateSlug(Volunteer::class, 'organisation_landing_slug', $request->organization_association_name ? : 'n-a', 0)
        :  '';
        $volunteer->organisation_name = $request->organization_association_name ?? '';
        $volunteer->is_newsletter = $request->is_newsletter ?? 0;
        $volunteer->is_weekly_alert_sent = $request->weekly_alerts ?? 0;
        //$volunteer->status = 0; // Assuming status 0 means inactive
        
        $pincode = '';
        $cityStateZip = CityStateZip::find(@request()->register_pincode);
        if(isset($cityStateZip->id)){
            $pincode = $cityStateZip->zipcode;
        }
        $volunteer->pincode = $pincode;
        $volunteer->city_id = $request->register_pincode ?? 0;
        $res = $volunteer->save();
        $volunteer->refresh();

        // create weekly alert if checkbox of weekly alert checked on the reigstration page 
        if($request->has('weekly_alerts') && $request->weekly_alerts == 1 && !empty($pincode)){
            (new FrontOrderController)->createWeeklyAlert(
                $volunteer->id,
                'Weekly Cleanups Near '.$pincode,
                $pincode,
                3
            );
        }
         
        if (!$res) {
            $this->setFlashSession(false, 'There was an error while creating your account. Please try again.');
            return redirect()->back()->withInput();
        }else{
            /**send the mail here */
            $mail_content = [
                'volunteer'         => $volunteer->first_name . ' ' . $volunteer->last_name,
                'email'             => $volunteer->email_address,
                'verification_link' => $this->mailUrl('front-verify-email', ['id' => $volunteer->encrypted_id,'timestamp' => encrypt(now()->timestamp)])
            ];
            $this->sendEmail($volunteer->email_address, VOLUNTEER_REG_BY_FRONTEND_SUB, $mail_content, "confirm-registration");
            if(isset($request->is_newsletter) && $request->is_newsletter==1){
                $this->syncWithWP($volunteer,'Register');
            }
            
            $this->setFlashSession(true, 'Your account has been created successfully. We will activate your account once you verify your email address.');
        }

        return redirect()->route('front-login');

    }

    public function verifyEmail($id,$timestamp){
        $volunteer = Volunteer::findOrFail(decrypt($id));
        $tokenCreatedAt = \Carbon\Carbon::createFromTimestamp(decrypt($timestamp));
        if(now()->diffInHours($tokenCreatedAt) > 24){
            $this->setFlashSession(false, 'Your verification link has expired. Please request a new one from login page.');
            return redirect()->route('front-login');
        }

        if($volunteer->email_verified_at == null){
            $volunteer->email_verified_at = now();
            //$volunteer->status = 1;
            $volunteer->save();
            $this->setFlashSession(true, 'Your email address has been verified successfully.');
        }else{
            $this->setFlashSession(false, 'Your email address has already been verified.');
        }
        return redirect()->route('front-login');
        
    }

    public function handleLogin(Request $request){
        $request->validate([
            'email' => 'required|exists:volunteers,email_address',
            'password' => 'required'
        ]);
        $validated = $request;
        $user = Volunteer::where('email_address', $validated['email'])->first();
        if (!isset($user->id)) {
            $this->setFlashSession(false, 'No account found with the provided email address.');
            return redirect()->route('front-login');
        }
        if ($user->status!=1) {
            $this->setFlashSession(false, 'You don\'t have permission to login. Please contact admin.');
            return redirect()->route('front-login');
        }
        if (empty($user->email_verified_at)) {
            $this->setFlashSession(false, 'Your account is not active. <a href="'.route('send-activation-link', encrypt($user->id)).'">Click here</a> to receive an activation link.');
            return redirect()->route('front-login');
        }

        if (\Auth::guard('front')->attempt([
                                            'email_address' => $validated['email'], 
                                            'password' => $validated['password'] ,
                                            'status' => 1
                                        ])) {
            //return redirect()->route('front-post-a-event');
            //return redirect()->route('front-dashboard');
            return redirect()->intended(route('front-dashboard'));

        } else {
            $this->setFlashSession(false, 'Incorrect email or password. Please try again with the correct credentials.');
            return redirect()->route('front-login');
        }
    }
    public function sendActivationLink($id,$useByInsideFunc=false){
        $user = Volunteer::findOrFail(decrypt($id));
        if(!empty($user->email_verified_at)){
            $this->setFlashSession(false, 'Your account has already been activated.');
            return redirect()->route('front-login');
        }
        $mail_content = [
            'volunteer'         => $user->first_name . ' ' . $user->last_name,
            'email'             => $user->email_address,
            'verification_link' => $this->mailUrl('activate-account', ['id' => encrypt($user->id),'timestamp' => encrypt(now()->timestamp)])
        ];
        $this->sendEmail($user->email_address, ACTIVATE_ACCOUNT_MAIL_SUB, $mail_content, "activate-account");
        if(!$useByInsideFunc){
            $this->setFlashSession(true, 'Your activation link has been sent to your email address. Please check your inbox.');
            return redirect()->route('front-login');
        }
    }
    public function activateAccount($id,$timestamp){
        $user = Volunteer::findOrFail(decrypt($id));
        if(!empty($user->email_verified_at)){
            $this->setFlashSession(false, 'Your account has already been activated.');
            return redirect()->route('front-login');
        }
        $tokenCreatedAt = \Carbon\Carbon::createFromTimestamp(decrypt($timestamp));
        if(now()->diffInHours($tokenCreatedAt) > 24){
            $this->setFlashSession(false, 'Your activation link has expired. Please request a new one from login page.');
            return redirect()->route('front-login');
        }
        $user->email_verified_at = now();
        $user->save();
        if(!empty($user->password)){
            $this->setFlashSession(true, 'Your account has been activated successfully.');
            return redirect()->route('front-login');
        } else {
            PasswordReset::where('email',$user->email_address)->delete();
            $random = Str::random(64);
            $pwReset = new PasswordReset();
            $pwReset->email = $user->email_address;
            $pwReset->token = $random;
            $pwReset->save();
            $this->setFlashSession(true, 'Your account has been activated successfully. Please set your password.');
            return redirect()->route('resetPassword',['token'=>$random]);
        }
    }
    public function logout(){
        auth()->guard('front')->logout();
        return redirect()->route('front-login');
    }

    public function resetPassword($token) {
        $pwReset = PasswordReset::where('token',request()->token)->first();
        if($pwReset){
            $data['title'] = 'Reset Password';
            $data['token'] = $token;
            return view('pages.frontend.reset-password',$data);
        }else{
            echo 'Token expired.';
        }
    }

    public function updatePassword() {
        request()->validate([
            'password'=>'required|confirmed',
            'password_confirmation'=>'required'
        ]);

        
        $pwReset = PasswordReset::where('token',request()->token)->first();
        if($pwReset){
            $user = Volunteer::where('email_address',$pwReset->email)->first();
            $user->password = Hash::make(request()->password);
            $user->save();
            $pwReset->delete();
            $this->setFlashSession(true,"Password updated successfully. You can now login.");
            return redirect()->route('front-login');
        }else{
            $this->setFlashSession(false,"Failed to reset password. ");
            return redirect()->back();
        }
    }

    public function importdata(){
        echo '<pre>';
        //$filePath = public_path('assets/csv/events.csv');
        $filePath = public_path('assets/csv/events-with-tickets.csv');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $data = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($header) === count($row)) {
                    $res = array_combine($header, $row);
                    $data[] = $res;
                    print_r(json_decode($res['_free_tickets']));
                }
            }
            fclose($handle);
        }
        //print_r($data);
        /* $org = [];
        $emails = [];
        foreach ($data as $key => $value) {
            if (!in_array($value['Author Email'], $emails)) {
                    $org[] = [
                        'is_organiser'=>1,
                        'password'=>Hash::make('123123'),
                        'first_name'=>$value['Author First Name'],
                        'last_name'=>$value['Author Last Name'],
                        'email_address'=>$value['Author Email'],
                        'organisation_landing_slug'=>$this->generateSlugByText($value['Author First Name'].' '.$value['Author Last Name']),
                        'organisation_name'=>$value['Author First Name'].' '.$value['Author Last Name'],
                    ];
                $emails[] = $value['Author Email']; // Keep track of added emails
            }
        } */
        //print_r($data);
    }
}
