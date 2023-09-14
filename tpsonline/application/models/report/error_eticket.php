<?php

class Error_eticket extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $this->db2 = $this->load->database(ILCS_TPS_ONLINE, TRUE);
    }

    function _get_datatables_query($status, $bulan, $tahun) {
        $table = 'ANNOUNCETRUCK_REQ';
        $column_order = array(null,'LICENCE_PLATE','SENDER','RESPON_MSG', 'RECORD_TIME');
        $column_search = array('LOWER(LICENCE_PLATE)','LOWER(SENDER)','LOWER(RESPON_MSG)');
        $order = array('RECORD_TIME' => 'desc');

        $this->db2->select('LICENCE_PLATE, SENDER, RESPON_MSG, RECORD_TIME');
        $this->db2->from($table);
        $this->db2->where("RESPON_CODE = '".$status."'");
        if($tahun != 'ALL')
        $this->db2->where("TO_DATE(TO_CHAR(RECORD_TIME, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY')");
        if($bulan != 'ALL')
        $this->db2->where("TO_DATE(TO_CHAR(RECORD_TIME, 'MM'),'MM') = TO_DATE('".$bulan."','MM')");


        if($_POST["search"]["value"])
        {
            $this->db2->where("(LOWER(LICENCE_PLATE) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(SENDER) LIKE '%".strtolower($_POST["search"]["value"])."%')", NULL, FALSE);
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

    function get_datatables($status, $bulan, $tahun)
    {
        $this->_get_datatables_query($status, $bulan, $tahun);

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

    function count_filtered($status, $bulan, $tahun)
    {
        $this->_get_datatables_query($status, $bulan, $tahun);
        $query = $this->db2->count_all_results();
        return $query;
    }

    public function count_all($status, $bulan, $tahun)
    {
        $this->_get_datatables_query($status, $bulan, $tahun);
        $query = $this->db2->count_all_results();
        return $query;
    }
}