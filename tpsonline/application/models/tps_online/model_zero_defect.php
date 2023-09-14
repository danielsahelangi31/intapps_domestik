<?php
require_once('./application/models/base/modelbase.php');
class Model_Zero_Defect extends ModelBase {

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
                            SUM("LQ_GATE_1_BACK_KCY") LQ_GATE_1_BACK_KCY, 
                            SUM("LQ_GATE_1_QUARANTINE") LQ_GATE_1_QUARANTINE, 
                            SUM("LQ_GATE_2")LQ_GATE_2, 
                            SUM("LQ_GATE_3")LQ_GATE_3,
                            SUM("CARGO_DEFECT")CARGO_DEFECT,
                            MAX("TERMINAL") TERMINAL , MAX("MAKER") MAKER                
                            '))
                ->from('DASHBOARD_ZERO_DEFECT')
                ->group_by('PERIODE_BULAN, TAHUN, TERMINAL, MAKER') 
                ->order_by('PERIODE_BULAN', 'ASC')
                ->get()
                ->result_array();
 
        $this->siapkanDB(true);

            $out->actualRows = $this->db->select('count("id_zero") AS "numRows"', FALSE)
            ->get('DASHBOARD_ZERO_DEFECT')
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
                        ->get('DASHBOARD_ZERO_DEFECT')->row();
    }

	public function select_ds($where = array()){

			$datasource = $this->db->select('PERIODE_BULAN, TAHUN')->where($where)->get('DASHBOARD_ZERO_DEFECT')->result();

			return $datasource;
		}




}



	