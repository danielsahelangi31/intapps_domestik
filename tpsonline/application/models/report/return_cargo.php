<?php

class Return_cargo extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $this->db2 = $this->load->database(ILCS_TPS_ONLINE, TRUE);
    }

    function _get_datatables_query($rc_status, $bulan, $tahun) {
        $table = 'RETURN_CARGO';
        $column_order = array(null,'VIN','TRUCK_CODE', 'CREATED_DT');
        $column_search = array('LOWER(VIN)','LOWER(TRUCK_CODE)');
        $order = array('VIN' => 'asc');

        $this->db2->select('VIN, TRUCK_CODE, CREATED_DT');
        $this->db2->from($table);
        $this->db2->where("RC_STATUS = '".$rc_status."'");
        if($tahun != 'ALL')
        $this->db2->where("TO_DATE(TO_CHAR(CREATED_DT, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY')");
        if($bulan != 'ALL')
        $this->db2->where("TO_DATE(TO_CHAR(CREATED_DT, 'MM'),'MM') = TO_DATE('".$bulan."','MM')");


        if($_POST["search"]["value"])
        {
            $this->db2->where("(LOWER(VIN) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(TRUCK_CODE) LIKE '%".strtolower($_POST["search"]["value"])."%')", NULL, FALSE);
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

    function get_datatables($rc_status, $bulan, $tahun)
    {
        $this->_get_datatables_query($rc_status, $bulan, $tahun);

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

    function count_filtered($rc_status, $bulan, $tahun)
    {
        $this->_get_datatables_query($rc_status, $bulan, $tahun);
        $query = $this->db2->count_all_results();
        return $query;
    }

    public function count_all($rc_status, $bulan, $tahun)
    {
        $this->_get_datatables_query($rc_status, $bulan, $tahun);
        $query = $this->db2->count_all_results();
        return $query;
    }
}