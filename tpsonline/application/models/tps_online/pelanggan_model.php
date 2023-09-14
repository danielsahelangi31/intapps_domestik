<?php
require_once('./application/models/base/modelbase.php');

class Pelanggan_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'ID' => 'ID',
		'NAMA_PERUSAHAAN' => 'Nama Perusahaan',
		'NPWP' => 'NPWP',
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'ID' => 'ID',
		'NAMA_PERUSAHAAN' => 'Nama Perusahaan',
		'NPWP' => 'NPWP',
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
		$out->datasource = $this->db->where('FLAG_DELETED', 0)
									->order_by("ID", "DESC")
									->get('MST_PELANGGAN p')->result();
		
		$this->siapkanDB(true);
		$out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
									->get('MST_PELANGGAN p')
									->row()->numRows;
		
		return $out;
	}
	//select semua data nama pelanggan
	public function select_ds($where = array()){
	
		$datasource = $this->db->select('ID, NAMA_PERUSAHAAN')->where($where)->get('MST_PELANGGAN')->result();
	
		return $datasource;
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
	
	public function get($id){	
		return $this->db->where('ID', $id)
						->get('MST_PELANGGAN')->row();
	}
	
	
	
	
	
}