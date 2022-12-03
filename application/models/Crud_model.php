<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class Crud_model extends CI_Model {

    // var $table = 'tbl_diagnosis_list';
     var $column_order = array(); //set column field database for datatable orderable
     var $column_search = array(); //set column field database for datatable searchable 
     var $order = array(); // default order 
    public function __construct()
    {
        parent::__construct();
    }
    private function _get_datatables_query($table,$select_column,$condition,$column_order,$order)
    {
        $this->db->select($select_column)
                 ->from($table)
                 ->where($condition);
        $i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if(count($this->column_search) - 1 == $i) //last loop

                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($order))
        {
            $order = $order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
    }
    function get_datatables($table,$select_column,$condition,$column_order,$order)
    {
        $column_order=$column_order;
        $column_search=$select_column;
       
        $this->_get_datatables_query($table,$select_column,$condition,$column_order,$order);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
    function count_filtered($table,$select_column,$condition,$column_order,$order)
    {
        $this->_get_datatables_query($table,$select_column,$condition,$column_order,$order);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function count_all($table,$select_column,$condition,$column_order,$order)
    {
        // $this->db->select($select_column);
        // $this->db->from($table);
         $this->_get_datatables_query($table,$select_column,$condition,$column_order,$order);
        // $query = $this->db->get();
        return $this->db->count_all_results();
    }
}