<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "-1");
require APPPATH . '/libraries/REST_Controller.php';

class Common extends REST_Controller {

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
                                    // 'notifn_topic' => $phone_no."PAUser",
                                    'user_type'=>$user_type['id']
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
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function login_data_post($value='')
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
                            $response['session_token'] = token_get();
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

}