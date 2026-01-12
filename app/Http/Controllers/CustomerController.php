<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

use Illuminate\Http\Request;

use App\Models\Customer;

class CustomerController extends Controller
{
    private $gatePlaceholder='customer'; 
    public function index()
    {
        Gate::authorize('view',$this->gatePlaceholder);
        return view('pages.customer.view-customer', [
            'listingData' => Customer::order()->get()->map(function($pb){
                                $pb->id = encrypt($pb->id);
                                return $pb;
                            }),
        ]);
    }

    public function showForm($id = null)
    {
        if($id){
            $data       =  Customer::findOrFail(decrypt($id));
            $data->id   =  encrypt($data->id);
            Gate::authorize('view' , $this->gatePlaceholder);
        }else{
            Gate::authorize('create' ,  $this->gatePlaceholder);
        }
        return view('pages.customer.manage-customer', ['formData' => $data ?? null]);
    }

    public function manage($id = null){
        if (!$id) {
            $row = new Customer();
            request()->validate([
                'name' => 'required',
                'phone' => 'required|unique:customers',
                //'email' => 'required|email|unique:customers,email',
            ]);
            Gate::authorize('create' ,  $this->gatePlaceholder);
            $row->is_active = 1;
        }else{
            $row = Customer::findOrFail(decrypt($id));
            request()->validate([
                'name' => "required",
                'phone' => "required|unique:customers,phone,$row->id",
                //'email' => "required|email|unique:customers,email,$row->id",
            ]);
            Gate::authorize('update' ,  $this->gatePlaceholder);
        }
        if(!empty(request()->sec_phone)){
            $sec_phone = request()->sec_phone;
            $checkCus = Customer::where(function($query) use ($sec_phone){
                $query->where('phone', $sec_phone)->orWhere('sec_phone', $sec_phone);
            });
            if (!empty($id)){
                $checkCus = $checkCus->where('id','!=',decrypt($id));
            }
            $checkCus = $checkCus->first();

            if(isset($checkCus->id)){
                $this->setFlashSession(false,'Secondary phone number already exists with another customer.');
                return redirect()->route('show-customer' , ['id' => $id]);
            }
        }
        //print_r(request()->post());die();
        $row->name = request()->name;
        $row->phone = request()->phone;
        $row->sec_phone = request()->sec_phone;
        $row->email = request()->email;
        $row->address = request()->address;
        $row->city = request()->city;
        $row->state = request()->state;
        $row->country = request()->country;
        $row->zipcode = request()->zipcode;
        $row->aadhar_number = request()->aadhar_number;
        $row->dl_number = request()->dl_number;
        $row->is_dl_verified = (request()->verified_dl)?1:0;
        $row->is_aadhar_verified = (request()->verified_ad)?1:0;
        if(request()->hasFile('dl')){
            $fileArr = $this->moveFiles(request()->file('dl'),'uploads/customer/dl');
            $row->driving_licence_photo = $fileArr['path'];
        }
        else if(request()->photo_remove_dl==1){
            $row->driving_licence_photo = '';
        }
        if(request()->hasFile('aadhar_front')){
            $fileArr = $this->moveFiles(request()->file('aadhar_front'),'uploads/customer/aadhar');
            $row->aadhar_front_photo = $fileArr['path'];
        }
        else if(request()->photo_remove_af==1){
            $row->aadhar_front_photo = '';
        }
        if(request()->hasFile('aadhar_back')){
            $fileArr = $this->moveFiles(request()->file('aadhar_back'),'uploads/customer/aadhar');
            $row->aadhar_back_photo = $fileArr['path'];
        }
        else if(request()->photo_remove_ab==1){
            $row->aadhar_back_photo = '';
        }
        $res = $row->save();
        $this->setFlashSession($res);
        if($res)
            return redirect()->route('index-customer');
        return redirect()->route('show-customer' , ['id' => $id]);
    }
    public function send_otp_customer_phone($id){
        if($id==''){
            return $this->generateJsonResponse(false, 'Customer not found');
        }
        $customer = Customer::findOrFail(decrypt($id));
        if(isset($customer) && isset($customer->id)){
            $otp = $this->generate_otp();
            $m_content = str_replace("{mobile}",$customer->phone,PARTNER_LOGIN_SMS);
            $m_content = str_replace("{otp}",$otp,$m_content);
            $this->send_sms($customer->phone,$m_content);

            $customer->phone_verify_otp = $otp;
            $customer->save();
            return $this->generateJsonResponse(true, 'OTP has been sent to mobile number successfully.');
        }
        else{
            return $this->generateJsonResponse(false, 'Customer not found.');
        }
    }
    public function verify_customer_phone(){
        if(request()->otp==''){
            return $this->generateJsonResponse(false, 'Please enter OTP');
        }
        if(request()->id==''){
            return $this->generateJsonResponse(false, 'Customer not found');
        }
        $customer = Customer::findOrFail(decrypt(request()->id));
        if(isset($customer) && isset($customer->id)){
            if(!empty($customer->phone_verify_otp) && $customer->phone_verify_otp==request()->otp){
                $customer->is_phone_verified = 1;
                $customer->save();
                return $this->generateJsonResponse(true, 'Customer verified successfully.');
            } else{
                return $this->generateJsonResponse(false, 'Please provide valid OTP.');
            }
        }
        else{
            return $this->generateJsonResponse(false, 'Customer not found.');
        }
    }

    public function toggleStatus($id){
        if(Gate::allows('update' , $this->gatePlaceholder)){
            $row = Customer::findOrFail(decrypt($id));
            $row->is_active = !$row->is_active ; 
            $this->setFlashSession($row->save());
        }
        return redirect()->route('index-customer');
    }

    public function delete($id){
        Gate::authorize('delete',$this->gatePlaceholder);
        $row = Customer::withCount('bookings','luggage_bookings')->findOrFail(decrypt($id));
        if($row->bookings_count)
            return $this->generateJsonResponse(false , 'Opps! Some bookings are connected with this customer, we can\'t delete this.');
        if($row->luggage_bookings_count)
            return $this->generateJsonResponse(false , 'Opps! Some luggage inquiry are connected with this customer, we can\'t delete this.');
        $res = $row->delete();
        return $this->generateJsonResponse($res);
    }
}