<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "-1");
require APPPATH . '/libraries/REST_Controller.php';

class Vendor_api extends REST_Controller {
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
    public function vendor_registration_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $phone_no = $this->input->post('phone_no');
                $email = $this->input->post('email');
                $device_id = $this->input->post('device_id');
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
                }else if(empty($email)){
                    $response['message'] = "Emal is required";
                    $response['code'] = 201;
                }else if(empty($password)){
                    $response['message'] = "Password is required";
                    $response['code'] = 201;
                }else{
                    $check_user_count = $this->model->CountWhereRecord('pa_users', array('phoneNo'=>$phone_no,'isActive'=>1,'user_type'=>5));
                    if($check_user_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Contact No is Already exist.';          
                    }else{
                        $user_type = $this->model->selectWhereData('tbl_user_type',array('user_type'=>"Vendor"),array('id'));
                        $curl_data =  array(
                            'firstName' => $first_name,
                            'lastName' =>  $last_name,
                            'email' => $email,
                            'phoneNo' => $phone_no,
                            'userName' => $first_name.$last_name,
                            'device_id' =>$device_id,
                            'device_type' =>$device_type,
                            'notifn_topic' => $phone_no."PAUser",
                            'user_type'=>$user_type['id']                                 
                        );
                        $inserted_id = $this->model->insertData('pa_users',$curl_data);
                        $vendor_data = $this->model->selectWhereData('pa_users',array('id'=>$inserted_id),array('*'));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Register Successfully';
                        $response['data'] = $vendor_data;
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function vendor_login_data_post()
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
                        $response['status'] = false;
                        $response['error_status'] = "wrong_password";
                        $response['message'] = 'Incorrect Password';
                    }      
                }  else {
                    $response['code'] = 201;
                    $response['status'] = false;
                    $response['message'] = 'Incorrect Username';
                    $response['error_status'] = "wrong_username";
                }          
            } 
        echo json_encode($response);
    }
    public function vendor_place_list_data_post()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Vendor Id is required";
                    $response['code'] = 201;
                }else{
                   $this->load->model('vendor_model');
                   $vendor_place_list = $this->vendor_model->get_vendor_place_list($id);
                   $response['code'] = REST_Controller::HTTP_OK;;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['vendor_place_list'] = $vendor_place_list;

                }
            }else{
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
    }
    public function total_earning_data_post()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $vendor_id = $this->input->post('vendor_id');
                if(empty($vendor_id)){
                    $response['message'] = "Vendor Id is required";
                    $response['code'] = 201;
                }else if(empty($from_date)){
                    $response['message'] = "From Date is required";
                    $response['code'] = 201;
                }else if(empty($to_date)){
                    $response['message'] = "To Date is required";
                    $response['code'] = 201;
                }else{
                   $this->load->model('vendor_model');
                    $vendor_place_list = $this->vendor_model->get_vendor_place_list($vendor_id);
                    foreach ($vendor_place_list as $vendor_place_list_key => $vendor_place_list_row) {
                        $total_amount_data = $this->vendor_model->total_earning_data($from_date,$to_date,$vendor_place_list_row['fk_place_id']);
                        $total_amount = explode(',',$total_amount_data['total_amount']);
                        $total_earning = array_sum($total_amount);
                        $vendor_place_list[$vendor_place_list_key]['total_earning'] = $total_earning;
                    }
                   $response['code'] = REST_Controller::HTTP_OK;;
                   $response['status'] = true;
                   $response['message'] = 'success';
                   $response['vendor_place_list'] = $vendor_place_list;
                }
            }else{
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
    }
    public function vendor_booking_history_post()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $vendor_id = $this->input->post('vendor_id');
                $place_id = $this->input->post('place_id');
                if(empty($vendor_id)){
                    $response['message'] = "Vendor Id is required";
                    $response['code'] = 201;
                }else{
                   $this->load->model('vendor_model');                    
                    $upcoming_booking_history = $this->vendor_model->upcoming_booking_history($vendor_id,$place_id);
                    $today_booking_history = $this->vendor_model->today_booking_history($vendor_id,$place_id);
                    $past_booking_history = $this->vendor_model->past_booking_history($vendor_id,$place_id,$from_date,$to_date);
                    
                   $response['code'] = REST_Controller::HTTP_OK;;
                   $response['status'] = true;
                   $response['message'] = 'success';
                   $response['upcoming_booking_history'] = $upcoming_booking_history;
                   $response['today_booking_history'] = $today_booking_history;
                   $response['past_booking_history'] = $past_booking_history;
                }
            }else{
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
    }
}