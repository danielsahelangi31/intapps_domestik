<?php
require_once('./application/models/base/modelbase.php');
class Model_lap_trafik_kapal extends ModelBase {

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

    public function get_data_laporan($id,$end)
	{
   
		$con = $this->load->database('ikt_postgree', TRUE);
     
        $dates = "'yyyy-mm'";
        $tp1 = "'5TP1'";
        $tp3 = "'5TP3'";
        $tp4 = "'5TP4'";
        $expr = "'EXPR'";
        $tp5 = "'5TP5'";
        $id = "'$id'";
        $end = "'$end'";

        $dataTrafikDom = 'SELECT DISTINCT to_char(mtk."PERIODE" ,'.$dates.') PERIODE
        FROM "MART_TRF_KAPAL" mtk 
        JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
        WHERE to_char(mtk."PERIODE",'.$dates.') BETWEEN '.$id.' AND '.$end.'
        group by to_char(mtk."PERIODE" ,'.$dates.') ';

		$data = $con->query($dataTrafikDom);
	
		$res = array(
	                "data"  => $data->result_array()
	                
	            );     
         
	    return $res;
	}

    public function get_data_intr($id,$end)
	{
		$con = $this->load->database('ikt_postgree', TRUE);
        $tp2 = "'5TP2'";
        $id = "'$id'";
        $end = "'$end'";
        $dates = "'yyyy-mm'";

        $dataTrafikIntr = 'SELECT DISTINCT to_char(mtk."PERIODE" ,'.$dates.') PERIODE
        FROM "MART_TRF_KAPAL" mtk 
        JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
        WHERE to_char(mtk."PERIODE",'.$dates.') BETWEEN '.$id.' AND '.$end.'
        group by to_char(mtk."PERIODE" ,'.$dates.') ';
        
		$data = $con->query($dataTrafikIntr);
	
       
		$res = array(
	                "data"  => $data->result_array()
	                
	            );     
          
	    return $res;
	}

	public function select_ds($where = array()){

			$datalap = $this->db->select('PERIODE, SHIPPING_AGENT, INSERT_DATE')->where($where)->get('MART_TRF_KAPAL')->result();

			return $datalap;
		}


	
}


	