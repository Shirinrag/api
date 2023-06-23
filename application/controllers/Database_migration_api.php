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
                $user_car_data = $this->model->selectWhereData('ci_car_details',array('user_id'=>$users_data_row['id']),array('car_number','is_deleted'));
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
                            'status'=>$status
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
}
