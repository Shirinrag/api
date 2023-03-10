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
        $this->db->select('pa_users.*,tbl_user_car_details.car_number,CONCAT(pa_users.isActive,",",pa_users.id) AS statusdata');
        $this->db->from('pa_users');
        $this->db->join('tbl_user_car_details','tbl_user_car_details.fk_user_id=pa_users.id','left');
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
}