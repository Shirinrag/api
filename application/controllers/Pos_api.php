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
                    $check_email_count = $this->model->CountWhereRecord('pa_users', array('email'=>$email,'isActive'=>1,'user_type'=>14));
                    $check_mobile_no_count = $this->model->CountWhereRecord('pa_users', array('phoneNo'=>$mobile_no,'isActive'=>1,'user_type'=>14));
                    $check_user_name_count = $this->model->CountWhereRecord('pa_users', array('username'=>$username,'isActive'=>1,'user_type'=>14));
                    if($check_email_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Email Already exist.';
                        $response['error_status'] = 'email';            
                    }else if($check_mobile_no_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Mobile No Already exist.';                     
                        $response['error_status'] = 'contact_no';       
                    }else if($check_user_name_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Username Already exist.'; 
                        $response['error_status'] = 'username';       
                    }else{
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
                        $curl_data = array(
                            'firstName' =>$first_name,
                            'lastName' =>$last_name,
                            'email' =>$email,
                            'phoneNo' =>$mobile_no,
                            'password' =>dec_enc('encrypt',$password),
                            'user_type' =>14,
                            'username' =>$username,
                            'pan_card'=> $pan_card,
                            'aadhaar_card' =>$aadhaar_card
                        );
                        $this->model->insertData('pa_users',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Registered Successfully';
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
            if (empty($username)) {
                $response['message'] = 'username is required.';
                $response['code'] = 201;
            } else if (empty($password)) {
                $response['message'] = 'Password is required.';
                $response['code'] = 201;
            } else {
                $encryptedpassword = dec_enc('encrypt',$password);
                $check_username_count = $this->model->CountWhereRecord('pa_users',array('username'=>$username));
                if($check_username_count > 0) {       
                    $login_credentials_data = array(
                      "username" => $username,
                      "password" => $encryptedpassword
                    );
                    $login_info = $this->model->selectWhereData('pa_users',$login_credentials_data,'*');
                    if(!empty($login_info)){
                            $response['code'] = REST_Controller::HTTP_OK;;
                            $response['status'] = true;
                            $response['message'] = 'success';
                            $response['data'] = $login_info;
                    } else {
                        $response['code'] = 201;
                        $response['status'] = "wrong_password";
                        $response['message'] = 'Incorrect Password';
                    }      
                }  else {
                    $response['code'] = 201;
                    $response['message'] = 'Incorrect Username';
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
    public function get_all_price_data_on_id()
    {
    	$response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
        	$fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
        	$fk_place_id = $this->input->post('fk_place_id');

        	if(empty($fk_vehicle_type_id)){
        		$response['message'] = "Vehicle Type Id is required";
        		$response['code'] = 201;
        	}else if(empty($fk_place_id)){
        		$response['message'] = "Place id is required";
        		$response['code'] = 201;
        	}else{
        		$hours_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>$fk_vehicle_type_id,'del_status'=>1),array('*'),false);

	            $response['code'] = REST_Controller::HTTP_OK;
	            $response['status'] = true;
	            $response['message'] = 'success';
	            $response['hours_price_slab_data'] = $hours_price_slab;
        	}           
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
}
