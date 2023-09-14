<?php
require_once('./application/models/base/modelbase.php');

class Kargolist_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'VIN' => 'Nama Kapal',
		'BL_NUMBER' => 'No BL',
		'BL_NUMBER_DATE' => 'Tanggal BL',
		'CONSIGNEE_NAME' => 'Nama Consignee',
		'LOGISTIC_COMPANY' => 'Nama Logistik',
		'DTS_ONTERMINAL' => 'On Terminal',
		'DTS_LOADED' => 'Loaded',
		'DTS_LEFT' => 'Left',
		
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'VIN' => 'Nama Kapal',
		'VISIT_ID' => 'Visit ID',
		'BL_NUMBER' => 'No BL',
		'BL_NUMBER_DATE' => 'Tanggal BL',
		'CONSIGNEE_NAME' => 'Nama Consignee',
		'LOGISTIC_COMPANY' => 'Nama Logistik',
		'DTS_ONTERMINAL' => 'On Terminal',
		'DTS_LOADED' => 'Loaded',
		'DTS_LEFT' => 'Left',
	);
	
	// Enumeration values
	public $DIRECTION = array(
		'1' => 'IMPORT',
		'2' => 'EXPORT'
	);
	
	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}
	
	public function select($id){
		$this->siapkanDB();
		$cleanid = urldecode($id);
		
		if(!$this->sort){
			
		}
		//like('title', 'match', 'after');
		$out = new StdClass();			
		$out->datasource = $this->db->like('BL_NUMBER', $cleanid, 'after')
									->get('CARTOS_CARGO c')->result();
		//print_r ($out); die;
		//echo '<pre>';
		//print_r($out->datasource);
		//echo '</pre>';
		//die;
		$this->siapkanDB(true);
		$out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
									->like('BL_NUMBER', $cleanid, 'after')
									->get('CARTOS_CARGO c')
									->row()->numRows;
							
		
		return $out;
	}
	
	public function select_unsent($VISIT_ID){
		return $this->db->select()
						->where('VISIT_ID', $VISIT_ID)
						->where('FLAG_SEND_COARRI', 0)
						->get('CARTOS_CARGO c')->result();
	}
	
	public function get($id){	
		$id = str_replace('%20',' ',$id);
		return $this->db->join('CARTOS_SHIP_VISIT T2', 'T2.VISIT_ID = T1.VISIT_ID', 'left')
						->join('TPS_ONLINE_TRANS_LOG T3', 'T3.BL_NUMBER = T1.BL_NUMBER', 'left')
						->where('VIN', $id)
						->get('CARTOS_CARGO T1')->row();
	}
	
	public function insert($result){
		$this->db->trans_start();
		
		
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}
	
	public function update($id){
		
	}
	
	
	
	
	
	
	
}