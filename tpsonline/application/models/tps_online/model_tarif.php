<?php
require_once('./application/models/base/modelbase.php');
class Model_tarif extends ModelBase {

	public $sortable = array(
        'TERMINAL' => 'Terminal',       
        'PERIODE_MULAI' => 'Periode Mulai',
        'PERIODE_SELESAI' => 'Periode Selesai'   
       
    );
    // Datagrid Searchable Fields
    public $searchable = array(  
        'TERMINAL' => 'Terminal',        
        'KOMODITI' => 'Komoditi',        
        'PELAYANAN' => 'Pelayanan',
        'GOLONGAN' => 'Golongan' ,
        'TAHUN' => 'Tahun'  
       
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
        $out->datatarif = $this->db->distinct()->select('ID_TARIF,KOMODITI,PELAYANAN,GOLONGAN,TYPE,TAHUN,TARIF_1,TARIF_2,TERMINAL') 
                ->from('DASHBOARD_TARIF_TW')             
                ->order_by('ID_TARIF', 'DESC')
                ->get()
                ->result_array();
 
        $this->siapkanDB(true);

            $out->actualRows = $this->db->select('count("ID_TARIF") AS "numRows"', FALSE)
            ->get('DASHBOARD_TARIF_TW')
            ->row()->numRows;
                  
        return $out;
    }

	
    public function get($id) {
        return $this->db
                        ->where('ID_TARIF', $id)
                        ->get('DASHBOARD_TARIF_TW')->row();
    }


	public function select_ds($where = array()){

			$datasource = $this->db->select('PERIODE_BULAN, TAHUN')->where($where)->get('DASHBOARD_ZERO_DEFECT')->result();

			return $datasource;
		}


}



	