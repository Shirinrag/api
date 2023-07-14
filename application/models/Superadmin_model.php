<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Superadmin_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
	}

	public function display_all_user_data()
	{
		$this->db->select('pa_users.*,tbl_user_car_details.car_number,CONCAT(pa_users.isActive,",",pa_users.id) AS statusdata');
		$this->db->from('pa_users');
		$this->db->join('tbl_user_car_details','tbl_user_car_details.fk_user_id=pa_users.id','left');
		$this->db->where('pa_users.user_type',10);
		$this->db->where('pa_users.del_status',1);
		$this->db->order_by('pa_users.id','DESC');
		$this->db->group_by('pa_users.id');
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function booking_history_data()
	{
        $this->db->select('tbl_booking.*,pa_users.firstName,pa_users.lastName,tbl_user_car_details.car_number,tbl_parking_place.place_name,tbl_parking_place.address,tbl_parking_place.pincode,tbl_states.name as state_name,tbl_cities.name as city_name,tbl_parking_place.latitude,tbl_parking_place.longitude,tbl_slot_info.display_id');
        $this->db->from('tbl_booking');
        $this->db->join('pa_users','tbl_booking.fk_user_id=pa_users.id','left');
        $this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
        $this->db->join('tbl_parking_place','tbl_booking.fk_place_id=tbl_parking_place.id','left');
        $this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
        $this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
        $this->db->join('tbl_slot_info','tbl_booking.fk_slot_id=tbl_slot_info.id','left');
        $this->db->order_by('tbl_booking.id','DESC');
        // $this->db->where('tbl_booking.fk_user_id',$user_id);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}

    public function extend_booking_history_data()
    {
       $this->db->select('tbl_extension_booking.id,tbl_extension_booking.booking_ext_replace,tbl_extension_booking.booking_from_date as ext_booking_from_date,tbl_extension_booking.booking_to_date as ext_booking_to_date,tbl_extension_booking.booking_from_time as ext_booking_from_time,tbl_extension_booking.booking_to_time as ext_booking_to_time,tbl_booking.booking_id');
        $this->db->from('tbl_extension_booking');
        $this->db->join('tbl_booking','tbl_extension_booking.fk_booking_id=tbl_booking.id','left');
        $this->db->order_by('tbl_extension_booking.id','DESC');
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
	public function display_all_admin_data()
	{
		$this->db->select('pa_users.*,tbl_user_type.user_type as user_type_name,CONCAT(pa_users.isActive,",",pa_users.id) AS statusdata');
		$this->db->from('pa_users');
		$this->db->join('tbl_user_type','pa_users.user_type=tbl_user_type.id','left');
		$this->db->where('pa_users.user_type !=',10);
		$this->db->where('pa_users.del_status',1);
		$this->db->order_by('pa_users.id','DESC');
		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
	}
	public function uniqueSlotName($variable) {
        $var1 = explode('-', $variable);
        if ($var1[1] == 'ZZ999') {
            print ("Sorry we cannot go beyond this ");
        } else if ($var1[1][2] == 9 && $var1[1][3] == 9 && $var1[1][4] == 9) {
            if ($var1[1][1] == 'Z') {
                $var1[1][0] = chr(ord($var1[1][0]) + 1);
                $var1[1][1] = 'A';
                $var1[1][2] = 0;
                $var1[1][3] = 0;
                $var1[1][4] = 1;
            } else {
                $var1[1][1] = chr(ord($var1[1][1]) + 1);
                $var1[1][2] = 0;
                $var1[1][3] = 0;
                $var1[1][4] = 1;
            }
        } else if ($var1[1][3] == 9 && $var1[1][4] == 9) {
            $var1[1][2] = $var1[1][2] + 1;
            $var1[1][3] = 0;
            $var1[1][4] = 0;
        } else {
            if ($var1[1][4] == 9) {
                $var1[1][3] = $var1[1][3] + 1;
                $var1[1][4] = 0;
            } else {
                $var1[1][3] = $var1[1][3];
                $var1[1][4] = $var1[1][4] + 1;
            }
        }
        return $var1[1];
    }
    public function parking_place_data_on_id($id='')
    {
    	$this->db->select('tbl_parking_place.*');
    	$this->db->from('tbl_parking_place');
    	$this->db->where('tbl_parking_place.id',$id);
    	$query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function display_all_device_data()
    {
    	$this->db->select('tbl_device.*,CONCAT(tbl_device.status,",",tbl_device.id) AS statusdata,tbl_parking_place.place_name,tbl_slot_info.slot_name,tbl_slot_info.display_id');

    	$this->db->from('tbl_device');
    	$this->db->join('tbl_slot_info','tbl_slot_info.fk_machine_id=tbl_device.id','left');
    	$this->db->join('tbl_parking_place','tbl_parking_place.id=tbl_slot_info.fk_place_id','left');
    	
    	$this->db->order_by('tbl_device.id','DESC');
    	$query = $this->db->get();
        $result = $query->result_array();
        return $result;

    }
    public function get_parking_place_details_on_id($id='')
    {
    	$this->db->select('tbl_parking_place.*,pa_users.firstName,pa_users.lastName,tbl_cities.name as city_name,tbl_states.name as state_name,tbl_countries.name as country_name,tbl_parking_place_status.place_status,tbl_parking_price_type.price_type');
    	$this->db->from('tbl_parking_place');
    	$this->db->join('pa_users','tbl_parking_place.fk_vendor_id=pa_users.id','left');
    	$this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
        $this->db->join('tbl_states','tbl_parking_place.fk_state_id=tbl_states.id','left');
        $this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
        $this->db->join('tbl_parking_place_status','tbl_parking_place.fk_place_status_id=tbl_parking_place_status.id','left');
        $this->db->join('tbl_parking_price_type','tbl_parking_place.fk_place_status_id=tbl_parking_price_type.id','left');
    	$this->db->where('tbl_parking_place.id',$id);
    	$query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }

    public function get_slot_id($slots='')
    {
    	$this->db->select('id');
    	$this->db->from('tbl_slot_info');
        $this->db->where('del_status',1);
    	$this->db->order_by('id','DESC');
  		$this->db->limit($slots);
  		$query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_count_slot_id()
    {
    	$this->db->select('count(id) as total');
        $this->db->from('tbl_slot_info');
        $this->db->where('del_status',1);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function display_all_duty_allocation_data()
    {
       $this->db->select('tbl_duty_allocation.*,pa_users.firstName,pa_users.lastName,tbl_parking_place.place_name,pa_users.phoneNo,tbl_parking_place.address');
       $this->db->from('tbl_duty_allocation');
       $this->db->join('pa_users','tbl_duty_allocation.fk_verifier_id=pa_users.id','left');
       $this->db->join('tbl_parking_place','tbl_duty_allocation.fk_place_id=tbl_parking_place.id','left');
       $this->db->where('tbl_duty_allocation.del_status','1');
       $this->db->order_by('tbl_duty_allocation.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function display_all_pos_device_map_data()
    {
        $this->db->select('tbl_pos_device_map.*,CONCAT(tbl_pos_device_map.status,",",tbl_pos_device_map.id) AS statusdata,tbl_parking_place.place_name,tbl_pos_device.pos_device_id');
        $this->db->from('tbl_pos_device_map');
        $this->db->join('tbl_parking_place','tbl_parking_place.id=tbl_pos_device_map.fk_place_id','left'); 
        $this->db->join('tbl_pos_device','tbl_pos_device.id=tbl_pos_device_map.device_id','left'); 
        $this->db->where('tbl_pos_device_map.del_status','1');       
        $this->db->order_by('tbl_pos_device_map.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function display_all_pos_verifier_duty_allocation_data()
    {
        $this->db->select('tbl_pos_duty_allocation.*,pa_users.firstName,pa_users.lastName,tbl_parking_place.place_name,pa_users.phoneNo,tbl_parking_place.address');
       $this->db->from('tbl_pos_duty_allocation');
       $this->db->join('pa_users','tbl_pos_duty_allocation.fk_pos_verifier_id=pa_users.id','left');
       $this->db->join('tbl_parking_place','tbl_pos_duty_allocation.fk_place_id=tbl_parking_place.id','left');
       $this->db->where('tbl_pos_duty_allocation.del_status','1');
       $this->db->order_by('tbl_pos_duty_allocation.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_hour_price_slab($id='')
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000');
        $this->db->select('GROUP_CONCAT(tbl_hours_price_slab.id) as id,GROUP_CONCAT(tbl_hours_price_slab.from_hours) as from_hours,GROUP_CONCAT(tbl_hours_price_slab.to_hours) as to_hours,GROUP_CONCAT(tbl_hours_price_slab.cost) as cost,tbl_hours_price_slab.fk_vehicle_type_id,tbl_vehicle_type.vehicle_type');
       $this->db->from('tbl_hours_price_slab');
       $this->db->join('tbl_vehicle_type','tbl_hours_price_slab.fk_vehicle_type_id=tbl_vehicle_type.id','left');
       $this->db->where('tbl_hours_price_slab.fk_place_id',$id);
       $this->db->group_by('tbl_hours_price_slab.fk_vehicle_type_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function display_all_register_user_complaint()
    {
        $this->db->select('tbl_user_complaint.*,tbl_parking_place.place_name,pa_users.firstName,pa_users.lastName,pa_users.email,tbl_booking.booking_id,tbl_booking.booking_from_date,tbl_booking.booking_to_date,tbl_booking.booking_from_time,tbl_booking.booking_to_time,tbl_issue_type.issue_type,tbl_user_car_details.car_number,tbl_slot_info.display_id');
        $this->db->from('tbl_user_complaint');
        $this->db->join('tbl_parking_place','tbl_user_complaint.fk_place_id=tbl_parking_place.id','left');
        $this->db->join('pa_users','tbl_user_complaint.fk_user_id=pa_users.id','left');
        $this->db->join('tbl_booking','tbl_user_complaint.fk_booking_id=tbl_booking.id','left');
        $this->db->join('tbl_issue_type','tbl_user_complaint.topic=tbl_issue_type.id','left');
        $this->db->join('tbl_user_car_details','tbl_booking.fk_car_id=tbl_user_car_details.id','left');
        $this->db->join('tbl_slot_info','tbl_booking.fk_slot_id=tbl_slot_info.id','left');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function display_all_unregister_user_complaint()
    {
        $this->db->select('tbl_complaint.*,tbl_issue_type.issue_type');
        $this->db->from('tbl_complaint');
        $this->db->join('tbl_issue_type','tbl_complaint.fk_issue_type_id=tbl_issue_type.id','left');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function display_all_vendor_map_place_data()
    {
        $this->db->select('tbl_vendor.vendor_id,tbl_vendor.id as tbl_vendor_id,tbl_vendor_map_place.fk_vendor_id as fk_vendor_id,GROUP_CONCAT(tbl_vendor_map_place.id) as id,GROUP_CONCAT(tbl_vendor_map_place.fk_place_id) as fk_place_id,GROUP_CONCAT(tbl_parking_place.place_name SEPARATOR ";") as place_name,CONCAT(tbl_vendor_map_place.status,",",tbl_vendor_map_place.id) AS statusdata,pa_users.firstName,pa_users.lastName');
        $this->db->from('tbl_vendor');
        $this->db->join('tbl_vendor_map_place','tbl_vendor_map_place.fk_vendor_id=tbl_vendor.id','left');
        $this->db->join('tbl_parking_place','tbl_vendor_map_place.fk_place_id=tbl_parking_place.id','left');
        $this->db->join('pa_users','tbl_vendor.vendor_id=pa_users.id','left');
        $this->db->order_by('tbl_vendor_map_place.id','DESC');
        $this->db->group_by('tbl_vendor_map_place.fk_vendor_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_vendor_map_place_data_on_id($id='')
    {
        $this->db->select('tbl_vendor.vendor_id,tbl_vendor.id as tbl_vendor_id,tbl_vendor_map_place.fk_vendor_id as fk_vendor_id,GROUP_CONCAT(tbl_vendor_map_place.id) as id,GROUP_CONCAT(tbl_vendor_map_place.id) as tbl_vendor_map_place_id,GROUP_CONCAT(tbl_vendor_map_place.fk_place_id) as fk_place_id,GROUP_CONCAT(tbl_parking_place.place_name SEPARATOR ";") as place_name,CONCAT(tbl_vendor_map_place.status,",",tbl_vendor_map_place.id) AS statusdata,pa_users.firstName,pa_users.lastName');
        $this->db->from('tbl_vendor');
        $this->db->join('tbl_vendor_map_place','tbl_vendor_map_place.fk_vendor_id=tbl_vendor.id','left');
        $this->db->join('tbl_parking_place','tbl_vendor_map_place.fk_place_id=tbl_parking_place.id','left');
        $this->db->join('pa_users','tbl_vendor.vendor_id=pa_users.id','left');
        $this->db->where('tbl_vendor.id',$id);
        $this->db->group_by('tbl_vendor_map_place.fk_vendor_id');
        $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }
    public function display_all_slot_complaint_data()
    {
        $this->db->select('tbl_verifier_complaint.id,tbl_verifier_complaint.fk_place_id,tbl_verifier_complaint.fk_slot_id,tbl_verifier_complaint.complaint_text,tbl_verifier_complaint.source,tbl_verifier_complaint.created_at,tbl_parking_place.place_name,tbl_parking_place.address,tbl_slot_info.display_id,');
        $this->db->from('tbl_verifier_complaint');
        $this->db->join('tbl_parking_place','tbl_verifier_complaint.fk_place_id=tbl_parking_place.id','left');
        $this->db->join('tbl_slot_info','tbl_verifier_complaint.fk_slot_id=tbl_slot_info.id','left');
        $this->db->order_by('tbl_verifier_complaint.id','DESC');
         $query = $this->db->get();
        $result = $query->row_array();
        return $result;
    }
    public function get_monthly_price_slab($id='')
    {
        $this->db->simple_query('SET SESSION group_concat_max_len=15000');
        $this->db->select('GROUP_CONCAT(tbl_pass_price_slab.id) as id,GROUP_CONCAT(tbl_pass_price_slab.no_of_days) as no_of_days,GROUP_CONCAT(tbl_pass_price_slab.cost) as cost,tbl_pass_price_slab.fk_vehicle_type_id,tbl_vehicle_type.vehicle_type');
       $this->db->from('tbl_pass_price_slab');
       $this->db->join('tbl_vehicle_type','tbl_pass_price_slab.fk_vehicle_type_id=tbl_vehicle_type.id','left');
       $this->db->where('tbl_pass_price_slab.fk_place_id',$id);
       $this->db->group_by('tbl_pass_price_slab.fk_vehicle_type_id');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function display_all_user_pass_details()
    {
        $this->db->select('tbl_user_pass_details.*,tbl_parking_place.place_name,tbl_nfc_device.nfc_device_id,tbl_pass_days.no_of_days');
        $this->db->from('tbl_user_pass_details');
        $this->db->join('tbl_parking_place','tbl_user_pass_details.fk_place_id=tbl_parking_place.id','left');
        $this->db->join('tbl_nfc_device','tbl_user_pass_details.fk_nfc_device_id=tbl_nfc_device.id','left');
        $this->db->join('tbl_pass_days','tbl_user_pass_details.fk_no_of_days=tbl_pass_days.id','left');
        $this->db->order_by('tbl_user_pass_details.id','DESC');
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
    public function get_details_on_car_no($car_no='')
    {
        $this->db->select('id,fk_user_id');
        $this->db->from('tbl_user_car_details');
        $this->db->like('car_number', $car_no);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_booking_id($car_no='')
    {
        $this->db->select('tbl_booking.id,tbl_booking.fk_user_id,tbl_booking_status.fk_booking_id,tbl_pos_booking.id as pos_id');
        $this->db->from('tbl_booking');
        $this->db->join('tbl_booking_status','tbl_booking_status.fk_booking_id=tbl_booking.id','left');
        $this->db->join('tbl_pos_booking','tbl_booking.fk_pos_booking_id=tbl_pos_booking.id','left');



    }
}
	