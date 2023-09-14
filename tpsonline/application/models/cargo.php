<?php
require_once('./application/models/base/modelbase.php');

class Cargo extends ModelBase
{

	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}

	//start cargo
	public function data_cargo() {
		
		$sql = "select ROW_NUMBER() OVER (ORDER BY t1.last_change2 DESC) nomor, t1.* from(
      select
      hu.code vin,
      ll.status,
      TO_CHAR(ll.lastchange, 'DD-MON-YYYY HH24:MM') last_change,
      ll.lastchange last_change2,
      hu.category1 jenis,
      hu.category2 direction,
      hu.category3 maker,
      hu.category4 model,
      (select name from t_location where id = oi.finallocation_id) final_location,
      (select name from t_organization where id = oi.controllingorganization_id) logistic_company,
      (select name from t_organization where id = oi.consignee_id) consignee,
      oi.customsnumber sppb,
      -- NPE nyusul
      case when hu.holdcount = 0 then 'Tidak Ada' else 'Ada' end Hold,
      vessel.name vessel,
      truck.licenseplate truck,
      truck.driver driver,
      truck.visit_id visit_id
      from t_handlingunit hu 
      left join 
        (select * from 
          ( select handlingunit_id, lastchange, max(lastchange) over (partition by handlingunit_id) max_lc,
              case localstatus
                when 1 then 'Announced'
                when 4 then 'On Terminal'
                when 5 then 'Loaded'
                when 6 then 'Left'
                when 7 then 'Deleted'
                when 10 then 'Refused'
              end status
            from t_locationlog)
        where lastchange = max_lc) ll on ll.handlingunit_id = hu.id
      left join t_orderinformation oi on hu.orderinformation_id = oi.id
      left join 
        (select handlingunit_id, name from
          ( select handlingunit_id, ve.name, ht.lastchange, max(ht.lastchange) over (partition by handlingunit_id) max_lc
            from t_handlingunitontrip ht
            left join t_trip tp on ht.trip_id = tp.id 
            left join t_visit vs on tp.visit_id = vs.id
            left join t_vessel ve on ve.id = vs.vessel_id
            where vs.transportmean = 1
          ) 
        where lastchange = max_lc) vessel on vessel.handlingunit_id = hu.id
      left join 
        (select handlingunit_id, visit_id, driver, licenseplate from
          ( select handlingunit_id, vs.nr visit_id, vs.driver, tr.licenseplate, ht.lastchange, max(ht.lastchange) over (partition by handlingunit_id) max_lc
            from t_handlingunitontrip ht
            left join t_trip tp on ht.trip_id = tp.id 
            left join t_visit vs on tp.visit_id = vs.id
            left join t_truck tr on tr.id = vs.truck_id
            where vs.transportmean = 3
          )
        where lastchange = max_lc) truck on truck.handlingunit_id = hu.id
        
      -- query biar cepet, pake limit di dalam
      where hu.id in 
        (select handlingunit_id from 
          (select handlingunit_id, max(lastchange) max_lc from t_locationlog group by handlingunit_id order by max_lc desc) 
        where rownum < 101)
      order by ll.lastchange desc)t1 where 1=1";

		return $sql;
	}

	public function search_order_cargo() {
		$sql = $this->data_cargo();

		if($_POST['VISIT_ID'] != null || $_POST['VISIT_ID'] != "") {
			$sql = $sql." and t1.visit_id = '".$_POST['VISIT_ID']."'";
		}

		if($_POST['search']['value']) {
			$sql = $sql." and lower(t1.vin || ' ' || t1.status || t1.last_change2 || ' ' || t1.jenis || ' ' || t1.direction || ' ' || t1.maker || ' ' || t1.model || ' ' || t1.final_location || ' ' || t1.logistic_company || ' ' || t1.consignee || ' ' || t1.sppb || ' ' || t1.Hold || ' ' || t1.vessel || ' ' || t1.truck || ' ' || t1.driver || ' ' || t1.visit_id) like lower('%".$_POST['search']['value']."%')";
		}

		$order = array(
			0 => "t1.logistic_company",
			1 => "t1.truck",
			2 => "t1.driver",
			3 => "t1.last_change",
			4 => "t1.vin",
			5 => "t1.sppb",
			6 => "t1.Hold",
			7 => "t1.status",
			8 => "t1.visit_id",
			9 => "t1.vessel",
			10 => "t1.direction",
			11 => "t1.jenis",
			12 => "t1.maker",
			13 => "t1.model",
			14 => "t1.consignee",
			15 => "t1.final_location"
		);

		if(isset($_POST['order'])) {
			$sql = $sql." order by ".$order[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir']."";
		} else {
			$sql = $sql." order by t1.last_change2 desc";
		}

		return $sql;
	}

	public function list_cargo()
	{
		$sql = $this->search_order_cargo();
		$start = (int)$_POST['start']+1;
		$length = (int)$_POST['length']+(int)$_POST['start'];
		return $this->db->query("select * from (".$sql.") t1 where t1.nomor between ".$start." and ".$length."")->result();
	}

	public function count_filtered_cargo()
	{
		$sql = $this->search_order_cargo();
		return $this->db->query("select count(*) jml from(".$sql.")")->row()->JML;
	}

	public function count_all_cargo()
	{
		$sql = $this->data_cargo();
		return $this->db->query("select count(*) jml from(".$sql.")")->row()->JML;
	}
	//end cargo

}