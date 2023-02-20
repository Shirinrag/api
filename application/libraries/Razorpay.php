<?php
    require(APPPATH.'third_party/razorpay-php/Razorpay.php');
	use Razorpay\Api\Api;
	
	class Razorpay {
	    
	    private $api;
	    
	    function __construct(){
            $CI =& get_instance();
            $api_key = $CI->config->item('api_key');
            $api_secret = $CI->config->item('api_secret');
            $this->api = new Api($api_key, $api_secret);
        }
        
	    function create_order($amount=''){
	        $amount = $amount*100;
	       // $razorpayOrder = $api->order->create(array('amount' => 500, 'receipt' => 'BILL13375649', 'method' => 'netbanking', 'currency' => 'INR', 'bank_account'=> array('account_number'=> '765432123456789','name'=> 'Gaurav Kumar','ifsc'=>'HDFC0000053')));
	       // $all_order = $api->order->all($options);
	       // print_r($all_order);die;
	        $orderData = [
                'receipt'         => 'rcptid_11',
                'amount'          => $amount, // 39900 rupees in paise
                'currency'        => 'INR',
            ];
            $razorpayOrder = $this->api->order->create($orderData);
            return $razorpayOrder->id;
	    }
	    
	    function check_payment_status_order_id($order_id){
	        $payment_info = $this->api->order->fetch($order_id)->payments();
	        $payment_attemt_count = $payment_info->count;
	        if(empty($payment_attemt_count)){
	            $result['payment_status'] = 'failed';
	            $result['payment_message'] = 'No payment attempts found';
	        } else {
	            $last_payment_key = $payment_attemt_count-1;
	            $last_payment_info = $payment_info->items[$last_payment_key];
	           
	            if($last_payment_info->status=='captured' && $last_payment_info->captured==1){
	                $result['payment_status'] = 'success';
	                $result['payment_id'] = $last_payment_info->id;
	                $result['payment_status1'] = $last_payment_info->status;
	                $result['payment_message'] = 'Payment Done Successfully';
	            } else {
	                $result['payment_status'] = 'success';
	                $result['payment_message'] = $last_payment_info->error_reason;
	            }
	        }
	        return $result;
	    }
	    
	   // $orderId = 'order_LHqTDsVW0yl06V';
            // echo $orderId;
            // $payment_info = $api->order->fetch($orderId)->payments();
        //   $payment_info = $api->payment->all($options);
            // $payment_info = $api->payment->fetch('order_LHqTDsVW0yl06V');
            // print_r($payment_info);die;
	    
	}