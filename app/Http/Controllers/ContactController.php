<?php
namespace App\Http\Controllers;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class ContactController extends Controller
{       
    public function index()
    {   
        Gate::authorize('view', 'other-inquiry');
        if(isset($_POST['f_date']) && !empty($_POST['f_date'])){
            $date = explode(' to ',$_POST['f_date']);
        }
        else{
            $date = [date('Y-m-01'),date('Y-m-d')];
        }
        $start_date = date('Y-m-d',strtotime($date[0]));
        $end_date = date('Y-m-d',strtotime($date[1]));
        $listing = Contact::query();
        if(isset($_POST['f_status']) && !empty($_POST['f_status'])){
            $listing = $listing->where('type',$_POST['f_status']);
        }
        if(isset($date) && !empty($date)){
            if(!isset($end_date) && isset($start_date) && !empty($start_date)){
                $listing = $listing->whereDate('created_at','=',$start_date);
            }
            else{
                if(isset($start_date) && !empty($start_date)){
                    $listing = $listing->whereDate('created_at','>=',$start_date);
                }
                if(isset($end_date) && !empty($end_date)){
                    $listing = $listing->whereDate('created_at','<=',$end_date);
                }
            }
        }
        $listing = $listing->order()->get();
        return view('pages.contact-request.view-contact-request', [
            'f_date' => implode(' to ',$date),
            'listingData' => $listing,
        ]);
    }
}
