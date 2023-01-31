<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "-1");
require APPPATH . '/libraries/REST_Controller.php';

class Pos_api extends REST_Controller {
	public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Type: application/json; charset=utf-8'); 
    }

   /*200 = OK
    201 = Bad Request (Required param is missing)
    202 = No Valid Auth key
    204 = No post data
    203 = Generic Error
    205 = Form Validation failed
    206 = Queury Failed
    207 = Already Logged-In Error
    208 = Curl Failed
    209 = Curl UNAUTHORIZED
    */ 
	public function index() {
        $response = array('status' => false, 'msg' => 'Oops! Please try again later.', 'code' => 200);
        echo json_encode($response);
    }
    public function register_pos_verifier_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $email = $this->input->post('email');
                $mobile_no = $this->input->post('mobile_no');
                $password = $this->input->post('password');
                $username = $this->input->post('username');
                $device_id = $this->input->post('device_id');
                $lang_id = $this->input->post('lang_id');
                if(empty($first_name)){
                    $response['message'] = "First Name is required";
                    $response['code'] = 201;
                }else if(empty($last_name)){
                    $response['message'] = "Last Name is required";
                    $response['code'] = 201;
                }else if(empty($email)){
                    $response['message'] = "Email is required";
                    $response['code'] = 201;
                }else if(empty($mobile_no)){
                    $response['message'] = "Mobile No is required";
                    $response['code'] = 201;
                }else if(empty($password)){
                    $response['message'] = "Password is required";
                    $response['code'] = 201;
                }else if(empty($username)){
                    $response['message'] = "User Name is required";
                    $response['code'] = 201;
                }else if(empty($device_id)){
                    $response['message'] = "Device Id is required";
                    $response['code'] = 201;
                }else{
                   
                    $check_mobile_no_count = $this->model->CountWhereRecord('pa_users', array('phoneNo'=>$mobile_no,'isActive'=>1,'user_type'=>14));
                    $check_user_name_count = $this->model->CountWhereRecord('pa_users', array('username'=>$username,'isActive'=>1,'user_type'=>14));
                     if($check_mobile_no_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        if($lang_id==1){
                            $response['message'] = 'Mobile No Already exist.'; 
                        }else{
                            $response['message'] = 'मोबाइल नंबर पहले से मौजूद है।'; 
                        }
                        $response['error_status'] = 'contact_no';       
                    }else if($check_user_name_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        if($lang_id==1){
                            $response['message'] = 'Username Already exist.'; 
                        }else{
                            $response['message'] = 'उपयोगकर्ता नाम पहले से मौजूद।';
                        }
                        $response['error_status'] = 'username';       
                    }else{
                        $verify_device_id = $this->model->CountWhereRecord('tbl_pos_device_map', array('device_id'=>$device_id));
                        if($verify_device_id > 0){
                                $is_signature_file = true;
                                if (!empty($_FILES['pan_card']['name'])) {
                                    $filename = $_FILES['pan_card']['name'];
                                    $ext = pathinfo($filename, PATHINFO_EXTENSION);
                                    $test_img = $filename;
                                    $test_img = preg_replace('/\s/', '_', $test_img);
                                    $test_image = mt_rand(100000, 999999) . '_' . $test_img;
                                    $config['upload_path'] = './uploads/';
                                    $config['file_name'] = $test_image;
                                    $config['overwrite'] = TRUE;
                                    $config["allowed_types"] = 'png|jpg|jpeg';
                                    $this->load->library('upload', $config);
                                    $this->upload->initialize($config);
                                    if (!$this->upload->do_upload('pan_card')) {
                                        $is_signature_file = false;
                                        $response['code'] = 201;
                                        $response['message'] = $this->upload->display_errors();
                                    } else {
                                        $pan_card = 'uploads/' . $test_image;
                                    }
                                } else if (empty($image)) {
                                    $is_signature_file = false;
                                    $response['status'] = 'failure';
                                    $response['error'] = array('image' => "Image required",);
                                }
                                if (!empty($_FILES['aadhaar_card']['name'])) {
                                    $filename1 = $_FILES['aadhaar_card']['name'];
                                    $ext = pathinfo($filename1, PATHINFO_EXTENSION);
                                    $test_img1 = $filename1;
                                    $test_img1 = preg_replace('/\s/', '_', $test_img1);
                                    $test_image1 = mt_rand(100000, 999999) . '_' . $test_img1;
                                    $config['upload_path'] = './uploads/';
                                    $config['file_name'] = $test_image;
                                    $config['overwrite'] = TRUE;
                                    $config["allowed_types"] = 'png|jpg|jpeg';
                                    $this->load->library('upload', $config);
                                    $this->upload->initialize($config);
                                    if (!$this->upload->do_upload('aadhaar_card')) {
                                        $is_signature_file = false;
                                        $response['code'] = 201;
                                        $response['message'] = $this->upload->display_errors();
                                    } else {
                                        $aadhaar_card = 'uploads/' . $test_image1;
                                    }
                                } else if (empty($image1)) {
                                    $is_signature_file = false;
                                    $response['status'] = 'failure';
                                    $response['error'] = array('image' => "Image required",);
                                }
                                if ($is_signature_file) {
                                        $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));
                                        $curl_data = array(
                                            'firstName' =>$first_name,
                                            'lastName' =>$last_name,
                                            'email' =>$email,
                                            'phoneNo' =>$mobile_no,
                                            'password' =>dec_enc('encrypt',$password),
                                            'user_type' =>14,
                                            'username' =>$username,
                                            'pan_card'=> $pan_card,
                                            'aadhaar_card' =>$aadhaar_card,
                                            'pos_device_id' => $pos_device_id['id']
                                        );
                                        $this->model->insertData('pa_users',$curl_data);
                                        $response['code'] = REST_Controller::HTTP_OK;
                                        $response['status'] = true;
                                        if($lang_id==1){
                                            $response['message'] = 'Registered Successfully';
                                        }else{
                                            $response['message'] = 'सफलतापूर्वक पंजीकृत';
                                        }
                                        
                                }
                        }else{
                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['message'] = 'डिवाइस आईडी मेल नहीं खाती';
                        }
                    }
                }
                    	
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function login_pos_verifier_post()
    {
        	$response = array('code' => - 1, 'status' => false, 'message' => '');
            $username = $this->input->post('username');           
            $password = $this->input->post('password');
            $lang_id = $this->input->post('lang_id');
            $device_id = $this->input->post('device_id');
            if (empty($username)) {
                if($lang_id==1){
                        $response['message'] = 'username is required.';
                }else{
                    $response['message'] = 'उपयोगकर्ता नाम आवश्यक है।';
                }
                $response['code'] = 201;
            } else if (empty($password)) {
                if($lang_id==1){
                    $response['message'] = 'Password is required.';
                }else{
                    $response['message'] = 'पासवर्ड की आवश्यकता है।';
                }
                $response['code'] = 201;
            } else if(empty($device_id)){
                $response['message'] ="Device Id is required";
                $response['code'] =201;
            }else {
                $encryptedpassword = dec_enc('encrypt',$password);
                $check_username_count = $this->model->CountWhereRecord('pa_users',array('username'=>$username));
                if($check_username_count > 0) {                    

                    $login_credentials_data = array(
                      "username" => $username,
                      "password" => $encryptedpassword
                    );
                    $login_info = $this->model->selectWhereData('pa_users',$login_credentials_data,'*');
                    $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));
                    $place_id = $this->model->selectWhereData('tbl_pos_duty_allocation',array('fk_device_id'=>$pos_device_id['id'],'date'=>date('d/m/Y')),array('fk_place_id'));
                    // echo '<pre>'; print_r($place_id); exit;
                    
                    $verify_device_id = $this->model->CountWhereRecord('tbl_pos_verifier_logged_in', array('fk_pos_verifier_id'=>$login_info['id'],'fk_device_id !='=>$pos_device_id['id'],'status'=>1));
            
                        if($verify_device_id > 0){
                            if($lang_id==1){
                                $response['message'] = "You are already logged in on another device. If you want to login from this device. please logout from another device";
                            }else{
                                $response['message'] ='आप पहले से ही किसी अन्य डिवाइस पर लॉग इन हैं। अगर आप इस डिवाइस से लॉग इन करना चाहते हैं। कृपया किसी अन्य डिवाइस से लॉगआउट करें';
                            }
                            $response['code']=201;
                        }else{
                            if(!empty($login_info)){

                                $curl_data =array(
                                    'fk_pos_verifier_id' =>$login_info['id'],
                                    'fk_device_id'=>$pos_device_id['id'],
                                    'status'=>1
                                );
                                 $this->model->insertData('tbl_pos_verifier_logged_in',$curl_data);
                                 $login_info['place_id']= $place_id['fk_place_id'];
                                $response['code'] = REST_Controller::HTTP_OK;;
                                $response['status'] = true;
                                $response['message'] = 'success';
                                $response['data'] = $login_info;
                            } else {
                                $response['code'] = 201;
                                $response['status'] = "wrong_password";
                                if($lang_id==1){
                                    $response['message'] = 'Incorrect Password';
                                }else{
                                    $response['message'] = 'गलत पासवर्ड';
                                }
                            }      
                        }                    
                }  else {
                    $response['code'] = 201;
                    if($lang_id==1){
                        $response['message'] = 'Incorrect Username';
                    }else{
                        $response['message'] = 'ग़लत उपयोगकर्ता नाम';
                    }
                    $response['status'] = "wrong_username";
                    
                }          
            } 
        echo json_encode($response);
    }
    public function get_all_vehicle_type_get()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $vehicle_type = $this->model->selectWhereData('tbl_vehicle_type',array('del_status'=>1,'status'=>1),array('id','vehicle_type'),false);

            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            $response['vehicle_type_data'] = $vehicle_type;
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_all_price_data_on_id_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
        	// $fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
        	$fk_place_id = $this->input->post('fk_place_id');

        	if(empty($fk_place_id)){
        		$response['message'] = "Place id is required";
        		$response['code'] = 201;
        	}else{
                $two_wheller_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>1,'del_status'=>1),array('*'),false);
                $three_wheller_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>2,'del_status'=>1),array('*'),false);
                $four_wheller_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>3,'del_status'=>1),array('*'),false);
        		$truck_van_wheller_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>4,'del_status'=>1),array('*'),false);

	            $response['code'] = REST_Controller::HTTP_OK;
	            $response['status'] = true;
	            $response['message'] = 'success';
                if(!empty($two_wheller_price_slab)){
                    $response['two_wheller_price_slab'] = $two_wheller_price_slab;
                }
                if(!empty($three_wheller_price_slab)){
                       $response['three_wheller_price_slab'] = $three_wheller_price_slab;
                }
                if(!empty($four_wheller_price_slab)){
                       $response['four_wheller_price_slab'] = $four_wheller_price_slab;
                }
                if(!empty($truck_van_wheller_price_slab)){
                       $response['truck_van_wheller_price_slab'] = $truck_van_wheller_price_slab;
                }
        	}           
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function check_in_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $fk_place_id = $this->input->post('fk_place_id');
            $fk_verifier_id = $this->input->post('fk_verifier_id');
            $fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
            $fk_lang_id = $this->input->post('fk_lang_id');
            $car_no = $this->input->post('car_no');
            $phone_no = $this->input->post('phone_no');
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $from_time = $this->input->post('from_time');
            $to_time = $this->input->post('to_time');
            $total_hours = $this->input->post('total_hours');
            $price = $this->input->post('price');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');
            $Mac_address = $this->input->post('Mac_address');
            $book_status = $this->input->post('book_status');
            $device_id = $this->input->post('device_id');

            if(empty($fk_lang_id)){
                $response['message'] ="Language Id is required";
                $response['code'] =201;
            }else if(empty($fk_place_id)){
                $response['message'] ="Place Id is required";
                $response['code'] =201;
            }else if(empty($fk_verifier_id)){
                $response['message'] ="Verifier Id is required";
                $response['code'] =201;
            }else if(empty($fk_vehicle_type_id)){
                $response['message'] ="Vehicle Type Id is required";
                $response['code'] =201;
            }else if(empty($car_no)){
                if($fk_lang_id ==1){
                        $response['message'] ="Car No is required";
                }else{
                    $response['message'] ="कार नंबर आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($from_date)){
                if($fk_lang_id ==1){
                    $response['message'] ="From Date is required";
                }else{
                    $response['message'] ="दिनांक से आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($to_date)){
                if($fk_lang_id ==1){
                    $response['message'] ="To Date is required";
                }else{
                    $response['message'] ="तिथि तक आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($from_time)){
                if($fk_lang_id ==1){
                    $response['message'] ="From Time is required";
                }else{
                    $response['message'] ="समय से आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($latitude)){
                $response['message'] ="Latitude is required";
                $response['code'] =201;
            }else if(empty($longitude)){
                $response['message'] ="Longitude is required";
                $response['code'] =201;
            }else if(empty($book_status)){
                $response['message'] ="Check-in Status is required";
                $response['code'] =201;
            }else if(empty($Mac_address)){
                $response['message'] ="Mac Address is required";
                $response['code'] =201;
            }else if(empty($device_id)){
                $response['message'] ="Device is required";
                $response['code'] =201;
            }else{
                $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));

                $curl_data=array(
                    'fk_place_id'=>$fk_place_id,
                    'fk_verifier_id' =>$fk_verifier_id,
                    'fk_vehicle_type_id'=>$fk_vehicle_type_id,
                    'fk_device_id'=>$pos_device_id['id'],
                    'fk_lang_id'=>$fk_lang_id,
                    'car_no'=>$car_no,
                    'phone_no'=>$phone_no,
                    'from_date'=>$from_date,
                    'to_date'=>$to_date,
                    'from_time'=>$from_time,
                    'to_time'=>$to_time,
                    'total_hours'=>$total_hours,
                    'price'=>$price,
                    'latitude'=>$latitude,
                    'longitude'=>$longitude,
                    'book_status'=>$book_status,
                    'Mac_address' =>$Mac_address,
                );
                $this->model->insertData('tbl_pos_booking',$curl_data);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                if($lang_id==1){
                    $response['message'] = 'Checked-in Successfully';
                }else{
                    $response['message'] = 'चेक-इन सफलतापूर्वक';
                }
            }

        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function check_out_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $fk_place_id = $this->input->post('fk_place_id');
            $fk_verifier_id = $this->input->post('fk_verifier_id');
            $fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
            $fk_lang_id = $this->input->post('fk_lang_id');
            $car_no = $this->input->post('car_no');
            $phone_no = $this->input->post('phone_no');
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $from_time = $this->input->post('from_time');
            $to_time = $this->input->post('to_time');
            $total_hours = $this->input->post('total_hours');
            $price = $this->input->post('price');
            $latitude = $this->input->post('latitude');
            $longitude = $this->input->post('longitude');
            $Mac_address = $this->input->post('Mac_address');
            $book_status = $this->input->post('book_status');
            $device_id = $this->input->post('device_id');

            if(empty($fk_lang_id)){
                $response['message'] ="Language Id is required";
                $response['code'] =201;
            }else if(empty($fk_place_id)){
                $response['message'] ="Place Id is required";
                $response['code'] =201;
            }else if(empty($fk_verifier_id)){
                $response['message'] ="Verifier Id is required";
                $response['code'] =201;
            }else if(empty($fk_vehicle_type_id)){
                $response['message'] ="Vehicle Type Id is required";
                $response['code'] =201;
            }else if(empty($car_no)){
                if($fk_lang_id ==1){
                        $response['message'] ="Car No is required";
                }else{
                    $response['message'] ="कार नंबर आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($from_date)){
                if($fk_lang_id ==1){
                    $response['message'] ="From Date is required";
                }else{
                    $response['message'] ="दिनांक से आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($to_date)){
                if($fk_lang_id ==1){
                    $response['message'] ="To Date is required";
                }else{
                    $response['message'] ="तिथि तक आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($from_time)){
                if($fk_lang_id ==1){
                    $response['message'] ="From Time is required";
                }else{
                    $response['message'] ="समय से आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($to_time)){
                if($fk_lang_id ==1){
                    $response['message'] ="To Time is required";
                }else{
                    $response['message'] ="समय की आवश्यकता है";
                }
                $response['code'] =201;
            } else if(empty($total_hours)){
                if($fk_lang_id ==1){
                    $response['message'] ="Total Hours is required";
                }else{
                    $response['message'] ="कुल घंटे आवश्यक हैं";
                }
                $response['code'] =201;
            }else if(empty($price)){
                if($fk_lang_id ==1){
                    $response['message'] ="Price is required";
                }else{
                    $response['message'] ="मूल्य आवश्यक है";
                }
                $response['code'] =201;
            }else if(empty($latitude)){
                $response['message'] ="Latitude is required";
                $response['code'] =201;
            }else if(empty($longitude)){
                $response['message'] ="Longitude is required";
                $response['code'] =201;
            }else if(empty($book_status)){
                $response['message'] ="Check-in Status is required";
                $response['code'] =201;
            }else if(empty($Mac_address)){
                $response['message'] ="Mac Address is required";
                $response['code'] =201;
            }else if(empty($device_id)){
                $response['message'] ="Device is required";
                $response['code'] =201;
            }else{
               $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));

                $curl_data=array(
                    'fk_place_id'=>$fk_place_id,
                    'fk_verifier_id' =>$fk_verifier_id,
                    'fk_vehicle_type_id'=>$fk_vehicle_type_id,
                    'fk_device_id'=>$pos_device_id['id'],
                    'fk_lang_id'=>$fk_lang_id,
                    'car_no'=>$car_no,
                    'phone_no'=>$phone_no,
                    'from_date'=>$from_date,
                    'to_date'=>$to_date,
                    'from_time'=>$from_time,
                    'to_time'=>$to_time,
                    'total_hours'=>$total_hours,
                    'price'=>$price,
                    'latitude'=>$latitude,
                    'longitude'=>$longitude,
                    'book_status'=>$book_status,
                    'Mac_address' =>$Mac_address,
                );
                $this->model->insertData('tbl_pos_booking',$curl_data);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                if($lang_id==1){
                    $response['message'] = 'Checked-out Successfully';
                }else{
                    $response['message'] = 'चेक-आउट सफलतापूर्वक';
                }
            }

        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function logout_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            
            $fk_pos_verifier_id = $this->input->post('fk_verifier_id');
            $device_id = $this->input->post('device_id');
            $lang_id = $this->input->post('lang_id');
            if(empty($fk_pos_verifier_id)){
                $response['message']= "Verifier Id is required";
                $response['code'] = 201;
            }else if(empty($device_id)){
                $response['message']= "Device Id is required";
                $response['code'] = 201;
            }else{
                $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));
                $curl_data = array(
                    'status'=> 2
                );
                $this->model->updateData('tbl_pos_verifier_logged_in',$curl_data,array('fk_pos_verifier_id'=> $fk_pos_verifier_id,'fk_device_id'=> $pos_device_id['id'],));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                if($lang_id==1){
                        $response['message'] = 'Logout Successfully';
                }else {
                    $response['message'] = 'लॉगआउट सफलतापूर्वक';
                }
            }            
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function pos_report_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {            
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $place_id = $this->input->post('place_id');
            if(empty($from_date)){
                $response['message']= "From Date is required";
                $response['code'] = 201;
            }else if(empty($to_date)){
                $response['message']= "To Date is required";
                $response['code'] = 201;
            }else{  
                $this->load->model('pos_model');
                $booking_data = $this->pos_model->pos_report($place_id,$from_date,$to_date);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['booking_data'] = $booking_data;
            }
        }else{
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function display_all_pos_booking_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $this->load->model('pos_model');
                $pos_booking_data = $this->pos_model->display_all_pos_booking_data();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['pos_booking_data'] = $pos_booking_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
}
