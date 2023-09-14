<?php
require_once('./application/models/base/modelbase.php');
class Model_berthing extends ModelBase {

	public $sortable = array(
        'VESSEL_CODE' => 'Kode Kapal',
        'VISIT_NAME' => 'Nama Kapal',
        'VOYAGE_IN' => 'Voyage IN',
		'VOYAGE_OUT' => 'Voyage OUT',

    );
    // Datagrid Searchable Fields
    public $searchable = array(
        'VESSEL_CODE' => 'Kode Kapal',
        'VISIT_NAME' => 'Nama Kapal',
        'VOYAGE_IN' => 'Voyage IN', 
        'VOYAGE_OUT' => 'Voyage OUT',  
        'ETAA' => 'ETA (mm-yyyy)',
        'ETDD' => 'ETD (mm-yyyy)', 
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
//        $out->datamanual = $this->db->where('VISIT_DIRECTION', '1')->order_by('VOYAGE', 'DESC')->get('VES_VOYAGE')->result();
            $out->databerth = $this->db->distinct()->select('*') 
            ->from("STAGING_CARTOS_SHIP_VISIT") 
            ->where('ETA IS NOT NULL')   
            ->where('VISIT_NAME IS NOT NULL')    
            ->order_by("ETA", 'DESC')
            ->get()
            ->result();
   
        $this->siapkanDB(true);
        $out->actualRows = $this->db->select('count(*) AS "numRows"', FALSE)
            ->get('STAGING_CARTOS_SHIP_VISIT')
            ->row()->numRows;

            // $out->actualRows = $this->db->select('count(DISTINCT "VISIT_ID") AS "numRows"', FALSE)
            // ->from('STAGING_CARTOS_SHIP_VISIT')  
            // ->group_by('VISIT_NAME') 
            // ->order_by('VISIT_NAME', 'DESC')
            // ->get()
            // ->row()->numRows;


        return $out;
    }

	
    public function get($id) {
        return $this->db
                        ->where('id_berthing', $id)
                        ->get('DASHBOARD_BERTHING_PLAN')->row();
    }

	public function select_ds($where = array()){

			$databerth = $this->db->select('VISIT_NAME, VISIT_ID, VESSEL_CODE, ETA, ETB, ETD, VOYAGE_IN, VOYAGE_OUT')->where($where)->get('STAGING_CARTOS_SHIP_VISIT')->result();

			return $databerth;
		}

	function get_autoComplete($keyword) {

        $data = $this->db->query("select * from VES_VOYAGE where ID_VESSEL like '%$keyword'");
        return $data->result();
    }

    public function select_type_kade($where = array()){
        $con = $this->load->database('ikt_postgree', TRUE);
    
        $datasource = $con->select('KADE')->where($where)->get('MST_KADE')->result();
    
        return $datasource;
      //  var_dump($datasource);die();
    }





    
	
}


	