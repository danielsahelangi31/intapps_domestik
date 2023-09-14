<?php
require_once('./application/models/base/modelbase.php');

class Sppb_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'NM_ANGKUT' => 'Vessel',
		'NO_VOY_FLIGHT' => 'Voyage',
		'NO_BL_AWB' => 'Nomor BL',
		'TGL_BL_AWB' => 'Tanggal BL',
		'NO_SPPB' => 'Nomor SPPB',
		//'TGL_SPPB' => 'Tanggal SPPB',
		//'NO_PIB' => 'Nomor PIB',
		//'TGL_PIB' => 'Tanggal PIB',
		//'NPWP_IMP' => 'NPWP Importir',
		
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'NM_ANGKUT' => 'Vessel',
		'NO_VOY_FLIGHT' => 'Voyage',
		'NO_BL_AWB' => 'Nomor BL',
		'TGL_BL_AWB' => 'Tanggal BL',
		'NO_SPPB' => 'Nomor SPPB',
		//'TGL_SPPB' => 'Tanggal SPPB',
		//'NO_PIB' => 'Nomor PIB',
		//'TGL_PIB' => 'Tanggal PIB',
		//'NPWP_IMP' => 'NPWP Importir',
		
	);
	
	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}
	
	public function select_ds($where = array()){
	
		$datasource = $this->db->select('NO_BL_AWB, NPWP_IMP')
							   ->where($where)
							   ->get('CARTOS_TPS_SPPB_PIB_H')->result();
	
		return $datasource;
	}
	
	public function select($users_id){
		$this->siapkanDB();
		
		if(!$this->sort){
			
		}
		
		$out = new StdClass();
		$querys = "select * from CARTOS_TPS_SPPB_PIB_H order by to_date(tgl_sppb,'MM/dd/yyyy') desc";
		//$hasil = $this->db->query($querys)->result_array();
		
		//var_dump($hasil); die();
		
		
		
		
		$this->db->select("to_date(TGL_SPPB, 'MM/dd/yyyy') as \"daysss\", sv.*",false);
		$this->db->order_by('daysss', "DESC");
		//$this->db->select('*');//select your colum as new column name wich is converted as str ot date
		//yo can do select more.
		//$this->db->order_by('day','ASC');
		$out->datasource = $this->db//->select("*")
									//->order_by("daysss DESC")
									->from('CARTOS_TPS_SPPB_PIB_H sv')->get()->result();
									// print_r($this->db->last_query());die();
		//var_dump($out->datasource); die();
		
		$this->siapkanDB(true);
		$out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
									->get('CARTOS_TPS_SPPB_PIB_H sv')
									->row()->numRows;
									
		return $out;
	}
	
	public function get($id){	
		/*return $this->db
						->where('NO_BL_AWB', $id)
						->get('CARTOS_TPS_SPPB_PIB_H H')->row();
		*/
		
		
		$out = new StdClass();
		$sppb	= $this->db
						->where('NO_BL_AWB', $id)
						->get('CARTOS_TPS_SPPB_PIB_H')
						->row();
						
		$cargo	= $this->db
						->where('BL_NUMBER', $id)
						->get('CARTOS_CARGO')
						->result();
		
		$out->sppb = $sppb;
		$out->cargo = $cargo;
		
		return $out;
		
		
	}
	
	/*
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
						->where('NO_BL_AWB', $id)
						->update('CARTOS_TPS_SPPB_PIB_H', $upd);
	}
	
	public function finalize_visit($visit_id){
	
	}*/
	
	
	
	
	
}