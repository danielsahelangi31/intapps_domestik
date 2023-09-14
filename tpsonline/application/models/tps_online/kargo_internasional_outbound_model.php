<?php

require_once('./application/models/base/modelbase.php');

class Kargo_Internasional_Outbound_Model extends ModelBase {

    // Datagrid Sortable Fields
    public $sortable = array(
        'BL_NUMBER' => 'No BL',
        'BL_NUMBER_DATE' => 'Tanggal BL',
        'CUSTOMS_NUMBER' => 'Customs No.',
        'VISIT_ID' => 'Visit ID',
        //'COUNTOF' => 'Jumlah VIN',
    );
    // Datagrid Searchable Fields
    public $searchable = array(
        'BL_NUMBER' => 'No BL',
        'BL_NUMBER_DATE' => 'Tanggal BL',
        'CUSTOMS_NUMBER' => 'Customs No.',
        'VISIT_ID' => 'Visit ID',
        //'COUNTOF' => 'Jumlah VIN',
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

    public function select($users_id) {
        $this->siapkanDB();

        if (!$this->sort) {
            
        }

        $out = new StdClass();
//        $out->datasource = $this->db->get('V_BL c')->result();
        $out->datasource = $this->db->select('A.BL_NUMBER, COUNT (A.BL_NUMBER) AS COUNTOF, A.BL_NUMBER_DATE, A.CUSTOMS_NUMBER, A.VISIT_ID, B.VISIT_DIRECTION, A.DIRECTION')
                ->from('CARTOS_CARGO A')
                ->join('CARTOS_SHIP_VISIT B', 'A.VISIT_ID = B.VISIT_ID', 'inner')
                ->where('A.BL_NUMBER IS NOT NULL', NULL, FALSE)
                ->where('A.BL_NUMBER_DATE IS NOT NULL', NULL, FALSE)
                ->where('A.BL_NUMBER_DATE <= SYSDATE + 100', NULL, FALSE)
                ->where('B.VISIT_DIRECTION', '1') // 1 = international, 2 = domestic
                ->where('A.DIRECTION', '2') // 1 = inbound, 2 = outbound
                ->group_by('A.BL_NUMBER, A.BL_NUMBER_DATE, A.CUSTOMS_NUMBER, A.NO_NPE, A.VISIT_ID, B.VISIT_DIRECTION, A.DIRECTION')
                ->order_by('A.BL_NUMBER_DATE', 'DESC')
                ->get()
                ->result();

        $this->siapkanDB(true);
        $out->actualRows = $this->db->select('A.BL_NUMBER, COUNT (A.BL_NUMBER) AS COUNTOF, A.BL_NUMBER_DATE, A.CUSTOMS_NUMBER, A.VISIT_ID, B.VISIT_DIRECTION, A.DIRECTION')
                ->from('CARTOS_CARGO A')
                ->join('CARTOS_SHIP_VISIT B', 'A.VISIT_ID = B.VISIT_ID', 'inner')
                ->where('A.BL_NUMBER IS NOT NULL', NULL, FALSE)
                ->where('A.BL_NUMBER_DATE IS NOT NULL', NULL, FALSE)
                ->where('A.BL_NUMBER_DATE <= SYSDATE + 100', NULL, FALSE)
                ->where('B.VISIT_DIRECTION', '1') // 1 = international, 2 = domestic
                ->where('A.DIRECTION', '2') // 1 = inbound, 2 = outbound
                ->group_by('A.BL_NUMBER, A.BL_NUMBER_DATE, A.CUSTOMS_NUMBER, A.NO_NPE, A.VISIT_ID, B.VISIT_DIRECTION, A.DIRECTION')
                ->order_by('A.BL_NUMBER_DATE', 'DESC')
                ->get()
                ->num_rows();

        return $out;
    }

    public function select_unsent($VISIT_ID) {
        return $this->db->select()
                        ->where('VISIT_ID', $VISIT_ID)
                        ->where('FLAG_SEND_COARRI', 0)
                        ->get('CARTOS_CARGO c')->result();
    }

    public function get($id) {
        $cleanid = urldecode($id);
        //echo $id; die;
        return $this->db->join('CARTOS_SHIP_VISIT T2', 'T2.VISIT_ID = T1.VISIT_ID', 'left')
                        ->where('BL_NUMBER', $cleanid)
                        ->get('CARTOS_CARGO T1')->result();
    }

    public function insert($result) {
        $this->db->trans_start();



        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function update($id) {
        
    }

}
