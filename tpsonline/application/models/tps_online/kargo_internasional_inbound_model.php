<?php

require_once('./application/models/base/modelbase2.php');

class Kargo_Internasional_Inbound_Model extends ModelBase2 {

    // Datagrid Sortable Fields
    public $sortable = array(
        'BL_NUMBER' => 'No BL',
        'BL_NUMBER_DATE' => 'Tanggal BL',
        'CUSTOMS_NUMBER' => 'Customs No.',
        'VISIT_ID' => 'Visit ID',
       // 'COUNTOF' => 'Jumlah VIN',
    );
    // Datagrid Searchable Fields
    public $searchable = array(
        'BL_NUMBER' => 'No BL',
        'BL_NUMBER_DATE' => 'Tanggal BL',
        'CUSTOMS_NUMBER' => 'Customs No.',
        'VISIT_ID' => 'Visit ID',
       // 'COUNTOF' => 'Jumlah VIN',
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
        //print_r($_GET);
        //echo $this->uri->segment(4);
        $out->datasource = $this->db->select('*')
                ->from('V_BL_EDII A')
                ->where('A.VISIT_DIRECTION', '1') // 1 = international, 2 = domestic
                ->where('A.DIRECTION', '1') // 1 = inbound, 2 = outbound
                ->get()
                ->result();
                //print_r($this->db->last_query());    
        $this->siapkanDB(true);
        $out->actualRows = $this->db->select('count(VISIT_ID) AS "numRows"', FALSE)
                        ->where('A.VISIT_DIRECTION', '1') // 1 = international, 2 = domestic
                        ->where('A.DIRECTION', '1') // 1 = inbound, 2 = outbound
                        ->get('V_BL_EDII A')
                        ->row()->numRows;

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
