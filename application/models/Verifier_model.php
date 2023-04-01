<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Verifier_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	public function place_details($verifier_id="",$place_id="",$user_type="")
	{
        $current_date = date("Y-m-d");
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
        $current_date = date("Y-m-d");
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

    public function place_data()
    {
        $this->db->select('tbl_parking_place.*,tbl_parking_place_status.place_status,tbl_countries.name as country_name,tbl_states.name as state_name,tbl_cities.name as city_name');
        // ,tbl_slot_info.display_id
        $this->db->from('tbl_parking_place');
        $this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
        $this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
        $this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
        $this->db->join('tbl_slot_info','tbl_slot_info.fk_place_id=tbl_parking_place.id','left');
        $this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
        $this->db->where('tbl_parking_place.fk_place_status_id',1);
        // $this->db->where('tbl_slot_info.fk_machine_id !=',"");
        // $this->db->group_by('tbl_slot_info.fk_place_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
}
	