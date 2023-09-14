<?php
require_once('../base/modelbase.php');

class Mapper_Port extends ModelBase{
	// Searchable Fields
	public $searchable = array(
		'nama_perusahaan' => 'Nama Perusahaan',
		'npwp' => 'NPWP',
	);
	
	public $sortable = array();

	public function __construct(){
		parent::__construct();
		
	}
	
	public function select(){
		$this->siapkanDB();
		$this->db	->select('SQL_CALC_FOUND_ROWS m.*', FALSE)
					->where('_approved', 0);
									
		$out = new StdClass();			
		$out->datasource = $this->db->get('member m')->result();
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;
		
		return $out;
		
	}
}	