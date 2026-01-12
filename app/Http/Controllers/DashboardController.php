<?php
namespace App\Http\Controllers;
use App\Models\RoleMenuPermission;
use Illuminate\Support\Facades\Gate;
use App\Models\Menu;
use App\Models\User;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Referrer;
use App\Models\Event;
use App\Models\Volunteer;
use App\Models\EventOrder;
use App\Models\EventOrderTicket;
use App\Models\Country;
use App\Models\City;
use App\Models\ReferrerWithdrawal;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function index(){
        Gate::authorize('view', 'dashboard');
        $data = [];
        $details = [
            'total_events'                  => Event::active()->count(),
            'upcoming_events'               => Event::future()->count(),
            'total_volunteers_registered'   => Volunteer::active()->count(),
            'total_organizers'   => Volunteer::active()->where('is_organiser',1)->count(),
            'total_registration_month'      => Volunteer::active()->whereDate('created_at', '>=', date('Y-m-01'))->count(),
            'total_orders'                  => EventOrder::count(),
            'pending_community_service_req' => EventOrderTicket::where('checkin', 1)->where('community_form_status', 'requested')->count(),
        ];
        $data['details'] = (object)$details;
        $data['seo_details'] = (object)[
                        'title' => 'Dashboard', 
                    ];
        return view('pages.dashboard_new', $data);
    }
}