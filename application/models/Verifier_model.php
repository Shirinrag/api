<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verifier_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	public function place_details($verifier_id="",$place_id="",$user_type="")
	{
        $current_date = date("d/m/Y");
		$this->db->select('tbl_parking_place.*,tbl_duty_allocation.date');
        $this->db->from('tbl_parking_place');
        $this->db->join('tbl_duty_allocation','tbl_duty_allocation.fk_place_id=tbl_parking_place.id','left');
        if($user_type != 13){
            $this->db->where('tbl_parking_place.id',$place_id);
            $this->db->where('tbl_duty_allocation.fk_verifier_id',$verifier_id);
            $this->db->where('tbl_duty_allocation.date',$current_date);
        }
        $this->db->where('tbl_parking_place.fk_place_status_id',"1");       
        $this->db->order_by('tbl_duty_allocation.id',"desc");
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}

    public function not_verified_booking_list($verifier_id="",$place_id="")
    {
        $current_date = date("d/m/Y");
        $this->db->select('tbl_booking.*,tbl_duty_allocation.date');
        $this->db->from('tbl_booking');
        $this->db->join('tbl_duty_allocation','tbl_duty_allocation.fk_place_id=tbl_booking.id','left');        
        $this->db->where('tbl_booking.id',$place_id);
        $this->db->where('tbl_booking.fk_verify_booking_status',2);
        $this->db->where('tbl_duty_allocation.fk_verifier_id',$verifier_id);
        $this->db->where('tbl_duty_allocation.date',$current_date);   
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
}
	