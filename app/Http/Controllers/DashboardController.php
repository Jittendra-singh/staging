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
use App\Models\EventCategory;
use App\Models\Tag;
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
            ->withMax('alerts', 'created_at')
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
            ->orderByDesc('alerts_max_created_at')
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

        $baseQuery = VolunteerAlert::with([
            'volunteer',
            'location',
            'statusUpdatedByVolunteer',
            'statusUpdatedByAdmin',
        ]);

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
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();
        $quarterStart = $now->copy()->startOfQuarter();
        $yearStart = $now->copy()->startOfYear();

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
                    'lat_sum' => 0,
                    'lng_sum' => 0,
                    'location_count' => 0,
                ];
            }

            $stateStats[$mapStateCode]['count']++;

            if (!empty($location->location_lat) && !empty($location->location_long)) {
                $stateStats[$mapStateCode]['lat_sum'] += (float) $location->location_lat;
                $stateStats[$mapStateCode]['lng_sum'] += (float) $location->location_long;
                $stateStats[$mapStateCode]['location_count']++;
            }
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

        $stateLabels = collect($stateStats)
            ->filter(function ($item) {
                return $item['location_count'] > 0;
            })
            ->map(function ($item) {
                return [
                    'code' => $item['code'],
                    'state' => $item['state'],
                    'name' => $item['name'],
                    'count' => $item['count'],
                    'lat' => $item['lat_sum'] / $item['location_count'],
                    'lng' => $item['lng_sum'] / $item['location_count'],
                ];
            })
            ->values();



        $data = [
            'seo_details' => (object) ['title' => 'Weekly Alert Analytics'],
            'pageName' => 'Weekly Alert Analytics',
            'counters' => (object) [
                /* 'total_alerts' => (clone $baseQuery)->count(),
                'active_alerts' => (clone $baseQuery)->where('status', 1)->count(),
                'inactive_alerts' => (clone $baseQuery)->where('status', 0)->count(), */
                'active_alerts' => (clone $baseQuery)->where('status', 1)->count(),
                'active_alerts_week' => (clone $baseQuery)->where('status', 1)->whereDate('created_at', '>=', $weekStart)->count(),
                'active_alerts_month' => (clone $baseQuery)->where('status', 1)->whereDate('created_at', '>=', $monthStart)->count(),
                'active_alerts_quarter' => (clone $baseQuery)->where('status', 1)->whereDate('created_at', '>=', $quarterStart)->count(),
                'active_alerts_year' => (clone $baseQuery)->where('status', 1)->whereDate('created_at', '>=', $yearStart)->count(),


                'disabled_alerts' => (clone $baseQuery)->where('status', 0)->count(),
                'disabled_week' => (clone $baseQuery)->where('status', 0)->whereDate('updated_at', '>=', $weekStart)->count(),
                'disabled_month' => (clone $baseQuery)->where('status', 0)->whereDate('updated_at', '>=', $monthStart)->count(),
                'disabled_quarter' => (clone $baseQuery)->where('status', 0)->whereDate('updated_at', '>=', $quarterStart)->count(),
                'disabled_year' => (clone $baseQuery)->where('status', 0)->whereDate('updated_at', '>=', $yearStart)->count(),

                'new_alerts' => (clone $baseQuery)->whereDate('created_at', '>=', $yearStart)->count(),
                'new_alerts_week' => (clone $baseQuery)->whereDate('created_at', '>=', $weekStart)->count(),
                'new_alerts_month' => (clone $baseQuery)->whereDate('created_at', '>=', $monthStart)->count(),
                'new_alerts_quarter' => (clone $baseQuery)->whereDate('created_at', '>=', $quarterStart)->count(),
                'new_alerts_year' => (clone $baseQuery)->whereDate('created_at', '>=', $yearStart)->count(),

                'subscribers' => (clone $baseQuery)->distinct('volunteer_id')->count('volunteer_id'),
                'subscribers_week' => VolunteerAlert::select('volunteer_id')->groupBy('volunteer_id')->havingRaw('MIN(created_at) >= ?', [$weekStart])->count(),
                'subscribers_month' => VolunteerAlert::select('volunteer_id')->groupBy('volunteer_id')->havingRaw('MIN(created_at) >= ?', [$monthStart])->count(),
                'subscribers_quarter' => VolunteerAlert::select('volunteer_id')->groupBy('volunteer_id')->havingRaw('MIN(created_at) >= ?', [$quarterStart])->count(),
                'subscribers_year' => VolunteerAlert::select('volunteer_id')->groupBy('volunteer_id')->havingRaw('MIN(created_at) >= ?', [$yearStart])->count(),
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
            'stateLabels' => $stateLabels,
        ];

        return view('pages.report-weekly-alert-analytics', $data);
    }
    public function toggleWeeklyAlertStatus($placeholder)
    {
        Gate::authorize('update', 'weekly-alerts');
        $obj = VolunteerAlert::findOrFail(decrypt($placeholder));
        if ($obj->status == 1) {
            $obj->status = 0;
            $obj->status_updated_by = auth()->user()->id; 
            $obj->status_updated_by_role = 2;

            $this->setFlashSession($obj->save(), 'Alert disabled successfully.');
        } else {
            if ($obj->status_updated_by_role == 2) {
                $obj->status = 1;
                $obj->status_updated_by = auth()->user()->id; 
                $obj->status_updated_by_role = 2;

                $this->setFlashSession($obj->save(), 'Alert enabled successfully.');
            } else {
                $this->setFlashSession(false, 'This alert was disabled by the subscriber, so admin cannot enable it.');
            }
        }
        return redirect()->route('index-weekly-alerts');
    }

    public function campaignAnalytics()
    {
        Gate::authorize('view', 'campaign-analytics');

        $defaultCampaignTagId = 1; // TODO: set your default campaign tag id here

        $selectedTagId = request()->filled('tag_id') ? request()->tag_id : $defaultCampaignTagId;
        $selected_organizer = request()->organizer_id ? Volunteer::find(request()->organizer_id) : null;

        $baseEventQuery = Event::with(['creator', 'category'])
            ->whereHas('tags', function ($q) use ($selectedTagId) {
                $q->where('tags.id', $selectedTagId);
            })
            ->when(request()->category_id, function ($q) {
                $q->where('event_category_id', request()->category_id);
            })
            ->when(request()->organizer_id, function ($q) {
                $q->where('creator_id', request()->organizer_id);
            })
            ->when(request()->start_date && request()->end_date, function ($q) {
                $q->whereDate('start_datetime', '>=', request()->start_date)
                ->whereDate('start_datetime', '<=', request()->end_date);
            });

        $events = (clone $baseEventQuery)->orderBy('start_datetime')->get();
        $eventIds = $events->pluck('id');

        $confirmedTicketsQuery = EventOrderTicket::whereIn('event_id', $eventIds)
            ->where('rsvp_confirmation', 1);

        $totalRegistrations = (clone $confirmedTicketsQuery)->count();
        $totalCheckins = (clone $confirmedTicketsQuery)->where('checkin', 1)->count();

        $totalDebrisRemoved = $events->sum(function ($event) {
            return (float) preg_replace('/[^0-9.]/', '', $event->waste_collected ?? 0);
        });

        $attendanceRate = $totalRegistrations > 0
            ? round(($totalCheckins / $totalRegistrations) * 100)
            : 0;

        if ($attendanceRate >= 80) {
            $attendanceStatus = 'High Engagement';
        } elseif ($attendanceRate >= 60) {
            $attendanceStatus = 'Moderate Engagement';
        } else {
            $attendanceStatus = 'Needs Attention';
        }

        $registrationMonths = (clone $confirmedTicketsQuery)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            ->get();

        $registrationTrendMonths = [];

        foreach ($registrationMonths as $registrationMonth) {
            $monthStart = Carbon::create($registrationMonth->year, $registrationMonth->month, 1)->startOfDay();
            $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();

            $buckets = [
                [$monthStart->copy()->day(1)->startOfDay(), $monthStart->copy()->day(7)->endOfDay()],
                [$monthStart->copy()->day(8)->startOfDay(), $monthStart->copy()->day(14)->endOfDay()],
                [$monthStart->copy()->day(15)->startOfDay(), $monthStart->copy()->day(21)->endOfDay()],
                [$monthStart->copy()->day(22)->startOfDay(), $monthEnd],
            ];

            $labels = [];
            $data = [];

            foreach ($buckets as [$bucketStart, $bucketEnd]) {
                if ($bucketEnd->gt($monthEnd)) {
                    $bucketEnd = $monthEnd->copy();
                }

                $labels[] = $bucketStart->format('M j') . ' - ' . $bucketEnd->format('j');
                $data[] = (clone $confirmedTicketsQuery)
                    ->whereBetween('created_at', [$bucketStart, $bucketEnd])
                    ->count();
            }

            $monthKey = $monthStart->format('Y-m');

            $registrationTrendMonths[$monthKey] = [
                'label' => $monthStart->format('F Y'),
                'total' => (int) $registrationMonth->total,
                'labels' => $labels,
                'data' => $data,
            ];
        }

        $defaultTrendMonth = array_key_first($registrationTrendMonths);

        if (!$defaultTrendMonth) {
            $emptyMonth = Carbon::now()->startOfMonth();

            $defaultTrendMonth = $emptyMonth->format('Y-m');
            $registrationTrendMonths[$defaultTrendMonth] = [
                'label' => $emptyMonth->format('F Y'),
                'total' => 0,
                'labels' => [
                    $emptyMonth->copy()->day(1)->format('M j') . ' - 7',
                    $emptyMonth->copy()->day(8)->format('M j') . ' - 14',
                    $emptyMonth->copy()->day(15)->format('M j') . ' - 21',
                    $emptyMonth->copy()->day(22)->format('M j') . ' - ' . $emptyMonth->copy()->endOfMonth()->format('j'),
                ],
                'data' => [0, 0, 0, 0],
            ];
        }

        $topEventOrganizers = $events
            ->filter(function ($event) {
                return !empty($event->creator_id);
            })
            ->groupBy('creator_id')
            ->map(function ($organizerEvents) {
                $organizer = $organizerEvents->first()->creator;

                return (object) [
                    'organizer' => $organizer,
                    'total_events' => $organizerEvents->count(),
                    'debris_removed' => $organizerEvents->sum(function ($event) {
                        return (float) preg_replace('/[^0-9.]/', '', $event->waste_collected ?? 0);
                    }),
                ];
            })
            ->sortByDesc('total_events')
            ->take(5)
            ->values();

        $topRegistrationOrganizers = EventOrderTicket::query()
            ->join('events', 'events.id', '=', 'event_order_tickets.event_id')
            ->leftJoin('volunteers', 'volunteers.id', '=', 'events.creator_id')
            ->whereIn('event_order_tickets.event_id', $eventIds)
            ->where('event_order_tickets.rsvp_confirmation', 1)
            ->whereNotNull('events.creator_id')
            ->select(
                'events.creator_id',
                'volunteers.first_name',
                'volunteers.last_name',
                'volunteers.email_address',
                DB::raw('COUNT(event_order_tickets.id) as total_registrations'),
                DB::raw('SUM(CASE WHEN event_order_tickets.checkin = 1 THEN 1 ELSE 0 END) as total_checkins')
            )
            ->groupBy('events.creator_id', 'volunteers.first_name', 'volunteers.last_name', 'volunteers.email_address')
            ->orderByDesc('total_registrations')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $item->conversion_rate = $item->total_registrations > 0
                    ? round(($item->total_checkins / $item->total_registrations) * 100)
                    : 0;

                return $item;
            });

        return view('pages.report-campaign-analytics', [
            'seo_details' => (object) ['title' => 'Campaign Analytics'],
            'pageName' => 'Campaign Analytics',
            'tags' => Tag::orderBy('name')->get(),
            'categories' => EventCategory::orderBy('name')->get(),
            'selectedTagId' => $selectedTagId,
            'selected_organizer' => $selected_organizer,
            'counters' => (object) [
                'total_events' => $events->count(),
                'total_registrations' => $totalRegistrations,
                'total_checkins' => $totalCheckins,
                'total_debris_removed' => $totalDebrisRemoved,
                'attendance_rate' => $attendanceRate,
                'attendance_status' => $attendanceStatus,
            ],
            'registrationTrendMonths' => $registrationTrendMonths,
            'defaultTrendMonth' => $defaultTrendMonth,
            'topEventOrganizers' => $topEventOrganizers,
            'topRegistrationOrganizers' => $topRegistrationOrganizers,
        ]);
    }
}