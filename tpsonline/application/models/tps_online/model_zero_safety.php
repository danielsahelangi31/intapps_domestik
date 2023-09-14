<?php
require_once('./application/models/base/modelbase.php');
class Model_Zero_Safety extends ModelBase {

	public $sortable = array(
        'periode_bulan' => 'Periode(Bulan)',
        'tahun' => 'Tahun',
        'terminal' => 'Terminal',
        'maker' => 'Maker',
        'accident' => 'Accident',
        'incident' => 'Incident',
        'unit_impact' => 'Unit Impact'
    
    );
    // Datagrid Searchable Fields
    public $searchable = array(
        'periode_bulan' => 'Periode(Bulan)',
        'tahun' => 'Tahun',
        'maker' => 'Maker',
        'terminal' => 'Terminal'

       
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
           $out->datazero = $this->db->select(('MAX("PERIODE_BULAN") PERIODE_BULAN, 
           MAX("TAHUN") TAHUN,                      
           SUM("ACCIDENT") ACCIDENT, 
           SUM("INCIDENT") INCIDENT, 
           SUM("UNIT_IMPACT")UNIT_IMPACT,        
           MAX("TERMINAL") TERMINAL , 
           MAX("MAKER") MAKER                
           '))
        ->from('DASHBOARD_ZERO_SAFETY')
        ->group_by('PERIODE_BULAN, TAHUN, TERMINAL, MAKER') 
        ->order_by('PERIODE_BULAN', 'ASC')
                ->get()
                ->result_array();
 
        $this->siapkanDB(true);

            $out->actualRows = $this->db->select('count("id_zero") AS "numRows"', FALSE)
            ->get('DASHBOARD_ZERO_SAFETY')
            ->row()->numRows;
                  
        return $out;
    }

    public function getMakers()
    {
        $db_ilcs = $this->load->database('ikt_cardom', TRUE);

        $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

        $datasource = $db_ilcs->query($query)->result();
        return $datasource;
        
    }

    public function get($id) {
        return $this->db
                        ->where('id_zero', $id)
                        ->get('DASHBOARD_ZERO_SAFETY')->row();
    }

   	public function select_ds($where = array()){

			$datasource = $this->db->select('PERIODE_BULAN, TAHUN')->where($where)->get('DASHBOARD_ZERO_DEFECT')->result();

			return $datasource;
		}

   
}



	