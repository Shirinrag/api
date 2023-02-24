<?php 
// Server file
class Pushnotifications {
	// (iOS) Private key's passphrase.
	private static $passphrase = 'joashp';
	// (Windows Phone 8) The name of our push channel.
    private static $channelName = "joashp";
	// Change the above three vriables as per your app.
	private $CI;
	private $API_ACCESS_KEY;
	public function __construct() {
		// parent::__construct();
		$this->CI =& get_instance();
		$this->API_ACCESS_KEY ='AAAASeBlySQ:APA91bG5g4s-FAsFw9zfKEJ638XWzhpSGbeUa4jallP5rh0wG6dozGFrihHYj4bneh3qoGrFS74QO7Ra5l_kuTXpnH40KptG6wZvoZcGJGLBdjwMRLL8F6Ajfv9CWSRNqemDaVlvgHDB';
	}

	public function android($user_id='',$data='') {
		$reg_ids = [];
		$device_key_info = $this->CI->model->selectWhereData('tbl_login',array('id'=>$user_id),array('android_device_id','ios_device_id'));
		$android_device_id = $device_key_info['android_device_id'];
		if(!empty($android_device_id)){
			array_push($reg_ids,$android_device_id);
		}
		$ios_device_id = $device_key_info['ios_device_id'];
		if(!empty($ios_device_id)){
			array_push($reg_ids,$ios_device_id);
		}
        $url = 'https://fcm.googleapis.com/fcm/send';	
 		$message = array('body' => $data['message'],'title' =>$data['title'] ,'message' =>  $data['message'], 'content_available' => 1,'is_background' =>  false,'image'=>@$data['image']);
        $arrayToSend = array('registration_ids' => $reg_ids, 'notification' => $message,'data'=>$message,'priority'=>'high');
 		$json = json_encode($arrayToSend);
        $headers = array (
            'Authorization: key=' . $this->API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        $fields = array(
            'registration_ids' => $reg_ids,
            'data' => $message,
        );
        	$return_curl['curl_response'] = $this->useCurl($url, $headers, json_encode($arrayToSend));
        	$return_curl['registration_ids'] = $reg_ids;
        	$return_curl['arrayToSend'] = $arrayToSend;
        	return $return_curl;
	}
    
	// Sends Push's toast notification for Windows Phone 8 users
	public function WP($data, $uri) {
		$delay = 2;
		$msg =  "<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
		        "<wp:Notification xmlns:wp=\"WPNotification\">" .
		            "<wp:Toast>" .
		                "<wp:Text1>".htmlspecialchars($data['mtitle'])."</wp:Text1>" .
		                "<wp:Text2>".htmlspecialchars($data['mdesc'])."</wp:Text2>" .
		            "</wp:Toast>" .
		        "</wp:Notification>";
		$sendedheaders =  array(
		    'Content-Type: text/xml',
		    'Accept: application/*',
		    'X-WindowsPhone-Target: toast',
		    "X-NotificationClass: $delay"
		);
		$response = $this->useCurl($uri, $sendedheaders, $msg);
		
		$result = array();
		foreach(explode("\n", $response) as $line) {
		    $tab = explode(":", $line, 2);
		    if (count($tab) == 2)
		        $result[$tab[0]] = trim($tab[1]);
		}
		return $result;
	}
	
        // Sends Push notification for iOS users
	public function iOS($data, $devicetoken) {
		$deviceToken = $devicetoken;
		$ctx = stream_context_create();
		// ck.pem is your certificate file
		stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', self::$passphrase);
		// Open a connection to the APNS server
		$fp = stream_socket_client(
			'ssl://gateway.sandbox.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);
		// Create the payload body
		$body['aps'] = array(
			'alert' => array(
			    'title' => $data['mtitle'],
                'body' => $data['mdesc'],
			 ),
			'sound' => 'default'
		);
		// Encode the payload as JSON
		$payload = json_encode($body);
		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
		// Close the connection to the server
		fclose($fp);
		if (!$result)
			return 'Message not delivered' . PHP_EOL;
		else
			return 'Message successfully delivered' . PHP_EOL;
	}
	
	// Curl 
	private function useCurl($url, $headers, $fields = null) {
        // Open connection
        $ch = curl_init();
        if ($url) {
            $ch = curl_init ();
	        curl_setopt ($ch, CURLOPT_URL,$url);
	        curl_setopt ($ch, CURLOPT_POST,true);
	        curl_setopt ($ch, CURLOPT_HTTPHEADER,$headers);
	        curl_setopt ($ch, CURLOPT_RETURNTRANSFER,true);
	        curl_setopt ($ch, CURLOPT_POSTFIELDS, $fields);
        	$result = curl_exec ($ch );
	        if ($result === FALSE) {
	            die('Curl failed: ' . curl_error($ch));
	        }
           	curl_close ( $ch );
            return $result;
        }
    }
}
?>