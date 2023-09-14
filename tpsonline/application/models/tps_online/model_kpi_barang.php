<?php
require_once('./application/models/base/modelbase.php');
class Model_kpi_barang extends ModelBase {

	public $sortable = array(
        'TERMINAL' => 'Terminal',
        'PERIODE_MULAI' => 'Periode Mulai',
        'PERIODE_SELESAI' => 'Periode Selesai',
        'USH' => 'USH',
        'BOR' => 'BOR(%)',
        'YOR' => 'YOR(%)',
        'ET_BT' => 'ET/BT(%)',
        'ZERO_DEFECT' => 'Zero Defect',
        'TRAFFIC_KAPAL' => 'Traffic Kapal',
        'SLA_PRANOTABM' => 'SLA Pranota BM',
    
    );
    // Datagrid Searchable Fields
    public $searchable = array(        
        'TERMINAL' => 'Terminal',
        'PERIODE' => 'Periode',     
              
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
          
        $out->databarang = $this->db->distinct()->select('TERMINAL,PERIODE,USH,BOR,YOR,ET_BT,ZERO_DEFECT,TRAFFIC_KAPAL,SLA_PRANOTABM,id_kpi,SAFETY') 
                ->from('DASHBOARD_KPI')             
                ->order_by('id_kpi', 'DESC')
                ->get()
                ->result_array();
 
        $this->siapkanDB(true);

            $out->actualRows = $this->db->select('count("id_kpi") AS "numRows"', FALSE)
            ->get('DASHBOARD_KPI')
            ->row()->numRows;
                  
        return $out;
    }

	
    public function get($id) {
        return $this->db
                        ->where('id_kpi', $id)
                        ->get('DASHBOARD_KPI')->row();
    }


	public function select_ds($where = array()){

			$datasource = $this->db->select('PERIODE_BULAN, TAHUN')->where($where)->get('DASHBOARD_ZERO_DEFECT')->result();

			return $datasource;
		}



}



	