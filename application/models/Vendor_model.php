<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}
	public function get_vendor_place_list($id='')
	{
		$this->db->select('tbl_vendor.vendor_id,tbl_vendor_map_place.fk_place_id,tbl_parking_place.place_name');
		$this->db->from('tbl_vendor');
		$this->db->join('tbl_vendor_map_place','tbl_vendor_map_place.fk_vendor_id=tbl_vendor.id','left');
		$this->db->join('tbl_parking_place','tbl_vendor_map_place.fk_place_id=tbl_parking_place.id','left');
		$this->db->where('tbl_vendor.vendor_id',$id);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function total_earning_place_wise($place_id='')
	{
		$this->db->select('tbl_booking.fk_place_id,GROUP_CONCAT(tbl_payment.total_amount) as total_amount');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_payment','tbl_payment.fk_booking_id=tbl_booking.id','left');
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->group_by('tbl_payment.total_amount');
		$query = $this->db->get();
        $result = $query->row_array();
        return $result;

	}
}
