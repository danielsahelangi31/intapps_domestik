<?php

require_once('./application/models/base/modelbase.php');

class Log_Pengiriman_Model extends ModelBase {

    // Datagrid Sortable Fields
    public $sortable = array(
        'VIN' => 'VIN',
        'BL_NUMBER' => 'BL Number',
        'TYPE_CARGO' => 'Type',
        'DTS_ONTERMINAL' => 'On Terminal (dd-mm-yyyy)',
        'DTS_LEFT' => 'Left (dd-mm-yyyy)',
        'RESPONSE_COARRI' => 'Response On Terminal',
        'RESPONSE_CODECO' => 'Response Left',
    );
    // Datagrid Searchable Fields
    public $searchable = array(
        'VIN' => 'VIN',
        'A.BL_NUMBER' => 'BL Number',
        'TYPE_CARGO' => 'Type',
        'DTS_ONTERMINAL' => 'On Terminal (dd-mm-yyyy)',
        'DTS_LEFT' => 'Left (dd-mm-yyyy)',
        'C.ACK' => 'Response On Terminal',
        'D.ACK' => 'Response Left',
    );

    public function __construct() {
        parent::__construct();
    }

    public function set_db($db) {
        $this->db = $db;
    }

    public function select_ds($where = array()) {

        $datasource = $this->db->select('VISIT_ID, VISIT_NAME')->where($where)->where('ETA IS NOT NULL', NULL, FALSE)->like('VISIT_ID', 'VES', 'AFTER')->order_by("ETA", "desc")->get('CARTOS_SHIP_VISIT')->result();

        return $datasource;
    }

    public function select_type_cargo($where = array()) {

        $datasource = $this->db->select('CUSTOMS_CODE, DESCRIPTION')->where($where)->get('CUSTOMS_TYPE_MAPPING')->result();

        return $datasource;
    }

    public function select($users_id) {
        $this->siapkanDB();

        if (!$this->sort) {
            
        }

        $out = new StdClass();
//        $out->datasource = $this->db->where('VISIT_DIRECTION', '1')->order_by('ETA', 'DESC')->get('CARTOS_SHIP_VISIT sv')->result();
//        $out->datasource = $this->db->distinct()->select('A.*')
//                ->from('CARTOS_SHIP_VISIT A')
//                ->join('CARTOS_CARGO B', 'A.VISIT_ID = B.VISIT_ID', 'left')
//                ->where('A.VISIT_DIRECTION', '1')
//                ->where('B.DIRECTION_TYPE', '1')
//                ->order_by('ETA', 'DESC')
//                ->get()
//                ->result();
//        B.VISIT_NAME, B.VESSEL_CODE, B.VOYAGE_IN, B.VOYAGE_OUT, B.ETA, B.ETD, 
//            B.VISIT_DIRECTION, 
//            A.VIN, A.BL_NUMBER, A.TYPE_CARGO, A.DTS_ONTERMINAL, A.DTS_LEFT, 
//            A.FLAG_SEND_COARRI, A.DATE_SEND_COARRI, A.REF_NUMBER_COARRI, C.ACK AS RESPONSE_COARRI,
//            A.FLAG_SEND_CODECO, A.DATE_SEND_CODECO, A.REF_NUMBER_CODECO, D.ACK AS RESPONSE_CODECO
        $out->datasource = $this->db->select('A.VIN, A.BL_NUMBER, A.TYPE_CARGO, A.DTS_ONTERMINAL, A.DTS_LEFT, 
            A.FLAG_SEND_COARRI, A.DATE_SEND_COARRI, A.REF_NUMBER_COARRI, C.ACK AS RESPONSE_COARRI,
            A.FLAG_SEND_CODECO, A.DATE_SEND_CODECO, A.REF_NUMBER_CODECO, D.ACK AS RESPONSE_CODECO')
                ->from('CARTOS_CARGO A')
                ->join('CARTOS_SHIP_VISIT B', 'A.VISIT_ID = B.VISIT_ID', 'inner')
                ->join('TPS_ONLINE_TRANS_LOG C', 'A.REF_NUMBER_COARRI = C.ID_TRX', 'left')
                ->join('TPS_ONLINE_TRANS_LOG D', 'A.REF_NUMBER_CODECO = D.ID_TRX', 'left')
                ->where('B.VISIT_DIRECTION', '1')
                ->order_by('B.ETA', 'DESC')
                ->get()
                ->result();
        
        $this->siapkanDB(true);
//        $out->actualRows = $this->db->select('count(DISTINT VISIT_ID) AS "numRows"', FALSE)
//                        ->where('VISIT_DIRECTION', '1')
//                        ->get('CARTOS_SHIP_VISIT sv')
//                        ->row()->numRows;
        $out->actualRows = $this->db->select('count(A.VISIT_ID) AS "numRows"', FALSE)
                        ->from('CARTOS_CARGO A')
                        ->join('CARTOS_SHIP_VISIT B', 'A.VISIT_ID = B.VISIT_ID', 'inner')
                        ->join('TPS_ONLINE_TRANS_LOG C', 'A.REF_NUMBER_COARRI = C.ID_TRX', 'left')
                        ->join('TPS_ONLINE_TRANS_LOG D', 'A.REF_NUMBER_CODECO = D.ID_TRX', 'left')
                        ->where('B.VISIT_DIRECTION', '1')
                        ->get()
                        ->row()->numRows;

        return $out;
    }

    public function get($id) {
        return $this->db
                        ->where('VISIT_ID', $id)
                        ->get('CARTOS_SHIP_VISIT')->row();
    }

    function get_autoComplete($keyword) {

        $data = $this->db->query("select * from cartos_ship_visit where visit_id like '%$keyword'");
        return $data->result();
    }

    public function update($id) {
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

    public function finalize_visit($visit_id) {
        
    }

}
