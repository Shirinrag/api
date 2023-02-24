<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
    public function pos_report($place_id="",$from_date="",$to_date="")
    {
       $this->db->select('tbl_pos_booking.*,pa_users.firstName,pa_users.lastName');
       $this->db->from('tbl_pos_booking');
       $this->db->join('pa_users','tbl_pos_booking.fk_verifier_id=pa_users.id','left');
       $this->db->where('tbl_pos_booking.fk_place_id',$place_id);
       $this->db->where('tbl_pos_booking.from_date >=',$from_date);
       $this->db->where('tbl_pos_booking.from_date <=',$to_date);
       $this->db->where('tbl_pos_booking.book_status',2);
       $this->db->order_by('tbl_pos_booking.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function display_all_pos_booking_data()
    {
       $this->db->select('tbl_pos_booking.*,pa_users.firstName,pa_users.lastName,tbl_vehicle_type.vehicle_type,tbl_pos_device.pos_device_id,tbl_parking_place.place_name');
       $this->db->from('tbl_pos_booking');
       $this->db->join('pa_users','tbl_pos_booking.fk_verifier_id=pa_users.id','left');
       $this->db->join('tbl_vehicle_type','tbl_pos_booking.fk_vehicle_type_id=tbl_vehicle_type.id','left');
       $this->db->join('tbl_pos_device','tbl_pos_booking.fk_device_id=tbl_pos_device.id','left');
       // $this->db->join('tbl_pos_device','tbl_pos_booking.fk_device_id=tbl_pos_device.id','left');
       $this->db->join('tbl_parking_place','tbl_pos_booking.fk_place_id=tbl_parking_place.id','left');
       $this->db->order_by('tbl_pos_booking.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
}
	