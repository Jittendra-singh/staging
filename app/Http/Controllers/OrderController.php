<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\EventOrder;
use App\Models\Volunteer;
use App\Models\Event;
use Illuminate\Support\Facades\Gate;
class OrderController extends Controller
{
    private $gateHandler = 'orders';
    private $viewDirectory = 'pages.orders.';
    private $modelClass;
    public function __construct()
    {
        $this->modelClass = EventOrder::class;
    }
    public function index()
    {
        Gate::authorize('view', $this->gateHandler);
        $selected_all_volunteer = request()->purchased_by_id ? Volunteer::find(request()->purchased_by_id) : null;
        $selected_event = request()->event_id ? Event::find(request()->event_id) : null;
        $listingData = $this->modelClass::where(function($q){
                if(request()->purchased_by_id){
                    $q->where('creator_id', request()->purchased_by_id);
                }
                if(request()->event_id){
                    $q->where('event_id', request()->event_id);
                }
                /* if(request()->volunteer){
                    $q->whereHas('creator', function($q){
                            $q->where('first_name', 'like' , '%'.request()->volunteer.'%')
                            ->orWhere('last_name', 'like' , '%'.request()->volunteer.'%');
                    });
                }
                if(request()->event){
                    $q->whereHas('event', function($q){
                            $q->where('event_name', 'like' , '%'.request()->event.'%');
                    });
                } */
                if(request()->start_date)
                    $q->whereDate('created_at', '>=' , request()->start_date);
                if(request()->end_date)
                    $q->whereDate('created_at', '<=' , request()->end_date);
            });
        if(request()->sort_by){
            $listingData = $listingData->orderBy('created_at', request()->sort_by == 1 ? 'desc' : 'asc');
        } else {
            $listingData = $listingData->orderBy('created_at','desc');
        }
        $listingData = $listingData->paginate(50)->withQueryString();
        return view($this->viewDirectory.'view', [
            'selected_all_volunteer'=>$selected_all_volunteer,
            'selected_event'=>$selected_event,
            'listingData' => $listingData,
            'module' => $this->gateHandler,
        ]);
    }
    public function salesReport()
    {
        Gate::authorize('view', $this->gateHandler);
        $selected_org = request()->org_id ? Volunteer::find(request()->org_id) : null;
        $selected_event = request()->event_id ? Event::find(request()->event_id) : null;
        
        $startDate = (request()->start_date)?request()->start_date:date('Y-m-01');
        $endDate = (request()->end_date)?request()->end_date:date('Y-m-d');
        $orders = $this->modelClass::whereHas('event', function ($q) use ($selected_org) {
            if(!empty($selected_org)){
                $q->where('creator_id', $selected_org->id);
            }
        })->where(function($q) use ($startDate,$endDate,$selected_event){
            $q->whereDate("created_at" , ">=" , $startDate);
            $q->whereDate("created_at" , "<=" , $endDate);
            $q->whereDate("payment_status" , "=" , "paid");
            if(!empty($selected_event)){
                $q->where('event_id', $selected_event->id);
            }
        });
        $orders_all = $orders;
        $orders_all = $orders_all->latest()->get();
        $orders = $orders->latest()->paginate(50)->withQueryString();
        //return view('pages.frontend.sales-report', ['orders'=>$orders,'start_date'=>$startDate,'end_date'=>$endDate]);

        return view($this->viewDirectory.'sales-report', [
            'start_date'=>$startDate,
            'end_date'=>$endDate,
            'selected_org'=>$selected_org,
            'selected_event'=>$selected_event,
            'listingData' => $orders,
            'orders' => $orders_all,
            'module' => $this->gateHandler,
        ]);
    }
    public function viewOrderDetails($placeholder)
    {
        $order = EventOrder::findOrFail(decrypt($placeholder));
        return view($this->viewDirectory.'details', [
            'order' => $order ,
            'module' => $this->gateHandler ,
        ]);
    }
    public function manage(Request $request, $placeholder = null)
    {
        // Create or update order
    }
    public function toggleStatus($placeholder)
    {
        // Toggle order status
    }
    public function delete($placeholder)
    {
        // Delete order
    }
    public function cancelRefundPaidTicketAdmin(){
        $frontOrderController = new FrontOrderController();
        return $frontOrderController->cancelRefundPaidTicket();
    }
}
