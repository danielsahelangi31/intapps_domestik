<?php
require_once('./application/models/base/modelbase.php');

class Report_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'SVC_INSTANCE' => 'Method',
		'ID_TRX' => 'REF Number',
		'BL_NUMBER' => 'Nomor BL',
		'VISIT_ID' => 'Visit ID',
		'CUSTOMS_CARGO_TYPE' => 'Tipe Kargo',
		'COUNTERS' => 'Pengiriman Ke',
		'SUM_CARGO' => 'Jumlah Kargo',
		'ACK' => 'Respon Pengiriman',
		'STATUS' => 'Status',
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'SVC_INSTANCE' => 'Method',
		'ID_TRX' => 'REF Number',
		'BL_NUMBER' => 'Nomor BL',
		'VISIT_ID' => 'Visit ID',
		'CUSTOMS_CARGO_TYPE' => 'Tipe Kargo',
		'COUNTERS' => 'Pengiriman Ke',
		'SUM_CARGO' => 'Jumlah Kargo',
		'ACK' => 'Respon Pengiriman',
		'STATUS' => 'Status',
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
		$out->datasource = $this->db
									->get('TPS_TRANSACTION_LOG sv')
									->result();
		
		$this->siapkanDB(true);
		$out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
									->get('TPS_TRANSACTION_LOG sv')
									->row()->numRows;
									
		return $out;
	}
	
	public function get($id){	
		/*return $this->db
						->where('NO_BL_AWB', $id)
						->get('CARTOS_TPS_SPPB_PIB_H H')->row();
		*/
		
		
		$out = new StdClass();
		$log	= $this->db
						->where('BL_NUMBER', $id)
						->get('TPS_TRANSACTION_LOG')
						->row();
						
		$cargo	= $this->db
						->where('BL_NUMBER', $id)
						->get('CARTOS_CARGO')
						->result();
		
		$out->log = $log;
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