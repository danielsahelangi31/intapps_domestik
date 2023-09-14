<?php
require_once('./application/models/base/modelbase.php');

class Log_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'ID' => 'ID',
		'VIN' => 'VIN',
		'KETERANGAN' => 'KETERANGAN',
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'ID' => 'ID',
		'VIN' => 'VIN',
		'KETERANGAN' => 'KETERANGAN',
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
		$out->datasource = $this->db->where('ID >', 0)
									->order_by("ID", "DESC")
									->get('LOG_UPDATE p')->result();
		$this->siapkanDB(true);
		$out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
									->get('LOG_UPDATE p')
									->row()->numRows;
		
		return $out;
	}
	
	
	
	
}