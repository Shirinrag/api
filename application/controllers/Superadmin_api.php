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
                        $curl_data = array(
                            'firstName' =>$first_name,
                            'lastName' =>$last_name,
                            'email' =>$email,
                            'phoneNo' =>$phone_no,
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
    public function display_all_admin_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
            $admin_data = $this->crud_model->get_datatables('pa_users', array('id', 'firstName', 'lastName',"email","mobile_no","username"), array('isActive' => 1), array(null, 'id',"firstName","lastName","email","mobile_no","username"), array('id' => 'DESC'));
                $count = $this->crud_model->count_all('pa_users', array('id', 'firstName', 'lastName',"email","mobile_no","username"), array('isActive' => 1), array(null, 'id',"firstName","lastName","email","mobile_no","username"), array('id' => 'DESC'));
                $count_filtered = $this->crud_model->count_filtered('pa_users', array('id', 'firstName', 'lastName',"email","mobile_no","username"), array('del_status' => 1), array(null, 'id',"firstName","lastName","email","mobile_no","username"), array('id' => 'DESC'));
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['admin_data'] = $admin_data;
                $response['count'] = $count;
                $response['count_filtered'] = $count_filtered;
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
                // $email = $this->input->post('email');
                $mobile_no = $this->input->post('mobile_no');
                // $password = $this->input->post('password');
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
                            'phoneNo' =>$phone_no,
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
                            'phoneNo' =>$phone_no,
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
    public function display_all_user_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
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
                // $email = $this->input->post('email');
                $mobile_no = $this->input->post('mobile_no');
                // $password = $this->input->post('password');
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
                            'phoneNo' =>$phone_no,
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

    public function booking_history_data_post($value='')
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $user_id = $this->input->post('user_id');
                if(empty($user_id)){
                    $response['message'] = "User Id is required";
                    $response['code'] = 201;
                }else{
                    $this->load->model('superadmin_model');
                    $booking_history = $this->superadmin_model->booking_history_data();

                        $response['code'] = REST_Controller::HTTP_OK;
                        $response['status'] = true;
                        $response['message'] = 'success';
                        $response['booking_history_data'] = $booking_history;
                    }
        }else {
            $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
            $response['message'] = 'Unauthorised';
        }
        echo json_encode($response);
    }

}