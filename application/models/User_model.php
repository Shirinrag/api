<?php
class User_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	public function booking_history($user_id='')
	{	
		$this->db->select('tbl_booking.*,pa_users.firstName,pa_users.lastName,tbl_user_car_details.car_number,tbl_parking_place.place_name,tbl_parking_place.address,tbl_parking_place.pincode,tbl_states.name as state_name,tbl_cities.name as city_name,tbl_parking_place.latitude,tbl_parking_place.longitude,tbl_slot_info.display_id,tbl_slot_info.bluetooth_device_status,tbl_parking_place_status.place_status,tbl_parking_place.fk_place_status_id');
		$this->db->from('tbl_booking');
		$this->db->join('pa_users','tbl_booking.fk_user_id=pa_users.id','left');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
		$this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$this->db->join('tbl_slot_info','tbl_booking.fk_slot_id=tbl_slot_info.id','left');
		$this->db->where('tbl_booking.fk_user_id',$user_id);
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function extend_booking($fk_booking_id='')
	{
		$this->db->select('GROUP_CONCAT(tbl_extension_booking.booking_ext_replace) AS booking_ext_replace,GROUP_CONCAT(tbl_extension_booking.booking_from_date) as ext_booking_from_date,GROUP_CONCAT(tbl_extension_booking.booking_to_date) as ext_booking_to_date,GROUP_CONCAT(tbl_extension_booking.booking_from_time) as ext_booking_from_time,GROUP_CONCAT(tbl_extension_booking.booking_to_time) as ext_booking_to_time,GROUP_CONCAT(tbl_payment.total_amount)');
		$this->db->from('tbl_extension_booking');
		$this->db->join('tbl_payment','tbl_payment.fk_ext_booking_id=tbl_extension_booking.id','left');
		$this->db->where('tbl_extension_booking.fk_booking_id',$fk_booking_id);
		$this->db->group_by('tbl_extension_booking.fk_booking_id');
		$query = $this->db->get();
        $result = $query->row_array();
        return $result;
	}
    public function booking_details_on_id($booking_id='')
	{
		$this->db->select('tbl_booking.*,tbl_booking_status.fk_status_id,pa_users.firstName,pa_users.lastName,pa_users.phoneNo,tbl_status_master.status as booking_status,tbl_user_car_details.car_number,tbl_parking_place.place_name,tbl_parking_place.address,tbl_parking_place.pincode,tbl_states.name as state_name,tbl_cities.name as city_name,tbl_booking_verify.verify_status');
// 		,GROUP_CONCAT(DISTINCT(tbl_extension_booking.booking_ext_replace)) as booking_ext_replace,GROUP_CONCAT(DISTINCT(tbl_extension_booking.booking_from_date)) as ext_booking_from_date,GROUP_CONCAT(DISTINCT(tbl_extension_booking.booking_to_date)) as ext_booking_to_date,GROUP_CONCAT(DISTINCT(tbl_extension_booking.booking_from_time)) as ext_booking_from_time,GROUP_CONCAT(DISTINCT(tbl_extension_booking.booking_to_time)) as ext_booking_to_time,GROUP_CONCAT(DISTINCT(tbl_extension_booking.reserve_from_time)) as ext_reserve_from_time,GROUP_CONCAT(DISTINCT(tbl_extension_booking.reserve_to_time)) as ext_reserve_to_time,
		$this->db->from('tbl_booking');
// 		$this->db->join('tbl_extension_booking','tbl_extension_booking.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->join('pa_users','tbl_booking.fk_user_id=pa_users.id','left');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$this->db->join('tbl_booking_verify','tbl_booking_verify.fk_booking_id=tbl_booking.id','left');
		$this->db->where('tbl_booking.id',$booking_id);
// 		$this->db->group_by('tbl_extension_booking.fk_booking_id');
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
		$this->db->where('tbl_parking_place.fk_place_status_id !=',2);
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
        $this->db->order_by('id',"DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
	}
	public function get_rate($total_hours='',$fk_vehicle_type_id="",$fk_place_id="")
    {
       	$this->db->select('cost,fk_currency_id');
       	$this->db->from('tbl_hours_price_slab');
       	$this->db->where('fk_vehicle_type_id',$fk_vehicle_type_id);
       	$this->db->where('fk_place_id',$fk_place_id);
       // $this->db->where('from_km >=', $total_hours);
        $this->db->where((int) $total_hours.' BETWEEN from_hours AND to_hours');
        // $this->db->where('status','1');
       // $this->db->order_by('to_km',"ASC");
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function get_last_ext_booking_id($id="")
	{
	   $this->db->select('booking_ext_replace');
        $this->db->from('tbl_extension_booking');
        $this->db->like('booking_ext_replace', "EXT");
        $this->db->where('fk_booking_id',$id);
        $this->db->order_by('id',"DESC");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row_array();
	}

	public function ongoing_unverified_booking_list($place_id='')
	{
		$this->db->select('tbl_booking.id,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.total_hours,tbl_user_car_details.car_number,tbl_status_master.status,tbl_booking_status.fk_status_id,tbl_slot_info.display_id');		
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->join('tbl_slot_info','tbl_booking.fk_slot_id=tbl_slot_info.id','left');
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->where('tbl_booking.fk_verify_booking_status',2);
		$this->db->where('tbl_status_master.id',1);
		$this->db->group_by('tbl_booking.id');
		$query = $this->db->get();
      return $query->result_array();

	}
	public function ongoing_verified_booking_list($place_id='')
	{
		$this->db->select('tbl_booking.id,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.total_hours,tbl_user_car_details.car_number,tbl_status_master.status,tbl_booking_status.fk_status_id,tbl_booking_verify.verify_status,tbl_slot_info.display_id');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->join('tbl_booking_verify','tbl_booking_verify.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_slot_info','tbl_booking.fk_slot_id=tbl_slot_info.id','left');
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->where('tbl_booking_verify.verify_status',1);
		$this->db->where('tbl_booking.fk_verify_booking_status',1);
		$this->db->where('tbl_status_master.id',1);
		$this->db->group_by('tbl_booking.id');
		$query = $this->db->get();
      return $query->result_array();
	}

	public function complete_booking_list($place_id='')
	{
		$this->db->select('tbl_booking.id,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.total_hours,tbl_user_car_details.car_number,tbl_status_master.status,tbl_booking_status.fk_status_id');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');	
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->where('tbl_status_master.id',2);
		$this->db->group_by('tbl_booking.id');
		$query = $this->db->get();
      return $query->result_array();
	}

	public function history_booking_list($place_id='')
	{
		$this->db->select('tbl_booking.id,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.total_hours,tbl_user_car_details.car_number,tbl_status_master.status,tbl_booking_status.fk_status_id');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');	
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->where('tbl_status_master.id',2);
		$query = $this->db->get();
      return $query->result_array();
	}

	public function get_slot_details($id="",$from_date="",$to_date="",$from_time="",$to_time="")
	{
		$from_time = date('H:i:s', strtotime($from_time));
	   $to_time = date('H:i:s', strtotime($to_time));
		$this->db->select('tbl_booking.booking_id,tbl_booking_status.fk_status_id,tbl_status_master.status');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		// 		$this->db->join('tbl_extension_booking','tbl_extension_booking.fk_booking_id=tbl_booking.id','left');
		$this->db->where('tbl_booking.fk_slot_id',$id);
		$this->db->where('tbl_booking.booking_from_date',$from_date);
		$this->db->where('tbl_booking.booking_to_date',$to_date);
		// $this->db->or_where('tbl_booking.booking_from_time',$from_time);
		 $this->db->where('tbl_booking.booking_to_time <=',$from_time);
		
		// 		$this->db->or_where('tbl_extension_booking.booking_from_date',$from_date);
		// 		$this->db->or_where('tbl_extension_booking.booking_to_date',$to_date);
		// 		$this->db->or_where('tbl_extension_booking.booking_from_time',$from_time);
		// 		$this->db->or_where('tbl_extension_booking.booking_to_time',$to_time);
		$query = $this->db->get();
      return $query->result_array();
	}
    public function traffice_details($user_id="")
	{
		$this->db->select('tbl_traffic_subscription.fk_city_id,tbl_cities.name');
		$this->db->from('tbl_traffic_subscription');
		$this->db->join('tbl_cities','tbl_traffic_subscription.fk_city_id=tbl_cities.id','left');
		$this->db->where('tbl_traffic_subscription.fk_user_id',$user_id);
		$query = $this->db->get();
      return $query->result_array();
	}

		public function ongoing_unverified_pos_booking_list($place_id="")
	{
		$this->db->select('tbl_booking.id,tbl_booking.fk_user_id,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.total_hours,tbl_user_car_details.car_number');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->where('tbl_booking.fk_verify_booking_status',2);
		$this->db->where('tbl_booking.booking_type',2);
		$query = $this->db->get();
        return $query->result_array();
	}
	public function accepted_pos_booking_list($place_id='')
	{
		$this->db->select('tbl_booking.id,tbl_booking.fk_user_id,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.total_hours,tbl_user_car_details.car_number,tbl_status_master.status,tbl_booking_status.fk_status_id,tbl_booking_verify.verify_status,tbl_slot_info.display_id');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->join('tbl_booking_verify','tbl_booking_verify.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_slot_info','tbl_booking.fk_slot_id=tbl_slot_info.id','left');
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->where('tbl_booking.booking_type',2);
		$this->db->where('tbl_status_master.id !=',2);
		$this->db->where('tbl_booking_status.used_status',1);
// 		$this->db->where('tbl_booking.fk_verify_booking_status',1);
// 		$this->db->where('tbl_booking_verify.verify_status',1);
// 		$this->db->where('tbl_booking_verify.verify_status !=',2);
// 		$this->db->where('tbl_status_master.id',1);
// 		$this->db->where('tbl_booking_status.used_status',1);
// 		$this->db->where('tbl_booking.fk_verify_booking_status !=',2);
// 		$this->db->where('tbl_booking.fk_verify_booking_status !=',3);
			
		$this->db->group_by('tbl_booking.id');
		$query = $this->db->get();
      return $query->result_array();
	}
	public function rejected_pos_booking_list($place_id='')
	{
		$this->db->select('tbl_booking.id,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.total_hours,tbl_user_car_details.car_number,tbl_status_master.status,tbl_booking_status.fk_status_id,tbl_booking_verify.verify_status,tbl_slot_info.display_id');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->join('tbl_booking_verify','tbl_booking_verify.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_slot_info','tbl_booking.fk_slot_id=tbl_slot_info.id','left');
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->where('tbl_booking.booking_type',2);
		$this->db->where('tbl_booking.fk_verify_booking_status',3);
		$this->db->where('tbl_booking_verify.verify_status',2);
		$this->db->where('tbl_status_master.id',3);
		
		$this->db->where('tbl_booking_status.used_status',1);
		$this->db->where('tbl_booking.fk_verify_booking_status !=',2);	
		$this->db->where('tbl_booking.fk_verify_booking_status !=',1);
	   // $this->db->where('tbl_booking.fk_verify_booking_status',3);
		$this->db->group_by('tbl_booking.id');
		$query = $this->db->get();
      return $query->result_array();
	}
	public function completed_booking_list($place_id='')
	{
			$this->db->select('tbl_booking.id,tbl_booking.fk_user_id,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_booking.total_hours,tbl_user_car_details.car_number,tbl_status_master.status,tbl_booking_status.fk_status_id,tbl_booking_verify.verify_status');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->join('tbl_booking_verify','tbl_booking_verify.fk_booking_id=tbl_booking.id','left');
		
		$this->db->where('tbl_booking.fk_place_id',$place_id);
		$this->db->where('tbl_booking.booking_type',2);
		// $this->db->where('tbl_booking.fk_verify_booking_status',1);
		// $this->db->where('tbl_booking_verify.verify_status',1);
		// $this->db->where('tbl_booking_verify.verify_status !=',2);
		$this->db->where('tbl_status_master.id',2);
		$this->db->where('tbl_booking_status.used_status',1);
		// $this->db->where('tbl_booking_status.status',2);
		// $this->db->where('tbl_booking.fk_verify_booking_status !=',2);
		// $this->db->where('tbl_booking.fk_verify_booking_status !=',3);
			
		$this->db->group_by('tbl_booking.id');
		$query = $this->db->get();
      return $query->result_array();
	}

	public function hours_price_slab($place_id='')
	{
		$this->db->select('tbl_hours_price_slab.*,tbl_currency.currency_symbol');
        $this->db->from('tbl_hours_price_slab');
        $this->db->join('tbl_currency','tbl_hours_price_slab.fk_currency_id=tbl_currency.id','left');
       $this->db->where('tbl_hours_price_slab.fk_place_id',$place_id);
       $this->db->where('tbl_hours_price_slab.del_status',1);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function booking_details_on_barcode($booking_id='')
	{
		$this->db->select('tbl_booking.*,tbl_booking_status.fk_status_id,pa_users.firstName,pa_users.lastName,pa_users.phoneNo,tbl_status_master.status as booking_status,tbl_user_car_details.car_number,tbl_parking_place.place_name,tbl_parking_place.address,tbl_parking_place.pincode,tbl_states.name as state_name,tbl_cities.name as city_name,tbl_booking_verify.verify_status,tbl_payment.total_amount');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_status_master','tbl_booking_status.fk_status_id=tbl_status_master.id','left');
		$this->db->join('pa_users','tbl_booking.fk_user_id=pa_users.id','left');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
		$this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
		$this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
		$this->db->join('tbl_booking_verify','tbl_booking_verify.fk_booking_id=tbl_booking.id','left');
		$this->db->join('tbl_payment','tbl_payment.fk_booking_id=tbl_booking.id','left');
		$this->db->where('tbl_booking.booking_id',$booking_id);
		$query = $this->db->get();
        $result = $query->row_array();
        return $result;
	}

	public function get_car_no_on_booking_id($booking_id='')
	{
		$this->db->select('tbl_booking.is_scanned,tbl_user_car_details.car_number');
		$this->db->from('tbl_booking');
		$this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
		$this->db->where('tbl_booking.booking_id',$booking_id);
		$query = $this->db->get();
        $result = $query->row_array();
        return $result;
	}
}
