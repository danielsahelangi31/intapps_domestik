<?php
require_once('./application/models/base/modelbase.php');

class Consignment_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'VIN' => 'Nama Kapal',
		'BL_NUMBER' => 'No BL',
		'BL_NUMBER_DATE' => 'Tanggal BL',
		'CONSIGNEE_NAME' => 'Nama Consignee',
		'OWNER_NAME' => 'Nama Pemilik',
		'DTS_ONTERMINAL' => 'On Terminal',
		'DTS_LOADED' => 'Loaded',
		'DTS_LEFT' => 'Left',
		
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'VIN' => 'Nama Kapal',
		'BL_NUMBER' => 'No BL',
		'BL_NUMBER_DATE' => 'Tanggal BL',
		'CONSIGNEE_NAME' => 'Nama Consignee',
		'OWNER_NAME' => 'Nama Pemilik',
		'DTS_ONTERMINAL' => 'On Terminal',
		'DTS_LOADED' => 'Loaded',
		'DTS_LEFT' => 'Left',
	);
	
	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}
	
	public function select($users_id){
		$this->siapkanDB();
		
		if(!$this->sort){
			
		}
		
		$out = new StdClass();			
		$out->datasource = $this->db
									->get('CARTOS_CARGO c')->result();
		
		$this->siapkanDB(true);
		$out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
									->get('CARTOS_CARGO c')
									->row()->numRows;
		
		return $out;
	}
	
	public function get($id, $users_id){	
		return $this->db->where('mu.users_id', $users_id)
						->where('mu.id', $id)
						->get('manifest_upload mu')->row();
	}
	
	public function insert($result){
		$this->db->trans_start();
		
		
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}
	
	public function update_visit($id){
		$upd = array(
			'VISIT_ID' => post('VISIT_ID'),
		);
		
		return $this->db
						->where('VIN', $id)
						->where('VISIT_ID', NULL)
						->update('CARTOS_cargo', $upd);
	}
	
	
	
	
	
	
	
}