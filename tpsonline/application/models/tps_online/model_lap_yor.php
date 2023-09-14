<?php
require_once('./application/models/base/modelbase.php');
class Model_lap_yor extends ModelBase {

	public $sortable = array(
        'VESSEL_CODE' => 'Kode Kapal',
        'VISIT_NAME' => 'Nama Kapal',
 
    );
    // Datagrid Searchable Fields
    public $searchable = array(
        'VESSEL_CODE' => 'Kode Kapal',
        'VISIT_NAME' => 'Nama Kapal',

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
 
    }

	
    public function get($id) {
        return $this->db
                        ->where('ID', $id)
                        ->get('MART_TRF_KAPAL')->row();
    }

    public function get_data_yor($id,$end){

        $con = $this->load->database('ikt_postgree', TRUE);
        $terminal = "'DOM'";
        $dates = "'yyyy-mm-dd'";
        $source = "'CARDOM'";
        $id = "'$id'";
        $end = "'$end'";

		$dataYorDom = 'SELECT DISTINCT TO_CHAR("PERIODE" ,'.$dates.') "PERIODE" from "MART_YOR" 
        WHERE "TERMINAL" = '.$terminal.' and "SOURCE" = '.$source.' and to_char("PERIODE",'.$dates.') BETWEEN  '.$id.' and '.$end.'
        ';

		$data = $con->query($dataYorDom);
	
		$res = array(
	                "data"  => $data->result_array()
	                
	            );

	    return $res;
	}

    
    public function get_yor_intr($id,$end){
        $con = $this->load->database('ikt_postgree', TRUE);
        $terminal = "'INT'";
        $dates = "'yyyy-mm-dd'";
        $source = "'SERVICE_SOAP'";
        $id = "'$id'";
        $end = "'$end'";

		$dataYorIntr = 'SELECT DISTINCT TO_CHAR("PERIODE" ,'.$dates.') "PERIODE" from "MART_YOR" 
        WHERE "TERMINAL" = '.$terminal.' and "SOURCE" = '.$source.' and to_char("PERIODE",'.$dates.') BETWEEN  '.$id.' and '.$end.'
        ';

        $data = $con->query($dataYorIntr);	
		$res = array(
	                "data"  => $data->result_array()
	                
	            );

	    return $res;

    }



}


	