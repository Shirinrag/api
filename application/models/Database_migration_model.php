<?php
class Database_migration_model extends CI_Model {
	// public $DB1;
	public $DB2;
	function __construct() {
		parent::__construct();
		// $this->DB1= $this->load->database("parking_adda", TRUE);
		$this->DB2= $this->load->database("easy_parking_adda_live", TRUE);
	}

	public function get_all_users()
	 {
	 	$this->DB2->select('*');
	 	$this->DB2->from('ci_user');
	 	 $query = $this->DB2->get();
        $result = $query->result_array();
        return $result;
	 }                         
}
?>