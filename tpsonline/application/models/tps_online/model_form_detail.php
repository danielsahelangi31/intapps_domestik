<?php
require_once('./application/models/base/modelbase.php');

class Model_form_detail extends CI_Model{

    private $_db;
    
	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}

    	
    public function get($id) {
        return $this->db
                        ->where('ID_MONITORING_HEADER', $id)
                        ->get('DASHBOARD_BM_HEADER')->row();
    }

    public function insert($result){
		$this->db->trans_start();
		
		
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}

}