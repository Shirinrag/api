<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6NzMzODB9.WZOS-riRPPhTFA1-rK1lfjWPwzavYR1ZKz3xsXMJ95E
ini_set("memory_limit", "-1");
require APPPATH . '/libraries/REST_Controller.php';
class User_api extends REST_Controller {
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
    public function register_user_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$first_name = $this->input->post('first_name');
		    	$last_name = $this->input->post('last_name');
		    	$phone_no = $this->input->post('phone_no');
		    	$address = $this->input->post('address');
		    	$profile_image = $this->input->post('profile_image');
		    	$email = $this->input->post('email');
		    	$car_no = $this->input->post('car_no');
		    	$referral_code = $this->input->post('referral_code');
		    	$device_type = $this->input->post('device_type');
		    	$password = $this->input->post('password');

		    	if(empty($first_name)){
		    		$response['message'] = "First Name is required";
		    		$response['code'] = 201;
		    	}else if(empty($last_name)){
		    		$response['message'] = "Last Name is required";
		    		$response['code'] = 201;
		    	}else if(empty($phone_no)){
		    		$response['message'] = "Phone No is required";
		    		$response['code'] = 201;
		    	}else{
		    		$is_file = true;
                    if (!empty($_FILES['profile_image']['name'])) {
                        $image = trim($_FILES['profile_image']['name']);
                        $image = preg_replace('/\s/', '_', $image);
                        $cat_image = mt_rand(100000, 999999) . '_' . $image;
                        $config['upload_path'] = './uploads/';
                        $config['file_name'] = $cat_image;
                        $config['overwrite'] = TRUE;
                        $config["allowed_types"] = 'gif|jpg|jpeg|png|bmp';
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('profile_image')) {
                            $is_file = false;
                            $errors = $this->upload->display_errors();
                            $response['code'] = 201;
                            $response['message'] = $errors;
                        } else {
                            $profile_image = 'uploads/' . $cat_image;
                        }
                    }
            		if ($is_file) {
            				$check_user_count = $this->model->CountWhereRecord('pa_users', array('phoneNo'=>$phone_no,'isActive'=>1));
            				if($check_user_count > 0){

                    			$response['code'] = 201;
                    			$response['status'] = false;
                    			$response['message'] = 'Contact No is Already exist.';            					
            				}else{

            					$user_type = $this->model->selectWhereData('tbl_user_type',array('user_type'=>"User"),array('id'));
            					$curl_data =  array(
            						'firstName' => $first_name,
            						'lastName' =>  $last_name,
            						'email' => $email,
            						'phoneNo' => $phone_no,
            						'address' => $address,
            						'image' => $profile_image,
            						'referal_code' => $referral_code,
            						'userName' => $first_name.$last_name,
            						'device_type' =>$device_type,
            						'notifn_topic' => $phone_no."PAUser",
            						'user_type'=>$user_type['id'],
            						
            					);
            					$inserted_id = $this->model->insertData('pa_users',$curl_data);
            					if(!empty($car_no)){
            						$insert_car_data = array(
	            						'fk_user_id' =>$inserted_id,
	            						'car_number' =>$car_no
	            					);
            						$this->model->insertData('tbl_user_car_details',$insert_car_data);
            					}         					
            					$bonus_amount = $this->model->selectWhereData('tbl_bonus',array('status'=>'1'),array('bonus_amount'));
            					$user_wallet_data = array(
            						'fk_user_id'=>$inserted_id,
            						'amount'=>$bonus_amount['bonus_amount']
            					);
            					$this->model->insertData('tbl_user_wallet',$user_wallet_data);

            					$user_wallet_history = array(
            						'fk_user_id'=>$inserted_id,
            						'add_amount'=>$bonus_amount['bonus_amount'],
            						'total_amount'=>$bonus_amount['bonus_amount'],
            						'fk_payment_type_id'=>1
            					);
            					$this->model->insertData('tbl_user_wallet_history',$user_wallet_history);

            					$user_data = $this->model->selectWhereData('pa_users',array('id'=>$inserted_id),array('*'));
            					$response['code'] = REST_Controller::HTTP_OK;
                                $response['status'] = true;
            					$response['message'] = 'Register Successfully';
            					$response['data'] = $user_data;
            				}
            		}

		    	}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function login_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
        	$phone_no = $this->input->post('phone_no');
        	if(empty($phone_no)){
        		$response['message'] = "Phone No is required";
		    	$response['code'] = 201;
        	}else{
        		$check_user_count = $this->model->CountWhereRecord('pa_users', array('phoneNo'=>$phone_no,'isActive'=>1));
				if($check_user_count == 0){
        			$response['code'] = 201;
        			$response['status'] = false;
        			$response['message'] = 'Contact No does not exist.';            					
				}else{
	        		$user_data = $this->model->selectWhereData('pa_users',array('phoneNo'=>$phone_no),array('*'));
	        		$response['code'] = REST_Controller::HTTP_OK;
	                $response['status'] = true;
					$response['message'] = 'Logged In Successfully';
					$response['data'] = $user_data;
				}
        	}
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_user_profile_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$first_name = $this->input->post('first_name');
		    	$last_name = $this->input->post('last_name');
		    	$address = $this->input->post('address');
		    	$profile_image = $this->input->post('profile_image');
		    	$email = $this->input->post('email');
		    	$user_id = $this->input->post('user_id');

		    	if(empty($first_name)){
		    		$response['message'] = "First Name is required";
		    		$response['code'] = 201;
		    	}else if(empty($last_name)){
		    		$response['message'] = "Last Name is required";
		    		$response['code'] = 201;
		    	}else if(empty($user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else{
		    		$is_file = true;
		    		$profile_image1 ="";
                    if (!empty($_FILES['profile_image']['name'])) {
                        $image = trim($_FILES['profile_image']['name']);
                        $image = preg_replace('/\s/', '_', $image);
                        $cat_image = mt_rand(100000, 999999) . '_' . $image;
                        $config['upload_path'] = './uploads/';
                        $config['file_name'] = $cat_image;
                        $config['overwrite'] = TRUE;
                        $config["allowed_types"] = 'gif|jpg|jpeg|png|bmp';
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('profile_image')) {
                            $is_file = false;
                            $errors = $this->upload->display_errors();
                            $response['code'] = 201;
                            $response['message'] = $errors;
                        } else {
                            	$profile_image = 'uploads/' . $cat_image;
                        }
                    }
            		if ($is_file) {
            			$user_data = $this->model->selectWhereData('pa_users',array('id'=>$user_id),array('image'));
            			if(empty($profile_image)){
            				$profile_image1 = $user_data['image'];
            			}else{
            				$profile_image1 = $profile_image;
            			}

    					$curl_data =  array(
    						'firstName' => $first_name,
    						'lastName' =>  $last_name,
    						'email' => $email,          
    						'address' => $address,
    						'image' => $profile_image1,
    						'userName' => $first_name.$last_name,
    					);
    					$this->model->updateData('pa_users',$curl_data,array('id'=>$user_id));

    					$user_data = $this->model->selectWhereData('pa_users',array('id'=>$user_id),array('*'));
    					$response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
    					$response['message'] = 'User Details Updated Successfully';
    					$response['data'] = $user_data;
            		}

		    	}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function add_user_car_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$user_id = $this->input->post('user_id');
		    	$fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
		    	$car_no = $this->input->post('car_no');
		    	if(empty($user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else if(empty($car_no)){
		    		$response['message'] = "Car No is required";
		    		$response['code'] = 201;
		    	}else if(empty($fk_vehicle_type_id)){
		    		$response['message'] = "Vehicle Type is required";
		    		$response['code'] = 201;
		    	}else{
		   			$check_user_car_count = $this->model->CountWhereRecord('tbl_user_car_details', array('car_number'=>$car_no,'status'=>1));
    				if($check_user_car_count > 0){
            			$response['code'] = 201;
            			$response['status'] = false;
            			$response['message'] = 'Car No is Already exist.';            					
    				}else{
    					$curl_data = array(
    						'fk_user_id' =>$user_id,
    						'car_number' =>$car_no,
    						'fk_vehicle_type_id'=>$fk_vehicle_type_id
    					);
    					$this->model->insertData('tbl_user_car_details',$curl_data);

    					$response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
    					$response['message'] = 'Car Data Inserted Successfully';
    				}
    			}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function delete_user_car_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$user_id = $this->input->post('user_id');
		    	$car_no = $this->input->post('car_no');
		    	if(empty($user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else if(empty($car_no)){
		    		$response['message'] = "Car No is required";
		    		$response['code'] = 201;
		    	}else{
		   			$check_user_car_count = $this->model->CountWhereRecord('tbl_user_car_details', array('car_number'=>$car_no,'status'=>1));
    				if($check_user_car_count == 0){
            			$response['code'] = 201;
            			$response['status'] = false;
            			$response['message'] = 'Car No is does not exist.';            					
    				}else{
    					$curl_data = array(
    						'status'=> 0
    					);
    					// echo '<pre>'; print_r($curl_data); exit;
    					$this->model->updateData('tbl_user_car_details',$curl_data,array('fk_user_id'=>$user_id,'car_number'=>$car_no));

    					$response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
    					$response['message'] = 'Car Data Deleted Successfully';
    				}
    			}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function user_cars_list_data_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$user_id = $this->input->post('user_id');
		    	if(empty($user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else{
		    		$car_list = $this->model->selectWhereData('tbl_user_car_details',array('fk_user_id'=>$user_id,'status'=>1),array('id','car_number','fk_vehicle_type_id'),false);
		    		if(!empty($car_list)){
		    			$response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
    					$response['message'] = 'success';
    					$response['car_list_data'] = $car_list;
		    		}else{
		    			$response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = false;
    					$response['message'] = 'Data not found';
    					$response['car_list_data'] =[];   					
    				}
		    	}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function booking_history_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$user_id = $this->input->post('user_id');
		    	if(empty($user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else{
		    		$this->load->model('user_model');
		    		$booking_history = $this->user_model->booking_history($user_id);
		    		$issue_type = $this->model->selectWhereData('tbl_issue_type',array('status'=>1),array('id','issue_type'),false);
					$response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
					$response['message'] = 'success';
					$response['booking_history_data'] = $booking_history;
					$response['issue_type'] = $issue_type;
    			}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function booking_details_on_id_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$booking_id = $this->input->post('booking_id');
		    	if(empty($booking_id)){
		    		$response['message'] = "Booking Id is required";
		    		$response['code'] = 201;
		    	}else{
		    		$this->load->model('user_model');
		    		$booking_details = $this->user_model->booking_details_on_id($booking_id);

    					$response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
    					$response['message'] = 'success';
    					$response['booking_details_data'] = $booking_details;
    					
    				}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function user_wallet_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$user_id = $this->input->post('user_id');
		    	if(empty($user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else{
		    		$this->load->model('user_model');
		    		$user_wallet = $this->model->selectWhereData('tbl_user_wallet',array('fk_user_id'=>$user_id),array('id','amount'));
					if(!empty($user_wallet)){
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
    					$response['message'] = 'success';
    					$response['user_wallet_data'] = $user_wallet;
                    }else{
                        $response['code'] = 201;
                        $response['status'] = false;
    					$response['message'] = 'No Record';
    					$response['user_wallet_data'] = "";
                    }
    			}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function place_list_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
        	$this->load->model('user_model');
		    $place_data = $this->user_model->place_data();
		    // $inactive_place = $this->user_model->inactive_place_data();
		    // $upcoming_place = $this->user_model->upcoming_place_data();
		    // $other_place = $this->user_model->other_place_data();

			$response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
			$response['message'] = 'success';
			$response['place_data'] = $place_data;
			// $response['inactive_place'] = $inactive_place;
			// $response['upcoming_place'] = $upcoming_place;
			// $response['other_place'] = $other_place;
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function place_details_on_id_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
        	$id = $this->input->post('id');
        	if(empty($id)){
        		$response['message'] = "Id is required";
		    	$response['code'] = 201;
        	}else{
        		$this->load->model('user_model');
			    $place_details = $this->user_model->place_details_on_id($id);
			    $slot_info = $this->model->selectWhereData('tbl_slot_info',array('del_status'=>1,'fk_place_id'=>$id),array('*'),false);
			    $price_details = $this->model->selectWhereData('tbl_hours_price_slab',array('del_status'=>1,'fk_place_id'=>$id),array('*'),false);
				$response['code'] = REST_Controller::HTTP_OK;
	            $response['status'] = true;
				$response['message'] = 'success';
				$response['place_details'] = $place_details;
				$response['slot_info'] = $slot_info;
				$response['price_details'] = $price_details;
        	}        	
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function place_traffic_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
        	$id = $this->input->post('id');
        	$place_id = $this->input->post('place_id');
        	if(empty($id)){
        		$response['message'] = "User Id is required";
		    	$response['code'] = 201;
        	}else if(empty($place_id)){
        		$response['message'] = "Place Id is required";
		    	$response['code'] = 201;
        	}else{
        		$curl_data = array(
    						'fk_user_id' =>$id,
    						'fk_place_id' =>$place_id
    					);
    			$this->model->insertData('tbl_place_traffic',$curl_data);
				$response['code'] = REST_Controller::HTTP_OK;
	            $response['status'] = true;
				$response['message'] = 'success';
        	}        	
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function user_terms_condition_get()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $user_terms_condition = $this->model->selectWhereData('tbl_terms_condition',array('terms_type'=>1),array('terms_condition'));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['user_terms_condition'] = $user_terms_condition;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function place_booking_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$fk_user_id = $this->input->post('fk_user_id');
		    	$fk_car_id = $this->input->post('fk_car_id');
		    	$fk_place_id = $this->input->post('fk_place_id');
		    	$fk_slot_id = $this->input->post('fk_slot_id');
		    	$fk_booking_type_id = $this->input->post('fk_booking_type_id');
		    	$booking_from_date = $this->input->post('booking_from_date');
		    	$booking_to_date = $this->input->post('booking_to_date');
		    	$booking_from_time = $this->input->post('booking_from_time');
		    	$booking_to_time = $this->input->post('booking_to_time');
		    	$longitude = $this->input->post('longitude');
		    	$latitude = $this->input->post('latitude');
		    	$total_hours = $this->input->post('total_hours');
	
		    	if(empty($fk_user_id)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "User Id is required";
		    	}else if(empty($fk_place_id)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "Place Id is required";
		    	}else if(empty($booking_from_date)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "From Date is required";
		    	}else if(empty($booking_to_date)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "To Date is required";
		    	}else if(empty($booking_from_time)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "From time is required";
		    	}else if(empty($booking_to_time)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "To time is required";
		    	}else if(empty($latitude)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "Latitude is required";
		    	}else if(empty($longitude)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "Longitude  is required";
		    	}else if(empty($total_hours)){
		    		$response['code']=201;
		    		$response['status']=false;
		    		$response['message']= "Total hours is required";
		    	}else{	
                	$this->load->model('user_model');
                	$sensor_data = $this->model->CountWhereRecord('tbl_sensor', array('fk_slot_id'=>$fk_slot_id,'fk_place_id'=>$fk_place_id));   	
                	if($sensor_data>0){
             
                		$reserved_slot_info = $this->model->selectWhereData('tbl_booking', array('fk_slot_id'=>$fk_slot_id,'fk_place_id'=>$fk_place_id,'booking_from_date'=>$booking_from_date,'booking_to_date'=>$booking_to_date,'booking_from_time'=>$booking_from_time),array('booking_to_time'));
		                		if($booking_from_time > $reserved_slot_info['booking_to_time']){

		                			$user_wallet_data = $this->model->selectWhereData('tbl_user_wallet',array('fk_user_id'=>$fk_user_id),array('amount'));
			                		if(!empty($user_wallet_data['amount'])){
		                				$vehicle_type_id = $this->model->selectWhereData('tbl_user_car_details',array('id'=>$fk_car_id),array('fk_vehicle_type_id'));
		                				$cost = $this->user_model->get_rate($total_hours,$vehicle_type_id['fk_vehicle_type_id'],$fk_place_id);

		                				if($user_wallet_data['amount'] < $cost['cost']){
		                					$response['message'] ="Insufficient Balance";
		                					$response['code']=201;
		                				}else{
		                						$reserve_from_time= date('H:i:s',strtotime($booking_from_time .'-10 minutes'));         	
		            							$reserve_to_time= date('H:i:s',strtotime($booking_to_time . ' +0 minutes'));
				                				$booking_data = $this->user_model->get_last_booking_id();
				                				// echo '<pre>'; print_r($booking_data); exit;
				                				if(empty($booking_data)){
				                					$new_booking_id  = 'PAB00000001';
				                				}else{
				                						// $booking_data = $this->user_model->get_last_booking_id();
				                						$explode = explode("B",$booking_data['booking_id']);
				                                        $count = 8-strlen($explode[1]+1);
				                                        $bookingId_rep =$explode[1]+1;                                                                              
				                                        for($i=0;$i<$count;$i++){
				                                            $bookingId_rep='0'.$bookingId_rep;
				                                        }
				                                        $new_booking_id = 'PAB'.$bookingId_rep;
				                				}

				                				$curl_data = array(
				                					'booking_id'=> $new_booking_id,
				                					'fk_user_id'=> $fk_user_id,
				                					'fk_car_id'=> $fk_car_id,
				                					'fk_place_id'=> $fk_place_id,
				                					'fk_slot_id' => $fk_slot_id,
				                					'fk_verifier_id'=>$fk_verifier_id,
				                					'fk_booking_type_id'=> $fk_booking_type_id,
				                					'booking_from_date' =>$booking_from_date,
				                					'booking_to_date' => $booking_to_date,
				                					'booking_from_time' => $booking_from_time,
				                					'booking_to_time' => $booking_to_time,
				                					'reserve_from_time' => $reserve_from_time,
				                					'reserve_to_time' => $reserve_to_time,
				                					'fk_booking_type_id'=>1,
				                					'total_hours' => $total_hours
				                				);
				    							$last_inserted_id =  $this->model->insertData('tbl_booking',$curl_data);
				                				   							    							
				    							$payment_data = array(
				    								'fk_booking_id'=>$last_inserted_id,
				    								'fk_user_id'=>$fk_user_id,
				    								'amount'=>$cost['cost'],
				    								'total_amount'=>$cost['cost'],
				    							);
				    							$last_payment_inserted_id =$this->model->insertData('tbl_payment',$payment_data);

				    							$update_payment_id = array('fk_payment_id'=> $last_payment_inserted_id);

				    							$this->model->updateData('tbl_booking',$update_payment_id,array('id'=>$last_inserted_id));

				    							$deactive_used_status = array('used_status'=>0);
				    							$this->model->updateData('tbl_user_wallet_history',$deactive_used_status,array('fk_user_id'=>$fk_user_id));

				    							$insert_user_wallet_history = array(
				    								'fk_user_id'=>$fk_user_id,
				    								'deduct_amount'=>$cost['cost'],
				    								'total_amount'=>$user_wallet_data['amount'] - $cost['cost'],
				    								'fk_payment_type_id'=>3
				    							);
				    							$this->model->insertData('tbl_user_wallet_history',$insert_user_wallet_history);

				    							$update_wallet_data = array(
				    								'amount'=>$user_wallet_data['amount'] - $cost['cost'],
				    							);
				    							$this->model->updateData('tbl_user_wallet',$update_wallet_data,array('fk_user_id'=>$fk_user_id));
				    							$booking_status = array(
				    								'fk_booking_id'=>$last_inserted_id,
				    								'fk_status_id'=>1,
				    								'used_status'=>1
				    							);
				    							$this->model->insertData('tbl_booking_status',$booking_status);

				    							$response['code'] = REST_Controller::HTTP_OK;
						                        $response['status'] = true;
						    					$response['message'] = 'Parking Slot Booked Successfully ';
				                		}
				                	}else{
		                				$response['message'] ="Insufficient Balance Kindly refill your wallet.";
		                				$response['code']=201;
				                	}
		                		}else{
		                			$response['message'] ="This slot is already booked until the"." ".$reserved_slot_info['booking_to_time'];
		                			$response['code']=201;
		                		} 
                			
                	}
		    	}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function extend_place_booking_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $place_id = $this->input->post('place_id');
                $user_id = $this->input->post('user_id');
                $id = $this->input->post('id');
                $from_time = $this->input->post('from_time');
                $to_time = $this->input->post('to_time');
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $no_of_hours = $this->input->post('no_of_hours');
                
                if(empty($place_id)){
                	$response['code']= 201;
                	$response['message'] = "Place Id is required";
                }else if(empty($user_id)){
                	$response['code']= 201;
                	$response['message'] = "User Id is required";
                }else if(empty($id)){
                	$response['code']= 201;
                	$response['message'] = "Booking Id is required";
                }else if(empty($from_time)){
                	$response['code']= 201;
                	$response['message'] = "From Time is required";
                }else if(empty($to_time)){
                	$response['code']= 201;
                	$response['message'] = "To Time is required";
                }else if(empty($from_date)){
                	$response['code']= 201;
                	$response['message'] = "From Date is required";
                }else if(empty($to_date)){
                	$response['code']= 201;
                	$response['message'] = "To Date is required";
                }else if(empty($no_of_hours)){
                	$response['code']= 201;
                	$response['message'] = "No of hours is required";
                }else{
                	$this->load->model('user_model');
                	$booking_details = $this->model->selectWhereData('tbl_booking',array('id'=>$id),array('*')); 

                	$booking_id = $booking_details['booking_id'];

                	$vehicle_type_id = $this->model->selectWhereData('tbl_user_car_details',array('id'=>$booking_details['fk_car_id']),array('fk_vehicle_type_id'));
                	$cost = $this->user_model->get_rate($no_of_hours,$vehicle_type_id['fk_vehicle_type_id'],$place_id);
                	$ext_per_hour = $this->model->selectWhereData('tbl_parking_place',array('id'=>$place_id),array('ext_price','per_hour_charges'));

                	if(!empty($ext_per_hour['per_hour_charges'])){
                		$new_cost = $no_of_hours * $ext_per_hour['per_hour_charges'];
                	}else{
                		$new_cost = $cost['cost'] + (($cost['cost'] * $ext_per_hour['ext_price']) / 100);
                	}          	
                	
		            $user_wallet_data = $this->model->selectWhereData('tbl_user_wallet',array('fk_user_id'=>$fk_user_id),array('amount'));
	                if($user_wallet_data['amount'] < $new_cost){
						$reserve_from_time= date('H:i:s',strtotime($from_time .'+0 minutes'));
						$reserve_to_time= date('H:i:s',strtotime($to_time . ' +0 minutes'));
						$last_ext_booking = $this->user_model->get_last_ext_booking_id($id);
          				if(empty($last_ext_booking)){
        					$new_ext_booking  = 'EXT' . '1';
        				}else{
    						$explode = explode("T",$last_ext_booking['booking_ext_replace']);
                            $count = $explode[1] + 1;
            				$new_ext_booking = 'EXT' . $count;
        				}
        				$curl_data = array(
    						'fk_booking_id' => $id,
    						'fk_place_id' => $place_id,
    						'fk_user_id' => $user_id,
    						'booking_ext_replace' => $new_ext_booking,
    						'booking_from_date' => $from_date,
    						'booking_to_date' => $to_date,
    						'booking_from_time' => $from_time,
    						'booking_to_time' => $to_time,
    						'reserve_from_time' => $reserve_from_time,
    						'reserve_to_time' => $reserve_to_time,
        				);
        				$last_inserted_id = $this->model->insertData('tbl_extension_booking',$curl_data);

        				$payment_data = array(
							'fk_booking_id'=>$id,
							'fk_ext_booking_id'=>$last_inserted_id,
							'fk_user_id'=>$fk_user_id,
							'amount'=>$cost['cost'],
							'charges'=>(($cost['cost'] * $ext_per_hour['ext_price']) / 100),
							'total_amount'=>$new_cost,
						);
						$last_payment_inserted_id = $this->model->insertData('tbl_payment',$payment_data);

						$update_payment_id = array('fk_payment_id'=> $last_payment_inserted_id);
		    							
		    			$this->model->updateData('tbl_extension_booking',$update_payment_id,array('id'=>$last_inserted_id));

        				$deactive_used_status = array('used_status'=>0);
						$this->model->updateData('tbl_user_wallet_history',$deactive_used_status,array('fk_user_id'=>$fk_user_id));

						$insert_user_wallet_history = array(
							'fk_user_id'=>$fk_user_id,
							'deduct_amount'=>$new_cost,
							'total_amount'=>$user_wallet_data['amount'] - $new_cost,
							'fk_payment_type_id'=>3
						);
						$this->model->insertData('tbl_user_wallet_history',$insert_user_wallet_history);

						$update_wallet_data = array(
							'amount'=>$user_wallet_data['amount'] - $new_cost,
						);
						$this->model->updateData('tbl_user_wallet',$update_wallet_data,array('fk_user_id'=>$fk_user_id));
						$booking_status = array(
							'fk_booking_id'=>$id,
							'fk_status_id'=> 1,
							'used_status'=> 1,
						);
						$this->model->insertData('tbl_booking_status',$booking_status);  
						$response['code'] = REST_Controller::HTTP_OK;
                		$response['status'] = true;
                		$response['message'] = 'success';
                	}else{
                		$response['message'] ="Insufficient Balance";
                		$response['code'] = 201;
                	}	              	
                }              
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function booking_cancel_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
        	$booking_id = $this->input->post('booking_id');
        	
        	if(empty($booking_id)){
        		$response['message'] = "Booking Id is required";
        		$response['code'] = 201;
        	}else{
        		$booking_data = $this->model->selectWhereData('tbl_booking',array('id'=>$booking_id),array('booking_from_time','fk_user_id'));

        		$booking_from_time= date('H:i:s',strtotime($booking_data['booking_from_time'] .'-60 minutes'));
        		$current_time = date('H:i:s');
        		if($current_time <= $booking_from_time){
        			$last_booking_status = $this->model->selectWhereData('tbl_booking_status',array('fk_booking_id'=>$booking_id,'status'=>1),array('id'));
        			$update_status = array('used_status'=> 0);
        			$this->model->updateData('tbl_booking_status',$update_status,array('id'=>$last_booking_status['id']));	
        			$insert_data = array(
        				'fk_booking_id'=>$booking_id,
        				'fk_status_id'=>3,
        				'used_status'=>1
        			);
        			$this->model->insertData('tbl_booking_status',$insert_data);
        			// $previous_user_amount = $this->model->selectWhereData('tbl_user_wallet',array('fk_user_id'=>$booking_data['fk_user_id']),array('amount'));
        			$previous_user_wallet_history = $this->model->selectWhereData('tbl_user_wallet_history',array('fk_user_id'=>$booking_data['fk_user_id'],'used_status'=>'1'),array('total_amount','id'));

        			$update_wallet_data = array('used_status'=>'0');
        			$this->model->updateData('tbl_user_wallet_history',$update_wallet_data,array('id'=>$previous_user_wallet_history['id']));
        			$prevoius_booking_amount = $this->model->selectWhereData('tbl_payment',array('fk_booking_id'=>$booking_id),array('amount'));

        			$new_amount = $prevoius_booking_amount['amount'] + $previous_user_wallet_history['total_amount'];

        			$insert_amount_wallet_history = array(
        				'fk_user_id'=>$booking_data['fk_user_id'],
        				'add_amount'=>$new_amount,
        				'total_amount'=>$new_amount,
        				'used_status'=>1
        			); 
        			$this->model->insertData('tbl_user_wallet_history',$insert_user_wallet_history);
        			$update_user_wallet = array('amount'=>$new_amount);
        			$this->model->updateData('tbl_user_wallet',$update_user_wallet,array('fk_user_id'=>$booking_data['fk_user_id']));
        			$response['code'] = REST_Controller::HTTP_OK;
                	$response['status'] = true;
                	$response['message'] = 'Booking Cancelled Successfully';

        		}else{
        			$response['message'] = "You cannot cancel the booking";
        			$response['code']= 201;
        		}       		
        	}
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function delete_user_account_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$user_id = $this->input->post('user_id');
		    	if(empty($user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else{
		    		$curl_data = array('isActive'=>0);
		    		$this->model->updateData('pa_users',$curl_data,array('id'=>$user_id));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
					$response['message'] = 'Account Deleted Successfully';
                   
    			}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function add_place_suggestion()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$fk_user_id = $this->input->post('fk_user_id');
		    	$place_name = $this->input->post('place_name');
		    	$address = $this->input->post('address');
		    	$landmark = $this->input->post('landmark');
		    	$place_image = $this->input->post('place_image');
		    	$latitude = $this->input->post('latitude');
		    	$longitude = $this->input->post('longitude');
		    	if(empty($fk_user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else if(empty($place_name)){
		    		$response['message'] = "Place Name is required";
		    		$response['code'] = 201;
		    	}else if(empty($address)){
		    		$response['message'] = "Address is required";
		    		$response['code'] = 201;
		    	}else if(empty($landmark)){
		    		$response['message'] = "Ladmark is required";
		    		$response['code'] = 201;
		    	}else if(empty($latitude)){
		    		$response['message'] = "Latitude is required";
		    		$response['code'] = 201;
		    	}else if(empty($longitude)){
		    		$response['message'] = "Longitude is required";
		    		$response['code'] = 201;
		    	}else{
		    		$is_file = true;
		    		$profile_image1 ="";
                    if (!empty($_FILES['place_image']['name'])) {
                        $image = trim($_FILES['place_image']['name']);
                        $image = preg_replace('/\s/', '_', $image);
                        $cat_image = mt_rand(100000, 999999) . '_' . $image;
                        $config['upload_path'] = './uploads/place_image/';
                        $config['file_name'] = $cat_image;
                        $config['overwrite'] = TRUE;
                        $config["allowed_types"] = 'gif|jpg|jpeg|png|bmp';
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('place_image')) {
                            $is_file = false;
                            $errors = $this->upload->display_errors();
                            $response['code'] = 201;
                            $response['message'] = $errors;
                        } else {
                            	$place_image = 'uploads/place_image/' . $cat_image;
                        }
                    }
            		if ($is_file) {
            			$curl_data = array(
            				'place_name'=> $place_name,
            				'fk_user_id' => $fk_user_id,
            				'address' => $address,
            				'landmark' =>$landmark,
            				'latitude' =>$latitude,
            				'longitude'=>$longitude,
            				'image'=>$place_image
            			);
            			$this->model->insertData('tbl_place_suggestion',$curl_data);		    		
	                    $response['code'] = REST_Controller::HTTP_OK;
	                    $response['status'] = true;
						$response['message'] = 'Place Inserted Successfully';
					}
                   
    			}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function user_complaint_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$fk_user_id = $this->input->post('fk_user_id');
		    	$fk_place_id = $this->input->post('fk_place_id');
		    	$topic = $this->input->post('topic');
		    	$description = $this->input->post('description');
		    	if(empty($fk_user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else if(empty($fk_place_id)){
		    		$response['message'] = "Place Id is required";
		    		$response['code'] = 201;
		    	}else if(empty($topic)){
		    		$response['message'] = "Issue is required";
		    		$response['code'] = 201;
		    	}else if(empty($description)){
		    		$response['message'] = "Description is required";
		    		$response['code'] = 201;
		    	}else{
		    		$curl_data = array(
		    			'fk_user_id' => $fk_user_id,
		    			'fk_place_id' => $fk_place_id,
		    			'topic' => $topic,
		    			'description'=>$description,
		    		);
		    		$this->model->insertData('tbl_user_complaint',$curl_data);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
					$response['message'] = 'Complaint Register Successfully';                  
    			}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function apply_for_vendor_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
		    	$name = $this->input->post('name');
		    	$address = $this->input->post('address');
		    	$landmark = $this->input->post('landmark');
		    	$apply_type = $this->input->post('apply_type');
		    	if(empty($name)){
		    		$response['message'] = "Name is required";
		    		$response['code'] = 201;
		    	}else if(empty($address)){
		    		$response['message'] = "Address is required";
		    		$response['code'] = 201;
		    	}else if(empty($landmark)){
		    		$response['message'] = "Landmark is required";
		    		$response['code'] = 201;
		    	}else if(empty($apply_type)){
		    		$response['message'] = "Apply Type is required";
		    		$response['code'] = 201;
		    	}else{
		    		$curl_data = array(
		    			'name' => $name,
		    			'address' => $address,
		    			'landmark' => $landmark,
		    			'apply_type'=>$apply_type
		    		);
		    		$this->model->insertData('tbl_apply_for_vendor',$curl_data);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
					$response['message'] = 'Application Submitted Successfully';
                   
    			}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
   	public function place_slot_price_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
	        $id = $this->input->post('id');
	        $from_date = $this->input->post('from_date');
	        $to_date = $this->input->post('to_date');
	        $from_time = $this->input->post('from_time');
	        $to_time = $this->input->post('to_time');
	        $total_hours = $this->input->post('total_hours');
	        $fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
	        if(empty($id)){
	        	$response['message'] = "Id is required";
	    		$response['code'] = 201;
	        }else if(empty($from_date)){
	        	$response['message'] = "From Date is required";
	    		$response['code'] = 201;
	        }else if(empty($to_date)){
	        	$response['message'] = "To Date is required";
	    		$response['code'] = 201;
	        }else if(empty($from_time)){
	       		 $response['message'] = "From Time is required";
	    		$response['code'] = 201;
	        }else if(empty($to_time)){
		        $response['message'] = "To time is required";
		    	$response['code'] = 201;
	        }else if(empty($total_hours)){
		        $response['message'] = "Total Hours is required";
		    	$response['code'] = 201;
	        }else if(empty($fk_vehicle_type_id)){
	        	$response['message'] = "Vehicle Type is required";
	    		$response['code'] = 201;
	        }else{
	        	$this->load->model('user_model');
	        	$cost = $this->user_model->get_rate($total_hours,$fk_vehicle_type_id,$id);
	            $from_time = date('H:i:s', strtotime($from_time));
	            $to_time = date('H:i:s', strtotime($to_time));
				$available_slots = [];
				$reserved_slots = [];
				$not_working_slots = [];
				$parked_slots = [];
				$working_slots_data_1 = [];
				$working_slots_data = $this->model->selectWhereData('tbl_sensor',array('fk_place_id'=>$id),array('fk_slot_id'),false,array('id','DESC'),'fk_slot_id');
				foreach ($working_slots_data as $working_slots_data_key => $working_slots_data_row) {
					$working_slots_data_1[] = $working_slots_data_row['fk_slot_id'];
				}
				$working_slots_data_1 = array_unique($working_slots_data_1,TRUE);
	   			$slot_info = $this->model->selectWhereData('tbl_slot_info',array('del_status'=>1,'fk_place_id'=>$id),array('*'),false);
	   			foreach($slot_info as $slot_info_key => $slot_info_row){
	 
	       			$slots_status = $this->model->selectWhereData('tbl_booking',array('fk_slot_id'=>$slot_info_row['id'],'booking_from_date'=>$from_date,'booking_to_date'=>$to_date,'booking_to_time'=>$from_time),array('fk_verify_booking_status'));      			
	       			
	       			if($slots_status['fk_verify_booking_status']==1){
	       				$slot_info[$slot_info_key]['fk_verify_booking_status'] = $slots_status['fk_verify_booking_status'];	

	       				// $slot_info[$slot_info_key]['color_hexcode'] = "#FF0000";
 	       				$parked_slots[] = $slot_info[$slot_info_key];
	       			}else if($slots_status['fk_verify_booking_status']==2){
	       				$slot_info[$slot_info_key]['fk_verify_booking_status'] = $slots_status['fk_verify_booking_status'];	

	       				// $slot_info[$slot_info_key]['color_hexcode'] = "#FFA500";
	       				$reserved_slots[] = $slot_info[$slot_info_key];
	       			}else if(empty($slots_status['fk_verify_booking_status']) && in_array($slot_info[$slot_info_key]['id'],$working_slots_data_1)){
	       				// $slot_info[$slot_info_key]['color_hexcode'] = "#00FF00";
	       				$available_slots[] = $slot_info[$slot_info_key];
	       			} else {
	       				// $slot_info[$slot_info_key]['color_hexcode'] = "#808080";
	       				$not_working_slots[] = $slot_info[$slot_info_key];
	       			}      			
	   			}   	

				$response['code'] = REST_Controller::HTTP_OK;
				$response['status'] = true;
				$response['message'] = 'success';
				$response['parked_slots'] = $parked_slots;
				$response['reserved_slots'] = $reserved_slots;
				$response['available_slots'] = $available_slots;
				$response['not_working_slots'] = $not_working_slots;
				$response['price'] = @$cost['cost'];
        	}        
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
   public function get_extend_booking_price_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
	        $id = $this->input->post('id');
	        $total_hours = $this->input->post('total_hours');
	        if(empty($id)){
	        	$response['message'] = "Id Id is required";
	    		$response['code'] = 201;
	        }else if(empty($total_hours)){
	        	$response['message'] = "total_hours is required";
	    		$response['code'] = 201;
	        }else{
	        	$this->load->model('user_model');
	        	$booking_details = $this->model->selectWhereData('tbl_booking',array('id'=>$id),array('fk_user_id','fk_place_id'));
	        	$vehicle_type_id = $this->model->selectWhereData('tbl_user_car_details',array('fk_user_id'=>$booking_details['fk_user_id'],'status'=>1),array('fk_vehicle_type_id'));
	        	$cost = $this->user_model->get_rate($total_hours,$vehicle_type_id['fk_vehicle_type_id'],$booking_details['fk_place_id']);
	        	$response['code'] = REST_Controller::HTTP_OK;
				$response['status'] = true;
				$response['message'] = 'success';
				$response['cost'] = $cost['cost'];
	        }
	    }else{
	    	$response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
	    }
	     echo json_encode($response);
    }
    public function user_wallet_rechange_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
        if ($validate) {
	        $user_id = $this->input->post('user_id');
	        $amount = $this->input->post('amount');
	    }else{
	    	$response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
	    }
	     echo json_encode($response);
    }
    public function user_wallet_create_order_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
    	$validate = true;
        if ($validate) {
	        $user_id = $this->input->post('user_id');
	        $amount = $this->input->post('amount');
	        if(empty($user_id)){
	           $response['message'] = "User Id is required";
	    	   $response['code'] = 201;
	        }else if(empty($amount)){
	           $response['message'] = "Amount is required";
	    	   $response['code'] = 201;
	        }else{
	            $this->load->library('razorpay');
	            $order_id = $this->razorpay->create_order($amount);
	            $curl_data = array(
	                'fk_user_id'=>$user_id,
	                'order_id'=>$order_id,
	                'amount'=>$amount,
	            );
	            $this->model->insertData('tbl_transcation',$curl_data);
                $response['code'] = REST_Controller::HTTP_OK;
				$response['status'] = true;
				$response['message'] = 'success';
				$response['order_id'] = $order_id;
	       }
	    }else{
	    	$response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
	    }
	     echo json_encode($response);
    }    
    public function check_payment_status_order_id_post()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
    	$validate = validateToken();
    	$validate = true;
        if ($validate) {
	        $order_id = $this->input->post('order_id');
	        if(empty($order_id)){
	           $response['message'] = "Order Id is required";
	    	   $response['code'] = 201;
	        }else{
	            $this->load->library('razorpay');
	            $payment_status_info = $this->razorpay->check_payment_status_order_id($order_id);
	            if($payment_status_info['payment_status']=='success'){
	                $update_data = array(
	                    'payment_id'=>$payment_status_info['payment_id'],
	                    'payment_status'=>$payment_status_info['payment_status1'],
	                );
	                $this->model->updateData('tbl_transcation',$update_data,array('order_id'=>$order_id));
	                $transcation_data= $this->model->selectWhereData('tbl_transcation',array('order_id'=>$order_id),array('fk_user_id','amount'));
	                $user_id = $transcation_data['fk_user_id'];
	                $amount = $transcation_data['amount'];
	                $last_total_amount = $this->model->selectWhereData('tbl_user_wallet_history',array('fk_user_id'=>$user_id,'used_status'=>1),array('total_amount'));
	                $deactive_used_status = array('used_status'=>0);
				    $this->model->updateData('tbl_user_wallet_history',$deactive_used_status,array('fk_user_id'=>$user_id));
	                $indert_data = array(
	                    'fk_user_id'=>$user_id,
	                    'add_amount'=> $amount,
	                    'total_amount'=> $last_total_amount['total_amount'] + $amount,
	                    'fk_payment_type_id'=> 2
	                );
	                $this->model->insertData('tbl_user_wallet_history',$indert_data);
	                $update_user_wallet = array(
	                    'amount' => $last_total_amount['total_amount'] + $amount,
	                );
	                $this->model->updateData('tbl_user_wallet',$update_user_wallet,array('fk_user_id'=>$user_id));
	                $response['code'] = REST_Controller::HTTP_OK;
    				$response['status'] = true;
    				$response['payment_id'] = $payment_status_info['payment_id'];
    				$response['payment_status1'] = $payment_status_info['payment_status1'];
    				$response['message'] = $payment_status_info['payment_message'];
	            } else {
	    	        $response['code'] = 201;
	    	        $response['message'] = $payment_status_info['payment_message'];
	            }
	       }
	    }else{
	    	$response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
	    }
	     echo json_encode($response);
    }
    public function fcm_notification($value='')
    {
    	define('API_ACCESS_KEY','AAAAVmWHGa8:APA91bHuVMV-6txudhc8FXcln825nV2rsxPO7o89mkvCoHFjxfdwyLNCKeDHnU6ZT8eh3GOHDBflGNUolTb0J9MpQvcsgRiAKjx5NHnlJRUzLeQHOKLkeYnGXJ9etQjHZKMGNunrxU-1');

    	$fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    	$notification = array('body' => $message,'title' =>$title ,'message' =>  $message, 'content_available' => 1,'is_background' =>  false);
        $extraNotificationData = $notification;
        $fcmNotification = [
                 'to' => '/topics/' .$fire_base,
                 'notification' => $extraNotificationData,
        ];          
       	$headers = [
           'Authorization: key=' . API_ACCESS_KEY,
           'Content-Type: application/json'
       	];

       	   $ch = curl_init();
		   curl_setopt($ch, CURLOPT_URL,$fcmUrl);
		   curl_setopt($ch, CURLOPT_POST, true);
		   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		   curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
		   $result = curl_exec($ch);                              
		   curl_close($ch);

    }
}
