<?php

require_once('./application/models/base/modelbase.php');

class Kargolist_Internasional_Inbound_Model extends ModelBase {

    // Datagrid Sortable Fields
    public $sortable = array(
        'VIN' => 'Nama Kapal',
        'BL_NUMBER' => 'No BL',
        'BL_NUMBER_DATE' => 'Tanggal BL',
        'CONSIGNEE_NAME' => 'Nama Consignee',
        'LOGISTIC_COMPANY' => 'Nama Logistik',
        'DTS_ONTERMINAL' => 'On Terminal',
        'DTS_LOADED' => 'Loaded',
        'DTS_LEFT' => 'Left',
    );
    // Datagrid Searchable Fields
    public $searchable = array(
        'VIN' => 'Nama Kapal',
        'VISIT_ID' => 'Visit ID',
        'BL_NUMBER' => 'No BL',
        'BL_NUMBER_DATE' => 'Tanggal BL',
        'CONSIGNEE_NAME' => 'Nama Consignee',
        'LOGISTIC_COMPANY' => 'Nama Logistik',
        'DTS_ONTERMINAL' => 'On Terminal',
        'DTS_LOADED' => 'Loaded',
        'DTS_LEFT' => 'Left',
    );
    // Enumeration values
    public $DIRECTION = array(
        '1' => 'IMPORT',
        '2' => 'EXPORT'
    );

    public function __construct() {
        parent::__construct();
    }

    public function set_db($db) {
        $this->db = $db;
    }

    public function select($id) {
        $this->siapkanDB();
        $cleanid = urldecode($id);

        if (!$this->sort) {
            
        }
        //like('title', 'match', 'after');
        $out = new StdClass();
        $out->datasource = $this->db->like('BL_NUMBER', $cleanid, 'after')
                        ->get('CARTOS_CARGO c')->result();
                        // echo $this->db->last_query();
        //print_r ($out); die;
        //echo '<pre>';
        //print_r($out->datasource);
        //echo '</pre>';
        //die;
        $this->siapkanDB(true);
        $out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
                        ->like('BL_NUMBER', $cleanid, 'after')
                        ->get('CARTOS_CARGO c')
                        ->row()->numRows;
        // echo $this->db->last_query();die();
                        
        return $out;
    }

    public function select_unsent($VISIT_ID) {
        return $this->db->select()
                        ->where('VISIT_ID', $VISIT_ID)
                        ->where('FLAG_SEND_COARRI', 0)
                        ->get('CARTOS_CARGO c')->result();
    }

    public function get($id) {
        return $this->db->join('CARTOS_SHIP_VISIT T2', 'T2.VISIT_ID = T1.VISIT_ID', 'left')
                        ->join('DO_IKT T3', 'T3.BL_NO = T1.BL_NUMBER', 'left')
                        ->where('T1.VIN', $id)
                        ->get('CARTOS_CARGO T1')->row();
    }

    public function get_($id,$blnumber) {
        return $this->db->query("select 
a.visit_id, a.vin, a.bl_number, to_char(a.bl_number_date,'YYYY-MM-DD') bl_number_date, a.CUSTOMS_NUMBER, a.CUSTOMS_DATE, a.type_cargo, 
case when a.type_cargo = 'CBU' then a.weight else d.bruto end weight, to_char(dts_onterminal,'YYYY-MM-DD') dts_onterminal, to_char(dts_left,'YYYY-MM-DD') dts_left, number_police, 
a.flag_send_codeco, flag_send_coarri, to_char(dts_announced,'YYYY-MM-DD') dts_announced, direction, direction_type, b.no_sppb, c.jns_kms, d.jumlah,
e.inward_bc11, to_char(e.inward_bc11_date,'YYYY-MM-DD') inward_bc11_date, e.outward_bc11, to_char(e.outward_bc11_date,'YYYY-MM-DD') outward_bc11_date, a.remark, a.no_npe, to_char(a.npe_date,'YYYY-MM-DD') npe_date, a.consignee_id,
a.consignee_name, a.make_name merk, a.weight bruto, a.in_out_doc, to_char(a.in_out_doc_date,'YYYY-MM-DD') in_out_doc_date, a.kd_dok,
e.discharger_port, e.next_port
from cartos_cargo a left join cartos_tps_sppb_pib_h b on trim(a.bl_number) = trim(b.no_bl_awb)
left join cartos_tps_sppb_pib_dk c on b.car = c.car
left join bl_cargo_type_mapping d on a.bl_number = d.bl_number
left join cartos_ship_visit e on a.visit_id = e.visit_id
where a.vin = '".$id."' AND a.bl_number = '".$blnumber."'")->row();
    }

    public function insert($result) {
        $this->db->trans_start();



        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function update($id) {
        
    }

}
