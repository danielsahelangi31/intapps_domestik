<?php

class Rc_Item_Model extends CI_Model {

    var $table = "CTOS.RETURN_CARGO";
    var $column_order = array(null,'RC_NO_REQ','CREATED_BY','VIN','TRUCK_CODE','DRIVER','DAMAGE_STATUS');
    var $column_search = array('LOWER(RC_NO_REQ)','LOWER(CREATED_BY)','LOWER(VIN)','LOWER(TRUCK_CODE)','LOWER(DRIVER)','LOWER(DAMAGE_STATUS)');
    var $order = array('VIN' => 'asc');

    private $wsdl = "./application/config/wsdl/eticket.wsdl";
    private $client = null;

    public function get_soap_client()
    {
        if (!$this->client) {
            libxml_disable_entity_loader(false);
            $this->client = new SoapClient($this->wsdl, array(
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS
            ));
        }

        return $this->client;
    }

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $this->db2 = $this->load->database(ILCS_TPS_ONLINE, TRUE);
    }

    public function submit_request($payload){
        $out = new StdClass();

        try {
            $client = $this->get_soap_client();
            $response = $client->op_returncargobymaker($payload);
            $out->response = $response;
        } catch (SoapFault $fault) {
            if ($fault->faultcode == 'SOAP-ENV:Server') {

                $out->success = false;
                $out->msg_code = $fault->detail->ExceptionInfo->Fault->FaultCode;
                $out->msg = $fault->detail->ExceptionInfo->Fault->FaultString;
                $out->payload = null;

            } else {
                $out->success = false;
                $out->msg_code = 503;
                $out->msg = 'Tidak dapat menghubungi Service Cartos, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
                $out->payload = null;
                $out->fault = $fault;
            }
        } catch (Exception $e) {
            $out->success = false;
            $out->msg_code = 500;
            $out->msg = 'Internal Server Error';
        }

        return $out;

//        $no_req = null ;
//
//        $seq = null;
//
//        $id = $this->db2->select('RC_NO_REQ')->from('RETURN_CARGO')
//            ->where("TO_CHAR(CREATED_DT,'YYYY-MM-DD')",date('Y-m-d'))
//            ->order_by('CREATED_DT',"desc")->limit(2)->get();
//        if($id->num_rows()>0)
//        {
//            foreach($id->result() as $k){
//                $seq = substr($k->RC_NO_REQ,-3,3);
//                $seq = intval($seq)+1;
//                $seq = str_pad($seq,3,'0',STR_PAD_LEFT);
//            }
//        }else{
//            $seq = '001';
//        }
//
//        $no_req = "IKTRC".date('Y').date('m').date('d').$seq;
//
//        $this->db2->trans_start();
//        $date = date('d/m/Y H:i:s');
//
//        $datas = array(
//            'RC_NO_REQ' => $no_req,
//            'VIN' => post('vin'),
//            'DAMAGE_STATUS' => $dmg,
//            'RC_STATUS' => 1,
//            'TRUCK_CODE' => null,
//            'DRIVER' => null,
//            'CREATED_BY' => $user_auth,
//            'UPDATED_BY' => null,
//        );
//
//        $this->db2->set('CREATED_DT',"to_date('$date','dd/mm/yyyy hh24:mi:ss')", false);
//        $this->db2->set('UPDATED_DT',"to_date('$date','dd/mm/yyyy hh24:mi:ss')", false);
//        $this->db2->insert('RETURN_CARGO', $datas);
//
//        $this->db2->trans_complete();
//
//        return $no_req;
    }

    private function _get_datatables_query()
    {
        $this->db2->from($this->table);
//        $this->db2->join("{$this->db1->CTOS_QAS}.T_VEHICLE","{$this->db1->CTOS_QAS}.T_VEHICLE.VIN = RETURN_CARGO.VIN");

//        $this->db2->join($this->db1->database.'.T_VEHICLE',"{$this->db1->database}.T_VEHICLE.VIN = {$this->db2->database}.RETURN_CARGO.VIN");

//        $this->db2->join("{$this->db1->database}.{$this->db1->sim_}T_VEHICLE","T_VEHICLE.VIN = {$this->db2->gap_}RETURN_CARGO.VIN",'left',TRUE);


        if($_POST["search"]["value"])
        {
            $this->db2->where("(LOWER(RC_NO_REQ) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(VIN) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(DAMAGE_STATUS) LIKE '%".strtolower($_POST["search"]["value"])."%' OR LOWER(CREATED_BY) LIKE '%".strtolower($_POST["search"]["value"])."%')", NULL, FALSE);
        }
        if(isset($_POST['order']))
        {
            $this->db2->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db2->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();

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

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db2->count_all_results();
        return $query;
    }

    public function count_all()
    {
        $this->_get_datatables_query();
        $query = $this->db2->count_all_results();
        return $query;
    }

    public function get_req_exist(){
        $this->db2->from($this->table);
        $this->db2->where('RC_STATUS','1');
        $query = $this->db2->get();
        $datas = [];
        foreach ($query->result_array() as $item){
            array_push($datas,$item["VIN"]);
        }

        return $datas;
    }

    public function getMakerAndModel($vin){
        $this->db->select('CATEGORY3 MAKER, CATEGORY4 MODEL');
        $this->db->from('T_HANDLINGUNIT');
        $this->db->where('CODE',$vin);
        $query = $this->db->get()->result();

        return $query;
    }

    public function submit_approval($payload){

        $out = new StdClass();

        try {
            $client = $this->get_soap_client();
            $response = $client->op_returnCargoByIKT($payload);
            $out->response = $response;
        } catch (SoapFault $fault) {
            if ($fault->faultcode == 'SOAP-ENV:Server') {

                $out->success = false;
                $out->msg_code = $fault->detail->ExceptionInfo->Fault->FaultCode;
                $out->msg = $fault->detail->ExceptionInfo->Fault->FaultString;
                $out->payload = null;

            } else {
                $out->success = false;
                $out->msg_code = 503;
                $out->msg = 'Tidak dapat menghubungi Service Cartos, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
                $out->payload = null;
                $out->fault = $fault;
            }
        } catch (Exception $e) {
            $out->success = false;
            $out->msg_code = 500;
            $out->msg = 'Internal Server Error';
        }

        return $out;
//
//        $this->db2->trans_start();
//        $date = date('d/m/Y H:i:s');
//        $datas = array(
//            'RC_STATUS' => 2,
//            'UPDATED_BY' => $auth,
//        );
//        $this->db2->set('UPDATED_DT',"to_date('$date','dd/mm/yyyy hh24:mi:ss')", false);
//        $this->db2->where('RC_NO_REQ', $rc_no_req)->update('RETURN_CARGO', $datas);
//
//        $this->db2->trans_complete();
//        return $this->db2->trans_status();
    }

    public function submit_rejected($payload){

        $out = new StdClass();

        try {
            $client = $this->get_soap_client();
            $response = $client->op_returnCargoByIKT($payload);
            $out->response = $response;
        } catch (SoapFault $fault) {
            if ($fault->faultcode == 'SOAP-ENV:Server') {

                $out->success = false;
                $out->msg_code = $fault->detail->ExceptionInfo->Fault->FaultCode;
                $out->msg = $fault->detail->ExceptionInfo->Fault->FaultString;
                $out->payload = null;

            } else {
                $out->success = false;
                $out->msg_code = 503;
                $out->msg = 'Tidak dapat menghubungi Service Cartos, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
                $out->payload = null;
                $out->fault = $fault;
            }
        } catch (Exception $e) {
            $out->success = false;
            $out->msg_code = 500;
            $out->msg = 'Internal Server Error';
        }

        return $out;

//        $this->db2->trans_start();
//        $date = date('d/m/Y H:i:s');
//        $datas = array(
//            'RC_STATUS' => 3,
//            'UPDATED_BY' => $auth,
//        );
//        $this->db2->set('UPDATED_DT',"to_date('$date','dd/mm/yyyy hh24:mi:ss')", false);
//        $this->db2->where('RC_NO_REQ', $rc_no_req)->update('RETURN_CARGO', $datas);
//
//        $this->db2->trans_complete();
//        return $this->db2->trans_status();
    }

    public function getTruckInformation($searchTerm="")
    {
        $datasource = $this->db->select('T_TRUCK.CODE,T_ORGANIZATION.NAME')
            ->from('T_TRUCK')
            ->join('T_ORGANIZATION','T_ORGANIZATION.ID = T_TRUCK.CARRIER_ID')
            ->where("UPPER(T_TRUCK.CODE) like '%".strtoupper($searchTerm)."%' ")
            ->get()->result_array();
        $data = array();
        foreach($datasource as $item){
            $data[] = array("id"=>$item['CODE'], "text"=>$item['CODE'].' - '.$item['NAME']);
        }
        return $data;
    }

    public function getCarrierByTruck($searchTerm){
        $datasource = $this->db->select('T_ORGANIZATION.NAME,T_TRUCK.LICENSEPLATE')
            ->from('T_TRUCK')
            ->join('T_ORGANIZATION','T_ORGANIZATION.ID = T_TRUCK.CARRIER_ID')
            ->where("UPPER(T_TRUCK.CODE) like '%".strtoupper($searchTerm)."%' ")
            ->get()->result();

        return $datasource;
    }

    public function submit_print(){
        $this->db2->trans_start();
        $datas = array(
            'TRUCK_CODE' => post('truckCode'),
            'DRIVER' => post('driverName'),
        );
        $this->db2->where('RC_NO_REQ', post('id_form'))->update('RETURN_CARGO', $datas);

        $this->db2->trans_complete();
        return $this->db2->trans_status();
    }

    public function getDataPrint($no_rc_req){
        $datasource = $this->db2->select('RC_NO_REQ,VIN,TRUCK_CODE,DRIVER,UPDATED_DT,DAMAGE_STATUS,CREATED_DT')
            ->from('RETURN_CARGO')
            ->where("UPPER(RC_NO_REQ) like '%".strtoupper($no_rc_req)."%' ")
            ->get()->result();

        $modelMaker = $this->getMakerAndModel($datasource[0]->VIN);

        $carrierByTruck =  $this->getCarrierByTruck($datasource[0]->TRUCK_CODE);

        $datas = array(
            'VIN' => $datasource[0]->VIN,
            'RC_NO_REQ' => $datasource[0]->RC_NO_REQ,
            'TRUCK_CODE' => $datasource[0]->TRUCK_CODE,
            'DRIVER' => $datasource[0]->DRIVER,
            'CREATED_DT' => $datasource[0]->CREATED_DT,
            'MAKER' => $modelMaker[0]->MAKER,
            'MODEL' => $modelMaker[0]->MODEL,
            'CARRIER' => $carrierByTruck[0]->NAME,
            'LICENSEPLATE' => $carrierByTruck[0]->LICENSEPLATE,
            'DAMAGE_STATUS' => $datasource[0]->DAMAGE_STATUS
        );
        return $datas;
    }

    public function update_file_stats($no_rc_req){
        $this->db2->trans_start();
        $datas = array(
            'FILE_STATS' => 1,
        );
        $this->db2->where('RC_NO_REQ', $no_rc_req)->update('RETURN_CARGO', $datas);

        $this->db2->trans_complete();
        return $this->db2->trans_status();
    }

    public function update_doc_stats($no_rc_req){
        $this->db2->trans_start();
        $datas = array(
            'DOC_STATS' => 1,
        );
        $this->db2->where('RC_NO_REQ', $no_rc_req)->update('RETURN_CARGO', $datas);

        $this->db2->trans_complete();
        return $this->db2->trans_status();
    }
}