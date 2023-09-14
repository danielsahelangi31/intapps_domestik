<?php
class Cartos extends CI_Model{
	private $local_db;
	
	public function __construct(){
		parent::__construct();	
	}
	
	private function get_db(){
		if(!$this->local_db){
			$this->local_db = $this->load->database(ILCS_CARTOS, TRUE);
			$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
			$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		}
		
		return $this->local_db;
	}
	
	/** Implement class access list here
	  *
	  */
	public function get_sub_cat($auth){
		$sub_cat = array(
			'bongkar_muat_kapal' => 'Bongkar Muat Per Kapal',
			//'bongkar_muat_periode' => 'Bongkar Muat Periode',
			'tps_coco_periode' => 'Pengiriman Data TPS Online',
			'other_report' => 'Report Lainnya'
		);
		
		return $sub_cat;
	}
	
	/** Implement datasource used on param form
	  *
	  */
	public function get_datasource($function, $auth){
		$out = new StdClass();
		$db = $this->get_db();
		
		switch($function){
			case 'bongkar_muat_kapal':
				$out->ship_visit_ds = $db->select('VISIT_ID, VESSEL_CODE, VISIT_NAME')->get('VESSEL_SCHEDULE_CTOS')->result();
				return $out;
				break;
		}
		
	}
	
	
	public function bongkar_muat_kapal(){
		$out = new StdClass();
		$db = $this->get_db();
		
		$visit_id = post('visit_id');
		$output_format = post('output_format');
		
		$kapal = $db	->select('VISIT_ID, VISIT_NAME, OPERATIONAL, COMPLETION, ARRIVAL, DEPARTURE')
						->from('VESSEL_SCHEDULE_CTOS SV')
						->where('SV.VISIT_ID', $visit_id)->get()->row();
		
		$statistik = $db	->select('EI, MIN(START_WORK_PBM) AS START_WORK_PBM, MAX(END_WORK_PBM) AS END_WORK_PBM', false)
							->from('TTH_CARGO_STEVEDORING TH')
							->where('TH.DILA_ID', $kapal->VISIT_ID)
							->group_by('EI')
							->get()
							->result();
		
		$datasource = $db	->select('SH.EI, SD.KD_CARGO, SD.JUMLAH, SD.M3, SD.TONNAGE, BH.PBM_CARGODORING, BH.PBM_STEVEDORING_ON_VESSEL')
							->from('TTH_CARGO_STEVEDORING SH')
							->join('TTD_CARGO_STEVEDORING SD', 'SD.STEVEDORING_ID = SH.STEVEDORING_ID')
							->join('TTH_CARGO_BOOKING BH', 'BH.NO_BOOKING = SD.NO_BOOKING')
							->where('SH.DILA_ID', $visit_id)
							->get()
							->result();
							
		// Pack
		if($output_format == 'HTML'){
			$out->custom_view = 'cartos/bongkar_muat_html';
		}else{
			$out->custom_view = 'cartos/bongkar_muat_excel';
		}
		
		$out->kapal = $kapal;
		$out->datasource = $datasource;
		$out->statistik = $statistik;
						
		return $out;
	}

