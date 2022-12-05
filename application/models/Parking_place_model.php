<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Parking_place_model extends CI_Model {
 
     public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function _get_datatables_query()
    {    
        $column_order = array('place_name','state_name','country_name','city_name','address','latitude','longitude');
        $column_search = array('place_name','state_name','country_name','city_name','address','latitude','longitude');

            $this->db->select('tbl_parking_place.*,tbl_cities.name as city_name,tbl_states.name as state_name,tbl_countries.name as country_name,pa_users.firstName,pa_users.lastName');
            $this->db->from('tbl_parking_place');
            $this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
            $this->db->join('tbl_states','tbl_cities.state_id=tbl_states.id','left');
            $this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
            $this->db->join('pa_users','tbl_parking_place.fk_vendor_id=pa_users.id','left');
            $this->db->where('tbl_parking_place.del_status',1);
	        $this->db->order_by('tbl_parking_place.id','DESC'); 
	       
        $i = 0; 

        foreach ($column_search as $item) // loop column 
        {
            if(@$_POST['search']['value']) // if datatable send POST for search
            {                 
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.

                    $this->db->like($item, @$_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, @$_POST['search']['value']);
                } 

                if(count($this->column_search) - 1 == $i) //last loop

                    $this->db->group_end(); //close bracket
            }

            $i++;
        }     

        if(!empty(@$_POST['order'])) // here order processing
        {
            $this->db->order_by($column_order[@$_POST['order']['0']['column']], @$_POST['order']['0']['dir']);
        } 

        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables()
    {
        $this->_get_datatables_query();
       // if($_POST['length'] != -1)
       // $this->db->limit($_POST['length'],$_POST['start']);
        $query=$this->db->get();
        return $query->result_array();
    } 

    function count_filtered()
    {
        $this->_get_datatables_query();

        $query = $this->db->get();

        return $query->num_rows();
    } 

    public function count_all()
    {         
         $this->db->select('tbl_parking_place.*,tbl_cities.name as city_name,tbl_states.name as state_name,tbl_countries.name as country_name,pa_users.firstName,pa_users.lastName');
        $this->db->from('tbl_parking_place');
        $this->db->join('tbl_countries','tbl_parking_place.fk_country_id=tbl_countries.id','left');
        $this->db->join('tbl_states','tbl_cities.state_id=tbl_states.id','left');
        $this->db->join('tbl_cities','tbl_parking_place.fk_city_id=tbl_cities.id','left');
        $this->db->join('pa_users','tbl_parking_place.fk_vendor_id=pa_users.id','left');
        $this->db->where('tbl_parking_place.del_status',1);
        $this->db->order_by('tbl_parking_place.id','DESC'); 
        return $this->db->count_all_results();
    }
}