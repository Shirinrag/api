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
    public function login_pos_verifier_post()
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
                    $verify_device_id = $this->model->CountWhereRecord('tbl_pos_verifier_logged_in', array('fk_pos_verifier_id'=>$login_info['id'],'fk_device_id !='=>$pos_device_id['id'],'status'=>1));
                        if($verify_device_id > 0){
                            $response['message'] = "You are already logged in on another device. If you want to login from this device. please logout from another device";
                            
                            $response['code']=201;
                        }else{
                            if(!empty($login_info)){

                                $curl_data =array(
                                    'fk_pos_verifier_id' =>$login_info['id'],
                                    'fk_device_id'=>$pos_device_id['id'],
                                    'status'=>1
                                );
                                 $this->model->insertData('tbl_pos_verifier_logged_in',$curl_data);

                                $response['code'] = REST_Controller::HTTP_OK;;
                                $response['status'] = true;
                                $response['message'] = 'success';
                                $response['data'] = $login_info;
                            } else {
                                $response['code'] = 201;
                                $response['status'] = "wrong_password";
                                $response['message'] = 'Incorrect Password';
                                
                            }      
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

   
}