	public function tps_coco_periode(){
		$out = new StdClass();
		$db = $this->get_db();
		
		$awal = date('Y-m-d', strtotime(post('start_date')));
		$akhir = date('Y-m-d', strtotime(post('end_date')));
		$output_format = post('output_format');
		$output_format = 'EXCEL';
							
		$coarri = $db	->select("TRUNC(SH.DATE_SEND_COARRI) as DATE_SEND_COARRI
								,TRIM(SD.VISIT_NAME) AS VISIT_NAME
								,TRIM(SD.VOYAGE_IN) AS VOYAGE_IN
								,TRIM(SD.VOYAGE_OUT) AS VOYAGE_OUT
								,TRIM(SH.VISIT_ID) AS VISIT_ID
								,TRIM(SH.BL_NUMBER) AS BL_NUMBER
								,TRIM(SH.DIRECTION) AS DIRECTION
								,COUNT(1) as COARRI")
							->from("CARTOS_CARGO SH")
							->join('CARTOS_SHIP_VISIT SD', 'SD.VISIT_ID = SH.VISIT_ID')
							//->where('SH.FLAG_SEND_COARRI', 1)
							//->where("SH.DATE_SEND_COARRI  BETWEEN to_date('".$awal. "','yyyy-mm-dd') and to_date('".$akhir."','yyyy-mm-dd')")
							->where("SD.ARRIVAL  BETWEEN to_date('".$awal. "','yyyy-mm-dd') and to_date('".$akhir."','yyyy-mm-dd')")
							//->where('SH.DATE_SEND_COARRI >=', $awal)
							//->where('SH.DATE_SEND_COARRI <=', $akhir)
							->group_by('TRUNC(SH.DATE_SEND_COARRI)
										,TRIM(SD.VISIT_NAME)
										,TRIM(SD.VOYAGE_IN)
										,TRIM(SD.VOYAGE_OUT)
										,TRIM(SH.VISIT_ID)
										,TRIM(SH.BL_NUMBER)
										,TRIM(SH.DIRECTION)')
							->order_by('TRIM(SD.VISIT_NAME),TRIM(SD.VOYAGE_IN)','ASC')
							->get()
							->result();
							
		$codeco = $db	->select("TRUNC(SH.DATE_SEND_CODECO) as DATE_SEND_CODECO
								,TRIM(SD.VISIT_NAME) AS VISIT_NAME
								,TRIM(SD.VOYAGE_IN) AS VOYAGE_IN
								,TRIM(SD.VOYAGE_OUT) AS VOYAGE_OUT
								,TRIM(SH.VISIT_ID) AS VISIT_ID
								,TRIM(SH.BL_NUMBER) AS BL_NUMBER
								,TRIM(SH.DIRECTION) AS DIRECTION
								,COUNT(1) as CODECO")
							->from("CARTOS_CARGO SH")
							->join('CARTOS_SHIP_VISIT SD', 'SD.VISIT_ID = SH.VISIT_ID')
							//->where('SH.FLAG_SEND_CODECO', 1)
							//->where("SH.DATE_SEND_CODECO BETWEEN to_date('".$awal. "','yyyy-mm-dd') and to_date('".$akhir."','yyyy-mm-dd')")
							->where("SD.ARRIVAL BETWEEN to_date('".$awal. "','yyyy-mm-dd') and to_date('".$akhir."','yyyy-mm-dd')")
							//->where('SH.DATE_SEND_CODECO >=', $awal)
							//->where('SH.DATE_SEND_CODECO <=', $akhir)
							->group_by('TRUNC(SH.DATE_SEND_CODECO)
										,TRIM(SD.VISIT_NAME)
										,TRIM(SD.VOYAGE_IN)
										,TRIM(SD.VOYAGE_OUT)
										,TRIM(SH.VISIT_ID)
										,TRIM(SH.BL_NUMBER)
										,TRIM(SH.DIRECTION)')
							->order_by('TRIM(SD.VISIT_NAME),TRIM(SD.VOYAGE_IN)','ASC')
							->get()
							->result();
//echo $db->last_query();
//echo "<br><br><br>";							
		/*
		$datasource2 = $db	->select("TRUNC(SH.DATE_SEND_COARRI) AS DATE_SEND_COARRI,TRUNC(SH.DATE_SEND_CODECO) AS DATE_SEND_CODECO,SD.VISIT_NAME,SD.VOYAGE_IN,SD.VOYAGE_OUT,SH.VISIT_ID,SH.BL_NUMBER,SH.DIRECTION,
									(SELECT COUNT(DATE_SEND_CODECO) FROM CARTOS_CARGO
									WHERE DATE_SEND_CODECO BETWEEN '".$awal."' AND '".$akhir."'
									AND VISIT_ID = SH.VISIT_ID AND BL_NUMBER= SH.BL_NUMBER
									AND DIRECTION = SH.DIRECTION) AS CODECO,
									(SELECT COUNT(DATE_SEND_COARRI) FROM CARTOS_CARGO
									WHERE DATE_SEND_COARRI BETWEEN '".$awal."' AND '".$akhir."'
									AND VISIT_ID = SH.VISIT_ID AND BL_NUMBER= SH.BL_NUMBER
									AND DIRECTION = SH.DIRECTION) AS COARRI")
							->from("CARTOS_CARGO SH")
							->join('CARTOS_SHIP_VISIT SD', 'SD.VISIT_ID = SH.VISIT_ID')
							//->where('SH.FLAG_SEND_COARRI', 1)
							->where("SH.DATE_SEND_COARRI BETWEEN '".$awal. "' and '".$akhir."' or SH.DATE_SEND_CODECO BETWEEN '".$awal."' and '".$akhir."'")
							//->where('SH.DATE_SEND_COARRI >=', $awal)
							//->where('SH.DATE_SEND_COARRI <=', $akhir)
							->group_by('TRUNC(SH.DATE_SEND_COARRI),TRUNC(SH.DATE_SEND_CODECO),SD.VISIT_NAME,SD.VOYAGE_IN,SD.VOYAGE_OUT,SH.VISIT_ID,SH.BL_NUMBER,SH.DIRECTION')
							->order_by('TRUNC(SH.DATE_SEND_COARRI),TRUNC(SH.DATE_SEND_CODECO)','ASC')
							->get()
							->result();
		*/			
		$datasource2 = $db	->select("TRUNC(SH.DATE_SEND_COARRI) AS DATE_SEND_COARRI,TRUNC(SH.DATE_SEND_CODECO) AS DATE_SEND_CODECO,SD.VISIT_NAME,SD.VOYAGE_IN,SD.VOYAGE_OUT,SH.VISIT_ID,SH.BL_NUMBER,SH.DIRECTION,
									sum(case when date_send_codeco is not null then 1 else 0 end) AS CODECO,
									sum(case when date_send_coarri is not null then 1 else 0 end) AS COARRI")
							->from("CARTOS_CARGO SH")
							->join('CARTOS_SHIP_VISIT SD', 'SD.VISIT_ID = SH.VISIT_ID')
							//->where('SH.FLAG_SEND_COARRI', 1)
							//->where("SH.DATE_SEND_COARRI BETWEEN to_date('".$awal. "','yyyy-mm-dd') and to_date('".$akhir."','yyyy-mm-dd') or SH.DATE_SEND_CODECO BETWEEN to_date('".$awal."','yyyy-mm-dd') and to_date('".$akhir."','yyyy-mm-dd')")
							->where("SD.ARRIVAL BETWEEN to_date('".$awal. "','yyyy-mm-dd') and to_date('".$akhir."','yyyy-mm-dd')")
							//->where('SH.DATE_SEND_COARRI >=', $awal)
							//->where('SH.DATE_SEND_COARRI <=', $akhir)
							->group_by('TRUNC(SH.DATE_SEND_COARRI),TRUNC(SH.DATE_SEND_CODECO),SD.VISIT_NAME,SD.VOYAGE_IN,SD.VOYAGE_OUT,SH.VISIT_ID,SH.BL_NUMBER,SH.DIRECTION')
							->order_by('SD.VISIT_NAME,SD.VOYAGE_IN','ASC')
							->get()
							->result();		
		//echo $db->last_query();
		//print_r($datasource2);
		//die;
							
		// Pack
		if($output_format == 'HTML'){
			$out->custom_view = 'cartos/tps_periode_html';
		}else{
			$out->custom_view = 'cartos/tps_periode_excel';
			//$out->custom_view = 'cartos/tps_periode_html';
		}
		
		$out->awal = $awal;
		$out->akhir = $akhir;
		$out->datasource = $datasource2;
		$out->coarri = $coarri;
		$out->codeco = $codeco;
		//$out->statistik = $statistik;
		//echo "<pre>";
		//print_r($out);
		//echo "</pre>";		
		return $out;
	}
	
	
	
	
}