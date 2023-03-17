<?php
defined('BASEPATH') OR exit('No direct script access allowed');
ini_set("memory_limit", "-1");
require APPPATH . '/libraries/REST_Controller.php';

class Report_api extends REST_Controller {

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

    public function user_report_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $this->load->model('report_model');
                $user_details = $this->report_model->display_all_user_data_report($from_date,$to_date);  
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['user_details'] = $user_details;
                // $response['count'] = $count;
                // $response['count_filtered'] = $count_filtered;
            } else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);                   
    }
    public function bonus_report_data_post()
    {
       $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $this->load->model('report_model');
                $bonus_details = $this->report_model->display_all_bonus_data_report($from_date,$to_date);  
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['bonus_details'] = $bonus_details;
            } else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);  
    }
    public function user_wallet_report_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $this->load->model('report_model');
                $user_wallet_details = $this->report_model->display_all_user_wallet_data_report($from_date,$to_date);  
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['user_wallet_details'] = $user_wallet_details;
            } else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);  
    }
    public function get_all_data_report_get()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $place_details = $this->model->selectWhereData('tbl_parking_place',array('status'=>1,'del_status'=>1),array('id','place_name'),false);
                $verifier_details = $this->model->selectWhereData('pa_users',array('isActive'=>1,'user_type'=>3),array('id','firstName','lastName'),false);

                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['place_details'] = $place_details;
                $response['verifier_details'] = $verifier_details;
               
            } else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response); 
    }
    public function verifier_attendance_report_data_post()
    {
        $response = array('code' => - 1, 'status' => false, 'message' => '');
        $validate = validateToken();
        if ($validate) {
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
                $fk_place_id = $this->input->post('fk_place_id');
                $fk_verifier_id = $this->input->post('fk_verifier_id');
                $this->load->model('report_model');
                $verifier_attendance_details = $this->report_model->display_all_verifier_attendance_data_report($from_date,$to_date,$fk_verifier_id);  
                foreach($verifier_attendance_details as $verifier_attendance_details_key => $verifier_attendance_details_row){
                    $duty_allocation =$this->report_model->duty_allocation_data($verifier_attendance_details_row['fk_verifier_id'],$fk_place_id); 
                    $verifier_attendance_details[$verifier_attendance_details_key]['place_name'] = $duty_allocation['place_name'];
                }
                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['verifier_attendance_details'] = $verifier_attendance_details;
            } else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);  
    }
}