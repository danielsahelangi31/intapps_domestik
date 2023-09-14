<?php 
require_once('./application/models/base/modelbase.php');

class Master_Data_Approval extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'entity' => 'Nomor DO',
		'partner_code' => 'Kode Partner',
		'partner_name' => 'Nama Partner',
		'entity' => 'Entitas',
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'entity' => 'Nomor DO',
		'partner_code' => 'Kode Partner',
		'partner_name' => 'Nama Partner',
		'entity' => 'Entitas',
	);
	
	public function __construct(){
		parent::__construct();
	}
	
	public function set_db($db){
		$this->db = $db;
	}
	
	public function select(){
		$this->siapkanDB();
		$this->db	->select('SQL_CALC_FOUND_ROWS p.*, mda.*', FALSE);
		
		if(!$this->sort){
			$this->db->order_by('mda.receipt_time', 'DESC');
		}
		
		$out = new StdClass();			
		$out->datasource = $this->db->join('partner p', 'p.partner_id = mda.partner_id')
									->get('master_data_approval mda')->result();
		
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;
		
		return $out;
	}
	
	public function get($id){
		$out = new StdClass();
		
		$datasource = $this->db	->select('p.*, mda.*')
								->join('partner p', 'p.partner_id = mda.partner_id')
								->get('master_data_approval mda')->row();
							
		if($datasource){
			$out->success = true;
			$out->msg_code = 200;
			$out->datasource = $datasource;
		}else{
			$out->success = false;
			$out->msg_code = 400;
			$out->msg = 'Data approval tidak ditemukan.';
			$out->datasource = null;
		}
		
		return $out;
	}

}