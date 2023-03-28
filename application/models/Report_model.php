<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Report_model extends CI_Model {
 
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function display_all_user_data_report($from_date="",$to_date="")
    {
        $this->db->select('pa_users.*,tbl_user_car_details.car_number,CONCAT(pa_users.isActive,",",pa_users.id) AS statusdata,tbl_parking_place.referral_code');
        $this->db->from('pa_users');
        $this->db->join('tbl_user_car_details','tbl_user_car_details.fk_user_id=pa_users.id','left');
        $this->db->join('tbl_parking_place','pa_users.referal_code=tbl_parking_place.referral_code','left');
        $this->db->where('pa_users.user_type',10);
        $this->db->where('pa_users.del_status',1);
        if (!empty($from_date)) {
               $from_date =date('Y-m-d', strtotime($from_date));
               $from_date = $from_date ." 00:00:00";
               $this->db->where('pa_users.created_at >=',$from_date);
        }
       if (!empty($to_date)) {
            $to_date =date('Y-m-d', strtotime($to_date));
            $to_date = $to_date ." 23:59:00";
            $this->db->where('pa_users.created_at <=',$to_date);
        }
        $this->db->order_by('pa_users.id','DESC');
        $this->db->group_by('pa_users.id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function display_all_bonus_data_report($from_date="",$to_date="")
    {
        $this->db->select('tbl_user_wallet_history.total_amount,tbl_user_wallet_history.created_at,pa_users.firstName,pa_users.lastName');
        $this->db->from('tbl_user_wallet_history');
        $this->db->join('pa_users','tbl_user_wallet_history.fk_user_id=pa_users.id','left');
        $this->db->where('tbl_user_wallet_history.fk_payment_type_id',1);
        if (!empty($from_date)) {
               $from_date =date('Y-m-d', strtotime($from_date));
               $from_date = $from_date ." 00:00:00";
               $this->db->where('tbl_user_wallet_history.created_at >=',$from_date);
        }
       if (!empty($to_date)) {
            $to_date =date('Y-m-d', strtotime($to_date));
            $to_date = $to_date ." 23:59:00";
            $this->db->where('tbl_user_wallet_history.created_at <=',$to_date);
        }
        $this->db->order_by('tbl_user_wallet_history.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function display_all_user_wallet_data_report($from_date="",$to_date="")
    {
        $this->db->select('tbl_user_wallet.amount,tbl_user_wallet.created_at,pa_users.firstName,pa_users.lastName');
        $this->db->from('tbl_user_wallet');
        $this->db->join('pa_users','tbl_user_wallet.fk_user_id=pa_users.id','left');
        if (!empty($from_date)) {
               $from_date =date('Y-m-d', strtotime($from_date));
               $from_date = $from_date ." 00:00:00";
               $this->db->where('tbl_user_wallet.created_at >=',$from_date);
        }
       if (!empty($to_date)) {
            $to_date =date('Y-m-d', strtotime($to_date));
            $to_date = $to_date ." 23:59:00";
            $this->db->where('tbl_user_wallet.created_at <=',$to_date);
        }
        $this->db->order_by('tbl_user_wallet.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function display_all_verifier_attendance_data_report($from_date="",$to_date="",$fk_verifier_id="")
    {
        // ,tbl_parking_place.place_name,tbl_duty_allocation.fk_place_id
        $this->db->select('tbl_verifier_logged_in.*,pa_users.firstName,pa_users.lastName');
        $this->db->from('tbl_verifier_logged_in');
        $this->db->join('pa_users','tbl_verifier_logged_in.fk_verifier_id=pa_users.id','left');
        // $this->db->join('tbl_duty_allocation','tbl_duty_allocation.fk_verifier_id=tbl_verifier_logged_in.fk_verifier_id','left');
        // $this->db->join('tbl_parking_place','tbl_duty_allocation.fk_place_id=tbl_parking_place.id','left');
        if (!empty($from_date)) {
               $from_date =date('Y-m-d', strtotime($from_date));
               $from_date = $from_date ." 00:00:00";
               $this->db->where('tbl_verifier_logged_in.created_at >=',$from_date);
        }
       if (!empty($to_date)) {
            $to_date =date('Y-m-d', strtotime($to_date));
            $to_date = $to_date ." 23:59:00";
            $this->db->where('tbl_verifier_logged_in.created_at <=',$to_date);
        }
        if(!empty($fk_verifier_id)){
            $this->db->where('tbl_verifier_logged_in.fk_verifier_id <=',$fk_verifier_id);
        }
        $this->db->order_by('tbl_verifier_logged_in.id','DESC');
        
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function duty_allocation_data($fk_verifier_id="",$fk_place_id=""){
        $this->db->select('tbl_duty_allocation.fk_place_id,tbl_parking_place.place_name');
        $this->db->from('tbl_duty_allocation');
        $this->db->join('tbl_parking_place','tbl_duty_allocation.fk_place_id=tbl_parking_place.id','left');
        $this->db->where('tbl_duty_allocation.fk_verifier_id',$fk_verifier_id);
        if(!empty($fk_place_id)){
            $this->db->where('tbl_parking_place.id',$tbl_parking_place);
        }
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }
    public function display_all_user_transcation_report_data($from_date="",$to_date="")
    {
        $this->db->select('tbl_transcation.*,pa_users.firstName,pa_users.lastName,pa_users.phoneNo,pa_users.email');
        $this->db->from('tbl_transcation');
        $this->db->join('pa_users','tbl_transcation.fk_user_id=pa_users.id','left');
        $this->db->where('tbl_transcation.payment_id !=',"");
        if (!empty($from_date)) {
               $from_date =date('Y-m-d', strtotime($from_date));
               $from_date = $from_date ." 00:00:00";
               $this->db->where('tbl_transcation.created_at >=',$from_date);
        }
        if (!empty($to_date)) {
            $to_date =date('Y-m-d', strtotime($to_date));
            $to_date = $to_date ." 23:59:00";
            $this->db->where('tbl_transcation.created_at <=',$to_date);
        }
        $this->db->order_by('tbl_transcation.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function display_all_booking_report_data($from_date="",$to_date="")
    {
        $this->db->select('tbl_booking.*,pa_users.firstName,pa_users.lastName,tbl_user_car_details.car_number,tbl_parking_place.place_name,tbl_parking_place.address,tbl_parking_place.pincode,tbl_states.name as state_name,tbl_cities.name as city_name,tbl_parking_place.latitude,tbl_parking_place.longitude,tbl_slot_info.display_id');
        $this->db->from('tbl_booking');
        $this->db->join('pa_users','tbl_booking.fk_user_id=pa_users.id','left');
        $this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
        $this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
        $this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
        $this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
        $this->db->join('tbl_slot_info','tbl_booking.fk_slot_id=tbl_slot_info.id','left');
         if (!empty($from_date)) {
               $from_date =date('Y-m-d', strtotime($from_date));
               $from_date = $from_date ." 00:00:00";
               $this->db->where('tbl_booking.created_at >=',$from_date);
        }
        if (!empty($to_date)) {
            $to_date =date('Y-m-d', strtotime($to_date));
            $to_date = $to_date ." 23:59:00";
            $this->db->where('tbl_booking.created_at <=',$to_date);
        }
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
}