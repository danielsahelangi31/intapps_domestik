<?php
require_once('./application/models/base/modelbase.php');

class Notifikasi_Model extends ModelBase{
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

		$out = new StdClass();

		$sql = "select a.visit_id, visit_name, voyage_in, arrival, departure, jml_bl, jml_vin, jml_sukses_codeco, jml_sukses_coarri
				from cartos_ship_visit a left join (select
				visit_id, count(distinct bl_number) jml_bl,count(*) jml_vin,
				sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco,
				sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri
				from cartos_cargo
				where  DIRECTION = 1
				group by visit_id) b on a.visit_id = b.visit_id
				where to_char(arrival,'yyyymm') = '201810'
				and visit_direction = '1' order by arrival desc";

		$query = $this->db->query($sql);
		$out->datasource = $query->result();

		// $this->siapkanDB(true);

		// $sql = "select count(*) AS numRows
		// 		from cartos_ship_visit a left join (select
		// 		visit_id, count(distinct bl_number) jml_bl,count(*) jml_vin,
		// 		sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco,
		// 		sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri
		// 		from cartos_cargo
		// 		where  DIRECTION = 1
		// 		group by visit_id) b on a.visit_id = b.visit_id
		// 		where to_char(arrival,'yyyymm') = '201810'
		// 		and visit_direction = '1' order by arrival desc";
		//
		// $query = $this->db->query($sql)->row();
		// $out->actualRows = $query->NUMROWS;

