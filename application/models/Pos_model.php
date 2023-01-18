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
       $this->db->where('tbl_pos_booking.from_date',$from_date);
       $this->db->where('tbl_pos_booking.to_date',$to_date);
       $this->db->order_by('tbl_pos_booking.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
}
	