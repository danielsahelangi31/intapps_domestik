<?php
require_once('./application/models/base/modelbase.php');

class Log_autogate extends ModelBase
{

	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}

	//start cargo
	public function data_log_autogate() {
		
		$sql = "select ROW_NUMBER() OVER (ORDER BY t1.log_time DESC) nomor, t1.* 
      from(
        select log_time, case gate_type when 'I' then 'IN' when 'O' then 'OUT' end gate_type, 
        license_plate, code, message, visit_id from log_autogate)t1 where 1=1";

		return $sql;
	}

	public function search_order_log_autogate() {
		$sql = $this->data_log_autogate();

		if($_POST['search']['value']) {
			$sql = $sql." and lower(t1.log_time || ' ' || t1.gate_type || t1.license_plate || ' ' || t1.code || ' ' || t1.message || ' ' || t1.visit_id) like lower('%".$_POST['search']['value']."%')";
		}

		$order = array(
			0 => "t1.log_time",
			1 => "t1.gate_type",
			2 => "t1.license_plate",
			3 => "t1.code",
			4 => "t1.message",
			5 => "t1.visit_id"
		);

		if(isset($_POST['order'])) {
			$sql = $sql." order by ".$order[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir']."";
		} else {
			$sql = $sql." order by t1.log_time desc";
		}

		return $sql;
	}

	public function list_log_autogate()
	{
		$sql = $this->search_order_log_autogate();
		$start = (int)$_POST['start']+1;
		$length = (int)$_POST['length']+(int)$_POST['start'];
		return $this->db->query("select * from (".$sql.") t1 where t1.nomor between ".$start." and ".$length."")->result();
	}

	public function count_filtered_log_autogate()
	{
		$sql = $this->search_order_log_autogate();
		return $this->db->query("select count(*) jml from(".$sql.")")->row()->JML;
	}

	public function count_all_log_autogate()
	{
		$sql = $this->data_log_autogate();
		return $this->db->query("select count(*) jml from(".$sql.")")->row()->JML;
	}
	//end log autogate

}