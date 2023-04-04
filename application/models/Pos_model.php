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

    public function get_details_on_pos_device_id($device_id='')
    {
         $this->db->select('tbl_pos_device.id as pos_device_id,tbl_pos_device_map.fk_place_id,tbl_parking_place.place_name,tbl_parking_place.id as place_id,tbl_parking_place.address,tbl_vendor_map_place.fk_vendor_id,tbl_vendor.vendor_id');
         $this->db->from('tbl_pos_device');
         $this->db->join('tbl_pos_device_map','tbl_pos_device_map.device_id=tbl_pos_device.id','left');
         $this->db->join('tbl_parking_place','tbl_pos_device_map.fk_place_id=tbl_parking_place.id','left');
         $this->db->join('tbl_vendor_map_place','tbl_vendor_map_place.fk_place_id=tbl_parking_place.id','left');
         $this->db->join('tbl_vendor','tbl_vendor_map_place.fk_vendor_id=tbl_vendor.id','left');
         $this->db->where('tbl_pos_device.pos_device_id',$device_id);
         $query = $this->db->get();
         $result = $query->row_array();
         return $result;
    }
    public function two_wheller_monthly_price_slab($id='')
    {
       $this->db->select('tbl_pass_price_slab.*,tbl_pass_days.no_of_days,tbl_pass_days.id as no_of_days_id');
         $this->db->from('tbl_pass_price_slab');
         $this->db->join('tbl_pass_days','tbl_pass_price_slab.no_of_days=tbl_pass_days.id','left');
         $this->db->where('tbl_pass_price_slab.fk_place_id',$id);
         $this->db->where('tbl_pass_price_slab.fk_vehicle_type_id',1);
         $query = $this->db->get();
         $result = $query->result_array();
         return $result;
    }
    public function three_wheller_monthly_price_slab($id='')
    {
       $this->db->select('tbl_pass_price_slab.*,tbl_pass_days.no_of_days,tbl_pass_days.id as no_of_days_id');
         $this->db->from('tbl_pass_price_slab');
         $this->db->join('tbl_pass_days','tbl_pass_price_slab.no_of_days=tbl_pass_days.id','left');
         $this->db->where('tbl_pass_price_slab.fk_place_id',$id);
         $this->db->where('tbl_pass_price_slab.fk_vehicle_type_id',2);

         $query = $this->db->get();
         $result = $query->result_array();
         return $result;
    }
    public function four_wheller_monthly_price_slab($id='')
    {
       $this->db->select('tbl_pass_price_slab.*,tbl_pass_days.no_of_days,tbl_pass_days.id as no_of_days_id');
         $this->db->from('tbl_pass_price_slab');
         $this->db->join('tbl_pass_days','tbl_pass_price_slab.no_of_days=tbl_pass_days.id','left');
         $this->db->where('tbl_pass_price_slab.fk_place_id',$id);
         $this->db->where('tbl_pass_price_slab.fk_vehicle_type_id',3);

         $query = $this->db->get();
         $result = $query->result_array();
         return $result;
    }
    public function heavy_wheller_monthly_price_slab($id='')
    {
       $this->db->select('tbl_pass_price_slab.*,tbl_pass_days.no_of_days,tbl_pass_days.id as no_of_days_id');
         $this->db->from('tbl_pass_price_slab');
         $this->db->join('tbl_pass_days','tbl_pass_price_slab.no_of_days=tbl_pass_days.id','left');
         $this->db->where('tbl_pass_price_slab.fk_place_id',$id);
         $this->db->where('tbl_pass_price_slab.fk_vehicle_type_id',4);
         $query = $this->db->get();
         $result = $query->result_array();
         return $result;
    }
}
	