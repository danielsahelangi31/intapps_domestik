<?php

class Truck2 extends CI_Model {

    private $_db;

    public function __construct() {
        parent::__construct();
        $this->_db = $this->load->database(ILCS_LOG_AUTOGATE, TRUE);
    }

    public function data_truck($select,$select2,$search,$start=null,$end=null,$limit=null) {
        // $sql = "SELECT $select FROM dash_truck WHERE 1=1";
        $where = '';
        if(!empty($search)) {
          $where .= " and lower(plat_no || driver || ' ' || direction || ' ' || status_sppb || trucking || ' ' || gate_in || ' ' || gate_out || ' ' || ' ' || visit_id) like lower('%".$search."%')";
        }
        if(!empty($start) && !empty($end)) {
            $start = date('Y/m/d 00:00:00', strtotime($start));
            $end = date('Y/m/d 00:00:00', strtotime($end));

            $where .= "and lastchange
                BETWEEN
                    to_date('$start','YYYY/MM/DD HH24:MI:SS')
                AND
                    to_date('$end','YYYY/MM/DD HH24:MI:SS')";
        }
        
        if(empty($limit)) {
          $sql = "SELECT $select 
                  FROM (
                      SELECT ROW_NUMBER() OVER (
                      ORDER BY 
                      lastchange DESC
                       ) nomor, $select2 
                      FROM dash_truck WHERE 1 = 1 $where
                  ) dash_truck 
                  WHERE 1=1
                  ";
        } else {
          $sql = "SELECT $select 
                  FROM (
                      SELECT $select2 
                      FROM dash_truck WHERE 1 = 1 $where $limit
                  ) dash_truck 
                  WHERE 1=1
                  ORDER BY LASTCHANGE DESC
                  ";
        }

        return $sql;
    }

    public function search_order_truck($select,$select2,$search = null, $start = null, $end = null,$periode_start = null, $periode_end = null) {
        $limit = "";
        if(!empty($start) && !empty($end) && empty($search) && empty($periode_start) && empty($periode_end)) {
            $query = "select nr from (SELECT ROW_NUMBER() OVER (ORDER BY lastchange DESC) nomor,nr
              from t_visit@DBL_TOCTOS2) where nomor between $start and $end";
            $visits = $this->_db->query($query)->result();
            $list_visit = "";
            foreach ($visits as $row) {
                $list_visit .=  "'".$row->NR."',";
            }
            $list_visit = substr($list_visit, 0, -1);
            $limit = "and visit_id in ($list_visit)";
        }
        
        $sql = $this->data_truck($select,$select2,$search,$periode_start,$periode_end, $limit);

        // if(!empty($start) && !empty($end)) {
            // $sql .= " and nomor between $start and $end";
        // }
        
        // if(!empty($search)) {
        //   $sql .= " and lower(plat_no || driver || ' ' || direction || ' ' || status_sppb || trucking || ' ' || gate_in || ' ' || gate_out || ' ' || ' ' || visit_id) like lower('%".$search."%')";
        // }
       

        // if(isset($_POST['order'])) {
        //  $sql = $sql." order by ".$order[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir']."";
        // } else {
        //  $sql = $sql." order by last_change2 desc";
        // }

        return $sql;
    }



    public function list_truck($search, $sort=null, $order=null, $offset=null, $limit=null, $periode_start=null, $periode_end=null,$type=null) {
        $select = "visit_id,trucking,plat_no,driver,direction,status_sppb,gate_in,gate_out,cam_1in,cam_2in,cam_3in,cam_1out,cam_2out,cam_3out,completion,lastchange";

        $select2 = "visit_id,trucking,plat_no,driver,direction,status_sppb,gate_in,gate_out,cam_1in,cam_2in,cam_3in,cam_1out,cam_2out,cam_3out,completion,lastchange";

        // if($)
        // return $type;
        if($type != null){
            $start = 1;
            $end = 1000000;
            $sql = $this->search_order_truck($select,$select2,$search, $start, $end,$periode_start,$periode_end);

            $hasil = $this->_db->query($sql)->result_array();   
        } else{
            $start = $offset + 1;
            $end = $limit + $offset;
            $sql = $this->search_order_truck($select,$select2,$search, $start, $end,$periode_start,$periode_end);
            // var_dump($sql);die;
            $hasil = $this->_db->query($sql)
                            ->result();   
        }
        // return $this->_db->last_query();
        return $hasil;

    }

    public function count_all_truck($search=null,$start = null, $end = null) {
        $select = "count(visit_id) jml";
        $select2 = "visit_id";
        $sql = $this->search_order_truck($select,$select2,$search,$start,$end);


        $hasil = $this->_db->query($sql)->row()->JML;
        // return $this->_db->last_query();
        return $hasil;
    }

    // public function count_all_truck() {
    //     $select = "count(visit_id) jml";
    //     $sql = $this->data_truck($select);
    //     return $this->_db->query($sql)->row()->JML;
    // }
}
