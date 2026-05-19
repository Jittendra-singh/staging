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
use App\Models\VolunteerAlert;
use Illuminate\Support\Carbon;
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
    public function subscribers()
    {
        Gate::authorize('view', 'subscribers');

        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        $baseQuery = Volunteer::withCount('alerts')
            ->whereHas('alerts');

        $listingData = (clone $baseQuery)
            ->when(request()->user_id, function ($q) {
                $q->where('id', request()->user_id);
            })
            ->when(request()->user_type, function ($q) {
                if (request()->user_type == 'organizer') {
                    $q->where('is_organiser', 1);
                } elseif (request()->user_type == 'volunteer') {
                    $q->where('is_organiser', 0);
                }
            })
            ->when(request()->filled('status'), function ($q) {
                $q->where('status', request()->status);
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $selected_user = request()->user_id
            ? Volunteer::find(request()->user_id)
            : null;

        $data = [
            'seo_details' => (object) ['title' => 'Subscribers'],
            'pageName' => 'Subscribers',
            'listingData' => $listingData,
            'selected_user' => $selected_user,
            'counters' => (object) [
                'total_subscribers' => (clone $baseQuery)->count(),
                'inactive_subscribers' => (clone $baseQuery)->where('status', 0)->count(),
                'new_this_month' => (clone $baseQuery)->whereDate('created_at', '>=', $monthStart)->count(),
                'new_this_year' => (clone $baseQuery)->whereDate('created_at', '>=', $yearStart)->count(),
            ],
        ];

        return view('pages.subscribers', $data);
    }

    public function weeklyAlerts()
    {
        Gate::authorize('view', 'weekly-alerts');

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $yearStart = Carbon::now()->startOfYear();

        $baseQuery = VolunteerAlert::with(['volunteer','location']);

        $listingData = (clone $baseQuery)
            ->when(request()->alert_name, function ($q) {
                $q->where('alert_name', 'like', '%' . request()->alert_name . '%');
            })
            ->when(request()->user_id, function ($q) {
                $q->where('volunteer_id', request()->user_id);
            })
            ->when(request()->user_type, function ($q) {
                $q->whereHas('volunteer', function ($userQuery) {
                    if (request()->user_type == 'organizer') {
                        $userQuery->where('is_organiser', 1);
                    } elseif (request()->user_type == 'volunteer') {
                        $userQuery->where('is_organiser', 0);
                    }
                });
            })
            ->when(request()->filled('status'), function ($q) {
                $q->where('status', request()->status);
            })
            ->when(request()->start_date, function ($q) {
                $q->whereDate('created_at', '>=', request()->start_date);
            })
            ->when(request()->end_date, function ($q) {
                $q->whereDate('created_at', '<=', request()->end_date);
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        $selected_user = request()->user_id
            ? Volunteer::find(request()->user_id)
            : null;

        $data = [
            'seo_details' => (object) ['title' => 'Alerts'],
            'pageName' => 'Alerts',
            'listingData' => $listingData,
            'selected_user' => $selected_user,
            'counters' => (object) [
                'total_alerts' => (clone $baseQuery)->count(),
                'new_this_week' => (clone $baseQuery)->whereDate('created_at', '>=', $weekStart)->count(),
                'new_this_month' => (clone $baseQuery)->whereDate('created_at', '>=', $monthStart)->count(),
                'new_this_year' => (clone $baseQuery)->whereDate('created_at', '>=', $yearStart)->count(),
                'active_alerts' => (clone $baseQuery)->where('status', 1)->count(),
                'disabled_alerts' => (clone $baseQuery)->where('status', 0)->count(),
                'disabled_this_week' => (clone $baseQuery)->where('status', 0)->whereDate('updated_at', '>=', $weekStart)->count(),
                'disabled_this_month' => (clone $baseQuery)->where('status', 0)->whereDate('updated_at', '>=', $monthStart)->count(),
            ],
        ];

        return view('pages.report-weekly-alert', $data);
    }
    public function weeklyAlertsAnalytics()
    {
        Gate::authorize('view', 'weekly-alert-analytics');

        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();

        $baseQuery = VolunteerAlert::with(['volunteer', 'location']);

        $weeklyLabels = [];
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $weeklyLabels[] = $date->format('D');
            $weeklyData[] = (clone $baseQuery)->whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthlyLabels[] = $date->format('M');
            $monthlyData[] = (clone $baseQuery)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        $quarterlyLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
        $quarterlyData = [];
        foreach ([1, 2, 3, 4] as $quarter) {
            $startMonth = (($quarter - 1) * 3) + 1;
            $startDate = Carbon::create($now->year, $startMonth, 1)->startOfDay();
            $endDate = $startDate->copy()->addMonths(3)->subDay()->endOfDay();

            $quarterlyData[] = (clone $baseQuery)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }

        $yearlyLabels = [];
        $yearlyData = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = $now->copy()->subYears($i)->year;
            $yearlyLabels[] = (string) $year;
            $yearlyData[] = (clone $baseQuery)->whereYear('created_at', $year)->count();
        }

        $alertsForMap = VolunteerAlert::with('location')->get();

        $stateStats = [];
        foreach ($alertsForMap as $alert) {
            $location = $alert->location;

            if (!$location || empty($location->state)) {
                continue;
            }

            $stateCode = strtoupper($location->state);
            $mapStateCode = 'US-' . $stateCode;

            if (!isset($stateStats[$mapStateCode])) {
                $stateStats[$mapStateCode] = [
                    'code' => $mapStateCode,
                    'state' => $stateCode,
                    'name' => $location->state_full_name ?: $stateCode,
                    'count' => 0,
                ];
            }

            $stateStats[$mapStateCode]['count']++;
        }

        $topStates = collect($stateStats)
            ->sortByDesc('count')
            ->take(5)
            ->values();

        $mapData = collect($stateStats)
            ->mapWithKeys(function ($item) {
                return [$item['code'] => $item['count']];
            })
            ->toArray();


        $data = [
            'seo_details' => (object) ['title' => 'Weekly Alert Analytics'],
            'pageName' => 'Weekly Alert Analytics',
            'counters' => (object) [
                'total_alerts' => (clone $baseQuery)->count(),
                'disabled_alerts' => (clone $baseQuery)->where('status', 0)->count(),
                'new_alerts' => (clone $baseQuery)->whereDate('created_at', '>=', $monthStart)->count(),
                'subscribers' => (clone $baseQuery)->distinct('volunteer_id')->count('volunteer_id'),
            ],
            'weeklyLabels' => $weeklyLabels,
            'weeklyData' => $weeklyData,
            'monthlyLabels' => $monthlyLabels,
            'monthlyData' => $monthlyData,
            'quarterlyLabels' => $quarterlyLabels,
            'quarterlyData' => $quarterlyData,
            'yearlyLabels' => $yearlyLabels,
            'yearlyData' => $yearlyData,
            'topStates' => $topStates,
            'mapData' => $mapData,
        ];

        return view('pages.report-weekly-alert-analytics', $data);
    }

}