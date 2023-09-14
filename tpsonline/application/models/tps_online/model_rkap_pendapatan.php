<?php
require_once('./application/models/base/modelbase.php');
class Model_rkap_pendapatan extends ModelBase {

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
        'KOMODITI' => 'Komoditi',    
        'PELAYANAN' => 'Pelayanan',
        'GOLONGAN' => 'Golongan',
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
          
        $out->databarang = $this->db->distinct()->select('KOMODITI,PELAYANAN,GOLONGAN,TAHUN,JANUARI,FEBRUARI,MARET,APRIL,MEI,JUNI,JULI,AGUSTUS,SEPTEMBER,OKTOBER,NOVEMBER,DESEMBER,id_pendapatan') 
                ->from('DASHBOARD_RKAP_PENDAPATAN')             
                ->order_by('TAHUN', 'DESC')
                ->get()
                ->result_array();
 
        $this->siapkanDB(true);

            $out->actualRows = $this->db->select('count("id_pendapatan") AS "numRows"', FALSE)
            ->get('DASHBOARD_RKAP_PENDAPATAN')
            ->row()->numRows;
                  
        return $out;
    }

	
    public function get($id) {
        return $this->db
                        ->where('id_pendapatan', $id)
                        ->get('DASHBOARD_RKAP_PENDAPATAN')->row();
    }


	public function select_ds($where = array()){

			$datasource = $this->db->select('PERIODE_BULAN, TAHUN')->where($where)->get('DASHBOARD_ZERO_DEFECT')->result();

			return $datasource;
		}
        

	

}



	