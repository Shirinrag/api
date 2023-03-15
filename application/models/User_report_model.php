<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class User_report_model extends CI_Model {
 
     public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function _get_datatables_query($from_date="",$to_date="")
    {    
        $column_order = array('firstName','lastName','phoneNo','car_number');
        $column_search = array('firstName','lastName','phoneNo','car_number');

        $this->db->select('pa_users.*,tbl_user_car_details.car_number,CONCAT(pa_users.isActive,",",pa_users.id) AS statusdata');
        $this->db->from('pa_users');
        $this->db->join('tbl_user_car_details','tbl_user_car_details.fk_user_id=pa_users.id','left');
        $this->db->where('pa_users.user_type',10);
        $this->db->where('pa_users.del_status',1);
        if (!empty($from_date)) {
               $from_date =date('Y-m-d', strtotime($from_date));
               $from_date = $from_date ." 00:00:00";
               $this->db->where('pa_users.created_at >=',$from_date);
        }
       if (!empty($to_date)) {
            $to_date =date('Y-m-d', strtotime($to_date));
            $to_date = $to_date ." 23:59:00";
            $this->db->where('pa_users.created_at <=',$to_date);
        }
        $this->db->order_by('pa_users.id','DESC');
        $this->db->group_by('pa_users.id');
	       
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
 
    function get_datatables($from_date="",$to_date="")
    {
        $this->_get_datatables_query($from_date,$to_date);
       // if($_POST['length'] != -1)
       // $this->db->limit($_POST['length'],$_POST['start']);
        $query=$this->db->get();
        return $query->result_array();
    } 

    function count_filtered($from_date="",$to_date="")
    {
        $this->_get_datatables_query($from_date,$to_date);

        $query = $this->db->get();

        return $query->num_rows();
    } 

    public function count_all($from_date="",$to_date="")
    {         
        $this->db->select('pa_users.*,tbl_user_car_details.car_number,CONCAT(pa_users.isActive,",",pa_users.id) AS statusdata');
        $this->db->from('pa_users');
        $this->db->join('tbl_user_car_details','tbl_user_car_details.fk_user_id=pa_users.id','left');
        $this->db->where('pa_users.user_type',10);
        $this->db->where('pa_users.del_status',1);
        if (!empty($from_date)) {
               $from_date =date('Y-m-d', strtotime($from_date));
               $from_date = $from_date ." 00:00:00";
               $this->db->where('pa_users.created_at >=',$from_date);
        }
       if (!empty($to_date)) {
            $to_date =date('Y-m-d', strtotime($to_date));
            $to_date = $to_date ." 23:59:00";
            $this->db->where('pa_users.created_at <=',$to_date);
        }
        $this->db->order_by('pa_users.id','DESC');
        $this->db->group_by('pa_users.id');
        return $this->db->count_all_results();
    }
}