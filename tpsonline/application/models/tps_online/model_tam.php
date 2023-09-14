<?php

class Model_tam extends CI_Model{

	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}

	public function get_data_tam()
	{
		$con = $this->load->database('ilcs_cartos', TRUE);

		$sql = "select a.VIN, bl_number, consignee_name_1 as LogiscticCompany,
			CASE TO_CHAR(a.dts_announced,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			    WHEN ',000000000' 
			        THEN
			        ''
			    else TO_CHAR(a.dts_announced,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			END as AnnouncedDate,
			CASE TO_CHAR(a.dts_onterminal,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			    WHEN ',000000000' 
			        THEN
			        ''
			    else TO_CHAR(a.dts_onterminal,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			END as OnTerminalDate,
			CASE TO_CHAR(a.dts_loaded,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			    WHEN ',000000000' 
			        THEN
			        ''
			    else TO_CHAR(a.dts_loaded,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			END as LoadedDate,
			CASE TO_CHAR(a.dts_left,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			    WHEN ',000000000' 
			        THEN
			        ''
			    else TO_CHAR(a.dts_left,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			END as LeftDate,
			CASE TO_CHAR(arrival_1,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			    WHEN ',000000000' 
			        THEN
			        ''
			    else TO_CHAR(arrival_1,'DD-MM-YYYY HH24.MI.SS')||',000000000'
			END as ATADate,
			TO_CHAR(sysdate,'DD-MM-YYYY HH24.MI.SS')||',000000000' as UpdateDate
			from codeco_coarri@ctos_link a join cartos_cargo b on a.vin = b.vin 
			where a.make_name='TOYOTA'  and 
			a.consignee_name_1='IKT LOGISTICS'  and
			a.direction='1' and
			a.dts_announced >= sysdate-40";

		$data = $con->query($sql);
	
		$res = array(
	                "data"  => $data->result_array()
	                
	            );

	    return $res;
	}

}