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
		    	$car_no = $this->input->post('car_no');
		    	if(empty($user_id)){
		    		$response['message'] = "User Id is required";
		    		$response['code'] = 201;
		    	}else if(empty($car_no)){
		    		$response['message'] = "Car No is required";
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
    						'car_number' =>$car_no
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
		    		$car_list = $this->model->selectWhereData('tbl_user_car_details',array('fk_user_id'=>$user_id,'status'=>1),array('id','car_number'),false);
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

    					$response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
    					$response['message'] = 'success';
    					$response['booking_history_data'] = $booking_history;
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
		    	// $reserve_from_time = $this->input->post('reserve_from_time');
		    	// $reserve_to_time = $this->input->post('reserve_to_time');

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
		    	}else{
		    		$delay_time1 = rand(100000, 800000);
                	$delay_time = rand(1000000, 1500000) + $delay_time1;	
                	
                	$slot_info = $this->model->selectWhereData('tbl_slot_info',array('id'=>$slot_id,'del_status'=>1,'isBlocked'=>1));
		    	}
		}else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
}
