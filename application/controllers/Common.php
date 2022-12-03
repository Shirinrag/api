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

    public function login_data_post($value='')
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
            $email = $this->input->post('email');           
            $password = $this->input->post('password');
            if (empty($email)) {
                $response['message'] = 'email is required.';
                $response['code'] = 201;
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Provide valid email address.';
                $response['code'] = 201;
            } else if (empty($password)) {
                $response['message'] = 'Password is required.';
                $response['code'] = 201;
            } else {
                $encryptedpassword = dec_enc('encrypt',$password);
                $check_email_count = $this->model->CountWhereRecord('pa_users',array('email'=>$email));
                if($check_email_count > 0) {       
                    $login_credentials_data = array(
                      "email" => $email,
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
                    $response['message'] = 'Incorrect email';
                    $response['status'] = "wrong_email";
                }          
            } 
        echo json_encode($response);
    }

}