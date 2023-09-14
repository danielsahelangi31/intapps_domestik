<?php
require_once('./application/models/base/modelbase.php');
class Model_Form_Manual extends ModelBase {

	public $sortable = array(
        'VISIT_NAME' => 'Nama Kapal',
        'VISIT_ID' => 'Visit ID',
        'VESSEL_CODE' => 'Call Sign',
        'VOYAGE_IN' => 'Voyage In',
        'ETA' => 'Estimated Time Arrival (mm-yyyy)',
        'ETB' => 'Estimated Time Arrival (mm-yyyy)',
        'VESSEL_STATUS' => 'Visit Status',
    );
    // Datagrid Searchable Fields

    public $searchable = array(
        'VESSEL_NAME' => 'Nama Kapal',
        'PBM' => 'PBM',
        'KADE_NAME' => 'KADE',
        'VOY_IN' => 'Voyage',
        'ETAA' => 'ETA (mm-yyyy)',
        'ATA' => 'ATA (mm-yyyy)',
        'ATB' => 'ATB (mm-yyyy)',
        'ATD' => 'ATD (mm-yyyy)',
	       
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

       $out->datamanual = $this->db->distinct()->select('"VESSEL_NAME","VOY_IN","VOY_OUT","CALL_SIGN","OPERATOR_ID","OPERATOR_NAME","ETA","ETD","ATA","ATD","ETB","ATB","WORK_COMMENCE","WORK_COMPLETE","ID_VVD","ID_VESSEL","VOYAGE","KADE_NAME","PBM","ID_TERMINAL"') 
       ->from("STAGING_VES_VOYAGE")
       ->where('("ATB" IS NOT NULL OR "ATD" IS NOT NULL)')    
       ->order_by("ETA", 'DESC')
       ->get()
       ->result();

        $this->siapkanDB(true);
        $out->actualRows = $this->db->select('count("ID_VVD") AS "numRows"', FALSE)
        ->get('STAGING_VES_VOYAGE')
        ->row()->numRows;

        return $out;
    }

	
    public function get($id) {
        
             return $this->db->join('DASHBOARD_BM_DETAIL T2', 'ID_MONITORING_DETAIL = ID_HEADER', 'left')               
                        ->where('ID_HEADER', $id)
                        ->get('DASHBOARD_BM_HEADER T1')->row();
                        
                
    }


    public function gets($id, $voyage) {
        $vessel = str_replace('%20',' ',$id);

        return $this->db->select('VESSEL_NAME,VOY_IN,VOY_OUT,CALL_SIGN,OPERATOR_ID,OPERATOR_NAME,ETA,ETD,ATA,ATD,ETB,ATB,WORK_COMMENCE,WORK_COMPLETE,ID_VVD,ID_VESSEL,VOYAGE,KADE_NAME,PBM,ID_TERMINAL')               
                   ->where('VESSEL_NAME', $vessel)
                   ->where('VOY_IN', $voyage)
                   ->get('STAGING_VES_VOYAGE')->row();

    }


	public function select_ds($where = array()){

			$datasource = $this->db->select('VESSEL_NAME, PBM, ETA, ETB, ETD, VOYAGE')->where($where)->get('STAGING_VES_VOYAGE')->result();

			return $datasource;
		}

        public function select_type_activity($where = array()){
            $con = $this->load->database('ikt_postgree', TRUE);
        
            $datasource = $con->select('NAMA_ACTIVITY')->where($where)->get('DASHBOARD_MST_ACTIVITY')->result();
        
            return $datasource;

        }   
    
    
        public function select_type_shift($where = array()){
            $con = $this->load->database('ikt_postgree', TRUE);
        
            $datasource = $con->select('NAMA_SHIFT')->where($where)->get('DASHBOARD_MST_SHIFT')->result();
        
            return $datasource;
        }


}


	