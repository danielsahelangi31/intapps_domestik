<?php
require_once('./application/models/base/modelbase.php');
class Model_lap_pendapatan extends ModelBase {

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

    public function get_data_pendapatan($id,$end)
	{
		$con = $this->load->database('ikt_postgree', TRUE);
        $dates = "'yyyy-mm'";
        $id = "'$id'";
        $end = "'$end'";

		$dataPendapatanDom = 'SELECT distinct to_char("PERIODE",'.$dates.') periode
                            FROM "MART_INCOME_PER_SERVICE"                           
                            WHERE  to_char("PERIODE",'.$dates.') BETWEEN '.$id.' AND '.$end.'
       ';
		$data = $con->query($dataPendapatanDom);
	
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


	