<?php

class Model_form extends CI_Model{

	public function __construct() {
        parent::__construct();
        $this->_db = $this->load->database('ikt_postgree', TRUE);
    }
		
	public function set_db($db){
		$this->db = $db;
	}

	public function get_data_form($id, $voyage)
	{
		//var_dump($id);die();
		$con = $this->load->database('ikt_postgree', TRUE);

		$sql = 'SELECT a."SHIFT", a."ACTIVITY", a."REALISASI_BONGKAR", a."REALISASI_MUAT", a."REMAINING_BONGKAR", a."REMAINING_MUAT", a."TOTAL", a."USH", a."USH_GROSS",a."BT", a."ET_BT",
			b."NAMA_KAPAL", b."VOYAGE", b."PBM", b."RENCANA_BONGKAR", b."RENCANA_MUAT", b."KADE_DERMAGA", 
			(b."RENCANA_BONGKAR"+b."RENCANA_MUAT") "TOTAL_BM",
			 b."ETA", 
			 b."ETB",
			 b."ETD",
			 b."ATA", 
			 b."ATB",
			 b."ATD",
			b."COMMENCE", 
			b."COMPLETE",
			"TIME_START",
			"TIME_END",
			"TANGGAL_TIME",			
			"WORKING_HOURBT",
			"TOTAL_IT",
			"TOTAL_NOT",
			"ET",
			"BWT"
	
 			FROM "DASHBOARD_BM_DETAIL" a join "DASHBOARD_BM_HEADER" b ON a."nama_kapal" = b."NAMA_KAPAL" AND a."voyage" = b."VOYAGE" AND b."ID_MONITORING_HEADER" = a."ID_MONITORING_DETAIL"
			WHERE b."NAMA_KAPAL" = '.$id.' AND b."VOYAGE" = '.$voyage.' 
			ORDER BY a."ID_MONITORING_DETAIL" ASC
		';

		$data = $con->query($sql);
	
		$res = array(
	                "data"  => $data->result_array()
	                
	            );
		//var_dump($data);die();
	    return $res;
	}

}