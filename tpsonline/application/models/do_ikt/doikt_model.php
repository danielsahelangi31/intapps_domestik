<?php

class Doikt_model extends CI_Model {

    var $table = 'DO_IKT';
    var $column_order = array('MSG_ID', 'DO_NO', 'BL_NO', 'VIN', 'CONSIGNEE', 'CUSTOMER', 'CARRIER', 'VESSEL_NAME', 'VESSEL_CALLSIGN', 'VESSEL_VOYAGE_IN', 'VESSEL_VOYAGE_OUT', 'PORT_LOADING', 'PORT_DISCHARGE', 'ATA', 'VIN_DESCRIPTION', 'RECORD_TIME', 'GROSS_WEIGHT', 'DO_RELEASE_DATE', 'DO_EXPIRED_DATE');
    var $column_search = array('MSG_ID', 'DO_NO', 'BL_NO', 'VIN', 'CONSIGNEE', 'CUSTOMER', 'CARRIER', 'VESSEL_NAME', 'VESSEL_CALLSIGN', 'VESSEL_VOYAGE_IN', 'VESSEL_VOYAGE_OUT');
    var $order = array('VIN' => 'asc');

    public function __construct() {
        parent::__construct();
        $this->load->database(ILCS_TPS_ONLINE, FALSE, TRUE);
    }

    private function _get_datatables_query() {
        $this->db->from($this->table);

        if(isset($_POST["search"]["value"])) {
            $this->db->or_like("LOWER(MSG_ID)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(DO_NO)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(BL_NO)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(VIN)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(CONSIGNEE)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(CUSTOMER)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(CARRIER)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(VESSEL_NAME)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(VESSEL_CALLSIGN)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(VESSEL_VOYAGE_IN)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(VESSEL_VOYAGE_OUT)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(PORT_LOADING)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(PORT_DISCHARGE)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(ATA)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(VIN_DESCRIPTION)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(RECORD_TIME)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(GROSS_WEIGHT)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(DO_RELEASE_DATE)", strtolower($_POST["search"]["value"]));
            $this->db->or_like("LOWER(DO_EXPIRED_DATE)", strtolower($_POST["search"]["value"]));
        }

        if(isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if(isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            if($_POST['start'] == 0) {
                $this->db->limit($_POST['length'] + 1, $_POST['start']);
            } elseif ($_POST['page'] == $_POST['pages']) {
                $this->db->limit($_POST['length'], $_POST['start']+1);
            } else {
                $this->db->limit($_POST['length'], $_POST['start']);
            }
        }

        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->count_all_results();
        return $query;
    }

    public function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
}