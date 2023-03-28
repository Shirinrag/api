<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	public function get_vendor_place_list($id='')
	{
		$this->db->select('tbl_vendor.vendor_id,tbl_vendor_map_place.fk_place_id,tbl_parking_place.place_name,tbl_parking_place.parking_place_type');
		$this->db->from('tbl_vendor');
		$this->db->join('tbl_vendor_map_place','tbl_vendor_map_place.fk_vendor_id=tbl_vendor.id','left');
		$this->db->join('tbl_parking_place','tbl_vendor_map_place.fk_place_id=tbl_parking_place.id','left');
		$this->db->where('tbl_vendor.vendor_id',$id);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function total_earning_data($from_date="",$to_date="",$place_id='')
	{
		$this->db->select('tbl_pos_booking.fk_place_id,GROUP_CONCAT(tbl_pos_booking.price) as total_amount');
		$this->db->from('tbl_pos_booking');
// 		$this->db->join('tbl_payment','tbl_payment.fk_booking_id=tbl_booking.id','left');
		$this->db->where('tbl_pos_booking.from_date',$from_date);
		$this->db->where('tbl_pos_booking.to_date',$to_date);
		$this->db->where('tbl_pos_booking.fk_place_id',$place_id);
		$this->db->group_by('tbl_pos_booking.fk_place_id');
		$query = $this->db->get();
        $result = $query->row_array();
        return $result;
	}
	public function upcoming_booking_history($vendor_id="",$place_id="")
	{	
		$current_time = date('H:i');
		$current_date = date('Y-m-d');
		$this->db->select('tbl_vendor.vendor_id,tbl_vendor_map_place.fk_place_id,tbl_parking_place.place_name,tbl_booking.booking_from_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.fk_car_id,tbl_payment.total_amount,tbl_user_car_details.car_number');
		$this->db->from('tbl_vendor');
		$this->db->join('tbl_vendor_map_place','tbl_vendor_map_place.fk_vendor_id=tbl_vendor.id','left');
		$this->db->join('tbl_booking','tbl_booking.fk_place_id=tbl_vendor_map_place.fk_place_id','left');
		$this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_payment','tbl_payment.fk_booking_id=tbl_booking.id','left');
		$this->db->where('tbl_vendor.vendor_id',$vendor_id);
		$this->db->where('tbl_booking.booking_from_time >',$current_time);
		$this->db->where('tbl_booking.booking_from_date',$current_date);
		if(!empty($place_id)){
			$this->db->where('tbl_booking.fk_place_id',$place_id);
		}
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function today_booking_history($vendor_id="",$place_id="")
	{	
		// $current_time = date('H:i');
		$current_date = date('Y-m-d');
		$this->db->select('tbl_vendor.vendor_id,tbl_vendor_map_place.fk_place_id,tbl_parking_place.place_name,tbl_booking.booking_from_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.fk_car_id,tbl_payment.total_amount,tbl_user_car_details.car_number');
		$this->db->from('tbl_vendor');
		$this->db->join('tbl_vendor_map_place','tbl_vendor_map_place.fk_vendor_id=tbl_vendor.id','left');
		$this->db->join('tbl_booking','tbl_booking.fk_place_id=tbl_vendor_map_place.fk_place_id','left');
		$this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_payment','tbl_payment.fk_booking_id=tbl_booking.id','left');
		$this->db->where('tbl_vendor.vendor_id',$vendor_id);
		$this->db->where('tbl_booking.booking_from_date',$current_date);
		if(!empty($place_id)){
			$this->db->where('tbl_booking.fk_place_id',$place_id);
		}
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function past_booking_history($vendor_id="",$place_id="",$from_date="",$to_date="")
	{
		$this->db->select('tbl_vendor.vendor_id,tbl_vendor_map_place.fk_place_id,tbl_parking_place.place_name,tbl_booking.booking_from_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.fk_car_id,tbl_payment.total_amount,tbl_user_car_details.car_number,tbl_booking_status.fk_status_id,tbl_status_master.status');
		$this->db->from('tbl_vendor');
		$this->db->join('tbl_vendor_map_place','tbl_vendor_map_place.fk_vendor_id=tbl_vendor.id','left');
		$this->db->join('tbl_booking','tbl_booking.fk_place_id=tbl_vendor_map_place.fk_place_id','left');
		$this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_payment','tbl_payment.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->where('tbl_vendor.vendor_id',$vendor_id);
		$this->db->where('tbl_booking_status.fk_status_id',2);
		if(!empty($from_date)){
			$this->db->where('tbl_booking.booking_from_date',$from_date);
		}
		if(!empty($to_date)){
			$this->db->where('tbl_booking.booking_from_date',$to_date);
		}
		if(!empty($place_id)){
			$this->db->where('tbl_booking.fk_place_id',$place_id);
		}
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
}
