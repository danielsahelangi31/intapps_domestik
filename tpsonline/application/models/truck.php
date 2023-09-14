<?php
require_once('./application/models/base/modelbase.php');

class Truck extends ModelBase
{

	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}

	//start truck
	public function data_truck() {
		$sql = "select ROW_NUMBER() OVER (ORDER BY t1.lastchange2 DESC) nomor, t1.* from(
    select 
      visit_id, trucking, plat_no, driver,
      case
        when cargo_import > 0 then 'IMPORT'
        when cargo_export > 0 then 'EXPORT'
      end direction,
      cargo_export, hold_export, npe_export,
      null status_npe,
      cargo_import, hold_import, sppb_import,
      case
        when cargo_import > 0 and cargo_import = sppb_import then 'COMPLETE'
        when cargo_import > 0 and cargo_import <> sppb_import then 'INCOMPLETE'
      end status_sppb,
      gate_in, gate_out, status, lastchange lastchange2, TO_CHAR(lastchange, 'DD-MON-YYYY HH24:MM')lastchange
    from (
      select
        nr visit_id,
        (select name from t_organization where id = (select owner_id from t_truck where id = vs.truck_id)) Trucking,
        transportmeanname plat_no,
        driver,
        ( select count(*) from t_handlingunitontrip ht, t_trip tp where 
          ht.trip_id = tp.id and
          tp.visit_id = vs.id 
          and incoming = 1
        ) cargo_export,
        0 npe_export,
        ( select count(nullif(holdcount,0)) from t_handlingunit hu, t_handlingunitontrip ht, t_trip tp where
            hu.id = ht.handlingunit_id and
            tp.id = ht.trip_id and
            tp.visit_id = vs.id and 
            tp.incoming = 1
        ) hold_export,
        ( select count(*) from t_handlingunitontrip ht, t_trip tp where 
          ht.trip_id = tp.id and
          tp.visit_id = vs.id 
          and incoming = 0
        ) cargo_import,
        ( select count(customsnumber) from t_orderinformation oi, t_handlingunit hu, t_handlingunitontrip ht, t_trip tp where
            oi.id = hu.orderinformation_id and
            hu.id = ht.handlingunit_id and
            tp.id = ht.trip_id and
            tp.visit_id = vs.id and 
            tp.incoming = 0
        ) sppb_import,
        ( select count(nullif(holdcount,0)) from t_handlingunit hu, t_handlingunitontrip ht, t_trip tp where
            hu.id = ht.handlingunit_id and
            tp.id = ht.trip_id and
            tp.visit_id = vs.id and 
            tp.incoming = 0
        ) hold_import,
        arrival gate_in,
        departure gate_out,
        case visitstatus 
          when 0 then 'ANNOUNCED'
          when 2 then 'ARRIVED'
          when 3 then 'OPERATIONAL'
          when 4 then 'COMPLETED'
          when 5 then 'LEFT'
          when 10 then 'DELETED'
        end status,
        lastchange
      from t_visit vs
      where transportmean = 3 
      
      -- inner limit to speed up
      and id in 
        (select id from
          (select id from t_visit where transportmean = 3 order by lastchange desc) 
        where rownum < 101)
    ) dt
    order by lastchange desc)t1 where 1=1";

		return $sql;
	}

	public function search_order_truck() {
		$sql = $this->data_truck();

		if($_POST['search']['value']) {
			$sql = $sql." and lower(t1.visit_id || ' ' || t1.trucking || t1.plat_no || ' ' || t1.driver || ' ' || t1.direction || ' ' || t1.cargo_export || ' ' || t1.hold_export || ' ' || t1.npe_export || ' ' || t1.status_npe || ' ' || t1.cargo_import || ' ' || t1.hold_import || ' ' || t1.sppb_import || ' ' || t1.status_sppb || ' ' || t1.gate_in || ' ' || t1.gate_out || ' ' || t1.status || ' ' || t1.lastchange) like lower('%".$_POST['search']['value']."%')";
		}

		$order = array(
			0 => "t1.visit_id",
			1 => "t1.trucking",
			2 => "t1.plat_no",
			3 => "t1.driver",
			4 => "t1.gate_in",
			5 => "t1.gate_out",
			6 => "t1.lastchange2",
			7 => "t1.status_sppb",
			8 => "t1.status",
			9 => "t1.direction",
			10 => "t1.npe_export",
			11 => "t1.hold_export",
			12 => "t1.cargo_export",
			13 => "t1.status_npe",
			14 => "t1.cargo_import",
			15 => "t1.hold_import",
			16 => "t1.sppb_import"
		);

		if(isset($_POST['order'])) {
			$sql = $sql." order by ".$order[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir']."";
		} else {
			$sql = $sql." order by t1.lastchange2 desc";
		}

		return $sql;
	}

	public function list_truck()
	{
		$sql = $this->search_order_truck();
		$start = (int)$_POST['start']+1;
		$length = (int)$_POST['length']+(int)$_POST['start'];
		return $this->db->query("select * from (".$sql.") t1 where t1.nomor between ".$start." and ".$length."")->result();
	}

	public function count_filtered_truck()
	{
		$sql = $this->search_order_truck();
		return $this->db->query("select count(*) jml from(".$sql.")")->row()->JML;
	}

	public function count_all_truck()
	{
		$sql = $this->data_truck();
		return $this->db->query("select count(*) jml from(".$sql.")")->row()->JML;
	}
	//end truck

}