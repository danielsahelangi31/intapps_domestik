<?php
require_once('./application/models/base/modelbase.php');
class Model_form_intr extends ModelBase {

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
        'VISIT_NAME' => 'Nama Kapal', 
        'VESSEL_CODE' => 'Kode Kapal',
        'VOYAGE_IN' => 'Voyage',  
        'POSITION_CODE' => 'Kade',
        'ETAA' => 'ETA (mm-yyyy)',     
        'ARRIVAL' => 'ATA (mm-yyyy)',
        'OPERATIONAL' => 'ATB (mm-yyyy)',
        'DEPARTURE' => 'ATD (mm-yyyy)',
    
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
        $tpt1 = "'TPT1'";
        $tpt2 = "'TPT2'";
         $out = new StdClass();

       $out->dataintr = $this->db->distinct()->select('"VISIT_ID","VISIT_NAME","VESSEL_STATUS","VESSEL_CODE","VOYAGE_IN","VOYAGE_OUT","ETA","ETD","ARRIVAL","OPERATIONAL","COMPLETION","DEPARTURE","POSITION_CODE","VISIT_DIRECTION"') 
       ->from("STAGING_CARTOS_SHIP_VISIT")         
       ->where('"VESSEL_CODE IS NOT NULL') 
       ->where('"OPERATIONAL" IS NOT NULL')
       ->where('("POSITION_CODE" = '.$tpt1.' OR "POSITION_CODE" = '.$tpt2.')')      
       ->order_by("ARRIVAL", 'DESC')
       ->get()
       ->result();

        $this->siapkanDB(true);
        $out->actualRows = $this->db->select('count("VISIT_ID") AS "numRows"', FALSE)
        ->get('STAGING_CARTOS_SHIP_VISIT')
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
   
        return $this->db->select('VISIT_ID,VISIT_NAME,VESSEL_STATUS,VESSEL_CODE,VOYAGE_IN,VOYAGE_OUT,ETA,ETD,ARRIVAL,OPERATIONAL,COMPLETION,DEPARTURE,POSITION_CODE,VISIT_DIRECTION')               
                   ->where('VISIT_NAME', $vessel)
                   ->where('VOYAGE_IN', $voyage)
                   ->get('STAGING_CARTOS_SHIP_VISIT')->row();
  
    }


	public function select_ds($where = array()){

			$datasource = $this->db->select('VISIT_NAME, VESSEL_CODE, VOYAGE_IN, VOYAGE_OUT')->where($where)->get('STAGING_CARTOS_SHIP_VISIT')->result();

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


	