		return $out;
	}
	public function select_search($users_id,$month,$year){
		$this->siapkanDB();
		if($month<10){
			$month = '0'.$month;
		}
		$key = $year.$month;
		$tgl = date("Y").date("m");
		$q="and to_char(arrival,'yyyymm') = '$tgl'";
		if($key!=0){
			$q = "and to_char(arrival,'yyyymm') = '$key'";
		}

		$out = new StdClass();

		$sql = "select a.visit_id, visit_name, voyage_in, arrival, departure, jml_bl, jml_vin, jml_sukses_codeco, jml_sukses_coarri,
				jml_car, jml_kms, jml_valid_car, jml_valid_kms
				from cartos_ship_visit a left join (select visit_id, count(distinct bl_number) jml_bl,count(*) jml_vin, 
				sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco, 
				sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri,
				sum(case when type_cargo = 'CBU' then 1 else 0 end) jml_car,
				sum(case when type_cargo <> 'CBU' and type_cargo is not null then 1 else 0 end) jml_kms,
				sum(nvl(is_valid_car,0)) jml_valid_car,
				sum(nvl(is_valid_kms,0)) jml_valid_kms
				from cartos_cargo where DIRECTION = 1 
				group by visit_id) b on a.visit_id = b.visit_id where visit_direction = '1' 
				".$q." 
				order by arrival desc";

		$query = $this->db->query($sql);
		$out->datasource = $query->result();

		// $this->siapkanDB(true);

		// $sql = "select count(*) AS numRows
		// 		from cartos_ship_visit a left join (select
		// 		visit_id, count(distinct bl_number) jml_bl,count(*) jml_vin,
		// 		sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco,
		// 		sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri
		// 		from cartos_cargo
		// 		where  DIRECTION = 1
		// 		group by visit_id) b on a.visit_id = b.visit_id
		// 		where to_char(arrival,'yyyymm') = '201810'
		// 		and visit_direction = '1' order by arrival desc";
		//
		// $query = $this->db->query($sql)->row();
		// $out->actualRows = $query->NUMROWS;

		return $out;
	}

	public function select_by_visit($userId,$visitID){
		$this->siapkanDB();
		
		$out = new StdClass();

		$sql = "select visit_id, nvl(a.bl_number,'BL_KOSONG') bl_number, type_cargo, count(*) jml_vin,
				sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco,
				sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri, 
				is_discharge_kms, is_discharge_car, is_gateout_kms, is_gateout_car, remark, remark2
				from cartos_cargo a left join (select distinct bl_number, max(case when svc_instance = 'CoarriCodeco_Kemasan' and kd_dok = 'DISCHARGE' then 1 else 0 end) is_discharge_kms,
                max(case when svc_instance = 'CoCoCarTer' and kd_dok = 'DISCHARGE' then 1 else 0 end) is_discharge_car,
                max(case when svc_instance = 'CoarriCodeco_Kemasan' and kd_dok = 'GATE-OUT' then 1 else 0 end) is_gateout_kms,
                max(case when svc_instance = 'CoCoCarTer' and kd_dok = 'GATE-OUT' then 1 else 0 end) is_gateout_car from tps_online_trans_log
                group by bl_number
                ) b on a.bl_number = b.bl_number
				where  DIRECTION = 1 and visit_id = '$visitID'
				group by visit_id, nvl(a.bl_number,'BL_KOSONG'), type_cargo,
                is_discharge_kms, is_discharge_car, is_gateout_kms, is_gateout_car, remark, remark2
				order by visit_id, type_cargo";

		$query = $this->db->query($sql);
		$out->datasource = $query->result();
		return $out;
	
	}
	
	public function select_by_vin($userId,$vinID){
		$this->siapkanDB();
		
		$out = new StdClass();

		$sql = "select visit_ids, nvl(bl_number,'BL_KOSONG') bl_number, type_cargo, count(*) jml_vin,VIN,sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco,
				sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri
				from cartos_cargo
				where  DIRECTION = 1 and VIN = '$vinID'
				group by visit_id, nvl(bl_number,'BL_KOSONG') bl_number, type_cargo,vin order by visit_id, type_cargo";

		$query = $this->db->query($sql);
		$out->datasource = $query->result();
		return $out;
	
	}
	public function select_by_bl($userId,$blID){
		$blID = str_replace("%20"," ",$blID);
		$this->siapkanDB();
		
		$out = new StdClass();

		$sql = "SELECT * FROM (select inner_query.*, rownum rnum FROM (SELECT * FROM V_BL_EDII WHERE VISIT_DIRECTION = '1' AND DIRECTION_TYPE = '1' AND BL_NUMBER LIKE '%$blID%' escape '!' ) inner_query WHERE rownum < 11);";

		$query = $this->db->query($sql);
		$out->datasource = $query->result();
		//print_r($out);
		return $out;
	
	}
	 public function selectHistory($users_id,$key,$visit_id,$type) {
        $this->siapkanDB();

        if (!$this->sort) {

		}
		if($key != 'BL_KOSONG'){
			$key = str_replace("%20"," ",$key);
			$where1 = "and a.bl_number = '".$key."'";
		} else {
			//$key = str_replace("%20"," ",$key);
			$where1 = "and a.bl_number is  null";
		}
		$out = new StdClass();
// 		$q = "SELECT T1.*, T2.*, T3.KD_DOK, T3.ID_TRX, T3.KD_DOK_INOUT, T3.DT 
// FROM CARTOS_CARGO T1 LEFT JOIN CARTOS_SHIP_VISIT T2 ON T2.VISIT_ID = T1.VISIT_ID 
// LEFT JOIN TPS_ONLINE_TRANS_LOG T3 ON T3.BL_NUMBER = T1.BL_NUMBER WHERE T1.BL_NUMBER = '$key' AND T1.VISIT_ID ='$visit_id'  AND T1.TYPE_CARGO = '$type'";
		
		$q = "select 
				a.visit_id, a.vin, a.bl_number, a.bl_number_date, a.CUSTOMS_NUMBER, a.CUSTOMS_DATE, a.type_cargo, 
				case when a.type_cargo = 'CBU' then a.weight else d.bruto end weight, dts_onterminal, dts_left, number_police, 
				a.flag_send_codeco, flag_send_coarri, dts_announced, direction, direction_type, b.no_sppb, c.jns_kms, d.jumlah,
				e.inward_bc11, e.inward_bc11_date, e.outward_bc11, e.outward_bc11_date, a.remark, a.no_npe, a.npe_date, a.consignee_id,
				a.consignee_name, a.make_name merk, a.weight bruto, a.in_out_doc, a.in_out_doc_date, a.kd_dok,
				e.discharger_port, e.next_port
				from cartos_cargo a left join cartos_tps_sppb_pib_h b on trim(a.bl_number) = trim(b.no_bl_awb)
				left join cartos_tps_sppb_pib_dk c on b.car = c.car
				left join bl_cargo_type_mapping d on a.bl_number = d.bl_number
				left join cartos_ship_visit e on a.visit_id = e.visit_id
				where a.visit_id = '".$visit_id."' ".$where1."";
		
		$out->datasource= $this->db->query($q)->result();
		// echo $this->db->last_query();die();
		// echo $this->db->last_query();die();
        /*
        $out->datasource = $this->db->select('*')
                ->from('V_BL_EDII')
                ->where('VISIT_DIRECTION', '1') // 1 = international, 2 = domestic
                ->where('DIRECTION_TYPE', '1') // 1 = inbound, 2 = outbound
                ->get()
                ->result();*/

        $this->siapkanDB(true);
        
		$out->actualRows = count($out->datasource);

        return $out;
    }
	public function findCargo($blID){
		$this->db->where('BL_NUMBER',$blID);
		$list = $this->db->get('CARTOS_CARGO')->result();
	}



	public function select_by_visit_export($userId,$visitID){
		$this->siapkanDB();
		
		$out = new StdClass();

		$sql = "select visit_id, nvl(bl_number,'BL_KOSONG') bl_number, type_cargo, count(*) jml_vin,
				sum(case when flag_send_codeco = 0 then 0 else 1 end) jml_sukses_codeco,
				sum(case when flag_send_coarri = 0 then 0 else 1 end) jml_sukses_coarri, 
				remark2, remark
				from cartos_cargo
				where  DIRECTION = 2 and visit_id = '$visitID'
				group by visit_id, nvl(bl_number,'BL_KOSONG'), type_cargo, remark2, remark order by visit_id, type_cargo";

		$query = $this->db->query($sql);
		// echo $this->db->last_query();die();
		$out->datasource = $query->result();
		return $out;
	
	}


}
