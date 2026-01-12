<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
//https://trinitytuts.com/razorpay-payment-gateway-php-integration-tutorial/
//https://stackoverflow.com/questions/58213840/how-can-i-use-razorpay-payment-gateway-in-php
//https://www.google.com/search?q=add+razorpay+payment+in+php&oq=add+razorpay+payment+in+php&aqs=chrome..69i57j33i160j33i671l8.10810j0j9&sourceid=chrome&ie=UTF-8
namespace App\Razorpay;
use Razorpay\Api\Api;
class RazorpayCustom {
    var $api_key = 'rzp_test_rdj2U7h9wyM2v9';
    var $api_secret = 'fmJ6I1Je4dDvLULhROiA0czk';
    public function __construct()
    {
        require_once base_path('app/Razorpay/Razorpay.php');
    }
    public static function getPaymentDetails($payment_id){
        require_once base_path('app/Razorpay/Razorpay.php');
        $razorpay = new Api('rzp_test_rdj2U7h9wyM2v9', 'fmJ6I1Je4dDvLULhROiA0czk');
        $payment = $razorpay->payment->fetch($payment_id);
        return $payment;
    }
    public static function createOrder($payload){
        require_once base_path('app/Razorpay/Razorpay.php');
        $razorpay = new Api('rzp_test_rdj2U7h9wyM2v9', 'fmJ6I1Je4dDvLULhROiA0czk');
        $code = rand ( 10000 , 99999 );
        $orderData = [
            'receipt'           => '"'.$code.'"',
            'amount'            => $payload['payment_amt'] * 100,
            'currency'          => 'INR',
            'payment_capture'   => 1
        ];
        $razorpayOrder = $razorpay->order->create($orderData);
       
        $displayCurrency = 'INR';
        $displayAmount = $amount = $orderData['amount'];
        $checkout = 'automatic';
        $data = [
            "key"               => 'rzp_test_rdj2U7h9wyM2v9',
            "amount"            => $amount,
            "name"              => $payload['b_name'],
            "description"       => "",
            "image"             => "",
            "prefill"           => [
                "name"              => $payload['b_name'],
                "email"             => $payload['b_email'],
                "contact"           => $payload['b_phone'],
            ],
            "notes"             => [ // this array is for custom data to get on fetching payment detail
                "address"           => "",
                "merchant_order_id" => "",
            ],
            "theme"             => [
                "color"             => "#F37254"
            ],
            "order_id"          => $razorpayOrder['id'],
        ];
    
        if ($displayCurrency !== 'INR')
        {
            $data['display_currency']  = $displayCurrency;
            $data['display_amount']    = $displayAmount;
        }
    
        $json = json_encode($data);
        return $json;
    }
    
    public static function createOrderOld($data){
        require_once base_path('app/Razorpay/Razorpay.php');
        $razorpay = new Api('rzp_test_rdj2U7h9wyM2v9', 'fmJ6I1Je4dDvLULhROiA0czk');
        $orderData = [
            'receipt'         => '"123"',
            'amount'          =>100 * 100,
            'currency'        => 'INR',
            'payment_capture' => 1
        ];
        $razorpayOrder = $razorpay->order->create($orderData);
       
        $displayCurrency = 'INR';
        $displayAmount = $amount = $orderData['amount'];
        $checkout = 'automatic';
        $data = [
            "key"               => 'rzp_test_rdj2U7h9wyM2v9',
            "amount"            => $amount,
            "name"              => "Aneh Thakur",
            "description"       => "Happy to help :)",
            "image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
            "prefill"           => [
                "name"              => "Aneh Thakur",
                "email"             => "Aneh@gmail.com",
                "contact"           => "9876543210",
            ],
            "notes"             => [
                "address"           => "Customer Address",
                "merchant_order_id" => "12312321",
            ],
            "theme"             => [
                "color"             => "#F37254"
            ],
            "order_id"          => $razorpayOrder['id'],
        ];
    
        if ($displayCurrency !== 'INR')
        {
            $data['display_currency']  = $displayCurrency;
            $data['display_amount']    = $displayAmount;
        }
    
        $json = json_encode($data);

        return $json;
        return $razorpayOrderId = $razorpayOrder['id'];
    }
}