<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "-1");
require APPPATH . '/libraries/REST_Controller.php';

class Database_migration_api extends REST_Controller {
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

    public function get_all_user_get()
    {           
            // $this->load->model('database_migration_model');
            $users_data = $this->model->selectWhereData('ci_users',array(),array('*'),false);

            foreach ($users_data as $users_data_key => $users_data_row) {
               
                $user_wallet_data = $this->model->selectWhereData('ci_wallet_user',array('user_id'=>$users_data_row['id']),array('amount'));         
                
                $insert_user_data = array(
                    'userName'=> $users_data_row['username'],
                    'firstName'=> $users_data_row['firstname'],
                    'lastName'=> $users_data_row['lastname'],
                    'email'=> $users_data_row['email'],
                    'phoneNo'=> $users_data_row['mobile_no'],
                    'address'=> $users_data_row['address'],
                    'user_type'=> 10,
                    'isVerified'=> $users_data_row['is_verify'],
                    'referal_code'=> $users_data_row['referal_code'],
                    'device_id'=> $users_data_row['device_id'],
                    'notifn_topic'=> $users_data_row['notifn_topic'],
                    'terms_condition'=> $users_data_row['terms_condition'],
                    'created_at'=>$users_data_row['created_at'],
                    'updated_at'=>$users_data_row['updated_at']
                );
                $inserted_id = $this->model->insertData('pa_users',$insert_user_data);
                $insert_user_wallet_data = array(
                    'fk_user_id'=> $inserted_id,
                    'amount' =>$user_wallet_data['amount']
                );
                $this->model->insertData('tbl_user_wallet',$insert_user_wallet_data);
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            // $response['user_type_data'] = $user_type;
            echo json_encode($response);
    }

    public function add_car_details_get()
    {
        $users_data = $this->model->selectWhereData('ci_users',array(),array('username','id','mobile_no'),false);

            foreach ($users_data as $users_data_key => $users_data_row) {
                $users_data_1 = $this->model->selectWhereData('pa_users',array('userName'=>$users_data_row['username']),array('phoneNo','id'));
                $user_car_data = $this->model->selectWhereData('ci_car_details',array('user_id'=>$users_data_row['id']),array('car_number','is_deleted','created_date','updated_date'));
                $status ="";
                if($user_car_data['is_deleted']== 0){
                    $status = 1;
                }else{
                    $status = 0;
                }
                if($users_data_row['mobile_no'] == $users_data_1['phoneNo']){
                    if(!empty($user_car_data['car_number'])){
                         $insert_car_details = array(
                            'fk_user_id'=> $users_data_1['id'],
                            'car_number'=>$user_car_data['car_number'],
                            'status'=>$status,
                            'created_at'=>$user_car_data['created_date'],
                            'updated_at'=>$user_car_data['updated_date'],
                        ); 
                         $this->model->insertData('tbl_user_car_details',$insert_car_details);
                    }
                }
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            // $response['user_type_data'] = $user_type;
            echo json_encode($response);
    }

    public function migrate_all_user_wallet_history_get()
    {
          $total_amount ='';
          $add_amount= '';
            
          $user_wallet_history = $this->model->selectWhereData('ci_wallet_history',array(),array('*'),false);
          
            foreach ($user_wallet_history as $user_wallet_history_key => $user_wallet_history_row) {

                if($user_wallet_history_row['status'] == 1){
                    $add_amount = $user_wallet_history_row['amount'];
                 }else if($user_wallet_history_row['status'] == 2){
                    $deduct_amount = $user_wallet_history_row['amount'];
                 }


                $users_data = $this->model->selectWhereData('ci_users',array('id'=>$user_wallet_history_row['user_id']),array('username'));               

                $new_user_details = $this->model->selectWhereData('pa_users',array('userName'=>$users_data['username']),array('id'));

                $last_wallet_data = $this->model->selectWhereData('tbl_user_wallet_history',array('used_status'=>1,'fk_user_id'=>$new_user_details['id']),array('total_amount','id'));

                 if(empty($last_wallet_data['total_amount'])){
                    $total_amount = $add_amount;
                 }else{
                    $total_amount = $last_wallet_data['total_amount'] - $deduct_amount;
                 }

                 $this->model->updateData('tbl_user_wallet_history',array('used_status'=> 0),array('id'=>$last_wallet_data['id']));
                 if(!empty($new_user_details['id'])){
                    if($user_wallet_history_row['status'] == 1){
                    $curl_data = array(
                        'fk_user_id'=>$new_user_details['id'],
                        'add_amount'=>$add_amount,
                        // 'deduct_amount'=>$deduct_amount,
                        'total_amount'=>$total_amount,
                        'transac_id'=>$user_wallet_history_row['transac_id'],
                        'created_at'=>$user_wallet_history_row['onCreated'],
                        'updated_at'=>$user_wallet_history_row['onUpdated'],
                    );
                 }else if($user_wallet_history_row['status'] == 2){
                   $curl_data = array(
                        'fk_user_id'=>$new_user_details['id'],
                        // 'add_amount'=>$add_amount,
                        'deduct_amount'=>$deduct_amount,
                        'total_amount'=>$total_amount,
                        'transac_id'=>$user_wallet_history_row['transac_id'],
                        'created_at'=>$user_wallet_history_row['onCreated'],
                        'updated_at'=>$user_wallet_history_row['onUpdated'],
                    );
                 }
                    
                    $this->model->insertData('tbl_user_wallet_history',$curl_data);
                 }
                                
            }
            // die;
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            // $response['user_type_data'] = $user_type;
            echo json_encode($response);
    }

    public function migrate_sensor_data_get()
    {
            $sensor_data = $this->model->selectWhereData('mpc_sensor',array(),array('*'),false);
            foreach ($sensor_data as $sensor_data_key => $sensor_data_row) {

                $place_name = $this->model->selectWhereData('ci_parking_places',array('id'=>$sensor_data_row['place_id']),array('placename'));

                $new_place_details_id = $this->model->selectWhereData('tbl_parking_place',array('place_name'=>$place_name['placename']),array('id'));  
                $slot_id = $this->model->selectWhereData('tbl_slot_info',array('fk_place_id'=>$new_place_details_id['id']),array('id'));
                if(!empty($sensor_data_row['place_id'])){
                    $insert_sensor_data = array(
                        'fk_place_id'=>$sensor_data_row['place_id'],
                        'fk_slot_id'=> $sensor_data_row['slot_id'],
                        'sensor_time'=>$sensor_data_row['sensor_time'],
                        'battery_voltage'=>$sensor_data_row['battery_voltage'],
                        'status'=>$sensor_data_row['status'],
                        'created_at'=>$sensor_data_row['created_date'],
                        'updated_at'=>$sensor_data_row['updated_date'],
                    );
                    $this->model->insertData('tbl_sensor',$insert_sensor_data);
                }                
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            // $response['user_type_data'] = $user_type;
            echo json_encode($response);
    }

    public function migrate_admin_data_get()
    {
        $admin_data = $this->model->selectWhereData('ci_admin',array('admin_role_id'=>12),array('*'),false);
            // echo '<pre>'; print_r($admin_data); exit;
            foreach ($admin_data as $admin_data_key => $admin_data_row) {
                $insert_user_data = array(
                    'userName'=> $admin_data_row['username'],
                    'firstName'=> $admin_data_row['firstname'],
                    'lastName'=> $admin_data_row['lastname'],
                    'email'=> $admin_data_row['email'],
                    'phoneNo'=> $admin_data_row['mobile_no'],
                    'user_type'=> 12,
                    'password'=> dec_enc('encrypt',"Password1"),
                    'isVerified'=> $admin_data_row['is_verify'],
                    'device_id'=> $admin_data_row['device_id'],
                    'notifn_topic'=> $admin_data_row['notifn_topic'],
                    'created_at'=> $admin_data_row['created_at'],
                    'updated_at'=> $admin_data_row['updated_at'],
                );
                $inserted_id = $this->model->insertData('pa_users',$insert_user_data);
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            // $response['user_type_data'] = $user_type;
            echo json_encode($response);
    }

    public function transcation_history_get()
    {
        $transcation_history = $this->model->selectWhereData('ci_transaction_history',array(),array('*'),false);
        // echo '<pre>'; print_r($transcation_history); exit;
        foreach ($transcation_history as $transcation_history_key => $transcation_history_row) {
            $users_data = $this->model->selectWhereData('ci_users',array('id'=>$transcation_history_row['user_id']),array('username'));
              $new_users_data = $this->model->selectWhereData('ci_users',array('userName'=>$users_data['username']),array('id'));
                // echo '<pre>'; print_r($new_users_data);
              if(!empty($new_users_data['id'])){
                    $insert_data= array(
                        'fk_user_id'=>$new_users_data['id'],
                        'order_id'=>$transcation_history_row['order_id'],
                        'payment_id'=>$transcation_history_row['payment_id'],
                        'amount'=>$transcation_history_row['amount'],
                        'payment_status'=>$transcation_history_row['status'],
                        'created_at'=>$transcation_history_row['on_created'],
                        'updated_at'=>$transcation_history_row['on_updated'],
                      );

                      $this->model->insertData('tbl_transcation',$insert_data);
              }              
        }
         $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            // $response['user_type_data'] = $user_type;
            echo json_encode($response);
    }

    public function migrate_booking_data_get()
    {
            $this->load->model('database_migration_model');
            $booking_data = $this->database_migration_model->booking_data();

            // $booking_data = $this->model->selectWhereData('ci_booking',array(),array('*'),false);
            // echo '<pre>'; print_r($booking_data); exit;
            foreach ($booking_data as $booking_data_key => $booking_data_row) {
                 $booking_check = $this->model->selectWhereData('ci_booking_check',array('booking_id'=>$booking_data_row['id']),array('*'));
                 $booking_verify = $this->model->selectWhereData('ci_booking_verify',array('booking_id'=>$booking_data_row['id']),array('*'));

                 $username = $this->model->selectWhereData('ci_users',array('id'=>$booking_data_row['user_id']),array('username'));

                  $new_users_data = $this->model->selectWhereData('ci_users',array('userName'=>$username['username']),array('id'));

                  $car_id = $this->model->selectWhereData('tbl_user_car_details',array('fk_user_id',$new_users_data['id']),array('id'));

                   $slot_id = $this->model->selectWhereData('tbl_slot_info',array('fk_place_id'=>$booking_data_row['place_id']),array('id'));

                    if($booking_data_row['booking_type']==0){
                        $booking_type = 1;
                    }else if($booking_data_row['booking_type']==1){
                        $booking_type = 2;
                    }else{
                        $booking_type = 3;
                    }
                    $time1 = strtotime($booking_data_row['from_time']);
                    $time2 = strtotime($booking_data_row['to_time']);
                    $difference = round(abs($time2 - $time1) / 3600,2);

                    if(empty($booking_data_row['book_ext'])){
                        $insert_booking_data = array(
                            'booking_id' =>$booking_data_row['unique_booking_id'],
                            'fk_user_id' =>$new_users_data['id'],
                            'fk_car_id' =>$car_id['id'],
                            'fk_place_id' =>$booking_data_row['place_id'],
                            'fk_slot_id' =>$slot_id['id'],
                            'fk_booking_type_id' =>$booking_type,
                            'booking_from_date' =>$booking_data_row['booking_from_date'],
                            'booking_to_date' =>$booking_data_row['booking_to_date'],
                            'booking_from_time' =>$booking_data_row['from_time'],
                            'booking_to_time' =>$booking_data_row['to_time'],
                            'reserve_from_time' =>$booking_data_row['reserve_from_time'],
                            'reserve_to_time' =>$booking_data_row['reserve_to_time'],
                            'total_hours' =>$difference,
                            'created_at'=>$booking_data_row['created_date'],
                            'updated_at'=>$booking_data_row['updated_date']
                        );  
                        $inserted_id = $this->model->insertData('tbl_booking',$insert_booking_data); 

                        $payment_data_insert = array(
                            'fk_booking_id'=>$inserted_id,
                            'fk_user_id'=>$new_users_data['id'],
                            'amount'=>$booking_data_row['originalCost'],
                            'total_amount'=>$booking_data_row['originalCost'],
                             'created_at'=>$booking_data_row['created_date'],
                            'updated_at'=>$booking_data_row['updated_date']
                        );
                        $this->model->insertData('tbl_payment',$payment_data_insert);

                        if($booking_data_row['booking_status']==0){
                            $status=1;
                        }else if($booking_data_row['booking_status']==1){
                            $status=2;
                        }else if($booking_data_row['booking_status']==2){
                            $status=3;
                        }else if($booking_data_row['booking_status']==3){
                            $status=4;
                        }else if($booking_data_row['booking_status']==4){
                            $status=5;
                        }
                        $this->model->updateData('tbl_booking_status',array('used_status'=>0),array('fk_booking_id'=>$inserted_id));
                        $booking_status = array(
                            'fk_booking_id'=>$inserted_id,
                            'fk_status_id'=>$status,
                            'used_status'=>1,
                        ); 
                        $this->model->insertData('tbl_booking_status',$booking_status);
                    }
                    $booking_id = $this->model->selectWhereData('tbl_booking',array('booking_id'=>$booking_data_row['unique_booking_id']),array('id'));

                    if(!empty($booking_data_row['book_ext'])){
                        $extension_booking_insert_data = array(
                            'fk_booking_id' =>$booking_id['id'],
                            'fk_user_id' =>$new_users_data['id'],
                            'fk_place_id' =>$booking_data_row['place_id'],
                            'booking_ext_replace'=> $booking_data_row['book_ext'],
                            'booking_from_date' =>$booking_data_row['booking_from_date'],
                            'booking_to_date' =>$booking_data_row['booking_to_date'],
                            'booking_from_time' =>$booking_data_row['from_time'],
                            'booking_to_time' =>$booking_data_row['to_time'],
                            'reserve_from_time' =>$booking_data_row['reserve_from_time'],
                            'reserve_to_time' =>$booking_data_row['reserve_to_time'],
                            'total_hours' =>$difference,
                             'created_at'=>$booking_data_row['created_date'],
                            'updated_at'=>$booking_data_row['updated_date']
                        );  
                        $extension_booking_inserted_id = $this->model->insertData('tbl_extension_booking',$extension_booking_insert_data); 
                        $extension_booking_payment_data_insert = array(
                            'fk_booking_id'=>$booking_id['id'],
                            'fk_ext_booking_id'=>$extension_booking_inserted_id,
                            'fk_user_id'=>$new_users_data['id'],
                            'amount'=>$booking_data_row['originalCost'],
                            'total_amount'=>$booking_data_row['originalCost'],
                             'created_at'=>$booking_data_row['created_date'],
                            'updated_at'=>$booking_data_row['updated_date']
                        );
                        $this->model->insertData('tbl_payment',$extension_booking_payment_data_insert); 

                         if($booking_data_row['booking_status']==0){
                            $status=1;
                        }else if($booking_data_row['booking_status']==1){
                            $status=2;
                        }else if($booking_data_row['booking_status']==2){
                            $status=3;
                        }else if($booking_data_row['booking_status']==3){
                            $status=4;
                        }else if($booking_data_row['booking_status']==4){
                            $status=5;
                        }
                        $this->model->updateData('tbl_booking_status',array('used_status'=>0),array('fk_booking_id'=>$booking_id['id']));
                        $booking_status = array(
                            'fk_booking_id'=>$booking_id['id'],
                            'fk_status_id'=>$status,
                            'used_status'=>1,
                        ); 
                        $this->model->insertData('tbl_booking_status',$booking_status);
                    }
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            // $response['user_type_data'] = $user_type;
            echo json_encode($response);
    }

    public function migrate_booking_verify_get()
    {
         $this->load->model('database_migration_model');
            $booking_data = $this->database_migration_model->booking_data();
            foreach ($booking_data as $booking_data_key => $booking_data_row) {
                    $booking_verify_data = $this->model->selectWhereData('ci_booking_verify',array(),array('*'));

                    $booking_id = $this->model->selectWhereData('tbl_booking',array('booking_id'=>$booking_data_row['unique_booking_id']),array('id'));

                    $verifier_username = $this->model->selectWhereData('ci_admin',array('admin_id'=>$booking_verify_data['verifier_id']),array('username'));

                    $verifier_id = $this->model->selectWhereData('pa_users',array('userName'=>$verifier_username['username'],'user_type'=>3),array('id'));

                    if($booking_verify_data['booking_type']==0){
                        $booking_type =1;
                    }else{
                         $booking_type =2;
                    }
                    if($booking_verify_data['booking_type']==0){
                        $booking_type =1;
                    }else{
                         $booking_type =2;
                    }
                    $insert_verify_booking_data = array(
                        'fk_booking_id'=>$booking_id['id'],
                        'fk_verifier_id'=>$verifier_id['id'],
                        'fk_booking_type_id'=>$booking_type,
                        'verify_status'=>$booking_verify_data['verify_status'],
                        'created_at'=>$booking_verify_data['onCreated'],
                        'updated_at'=>$booking_verify_data['onUpdated'],
                    );
                    $this->model->insertData('tbl_booking_verify',$insert_verify_booking_data);
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            // $response['user_type_data'] = $user_type;
            echo json_encode($response);

    }

    public function migrate_booking_check_in_out_get()
    {
         $this->load->model('database_migration_model');
            // $booking_data = $this->database_migration_model->booking_data();
             $booking_check_in_out_data = $this->model->selectWhereData('ci_booking_check',array(),array('*'),false);
            foreach ($booking_check_in_out_data as $booking_check_in_out_data_key => $booking_check_in_out_data_row) {
                    // $booking_check_in_out_data = $this->model->selectWhereData('ci_booking_check',array(),array('*'));
                $booking_data = $this->model->selectWhereData('ci_booking',array('id'=>$booking_check_in_out_data_row['booking_id']),array('*'));

                    $booking_id = $this->model->selectWhereData('tbl_booking',array('booking_id'=>$booking_data['unique_booking_id']),array('id'));

                    $verifier_username = $this->model->selectWhereData('ci_admin',array('admin_id'=>$booking_check_in_out_data_row['verifier_id']),array('username'));

                    $verifier_id = $this->model->selectWhereData('pa_users',array('userName'=>$verifier_username['username'],'user_type'=>3),array('id'));

                    if($booking_check_in_out_data_row['check_type']==0){
                        $check_type =1;
                    }else if($booking_check_in_out_data_row['check_type']==1){
                         $check_type =2;
                    }elseif($booking_check_in_out_data_row['check_type']==2){
                            $check_type =3;
                    }
                    if($booking_check_in_out_data_row['checkout_stat']==0){
                        $checkout_status =1;
                    }elseif($booking_check_in_out_data_row['checkout_stat']==1){
                         $checkout_status =2;
                    }elseif($booking_check_in_out_data_row['checkout_stat']==2){
                         $checkout_status =3;
                    }elseif($booking_check_in_out_data_row['checkout_stat']==3){
                         $checkout_status =4;
                    }
                    $insert_check_in_out_booking_data = array(
                        'fk_booking_id'=>$booking_id['id'],
                        'fk_verifier_id'=>$verifier_id['id'],
                        'check_in'=>$booking_check_in_out_data_row['check_in'],
                        'check_out'=>$booking_check_in_out_data_row['check_out'],
                        'fk_booking_check_type'=>$check_type,
                        'fk_booking_checkout_status'=>$checkout_status,
                     
                        'created_at'=>$booking_check_in_out_data_row['created_at'],
                        'updated_at'=>$booking_check_in_out_data_row['updated_at'],
                    );
                    $this->model->insertData('tbl_booking_check_in_out',$insert_check_in_out_booking_data);
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            echo json_encode($response);
        }

        public function migrate_verifier_logged_in_get()
        {          
             $verifier_login_data = $this->model->selectWhereData('tbl_verifier_login',array(),array('*'),false);
                foreach ($verifier_login_data as $verifier_login_data_key => $verifier_login_data_row) {                

                    $verifier_username = $this->model->selectWhereData('ci_admin',array('admin_id'=>$verifier_login_data_row['verifier_id']),array('username'));

                    $verifier_id = $this->model->selectWhereData('pa_users',array('userName'=>$verifier_username['username'],'user_type'=>3),array('id'));

                    if($verifier_login_data_row['status']==0){
                        $status = 1;
                    }else{
                        $status = 2;
                    }

                    $insert_verifier_logged_in_data = array(
                        'fk_verifier_id'=>$verifier_id['id'],
                        'login_time'=>$verifier_login_data_row['login_time'],
                        'logout_time'=>$verifier_login_data_row['logout_time'],
                        'status'=>$status,
                     
                        'created_at'=>$verifier_login_data_row['created_at'],
                        'updated_at'=>$verifier_login_data_row['created_at'],
                    );
                    $this->model->insertData('tbl_verifier_logged_in',$insert_verifier_logged_in_data);
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            echo json_encode($response);
        }

        public function migrate_verifier_duty_get()
        {
            $verifier_duty_data = $this->model->selectWhereData('tbl_verifier_place',array(),array('*'),false);
                foreach ($verifier_duty_data as $verifier_duty_data_key => $verifier_duty_data_row) {                

                    $verifier_username = $this->model->selectWhereData('ci_admin',array('admin_id'=>$verifier_duty_data_row['verifier_id']),array('username'));

                    $verifier_id = $this->model->selectWhereData('pa_users',array('userName'=>$verifier_username['username'],'user_type'=>3),array('id'));
                    if(!empty($verifier_duty_data_row['duty_date'])){
                        if(!empty($verifier_id['id']))
                        {

                                 $insert_verifier_logged_in_data = array(
                                        'fk_verifier_id'=>$verifier_id['id'],
                                        'fk_place_id'=>$verifier_duty_data_row['place_id'],
                                        'date'=>$verifier_duty_data_row['duty_date'],
                                        'created_at'=>$verifier_duty_data_row['onCreated'],
                                        'updated_at'=>$verifier_duty_data_row['updatedDate'],
                                    );
                                    $this->model->insertData('tbl_duty_allocation',$insert_verifier_logged_in_data);
                                        }

                    }
                    
            }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            echo json_encode($response);
        }
}
