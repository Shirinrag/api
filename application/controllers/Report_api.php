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
                   
                   $response = array('code' => - 1, 'status' => false, 'message' => '');
                $this->load->model('user_report_model');
                $user_details = $this->user_report_model->get_datatables($from_date,$to_date);
                $count = $this->user_report_model->count_all($from_date,$to_date);
                $count_filtered = $this->user_report_model->count_filtered($from_date,$to_date);             

                $response['code'] = REST_Controller::HTTP_OK;
                $response['status'] = true;
                $response['message'] = 'success';
                $response['user_details'] = $user_details;
                $response['count'] = $count;
                $response['count_filtered'] = $count_filtered;
            } else {
                $response['code'] = REST_Controller::HTTP_UNAUTHORIZED;
                $response['message'] = 'Unauthorised';
            }
            echo json_encode($response);
                    
    }
}