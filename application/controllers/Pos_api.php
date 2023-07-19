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
                $pan_card= $this->input->post('pan_card');
                 $aadhaar_card= $this->input->post('aadhaar_card');
                 $business_registration_number = $this->input->post('business_registration_number');
                 $business_registration_name= $this->input->post('business_registration_name');
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
                        $verify_device_id = $this->model->CountWhereRecord('tbl_pos_device', array('pos_device_id'=>$device_id));
                        // if($verify_device_id > 0){
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
                                }
                                // else if (empty($image)) {
                                //     $is_signature_file = false;
                                //     $response['status'] = 'failure';
                                //     $response['error'] = array('image' => "Image required",);
                                // }
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
                                }
                                
                                // else if (empty($image1)) {
                                //     $is_signature_file = false;
                                //     $response['status'] = 'failure';
                                //     $response['error'] = array('image' => "Image required",);
                                // }
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
                                            'pos_device_id' => $pos_device_id['id'],
                                            'isActive'=>0,
                                            'business_registration_name'=>$business_registration_name,
                                            'business_registration_number' => $business_registration_number,
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
                        // }else{
                        //     $response['code'] = 201;
                        //     $response['status'] = false;
                        //     if($lang_id==1){
                        //         $response['message'] = 'Device Id does not exist';
                        //     }else{
                        //         $response['message'] = 'डिवाइस आईडी मेल नहीं खाती';
                        //     }
                        // }
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
                    $this->load->model('pos_model');
                    $get_details_on_pos_device_id = $this->pos_model->get_details_on_pos_device_id($device_id);
                    $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));
                    // $place_id = $this->model->selectWhereData('tbl_pos_device_map',array('device_id'=>$pos_device_id['id']),array('fk_place_id'));  
                    // // $place_id = $this->model->selectWhereData('tbl_pos_duty_allocation',array('fk_device_id'=>$pos_device_id['id'],'date'=>date('d/m/Y')),array('fk_place_id'));    
                    // $place_details = $this->model->selectWhereData('tbl_parking_place',array('id'=>$place_id['fk_place_id']),array('place_name','address'));        
                    $verify_device_id = $this->model->CountWhereRecord('tbl_pos_verifier_logged_in', array('fk_pos_verifier_id'=>$login_info['id'],'fk_device_id !='=>$get_details_on_pos_device_id['pos_device_id'],'status'=>1));       
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
                                $login_info['place_id']= @$get_details_on_pos_device_id['fk_place_id'];
                                $login_info['place_name']= @$get_details_on_pos_device_id['place_name'];
                                $login_info['place_address']= @$get_details_on_pos_device_id['address'];
                                $login_info['company_name']= @$get_details_on_pos_device_id['company_name'];
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
            // unset($vehicle_type[0]);
            // unset($vehicle_type[1]);
            // unset($vehicle_type[3]);
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
            $fk_place_id = $this->input->post('fk_place_id');
            if(empty($fk_place_id)){
                $response['message'] = "Place id is required";
                $response['code'] = 201;
            }else{
                $two_wheller_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>1,'del_status'=>1),array('*'),false);
                $three_wheller_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>2,'del_status'=>1),array('*'),false);
                $four_wheller_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>3,'del_status'=>1),array('*'),false);
                $truck_van_wheller_price_slab = $this->model->selectWhereData('tbl_hours_price_slab',array('fk_place_id'=>$fk_place_id,'fk_vehicle_type_id'=>4,'del_status'=>1),array('*'),false);
                $this->load->model('pos_model');
                $two_wheller_monthly_price_slab = $this->pos_model->two_wheller_monthly_price_slab($fk_place_id);
                $three_wheller_monthly_price_slab = $this->pos_model->three_wheller_monthly_price_slab($fk_place_id);;
                $four_wheller_monthly_price_slab = $this->pos_model->four_wheller_monthly_price_slab($fk_place_id);;
                $heavy_wheller_monthly_price_slab = $this->pos_model->heavy_wheller_monthly_price_slab($fk_place_id);;
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
                if(!empty($two_wheller_monthly_price_slab)){
                       $response['two_wheller_monthly_price_slab'] = $two_wheller_monthly_price_slab;
                }
                if(!empty($three_wheller_monthly_price_slab)){
                       $response['three_wheller_monthly_price_slab'] = $three_wheller_monthly_price_slab;
                }
                if(!empty($four_wheller_monthly_price_slab)){
                       $response['four_wheller_monthly_price_slab'] = $four_wheller_monthly_price_slab;
                }
                if(!empty($heavy_wheller_monthly_price_slab)){
                       $response['heavy_wheller_monthly_price_slab'] = $heavy_wheller_monthly_price_slab;
                }
            }           
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // Check in function for inside check-in button
    //   public function check_in_post()
    // {
    //     $response = array('code' => - 1, 'status' => false, 'message' => '');
    //     $validate = validateToken();
    //     if ($validate) {
    //         $fk_place_id = $this->input->post('fk_place_id');
    //         $fk_verifier_id = $this->input->post('fk_verifier_id');
    //         $fk_vehicle_type_id = $this->input->post('fk_vehicle_type_id');
    //         $fk_lang_id = $this->input->post('fk_lang_id');
    //         $car_no = $this->input->post('car_no');
    //         $phone_no = $this->input->post('phone_no');
    //         $from_date = $this->input->post('from_date');
    //         $to_date = $this->input->post('to_date');
    //         $from_time = $this->input->post('from_time');
    //         $to_time = $this->input->post('to_time');
    //         $total_hours = $this->input->post('total_hours');
    //         $price = $this->input->post('price');
    //         $latitude = $this->input->post('latitude');
    //         $longitude = $this->input->post('longitude');
    //         $book_status = $this->input->post('book_status');
    //         $device_id = $this->input->post('device_id');
    //         $nfc_device_id = $this->input->post('nfc_device_id');
    //         $reason = $this->input->post('reason');
    //         $fk_booking_id = $this->input->post('fk_booking_id');
    //         $fk_user_id = $this->input->post('fk_user_id');
    //         $primary_key = $this->input->post('primary_key');
    //         $receipt_no = $this->input->post('receipt_no');
    //         if(empty($fk_lang_id)){
    //             $response['message'] ="Language Id is required";
    //             $response['code'] =201;
    //         }else if(empty($fk_place_id)){
    //             $response['message'] ="Place Id is required";
    //             $response['code'] =201;
    //         }else if(empty($fk_verifier_id)){
    //             $response['message'] ="Verifier Id is required";
    //             $response['code'] =201;
    //         }else if(empty($fk_vehicle_type_id)){
    //             $response['message'] ="Vehicle Type Id is required";
    //             $response['code'] =201;
    //         }else if(empty($car_no)){
    //             if($fk_lang_id ==1){
    //                     $response['message'] ="Car No is required";
    //             }else{
    //                 $response['message'] ="कार नंबर आवश्यक है";
    //             }
    //             $response['code'] =201;
    //         }else if(empty($from_date)){
    //             if($fk_lang_id ==1){
    //                 $response['message'] ="From Date is required";
    //             }else{
    //                 $response['message'] ="दिनांक से आवश्यक है";
    //             }
    //             $response['code'] =201;
    //         }else if(empty($from_time)){
    //             if($fk_lang_id ==1){
    //                 $response['message'] ="From Time is required";
    //             }else{
    //                 $response['message'] ="समय से आवश्यक है";
    //             }
    //             $response['code'] =201;
    //         }else if(empty($latitude)){
    //             $response['message'] ="Latitude is required";
    //             $response['code'] =201;
    //         }else if(empty($longitude)){
    //             $response['message'] ="Longitude is required";
    //             $response['code'] =201;
    //         }else if(empty($book_status)){
    //             $response['message'] ="Check-in Status is required";
    //             $response['code'] =201;
    //         }else if(empty($device_id)){
    //             $response['message'] ="Device is required";
    //             $response['code'] =201;
    //         }else{
    //             $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));
    //             // print_r($_POST);die;
    //             if(!empty($nfc_device_id)){
    //                     $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
    //                     $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>1),array('*'));
    //                     $current_date= date('Y-m-d');

    //                     if($pass_previous_details['to_date'] > $current_date){
    //                         $curl_data=array(
    //                             'fk_booking_id'=>$fk_booking_id,
    //                             'fk_user_id'=>$fk_user_id,
    //                             'fk_place_id'=>$fk_place_id,
    //                             'fk_verifier_id' =>$fk_verifier_id,
    //                             'fk_vehicle_type_id'=>$fk_vehicle_type_id,
    //                             'fk_device_id'=>$pos_device_id['id'],
    //                             'fk_lang_id'=>$fk_lang_id,
    //                             'car_no'=>$car_no,
    //                             'phone_no'=>$phone_no,
    //                             'from_date'=>$from_date,
    //                             'to_date'=>$to_date,
    //                             'from_time'=>$from_time,
    //                             'to_time'=>$to_time,
    //                             'total_hours'=>$total_hours,
    //                             'price'=>$price,
    //                             'latitude'=>$latitude,
    //                             'longitude'=>$longitude,
    //                             'book_status'=>$book_status,
    //                             'reason'=>$reason,
    //                             'primary_key'=>$primary_key,
    //                             'receipt_no'=>$receipt_no,
    //                         );
    //                         $this->model->insertData('tbl_pos_booking',$curl_data);
    //                         $response['code'] = REST_Controller::HTTP_OK;
    //                         $response['status'] = true;
    //                         if($fk_lang_id==1){
    //                             $response['message'] = 'Checked-in Successfully';
    //                         }else{
    //                             $response['message'] = 'चेक-इन सफलतापूर्वक';
    //                         }
    //                     }else{
    //                          $update_data = array(
    //                             'used_status'=>0,
    //                         );
    //                         $this->model->updateData('tbl_user_pass_details',$update_data,array('id'=>$pass_previous_details['id']));

    //                         $response['code'] = 201;
    //                         $response['status'] = false;
    //                         $response['message'] = 'Your Pass has expired on "'.$pass_previous_details['to_date'].'". Kindly Generate New Pass'; 
    //                     }
    //             }else{
    //                     $curl_data=array(
    //                         'fk_booking_id'=>$fk_booking_id,
    //                         'fk_user_id'=>$fk_user_id,
    //                         'fk_place_id'=>$fk_place_id,
    //                         'fk_verifier_id' =>$fk_verifier_id,
    //                         'fk_vehicle_type_id'=>$fk_vehicle_type_id,
    //                         'fk_device_id'=>$pos_device_id['id'],
    //                         'fk_lang_id'=>$fk_lang_id,
    //                         'car_no'=>$car_no,
    //                         'phone_no'=>$phone_no,
    //                         'from_date'=>$from_date,
    //                         'to_date'=>$to_date,
    //                         'from_time'=>$from_time,
    //                         'to_time'=>$to_time,
    //                         'total_hours'=>$total_hours,
    //                         'price'=>$price,
    //                         'latitude'=>$latitude,
    //                         'longitude'=>$longitude,
    //                         'book_status'=>$book_status,
    //                         'reason'=>$reason,
    //                         'primary_key'=>$primary_key,
    //                         'receipt_no'=>$receipt_no,
    //                     );
    //                     $this->model->insertData('tbl_pos_booking',$curl_data);
    //                     $response['code'] = REST_Controller::HTTP_OK;
    //                     $response['status'] = true;
    //                     if($fk_lang_id==1){
    //                         $response['message'] = 'Checked-in Successfully';
    //                     }else{
    //                         $response['message'] = 'चेक-इन सफलतापूर्वक';
    //                     }
    //             }                
    //         }

    //     }else {
    //         $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
    //         $response['message'] = 'Unauthorised';
    //     }
    //     echo json_encode($response);
    // } 
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
            $book_status = $this->input->post('book_status');
            $device_id = $this->input->post('device_id');
            $nfc_device_id = $this->input->post('nfc_device_id');
            $reason = $this->input->post('reason');
            $fk_booking_id = $this->input->post('fk_booking_id');
            $fk_user_id = $this->input->post('fk_user_id');
            $primary_key = $this->input->post('primary_key');
            $receipt_no = $this->input->post('receipt_no');

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
            }else if(empty($device_id)){
                $response['message'] ="Device is required";
                $response['code'] =201;
            }else{
                $this->load->model('superadmin_model');
                $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));
                if(!empty($nfc_device_id)){
                        $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
                        $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>1),array('*'));
                        $current_date= date('Y-m-d');

                        if($pass_previous_details['to_date'] > $current_date){
                            $curl_data=array(
                                'fk_booking_id'=>$fk_booking_id,
                                'fk_user_id'=>$fk_user_id,
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
                                'reason'=>$reason,
                                'primary_key'=>$primary_key,
                                'receipt_no'=>$receipt_no,
                            );
                            $this->model->insertData('tbl_pos_booking',$curl_data);
                            $response['code'] = REST_Controller::HTTP_OK;
                            $response['status'] = true;
                            if($fk_lang_id==1){
                                $response['message'] = 'Checked-in Successfully';
                            }else{
                                $response['message'] = 'चेक-इन सफलतापूर्वक';
                            }
                        }else{
                             $update_data = array(
                                'used_status'=>0,
                            );
                            $this->model->updateData('tbl_user_pass_details',$update_data,array('id'=>$pass_previous_details['id']));

                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['message'] = 'Your Pass has expired on "'.$pass_previous_details['to_date'].'". Kindly Generate New Pass'; 
                        }
                }else{
                        $curl_data=array(
                            'fk_booking_id'=>$fk_booking_id,
                            'fk_user_id'=>$fk_user_id,
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
                            'reason'=>$reason,
                            'primary_key'=>$primary_key,
                            'receipt_no'=>$receipt_no
                        );
                        // $this->model->insertData('tbl_pos_booking',$curl_data);
                        $last_inserted_id = $this->model->insertData('tbl_pos_booking',$curl_data);
                      
                        $car_id = $this->superadmin_model->get_details_on_car_no($car_no);
                      
                        $current_time = date('H:i:s');
                     
                        $booking_from_time= date('H:i:s',strtotime($current_time .'-15 minutes'));         
                        $booking_to_time= date('H:i:s',strtotime($current_time . ' +15 minutes'));
                        $booking_from_time_1 = str_replace(" ","",$booking_from_time);
                        $booking_to_time_1 = str_replace(" ","",$booking_to_time);
                        foreach ($car_id as $car_id_key => $car_id_row) {
                            $booking_data = $this->model->selectWhereData('tbl_booking',array('fk_car_id'=>$car_id_row['id'],'booking_from_date'=>$from_date,'booking_from_time >='=>$booking_from_time,'booking_from_time <='=>$booking_to_time),array('id','fk_user_id'),true,array('booking_from_time','ASC'));
                            if(!empty($booking_data)){
                                $update_data = array(
                                    'fk_booking_id'=>$booking_data['id'],
                                    'fk_user_id'=>$booking_data['fk_user_id'],
                                );
                                $this->model->updateData('tbl_pos_booking',$update_data,array('id'=>$last_inserted_id));

                                $update_booking_data = array(
                                    'fk_pos_booking_check_in_id'=>$last_inserted_id,
                                );
                                $this->model->updateData('tbl_booking',$update_booking_data,array('id'=>$booking_data['id']));
                            }
                        }                         
                        
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        if($fk_lang_id==1){
                            $response['message'] = 'Checked-in Successfully';
                        }else{
                            $response['message'] = 'चेक-इन सफलतापूर्वक';
                        }
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
            $fk_booking_id = $this->input->post('fk_booking_id');
            $fk_user_id = $this->input->post('fk_user_id');
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
            $book_status = $this->input->post('book_status');
            $device_id = $this->input->post('device_id');
            $payment_type = $this->input->post('payment_type');
            $nfc_device_id = $this->input->post('nfc_device_id');
            $reason = $this->input->post('reason');
            $primary_key = $this->input->post('primary_key');
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
            }else if(empty($device_id)){
                $response['message'] ="Device is required";
                $response['code'] =201;
            }else if(empty($payment_type)){
                if($fk_lang_id ==1){
                    $response['message'] ="Payment Type is required";
                }else{
                    $response['message'] ="भुगतान प्रकार आवश्यक है";
                }
                $response['code'] =201;
            }else{
               $pos_device_id = $this->model->selectWhereData('tbl_pos_device',array('pos_device_id'=>$device_id),array('id'));
                $data = $this->model->selectWhereData('tbl_pos_booking',array('primary_key'=>$primary_key),array('fk_booking_id','fk_user_id'));
                
               if(!empty($nfc_device_id)){
                        $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
                        $pass_previous_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>0),array('*'));
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
                            $curl_data=array(
                                'fk_booking_id'=>$data['fk_booking_id'],
                                'fk_user_id'=>$data['fk_user_id'],
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
                                'payment_type'=>$payment_type,
                                'reason'=>$reason,
                                'primary_key'=>$primary_key,
                            );
                            $this->model->insertData('tbl_pos_booking',$curl_data);
                            $response['code'] = REST_Controller::HTTP_OK;
                            $response['status'] = true;
                            if($fk_lang_id==1){
                                $response['message'] = 'Checked-out Successfully';
                            }else{
                                $response['message'] = 'चेक-आउट सफलतापूर्वक';
                            }
                        }
                    }else{
                        $curl_data=array(
                               'fk_booking_id'=>$data['fk_booking_id'],
                                'fk_user_id'=>$data['fk_user_id'],
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
                                'payment_type'=>$payment_type,
                                'reason'=>$reason,
                                'primary_key'=>$primary_key,
                            );
                            $last_inserted_id= $this->model->insertData('tbl_pos_booking',$curl_data);
                            
                            if(!empty($data['fk_booking_id'])){
                                $this->model->updateData('tbl_booking_status',array('used_status'=>0),array('fk_booking_id'=>$data['fk_booking_id']));
                                $booking_status = array(
                                    'fk_booking_id'=>$data['fk_booking_id'],
                                    'fk_status_id'=>2,
                                    'used_status'=>1
                                );
                                $this->model->insertData('tbl_booking_status',$booking_status);
                                
                                $update_booking_data = array(
                                'fk_pos_booking_check_out_id'=>$last_inserted_id,
                            );
                            $this->model->updateData('tbl_booking',$update_booking_data,array('id'=>$data['fk_booking_id']));
                            }
                            
                            $response['code'] = REST_Controller::HTTP_OK;
                            $response['status'] = true;
                            if($fk_lang_id==1){
                                $response['message'] = 'Checked-out Successfully';
                            }else{
                                $response['message'] = 'चेक-आउट सफलतापूर्वक';
                            }
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
                foreach ($booking_data as $booking_data_key => $booking_data_row) {
                        if($booking_data_row['payment_type']==1){
                            $booking_data[$booking_data_key]['payment_type_1'] = "Cash";
                        }else if($booking_data_row['payment_type']==2){
                            $booking_data[$booking_data_key]['payment_type_1'] = "Online";
                        }                    
                }
                $total_amount = $this->model->selectWhereData('tbl_pos_booking',array('book_status'=>2),array('SUM(price) as price'));

                $new_pass_data = $this->pos_model->new_pass_data($place_id,$from_date,$to_date);
                $renewal_pass_data = $this->pos_model->renewal_pass_data($place_id,$from_date,$to_date);

                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['booking_data'] = $booking_data;
                $response['total_amount'] = $total_amount;
                $response['new_pass_data'] = $new_pass_data;
                $response['renewal_pass_data'] = $renewal_pass_data;
            }
        }else{
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function reset_password_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {            
            $contact_no = $this->input->post('contact_no');
            $password = $this->input->post('password');
            $lang_id = $this->input->post('lang_id');
            if(empty($contact_no)){
                $response['message']= "Contact No is required";
                $response['code'] = 201;
            }else if(empty($password)){
                $response['message']= "Password is required";
                $response['code'] = 201;
            }else if(empty($lang_id)){
                $response['message']= "Language Id is required";
                $response['code'] = 201;
            }else{  
                $curl_data = array('password' =>dec_enc('encrypt',$password));
                $this->model->updateData('pa_users',$curl_data,array('phoneNo'=>$contact_no,'user_type'=>14));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                if($lang_id==1){
                    $response['message'] = 'Password Updated Successfully';
                }else{
                    $response['message'] = 'पासवर्ड सफलतापूर्वक अद्यतन';
                }
            }
        }else{
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function user_pass_details_on_nfc_card_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {            
            $nfc_device_id = $this->input->post('nfc_device');
            
            if(empty($nfc_device_id)){
                $response['message']= "NFC device is required";
                $response['code'] = 201;
            }else{                  
                    $nfc_device = $this->model->selectWhereData('tbl_nfc_device',array('nfc_device_id'=>$nfc_device_id),array('id'));
                    if(!empty($nfc_device['id'])){
                        $pass_details = $this->model->selectWhereData('tbl_user_pass_details',array('fk_nfc_device_id'=>$nfc_device['id'],'used_status'=>1),array('*'));
                        $car_no = explode(",",$pass_details['car_no']);
                        // foreach ($car_no as $car_no_key => $car_no_row) {
                        //     $car_details = $this->model->selectWhereData('tbl_user_car_details',array('car_number'=>$car_no_row),array('fk_vehicle_type_id','id'));
                        //     // $c
                        // }
                        $pass_details['car_no']=$car_no;
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['pass_details'] = $pass_details;
                    }else{
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = "No Data Found";
                        $response['pass_details'] = [];
                    }
            }
        }else{
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function pos_booking_verify_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {            
            $id = $this->input->post('id');
            $verifier_id = $this->input->post('verifier_id');
            $verify_status = $this->input->post('verify_status');
            // print_r($_POST);die;
            if(empty($id)){
                $response['message']= "Id is required";
                $response['code'] = 201;
            }else if(empty($verifier_id)){
                $response['message']= "Verifier Id is required";
                $response['code'] = 201;
            }else if(empty($verify_status)){
                $response['message']= "Verifier Status is required";
                $response['code'] = 201;
            }else{          
                    // if($verify_status==true){
                    //     $verify_status_1 = 1;
                    // }else{
                    //     $verify_status_1 = 2;
                    // }
                   $curl_data = array(
                        'fk_booking_id'=>$id,
                        'fk_verifier_id'=>$verifier_id,
                        'verify_status'=>$verify_status,
                   );

                   $this->model->insertData('tbl_booking_verify',$curl_data);

                   if($verify_status==1){
                         $this->model->updateData('tbl_booking_status',array('used_status'=>0),array('fk_booking_id'=>$id));
                         $booking_status = array(
                                'fk_booking_id'=>$id,
                                'fk_status_id'=>1,
                                'used_status'=>1
                        );
                        $this->model->insertData('tbl_booking_status',$booking_status);
                        $booking_details = $this->model->selectWhereData('tbl_booking',array('id'=>$id),array('booking_id','fk_user_id'));
                        
                        $this->model->updateData('tbl_booking',array('fk_verify_booking_status'=>1),array('id'=>$id));
                        
                        $this->load->model('pushnotification_model');
                        $this->pushnotification_model->booking_accepted($booking_details['fk_user_id'],$booking_details['booking_id']);
                   }else{
                        $this->model->updateData('tbl_booking_status',array('used_status'=>0),array('fk_booking_id'=>$id));
                        $booking_status = array(
                                'fk_booking_id'=>$id,
                                'fk_status_id'=>3,
                                'used_status'=>1
                        );
                        $this->model->insertData('tbl_booking_status',$booking_status);
                        $this->model->updateData('tbl_booking',array('fk_verify_booking_status'=>3),array('id'=>$id));
                        $booking_details = $this->model->selectWhereData('tbl_booking',array('id'=>$id),array('booking_id','fk_user_id'));
                        $this->load->model('pushnotification_model');
                        $this->pushnotification_model->booking_rejected($booking_details['fk_user_id'],$booking_details['booking_id']);
                   }          
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = "success";
            }
        }else{
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // public function pos_booking_list_post()
    // {
    //   $response = array('code' => - 1, 'status' => false, 'message' => '');
    //     $validate = validateToken();
    //     if ($validate) {
    //             $place_id = $this->input->post('place_id');
    //             if(empty($place_id)){
    //                 $response['message'] = "Place Id is required";
    //                 $response['code'] = 201;
    //             }else{
    //                     $this->load->model('user_model');
    //                     $ongoing_unverified_booking_list = $this->user_model->ongoing_unverified_pos_booking_list($place_id);
                        
    //                     $accepted_pos_booking_list = $this->user_model->accepted_pos_booking_list($place_id);
    //                     foreach($accepted_pos_booking_list as $accepted_pos_booking_list_key => $accepted_pos_booking_list_row){
    //                              $accepted_pos_booking_list[$accepted_pos_booking_list_key]['color_code']= "#008000";
    //                     }
    //                     $rejected_pos_booking = $this->user_model->rejected_pos_booking_list($place_id);
    //                   foreach ($rejected_pos_booking as $rejected_pos_booking_key => $rejected_pos_booking_row) {
    //                         $rejected_pos_booking[$rejected_pos_booking_key]['color_code'] = "#FF0000";
    //                     }
    //                     // $history_booking = $this->user_model->history_booking_list($place_id);
    //                     $response['code'] = REST_Controller::HTTP_OK;
    //                     $response['status'] = true;
    //                     $response['message'] = 'success';
    //                     $response['ongoing_unverified_booking_list'] = $ongoing_unverified_booking_list;
    //                     $response['accepted_pos_booking_list'] = $accepted_pos_booking_list;
    //                     $response['rejected_pos_booking'] = $rejected_pos_booking;
    //                     // $response['history_booking'] = $history_booking;           
    //             }
    //     }else {
    //         $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
    //         $response['message'] = 'Unauthorised';
    //     }
    //     echo json_encode($response);
    // }
    public function pos_booking_list_post()
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
                        $ongoing_unverified_booking_list = $this->user_model->ongoing_unverified_pos_booking_list($place_id);
                        
                        $accepted_pos_booking_list = $this->user_model->accepted_pos_booking_list($place_id);
                        foreach($accepted_pos_booking_list as $accepted_pos_booking_list_key => $accepted_pos_booking_list_row){
                            if($accepted_pos_booking_list_row['verify_status'] == 1){
                                $accepted_pos_booking_list[$accepted_pos_booking_list_key]['color_code']= "#008000";

                        }else{
                            $accepted_pos_booking_list[$accepted_pos_booking_list_key]['color_code']= "#FF0000";
                            }
                        }
                        
                        $rejected_pos_booking = $this->user_model->rejected_pos_booking_list($place_id);
                        foreach ($rejected_pos_booking as $rejected_pos_booking_key => $rejected_pos_booking_row) {
                            $rejected_pos_booking[$rejected_pos_booking_key]['color_code'] = "#FF0000";
                        }
                        $completed_booking_list = $this->user_model->completed_booking_list($place_id);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'success';
                        $response['ongoing_unverified_booking_list'] = $ongoing_unverified_booking_list;
                        $response['booking_list_status'] = $accepted_pos_booking_list;
                        $response['completed_booking_list'] = $completed_booking_list; 
                        // $response['rejected_pos_booking'] = $rejected_pos_booking;
                        // $response['history_booking'] = $history_booking;           
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_all_pos_checked_in_data_post()
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
                    $check_in_data = $this->model->selectWhereData('tbl_pos_booking',array('fk_place_id'=>$place_id,'book_status'=>1),array('*'),false);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['checked_in_data']= $check_in_data;
                }                                  
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
}
