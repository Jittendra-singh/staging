<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller as BaseController;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function generateResponse($res , $message = '' , $data = []){
        $return_array = [
            'status' => $res ? true : false,
            'message' => $message ? : ($res ? config('app.success') : config('app.failed') )
        ];
        if(!empty($data)){
            $return_array['data'] = $data;
        }
        return $return_array;
    }
    public function setFlashSession($res , $message='' , $data = []){
        $response = $this->generateResponse($res , $message , $data);
        request()->session()->flash('flash_data', $response);
        return true;
    }
    public function generateJsonResponse($res , $message = '', $data = []){
        return response()->json($this->generateResponse($res , $message , $data));
    }
    protected function uploadFiles($file_name , $path = '' ,  $index = -1){
        $uploads = request()->file($file_name);
        if($uploads && $index >= 0){
            $uploads = $uploads[$index] ?? null;
        }
        $files_array = [];
        if(is_array($uploads) && !empty($uploads)){
            foreach ($uploads as $key => $upload) {
                array_push( $files_array , $this->moveFiles($upload , $path));
            }
        }else if($uploads){
            $files_array = $this->moveFiles($uploads , $path);
        }
        return $files_array;
    }
    protected function moveFiles($upload , $destinationPath){
        $fileName = str_replace(' ', '', date('dmyhis') . $upload->getClientOriginalName());
        $upload->move(public_path($destinationPath), $fileName);
        return [
                'path'          => "$destinationPath/" . $fileName,
                'original_name' => $upload->getClientOriginalName(),
                'extension'     => $upload->getClientOriginalExtension(),
            ];
    }
    public function generate_captcha_code(){
        $str_result1 = '0123456789';
        $str_result2 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code1 = substr(str_shuffle($str_result1),0, 3);
        $code2 = substr(str_shuffle($str_result2),0, 3);
        return $code2.$code1;
    }
    public function callCurl($type='GET',$url,$data){
        $curl = curl_init();
        if($type=='POST'){
            $arr = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $type,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Basic YWRtaW46YWRtaW5fYnlvZA=='
                ),
            );
        }
        else if($type=='PUT'){
            $arr = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $type,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            );
        }
        else{
            $arr = array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $type,
            );
        }
        curl_setopt_array($curl, $arr);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
    public function mailUrl($slug,$param=null){
        $checkUrl = route($slug,$param);
        if(strpos($checkUrl, 'volunteercleanup') !== false){
            $parsedUrl = parse_url($checkUrl);

            $remainingSlug = $parsedUrl['path'] ?? '';

            $link = rtrim(config('app.laravelurl'), '/') . $remainingSlug;

            return $link;
        }else{
            return $checkUrl;
        }
    }
    public function syncWithWP($volunteer,$tag=''){
        $url = config('app.wp_sync_url');
        $role = $volunteer->is_organiser ? 'Organizer' : 'Volunteer';
        $data = [
            'first_name'=>$volunteer->first_name,
            'last_name'=>$volunteer->last_name,
            'full_name'=>$volunteer->first_name.' '.$volunteer->last_name,
            'email'=>$volunteer->email_address,
            'phone'=>$volunteer->phone,
            'address_line_1'=>$volunteer->address_line_1,
            'postal_code'=>$volunteer->pincode,
            'country'=>'United States',
            //'tags'=>['Newsletter',$role,$tag],
            'tags'=>['Newsletter',$role],
            'fcrm_vol_newsletter'=>'Yes'
        ];
        $this->callCurl('POST',$url,json_encode($data));
    }
    public function syncWithWPData($volunteerData){
        $url = config('app.wp_sync_url');
        $data = [
            'first_name'=>$volunteerData['first_name'],
            'last_name'=>$volunteerData['last_name'],
            'full_name'=>$volunteerData['first_name'].' '.$volunteerData['last_name'],
            'email'=>$volunteerData['email_address'],
            'phone'=>'',
            'address_line_1'=>'',
            'postal_code'=>'',
            'country'=>'United States',
            'tags'=>['Newsletter'],
            'fcrm_vol_newsletter'=>'Yes'
        ];
        $this->callCurl('POST',$url,json_encode($data));
    }
	public static function format_price($price,$add_comma=false){
		$comma = ($add_comma)?',':'';
		return number_format($price,2, '.', $comma);
	}
    public function generateSlug($class, $column, $slug, $ignoreId = 0){
        $originalSlug = \Str::slug($slug);
        $newSlug = $originalSlug;
        $counter = 1;
        while ($class::where($column, $newSlug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                return $query->where('id', '!=', $ignoreId);
            })
            ->exists()) {
            $newSlug = $originalSlug . '-' . $counter;
            $counter++;
        }
        return $newSlug;
    }
    public function generateSlugByText($text){
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim
        $text = trim($text, '-');
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
    public function getEventMetaDataByAi($event){
        $content = "You are an SEO expert. Based on the given event details, generate the following:

        1. Meta Title (up to 60 characters) – a short, SEO-friendly title.
        2. Meta Description (up to 160 characters) – a compelling description optimized for search engines.
        3. Meta Keywords – a comma-separated list of relevant keywords.

        Event Name: {$event['name']}
        Event Description: {$event['description']}
        Location: {$event['location']}
        Start Date: {$event['start_date']}
        End Date: {$event['end_date']}

        Rules:
        - Return only the output as a JSON object with keys: meta_title, meta_description, meta_keywords.
        - Do not include any explanations or extra content.
        - Meta keywords must be a plain comma-separated list.
        - If event details are insufficient or unclear, return:
        {
            \"meta_title\": \"\",
            \"meta_description\": \"\",
            \"meta_keywords\": \"\"
        }";
        $content = $this->hitApiToAi($content);
        return $content; 
    }
    public function getAiFocusKeyword($meta_title,$meta_description){
        $content = "
                        You are an SEO expert. Your task is to extract the most relevant SEO focus keywords from the given meta title and meta description.
                        Meta Title: {$meta_title}
                        Meta Description: {$meta_description}
                        Instructions:
                        - Return a comma-separated list of high-impact SEO keywords and key phrases directly derived from the title and description.
                        - Only include terms that users are likely to search for and that accurately reflect the content.
                        - If no relevant keywords or key phrases are found, return nothing. Do not write 'empty string' or 'nothing', 'none', or any explanation — just leave the response blank.
                        Output format (strict):
                        keyword1, keyword2, keyword3
                    ";
        $content = " You are an SEO expert. Extract the most relevant SEO focus keywords from the given meta title and meta description.
                        Meta Title: {$meta_title}
                        Meta Description: {$meta_description}
                        Rules:
                        - Return only a comma-separated list of keywords.
                        - Do not explain anything.
                        - Do not ask for more information.
                        - If nothing meaningful is provided or no keywords can be extracted, return exactly a blank string which literally empty.: 
                        Output: (Do not include labels or extra formatting — return only the keyword list or an empty string)
                    ";
        $content = $this->hitApiToAi($content);
        return $content; 
    }
    public function hitApiToAi($content){
        $url = "https://api.openai.com/v1/chat/completions";
        $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('AITOKEN'),
                        'Content-Type' => 'application/json',
                    ])->post($url, [
                            'model' => 'gpt-3.5-turbo',
                            'messages' => [
                                [
                                    "role" => "system",
                                    "content" => "You are a helpful assistant."
                                ],
                                [
                                    "role" => "user",
                                    "content" => "$content" ,
                                ]
                            ],
                            'max_tokens' => 300, // Adjust based on your needs
                            'temperature' => 0.7,
                    ]);
        if($response->successful()){
            $object = $response->object();
            $content = @$object?->choices[0]?->message?->content;
            return $content;
        }
        throw new PlanException( 405 , "AI is not giving any response rightnow." );
    }
    public function getGoogleCoordinates($address){
        try{
            $apiKey = env('GOOGLE_API_KEY');
            $encodedAddress = urlencode($address);

            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                'address' => $encodedAddress,
                'key' => $apiKey,
            ]);

            // Check if the request was successful
            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'OK') {
                    $location = $data['results'][0]['geometry']['location'];
                    $latitude = $location['lat'];
                    $longitude = $location['lng'];

                    return [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                    ];
                } else {
                    return [
                        'latitude' => null,
                        'longitude' => null,
                    ];
                }
            } else {
                return [
                    'latitude' => null,
                    'longitude' => null,   
                ];
            }
        }catch(\Exception $e){
            return [
                    'latitude' => null,
                    'longitude' => null,   
                ];
        }
        
    }

    public function generateRandomOrderNumber(){
        $randomString = str()->random(8); // Generates a random 8-character string
        $timestamp = now()->timestamp; // Current timestamp for uniqueness
        $orderNumber = 'ORD-' . $timestamp . '-' . $randomString; // Example format: ORD-1697051234-ABCD1234
        return $orderNumber;
    }

    public function generateRandomTicketNumber(){
        $randomString = str()->random(8); // Generates a random 8-character string
        $timestamp = now()->timestamp; // Current timestamp for uniqueness
        $orderNumber = 'TKT-' . $timestamp . '-' . $randomString; // Example format: ORD-1697051234-ABCD1234
        return $orderNumber;
    }

    public function generateRandomWidgetNumber(){
        $randomString = str()->random(8); 
        $widgetNumber = 'WID' . strtoupper($randomString);
        return $widgetNumber;
    }

    function generateRandomPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-=';
        return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
    } 

    function sendEmail($to, $subject, $bodyData, $view){
        if(SEND_MAIL){
            try {
                //$body = view('pages.emails.'.$view, $bodyData)->render();
                //$this->SendSendgridEmail($to, $subject,$body);  
                \Mail::to($to)->send( //$to
                    new \App\Mail\SendMail(
                        $subject,
                        "pages.emails.".$view,
                        $bodyData
                    )
                );
            } catch (\Exception $e) {
                // Handle the exception if needed
                // For example, log the error or return a specific response
                //echo '123123';
                echo 'Mail error: ' . $e->getMessage(); die();
            }
        }
    }
    function SendSendgridEmail($email,$subject,$content,$data=null){
        // Set your SendGrid API Key
        $apiKey = env('MAIL_SENDGRID_KEY');

        // Prepare the email payload
        if($data==null){
            $data = [
                'personalizations' => [
                    [
                        'to' => [
                            ['email' => $email], //'learningdcm@gmail.com'
                        ],
                    ]
                ],
                'from' => [
                    'name' =>env('APP_NAME'),
                    'email' => env('MAIL_FROM_ADDRESS') //'Outreach@volunteercleanup.org'
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type' => 'text/html',//'text/plain',
                        'value' => $content
                    ]
                ]
            ];
        }

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            //echo 'Curl error: ' . curl_error($ch);
            $error = curl_error($ch);
            curl_close($ch);
            return array("error" => true, "message" => $error);
        } else {
            //$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            //echo "Response Code: $http_code\n";
            //echo "Response: $response\n";
        }

        // Close cURL session
        curl_close($ch);
        return $response;
    }
}