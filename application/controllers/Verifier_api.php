<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "-1");
require APPPATH . '/libraries/REST_Controller.php';

class Verifier_api extends REST_Controller {
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
    public function login_verifier_post()
    {
        	$response = array('code' => - 1, 'status' => false, 'message' => '');
            $username = $this->input->post('username');           
            $password = $this->input->post('password');           
            $device_id = $this->input->post('device_id');
            if (empty($username)) {
                
                $response['message'] = 'username is required.';
                $response['code'] = 201;
            } else if (empty($password)) {                
                $response['message'] = 'Password is required.';
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
                 
                            if(!empty($login_info)){
                                $curl_data =array(
                                    'fk_verifier_id' =>$login_info['id'],
                                    'login_time'=>date("Y-m-d H:i:s"),
                                    'status'=>1
                                );
                                 $this->model->insertData('tbl_verifier_login',$curl_data);

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

    public function logout_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            
            $fk_verifier_id = $this->input->post('fk_verifier_id');
           
            if(empty($fk_verifier_id)){
                $response['message']= "Verifier Id is required";
                $response['code'] = 201;
            }else{
                $pos_device_id = $this->model->selectWhereData('tbl_verifier_login',array('status'=>1),array('id'));
                $curl_data = array(
                    'status'=> 2,
                    'logout_time'=>date("Y-m-d H:i:s"),
                );
                $this->model->updateData('tbl_verifier_login',$curl_data,array('fk_verifier_id'=> $fk_verifier_id));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'Logout Successfully';
            }            
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function verify_booking_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $booking_id = $this->input->post('booking_id');
            $verifier_id = $this->input->post('verifier_id');
            $booking_type = $this->input->post('booking_type');
            $verify_status = $this->input->post('verify_status');

            if(empty($booking_id)){
                $response['message'] = "Booking Id is required";
                $response['code']= 201;
            }elseif(empty($verifier_id)){
                $response['message'] = "Verifier Id is required";
                $response['code']= 201;
            }else if(empty($booking_type)){
                $response['message'] = "Booking Type is required";
                $response['code']= 201;
            }else if(empty($verify_status)){
                $response['message'] = "Verify Status is required";
                $response['code']= 201;
            }else{
                 $curl_data = array(
                    'fk_booking_id' =>$booking_id,
                    'fk_verifier_id'=> $verifier_id,
                    'fk_booking_type_id'=> $booking_type,
                    'verify_status' => $verify_status,
                 );
                 $this->model->insertData('tbl_booking_verify',$curl_data);

                 $booking_unique_id = $this->model->selectWhereData('tbl_booking',array('id'=>$booking_id),array('booking_id'));
                 $check_in_booking = array(
                    'fk_booking_id'=> $booking_id,
                    'check_in' => date("Y-m-d H:i:s"),
                    'fk_verifier_id'=> $verifier_id,
                    'fk_booking_check_type' => 1
                 );
                 $this->model->insertData('tbl_booking_check_in_out',$check_in_booking);
                 
                 $response['code'] = REST_Controller::HTTP_OK;
                 $response['status'] = true;
                 $response['message'] = "Your Booking'". $booking_unique_id['booking_id'] ."' is successfully verified by our Guid. '.'🚗😃 ";
            }
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function verifier_booking_issue_raised_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $verifier_id = $this->input->post('verifier_id');
            $place_id = $this->input->post('place_id');
            $slot_id = $this->input->post('slot_id');
            $booking_id = $this->input->post('booking_id');
            $complaint_text = $this->input->post('complaint_text');
            $issue_image = $this->input->post('issue_image');

            if(empty($verifier_id)){
                $response['message'] = "Verifier Id is required";
                $response['code'] = 201;
            }else if(empty($place_id)){
                $response['message'] = "Place Id is required";
                $response['code'] = 201;
            }else if(empty($slot_id)){
                $response['message'] = "Slot Id is required";
                $response['code'] = 201;
            }else if(empty($booking_id)){
                $response['message'] = "Booking Id is required";
                $response['code'] = 201;
            }else if(empty($complaint_text)){
                $response['message'] = "Comlaint Text is required";
                $response['code'] = 201;
            }else{
                $is_file = true;
                    if (!empty($_FILES['issue_image']['name'])) {
                        $image = trim($_FILES['issue_image']['name']);
                        $image = preg_replace('/\s/', '_', $image);
                        $cat_image = mt_rand(100000, 999999) . '_' . $image;
                        $config['upload_path'] = './uploads/complaint/';
                        $config['file_name'] = $cat_image;
                        $config['overwrite'] = TRUE;
                        $config["allowed_types"] = 'gif|jpg|jpeg|png|bmp';
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload('issue_image')) {
                            $is_file = false;
                            $errors = $this->upload->display_errors();
                            $response['code'] = 201;
                            $response['message'] = $errors;
                        } else {
                            $issue_image = 'uploads/complaint/' . $cat_image;
                        }
                    }
                    if ($is_file) {
                        $curl_data = array(
                            'fk_verifier_id'=>$verifier_id,
                            'fk_place_id' =>$place_id,
                            'fk_booking_id'=>$booking_id,
                            'fk_slot_id'=>$slot_id,
                            'complaint_text'=>$complaint_text,
                            'source'=>1,
                            'image'=>$issue_image,
                        );
                        $this->model->insertData('tbl_verifier_complaint',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message']= "Complaint Raised Successfully";
                    }
            }

        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function booking_list_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $place_id = $this->input->post('place_id');
                if(empty($place_id)){
                    $response['message'] = "Place Id is required";
                    $response['code'] = 201;
                }else{
                        $this->load->model('user_model');
                        $ongoing_unverified_booking_list = $this->user_model->ongoing_unverified_booking_list($place_id);
                        $ongoing_verified_booking_list = $this->user_model->ongoing_verified_booking_list($place_id);
                        $complete_booking = $this->user_model->complete_booking_list($place_id);
                        $history_booking = $this->user_model->history_booking_list($place_id);

                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'success';
                        $response['ongoing_unverified_booking_list'] = $ongoing_unverified_booking_list;
                        $response['ongoing_verified_booking_list'] = $ongoing_verified_booking_list;
                        $response['complete_booking'] = $complete_booking;
                        $response['history_booking'] = $history_booking;                  
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function booking_details()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
   
}
