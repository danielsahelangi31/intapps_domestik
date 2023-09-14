<?php

class Data_histori_model extends CI_Model{

private $local_db;
public function __construct(){
		parent::__construct();
		
		// $this->load->database(ILCS_TPS_ONLINE, TRUE);
		// $this->db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		// $this->db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		if(!$this->local_db){
			$this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
			$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
			$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		}
		
		return $this->local_db;
	
}



public function data_level1($tahun,$bulan ){
	// $tahun = '2018'; 
	// $bulan = '06';
	$q = " and to_char(arrival,'yyyymm') = '".$tahun.$bulan."'";
	// $awal = (int)$params['start'] + 1;
	// $akhir = (int)$params['length']+ (int)$params['start'];
	// $rownum = " r >= ".$params['start']." and r <=".$akhir." ";
	$query =" select a.visit_id, visit_name, voyage_in, voyage_out, arrival, departure, jml_bl, jml_vin, jml_sukses_codeco, jml_sukses_coarri,
				jml_car, jml_kms, jml_valid_car, jml_valid_kms
				from cartos_ship_visit a left join (select visit_id, count(distinct bl_number) jml_bl,count(*) jml_vin, 
				sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco, 
				sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri,
				sum(case when type_cargo = 'CBU' then 1 else 0 end) jml_car,
				sum(case when type_cargo <> 'CBU' and type_cargo is not null then 1 else 0 end) jml_kms,
				sum(nvl(is_valid_car,0)) jml_valid_car,
				sum(nvl(is_valid_kms,0)) jml_valid_kms
				from cartos_cargo where DIRECTION = 1 
				group by visit_id) b on a.visit_id = b.visit_id 
				where visit_direction = '1' 
				".$q." 
				order by arrival desc
	" ;
	// echo($query);die;
	$a = $this->local_db->query($query);
	
	$result = array(
			    "data"	=> $a->result_array()
			    
			);
	
	return $result;

}

public function data_level2($visit_id){
	$query = "select visit_id, bl_number, type_cargo, count(*) jml_vin,sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco,
				sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri
				from cartos_cargo
				where  DIRECTION = 1 and visit_id = '$visit_id'
				group by visit_id, bl_number, type_cargo order by visit_id, bl_number, type_cargo";
	$a = $this->local_db->query($query);
	$result = array(
			    "data"	=> $a->result_array()
			);
	// echo json_encode($result);die;
	return $result;	

}

public function data_level3($blnum,$visit_id,$type){
	$query = "SELECT T1.*, T2.*, T3.KD_DOK, T3.ID_TRX, T3.KD_DOK_INOUT, T3.DT 
				FROM CARTOS_CARGO T1 LEFT JOIN CARTOS_SHIP_VISIT T2 ON T2.VISIT_ID = T1.VISIT_ID 
					LEFT JOIN TPS_ONLINE_TRANS_LOG T3 ON T3.BL_NUMBER = T1.BL_NUMBER WHERE T1.BL_NUMBER = '$blnum' AND T1.VISIT_ID ='$visit_id'  AND T1.TYPE_CARGO = '$type'";
	$a = $this->local_db->query($query);
	$result = array(
			    "data"	=> $a->result_array()
			);
	// echo json_encode($result);die;
	return $result;	

}		


// ==== notifikasi export ==== //
	
	public function query_export($tahun,$bulan )
	{

		$q = " and to_char(arrival,'yyyymm') = '".$tahun.$bulan."'";

		$query =" 
				SELECT a.visit_id, visit_name, voyage_in, voyage_out, arrival, departure, jml_bl, jml_vin, jml_sukses_codeco, jml_sukses_coarri, jml_car, jml_kms, jml_valid_car, jml_valid_kms
					from cartos_ship_visit a 
					left join (select visit_id, count(distinct bl_number) jml_bl,count(*) jml_vin, 
					sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco, 
					sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri,
					sum(case when type_cargo = 'CBU' then 1 else 0 end) jml_car,
					sum(case when type_cargo <> 'CBU' and type_cargo is not null then 1 else 0 end) jml_kms,
					sum(nvl(is_valid_car,0)) jml_valid_car,
					sum(nvl(is_valid_kms,0)) jml_valid_kms
					from cartos_cargo 
					where DIRECTION = 2 group by visit_id) b on a.visit_id = b.visit_id 
					where visit_direction = '1' ".$q." order by arrival desc
				";

		// direction = 1 visit_direction = 2
		// direction = 1 visit_direction = 1

		$a = $this->local_db->query($query);
		
		$result = array(
				    "data"	=> $a->result_array()
				    
				);
		
		return $result;

	}

	
	
	
	
	
}