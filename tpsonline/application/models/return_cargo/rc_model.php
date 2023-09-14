<?php

class Rc_Model extends CI_Model {

    var $table = DB_CTOS.'.T_HANDLINGUNIT H';
    var $column_order = array(null,'V.VIN','H.CATEGORY3','MD.DESCRIPTION','H.DAMAGECOUNT','H.HOLDCOUNT');
    var $column_search = array('LOWER(V.VIN)','LOWER(H.CATEGORY3)','LOWER(MD.DESCRIPTION)','LOWER(H.DAMAGECOUNT)','LOWER(H.HOLDCOUNT)');
    var $order = array('VIN' => 'asc');

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $this->db2 = $this->load->database(ILCS_TPS_ONLINE, TRUE);
    }

    private function _get_datatables_query()
    {
        if($this->userauth->getLoginData()->sender != 'IKT'){
            $datas = $this->db2->select('AUTOGATE_MAKER.MAKE')
                ->from('AUTOGATE_MAKER')
                ->where('SENDER', $this->userauth->getLoginData()->sender)->get()->result();
        }else{
            $datas = $this->db2->select('AUTOGATE_MAKER.MAKE')
                ->from('AUTOGATE_MAKER')
                ->get()->result();
        }

        $arr = [];
        foreach ($datas as $data) {
            array_push($arr, $data->MAKE);
        }

        $this->db->select('H.DAMAGECOUNT,L.LOCALSTATUS,V.VIN VIN, H.CATEGORY3 CATEGORY3,MD.DESCRIPTION DESCRIPTION, H.HOLDCOUNT HOLDCOUNT');
        $this->db->from($this->table);
        $this->db->join('T_VEHICLE V','V.HANDLINGUNIT_ID = H.ID');
        $this->db->join('T_LOCATIONLOG L','L.HANDLINGUNIT_ID = H.ID');
        $this->db->join('T_MODEL MD','V.MODEL_ID = MD.ID');
        $this->db->where('L.LOCALSTATUS','4');
        $this->db->where_in('H.CATEGORY3',$arr);

        if($_POST["search"]["value"])
        {
            $this->db->where("(LOWER(V.VIN) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(H.CATEGORY3) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(MD.DESCRIPTION) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(H.HOLDCOUNT) LIKE '%".strtolower($_POST["search"]["value"])."%')", NULL, FALSE);
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
        $this->_get_datatables_query();
        $query = $this->db->count_all_results();
        return $query;
    }

    public function get_damage($vin){
        $dmg = $this->db->select('T_HANDLINGUNIT.CODE, T_DAMAGE.STATUS')
            ->from('T_HANDLINGUNIT')
            ->join('T_DAMAGE','T_DAMAGE.HANDLINGUNIT_ID = T_HANDLINGUNIT.ID')
            ->where('T_HANDLINGUNIT.CODE',$vin)
            ->get()->result_array();
        return $dmg;
    }

    public function get_rc_status($vin){

        $dmg = $this->db2->select('RC_NO_REQ,VIN, RC_STATUS')
            ->from('RETURN_CARGO')
            ->where('VIN',$vin)
            ->where('RC_STATUS <',3)
            ->order_by('CREATED_DT',"desc")->limit(2)
            ->get()->result_array();
        return $dmg;
    }

     public function create_import($visit_id,$truck_code,$bl,$res_code,$res_desc){
        $this->db2->trans_start();
        $date = date('d/m/Y H:i:s');

        $datas = array(
            'VISIT_ID' => $visit_id,
            'TRUCK_CODE' => $truck_code,
            'BL_NUMBER' => $bl,
            'RESPONSE_CODE' => $res_code,
            'RESPONSE_DESC' => $res_desc,
        );

        $this->db2->set('CREATED_AT',"to_date('$date','dd/mm/yyyy hh24:mi:ss')", false);
        $this->db2->insert('ETICKET_IMPORT', $datas);

        $this->db2->trans_complete();

        return $this->db2->trans_status();
    }

    public function getMaker($bl){
        $dmg = $this->db2->distinct()->select('MAKE')
            ->from('CARTOS_CARGO')
            ->where('BL_NUMBER',$bl)
            ->get()->result_array();
        return $dmg;
    }

    public function update_visit($visit,$bl){

        $get_maker = $this->getMaker($bl);

        $this->db->trans_start();
        $datas = array(
            'ADDITIONALINFORMATION' => 'IMPORT',
            'CATEGORYFIELD5' => $get_maker[0]["MAKE"],
        );
        $this->db->where('NR', $visit)->update('T_VISIT', $datas);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

}