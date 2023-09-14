<?php
require_once('./application/models/base/modelbase.php');

class Kargo_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'BL_NUMBER' => 'No BL',
		'BL_NUMBER_DATE' => 'Tanggal BL',
		'NO_PEB' => 'No. PEB',
		'NO_NPE' => 'No. NPE',
		'VISIT_ID' => 'Visit ID',
		'COUNT_OF' => 'Jumlah VIN',
		
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'BL_NUMBER' => 'No BL',
		'BL_NUMBER_DATE' => 'Tanggal BL',
		'NO_PEB' => 'No. PEB',
		'NO_NPE' => 'No. NPE',
		'VISIT_ID' => 'Visit ID',
		'COUNTOF' => 'Jumlah VIN',
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
	
	public function select($users_id){
		$this->siapkanDB();
		
		if(!$this->sort){
			
		}
		
		$out = new StdClass();			
		$out->datasource = $this->db
									->get('V_BL c')->result();
		
		$this->siapkanDB(true);
		$out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
									->get('V_BL c')
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
		$cleanid = urldecode($id);
		//echo $id; die;
		return $this->db->join('CARTOS_SHIP_VISIT T2', 'T2.VISIT_ID = T1.VISIT_ID', 'left')
						->where('BL_NUMBER', $cleanid)
						->get('CARTOS_CARGO T1')->result();
	}
	
	public function insert($result){
		$this->db->trans_start();
		
		
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}
	
	public function update($id){
		
	}
	
	
	
	
	
	
	
}