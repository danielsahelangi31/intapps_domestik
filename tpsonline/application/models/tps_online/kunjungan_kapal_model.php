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
		'VISIT_DIRECTION' => 'Visit Direction',
		'VESSEL_STATUS' => 'Visit Status',
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
		'VISIT_DIRECTION' => 'Visit Direction',
		'VESSEL_STATUS' => 'Visit Status',
	);
	
	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}
	
	public function select_ds($where = array()){
	
		$datasource = $this->db->select('VISIT_ID, VISIT_NAME')->where($where)->where('ETA IS NOT NULL', NULL, FALSE)->like('VISIT_ID', 'VES', 'AFTER')->order_by("ETA", "desc")->get('CARTOS_SHIP_VISIT')->result();
	
		return $datasource;
	}

	public function select_all_bl($noBL){

	
		$datasource = $this->db->query("select distinct(bl_number) as bl_number from cartos_cargo where bl_number like '%".$noBL."%'")->result();

	
		return $datasource;
	}

	public function autofill_from_bl($bl){

	
		$datasource = $this->db->query("select bl_number, nvl(inward_bc11,outward_bc11) bc11, nvl(to_char(inward_bc11_date,'DD-MM-YYYY'),to_char(outward_bc11_date,'DD-MM-YYYY')) bc11_date, consignee_name, consignee_tax_ref 
			from cartos_cargo a join cartos_ship_visit b on a.visit_id = b.visit_id
			where bl_number = '".$bl."' and rownum < 2")->result();
	
		return $datasource;
	}

	public function autofill_from_vis($visID){

	
		$datasource = $this->db->query("select visit_id, nvl(inward_bc11,outward_bc11) bc11, nvl(to_char(inward_bc11_date,'DD-MM-YYYY'),to_char(outward_bc11_date,'DD-MM-YYYY')) bc11_date from 
			cartos_ship_visit where visit_id = '".$visID."'and rownum < 2")->result();
	
		return $datasource;
	}
	
	public function select_type_cargo($where = array()){
	
		$datasource = $this->db->select('CUSTOMS_CODE, DESCRIPTION')->where($where)->get('CUSTOMS_TYPE_MAPPING')->result();
					  // echo $this->db->last_query();die();
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
	
	function get_autoComplete($keyword) {
		
		$data=$this->db->query("select * from cartos_ship_visit where visit_id like '%$keyword'");
	    return $data->result();
    }
	
	public function update($id){
		$upd = array(
			'INWARD_BC11' => post('INWARD_BC11'),
			'INWARD_BC11_DATE' => date('Y-m-d', strtotime(post('INWARD_BC11_DATE'))),
			'OUTWARD_BC11' => post('OUTWARD_BC11'),
			'OUTWARD_BC11_DATE' => date('Y-m-d', strtotime(post('OUTWARD_BC11_DATE'))),
			'LOAD_PORT' => post('LOAD_PORT'),
			'TRANSIT_PORT' => post('TRANSIT_PORT'),
			'DISCHARGER_PORT' => post('DISCHARGER_PORT'),
			//'NEXT_PORT' => post('NEXT_PORT'),
		);
		
		return $this->db
						->where('VISIT_ID', $id)
						->update('CARTOS_SHIP_VISIT', $upd);
	}
	
	public function finalize_visit($visit_id){
	
	}

	public function get_doc_type(){
		$sql = "SELECT * FROM MST_JENIS_DOKUMEN order by ID ASC";
		$res = $this->db->query($sql);
		$data = array();

		foreach ($res->result() as $row) {
			$data['ID'][] = $row->ID;
			$data['DOC_TYPE'][] = $row->DOC_TYPE;
		}

		return $data;
	}
	
	
}