<?php

class Inquiry_model extends CI_Model {

    var $table = 'NPE_PIB';
    var $column_order = array('VIN','MAKE','DESTINATION','MODEL','ENGINE_NO','VESSEL_NAME','ETD','NO_PEB','TGL_PEB','CUSTOMS_NUMBER');
    var $column_search = array('VIN','MAKE','DESTINATION','MODEL','ENGINE_NO','VESSEL_NAME','ETD','NO_PEB','TGL_PEB','CUSTOMS_NUMBER');
    var $order = array('VIN' => 'asc');

    public function __construct()
    {
        parent::__construct();
        $this->load->database(ILCS_TPS_ONLINE, FALSE,TRUE);
    }

    private function _get_datatables_query()
    {

        $this->db->from($this->table);

        if(isset($_POST["search"]["value"]))
        {
            $this->db->or_like("LOWER(VIN)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(DESTINATION)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(MODEL)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(ENGINE_NO)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(VESSEL_NAME)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(ETD)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(NO_PEB)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(TGL_PEB)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(MAKE)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(CUSTOMS_NUMBER)", strtolower($_POST["search"]["value"]));
        }
        if(isset($_POST['order']))
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
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

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->count_all_results();
        return $query;
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

}