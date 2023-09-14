<?php

class Visit_id extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $this->db2 = $this->load->database(ILCS_TPS_ONLINE, TRUE);
    }

    function _get_datatables_query($status, $bulan, $tahun) {
        $table = 'T_VISIT';
        $column_order = array(null, 'LASTCHANGE', 'NR','TRUCK_ID');
        $column_search = array('LOWER(NR)','LOWER(TRUCK_ID)','LOWER(CATEGORY3)');
        $order = array('T_VISIT.LASTCHANGE' => 'desc');

        $this->db->select('T_VISIT.NR, T_VISIT.TRUCK_ID, T_VISIT.LASTCHANGE, T_HANDLINGUNIT.CATEGORY3');
        $this->db->from($table);
        $this->db->join('T_TRIP','T_VISIT.ID = T_TRIP.VISIT_ID','left');
        $this->db->join('T_HANDLINGUNITONTRIP','T_TRIP.ID = T_HANDLINGUNITONTRIP.TRIP_ID','left');
        $this->db->join('T_HANDLINGUNIT','T_HANDLINGUNIT.ID = T_HANDLINGUNITONTRIP.HANDLINGUNIT_ID','left');
        $this->db->where("T_VISIT.VISITSTATUS = '".$status."'");
        if($tahun != 'ALL')
        $this->db->where("TO_DATE(TO_CHAR(T_VISIT.LASTCHANGE, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY')");
        if($bulan != 'ALL')
        $this->db->where("TO_DATE(TO_CHAR(T_VISIT.LASTCHANGE, 'MM'),'MM') = TO_DATE('".$bulan."','MM')");


        if($_POST["search"]["value"])
        {
            $this->db->where("(LOWER(T_VISIT.NR) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(T_HANDLINGUNIT.CATEGORY3) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(T_VISIT.TRUCK_ID) LIKE '%".strtolower($_POST["search"]["value"])."%')", NULL, FALSE);
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($order))
        {
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($status, $bulan, $tahun)
    {
        $this->_get_datatables_query($status, $bulan, $tahun);

        if($_POST['length'] != -1)
        {
            if($_POST['start'] == 0){
                $this->db->limit($_POST['length']+1, $_POST['start']);
            }
            elseif ($_POST['page'] == $_POST['pages']){
                $this->db->limit($_POST['length'], $_POST['start']+1);
            }
            else{
                $this->db->limit($_POST['length'], $_POST['start']);
            }
        }

        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($status, $bulan, $tahun)
    {
        $this->_get_datatables_query($status, $bulan, $tahun);
        $query = $this->db->count_all_results();
        return $query;
    }

    public function count_all($status, $bulan, $tahun)
    {
        $this->_get_datatables_query($status, $bulan, $tahun);
        $query = $this->db->count_all_results();
        return $query;
    }
}