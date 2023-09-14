<?php

class Selfdrive_Model extends CI_Model {

    private $wsdl = WSDL_ETIKET;
    private $client = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_soap_client()
    {
        if (!$this->client) {
            libxml_disable_entity_loader(false);
            $this->client = new SoapClient($this->wsdl.'/Cartos_VPC/api', array(
                'location' => $this->wsdl."/Cartos_VPC/api",
                'uri'      => '/Cartos_VPC/api',
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS
            ));
        }

        return $this->client;
    }

    public function submit_request($payload){
        $out = new StdClass();

        try {
            $client = $this->get_soap_client();
            $response = $client->op_selfdrive($payload);
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
    }

    public function getTruckSelf(){
        $db_ilcs = $this->load->database(ILCS_CTOS_QAS, TRUE);

        $query = "SELECT CODE, NAME from T_TRUCK where DESCRIPTION = 'SELFDRIVE'";

        $getData = $db_ilcs->query($query)->result();
        return $getData;
    }

    public function getListVINSelfDrive($sender,$type,$searchTerm=""){
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $db_ilcs = $this->load->database(ILCS_TPS_ONLINE, TRUE);
        $maker = [];

        if($sender == 'OTHER' || $sender == 'EMERGENCY'){
            $get_maker = $db_ilcs->select('MAKE')
                ->from('AUTOGATE_MAKER')
                ->where('SENDER !=', $sender)
                ->get()
                ->result();
        }else{
            $get_maker = $db_ilcs->select('MAKE')
                ->from('AUTOGATE_MAKER')
                ->where('SENDER', $sender)
                ->get()
                ->result();
        }

        foreach ($get_maker as $data) {
            array_push($maker, $data->MAKE);
        }

        if($sender == 'OTHER'){
            $datas = $db_ctos->select('T_HANDLINGUNIT.CATEGORY3, T_HANDLINGUNIT.CATEGORY2, T_HANDLINGUNIT.CODE VIN')
                ->from('T_HANDLINGUNIT')
                ->where('T_HANDLINGUNIT.CATEGORY2', $type)
//                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->where("UPPER(T_HANDLINGUNIT.CODE) like '%".strtoupper($searchTerm)."%' ")
                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->where_not_in('T_HANDLINGUNIT.CATEGORY3', $maker)
                ->get()->result_array();
        } else if ( $sender == 'EMERGENCY') {
            $datas = $db_ctos->select('T_HANDLINGUNIT.CATEGORY3, T_HANDLINGUNIT.CATEGORY2, T_HANDLINGUNIT.CODE VIN')
                ->from('T_HANDLINGUNIT')
                ->where('T_HANDLINGUNIT.CATEGORY2', $type)
                ->where("UPPER(T_HANDLINGUNIT.CODE) like '%".strtoupper($searchTerm)."%' ")
                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->get()->result_array();
        } else {
            $datas = $db_ctos->select('T_HANDLINGUNIT.CATEGORY3, T_HANDLINGUNIT.CATEGORY2, T_HANDLINGUNIT.CODE VIN')
                ->from('T_HANDLINGUNIT')
                ->where('T_HANDLINGUNIT.CATEGORY2', $type)
//                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->where("UPPER(T_HANDLINGUNIT.CODE) like '%".strtoupper($searchTerm)."%' ")
                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->where_in('T_HANDLINGUNIT.CATEGORY3', $maker)
                ->get()->result_array();
        }

        $data = array();
        foreach($datas as $item){
            $data[] = array("id"=>$item['VIN'], "text"=>$item['VIN'].' - '.$item['CATEGORY3']);
        }

        return $data;
    }

}