<?php
class Superadmin_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	public function display_all_user_data()
	{
		$this->db->select('pa_users.*,tbl_user_car_details.car_number');
		$this->db->from('pa_users');
		$this->db->join('tbl_user_car_details','tbl_user_car_details.fk_user_id=pa_users.id','left');
		$this->db->where('pa_users.user_type',10);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function booking_history_data()
	{
		$this->db->select('tbl_booking.*,tbl_extension_booking.booking_ext_replace,tbl_extension_booking.booking_from_date as ext_booking_from_date,tbl_extension_booking.booking_to_date as ext_booking_to_date,tbl_extension_booking.booking_from_time as ext_booking_from_time,tbl_extension_booking.booking_to_time as ext_booking_to_time,tbl_extension_booking.reserve_from_time as ext_reserve_from_time,tbl_extension_booking.reserve_to_time as ext_reserve_to_time,tbl_booking_status.fk_status_id,pa_users.firstName,pa_users.lastName,tbl_status_master.status as booking_status,tbl_user_car_details.car_number,tbl_parking_place.place_name,tbl_parking_place.address,tbl_parking_place.pincode,tbl_states.name as state_name,tbl_cities.name as city_name');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_extension_booking','tbl_extension_booking.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->join('pa_users','tbl_booking.fk_user_id=pa_users.id','left');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}

	public function get_count_slot_name($prefix='')
	{
		$this->db->select('count(fk_place_id) as total');
        $this->db->from('tbl_slot_info');
        $this->db->like('slot_name', $prefix);
        $query = $this->db->get();
        return $query->row_array();
	}
}
	