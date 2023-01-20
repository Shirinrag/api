<?php
class User_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	public function booking_history($user_id='')
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
		$this->db->where('tbl_booking.fk_user_id',$user_id);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function booking_details_on_id($booking_id='')
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
		$this->db->where('tbl_booking.id',$booking_id);
		$query = $this->db->get();
        $result = $query->row_array();
        return $result;
	}
	public function place_data()
	{
		$this->db->select('tbl_parking_place.*,tbl_parking_place_status.place_status,tbl_countries.name as country_name,tbl_states.name as state_name,tbl_cities.name as city_name');
		$this->db->from('tbl_parking_place');
		$this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
		$this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		// $this->db->where('tbl_parking_place.fk_place_status_id',1);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function active_place_data()
	{
		$this->db->select('tbl_parking_place.*,tbl_parking_place_status.place_status,tbl_countries.name as country_name,tbl_states.name as state_name,tbl_cities.name as city_name');
		$this->db->from('tbl_parking_place');
		$this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
		$this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$this->db->where('tbl_parking_place.fk_place_status_id',1);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function inactive_place_data()
	{
		$this->db->select('tbl_parking_place.*,tbl_parking_place_status.place_status,tbl_countries.name as country_name,tbl_states.name as state_name,tbl_cities.name as city_name');
		$this->db->from('tbl_parking_place');
		$this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
		$this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$this->db->where('tbl_parking_place.fk_place_status_id',2);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function upcoming_place_data()
	{
		$this->db->select('tbl_parking_place.*,tbl_parking_place_status.place_status,tbl_countries.name as country_name,tbl_states.name as state_name,tbl_cities.name as city_name');
		$this->db->from('tbl_parking_place');
		$this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
		$this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$this->db->where('tbl_parking_place.fk_place_status_id',3);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function other_place_data()
	{
		$this->db->select('tbl_parking_place.*,tbl_parking_place_status.place_status,tbl_countries.name as country_name,tbl_states.name as state_name,tbl_cities.name as city_name');
		$this->db->from('tbl_parking_place');
		$this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
		$this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$this->db->where('tbl_parking_place.fk_place_status_id',4);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function place_details_on_id($id='')
	{
		$this->db->select('tbl_parking_place.*,tbl_parking_place_status.place_status,tbl_countries.name as country_name,tbl_states.name as state_name,tbl_cities.name as city_name,');
		$this->db->from('tbl_parking_place');
		$this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
		$this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$this->db->where('tbl_parking_place.id',$id);
		$query = $this->db->get();
        $result = $query->row_array();
        return $result;
	}
	public function get_last_booking_id()
	{
		$this->db->select('booking_id');
        $this->db->from('tbl_booking');
        $this->db->like('booking_id', "PAB");
        $query = $this->db->get();
        return $query->row_array();
	}
	public function get_rate($total_hours='',$fk_vehicle_type_id="",$fk_place_id="")
    {
       	$this->db->select('cost');
       	$this->db->from('tbl_hours_price_slab');
       	$this->db->where('fk_vehicle_type_id',$fk_vehicle_type_id);
       	$this->db->where('fk_place_id',$fk_place_id);
       // $this->db->where('from_km >=', $total_hours);
        $this->db->where((int) $total_hours.' BETWEEN from_hours AND from_hours');
        // $this->db->where('status','1');
       // $this->db->order_by('to_km',"ASC");
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }
}
