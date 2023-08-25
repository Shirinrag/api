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
            // $device_id = $this->input->post('device_id');

            if (empty($username)) {                
                $response['message'] = 'username is required.';
                $response['code'] = 201;
            } else if (empty($password)) {                
                $response['message'] = 'Password is required.';
                $response['code'] = 201;
            }else {
                $encryptedpassword = dec_enc('encrypt',$password);
                // $encryptedpassword = dec_enc('decrypt',$password);
                // print_r($encryptedpassword);die;
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
                                 $this->model->insertData('tbl_verifier_logged_in',$curl_data);
                                 $place_id = $this->model->selectWhereData('tbl_duty_allocation',array('fk_verifier_id'=>$login_info['id']),array('fk_place_id'),true,array('id','DESC'));
                                  $referral_code = $this->model->selectWhereData('tbl_parking_place',array('id'=>$place_id['fk_place_id']),array('referral_code'));
                                 $login_info['place_id'] = $place_id['fk_place_id'];
                                 $login_info['referral_code'] = $referral_code['referral_code'];
                                 

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
    public function logout_verifier_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            
            $fk_verifier_id = $this->input->post('fk_verifier_id');
           
            if(empty($fk_verifier_id)){
                $response['message']= "Verifier Id is required";
                $response['code'] = 201;
            }else{
                $pos_device_id = $this->model->selectWhereData('tbl_verifier_logged_in',array('status'=>1),array('id'));
                $curl_data = array(
                    'status'=> 2,
                    'logout_time'=>date("Y-m-d H:i:s"),
                );
                $this->model->updateData('tbl_verifier_logged_in',$curl_data,array('fk_verifier_id'=> $fk_verifier_id));
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

                 $booking_details = $this->model->selectWhereData('tbl_booking',array('id'=>$booking_id),array('booking_id','fk_user_id'));
                 $check_in_booking = array(
                    'fk_booking_id'=> $booking_id,
                    'check_in' => date("Y-m-d H:i:s"),
                    'fk_verifier_id'=> $verifier_id,
                    'fk_booking_check_type' => 1
                 );
                 $this->model->insertData('tbl_booking_check_in_out',$check_in_booking);
                 
                 $this->model->updateData('tbl_booking',array('fk_verify_booking_status'=>1),array('id'=>$booking_id));
                 $response['code'] = REST_Controller::HTTP_OK;
                 $response['status'] = true;
                 $response['message'] = "Your Booking'". $booking_details['booking_id'] ."' is successfully verified by our Guide. '.'🚗😃 ";

                $this->load->model('pushnotification_model');
                $this->pushnotification_model->verify_booking($booking_details['fk_user_id'],$booking_details['booking_id']);
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
            $fk_issue_type_id = $this->input->post('fk_issue_type_id');

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
                            'fk_issue_type_id'=>$fk_issue_type_id,
                            'issue_type' =>1,
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
                        foreach($ongoing_unverified_booking_list as $ongoing_unverified_booking_list_key => $ongoing_unverified_booking_list_row){
                            $verify_status = $this->model->selectWhereData('tbl_booking_verify',array('fk_booking_id'=>$ongoing_unverified_booking_list_row['id']),array('verify_status'));
                            $ongoing_unverified_booking_list[$ongoing_unverified_booking_list_key]['verify_status'] = @$verify_status['verify_status'];
                        }
                        $ongoing_verified_booking_list = $this->user_model->ongoing_verified_booking_list($place_id);
                        $complete_booking = $this->user_model->complete_booking_list($place_id);
                        $history_booking = $this->user_model->history_booking_list($place_id);
                        $issue_type = $this->model->selectWhereData('tbl_issue_type',array('status'=>1),array('id','issue_type'),false);

                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'success';
                        $response['ongoing_unverified_booking_list'] = $ongoing_unverified_booking_list;
                        $response['ongoing_verified_booking_list'] = $ongoing_verified_booking_list;
                        $response['complete_booking'] = $complete_booking;
                        $response['history_booking'] = $history_booking;    
                        $response['issue_type'] = $issue_type;              
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function booking_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $id = $this->input->post('id');
            if(empty($id)){
                $response['message'] = "Id is required";
                $response['code']=201;
            }else{
                $this->load->model('user_model');
                $booking_details = $this->user_model->booking_details_on_id($id);
                $booking_details['extend_booking'] = $this->user_model->extend_booking($id);
                
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['booking_details_data'] = $booking_details;
                
            }
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_all_price_details_post()
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
                $this->load->model('superadmin_model');
                $two_wheller_price_slab = $this->superadmin_model->two_wheller_price_slab($fk_place_id);
                $three_wheller_price_slab = $this->superadmin_model->three_wheller_price_slab($fk_place_id);
                $four_wheller_price_slab = $this->superadmin_model->four_wheller_price_slab($fk_place_id);
                $truck_van_wheller_price_slab = $this->superadmin_model->truck_van_wheller_price_slab($fk_place_id);

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
    public function verifier_extend_place_booking_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $verifier_id = $this->input->post('verifier_id');
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
                }else if(empty($verifier_id)){
                    $response['code']= 201;
                    $response['message'] = "Verifier Id is required";
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
                    // echo '<pre>'; print_r($vehicle_type_id); exit;                   
                    $cost = $this->user_model->get_rate($no_of_hours,$vehicle_type_id['fk_vehicle_type_id'],$place_id);
                    // echo '<pre>'; print_r($cost); exit;
                    $ext_per_hour = $this->model->selectWhereData('tbl_parking_place',array('id'=>$place_id),array('ext_price','per_hour_charges'));
                    if(!empty($ext_per_hour['per_hour_charges'])){
                        $new_cost = $no_of_hours * $ext_per_hour['per_hour_charges'];
                    }else{
                        $new_cost = $cost['cost'] + (($cost['cost'] * $ext_per_hour['ext_price']) / 100);
                    }       
                    // echo '      
                    
                    $user_wallet_data = $this->model->selectWhereData('tbl_user_wallet',array('fk_user_id'=>$user_id),array('amount'));
                    // echo '<pre>'; print_r($user_wallet_data); exit;
                    if($new_cost < $user_wallet_data['amount']){
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
                            'fk_user_id'=>$user_id,
                            'amount'=>$cost['cost'],
                            'charges'=>(($cost['cost'] * $ext_per_hour['ext_price']) / 100),
                            'total_amount'=>$new_cost,
                        );
                        $last_payment_inserted_id = $this->model->insertData('tbl_payment',$payment_data);

                        $update_payment_id = array('fk_payment_id'=> $last_payment_inserted_id);
                                        
                        $this->model->updateData('tbl_extension_booking',$update_payment_id,array('id'=>$last_inserted_id));

                        $deactive_used_status = array('used_status'=>0);
                        $this->model->updateData('tbl_user_wallet_history',$deactive_used_status,array('fk_user_id'=>$user_id));

                        $insert_user_wallet_history = array(
                            'fk_user_id'=>$user_id,
                            'deduct_amount'=>$new_cost,
                            'total_amount'=>$user_wallet_data['amount'] - $new_cost,
                            'fk_payment_type_id'=>3
                        );
                        $this->model->insertData('tbl_user_wallet_history',$insert_user_wallet_history);

                        $update_wallet_data = array(
                            'amount'=>$user_wallet_data['amount'] - $new_cost,
                        );
                        $this->model->updateData('tbl_user_wallet',$update_wallet_data,array('fk_user_id'=>$user_id));
                        $booking_status = array(
                            'fk_booking_id'=>$id,
                            'fk_status_id'=> 1,
                            'used_status'=> 1,
                        );
                        $this->model->insertData('tbl_booking_status',$booking_status);

                        $this->load->model('pushnotification_model');
                        $this->pushnotification_model->extended_booking_by_verifier($user_id,$booking_details['booking_id'],$new_cost);    
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
    public function verifier_dashboard_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $verifier_id = $this->input->post('verifier_id');
            $user_type = $this->input->post('user_type');
            $place_id = $this->input->post('place_id');
            if(empty($verifier_id)){
                $response['message'] = "Verifier Id is required";
                $response['code'] = 201;
            }else if(empty($user_type)){
                $response['message'] = "Role Id is required";
                $response['code'] = 201;
            }else{
                $this->load->model('verifier_model');
                $place_details = $this->verifier_model->place_details($verifier_id,$place_id,$user_type);
                $checkout_status = $this->model->selectWhereData('tbl_booking_checkout_status',array('status'=>1),array('id','checkout_status'),false);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['place_details'] = $place_details;
                $response['checkout_status'] = $checkout_status;
            }

        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function not_verified_and_followup_booking_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $verifier_id = $this->input->post('verifier_id');
            // $user_type = $this->input->post('user_type');
            $place_id = $this->input->post('place_id');
            if(empty($verifier_id)){
                $response['message'] = "Verifier Id is required";
                $response['code'] = 201;
            }else if(empty($place_id)){
                $response['message'] = "Place Id is required";
                $response['code'] = 201;
            }else{
                $this->load->model('verifier_model');
                $not_verified_booking_list = $this->verifier_model->not_verified_booking_list($verifier_id,$place_id);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['not_verified_booking_list'] = $not_verified_booking_list;
            }

        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function check_out_booking_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
           
            $checkout_status = $this->input->post('checkout_status');
            $checkout_time = $this->input->post('checkout_time');
            $id = $this->input->post('id'); // tbl_booking auto increment id
            if(empty($id)){
                $response['message'] = "Id is required";
                $response['code'] = 201;
            }else if(empty($checkout_status)){
                $response['message'] = "Checkout Status is required";
                $response['code'] = 201;
            }else if(empty($checkout_time)){
                $response['message'] = "Checkout Time is required";
                $response['code'] = 201;
            }else{
                $curl_data= array(
                    'fk_booking_check_type'=>2,
                    'fk_booking_checkout_status'=>$checkout_status,
                    'check_out'=>date("Y-m-d H:i:s"),
                );
                 $this->model->updateData('tbl_booking_check_in_out',$curl_data,array('fk_booking_id'=>$id));      
                 $this->model->updateData('tbl_booking_status',array('used_status'=>0),array('fk_booking_id'=>$id));

                $booking_status = array(
                    'fk_booking_id'=>$id,
                    'fk_status_id'=> 2,
                    'used_status'=> 1,
                );
                $this->model->insertData('tbl_booking_status',$booking_status);
                $this->model->updateData('tbl_booking',array('fk_verify_booking_status'=> NULL),array('id'=>$id));    
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'Checked Out Successfully';             
            }
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    // This booking_confirmation api is for huma place 
    public function booking_confirmation_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $id = $this->input->post('id'); // tbl_booking auto increment id
            $confirmation_status = $this->input->post('confirmation_status'); // 1 : Approve, 2: Rejected
            if(empty($id)){
                $response['message'] = "Id is required";
                $response['code'] = 201;
            }else if(empty($confirmation_status)){
                $response['message'] = "Confirmation Status is required";
                $response['code'] = 201;
            }else{
                $curl_data= array(
                    'confirmation_status'=> $confirmation_status
                );
                $this->model->updateData('tbl_booking',$curl_data,array('id'=>$id));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'Booking Confirmation Done Successfully';
            }
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);    
    }
    // public function slot_status_details_post()
    // {
    //     $response = array('code' => - 1, 'status' => false, 'message' => '');
    //     $validate = validateToken();
    //     if ($validate) {
    //         $id = $this->input->post('id');
    //         if(empty($id)){
    //             $response['message'] = "Id is required";
    //             $response['code'] = 201;
    //         }else{
    //             $this->load->model('user_model');
    //             $current_time = date("H:i:s");
    //             $from_date = date("Y-m-d");
    //             $to_date = date("Y-m-d");
    //             $from_time = date('H:i:s', strtotime($current_time));
               
    //             $available_slots = [];
    //             $reserved_slots = [];
    //             $not_working_slots = [];
    //             $parked_slots = [];
    //             $working_slots_data_1 = [];
    //             $working_slots_data = $this->model->selectWhereData('tbl_sensor',array('fk_place_id'=>$id),array('fk_slot_id'),false,array('id','DESC'),'fk_slot_id');
    //             foreach ($working_slots_data as $working_slots_data_key => $working_slots_data_row) {
    //                 $working_slots_data_1[] = $working_slots_data_row['fk_slot_id'];
    //             }
    //             $working_slots_data_1 = array_unique($working_slots_data_1,TRUE);
    //             $slot_info = $this->model->selectWhereData('tbl_slot_info',array('del_status'=>1,'fk_place_id'=>$id),array('*'),false);
    //             foreach($slot_info as $slot_info_key => $slot_info_row){
     
    //                 $slots_status = $this->model->selectWhereData('tbl_booking',array('fk_slot_id'=>$slot_info_row['id'],'booking_from_date'=>$from_date,'booking_to_date'=>$to_date,),array('fk_verify_booking_status'));                 
                    
    //                 if($slots_status['fk_verify_booking_status']==1){
    //                     $slot_info[$slot_info_key]['fk_verify_booking_status'] = $slots_status['fk_verify_booking_status']; 
    //                     $slot_info[$slot_info_key]['color_hexcode'] = "#FF0000";
    //                     $parked_slots[] = $slot_info[$slot_info_key];
    //                 }else if($slots_status['fk_verify_booking_status']==2){
    //                     $slot_info[$slot_info_key]['fk_verify_booking_status'] = $slots_status['fk_verify_booking_status']; 
    //                     $slot_info[$slot_info_key]['color_hexcode'] = "#FFA500";
    //                     $reserved_slots[] = $slot_info[$slot_info_key];
    //                 }else if(empty($slots_status['fk_verify_booking_status']) && in_array($slot_info[$slot_info_key]['id'],$working_slots_data_1)){
    //                     $slot_info[$slot_info_key]['color_hexcode'] = "#00FF00";
    //                     $available_slots[] = $slot_info[$slot_info_key];
    //                 } else {
    //                     $slot_info[$slot_info_key]['color_hexcode'] = "#808080";
    //                     $not_working_slots[] = $slot_info[$slot_info_key];
    //                 }               
    //             }       
    //             $response['code'] = REST_Controller::HTTP_OK;
    //             $response['status'] = true;
    //             $response['message'] = 'success';
    //             $response['parked_slots'] = $parked_slots;
    //             $response['reserved_slots'] = $reserved_slots;
    //             $response['available_slots'] = $available_slots;
    //             $response['not_working_slots'] = $not_working_slots;
    //         }        
    //     }else {
    //         $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
    //         $response['message'] = 'Unauthorised';
    //     }
    //     echo json_encode($response);
    // }
     public function slot_status_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $id = $this->input->post('id');
            $phone_no = $this->input->post('phone_no');
            if(empty($id)){
                $response['message'] = "Id is required";
                $response['code'] = 201;
            }else{
                $this->load->model('user_model');
                $current_time = date("H:i:s");
                $from_date = date("Y-m-d");
                $to_date = date("Y-m-d");
                $from_time = date('H:i:s', strtotime($current_time));
                
                // aaj chi date yete to_date madhe
                $currentdatetime = strtotime(date("Y-m-d H:i:s"));
                $enddate = strtotime("-3 min", $currentdatetime);
                $currentdatetime_d = date("Y-m-d H:i:s", $currentdatetime);
                $enddate_d = date("Y-m-d H:i:s", $enddate);
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
                $verifier_id = $this->model->selectWhereData('pa_users',array('phoneNo'=>$phone_no),array('id'));
                foreach($slot_info as $slot_info_key => $slot_info_row){
                    // 'booking_to_time'=>$from_time , ,'booking_to_date'=>$to_date
                    $slots_status = $this->model->selectWhereData('tbl_booking',array('fk_slot_id'=>$slot_info_row['id'],'booking_from_date'=>$from_date,),array('fk_verify_booking_status','booking_to_date','booking_from_time','booking_to_time'),true,array('id','DESC'));
                     $to_date_time = $slots_status['booking_to_date'].$slots_status['booking_to_time'];
                     $to_date_1 = $slots_status['booking_to_date'];
                     $to_time_1 = $slots_status['booking_to_time'];
                     $combinedtoDT = date('Y-m-d H:i:s', strtotime("$to_date_1 $to_time_1"));
                     $from_date_1 = $slots_status['booking_to_date'];
                     $from_time_1 = $slots_status['booking_to_time'];
                     $combinedFromDT = date('Y-m-d H:i:s', strtotime("$to_date_1 $to_time_1"));
                     if(strtotime()){

                     }
                     echo $combinedDT;
                     echo '<br>';
                     echo $currentdatetime;
                     die;
                    echo '<pre>'; print_r($slots_status);die;
                    $sensor_status = $this->model->selectWhereData('tbl_sensor',array('fk_place_id'=>$id,'fk_slot_id'=>$slot_info_row['id']),array('status','id','notification_status'),true,array('id','DESC'));
                    if($slots_status['fk_verify_booking_status']==1 || $sensor_status['status']==1){
                        $slot_info[$slot_info_key]['fk_verify_booking_status'] = $slots_status['fk_verify_booking_status']; 
                        $slot_info[$slot_info_key]['color_hexcode'] = "#FF0000";
                        $parked_slots[] = $slot_info[$slot_info_key];
                        if($sensor_status['notification_status'] != 1){
                                    define( 'API_ACCESS_KEY', 'AAAA76t6JqE:APA91bFQJmeXSI-NcWbRP0aGoREfvUlF-fyEywl-7MuavHYgSdTeUWynOmVk_itfxUitP6sVj3JHP0IUDtU_oVf4wy5RpQBWP_P-qYIW9NFLBayfHc2iZT3JNuevu7_MZtj_VKsRdDgz' );
                        
                                    $data =  array("to" => "/topics/".$verifier_id['id'],
                                  
                                    "notification" => array( "title" => "Some object present over Sensor", "body" => "Some object present over Sensor id is ".$slot_info_row['display_id'], "content_available" => true,"priority"=> "high","icon" => "icon.png"));                                                                    
                                    $data_string = json_encode($data); 
                            
                                    // echo "The Json Data : ".$data_string; 
                            
                                    $headers = array
                                    (
                                         'Authorization: key=' . API_ACCESS_KEY, 
                                         'Content-Type: application/json'
                                    );                                                                                 
                                    $ch = curl_init();  
                                    
                                    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );                                                                  
                                    curl_setopt( $ch,CURLOPT_POST, true );  
                                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                                    curl_setopt( $ch,CURLOPT_POSTFIELDS, $data_string);                                                                  
                                                                                                                                                         
                                    $result = curl_exec($ch);
                                    curl_close ($ch);
                        }
                        $this->model->updateData('tbl_sensor',array('notification_status'=>1),array('id'=>$sensor_status['id']));
                    }else if($slots_status['fk_verify_booking_status']==2){
                        $slot_info[$slot_info_key]['fk_verify_booking_status'] = $slots_status['fk_verify_booking_status']; 
                        $slot_info[$slot_info_key]['color_hexcode'] = "#FFA500";
                        $reserved_slots[] = $slot_info[$slot_info_key];
                    }else if(empty($slots_status['fk_verify_booking_status']) && in_array($slot_info[$slot_info_key]['id'],$working_slots_data_1) || $sensor_status['status']==0){
                        $slot_info[$slot_info_key]['color_hexcode'] = "#00FF00";
                        $available_slots[] = $slot_info[$slot_info_key];
                    } else {
                        $slot_info[$slot_info_key]['color_hexcode'] = "#808080";
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
            }        
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function slot_issue_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $verifier_id = $this->input->post('verifier_id');
            $place_id = $this->input->post('place_id');
            $slot_id = $this->input->post('slot_id');
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
                            'issue_type' =>2,
                        );
                        $this->model->insertData('tbl_verifier_complaint',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message']= "Slot Complaint Raised Successfully";
                    }
            }
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function duty_allocated_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $id = $this->input->post('id');             
            if(empty($id)){
                $response['message'] = "Id is required";
                $response['code'] = 201;
            }else{
                $current_date = date('d/m/Y');
                $duty_allocated_data = $this->model->selectWhereData('tbl_duty_allocation',array('date'=>$current_date,'fk_verifier_id'=>$id),array('fk_place_id'));
                $place_details = $this->model->selectWhereData('tbl_parking_place',array('id'=>$duty_allocation_data['fk_place_id']),array('id','place_name','address'));
            
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['duty_allocated_place_details'] = $place_details;
            }
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function get_all_place_list_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {           
            $this->load->model('verifier_model');
            $place_data = $this->verifier_model->place_data();
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            $response['place_data'] = $place_data;
        }else{
             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
             $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    // public function nfc_device_mapped_with_user_post()
    // {
    //         $response = array('code' => - 1, 'status' => false, 'message' => '');
    //         $validate = validateToken();
    //         if ($validate) {
    //             $place_id = $this->input->post('place_id');
    //             $nfc_device_id = $this->input->post('nfc_device_id');
    //             $phone_no = $this->input->post('phone_no');
    //             $no_of_days = $this->input->post('no_of_days');
    //             $discount_price = $this->input->post('discount_price');
    //             if(empty($place_id)){
    //                 $response['message'] = "Place Id is required";
    //                 $response['code'] = 201;
    //             }else if(empty($nfc_device_id)){
    //                 $response['message'] = "Nfc Device Id is required";
    //                 $response['code'] = 201;
    //             }else if(empty($phone_no)){
    //                 $response['message'] = "Phone No is required";
    //                 $response['code'] = 201;
    //             }else if(empty($no_of_days)){
    //                 $response['message'] = "No of Dayss is required";
    //                 $response['code'] = 201;
    //             }else{
    //                     $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
    //                     $monthly_pass_details = $this->model->selectWhereData('tbl_pass_price_slab',array('no_of_days'=>$nfc_device['id'],'fk_place_id'=>$place_id),array('cost'));
    //                     $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>1),array('*'));
    //                     $no_of_days_1 = $this->model->selectWhereData('tbl_pass_days',array('id'=>$no_of_days),array('no_of_days'));
    //                     $from_date = date('Y-m-d');
    //                     $to_date = Date('Y-m-d', strtotime($from_date.'+'.$no_of_days_1['no_of_days']));
    //                     $check_user_count = $this->model->CountWhereRecord('tbl_user_pass_details', array('fk_nfc_device_id'=>$nfc_device['id'],'phone_no'=>$phone_no,'used_status'=>1));
    //                     if($check_user_count > 0){
    //                         $response['code'] = 201;
    //                         $response['status'] = false;
    //                         $response['message'] = 'User Already Exist......!';                             
    //                     }else{
    //                     $curl_data = array(
    //                         'fk_place_id'=>$place_id,
    //                         'fk_nfc_device_id'=>$nfc_device['id'],
    //                         'fk_no_of_days'=>$no_of_days,
    //                         'phone_no'=>$phone_no,
    //                         'from_date'=> date('Y-m-d'),
    //                         'to_date'=>$to_date,
    //                         'used_status'=>1,
    //                         'price'=>$monthly_pass_details['cost'],
    //                         'discount_price'=>$discount_price,
    //                         'total_price'=> $monthly_pass_details['cost'] - $discount_price
    //                     );      
    //                     $this->model->insertData('tbl_user_pass_details',$curl_data);
    //                     $response['code'] = REST_Controller::HTTP_OK;
    //                     $response['status'] = true;
    //                     $response['message'] = 'Pass Renewal Done Successfully '; 
    //                 }
    //             }
    //         }else{
    //             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
    //             $response['message'] = 'Unauthorised';
    //         }
    //         echo json_encode($response);
    // }
    //  public function nfc_device_mapped_with_user_post()
    // {
    //         $response = array('code' => - 1, 'status' => false, 'message' => '');
    //         $validate = validateToken();
    //         if ($validate) {
    //             $place_id = $this->input->post('place_id');
    //             $nfc_device_id = $this->input->post('nfc_device_id');
    //             $phone_no = $this->input->post('phone_no');
    //             $no_of_days = $this->input->post('no_of_days');
    //             $discount_price = $this->input->post('discount_price');
    //             if(empty($place_id)){
    //                 $response['message'] = "Place Id is required";
    //                 $response['code'] = 201;
    //             }else if(empty($nfc_device_id)){
    //                 $response['message'] = "Nfc Device Id is required";
    //                 $response['code'] = 201;
    //             }else if(empty($phone_no)){
    //                 $response['message'] = "Phone No is required";
    //                 $response['code'] = 201;
    //             }else if(empty($no_of_days)){
    //                 $response['message'] = "No of Dayss is required";
    //                 $response['code'] = 201;
    //             }else{
    //                 $check_nfc_device_count = $this->model->CountWhereRecord('tbl_nfc_device', array('nfc_device_id'=>$nfc_device_id,'status'=>1));
    //                 if($check_nfc_device_count > 0){
    //                     $response['code'] = 201;
    //                     $response['status'] = false;
    //                     $response['message'] = 'NFC Device Already exist.';  

    //                     $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
    //                     $monthly_pass_details = $this->model->selectWhereData('tbl_pass_price_slab',array('no_of_days'=>$no_of_days,'fk_place_id'=>$place_id),array('cost'));
    //                     $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>1),array('*'));
    //                     $no_of_days_1 = $this->model->selectWhereData('tbl_pass_days',array('id'=>$no_of_days),array('no_of_days'));
    //                     $from_date = date('Y-m-d');
    //                     $to_date = Date('Y-m-d', strtotime($from_date.'+'.$no_of_days_1['no_of_days']));
    //                     $check_user_count = $this->model->CountWhereRecord('tbl_user_pass_details', array('fk_nfc_device_id'=>$nfc_device['id'],'phone_no'=>$phone_no,'used_status'=>1));
    //                     if($check_user_count > 0){
    //                         // $response['code'] = 201;
    //                         // $response['status'] = false;
    //                         // $response['message'] = 'User Already Exist......!';
    //                         $current_date = date('Y-m-d');
    //                         if($current_date < $pass_previous_details['to_date']){
    //                             $response['code'] = 201;
    //                             $response['status'] = false;
    //                             $response['message'] = 'Your Pass will be expired on "'.$pass_previous_details['to_date'].'". Kindly Generate New Pass before the date';
    //                         }else if($current_date >=$pass_previous_details['to_date']){
    //                                 $update_data = array(
    //                                     'used_status'=>0,
    //                                 );
    //                                 $this->model->updateData('tbl_user_pass_details',$update_data,array('id'=>$pass_previous_details['id']));
    //                                 $curl_data = array(
    //                                     'fk_place_id'=>$place_id,
    //                                     'fk_nfc_device_id'=>$nfc_device['id'],
    //                                     'fk_no_of_days'=>$no_of_days,
    //                                     'phone_no'=>$phone_no,
    //                                     'from_date'=> date('Y-m-d'),
    //                                     'to_date'=>$to_date,
    //                                     'used_status'=>1,
    //                                     'price'=>$monthly_pass_details['cost'],
    //                                     'discount_price'=>$discount_price,
    //                                     'total_price'=> $monthly_pass_details['cost'] - $discount_price
    //                                 );      
    //                                 $this->model->insertData('tbl_user_pass_details',$curl_data);
    //                                 $response['code'] = REST_Controller::HTTP_OK;
    //                                 $response['status'] = true;
    //                                 $response['message'] = 'Pass Generated Successfully '; 
    //                         }else{
    //                             $response['code'] = 201;
    //                             $response['status'] = false;
    //                             $response['message'] = 'Your Pass has expired on "'.$pass_previous_details['to_date'].'". Kindly Generate New Pass'; 
    //                         }
    //                     }       
    //                 }else{
    //                     $curl_data = array(
    //                         'nfc_device_id' =>$nfc_device_id,
    //                     );
    //                     $last_inserted_id = $this->model->insertData('tbl_nfc_device',$curl_data);
    //                     // $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
    //                     $monthly_pass_details = $this->model->selectWhereData('tbl_pass_price_slab',array('no_of_days'=>$no_of_days,'fk_place_id'=>$place_id),array('cost'));
    //                     $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$last_inserted_id,'used_status'=>1),array('*'));
    //                     $no_of_days_1 = $this->model->selectWhereData('tbl_pass_days',array('id'=>$no_of_days),array('no_of_days'));
    //                     $from_date = date('Y-m-d');
    //                     $to_date = Date('Y-m-d', strtotime($from_date.'+'.$no_of_days_1['no_of_days']));
    //                     $check_user_count = $this->model->CountWhereRecord('tbl_user_pass_details', array('fk_nfc_device_id'=>$last_inserted_id,'phone_no'=>$phone_no,'used_status'=>1));
    //                     if($check_user_count > 0){
    //                         $response['code'] = 201;
    //                         $response['status'] = false;
    //                         $response['message'] = 'User Already Exist......!';
    //                     }else{
    //                         $curl_data = array(
    //                             'fk_place_id'=>$place_id,
    //                             'fk_nfc_device_id'=>$last_inserted_id,
    //                             'fk_no_of_days'=>$no_of_days,
    //                             'phone_no'=>$phone_no,
    //                             'from_date'=> date('Y-m-d'),
    //                             'to_date'=>$to_date,
    //                             'used_status'=>1,
    //                             'price'=>$monthly_pass_details['cost'],
    //                             'discount_price'=>$discount_price,
    //                             'total_price'=> $monthly_pass_details['cost'] - $discount_price
    //                         );      
    //                         $this->model->insertData('tbl_user_pass_details',$curl_data);
    //                         $response['code'] = REST_Controller::HTTP_OK;
    //                         $response['status'] = true;
    //                         $response['message'] = 'Pass Generated Successfully '; 
    //                     }
    //                 }    
    //             }
    //         }else{
    //             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
    //             $response['message'] = 'Unauthorised';
    //         }
    //         echo json_encode($response);
    // }
    // public function renewal_user_pass_post()
    // {
    //         $response = array('code' => - 1, 'status' => false, 'message' => '');
    //         $validate = validateToken();
    //         if ($validate) {
    //             $nfc_device_id = $this->input->post('nfc_device_id');
    //             $no_of_days = $this->input->post('no_of_days');
    //             if(empty($nfc_device_id)){
    //                 $response['message'] = "Nfc Device Id is required";
    //                 $response['code'] = 201;
    //             }else if(empty($no_of_days)){
    //                 $response['message'] = "No of Dayss is required";
    //                 $response['code'] = 201;
    //             }else{

    //                 $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
    //                 $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>1),array('*'));
    //                 $no_of_days_1 = $this->model->selectWhereData('tbl_pass_days',array('id'=>$no_of_days),array('no_of_days'));
    //                 $from_date = date('Y-m-d', strtotime("+1 day", strtotime($pass_previous_details['to_date'])));
    //                 $to_date = Date('Y-m-d', strtotime($from_date.'+'.$no_of_days_1['no_of_days']));
    //                 $current_date= date('Y-m-d');

    //                 if($current_date > $pass_previous_details['to_date']){
    //                     $update_data = array(
    //                         'used_status'=>0,
    //                     );
    //                     $this->model->updateData('tbl_user_pass_details',$update_data,array('id'=>$pass_previous_details['id']));

    //                     $response['code'] = 201;
    //                     $response['status'] = false;
    //                     $response['message'] = 'Your Pass has expired on "'.$pass_previous_details['to_date'].'". Kindly Generate New Pass'; 
    //                 }else{
    //                   $update_data = array(
    //                     'used_status'=>0,);
    //                     $this->model->updateData('tbl_user_pass_details',$update_data,array('id'=>$pass_previous_details['id']));
    //                     $curl_data = array(
    //                         'fk_place_id'=>$pass_previous_details['fk_place_id'],
    //                         'fk_nfc_device_id'=>$pass_previous_details['fk_nfc_device_id'],
    //                         'fk_no_of_days'=>$no_of_days,
    //                         'phone_no'=>$pass_previous_details['phone_no'],
    //                         'from_date'=> date('Y-m-d'),
    //                         'to_date'=>$to_date,
    //                         'used_status'=>1,
    //                     );      
    //                     $this->model->insertData('tbl_user_pass_details',$curl_data);  
    //                 }

                   
    //                 $response['code'] = REST_Controller::HTTP_OK;
    //                 $response['status'] = true;
    //                 $response['message'] = 'Pass Renewal Done Successfully';
    //             }
    //         }else{
    //             $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
    //             $response['message'] = 'Unauthorised';
    //         }
    //         echo json_encode($response);
    // }

    public function nfc_device_mapped_with_user_post()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                $place_id = $this->input->post('place_id');
                $nfc_device_id = $this->input->post('nfc_device_id');
                $phone_no = $this->input->post('phone_no');
                $no_of_days = $this->input->post('no_of_days');
                $discount_price = $this->input->post('discount_price');
                $fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
                $car_no = $this->input->post('car_no');
                $car_no = json_decode($car_no);
                $from_date = $this->input->post('from_date');
                if(empty($place_id)){
                    $response['message'] = "Place Id is required";
                    $response['code'] = 201;
                }else if(empty($nfc_device_id)){
                    $response['message'] = "Nfc Device Id is required";
                    $response['code'] = 201;
                }else if(empty($phone_no)){
                    $response['message'] = "Phone No is required";
                    $response['code'] = 201;
                }else if(empty($no_of_days)){
                    $response['message'] = "No of Days is required";
                    $response['code'] = 201;
                }else if(empty($from_date)){
                    $response['message'] = "From Date is required";
                    $response['code'] = 201;
                }else{
                    $check_nfc_device_count = $this->model->CountWhereRecord('tbl_nfc_device', array('nfc_device_id'=>$nfc_device_id,'status'=>1));
                    
                    if($check_nfc_device_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'NFC Device Already exist.';  
                        $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
                        $monthly_pass_details = $this->model->selectWhereData('tbl_pass_price_slab',array('no_of_days'=>$no_of_days,'fk_place_id'=>$place_id,'fk_vehicle_type_id'=>$fk_vehicle_type_id),array('cost'));
                        $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>1),array('*'));
                        $no_of_days_1 = $this->model->selectWhereData('tbl_pass_days',array('id'=>$no_of_days),array('no_of_days'));
                        // $from_date = date('Y-m-d');
                        $to_date = Date('Y-m-d', strtotime($from_date.'+'.$no_of_days_1['no_of_days']));
                        $check_user_count = $this->model->CountWhereRecord('tbl_user_pass_details', array('fk_nfc_device_id'=>$nfc_device['id'],'phone_no'=>$phone_no,'used_status'=>1));
                        if($check_user_count > 0){
                            // $response['code'] = 201;
                            // $response['status'] = false;
                            // $response['message'] = 'User Already Exist......!';
                            $current_date = date('Y-m-d');
                            if($current_date < $pass_previous_details['to_date']){
                                $response['code'] = 201;
                                $response['status'] = false;
                                $response['message'] = 'Your Pass will be expired on "'.$pass_previous_details['to_date'].'". Kindly Generate New Pass before the date';
                            }else if($current_date >=$pass_previous_details['to_date']){
                                    $update_data = array(
                                        'used_status'=>0,
                                    );
                                    $this->model->updateData('tbl_user_pass_details',$update_data,array('id'=>$pass_previous_details['id']));
                                    $curl_data = array(
                                        'fk_place_id'=>$place_id,
                                        'fk_nfc_device_id'=>$nfc_device['id'],
                                        'fk_no_of_days'=>$no_of_days,
                                        'phone_no'=>$phone_no,
                                        'from_date'=> $from_date,
                                        'to_date'=>$to_date,
                                        'used_status'=>1,
                                        'price'=>$monthly_pass_details['cost'],
                                        'discount_price'=>$discount_price,
                                        'total_price'=> $monthly_pass_details['cost'] - $discount_price,
                                        'status'=>'Renewal',
                                        'car_no'=>$car_no,
                                        'fk_vehicle_type_id'=>$fk_vehicle_type_id
                                    );      
                                    $this->model->insertData('tbl_user_pass_details',$curl_data);
                                    $response['code'] = REST_Controller::HTTP_OK;
                                    $response['status'] = true;
                                    $response['message'] = 'Pass Generated Successfully '; 
                            }else{
                                $response['code'] = 201;
                                $response['status'] = false;
                                $response['message'] = 'Your Pass has expired on "'.$pass_previous_details['to_date'].'". Kindly Generate New Pass'; 
                            }
                        }       
                    }else{
                        $curl_data = array(
                            'nfc_device_id' =>$nfc_device_id,
                        );
                        $last_inserted_id = $this->model->insertData('tbl_nfc_device',$curl_data);
                        // $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
                        $monthly_pass_details = $this->model->selectWhereData('tbl_pass_price_slab',array('no_of_days'=>$no_of_days,'fk_place_id'=>$place_id,'fk_vehicle_type_id'=>$fk_vehicle_type_id),array('cost'));
                        $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$last_inserted_id,'used_status'=>1),array('*'));
                        $no_of_days_1 = $this->model->selectWhereData('tbl_pass_days',array('id'=>$no_of_days),array('no_of_days'));
                        $from_date = date('Y-m-d');
                        $to_date = Date('Y-m-d', strtotime($from_date.'+'.$no_of_days_1['no_of_days']));
                        $check_user_count = $this->model->CountWhereRecord('tbl_user_pass_details', array('fk_nfc_device_id'=>$last_inserted_id,'phone_no'=>$phone_no,'used_status'=>1));
                        if($check_user_count > 0){
                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['message'] = 'User Already Exist......!';
                        }else{
                            $curl_data = array(
                                'fk_place_id'=>$place_id,
                                'fk_nfc_device_id'=>$last_inserted_id,
                                'fk_no_of_days'=>$no_of_days,
                                'phone_no'=>$phone_no,
                                'from_date'=> $from_date,
                                'to_date'=>$to_date,
                                'used_status'=>1,
                                'price'=>$monthly_pass_details['cost'],
                                'discount_price'=>$discount_price,
                                'total_price'=> $monthly_pass_details['cost'] - $discount_price,
                                 'status'=>'New',
                                 'car_no'=>implode(",",$car_no),
                                 'fk_vehicle_type_id'=>$fk_vehicle_type_id
                            );      
                            $this->model->insertData('tbl_user_pass_details',$curl_data);
                            $response['code'] = REST_Controller::HTTP_OK;
                            $response['status'] = true;
                            $response['message'] = 'Pass Generated Successfully '; 
                        }
                    }    
                }
            }else{
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
    }
    public function renewal_user_pass_post()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                $nfc_device_id = $this->input->post('nfc_device_id');
                $no_of_days = $this->input->post('no_of_days');
                $fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
                $discount_price = $this->input->post('discount_price');
                if(empty($nfc_device_id)){
                    $response['message'] = "Nfc Device Id is required";
                    $response['code'] = 201;
                }else if(empty($no_of_days)){
                    $response['message'] = "No of Dayss is required";
                    $response['code'] = 201;
                }else{
                    $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
                    $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>1),array('*'));
                    $monthly_pass_details = $this->model->selectWhereData('tbl_pass_price_slab',array('no_of_days'=>$no_of_days,'fk_place_id'=>$pass_previous_details['fk_place_id'],'fk_vehicle_type_id'=>$fk_vehicle_type_id),array('cost'));
                    $no_of_days_1 = $this->model->selectWhereData('tbl_pass_days',array('id'=>$no_of_days),array('no_of_days'));
                    $from_date = date('Y-m-d', strtotime("+1 day", strtotime($pass_previous_details['to_date'])));
                    $to_date = Date('Y-m-d', strtotime($from_date.'+'.$no_of_days_1['no_of_days']));
                    $current_date= date('Y-m-d');

                    if($current_date > $pass_previous_details['to_date']){
                        $update_data = array(
                            'used_status'=>0,
                        );
                        $this->model->updateData('tbl_user_pass_details',$update_data,array('id'=>$pass_previous_details['id']));

                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Your Pass has expired on "'.$pass_previous_details['to_date'].'". Kindly Generate New Pass'; 
                    }else{
                       $update_data = array(
                        'used_status'=>0,);
                        $this->model->updateData('tbl_user_pass_details',$update_data,array('id'=>$pass_previous_details['id']));
                        $curl_data = array(
                            'fk_place_id'=>$pass_previous_details['fk_place_id'],
                            'fk_nfc_device_id'=>$pass_previous_details['fk_nfc_device_id'],
                            'fk_no_of_days'=>$no_of_days,
                            'phone_no'=>$pass_previous_details['phone_no'],
                            'from_date'=> date('Y-m-d'),
                            'to_date'=>$to_date,
                            'used_status'=>1,
                            'price'=>$monthly_pass_details['cost'],
                            'discount_price'=>$discount_price,
                            'total_price'=> $monthly_pass_details['cost'] - $discount_price,
                             'status'=>'Renewal'
                        );      
                        $this->model->insertData('tbl_user_pass_details',$curl_data);  
                    }
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success'; 
                }
            }else{
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
    }
    
    
   
}
