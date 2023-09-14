<?php
require_once('./application/models/base/modelbase.php');

class Kunjungan_Kapal_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'VISIT_NAME' => 'Nama Kapal',
		'VISIT_ID' => 'Visit ID',
		'VESSEL_CODE' => 'Vessel Code',
		'VOYAGE_IN' => 'Voyage In',
		'VOYAGE_OUT' => 'Voyage Out',
		'ETA' => 'Estimated Time Arrival',
		'ETD' => 'Estimated Time Departure',
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'VISIT_NAME' => 'Nama Kapal',
		'VISIT_ID' => 'Visit ID',
		'VESSEL_CODE' => 'Vessel Code',
		'VOYAGE_IN' => 'Voyage In',
		'VOYAGE_OUT' => 'Voyage Out',
		'ETA' => 'Estimated Time Arrival',
		'ETD' => 'Estimated Time Departure',
	);
	
	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}
	
	public function select_ds($where = array()){
	
		$datasource = $this->db->select('VISIT_ID, VISIT_NAME')->where($where)->get('CARTOS_SHIP_VISIT')->result();
	
		return $datasource;
	}
	
	public function select($users_id){
		$this->siapkanDB();
		
		if(!$this->sort){
			
		}
		
		$out = new StdClass();			
		$out->datasource = $this->db
									->get('CARTOS_SHIP_VISIT sv')->result();
		
		$this->siapkanDB(true);
		$out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
									->get('CARTOS_SHIP_VISIT sv')
									->row()->numRows;
									
		return $out;
	}
	
	public function get($id){	
		return $this->db
						->where('VISIT_ID', $id)
						->get('CARTOS_SHIP_VISIT')->row();
	}
	
	public function update($id){
		$upd = array(
			'BC_NUMBER' => post('BC_NUMBER'),
			'BC_DATE_NUMBER' => date('Y-m-d', strtotime(post('BC_DATE_NUMBER'))),
			'LOAD_PORT' => post('LOAD_PORT'),
			'TRANSIT_PORT' => post('TRANSIT_PORT'),
			'DISCHARGER_PORT' => post('DISCHARGER_PORT'),
			'NEXT_PORT' => post('NEXT_PORT'),
		);
		
		return $this->db
						->where('VISIT_ID', $id)
						->update('CARTOS_SHIP_VISIT', $upd);
	}
	
	public function finalize_visit($visit_id){
	
	}
	
	
	
	
	
}