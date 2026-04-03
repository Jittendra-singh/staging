<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventOrderTicket;
use App\Models\Volunteer;
use App\Models\EventCoHost;
use App\Models\CityStateZip;
class CommonSystemController extends Controller
{
    public function RespondCohostInvitationLink($response , $event ,$volunteer){
        $event_obj                      = Event::findOrFail(decrypt($event));
        $volunteer_obj                  = Volunteer::findOrFail(decrypt($volunteer));
        $eventCohostObj                 = EventCoHost::where('event_id' , $event_obj->id)->where('volunteer_id', $volunteer_obj->id)->first();
        $eventCohostObj->request_status = $response == "accept" ? "approved" : "rejected" ; 
        $eventCohostObj->accepted_at    = now(); 
        $res = $eventCohostObj->save();

        // send email to event organizer about cohost response
        $email_content = [
            'is_accepted'=> $response == "accept" ? true : false,
            'cohost_first_name' => $volunteer_obj->first_name,
            'event_name' => $event_obj->event_name,
            'organizer_first_name' => $event_obj->creator->first_name,
            'eventEditLink' => route('front-post-a-event',['id'=>$event]),
        ];
        if($email_content['is_accepted']){
            $sub = $email_content['cohost_first_name'].' accepted your co-host invitation for '.$email_content['event_name'];
        } else {
            $sub = $email_content['cohost_first_name'].' declined your co-host invitation';
        }
        $this->sendEmail($event_obj->creator->email_address, $sub, $email_content, "cohost-accept-decline");

        $this->syncWithWP($volunteer_obj);

        //$this->setFlashSession($res);
        //return redirect(config('app.frontsite'));
        $data = [];
        $data['event'] = $event_obj; 
        $data['invitation_response'] = $response == "accept" ? true : false;
        $data['seo_details'] = (object)['title' => 'Co-Host Invitation Response'];
        return view('pages.frontend.invite-cohost-response', $data);
    }

    public function getAjaxSlug(Request $request, $type)
    {
        $slug = $request->input('slug_value');
        $existingId = $request->input('existing_id', 0);
        $currentSlug = $request->input('current_slug') ? : $slug;

        // Validate the inputs
        if (empty($slug) ) {
            return $this->generateJsonResponse(false, 'Seems like some error occured');
        }

        // Generate the slug based on the type
        if ($type === 'event') {
            $generatedSlug = $this->generateSlug(Event::class, 'slug', $currentSlug, $existingId);
        } elseif($type === 'volunteer') {
            $generatedSlug = $this->generateSlug(Volunteer::class, 'organisation_landing_slug', $currentSlug, $existingId);
        } else {
            $generatedSlug = '';
        }

        return $this->generateJsonResponse(true, '', ['slug' => $generatedSlug]);

    }
    function getPincodeOptions(){
        $search = request()->search;
        if (!empty($search)) {
            $pincodes = CityStateZip::where('zipcode', 'like', '%' . $search . '%')->get();
            if ($pincodes->isEmpty()) {
                $options = [];
            } else {
                foreach ($pincodes as $pincode) {
                    $options[] = [
                        'id' => $pincode->id,
                        'text' => $pincode->zipcode.' ('.$pincode->city.', '.$pincode->state.')',
                    ];
                }
            }
        } else {
            $options = [];
        }
        return response()->json(['results' => $options]);
    }
    function importCityStateZip(){
        // Use a file from the assets folder instead of uploaded file
        $filePath = public_path('assets/uszips.csv');
        if (!file_exists($filePath)) {
            return back()->withErrors(['csv_file' => 'CSV file not found in assets folder.']);
        }

        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);

        // Expected columns: city, state, zip, latitude, longitude
        $expected = ['city', 'state_id', 'state_name', 'zip', 'lat', 'lng'];
        $header = array_map('strtolower', $header);

        if (array_diff($expected, $header)) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV header must include: city, state_id, state_name, zip, lat, lng']);
        }

        $cityIndex = array_search('city', $header);
        $stateIndex = array_search('state_id', $header);
        $stateNameIndex = array_search('state_name', $header);
        $zipIndex = array_search('zip', $header);
        $latIndex = array_search('lat', $header);
        $lngIndex = array_search('lng', $header);

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = [
            'city' => $data[$cityIndex],
            'state' => $data[$stateIndex],
            'state_full_name' => $data[$stateNameIndex],
            'zipcode' => $data[$zipIndex],
            'lat' => $data[$latIndex],
            'long' => $data[$lngIndex],
            //'created_at' => now(),
            //'updated_at' => now(),
            ];
        }
        fclose($handle);
        echo '<pre>';
        print_r($rows);
        /* if (!empty($rows)) {
            $chunkSize = 500;
            foreach (array_chunk($rows, $chunkSize) as $chunk) {
                DB::table('city_state_zips')->insert($chunk);
            }
        } */
    }
    function cronEventRSVPReminder(){
        // i want to get the event which start_datetime is coming in next 2 days and we only consider dates not time
        $events = Event::with('attendees')->whereDate('start_datetime', '>=', now())
            ->whereDate('start_datetime', '<=', now()->addDays(2))
            ->get();
            echo '<pre>';
            //print_r($events->toArray());
            foreach ($events as $key => $event) {
                $waiver_form_content = $event['waiver_form_content'];
                foreach ($event->attendees as $key => $attendee) {
                    if(empty($attendee['pivot']['rsvp_filled_at'])){
                        $orderTicket = EventOrderTicket::find($attendee['pivot']['id']);
                        if(isset($orderTicket->id)){
                            $fOrderController = new FrontOrderController();
                            //$fOrderController->sendRSVPandWaiverMail($orderTicket);
                            print_r($orderTicket->toArray());
                        }
                    } else if(!empty($waiver_form_content) && empty($attendee['pivot']['waiver_filled_at'])){
                        $orderTicket = EventOrderTicket::find($attendee['pivot']['id']);
                        if(isset($orderTicket->id)){
                            $fOrderController = new FrontOrderController();
                            //$fOrderController->sendRSVPandWaiverMail($orderTicket);
                            print_r($orderTicket->toArray());
                        }
                    }
                }
            }
    }
}
