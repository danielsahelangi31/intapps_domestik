<?php
require_once('./application/models/base/modelbase.php');
class Model_laparusbarang extends ModelBase {

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

    public function get_data_laparusbarang($id,$end)
	{    
		$con = $this->load->database('ikt_postgree', TRUE);

        $dates = "'yyyy-mm'";
        $domestik = 'DOMESTIK';
        $domestik = "'$domestik'";
        $id = "'$id'";
        $end = "'$end'";
		$dataArusDom = 'select distinct to_char("PERIODE",'.$dates.') periode
        from "MART_RKAP_ARUS_BARANG" 
        where "TERMINAL" ='.$domestik.' AND to_char("PERIODE",'.$dates.')  BETWEEN '.$id.' AND '.$end.'
        group by to_char("PERIODE",'.$dates.')';
        
        $data = $con->query($dataArusDom);	
		$res = array(
	                "data"  => $data->result_array()
	                
	            );

	    return $res;
	}

    
    public function get_data_laparusintr($id,$end)
	{

		$con = $this->load->database('ikt_postgree', TRUE);

        $dates = "'yyyy-mm'";
        $internasional = 'INTERNASIONAL';
        $internasional = "'$internasional'";
        $id = "'$id'";
        $end = "'$end'";
		$dataArusIntr = 'select distinct to_char("PERIODE",'.$dates.') periode
        from "MART_RKAP_ARUS_BARANG"
        where "TERMINAL" ='.$internasional.' AND to_char("PERIODE",'.$dates.')  BETWEEN '.$id.' AND '.$end.'
        group by to_char("PERIODE",'.$dates.')';
        
        $data = $con->query($dataArusIntr);	
		$res = array(
	                "data"  => $data->result_array()
	                
	            );
	
	    return $res;
	}


    
    public function get_data_arusbarangintr($id,$end)
	{
  		$con = $this->load->database('ikt_carter', TRUE);

		$dataArusInt = "SELECT  
        to_char(TGL_NOTA ,'yyyy-mm') periode, snc.JN_NOTA, mm.NM_NOTA, snc.EI, sum(JUMLAH) UNIT,sum(M3) M3, sum(TONNAGE) TONNAGE
        FROM  TTD_CARGO_STEVEDORING a join TTH_CARGO_STEVEDORING b ON a.STEVEDORING_ID =b.STEVEDORING_ID --pakai stevedoring_id
        join VESSEL_SCHEDULE c ON b.DILA_ID =c.DILA_ID  
        JOIN SIMKEU_NOTA_CARTER snc ON c.KD_KAPAL =snc.KD_KAPAL 
        JOIN MST_MODUL mm ON snc.JN_NOTA =mm.JN_NOTA 
        WHERE  to_char(TGL_NOTA,'yyyy-mm') BETWEEN  '$id' and '$end'
        and snc.JN_NOTA NOT IN ('CAR21', 'CAR23', 'CAR29') 
        AND snc.EI='I'
        group by to_char(TGL_NOTA ,'yyyy-mm'), snc.JN_NOTA, mm.NM_NOTA, snc.EI ";
        
        $data = $con->query($dataArusInt);	
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


	