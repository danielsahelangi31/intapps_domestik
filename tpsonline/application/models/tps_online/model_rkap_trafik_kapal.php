<?php
require_once('./application/models/base/modelbase.php');
class Model_rkap_trafik_kapal extends ModelBase {

	public $sortable = array(
        'periode_bulan' => 'Periode(Bulan)',
        'tahun' => 'Tahun',
        'lq_gate_1_back_kcy' => 'LQ Gate1 (Back KCY)',
        'lq_gate_1_quarantine' => 'LQ Gate1 (Quarantine)',
        'lq_gate_2' => 'LQ Gate 2',
        'lq_gate_3' => 'LQ Gate 3',
        'cargo_defect' => 'CARGO DEFECT'
    
    );
    // Datagrid Searchable Fields
    public $searchable = array(
        // 'PELAYARAN' => 'Pelayaran',
        'TERMINAL' => 'Terminal',
        'TAHUN' => 'Tahun',

       
    );

	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}

	public function select($users_id) {
        $this->siapkanDB();

        if (!$this->sort) {
            
        }

        $out = new StdClass();
        $out->datatrafik = $this->db->distinct()->select('TERMINAL,PELAYARAN,TAHUN,SATUAN,JANUARI,FEBRUARI,MARET,APRIL,MEI,JUNI,JULI,AGUSTUS,SEPTEMBER,OKTOBER,NOVEMBER,DESEMBER,id_trafik') 
                ->from('DASHBOARD_RKAP_TRAFFIK')             
                ->order_by('id_trafik', 'ASC')
                ->get()
                ->result_array();
 
        $this->siapkanDB(true);

            $out->actualRows = $this->db->select('count("id_trafik") AS "numRows"', FALSE)
            ->get('DASHBOARD_RKAP_TRAFFIK')
            ->row()->numRows;
                  
        return $out;
    }

	
    public function get($id) {
        return $this->db
                        ->where('id_trafik', $id)
                        ->get('DASHBOARD_RKAP_TRAFFIK')->row();
    }


	public function select_ds($where = array()){

			$datasource = $this->db->select('PERIODE_BULAN, TAHUN')->where($where)->get('DASHBOARD_ZERO_DEFECT')->result();

			return $datasource;
		}

	function get_autoComplete($keyword) {

        $data = $this->db->query("select * from VES_VOYAGE where ID_VESSEL like '%$keyword'");
        return $data->result();
    }

}



	