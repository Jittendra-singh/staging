<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Volunteer;
use App\Models\Setting;
use App\Models\Cart;
use App\Models\StripeLog;
use App\Models\EventOrder;
use App\Exceptions\CustomException;
use App\Http\Controllers\FrontOrderController;

class StripeController extends Controller
{
    protected $stripeSecret;
    public function __construct()
    {
        $this->stripeSecret = env('STRIPE_SECRET'); // platform secret key
        if (!$this->stripeSecret) {
            throw new \Exception("Set STRIPE_SECRET in .env");
        }
    }

    /**
     * Create Checkout Session on behalf of a connected account (Direct Charge)
     * Request body (JSON):
     * {
     *   "organizer_account": "acct_ABC123",
     *   "currency": "usd",
     *   "items": [
     *     {"name": "Ticket A", "unit_amount": 1000, "quantity": 2},
     *     {"name": "Ticket B", "unit_amount": 0, "quantity": 1}
     *   ],
     *   "success_url": "https://example.com/success",
     *   "cancel_url": "https://example.com/cancel",
     *   "order_id": 1234
     * }
     */
    public function connectStripe(){
        // add validation for email 
        $request = request();
        $validated = $request->validate([
            'email'    => 'required|email',
        ]);

        //$organizerEmail = 'rashmi.verma.0402@gmail.com';
        $organizerEmail = $validated['email'];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/accounts");
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret.":");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            "type" => "express",
            "country" => "US",
            "email" => $organizerEmail,
        ]));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $account = json_decode($response, true);
        //echo '<pre>';
        //print_r($account);
        $accountId = $account['id'] ?? null;
        if ($accountId) {
            $org_id = auth()->guard('front')->user()->id;
            $organizer = Volunteer::findOrfail($org_id);
            $organizer->stripe_email = $organizerEmail;
            $organizer->save();

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/account_links");
            curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret.":");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                "account" => $accountId,
                //"refresh_url" => url('/onboarding/refresh'),
                //"return_url" => url('/onboarding/done'),
                "refresh_url" => $this->mailUrl('stripe-connect-refresh'),
                "return_url" => $this->mailUrl('stripe-connect-done',['id'=>encrypt($org_id),'acc_id'=>$accountId]),
                "type" => "account_onboarding"
            ]));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $this->saveStripeLog(['log_type'=>'stripe_connect_link','system_data'=>json_encode(['accountId'=>$accountId,'org_id'=>$org_id]),'stripe_response'=>$response]);

            $link = json_decode($response, true);
            //print_r($link);
            return $this->generateJsonResponse(true, '',$link);
        }
        return $this->generateJsonResponse(false, 'Failed to get stripe account id.');
    }
    public function stripeConnectRefresh(Request $req){
        //echo '<pre>';
        //print_r($req);
        return redirect()->route('front-stripe-connect');
    }
    public function stripeConnectDone($id,$acc_id, Request $req){
        $state = $id; // may be null
        if ($state) {
            $organizer = Volunteer::findOrfail(decrypt($state));
        } else {
            // If no state, you may have stored acct id in session or elsewhere
            $organizer = null;
        }

        // If we know organizer and have account id, fetch account status from Stripe
        if (!empty($organizer) && !empty($acc_id)) {
            $account = $this->stripeGetAccount($acc_id);
            $this->saveStripeLog(['log_type'=>'stripe_connect_done','system_data'=>json_encode(['id'=>$id,'acc_id'=>$acc_id]),'stripe_response'=>json_encode($account)]);
            if ($account) {
                $organizer->stripe_account_id = $acc_id;
                $organizer->stripe_payouts_enabled = $account['payouts_enabled'] ?? false;
                $organizer->stripe_charges_enabled = $account['charges_enabled'] ?? false;
                $organizer->stripe_raw = json_encode($account);
                $organizer->stripe_connect_dt = now();
                $organizer->save();

                if (!empty($account['payouts_enabled'])) {
                    return redirect()->route('front-stripe-connect')->with('success','Onboarding complete, payouts enabled.');
                    //return redirect('/onboarding/success')->with('success','Onboarding complete, payouts enabled.');
                } else {
                    return redirect()->route('front-stripe-connect')->with('error','Onboarding incomplete — check verification in your Stripe dashboard.');
                    //return redirect('/onboarding/incomplete')->with('warning','Onboarding incomplete — check verification in your Stripe dashboard.');
                }
            }
        }

        // fallback: show a page instructing organizer to wait or check email
        //return view('onboarding.wait'); // or redirect anywhere
        return redirect()->route('front-stripe-connect')->with('error','Organizer not found.');
    }
    private function stripeGetAccount(string $acctId)
    {
        $url = "https://api.stripe.com/v1/accounts/{$acctId}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret . ":");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $res = curl_exec($ch);
        $err = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($err || $httpCode >= 400) {
            //Log::error('Stripe account fetch failed', ['acct'=>$acctId, 'err'=>$err, 'http'=>$httpCode, 'res'=>$res]);
            return null;
        }
        return json_decode($res, true);
    }
    public function stripePaymentSuccess(Request $request){
        /* $data = $request->json()->all();
        $data2 = $request->all();
        echo '<pre>';
        print_r($data);
        echo '<br/>==================================================================<br>';
        print_r($data2);
        echo '<br/>==================================================================<br>';
        print_r($_REQUEST); */

        $sessionId = $request->session_id;

        $cart = Cart::where('stripe_session_id',$sessionId)->first();
        if(!isset($cart->id)){
            $this->saveStripeLog(['log_type'=>'stripe_payment_success_cart_not_found','system_data'=>json_encode(['stripe_session_id'=>$sessionId])]);
            throw new CustomException("Something went wrong with your payment. Please contact us by email for assistance.",403,'front-checkout');
        }

        // 1. Retrieve checkout session
        $ch = curl_init("https://api.stripe.com/v1/checkout/sessions/$sessionId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->stripeSecret
        ]);
        $sessionRes = curl_exec($ch);
        curl_close($ch);

        $session = json_decode($sessionRes, true);
        //print_r($session);//die();

        if(!isset($session['payment_intent'])){
            $this->saveStripeLog(['log_type'=>'stripe_payment_success_intent_not_found','system_data'=>json_encode(['stripe_session_id'=>$sessionId]),'stripe_response'=>$sessionRes]);
            throw new CustomException("Something went wrong with your payment. Please contact us by email for assistance.",403,'front-checkout');
        }
        // 2. Retrieve payment intent
        $paymentIntentId = $session['payment_intent'];
        $cart->stripe_payment_intent_id = $paymentIntentId;
        $cart->save();

        $ch = curl_init("https://api.stripe.com/v1/payment_intents/$paymentIntentId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->stripeSecret
        ]);
        $piRes = curl_exec($ch);
        curl_close($ch);

        $pi = json_decode($piRes, true);
        //print_r($pi);
        $this->saveStripeLog(['log_type'=>'stripe_payment_success_log','system_data'=>json_encode(['stripe_session_id'=>$sessionId,'payment_intent_id'=>$paymentIntentId]),'stripe_response'=>$piRes]);

        if($session['payment_status']==='paid' && ($session['status']==='complete' || $session['status']==='succeeded')){
            //return redirect()->route('front-booking-confirmed', ['order' => encrypt($order_object->id)]);
            // need to comment below 2 lines becuase now we are creating order from webhook 
            $orderCon = new FrontOrderController();
            return $orderCon->frontCreateOrder($this,$cart->id);
        } else {
            throw new CustomException("Payment failed. Something went wrong, please try again.",403,'front-checkout');
        }

        // 3. Save order here...

        /* return view('payment-success', [
            'session' => $session,
            'paymentIntent' => $pi
        ]); */
    }
    public function createCheckout($customerData, $grand_total,$donation_amount,$cart_id) // Request $req
    {
        $event               = session()->get('checkout_event');
        $order_total_details = session()->get('checkout_order_details');
        $ticket_booking      = session()->get('ticket_booking');
        $setting = Setting::first();
       /*  echo '<pre>';
        print_r($order_total_details);
        print_r($ticket_booking);
        print_r($grand_total);
        print_r($donation_amount);
        print_r($event->toArray()); */
        //return false;

        //$data = $req->json()->all();

        $connectedAccount = $event->creator->stripe_account_id;
        $currency = 'usd';
        $items = $ticket_booking ?? [];
        $successUrl = route('stripe-payment-success');
        $cancelUrl = route('front-checkout');
        $orderId = null;

        if (!$connectedAccount || empty($items)) {
            return response()->json(['error' => 'organizer_account and items required'], 400);
        }

        // Build line_items for Checkout Session as form-encoded fields
        // Also compute total to calculate application_fee_amount (0.6%)
        $totalCentsBase = 0;
        $totalCents = 0;
        $postFields = [];
        $liIndex = 0;
        foreach ($items as $it) {
            $name = $it['name'];
            $unit = intval($it['price'] * 100);
            $base_price = intval($it['base_price'] * 100);
            $qty = intval($it['count']) ?: 1;

            $postFields["line_items[{$liIndex}][price_data][currency]"] = $currency;
            $postFields["line_items[{$liIndex}][price_data][product_data][name]"] = $name.' ('.$it['id'].')';
            $postFields["line_items[{$liIndex}][price_data][unit_amount]"] = $unit;
            $postFields["line_items[{$liIndex}][quantity]"] = $qty;

            $totalCents += $unit * $qty;
            $totalCentsBase += $base_price * $qty;
            $liIndex++;
        }
        if($donation_amount>0){
            $donation_amount = intval($donation_amount * 100);
            $postFields["line_items[{$liIndex}][price_data][currency]"] = $currency;
            $postFields["line_items[{$liIndex}][price_data][product_data][name]"] = 'DONATION';
            $postFields["line_items[{$liIndex}][price_data][unit_amount]"] = $donation_amount;
            $postFields["line_items[{$liIndex}][quantity]"] = 1;
            $totalCents += $donation_amount;
            $totalCentsBase += $donation_amount;
        }

        // Platform fee: 0.6% => 0.006
        //$platformFee = (int) round($totalCents * 0.006);
        $platformFrePer = $setting->event_commission/100;
        //$platformFee = (int) round($totalCents * $platformFrePer);
        $platformFee = (int) round($totalCentsBase * $platformFrePer);

        // Prepare other session fields
        $postFields['mode'] = 'payment';
        $postFields['payment_method_types[0]'] = 'card';
        $postFields['success_url'] = $successUrl . '?session_id={CHECKOUT_SESSION_ID}';
        $postFields['cancel_url'] = $cancelUrl;
        // pass application_fee_amount inside payment_intent_data
        $postFields['payment_intent_data[application_fee_amount]'] = $platformFee;
        $postFields['payment_intent_data[transfer_data][destination]'] = $connectedAccount;
        $postFields['customer_email'] = $customerData['customer_email'];

        // Optionally attach metadata so you can link session -> your order
        if ($orderId) {
            $postFields['metadata[order_id]'] = $orderId;
        }
        if ($cart_id) {
            $postFields['metadata[cart_id]'] = $cart_id;
        }
        $postFieldsLog = $postFields;
        $postFields = http_build_query($postFields);
        // Make the API call to create Checkout Session ON BEHALF OF connected account
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Basic auth with secret key
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret . ':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        // Header to create on connected account (Direct Charge)
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //"Stripe-Account: {$connectedAccount}",
            "Accept: application/json",
        ]);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        $this->saveStripeLog(['log_type'=>'stripe_create_checkout_log','system_data'=>json_encode($postFieldsLog),'stripe_response'=>json_encode(['res'=>$resp,'err'=>$err])]);

        if ($err) {
            //return response()->json(['error' => $err], 500);
            return ['status'=>false,'data'=>$err];
        }

        $json = json_decode($resp, true);

        if (isset($json['error'])) {
            //return response()->json($json, 400);
            return ['status'=>false,'data'=>$json];
        }

        //return response()->json($json);
        // we need to save the $json['id'] value on the cart table and save all cart data and checkout page request data as json on the chart table. this id is session_id and you can fetch payment intent / session data of stripe by this id, it's shows the payment status. show save the cart data here with this id  
        //echo $json['id'];die();
        return ['status'=>true,'data'=>$json];
    }
    public function createCheckoutOLD(Request $req)  
    {

        $data = $req->json()->all();

        $connectedAccount = $data['organizer_account'] ?? null;
        $currency = $data['currency'] ?? 'usd';
        $items = $data['items'] ?? [];
        $successUrl = $data['success_url'] ?? url('/success');
        $cancelUrl = $data['cancel_url'] ?? url('/cancel');
        $orderId = $data['order_id'] ?? null;

        if (!$connectedAccount || empty($items)) {
            return response()->json(['error' => 'organizer_account and items required'], 400);
        }

        // Build line_items for Checkout Session as form-encoded fields
        // Also compute total to calculate application_fee_amount (0.6%)
        $totalCents = 0;
        $postFields = [];
        $liIndex = 0;
        foreach ($items as $it) {
            $name = $it['name'];
            $unit = intval($it['unit_amount']);
            $qty = intval($it['quantity']) ?: 1;

            $postFields["line_items[{$liIndex}][price_data][currency]"] = $currency;
            $postFields["line_items[{$liIndex}][price_data][product_data][name]"] = $name;
            $postFields["line_items[{$liIndex}][price_data][unit_amount]"] = $unit;
            $postFields["line_items[{$liIndex}][quantity]"] = $qty;

            $totalCents += $unit * $qty;
            $liIndex++;
        }

        // Platform fee: 0.6% => 0.006
        $platformFee = (int) round($totalCents * 0.006);

        // Prepare other session fields
        $postFields['mode'] = 'payment';
        $postFields['payment_method_types[0]'] = 'card';
        $postFields['success_url'] = $successUrl . '?session_id={CHECKOUT_SESSION_ID}';
        $postFields['cancel_url'] = $cancelUrl;
        // pass application_fee_amount inside payment_intent_data
        $postFields['payment_intent_data[application_fee_amount]'] = $platformFee;

        // Optionally attach metadata so you can link session -> your order
        if ($orderId) {
            $postFields['metadata[order_id]'] = $orderId;
        }

        // Make the API call to create Checkout Session ON BEHALF OF connected account
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Basic auth with secret key
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret . ':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        // Header to create on connected account (Direct Charge)
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Stripe-Account: {$connectedAccount}",
            "Accept: application/json",
        ]);

        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json(['error' => $err], 500);
        }

        $json = json_decode($resp, true);

        if (isset($json['error'])) {
            return response()->json($json, 400);
        }

        // Suggested: save session.id and payment_intent later on checkout.session.completed webhook
        // Save minimal order info if order_id provided
        if ($orderId) {
            DB::table('orders')->updateOrInsert(
                ['id' => $orderId],
                [
                    'stripe_checkout_session_id' => $json['id'],
                    'stripe_payment_intent_id' => $json['payment_intent'] ?? null,
                    'organizer_account' => $connectedAccount,
                    'total_amount_cents' => $totalCents,
                    'platform_fee_cents' => $platformFee,
                    'status' => 'checkout_created',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        return response()->json($json);
    }

    /**
     * Webhook to receive checkout.session.completed.
     * You must point Stripe webhook to this route and configure webhook secret in env if you want to verify signature.
     */
    public function stripeCheckoutWebhook(Request $request){
        // 1. Get raw payload & Stripe signature
        $payload    = $request->getContent();
        $sigHeader  = $request->header('Stripe-Signature');
        $secret     = $this->stripeSecret;

        /* if (!$sigHeader) {
            return response('Missing signature', 400);
        }

        // 2. Verify Stripe signature manually (cURL-safe logic)
        $parts = explode(',', $sigHeader);
        $timestamp = null;
        $signature = null;

        foreach ($parts as $part) {
            if (str_starts_with($part, 't=')) {
                $timestamp = substr($part, 2);
            }
            if (str_starts_with($part, 'v1=')) {
                $signature = substr($part, 3);
            }
        }

        if (!$timestamp || !$signature) {
            return response('Invalid signature header', 400);
        }

        $signedPayload = $timestamp . '.' . $payload;
        $expectedSig   = hash_hmac('sha256', $signedPayload, $secret);

        if (!hash_equals($expectedSig, $signature)) {
            return response('Invalid signature', 400);
        } */

        // 3. Decode event
        $event = json_decode($payload, true);

        if (!isset($event['type']) || $event['type'] !== 'checkout.session.completed') {
            return response('Event ignored', 200);
        }

        $session = $event['data']['object'];
        $cart_id = $session['metadata']['cart_id'];
        $payment_intent = $session['payment_intent'];
        file_put_contents(storage_path('logs/stripe_webhook.json'), json_encode(['session'=>$session['payment_status'],'id'=>$session['id'],'cart_id'=>$cart_id]));

        $eventOrder = EventOrder::where('payment_session_id',$session['id'])->first();
        if(isset($eventOrder->id)){
            // 4. PAYMENT CONFIRMATION (this is the ONLY check required)
            if (!isset($session['payment_status']) || $session['payment_status'] !== 'paid') {
                $this->saveStripeLog(['log_type'=>'stripe_webhook_checkout_payment_failed_order_found','system_data'=>json_encode(['stripe_session_id'=>$session['id'],'cart_id'=>$cart_id]),'stripe_response'=>$payload]);
                $eventOrder->payment_status='pending';
                $eventOrder->save();
                return response('Payment not completed yet', 200);
            } else {
                $eventOrder->payment_status='paid';
                $eventOrder->save();
            }
        } else {
            // 4. PAYMENT CONFIRMATION (this is the ONLY check required)
            if (!isset($session['payment_status']) || $session['payment_status'] !== 'paid') {
                $this->saveStripeLog(['log_type'=>'stripe_webhook_checkout_payment_failed_order_not_found','system_data'=>json_encode(['stripe_session_id'=>$session['id'],'cart_id'=>$cart_id]),'stripe_response'=>$payload]);
                return response('Payment not completed yet', 200);
            } else {
                $cart = Cart::find($cart_id);
                if(isset($cart->id)){
                    $cart->stripe_payment_intent_id = $payment_intent;
                    $cart->save();
                }
                $orderCon = new FrontOrderController();
                return $orderCon->frontCreateOrder($this,$cart_id,false);
            }
        }
        return response('Order created successfully', 200);
    }
    public function webhook(Request $req)
    {
        $payload = $req->getContent();
        $sigHeader = $req->header('stripe-signature'); // use this to verify

        // TODO: verify webhook signature using your webhook secret (env('STRIPE_WEBHOOK_SECRET'))
        // Implementation omitted here for brevity. Highly recommended to verify signatures.

        $event = json_decode($payload, true);
        if (!$event) {
            return response()->json(['received' => true], 400);
        }

        $type = $event['type'] ?? '';
        $data = $event['data']['object'] ?? [];

        if ($type === 'checkout.session.completed') {
            $session = $data;

            // session.payment_intent contains payment_intent id created ON connected account
            $piId = $session['payment_intent'] ?? null;
            $sessionId = $session['id'] ?? null;
            $metadata = $session['metadata'] ?? [];

            // If we have payment_intent, fetch PaymentIntent (on connected account) to get Charge id
            $connectedAccount = $session['client_reference_id'] ?? $session['metadata']['organizer_account'] ?? null;
            // If you stored organizer_account earlier in your DB, fetch from order row instead.

            if ($piId) {
                // Fetch PaymentIntent from connected account to get the charge object
                $chargeId = $this->retrieveChargeFromPaymentIntent($piId, $connectedAccount);

                // Persist chargeId and update order record (use metadata.order_id if set)
                if (!empty($metadata['order_id'])) {
                    $orderId = $metadata['order_id'];
                    DB::table('orders')->where('id', $orderId)->update([
                        'stripe_payment_intent_id' => $piId,
                        'stripe_charge_id' => $chargeId,
                        'status' => 'paid',
                        'updated_at' => now(),
                    ]);
                } else {
                    // store session->charge mapping
                    DB::table('stripe_sessions')->updateOrInsert(
                        ['stripe_session_id' => $sessionId],
                        ['payment_intent' => $piId, 'charge_id' => $chargeId, 'created_at' => now(), 'updated_at' => now()]
                    );
                }

                // IMPORTANT: find the application_fee id on your platform by listing application_fees filtered by charge
                // (application_fees live on the platform account)
                if ($chargeId) {
                    $appFee = $this->findApplicationFeeByCharge($chargeId);
                    if ($appFee) {
                        $appFeeId = $appFee['id'];
                        // store application_fee_id with order
                        if (!empty($metadata['order_id'])) {
                            DB::table('orders')->where('id', $metadata['order_id'])->update([
                                'application_fee_id' => $appFeeId,
                                'updated_at' => now(),
                            ]);
                        } else {
                            DB::table('stripe_sessions')->where('stripe_session_id', $sessionId)
                                ->update(['application_fee_id' => $appFeeId, 'updated_at' => now()]);
                        }
                    }
                }
            }
        }

        // respond quickly
        return response()->json(['received' => true]);
    }

    /**
     * Helper: retrieve PaymentIntent and return its first charge id.
     * For a PaymentIntent created on a connected account, we must request it from connected account.
     * $connectedAccount optional; if null and you know it was created on a connected account you should pass it.
     */
    protected function retrieveChargeFromPaymentIntent($paymentIntentId, $connectedAccount = null)
    {
        $url = "https://api.stripe.com/v1/payment_intents/{$paymentIntentId}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret . ':');
        $headers = ["Accept: application/json"];
        if ($connectedAccount) {
            $headers[] = "Stripe-Account: {$connectedAccount}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            \Log::error("Stripe PI fetch error: {$err}");
            return null;
        }
        $json = json_decode($resp, true);
        if (isset($json['charges']['data'][0]['id'])) {
            return $json['charges']['data'][0]['id'];
        }
        return null;
    }

    /**
     * Helper: find application fee on **platform** by charge id
     * GET /v1/application_fees?charge=ch_...
     * NOTE: application fees are on the platform account.
     */
    protected function findApplicationFeeByCharge($chargeId)
    {
        $url = "https://api.stripe.com/v1/application_fees?charge={$chargeId}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret . ':');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        print_r($resp);
        if ($err) {
            \Log::error("Stripe application_fees list error: {$err}");
            return null;
        }
        $json = json_decode($resp, true);
        if (!empty($json['data'][0])) {
            return $json['data'][0]; // contains id and amount
        }
        return null;
    }
    protected function getCharge($chargeId)
    {
        $url = "https://api.stripe.com/v1/charges/{$chargeId}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret . ':');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);

        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        curl_close($ch);
        if ($err) {
            return ['status'=>false,'data'=>$err];
        }
        $json = json_decode($resp, true);
        return ['status'=>true,'data'=>$json];
    }

    /**
     * Refund a specified amount (in cents) for a given charge.
     * Request JSON:
     * {
     *   "charge_id": "ch_....",
     *   "amount": 1000,           // cents to refund
     *   "refund_application_fee": false, // optional boolean
     *   "application_fee_id": "fee_..." // optional: if you want to refund app fee exactly
     *   "application_fee_amount": 6 // cents to refund from app fee (if application_fee_id provided)
     * }
     */
    public function refundCharge($order_id,$paymentIntentId,$amount,$applicationFeeAmount)
    {
        //$paymentIntentId = 'pi_xxxxx';

        $ch = curl_init("https://api.stripe.com/v1/payment_intents/$paymentIntentId");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->stripeSecret
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $pi = json_decode($response, true);
        echo '<pre>';
        //print_r($pi);
        //die();
        $chargeId = $pi['latest_charge'];
        //$chargeId = $pi['charges']['data'][0]['id'];
        //$applicationFeeId = $pi['application_fee'];

        //$chargeId = $req->input('charge_id');
        //$amount = intval($req->input('amount', 0));
        $refundApplicationFee = true;
        //$refundApplicationFee = filter_var($req->input('refund_application_fee', false), FILTER_VALIDATE_BOOLEAN);
        //$applicationFeeId = $req->input('application_fee_id');
        //$applicationFeeAmount = intval($req->input('application_fee_amount', 0));

        if (!$chargeId || $amount <= 0) {
            $this->saveStripeLog(['order_id',$order_id,'payment_intent'=>$paymentIntentId,'charge_id'=>$chargeId,'amount'=>$amount,'fee'=>$applicationFeeAmount,'log_type'=>'refund_amt_failed']);
            return ['status'=>false,'error' => 'charge_id and positive amount required'];
        }

        // 1) Create refund for the charge (this refunds money to the customer)
        $post = [
            'charge' => $chargeId,
            'amount' => round($amount*100)
        ];
        /* if ($refundApplicationFee) {
            // This tells Stripe to refund proportional application fee (if any)
            $post['refund_application_fee'] = 'true';
        } */

        $post = http_build_query($post);
        $ch = curl_init('https://api.stripe.com/v1/refunds');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret . ':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            $this->saveStripeLog(['order_id',$order_id,'payment_intent'=>$paymentIntentId,'charge_id'=>$chargeId,'amount'=>$amount,'fee'=>$applicationFeeAmount,'log_type'=>'refund_api_err','stripe_response'=>json_encode(['err'=>$err,'resp'=>$resp])]);
            return ['status'=>false,'error' => $err];
        }
        $refundResp = json_decode($resp, true);
        if (isset($refundResp['error'])) {
            //return response()->json($refundResp, 400);
            $this->saveStripeLog(['order_id',$order_id,'payment_intent'=>$paymentIntentId,'charge_id'=>$chargeId,'amount'=>$amount,'fee'=>$applicationFeeAmount,'log_type'=>'refund_api_err2','stripe_response'=>json_encode(['resp'=>$resp])]);
            return ['status'=>false,'error' => $refundResp];
        }

        // get the charge detail to get the fee id 
        $chargeObj = $this->getCharge($chargeId);
        $applicationFeeId = null;
        if(isset($chargeObj['data']) && isset($chargeObj['data']['application_fee'])){
            $applicationFeeId = $chargeObj['data']['application_fee'];
        }
        
        //print_r($refundResp);
        //print_r($applicationFeeAmount);
        //print_r($chargeObj);
        //echo '1';
        //die();

        // 2) If you want to refund application fee exactly (precise control), call:
        $appFeeRefundResp = null;
        if ($applicationFeeId && $applicationFeeAmount > 0) {
            // POST /v1/application_fees/{fee}/refunds
            $url = "https://api.stripe.com/v1/application_fees/{$applicationFeeId}/refunds";
            $postFee = ['amount' => $applicationFeeAmount*100];
            $postFee = http_build_query($postFee);
            $ch2 = curl_init($url);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch2, CURLOPT_USERPWD, $this->stripeSecret . ':');
            curl_setopt($ch2, CURLOPT_POSTFIELDS, $postFee);
            curl_setopt($ch2, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
            $resp2 = curl_exec($ch2);
            $err2 = curl_error($ch2);
            curl_close($ch2);
            if ($err2) {
                //return response()->json(['error' => $err2], 500);
                //echo '@1';
                //print_r($err2);die();
                $this->saveStripeLog(['order_id',$order_id,'payment_intent'=>$paymentIntentId,'charge_id'=>$chargeId,'fee_id'=>$applicationFeeId,'amount'=>$amount,'fee'=>$applicationFeeAmount,'log_type'=>'fee_refund_api_err','stripe_response'=>json_encode(['err'=>$err2,'resp'=>$resp2])]);
                return ['status'=>false,'error' => $err2];
            }
            $appFeeRefundResp = json_decode($resp2, true);
            if (isset($appFeeRefundResp['error'])) {
                //echo '#1';
                //print_r($appFeeRefundResp);die();
                //return response()->json($appFeeRefundResp, 400);
                $this->saveStripeLog(['order_id',$order_id,'payment_intent'=>$paymentIntentId,'charge_id'=>$chargeId,'fee_id'=>$applicationFeeId,'amount'=>$amount,'fee'=>$applicationFeeAmount,'log_type'=>'fee_refund_api_err2','stripe_response'=>json_encode(['resp'=>$resp2])]);
                return ['status'=>false,'error' => $appFeeRefundResp];
            }
                //echo '$1';
                //print_r($appFeeRefundResp);die();
        }
        //echo '2';
        //die();

        // update DB / order status: you should mark the refunded unit(s) in your order rows here
        // For example: mark DB->orders or DB->order_units as refunded

        //return response()->json(['refund' => $refundResp, 'application_fee_refund' => $appFeeRefundResp]);
        $this->saveStripeLog(['order_id',$order_id,'payment_intent'=>$paymentIntentId,'charge_id'=>$chargeId,'fee_id'=>$applicationFeeId,'amount'=>$amount,'fee'=>$applicationFeeAmount,'log_type'=>'refund_amt_fee_api_success','stripe_response'=>json_encode(['amt_refund_resp'=>$refundResp,'fee_refund_resp'=>$appFeeRefundResp])]);
        return ['status'=>true,'refund' => $refundResp, 'application_fee_refund' => $appFeeRefundResp];
    }
    public function refundChargeOLD(Request $req)
    {
        $chargeId = $req->input('charge_id');
        $amount = intval($req->input('amount', 0));
        $refundApplicationFee = filter_var($req->input('refund_application_fee', false), FILTER_VALIDATE_BOOLEAN);
        $applicationFeeId = $req->input('application_fee_id');
        $applicationFeeAmount = intval($req->input('application_fee_amount', 0));

        if (!$chargeId || $amount <= 0) {
            return response()->json(['error' => 'charge_id and positive amount required'], 400);
        }

        // 1) Create refund for the charge (this refunds money to the customer)
        $post = [
            'charge' => $chargeId,
            'amount' => $amount
        ];
        if ($refundApplicationFee) {
            // This tells Stripe to refund proportional application fee (if any)
            $post['refund_application_fee'] = 'true';
        }

        $ch = curl_init('https://api.stripe.com/v1/refunds');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->stripeSecret . ':');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        if ($err) {
            return response()->json(['error' => $err], 500);
        }
        $refundResp = json_decode($resp, true);
        if (isset($refundResp['error'])) {
            return response()->json($refundResp, 400);
        }

        // 2) If you want to refund application fee exactly (precise control), call:
        $appFeeRefundResp = null;
        if ($applicationFeeId && $applicationFeeAmount > 0) {
            // POST /v1/application_fees/{fee}/refunds
            $url = "https://api.stripe.com/v1/application_fees/{$applicationFeeId}/refunds";
            $postFee = ['amount' => $applicationFeeAmount];

            $ch2 = curl_init($url);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch2, CURLOPT_USERPWD, $this->stripeSecret . ':');
            curl_setopt($ch2, CURLOPT_POSTFIELDS, $postFee);
            curl_setopt($ch2, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
            $resp2 = curl_exec($ch2);
            $err2 = curl_error($ch2);
            curl_close($ch2);
            if ($err2) {
                return response()->json(['error' => $err2], 500);
            }
            $appFeeRefundResp = json_decode($resp2, true);
            if (isset($appFeeRefundResp['error'])) {
                return response()->json($appFeeRefundResp, 400);
            }
        }

        // update DB / order status: you should mark the refunded unit(s) in your order rows here
        // For example: mark DB->orders or DB->order_units as refunded

        return response()->json(['refund' => $refundResp, 'application_fee_refund' => $appFeeRefundResp]);
    }
    function saveStripeLog($data){
        $sLog = new StripeLog();
        if(isset($data['order_id'])){
            $sLog->order_id = $data['order_id'];
        }
        if(isset($data['payment_intent'])){
            $sLog->payment_intent = $data['payment_intent'];
        }
        if(isset($data['payment_session_id'])){
            $sLog->payment_session_id = $data['payment_session_id'];
        }
        if(isset($data['charge_id'])){
            $sLog->charge_id = $data['charge_id'];
        }
        if(isset($data['fee_id'])){
            $sLog->fee_id = $data['fee_id'];
        }
        if(isset($data['log_type'])){
            $sLog->log_type = $data['log_type'];
        }
        if(isset($data['system_data'])){
            $sLog->system_data = $data['system_data'];
        }
        if(isset($data['stripe_response'])){
            $sLog->stripe_response = $data['stripe_response'];
        }
        $sLog->save();
    }
}
