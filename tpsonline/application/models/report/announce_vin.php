<?php

class Announce_vin extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $this->db2 = $this->load->database(ILCS_TPS_ONLINE, TRUE);
    }

    function _get_datatables_query($maker, $bulan, $tahun) {
        $table = 'ANNOUNCEVIN_REQ';
        $column_order = array(null,'DOCUMENT_TRANSFERID', 'FUEL', 'RESPON_CODE', 'SENDER', 'RECORD_TIME');
        $column_search = array('LOWER(DOCUMENT_TRANSFERID)','LOWER(SENDER)','LOWER(FUEL)');
        $order = array('DOCUMENT_TRANSFERID' => 'asc');

        $this->db2->select('DOCUMENT_TRANSFERID, FUEL, RESPON_CODE, SENDER, RECORD_TIME');
        $this->db2->from($table);
        if($maker == 'OTHER'){
            $this->db2->where("SENDER NOT LIKE '%EVLS%'");
            $this->db2->where("SENDER NOT LIKE '%ADLES%'");
            $this->db2->where("SENDER NOT LIKE '%MMKI%'");
            $this->db2->where("SENDER NOT LIKE '%NSDS%'");
        }else{
            $this->db2->where("SENDER like '%".$maker."%'");
        }
        if($tahun != 'ALL')
        $this->db2->where("TO_DATE(TO_CHAR(RECORD_TIME, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY')");
        if($bulan != 'ALL')
        $this->db2->where("TO_DATE(TO_CHAR(RECORD_TIME, 'MM'),'MM') = TO_DATE('".$bulan."','MM')");


        if($_POST["search"]["value"])
        {
            $this->db2->where("(LOWER(DOCUMENT_TRANSFERID) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(FUEL) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(SENDER) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(FUEL) LIKE '%".strtolower($_POST["search"]["value"])."%')", NULL, FALSE);
        }
        if(isset($_POST['order']))
        {
            $this->db2->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($order))
        {
            $this->db2->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables($maker, $bulan, $tahun)
    {
        $this->_get_datatables_query($maker, $bulan, $tahun);

        if($_POST['length'] != -1)
        {
            if($_POST['start'] == 0){
                $this->db2->limit($_POST['length']+1, $_POST['start']);
            }
            elseif ($_POST['page'] == $_POST['pages']){
                $this->db2->limit($_POST['length'], $_POST['start']+1);
            }
            else{
                $this->db2->limit($_POST['length'], $_POST['start']);
            }
        }

        $query = $this->db2->get();
        return $query->result();
    }

    function count_filtered($maker, $bulan, $tahun)
    {
        $this->_get_datatables_query($maker, $bulan, $tahun);
        $query = $this->db2->count_all_results();
        return $query;
    }

    public function count_all($maker, $bulan, $tahun)
    {
        $this->_get_datatables_query($maker, $bulan, $tahun);
        $query = $this->db2->count_all_results();
        return $query;
    }

    function chart_maker($maker, $bulan, $tahun){
        if($bulan != 'ALL') $where_bulan = " AND TO_DATE(TO_CHAR(RECORD_TIME, 'MM'),'MM') = TO_DATE('".$bulan."','MM') ";
        if($tahun != 'ALL') $where_tahun = " AND TO_DATE(TO_CHAR(RECORD_TIME, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY') ";
        $query = "SELECT DISTINCT (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE RESPON_CODE = '200' AND SENDER LIKE '%".$maker."%' ".$where_bulan." ".$where_tahun.") AS Sukses, 
                    (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE (RESPON_CODE != '200' OR RESPON_CODE = '' OR RESPON_CODE IS NULL) AND SENDER LIKE '%".$maker."%' ".$where_bulan." ".$where_tahun.") AS Gagal
                    FROM ANNOUNCEVIN_REQ";
        if($maker == 'OTHER')
            $query = "SELECT DISTINCT (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE RESPON_CODE = '200' AND (SENDER NOT LIKE '%NSDS%' AND SENDER NOT LIKE '%EVLS%' AND SENDER NOT LIKE '%MMKI%' AND SENDER NOT LIKE '%ADLES%') ".$where_bulan." ".$where_tahun.") AS Sukses, 
                    (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE (RESPON_CODE != '200' OR RESPON_CODE = '' OR RESPON_CODE IS NULL) AND (SENDER NOT LIKE '%NSDS%' AND SENDER NOT LIKE '%EVLS%' AND SENDER NOT LIKE '%MMKI%' AND SENDER NOT LIKE '%ADLES%') ".$where_bulan." ".$where_tahun.") AS Gagal
                    FROM ANNOUNCEVIN_REQ";
        $exec  = $this->db2->query($query);
        return $exec->row();
    }
}