<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "-1");
require APPPATH . '/libraries/REST_Controller.php';

class Superadmin_api extends REST_Controller {

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
    // =======================Role API=============================
    public function add_role_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $user_type = $this->input->post('user_type');
                if(empty($user_type)){
                    $response['message'] = "User Id is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_user_type', array('user_type'=>$user_type,'status'=>1));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Role Already exist.';                              
                    }else{
                        $curl_data = array(
                            'user_type' =>$user_type,
                        );
                        $this->model->insertData('tbl_user_type',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Role Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function display_all_role_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $role_data = $this->crud_model->get_datatables('tbl_user_type', array('id', 'user_type'), array('status' => 1), array(null, 'user_type'), array('id' => 'DESC'));
                $count = $this->crud_model->count_all('tbl_user_type', array('id', 'user_type'), array('status' => 1), array(null, 'user_type'), array('id' => 'DESC'));
                $count_filtered = $this->crud_model->count_filtered('tbl_user_type', array('id', 'user_type'), array('status' => 1), array(null, 'user_type'), array('id' => 'DESC'));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['role_data'] = $role_data;
                $response['count'] = $count;
                $response['count_filtered'] = $count_filtered;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_all_role_on_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "User Id is required";
                    $response['code'] = 201;
                }else{
                    $user_type = $this->model->selectWhereData('tbl_user_type',array('id'=>$id),array('*'));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['user_type_data'] = $user_type;
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_role_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $user_type = $this->input->post('user_type');
                $id = $this->input->post('id');
                if(empty($user_type)){
                    $response['message'] = "User Id is required";
                    $response['code'] = 201;
                }else if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $check_user_type_count = $this->model->CountWhereRecord('tbl_user_type', array('user_type'=>$user_type,'id !='=>$id,'status'=>1));
                    if($check_user_type_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Role Already exist.';        
                    }else{
                        $curl_data = array(
                            'user_type' =>$user_type,
                        );
                        $this->model->updateData('tbl_user_type',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Role Updated Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function delete_role_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                        $curl_data = array(
                            'status' =>0,
                        );
                        $this->model->updateData('tbl_user_type',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Role Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function change_password_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                $password = $this->input->post('password');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else if(empty($password)){
                    $response['message'] = "Password is required";
                    $response['code'] = 201;
                }else{
                        $curl_data = array(
                            'password' =>dec_enc('encrypt',$password),
                        );
                        $this->model->updateData('pa_users',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Password Updated Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function add_admin_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $user_type = $this->input->post('user_type');
                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $email = $this->input->post('email');
                $mobile_no = $this->input->post('mobile_no');
                $password = $this->input->post('password');
                $username = $this->input->post('username');
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
                }else if(empty($user_type)){
                    $response['message'] = "User Type is required";
                    $response['code'] = 201;
                }else if(empty($username)){
                    $response['message'] = "User Name is required";
                    $response['code'] = 201;
                }else{
                    $check_email_count = $this->model->CountWhereRecord('pa_users', array('email'=>$email,'isActive'=>1,'user_type'=>$user_type));
                    $check_mobile_no_count = $this->model->CountWhereRecord('pa_users', array('phoneNo'=>$mobile_no,'isActive'=>1,'user_type'=>$user_type));
                    $check_user_name_count = $this->model->CountWhereRecord('pa_users', array('username'=>$username,'isActive'=>1,'user_type'=>$user_type));
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
                        $curl_data = array(
                            'firstName' =>$first_name,
                            'lastName' =>$last_name,
                            'email' =>$email,
                            'phoneNo' =>$mobile_no,
                            'password' =>dec_enc('encrypt',$password),
                            'user_type' =>$user_type,
                            'username' =>$username,
                        );
                        $this->model->insertData('pa_users',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Admin Data Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function display_all_admin_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
               $this->load->model('superadmin_model');
                $admin_data = $this->superadmin_model->display_all_admin_data();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['admin_data'] = $admin_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_admin_data_on_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $admin = $this->model->selectWhereData('pa_users',array('id'=>$id),array('*'));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['admin_data'] = $admin;
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_admin_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $user_type = $this->input->post('user_type');
                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $email = $this->input->post('email');
                $mobile_no = $this->input->post('mobile_no');
                $password = $this->input->post('password');
                // $username = $this->input->post('username');
                $id = $this->input->post('id');
                if(empty($first_name)){
                    $response['message'] = "First Name is required";
                    $response['code'] = 201;
                }else if(empty($last_name)){
                    $response['message'] = "Last Name is required";
                    $response['code'] = 201;
                }else if(empty($mobile_no)){
                    $response['message'] = "Mobile No is required";
                    $response['code'] = 201;
                }else if(empty($user_type)){
                    $response['message'] = "User Type is required";
                    $response['code'] = 201;
                }else if(empty($email)){
                    $response['message'] = "Email is required";
                    $response['code'] = 201;
                }else{
                    $check_mobile_no_count = $this->model->CountWhereRecord('pa_users', array('phoneNo'=>$mobile_no,'del_status'=>1,'id !=' => $id));
                    $check_email_count = $this->model->CountWhereRecord('pa_users', array('email'=>$email,'del_status'=>1,'id !=' => $id));
                   if($check_mobile_no_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Mobile No Already exist.';         
                        $response['error_status'] = 'contact_no';                     
                    }else if($check_email_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Email Already exist.';
                        $response['error_status'] = 'email';                             
                    }else{
                        $curl_data = array(
                            'firstName' =>$first_name,
                            'lastName' =>$last_name,
                            'phoneNo' =>$mobile_no,
                            'user_type' =>$user_type,
                            'email' =>$email
                        );
                        $this->model->updateData('pa_users',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Admin Data Updated Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
     public function delete_admin_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                        $curl_data = array(
                            'del_status' =>0,
                        );
                        $this->model->updateData('pa_users',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Admin Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function add_user_post()
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
                }else{
                    $check_email_count = $this->model->CountWhereRecord('pa_users', array('email'=>$email,'status'=>1));
                    $check_mobile_no_count = $this->model->CountWhereRecord('pa_users', array('mobile_no'=>$mobile_no,'status'=>1));
                    $check_user_name_count = $this->model->CountWhereRecord('pa_users', array('username'=>$username,'status'=>1));
                    if($check_email_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Email Already exist.';    
                    }else if($check_mobile_no_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Mobile No Already exist.';         
                    }else if($check_user_name_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Username Already exist.';                          
                    }else{
                        $user_type = $this->model->selectWhereData('tbl_user_type',array('user_type'=>"User"),array('id'));
                        $curl_data = array(
                            'firstName' =>$first_name,
                            'lastName' =>$last_name,
                            'email' =>$email,
                            'phoneNo' =>$mobile_no,
                            'password' =>dec_enc('encrypt',$password),
                            'user_type' =>$user_type['id'],
                            'username' =>$username,
                        );
                        $this->model->insertData('pa_users',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'User Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function display_all_user_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $this->load->model('superadmin_model');
                $user_data = $this->superadmin_model->display_all_user_data();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['user_data'] = $user_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_user_data_on_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $admin = $this->model->selectWhereData('pa_users',array('id'=>$id),array('*'));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['admin_data'] = $admin;
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_user_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $user_type = $this->input->post('user_type');
                $first_name = $this->input->post('first_name');
                $last_name = $this->input->post('last_name');
                $mobile_no = $this->input->post('mobile_no');
                $username = $this->input->post('username');
                $id = $this->input->post('id');
                if(empty($first_name)){
                    $response['message'] = "First Name is required";
                    $response['code'] = 201;
                }else if(empty($last_name)){
                    $response['message'] = "Last Name is required";
                    $response['code'] = 201;
                }else if(empty($mobile_no)){
                    $response['message'] = "Mobile No is required";
                    $response['code'] = 201;
                }else if(empty($user_type)){
                    $response['message'] = "User Type is required";
                    $response['code'] = 201;
                }else if(empty($username)){
                    $response['message'] = "User Name is required";
                    $response['code'] = 201;
                }else{
                    $check_mobile_no_count = $this->model->CountWhereRecord('pa_users', array('mobile_no'=>$mobile_no,'status'=>1));
                    $check_user_name_count = $this->model->CountWhereRecord('pa_users', array('username'=>$username,'status'=>1));
                   if($check_mobile_no_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Mobile No Already exist.';
                    }else if($check_user_name_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Username Already exist.';
                    }else{
                        $curl_data = array(
                            'firstName' =>$first_name,
                            'lastName' =>$last_name,
                            'phoneNo' =>$mobile_no,
                            'user_type' =>$user_type,
                            'username' =>$username,
                        );
                        $this->model->updateData('pa_users',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Admin Data Updated Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function delete_user_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                        $curl_data = array(
                            'status' =>0,
                        );
                        $this->model->updateData('pa_users',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Role Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_user_status_post() {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'isActive'=>$status,
                );
                $this->model->updateData('pa_users',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }

    public function booking_history_data_get()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $this->load->model('superadmin_model');
            $booking_history = $this->superadmin_model->booking_history_data();
            foreach ($booking_history as $booking_history_key => $booking_history_row) {
                        $total_amount = $this->model->selectWhereData('tbl_payment',array('fk_booking_id'=>$booking_history_row['id']),array('total_amount'));
                        $booking_history[$booking_history_key]['total_amount'] = $total_amount['total_amount'];
                        $booking_status_id = $this->model->selectWhereData('tbl_booking_status',array('fk_booking_id'=>$booking_history_row['id'],'used_status'=>1),array('fk_status_id'));
                        $booking_history[$booking_history_key]['fk_status_id'] = $booking_status_id['fk_status_id'];
                        $booking_status = $this->model->selectWhereData('tbl_status_master',array('id'=>$booking_status_id['fk_status_id']),array('status'));
                        $booking_history[$booking_history_key]['booking_status'] = $booking_status['status'];                        
                    }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            $response['booking_history_data'] = $booking_history;
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
     public function extend_booking_history_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $this->load->model('superadmin_model');
            $extend_booking_history = $this->superadmin_model->extend_booking_history_data();
            foreach ($extend_booking_history as $extend_booking_history_key => $extend_booking_history_row) {
                        $total_amount = $this->model->selectWhereData('tbl_payment',array('fk_ext_booking_id'=>$extend_booking_history_row['id']),array('total_amount'));
                        $extend_booking_history[$extend_booking_history_key]['total_amount'] = $total_amount['total_amount'];        
                    }
            $response['code'] = REST_Controller::HTTP_OK;
            $response['status'] = true;
            $response['message'] = 'success';
            $response['extend_booking_history_data'] = $extend_booking_history;
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function add_place_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $fk_vendor_id = $this->input->post('fk_vendor_id');
                $fk_country_id = $this->input->post('fk_country_id');
                $fk_state_id = $this->input->post('fk_state_id');
                $fk_city_id = $this->input->post('fk_city_id');
                $place_name = $this->input->post('place_name');
                $address = $this->input->post('address');
                $pincode = $this->input->post('pincode');
                $latitude = $this->input->post('latitude');
                $longitude = $this->input->post('longitude');
                $pincode = $this->input->post('pincode');
                $slots = $this->input->post('slots');
                $fk_place_status_id = $this->input->post('fk_place_status_id');
                $fk_parking_price_type = $this->input->post('fk_parking_price_type');
                $ext_price = $this->input->post('ext_price');
                $from_hours = $this->input->post('from_hours');
                $from_hours = json_decode($from_hours,true);
                $to_hours = $this->input->post('to_hours');
                $to_hours = json_decode($to_hours,true);                
                $price = $this->input->post('price');
                $price = json_decode($price,true);       
                $per_hour_charges = $this->input->post('per_hour_charges');
                $fk_vehicle_type = $this->input->post('fk_vehicle_type');
                $fk_vehicle_type = json_decode($fk_vehicle_type,true);
                $place_count = $this->input->post('place_count');
                $reserved_place_count = $this->input->post('reserved_place_count');
                $total_place_count = $this->input->post('total_place_count');
                $referral_code = $this->input->post('referral_code');
                if(empty($fk_vendor_id)){
                    $response['message'] = "First Name is required";
                    $response['code'] = 201;
                }else if(empty($fk_country_id)){
                    $response['message'] = "Last Name is required";
                    $response['code'] = 201;
                }else if(empty($fk_state_id)){
                    $response['message'] = "fk_state_id is required";
                    $response['code'] = 201;
                }else if(empty($fk_city_id)){
                    $response['message'] = "Mobile No is required";
                    $response['code'] = 201;
                }else if(empty($place_name)){
                    $response['message'] = "place_name is required";
                    $response['code'] = 201;
                }else if(empty($address)){
                    $response['message'] = "User Name is required";
                    $response['code'] = 201;
                }else if(empty($pincode)){
                    $response['message'] = "Pincode is required";
                    $response['code'] = 201;
                }else if(empty($latitude)){
                    $response['message'] = "Latitude is required";
                    $response['code'] = 201;
                }else if(empty($longitude)){
                    $response['message'] = "Longitude is required";
                    $response['code'] = 201;
                }else if(empty($slots)){
                    $response['message'] = "Slots is required";
                    $response['code'] = 201;
                }else if(empty($ext_price)){
                    $response['message'] = "Extension Price is required";
                    $response['code'] = 201;
                }else{
                    $check_place_count = $this->model->CountWhereRecord('tbl_parking_place', array('place_name'=>$place_name,'fk_place_status_id'=>$fk_place_status_id,'status'=>1));  
                    $check_referral_code_count = $this->model->CountWhereRecord('tbl_referral_code', array('referral_code'=>$referral_code,'status'=>1));          
                    if($check_place_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Place Name Already exist.';             
                    }else if($check_referral_code_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Referral Code Already Exist......!';                              
                    }else{
                        $curl_data = array(
                            'fk_vendor_id'=>$fk_vendor_id,
                            'fk_country_id'=>$fk_country_id,
                            'fk_state_id'=>$fk_state_id,
                            'fk_city_id'=>$fk_city_id,
                            'place_name'=>$place_name,
                            'address'=>$address,
                            'pincode'=>$pincode,
                            'latitude'=>$latitude,
                            'longitude'=>$longitude,
                            'slots'=>$slots,
                            'fk_place_status_id'=>$fk_place_status_id,
                            'fk_parking_price_type'=>$fk_parking_price_type,
                            'ext_price'=>$ext_price,
                            'per_hour_charges'=>$per_hour_charges,
                            'place_count'=>$place_count,
                            'reserved_place_count'=>$reserved_place_count,
                            'total_place_count'=>$total_place_count,
                            'referral_code' =>$referral_code,
                        );
                        $last_inserted_id = $this->model->insertData('tbl_parking_place',$curl_data); 

                        if(!empty($fk_vehicle_type[0])){
                            foreach ($fk_vehicle_type as $fk_vehicle_type_key => $fk_vehicle_type_row) {
                                $insert_vehicle_type_data = array(
                                    'fk_place_id' =>$last_inserted_id,
                                    'fk_vehicle_type_id'=>$fk_vehicle_type_row
                                );                                    
                                $this->model->insertData('tbl_parking_place_vehicle_type',$insert_vehicle_type_data); 
                                $from_hours_1 = @$from_hours[$fk_vehicle_type_row];
                                $to_hours_1 = @$to_hours[$fk_vehicle_type_row];
                                $cost_1 = @$price[$fk_vehicle_type_row];
                               
                                foreach ($from_hours_1 as $from_hours_1_key => $from_hours_1_row) {
                                     $insert_price_data = array(
                                            'from_hours' =>$from_hours_1_row,
                                            'to_hours' =>@$to_hours_1[$from_hours_1_key],
                                            'cost' =>@$cost_1[$from_hours_1_key],
                                            'fk_place_id'=>$last_inserted_id,
                                            'fk_vehicle_type_id'=>$fk_vehicle_type_row
                                    );
                                     $this->model->insertData('tbl_hours_price_slab',$insert_price_data);                          
                                }
                            }                                                     
                        }
                        $prefix = $this->model->selectWhereData('tbl_states',array('id'=>$fk_state_id),array('prefix'));
                        for ($i=0; $i < $slots; $i++) { 
                            $this->load->model('superadmin_model');
                            $count = $this->superadmin_model->get_count_slot_name($prefix['prefix']);
                            if($count['total']==0){
                                $slot_name = $prefix['prefix'] . "-AA000";
                            }else{
                                $slot_name = $this->model->selectWhereData('tbl_slot_info',array('del_status'=>1),array('slot_name'),true,array('id','DESC'));
                                $slot_name = $slot_name['slot_name'];
                            }  
                            $this->load->model('superadmin_model');
                            $slot_name1 = $prefix['prefix'] . "-" . $this->superadmin_model->uniqueSlotName($slot_name);
                                $display_id = "P-" . ($i + 1);
                                $insert_slot_info_data=array(
                                    'fk_place_id' =>$last_inserted_id,
                                    'slot_name' =>$slot_name1,
                                    'display_id' =>$display_id
                                );
                                $this->model->insertData('tbl_slot_info',$insert_slot_info_data);  
                        }
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Parking Places Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function display_all_parking_place_data_post()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                $this->load->model('parking_place_model');
                $parking_place_data = $this->parking_place_model->get_datatables();
                $count = $this->parking_place_model->count_all();
                $count_filtered = $this->parking_place_model->count_filtered();
                $place_status = $this->model->selectWhereData('tbl_parking_place_status',array('status'=>1),array('id','place_status'),false);

                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['parking_place_data'] = $parking_place_data;
                $response['count'] = $count;
                $response['count_filtered'] = $count_filtered;
                $response['place_status'] = $place_status;
            } else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
    }
    public function get_parking_place_details_on_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $this->load->model('superadmin_model');
                    $parking_place = $this->superadmin_model->get_parking_place_details_on_id($id);
                    $state_details = $this->model->selectWhereData('tbl_states',array('country_id'=>$parking_place['fk_country_id']),array('id','name'),false);
                    $city_details = $this->model->selectWhereData('tbl_cities',array('state_id'=>$parking_place['fk_state_id']),array('id','name'),false);
                     $hour_price_slab_on_place_id = $this->superadmin_model->get_hour_price_slab($id);
                    $slot_info_on_place_id = $this->model->selectWhereData('tbl_slot_info',array('fk_place_id'=>$id,'del_status'=>1),array('*',"id as slot_info_id",),false);
                    foreach ($slot_info_on_place_id as $slot_info_on_place_id_key => $slot_info_on_place_id_row) {
                        $device_data = $this->model->selectWhereData('tbl_device',array(),array('id','device_id'),false,array('id',"ASC"));
                        if(!empty($slot_info_on_place_id_row['fk_machine_id'])){
                            $device_id = $this->model->selectWhereData('tbl_device',array('id'=>$slot_info_on_place_id_row['fk_machine_id']),array('device_id'));
                            $slot_info_on_place_id[$slot_info_on_place_id_key]['device_id'] = $device_id['device_id'];
                        }
                    }
                    $parking_place_vehicle_type = $this->model->selectWhereData('tbl_parking_place_vehicle_type',array('fk_place_id'=>$id),array('*',"id as parking_place_vehicle_type_id"),false);
                    $selected_parking_place_vehicle_type = $this->model->selectWhereData('tbl_parking_place_vehicle_type',array('fk_place_id'=>$id),array("GROUP_CONCAT(fk_vehicle_type_id) as fk_vehicle_type_id"),true,'','fk_place_id');
                    $vehicle_type = $this->model->selectWhereData('tbl_vehicle_type',array('del_status'=>1,'status'=>1),array('id','vehicle_type'),false);
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['parking_place_data'] = $parking_place;
                    $response['hour_price_slab'] = $hour_price_slab_on_place_id;
                    $response['slot_info'] = $slot_info_on_place_id;
                    $response['state_details'] = $state_details;
                    $response['city_details'] = $city_details;
                    $response['device_data'] = $device_data;
                    $response['parking_place_vehicle_type'] = $parking_place_vehicle_type;
                    $response['selected_parking_place_vehicle_type'] = $selected_parking_place_vehicle_type;
                    $response['vehicle_type'] = $vehicle_type;
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_place_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                $fk_vendor_id = $this->input->post('fk_vendor_id');
                $fk_country_id = $this->input->post('fk_country_id');
                $fk_state_id = $this->input->post('fk_state_id');
                $fk_city_id = $this->input->post('fk_city_id');
                $place_name = $this->input->post('place_name');
                $address = $this->input->post('address');
                $pincode = $this->input->post('pincode');
                $latitude = $this->input->post('latitude');
                $longitude = $this->input->post('longitude');
                $pincode = $this->input->post('pincode');
                $slots = $this->input->post('slots');
                $fk_place_status_id = $this->input->post('fk_place_status_id');
                $fk_parking_price_type = $this->input->post('fk_parking_price_type');
                $ext_price = $this->input->post('ext_price');
                $hour_price_slab_id = $this->input->post('hour_price_slab_id');
                $hour_price_slab_id = json_decode($hour_price_slab_id,true);
                $from_hours = $this->input->post('from_hours');
                $from_hours = json_decode($from_hours,true);
                $to_hours = $this->input->post('to_hours');
                $to_hours = json_decode($to_hours,true);                
                $price = $this->input->post('price');
                $price = json_decode($price,true); 
                $fk_vehicle_type = $this->input->post('fk_vehicle_type');
                $fk_vehicle_type = json_decode($fk_vehicle_type,true);
                $per_hour_charges = $this->input->post('per_hour_charges');
                $place_count = $this->input->post('place_count');
                $reserved_place_count = $this->input->post('reserved_place_count');
                $total_place_count = $this->input->post('total_place_count');
                $referral_code = $this->input->post('referral_code');
                
                if(empty($fk_vendor_id)){
                    $response['message'] = "First Name is required";
                    $response['code'] = 201;
                }else if(empty($fk_country_id)){
                    $response['message'] = "Last Name is required";
                    $response['code'] = 201;
                }else if(empty($fk_state_id)){
                    $response['message'] = "fk_state_id is required";
                    $response['code'] = 201;
                }else if(empty($fk_city_id)){
                    $response['message'] = "Mobile No is required";
                    $response['code'] = 201;
                }else if(empty($place_name)){
                    $response['message'] = "place_name is required";
                    $response['code'] = 201;
                }else if(empty($address)){
                    $response['message'] = "User Name is required";
                    $response['code'] = 201;
                }else if(empty($pincode)){
                    $response['message'] = "Pincode is required";
                    $response['code'] = 201;
                }else if(empty($latitude)){
                    $response['message'] = "Latitude is required";
                    $response['code'] = 201;
                }else if(empty($longitude)){
                    $response['message'] = "Longitude is required";
                    $response['code'] = 201;
                }else if(empty($slots)){
                    $response['message'] = "Slots is required";
                    $response['code'] = 201;
                }else if(empty($ext_price)){
                    $response['message'] = "Extension Price is required";
                    $response['code'] = 201;
                }else{
                    $check_place_count = $this->model->CountWhereRecord('tbl_parking_place', array('place_name'=>$place_name,'fk_place_status_id'=>$fk_place_status_id,'id !='=> $id));                  
                    if($check_place_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Place Name Already exist.';
                    }else{
                        $slots_data = $this->model->selectWhereData('tbl_parking_place',array('id'=>$id),array('slots')); 
                        $curl_data = array(
                            'fk_vendor_id'=>$fk_vendor_id,
                            'fk_country_id'=>$fk_country_id,
                            'fk_state_id'=>$fk_state_id,
                            'fk_city_id'=>$fk_city_id,
                            'place_name'=>$place_name,
                            'address'=>$address,
                            'pincode'=>$pincode,
                            'latitude'=>$latitude,
                            'longitude'=>$longitude,
                            'slots'=>$slots,
                            'fk_place_status_id'=>$fk_place_status_id,
                            'fk_parking_price_type'=>$fk_parking_price_type,
                            'ext_price'=>$ext_price,
                            'per_hour_charges'=>$per_hour_charges,
                            'place_count'=>$place_count,
                            'reserved_place_count'=>$reserved_place_count,
                            'total_place_count'=>$total_place_count,
                            'referral_code'=>$referral_code
                        );
                        $this->model->updateData('tbl_parking_place',$curl_data,array('id'=>$id));
                        $previous_vehicle_type = $this->model->selectWhereData('tbl_parking_place_vehicle_type',array('fk_place_id'=>$id),array('GROUP_CONCAT(fk_vehicle_type_id) as fk_vehicle_type_id'),true,'','fk_place_id');
                        $previous_vehicle_type_1 = explode(",",$previous_vehicle_type['fk_vehicle_type_id']);                       
                        $delete_vehicle_info=array_diff($previous_vehicle_type_1,$fk_vehicle_type);
                        $new_vehicle_info=array_diff($fk_vehicle_type,$previous_vehicle_type_1);
                        if (!empty($new_vehicle_info)) {
                           foreach ($new_vehicle_info as $new_vehicle_info_key => $new_vehicle_info_row) {
                               $insert_data = array(
                                    'fk_place_id'=>$id,
                                    'fk_vehicle_type_id'=>$new_vehicle_info_row
                               );
                               $this->model->insertData('tbl_parking_place_vehicle_type',$insert_data);
                                $from_hours_1 = @$from_hours[$new_vehicle_info_row];
                                $to_hours_1 = @$to_hours[$new_vehicle_info_row];
                                $cost_1 = @$price[$new_vehicle_info_row];
                                foreach ($from_hours_1 as $from_hours_1_key => $from_hours_1_row) {
                                     $insert_price_data = array(
                                            'from_hours' =>$from_hours_1_row,
                                            'to_hours' =>@$to_hours_1[$from_hours_1_key],
                                            'cost' =>@$cost_1[$from_hours_1_key],
                                            'fk_place_id'=>$id,
                                            'fk_vehicle_type_id'=>$new_vehicle_info_row
                                    );
                                     $this->model->insertData('tbl_hours_price_slab',$insert_price_data);
                                }                              
                               unset($fk_vehicle_type,$new_vehicle_info_key);
                           }
                        }                     
                        
                        if (!empty($fk_vehicle_type)) {
                           foreach ($fk_vehicle_type as $fk_vehicle_type_key => $fk_vehicle_type_row) {
                                $from_hours_1 = @$from_hours[$fk_vehicle_type_row];
                                $to_hours_1 = @$to_hours[$fk_vehicle_type_row];
                                $cost_1 = @$price[$fk_vehicle_type_row];

                                foreach ($from_hours_1 as $from_hours_1_key => $from_hours_1_row) {
                                    $slab_id = $hour_price_slab_id[$fk_vehicle_type_row][$from_hours_1_key];
                                    if(!empty($slab_id)){
                                        $update_price_data = array(
                                            'from_hours' =>$from_hours_1_row,
                                            'to_hours' =>@$to_hours_1[$from_hours_1_key],
                                            'cost' =>@$cost_1[$from_hours_1_key],
                                        );
                                        $this->model->updateData('tbl_hours_price_slab',$update_price_data,array('id'=>$slab_id));
                                    
                                    } else {
                                        $insert_price_data = array(
                                            'from_hours' =>$from_hours_1_row,
                                            'to_hours' =>@$to_hours_1[$from_hours_1_key],
                                            'cost' =>@$cost_1[$from_hours_1_key],
                                            'fk_place_id'=>$id,
                                            'fk_vehicle_type_id'=>$fk_vehicle_type_row
                                    );
                                     $this->model->insertData('tbl_hours_price_slab',$insert_price_data);
                                    }                                 
                                }                                
                           }                         
                        }
                        if(!empty($delete_vehicle_info)){
                            foreach ($delete_vehicle_info as $delete_vehicle_info_key => $delete_vehicle_info_row) {
                                    $this->model->direct_delete('tbl_parking_place_vehicle_type',array('fk_place_id'=>$id,'fk_vehicle_type_id'=>$delete_vehicle_info_row));
                                    $this->model->direct_delete('tbl_hours_price_slab',array('fk_place_id'=>$id,'fk_vehicle_type_id'=>$delete_vehicle_info_row));

                            }
                        }                       
                        $prefix = $this->model->selectWhereData('tbl_states',array('id'=>$fk_state_id),array('prefix'));
                       
                        if($slots_data['slots'] < $slots){   

                            for ($i=$slots_data['slots']; $i < $slots; $i++) {
                                    $this->load->model('superadmin_model');
                                    $count = $this->superadmin_model->get_count_slot_name($prefix['prefix']);
                                    if($count['total']==0){
                                        $slot_name = $prefix['prefix'] . "-AA000";
                                    }else{
                                        $slot_name = $this->model->selectWhereData('tbl_slot_info',array('del_status'=>1),array('slot_name'),true,array('id','DESC'));
                                        $slot_name = $slot_name['slot_name'];
                                    }  

                                    $this->load->model('superadmin_model');
                                    $slot_name1 = $prefix['prefix'] . "-" . $this->superadmin_model->uniqueSlotName($slot_name);                                   
                                        $display_id = "P-" . ($i + 1);
                                        $insert_slot_info_data=array(
                                            'fk_place_id' =>$id,
                                            'slot_name' =>$slot_name1,
                                            'display_id' =>$display_id
                                        );
                                        $this->model->insertData('tbl_slot_info',$insert_slot_info_data);  
                            }
                        }                        
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Parking Places Updated Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function delete_parking_place_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                        $curl_data = array(
                            'status' =>0,
                        );
                        $this->model->updateData('tbl_parking_place',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Place Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_parking_place_status_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'fk_place_status_id'=>$status,
                );
                $this->model->updateData('tbl_parking_place',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function add_device_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $device_id = $this->input->post('device_id');
                $device_id = json_decode($device_id,true);
                if(empty($device_id)){
                    $response['message'] = "Device Id is required";
                    $response['code'] = 201;
                }else{
                    foreach ($device_id as $device_id_key => $device_id_row) {
                        $check_user_car_count = $this->model->CountWhereRecord('tbl_device', array('device_id'=>$device_id_row,'status'=>1));
                        if($check_user_car_count > 0){
                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['message'] = 'Device Already exist.';                              
                        }else{
                            $curl_data = array(
                                'device_id' =>$device_id_row,
                            );
                            $this->model->insertData('tbl_device',$curl_data);
                            $response['code'] = REST_Controller::HTTP_OK;
                            $response['status'] = true;
                            $response['message'] = 'Device Inserted Successfully';
                        }
                    }                    
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function display_all_device_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $this->load->model('superadmin_model');
                $device_data = $this->superadmin_model->display_all_device_data();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['device_data'] = $device_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function update_device_status_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $status = $this->input->post('status');
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $update_data = array('status' => $status);
                    $this->model->updateData('tbl_device',$update_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Device Status Updated Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function add_bonus_amount_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $bonus_amount = $this->input->post('bonus_amount');
                if(empty($bonus_amount)){
                    $response['message'] = "User Id is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_bonus', array('bonus_amount'=>$bonus_amount,'status'=>1));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Role Already exist.';                      
                    }else{
                        $curl_data = array(
                            'bonus_amount' =>$bonus_amount,
                        );
                        $id = $this->model->insertData('tbl_bonus',$curl_data);
                        $update_status = array('status' => "0");
                    $this->db->where('id!=', $id);
                    $this->db->update('tbl_bonus', $update_status);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Role Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }       
    public function display_all_bonus_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $bonus_data = $this->model->selectWhereData('tbl_bonus',array(),array('*','CONCAT(tbl_bonus.status,",",tbl_bonus.id) AS statusdata'),false,array('id',"desc"));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['bonus_price'] = $bonus_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_bonus_status_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $status = $this->input->post('status');
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $update_data = array('status' => $status);
                    $this->model->updateData('tbl_bonus',$update_data,array('id'=>$id));
                    $update_status = array('status' => "0");
                    $this->db->where('id!=', $id);
                    $this->db->update('tbl_bonus', $update_status);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Bonus Status Updated Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function add_place_status_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $place_status = $this->input->post('place_status');
                if(empty($place_status)){
                    $response['message'] = "Place Status is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_parking_place_status', array('place_status'=>$place_status,'status'=>1));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Place Status Already exist.';                              
                    }else{
                        $curl_data = array(
                            'place_status' =>$place_status,
                        );
                        $this->model->insertData('tbl_parking_place_status',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Place Status Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }       
    public function display_all_place_status_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $place_status = $this->model->selectWhereData('tbl_parking_place_status',array('del_status'=>1),array('*','CONCAT(tbl_parking_place_status.status,",",tbl_parking_place_status.id) AS statusdata'),false,array('id',"desc"));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['place_status'] = $place_status;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_place_status_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'status'=>$status,
                );
                $this->model->updateData('tbl_parking_place_status',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function update_place_status_data_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $place_status = $this->input->post('place_status');
                $id = $this->input->post('id');
                if(empty($place_status)){
                    $response['message'] = "Place Status is required";
                    $response['code'] = 201;
                }else if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_parking_place_status', array('place_status'=>$place_status,'status'=>1,'id !=' =>$id));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Place Status Already exist.';                              
                    }else{
                        $curl_data = array(
                            'place_status' =>$place_status,
                        );
                        $this->model->updateData('tbl_parking_place_status',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Place Status Updated Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }  
    public function add_price_type_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $price_type = $this->input->post('price_type');
                if(empty($price_type)){
                    $response['message'] = "Place Status is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_parking_price_type', array('price_type'=>$price_type,'status'=>1));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Price Type Already exist.';                              
                    }else{
                        $curl_data = array(
                            'price_type' =>$price_type,
                        );
                        $this->model->insertData('tbl_parking_price_type',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Price Type Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }       
    public function display_all_price_type_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $price_type = $this->model->selectWhereData('tbl_parking_price_type',array('del_status'=>1),array('*','CONCAT(tbl_parking_price_type.status,",",tbl_parking_price_type.id) AS statusdata'),false,array('id',"desc"));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['price_type'] = $price_type;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_price_type_status_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'status'=>$status,
                );
                $this->model->updateData('tbl_parking_price_type',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function update_price_type_data_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $price_type = $this->input->post('price_type');
                $id = $this->input->post('id');
                if(empty($price_type)){
                    $response['message'] = "Price Type is required";
                    $response['code'] = 201;
                }else if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_parking_price_type', array('price_type'=>$price_type,'status'=>1,'id !=' =>$id));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Price Type Already exist.';  
                    }else{
                        $curl_data = array(
                            'price_type' =>$price_type,
                        );
                        $this->model->updateData('tbl_parking_price_type',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Price Type Updated Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }  

    public function save_mapped_device_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $edit_id = $this->input->post('edit_id');
            $fk_machine_id=$this->input->post('fk_machine_id');
            $fk_machine_id = json_decode($fk_machine_id);
            $slot_id=$this->input->post('slot_id');
            $slot_id = json_decode($slot_id);
            if (empty($edit_id)) {
                $response['message'] = 'Id is required';
                $response['code'] = 201;
            }else if (empty($fk_machine_id[0])) {
                $response['message'] = 'Machine Id is required';
                $response['code'] = 201;
            }else if (empty($slot_id[0])) {
                $response['message'] = 'Slot Id is required';
                $response['code'] = 201;
            } else {
                foreach($fk_machine_id as $fk_machine_id_key => $fk_machine_id_row){
                        $check_slot_info_count = $this->model->CountWhereRecord('tbl_slot_info', array('fk_machine_id'=>$fk_machine_id_row,'del_status'=>1,'id !='=>$slot_id[$fk_machine_id_key]));
                        if($check_slot_info_count > 0){
                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['message'] = 'Device Id Already exist.';
                        }else{
                            $update_data = array(
                                'fk_machine_id'=>$fk_machine_id_row,
                                'fk_machine_status'=>1
                            );
                            $this->model->updateData('tbl_slot_info',$update_data, array('id'=>$slot_id[$fk_machine_id_key]));
                            $insert_data=array(
                                    'fk_parking_place_id' =>$edit_id,
                                    'fk_device_id' => $fk_machine_id_row
                                );
                                $this->model->insertData('tbl_place_device_mapped',$insert_data);
                                $response['message'] = 'success';
                                $response['code'] = 200;
                                $response['status'] = true;
                        }                    
                    }               
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function update_machine_device_status_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'fk_machine_status'=>$status,
                );
                $this->model->updateData('tbl_slot_info',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function delete_slots_device_status_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            $place_id=$this->input->post('place_id');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'del_status'=>$status,
                );
                $this->model->updateData('tbl_slot_info',$update_data, array('id'=>$id));
                $count = $this->model->CountWhereInRecord('tbl_slot_info',array('del_status'=>1,'fk_place_id'=>$place_id));                
                $update_data=array('slots'=>$count);
                $this->model->updateData('tbl_parking_place',$update_data,array('id'=>$place_id));
                $response['message'] = 'Slot Deleted Successfully';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function dashboard_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $total_user_count = $this->model->CountWhereInRecord('pa_users',array('isActive'=>1,'user_type'=>10));       
            $total_place_count = $this->model->CountWhereInRecord('tbl_parking_place',array('del_status'=>1));       
            $total_booking_count = $this->model->CountWhereInRecord('tbl_booking',array());      
            $total_download_count = $this->model->CountWhereInRecord('pa_users',array('isActive'=>1,'user_type'=>10));      
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
                $response['total_user_count'] = $total_user_count;
                $response['total_place_count'] = $total_place_count;
                $response['total_booking_count'] = $total_booking_count;
                $response['total_download_count'] = $total_download_count;
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function get_allocation_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $place_list = $this->model->selectWhereData('tbl_parking_place',array('status'=>1),array('id','place_name'),false);
                $verifier_list = $this->model->selectWhereData('pa_users',array('isActive'=>1,'user_type'=>3),array('id','firstName','lastName'),false);
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
                $response['place_list'] = $place_list;
                $response['verifier_list'] = $verifier_list;
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    

    public function save_duty_allocation_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $fk_place_id = $this->input->post('fk_place_id');
            $fk_place_id = json_decode($fk_place_id);
            $fk_verifier_id=$this->input->post('fk_verifier_id');
            $fk_verifier_id = json_decode($fk_verifier_id);
            $date=$this->input->post('date');
            $date = json_decode($date);
            if (empty($fk_place_id[0])) {
                $response['message'] = 'Place is required';
                $response['code'] = 201;
            }else if (empty($fk_verifier_id[0])) {
                $response['message'] = 'Verifier Id is required';
                $response['code'] = 201;
            }else if (empty($date[0])) {
                $response['message'] = 'Date is required';
                $response['code'] = 201;
            } else {
                foreach($fk_verifier_id as $fk_verifier_id_key => $fk_verifier_id_row){
                    $insert_data=array(
                        'fk_place_id' =>$fk_place_id[$fk_verifier_id_key],      
                        'fk_verifier_id' => $fk_verifier_id_row,
                        'date'=>$date[$fk_verifier_id_key]
                    );
                    $this->model->insertData('tbl_duty_allocation',$insert_data);
                    $response['message'] = 'success';
                    $response['code'] = 200;
                    $response['status'] = true;   
                }               
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }

    public function display_all_duty_allocation_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $this->load->model('superadmin_model');
                $duty_allocation = $this->superadmin_model->display_all_duty_allocation_data();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['duty_allocation'] = $duty_allocation;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_duty_allocation_details_on_id()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                $duty_allocation = $this->model->selectWhereData('tbl_duty_allocation',array('id'=>$id),array('*'));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['duty_allocation'] = $duty_allocation;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function delete_duty_allocation_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $curl_data = array(
                        'del_status' =>0,
                    );
                    $this->model->updateData('tbl_duty_allocation',$curl_data,array('id'=>$id));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'Duty Allocation Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // ======================== Blogs =========================================
    public function save_blogs_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $title = $this->input->post('title');
                $description = $this->input->post('description');
                $image = $this->input->post('image');
                if (empty($title)) {
                    $response['message'] = "Title is required";
                    $response['code'] = 201;
                }else if (empty($description)) {
                    $response['message'] = "Description is required";
                    $response['code'] = 201;
                } else {
                         $check_blogs_count = $this->model->CountWhereRecord('tbl_blogs', array('title'=>$title,'del_status'=>1));
                        if($check_blogs_count > 0){
                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['message'] = 'Blogs Already Exist';
                        } else {
                            $curl_data = array(
                                'title' => $title, 
                                'description' => $description, 
                                'image' => $image
                            );
                            $curl = $this->model->insertData('tbl_blogs', $curl_data);
                            $response['message'] = 'Blogs Added Successfully';
                            $response['code'] = 200;
                            $response['status'] = true;
                        }
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function display_all_blogs_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $blogs_data = $this->model->selectWhereData('tbl_blogs',array('del_status'=>1),array('*'),false);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['blogs_data'] = $blogs_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
     public function update_blogs_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                $title = $this->input->post('title');
                $description = $this->input->post('description');
                $image = $this->input->post('image');
                if (empty($title)) {
                    $response['message'] = "Title is required";
                    $response['code'] = 201;
                }else if (empty($description)) {
                    $response['message'] = "Description is required";
                    $response['code'] = 201;
                } else if (empty($id)) {
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                } else {
                         $check_blogs_count = $this->model->CountWhereRecord('tbl_blogs', array('title'=>$title,'del_status'=>1,'id !=' =>$id));
                        if($check_blogs_count > 0){
                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['message'] = 'Blogs Already Exist';
                        } else {
                            $curl_data = array(
                                'title' => $title, 
                                'description' => $description, 
                                'image' => $image
                            );
                            $curl = $this->model->updateData('tbl_blogs', $curl_data,array('id'=>$id));
                            $response['message'] = 'Blogs Added Successfully';
                            $response['code'] = 200;
                            $response['status'] = true;
                        }
                }
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function delete_blogs_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $curl_data = array(
                        'del_status' =>0,
                    );
                    $this->model->updateData('tbl_blogs',$curl_data,array('id'=>$id));
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'Blogs Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    // ================================= Vechile Type =====================================

    public function add_vehicle_type_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $vehicle_type = $this->input->post('vehicle_type');
                if(empty($vehicle_type)){
                    $response['message'] = "Place Status is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_vehicle_type', array('vehicle_type'=>$vehicle_type,'status'=>1));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Vehicle Type Already exist.';                
                    }else{
                        $curl_data = array(
                            'vehicle_type' =>$vehicle_type,
                        );
                        $this->model->insertData('tbl_vehicle_type',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Price Type Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }       
    public function display_all_vehicle_type_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $vehicle_type = $this->model->selectWhereData('tbl_vehicle_type',array('del_status'=>1),array('*','CONCAT(tbl_vehicle_type.status,",",tbl_vehicle_type.id) AS statusdata'),false,array('id',"desc"));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['vehicle_type'] = $vehicle_type;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function update_vehicle_type_status_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'status'=>$status,
                );
                $this->model->updateData('tbl_vehicle_type',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function update_vehicle_type_data_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $vehicle_type = $this->input->post('vehicle_type');
                $id = $this->input->post('id');
                if(empty($vehicle_type)){
                    $response['message'] = "vehicle Type is required";
                    $response['code'] = 201;
                }else if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_vehicle_type', array('vehicle_type'=>$vehicle_type,'status'=>1,'id !=' =>$id));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'vehicle Type Already exist.';  
                    }else{
                        $curl_data = array(
                            'vehicle_type' =>$vehicle_type,
                        );
                        $this->model->updateData('tbl_vehicle_type',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Vehicle Type Updated Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    } 

    public function delete_vehicle_type_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                        $curl_data = array(
                            'del_status' =>0,
                        );
                        $this->model->updateData('tbl_vehicle_type',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Vehicle Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_vehicle_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                        $vehicle_data = $this->model->selectWhereData('tbl_vehicle_type',array('id'=>$id),array('id','vehicle_type'));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'success';
                        $response['vehicle_data'] = $vehicle_data;
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
     // ================================= POS Device Mapped ==============================
    public function get_pos_map_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $place_list = $this->model->selectWhereData('tbl_parking_place',array('status'=>1),array('id','place_name'),false);
                $device_id = $this->model->selectWhereData('tbl_pos_device',array('status'=>1,'del_status'=>1),array('id','pos_device_id'),false);
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
                $response['place_list'] = $place_list;
                $response['device_id'] = $device_id;
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
     public function add_pos_device_mapped_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $fk_place_id = $this->input->post('fk_place_id');
                $device_id = $this->input->post('device_id');
                if(empty($fk_place_id)){
                    $response['message'] = "Place Status is required";
                    $response['code'] = 201;
                }else if(empty($device_id)){
                    $response['message'] = "Device id is required";
                    $response['code'] = 201;
                }else{
                    $check_pos_device_map_count = $this->model->CountWhereRecord('tbl_pos_device_map', array('fk_place_id'=>$fk_place_id,'device_id'=>$device_id,'status'=>1));
                    if($check_pos_device_map_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Device Already exist.';
                    }else{
                        $curl_data = array(
                            'fk_place_id' => $fk_place_id,
                            'device_id' => $device_id,
                        );
                        $this->model->insertData('tbl_pos_device_map',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'POS Device Mapped Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function display_all_pos_device_map_data_get()
      {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                   $this->load->model('superadmin_model');
                    $pos_device_map_data = $this->superadmin_model->display_all_pos_device_map_data();
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                    $response['pos_device_map_data'] = $pos_device_map_data;
            } else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
      }  
      public function update_pos_device_map_post()
      {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                $fk_place_id = $this->input->post('fk_place_id');
                $device_id = $this->input->post('device_id');
                $id = $this->input->post('id');
                if(empty($fk_place_id)){
                    $response['message'] = "Place Status is required";
                    $response['code'] = 201;
                }else if(empty($device_id)){
                    $response['message'] = "Device id is required";
                    $response['code'] = 201;
                }else if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $check_pos_device_map_count = $this->model->CountWhereRecord('tbl_pos_device_map', array('fk_place_id'=>$fk_place_id,'device_id'=>$device_id,'id !='=>$id,'status'=>1));
                    if($check_pos_device_map_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Device Already exist.';
                    }else{
                        $curl_data = array(
                            'fk_place_id' => $fk_place_id,
                            'device_id' => $device_id,
                        );
                        $this->model->updateData('tbl_pos_device_map',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'POS Device Updated Successfully';
                    }
                }
            }else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
      }

      public function delete_pos_device_map_post()
      {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if ($validate) {
                    $id = $this->input->post('id');
                    if(empty($id)){
                        $response['message'] = "Id is required";
                        $response['code'] = 201;
                    }else{
                            $curl_data = array(
                                'del_status' =>0,
                            );
                            $this->model->updateData('tbl_pos_device_map',$curl_data,array('id'=>$id));
                            $response['code'] = REST_Controller::HTTP_OK;
                            $response['status'] = true;
                            $response['message'] = 'POS Device Deleted Successfully';
                    }
            }else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
      }

      public function update_pos_device_map_status_post()
      {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'status'=>$status,
                );
                $this->model->updateData('tbl_pos_device_map',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
      }

      public function get_all_pos_verifier_get()
      {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if($validate){
                    $place_list = $this->model->selectWhereData('tbl_parking_place',array('status'=>1),array('id','place_name'),false);
                    $pos_verifier_list = $this->model->selectWhereData('pa_users',array('isActive'=>1,'user_type'=>14),array('id','firstName','lastName'),false);
                    $response['message'] = 'success';
                    $response['code'] = 200;
                    $response['status'] = true;
                    $response['place_list'] = $place_list;
                    $response['pos_verifier_list'] = $pos_verifier_list;
            } else {
                $response['message'] = 'Invalid Request';
                $response['code'] = 204;
            }
            echo json_encode($response);
      }

      public function save_pos_verifier_duty_allocation_post()
      {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $fk_place_id = $this->input->post('fk_place_id');
            $fk_place_id = json_decode($fk_place_id);

            $fk_pos_verifier_id=$this->input->post('fk_pos_verifier_id');
            $fk_pos_verifier_id = json_decode($fk_pos_verifier_id);
            $date=$this->input->post('date');
            $date = json_decode($date);
            if (empty($fk_place_id[0])) {
                $response['message'] = 'Place is required';
                $response['code'] = 201;
            }else if (empty($fk_pos_verifier_id[0])) {
                $response['message'] = 'Verifier Id is required';
                $response['code'] = 201;
            }else if (empty($date[0])) {
                $response['message'] = 'Date is required';
                $response['code'] = 201;
            } else {
                foreach($fk_pos_verifier_id as $fk_pos_verifier_id_key => $fk_pos_verifier_id_row){
                         $device_id = $this->model->selectWhereData('tbl_pos_device_map',array('fk_place_id'=>$fk_place_id[$fk_pos_verifier_id_key]),array('id'));
                        $insert_data=array(
                            'fk_place_id' =>$fk_place_id[$fk_pos_verifier_id_key],      
                            'fk_pos_verifier_id' => $fk_pos_verifier_id_row,
                            'date'=>$date[$fk_pos_verifier_id_key],
                            'fk_device_id'=>$device_id['id']
                        );
                        $this->model->insertData('tbl_pos_duty_allocation',$insert_data);
                        $response['message'] = 'success';
                        $response['code'] = 200;
                        $response['status'] = true;   
                }                              
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
      }

    public function display_all_pos_verifier_duty_allocation_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $this->load->model('superadmin_model');
                $pos_duty_allocation = $this->superadmin_model->display_all_pos_verifier_duty_allocation_data();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['pos_duty_allocation'] = $pos_duty_allocation;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function delete_pos_duty_allocation_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                        $curl_data = array(
                            'del_status' =>0,
                        );
                        $this->model->updateData('tbl_pos_duty_allocation',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'POS Duty Allocation Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

    public function add_pos_device_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $device_id = $this->input->post('device_id');
                $device_id = json_decode($device_id,true);
                if(empty($device_id)){
                    $response['message'] = "Device Id is required";
                    $response['code'] = 201;
                }else{
                    foreach ($device_id as $device_id_key => $device_id_row) {
                        $check_user_car_count = $this->model->CountWhereRecord('tbl_pos_device', array('pos_device_id'=>$device_id_row,'status'=>1));
                        if($check_user_car_count > 0){
                            $response['code'] = 201;
                            $response['status'] = false;
                            $response['message'] = 'POS Device Already exist.';                     
                        }else{
                            $curl_data = array(
                                'pos_device_id' =>$device_id_row,
                            );
                            $this->model->insertData('tbl_pos_device',$curl_data);
                            $response['code'] = REST_Controller::HTTP_OK;
                            $response['status'] = true;
                            $response['message'] = 'POS Device Inserted Successfully';
                        }
                    }
                    
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
     public function display_all_pos_device_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $this->load->model('superadmin_model');
                $device_data = $this->model->selectWhereData('tbl_pos_device',array(),array('*','CONCAT(tbl_pos_device.status,",",tbl_pos_device.id) AS statusdata'),false,array('id',"desc"));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['device_data'] = $device_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function update_pos_device_status_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'status'=>$status,
                );
                $this->model->updateData('tbl_pos_device',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }

    public function user_terms_n_condition_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
         
                $user_terms_n_condition = $this->model->selectWhereData('tbl_terms_condition',array('terms_type'=>1),array('*'));
                 $user_privacy_n_policy = $this->model->selectWhereData('tbl_privacy_policy',array('privacy_type'=>1),array('*'));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
                $response['user_terms_n_condition'] = $user_terms_n_condition;
                $response['user_privacy_n_policy'] = $user_privacy_n_policy;
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function verifier_terms_n_condition_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $verifier_terms_n_condition = $this->model->selectWhereData('tbl_terms_condition',array('terms_type'=>2),array('*'));
            $response['message'] = 'success';
            $response['code'] = 200;
            $response['status'] = true;
            $response['verifier_terms_n_condition'] = $verifier_terms_n_condition;
           
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function vendor_terms_n_condition_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $vendor_terms_n_condition = $this->model->selectWhereData('tbl_terms_condition',array('terms_type'=>3),array('*'));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
                $response['vendor_terms_n_condition'] = $vendor_terms_n_condition;
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function update_terms_n_condition_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $id = $this->input->post('id');
                $terms_condition = $this->input->post('terms_condition');
                if(empty($id)){
                    $response['message']= "Id is required";
                    $response['code']=201;
                }else if(empty($terms_condition)){
                    $response['message']= "Terms & Condition is required";
                    $response['code']=201;
                }else{
                    $curl_data=array(
                        'terms_condition' => $terms_condition
                    );
                    $this->model->updateData('tbl_terms_condition',$curl_data,array('id'=>$id));
                    $response['status'] = true;
                    $response['code'] = 200;
                    $response['message'] = 'Terms & Condition Updated Successfully';
                }
                
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }

    public function display_all_suggested_parking_place_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $this->load->model('superadmin_model');
                $suggested_place = $this->model->selectWhereData('tbl_place_suggestion',array(),array('*'),false,array('id',"desc"));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['suggested_place'] = $suggested_place;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function display_all_pos_booking_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $this->load->model('pos_model');
                $pos_booking_data = $this->pos_model->display_all_pos_booking_data();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['pos_booking_data'] = $pos_booking_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function get_customer_support_details_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $issue_type = $this->model->selectWhereData('tbl_issue_type',array('status'=>1),array('id','issue_type'),false);
                $user_list = $this->model->selectWhereData('pa_users',array('isActive'=>1,'del_status'=>1,'user_type'=>10),array('id','firstName','lastName'),false);
                $booking_id_list = $this->model->selectWhereData('tbl_booking',array(),array('id','booking_id'),false);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['issue_type'] = $issue_type;
                $response['user_list'] = $user_list;
                $response['booking_id_list'] = $booking_id_list;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function add_complaint_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $user_type = $this->input->post('user_type');
                $description = $this->input->post('description');
                $fk_booking_id = $this->input->post('fk_booking_id');
                $contact_no = $this->input->post('contact_no');
                $fk_user_id = $this->input->post('fk_user_id');
                $fk_issue_type_id = $this->input->post('fk_issue_type_id');
                if(empty($user_type)){
                    $response['message']="User Type is required";
                    $response['code']= 201;
                }else if(empty($description)){
                    $response['message']="Description is required";
                    $response['code']= 201;
                }else if(empty($contact_no)){
                    $response['message']="Contact No is required";
                    $response['code']= 201;
                }else if(empty($fk_issue_type_id)){
                    $response['message']="Issue Type is required";
                    $response['code']= 201;
                }else if(empty($fk_user_id)){
                    $response['message']="User is required";
                    $response['code']= 201;
                } else{
                    if($user_type==1){
                        $place_id = $this->model->selectWhereData('tbl_booking',array('id'=>$fk_booking_id),array('fk_place_id'));
                        $curl_data = array(
                            'fk_user_id'=>$fk_user_id,
                            'fk_place_id'=>$place_id['fk_place_id'],
                            'fk_booking_id'=>$fk_booking_id,
                            'topic'=>$fk_issue_type_id,
                            'description'=>$description,
                            'contact_no'=>$contact_no,
                            'source_type'=>2
                        );
                        $this->model->insertData('tbl_user_complaint',$curl_data);
                    }else{
                        $curl_data = array(
                            'user_name'=>$fk_user_id,
                            'fk_issue_type_id'=>$fk_issue_type_id,
                            'description'=>$description,
                            'contact_no'=>$contact_no,
                            'source_type'=>2
                        );
                        $this->model->insertData('tbl_complaint',$curl_data);
                    }
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'success';
                }                
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function display_all_register_user_complaint_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $this->load->model('superadmin_model');
                $register_user_complaint_data = $this->superadmin_model->display_all_register_user_complaint();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['register_user_complaint_data'] = $register_user_complaint_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function display_all_unregister_user_complaint_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $this->load->model('superadmin_model');
                $unregister_user_complaint_data = $this->superadmin_model->display_all_unregister_user_complaint();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['unregister_user_complaint_data'] = $unregister_user_complaint_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }
    public function update_register_user_complaint_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $action_type = $this->input->post('action_type');
            $customer_care_remark = $this->input->post('customer_care_remark');
            $edit_id = $this->input->post('edit_id');
            $complaint_status = $this->input->post('complaint_status');
            if (empty($action_type)) {
                $response['message'] = "Action Type is required";
                $response['code']=201;
            }else if (empty($customer_care_remark)) {
                $response['message'] = "Customer Care is required";
                $response['code']=201;
            }else if (empty($complaint_status)) {
                $response['message'] = "Complaint Status is required";
                $response['code']=201;
            }else if (empty($edit_id)) {
                $response['message'] = "Id is required";
                $response['code']=201;
            }else{
                $curl_data= array(
                    'action_type' =>$action_type,
                    'customer_care_remark' =>$customer_care_remark,
                    'status' =>$complaint_status,
                );
                $this->model->updateData('tbl_user_complaint',$curl_data,array('id'=>$edit_id));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
            }
            
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);   
    }

    public function update_un_register_user_complaint_details_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $action_type = $this->input->post('action_type');
            $customer_care_remark = $this->input->post('customer_care_remark');
            $edit_id = $this->input->post('edit_id');
            $complaint_status = $this->input->post('complaint_status');
            if (empty($action_type)) {
                $response['message'] = "Action Type is required";
                $response['code']=201;
            }else if (empty($customer_care_remark)) {
                $response['message'] = "Customer Care is required";
                $response['code']=201;
            }else if (empty($complaint_status)) {
                $response['message'] = "Complaint Status is required";
                $response['code']=201;
            }else if (empty($edit_id)) {
                $response['message'] = "Id is required";
                $response['code']=201;
            }else{
                $curl_data= array(
                    'action_type' =>$action_type,
                    'customer_care_remark' =>$customer_care_remark,
                    'status' =>$complaint_status,
                );
                $this->model->updateData('tbl_complaint',$curl_data,array('id'=>$edit_id));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
            }            
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);  
    }

     public function add_referral_code_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $referral_code = $this->input->post('referral_code');
                if(empty($referral_code)){
                    $response['message'] = "Place Status is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_referral_code', array('referral_code'=>$referral_code,'status'=>1));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Referral Code Already Exist......!';                              
                    }else{
                        $curl_data = array(
                            'referral_code' =>$referral_code,
                        );
                        $this->model->insertData('tbl_referral_code',$curl_data);
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Place Status Inserted Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }  
    public function display_all_referral_code_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $referral_code_data = $this->model->selectWhereData('tbl_referral_code',array('del_status'=>1),array('*','CONCAT(tbl_referral_code.status,",",tbl_referral_code.id) AS statusdata'),false,array('id',"desc"));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['referral_code_data'] = $referral_code_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
     public function update_referral_code_status_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
            $id = $this->input->post('id');
            $status=$this->input->post('status');
            if (empty($id)) {
                $response['message'] = 'id is required';
                $response['code'] = 201;
            } else {
                $update_data = array(
                    'status'=>$status,
                );
                $this->model->updateData('tbl_referral_code',$update_data, array('id'=>$id));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
            }
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function update_referral_code_data_post()
    {
         $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $referral_code = $this->input->post('referral_code');
                $id = $this->input->post('id');
                if(empty($referral_code)){
                    $response['message'] = "Place Status is required";
                    $response['code'] = 201;
                }else if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                    $check_user_car_count = $this->model->CountWhereRecord('tbl_referral_code', array('referral_code'=>$referral_code,'status'=>1,'id !=' =>$id));
                    if($check_user_car_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Referral Code Already exist.';                              
                    }else{
                        $curl_data = array(
                            'referral_code' =>$referral_code,
                        );
                        $this->model->updateData('tbl_referral_code',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Referral Code Updated Successfully';
                    }
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    } 
    public function delete_referral_code_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                if(empty($id)){
                    $response['message'] = "Id is required";
                    $response['code'] = 201;
                }else{
                        $curl_data = array(
                            'del_status' =>0,
                        );
                        $this->model->updateData('tbl_referral_code',$curl_data,array('id'=>$id));
                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'Referral Code Deleted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    } 
    public function user_privacy_n_policy_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $user_privacy_n_policy = $this->model->selectWhereData('tbl_privacy_policy',array('privacy_type'=>1),array('*'));
                $response['message'] = 'success';
                $response['code'] = 200;
                $response['status'] = true;
                $response['user_privacy_n_policy'] = $user_privacy_n_policy;
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    public function update_privacy_n_policy_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if($validate){
                $id = $this->input->post('id');
                $privacy_policy = $this->input->post('privacy_policy');
                if(empty($id)){
                    $response['message']= "Id is required";
                    $response['code']=201;
                }else if(empty($privacy_policy)){
                    $response['message']= "Terms & Condition is required";
                    $response['code']=201;
                }else{
                    $curl_data=array(
                        'privacy_policy' => $privacy_policy
                    );
                    $this->model->updateData('tbl_privacy_policy',$curl_data,array('id'=>$id));
                    $response['status'] = true;
                    $response['code'] = 200;
                    $response['message'] = 'Privacy & Policy Updated Successfully';
                }
                
        } else {
            $response['message'] = 'Invalid Request';
            $response['code'] = 204;
        }
        echo json_encode($response);
    }
    // ============================== Vendor Mapped Place===================================

    public function get_vendor_map_place_data_get()
    {
            $response = array('code' => - 1, 'status' => false, 'message' => '');
            $validate = validateToken();
            if($validate){
                    $place_list = $this->model->selectWhereData('tbl_parking_place',array('status'=>1),array('id','place_name'),false);
                    $vendor_list = $this->model->selectWhereData('pa_users',array('isActive'=>1,'user_type'=>5),array('id','firstName','lastName'),false);
                    $response['message'] = 'success';
                    $response['code'] = 200;
                    $response['status'] = true;
                    $response['place_list'] = $place_list;
                    $response['vendor_list'] = $vendor_list;
            } else {
                $response['message'] = 'Invalid Request';
                $response['code'] = 204;
            }
            echo json_encode($response);
    }
    public function save_vendor_map_place_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $fk_vendor_id = $this->input->post('fk_vendor_id');
                $fk_place_id = json_decode($this->input->post('fk_place_id'));
                if(empty($fk_vendor_id)){
                    $response['message'] = "Vendor Id is required";
                    $response['code'] = 201;
                }else if(empty($fk_place_id)){
                    $response['message'] = "Vendor Id is required";
                    $response['code'] = 201;
                }else{
                    $check_vendor_count = $this->model->CountWhereRecord('tbl_vendor', array('vendor_id'=>$fk_vendor_id));
                    if($check_vendor_count > 0){
                        $response['code'] = 201;
                        $response['status'] = false;
                        $response['message'] = 'Vendor Already Exist......!';                             
                    }else{
                        $insert_data = array(
                            'vendor_id'=>$fk_vendor_id,
                        );
                        $last_inserted_id = $this->model->insertData('tbl_vendor',$insert_data);

                        foreach ($fk_place_id as $fk_place_id_key => $fk_place_id_row) {
                                $check_mapped_vendor_count = $this->model->CountWhereRecord('tbl_vendor_map_place', array('fk_place_id'=>$fk_place_id_row,'status'=>1));
                                if($check_mapped_vendor_count > 0){
                                    $response['code'] = 201;
                                    $response['status'] = false;
                                    $response['message'] = 'Mapping Already Exist......!';
                                }else{
                                    $curl_data = array(
                                        'fk_vendor_id' =>$last_inserted_id,
                                        'fk_place_id' =>$fk_place_id_row,
                                    );
                                      $this->model->insertData('tbl_vendor_map_place',$curl_data);            
                                }
                          }
                    }                  
                    $response['code'] = REST_Controller::HTTP_OK;
                    $response['status'] = true;
                    $response['message'] = 'Place Status Inserted Successfully';
                }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function display_all_vendor_map_place_data_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $this->load->model('superadmin_model');
                $vendor_map_data = $this->superadmin_model->display_all_vendor_map_place_data();
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['vendor_map_data'] = $vendor_map_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    public function get_vendor_map_place_data_on_id_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $id = $this->input->post('id');
                $this->load->model('superadmin_model');
                $vendor_map_data = $this->superadmin_model->get_vendor_map_place_data_on_id($id);
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['vendor_map_data'] = $vendor_map_data;
        } else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }
    
    
}