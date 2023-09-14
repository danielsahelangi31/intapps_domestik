<?php
include_once('base/modelbase.php');

class Etickets extends ModelBase
{

    private $wsdl = './application/config/wsdl/eticket.wsdl';
    private $wsdl2 = WSDL_ETIKET;
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
    }

    public function get_soap_client2()
    {
        if (!$this->client) {
            libxml_disable_entity_loader(false);
            $this->client = new SoapClient($this->wsdl2.'/Cartos_VPC/api', array(
                'location' => $this->wsdl2."/Cartos_VPC/api",
                'uri'      => '/Cartos_VPC/api',
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS
            ));
        }

        return $this->client;
    }

    public function getByTransferID($docTransferID)
    {
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        $query = "SELECT VISITID from ANNOUNCETRUCK_REQ where DOCUMENT_TRANSFERID = '$docTransferID'";

        $getData = $db_maker->query($query)->result();
        return $getData;
    }

    public function getEntryTicketInfo($id)
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $query = "SELECT
            OWNERS.NAME AS OWNER,
            CARRIERS.NAME AS CARRIER,
            T_TRUCK.CODE,
            TO_CHAR(T_VISIT.ARRIVAL, 'DD-MON-YYYY HH:MI:SS') AS ARRIVAL_DATE,
            T_VISIT.TRANSPORTMEANNAME,
            T_VISIT.DRIVER,
            T_VISIT.NR AS TNR,
            T_TRUCK.DESCRIPTION 
        FROM
            T_VISIT FULL outer
            JOIN T_TRUCK ON T_VISIT.TRUCK_ID = T_TRUCK.ID FULL outer
            JOIN T_ORGANIZATION CARRIERS ON CARRIERS.ID = T_TRUCK.CARRIER_ID FULL outer
            JOIN T_ORGANIZATION OWNERS ON OWNERS.ID = T_TRUCK.OWNER_ID 
        WHERE
            T_VISIT.NR = '$id'";
        $getData = $db_ctos->query($query)->result();
        return $getData;
    }

    public function getAsosiasiByTruckVisitID($visitID)
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);

        $query = "SELECT
            RI.VIN,
            RI.DIRECTION,
            RI.MAKE,
            VIS.TRANSPORTMEANNAME,
            externalreferencein,
            externalreferenceout,
            inwardbc11nr,
            inwardbc11date,
            outwardbc11nr,
            outwardbc11date 
        FROM
            codeco_coarri RI
            LEFT JOIN T_VISIT VIS ON RI.VISIT_ID_1 = VIS.NR
            WHERE RI.VISIT_ID_2 = '$visitID'
        UNION
        SELECT
            RI.VIN,
            RI.DIRECTION,
            RI.MAKE,
            VIS.TRANSPORTMEANNAME,
            externalreferencein,
            externalreferenceout,
            inwardbc11nr,
            inwardbc11date,
            outwardbc11nr,
            outwardbc11date 
        FROM
            codeco_coarri RI
            LEFT JOIN T_VISIT VIS ON RI.VISIT_ID_2 = VIS.NR
            WHERE RI.VISIT_ID_1 = '$visitID'";

        $getData = $db_ctos->query($query)->result();
        return $getData;
    }

    public function getGateOut($visitID)
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $query = "SELECT
            RI.VIN,
            RI.DIRECTION,
            transportmeanname,
            width,
            weight,
            height,
            (length * width) AS persegi,
            make_name AS brand,
            model_CODE AS Model,
            color,
            CUSTOMS_NUMBER AS SPPB_NO,
            TO_CHAR(CUSTOMS_DATE, 'DD-MON-YYYY HH:MI:SS') AS SPPB_DATE,
            CONSIGNEE_ID_1 AS CONSIGNEE 
        FROM
            codeco_coarri RI
            LEFT JOIN t_visit VIS ON RI.visit_id_2 = VIS.nr 
        WHERE
            RI.visit_id_2 = '$visitID'
        ORDER BY
            sppb_date ASC";
        $getData = $db_ctos->query($query)->result();
        return $getData;
    }

    public function getImport($param)
    {
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        $query = "SELECT
            DISTINCT(a.BL_NUMBER),
            b.KD_DOK,
            c.DOC_TYPE 
        FROM
            ETICKET_IMPORT a
            INNER JOIN CARTOS_TPS_SPPB_PIB_H b ON b.NO_BL_AWB = a.BL_NUMBER 
            OR b.NO_MASTER_BL_AWB = a.BL_NUMBER
            LEFT JOIN MST_JENIS_DOKUMEN c ON c.id = b.KD_DOK 
        WHERE
            a.VISIT_ID = '$param'";

        $getData = $db_maker->query($query)->result();
        return $getData;
    }

    public function getKdDok($param)
    {
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        $query = "SELECT * from MST_JENIS_DOKUMEN ORDER BY ID + 0";

        $getData = $db_maker->query($query)->result();
        return $getData;
    }

    public function get_dok($jns_dok)
    {
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        $query = "SELECT * from MST_JENIS_DOKUMEN WHERE KETERANGAN != '".$jns_dok."' ORDER BY ID + 0";

        $getData = $db_maker->query($query)->result();
        return $getData;
    }

    public function getNPWP($sender)
    {
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        $query = "SELECT NPWP from MST_MAKER_NPWP WHERE SENDER = '$sender'";

        $getData = $db_maker->query($query)->result();
        return $getData;
    }

    public function deleteBL($param, $visit)
    {
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);
        $truck_code = $db_maker->where('VISIT_ID', $visit)->limit(2)->get('ETICKET_IMPORT')->result_array();

        $db_maker->where('VISIT_ID', $visit);
        $db_maker->where_not_in('BL_NUMBER', $param);
        $db_maker->delete('ETICKET_IMPORT');

        foreach ($param as $item) {
            $db_maker->where('VISIT_ID', $visit);
            $db_maker->where('BL_NUMBER', $item);
            $q = $db_maker->get('ETICKET_IMPORT');
            $db_maker->trans_start();
            $date = date('d/m/Y H:i:s');
            $db_maker->set('UPDATED_AT', "to_date('$date','dd/mm/yyyy hh24:mi:ss')", false);
            $datas = array(
                'VISIT_ID' => $visit,
                'TRUCK_CODE' => $truck_code[0]["TRUCK_CODE"],
                'BL_NUMBER' => $item
            );

            if ($q->num_rows() > 0) {
                $db_maker->where('VISIT_ID', $visit);
                $db_maker->where('BL_NUMBER', $item);
                $db_maker->update('ETICKET_IMPORT', $datas);
            } else {
                $db_maker->insert('ETICKET_IMPORT', $datas);
            }
            $db_maker->trans_complete();
        }

        return $db_maker->trans_status();
    }

    public function getAsosiasiByTruckCode($sender, $gate, $truck_code)
    {
        $out = new StdClass();

        $payload = array(
            'Sender' => $sender,
            'Gate' => $gate,
            'TruckCode' => $truck_code
        );

        try {
            $client = $this->get_soap_client();
            $response = $client->op_getvinbytruck($payload);

            $out->success = true;
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

    public function getTruckInfo($param)
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);

        $datas = $db_ctos->select('T_TRUCK.CODE  truck_code, T_TRUCK.LICENSEPLATE, T_TRUCK.NAME  driver_name, CARRIERS.CODE  carrier_code, CARRIERS.NAME  carrier_name, OWNERS.CODE  owner_code, OWNERS.NAME  owner_name, T_TRUCK.DESCRIPTION desc_type')
            ->from('T_TRUCK')
            ->join('T_ORGANIZATION CARRIERS', 'CARRIERS.ID = T_TRUCK.CARRIER_ID')
            ->join('T_ORGANIZATION OWNERS', 'OWNERS.ID = T_TRUCK.OWNER_ID')
            ->where('T_TRUCK.CODE', strtoupper($param))->get()->result();

        return $datas;
    }

    public function OpAnnounceTruck($payload)
    {
        $out = new StdClass();

        try {
            $client = $this->get_soap_client();
            $response = $client->OpAnnounceTruck($payload);

            $out->success = true;
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

    public function OpAnnounceVin($payload)
    {
        $out = new StdClass();


        try {
            $client = $this->get_soap_client2();
            $response = $client->OpAnnounceVin($payload);
            $out->success = true;
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

    public function get_list_bl($sender, $searchTerm = "")
    {
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        if ($sender != 'IKT' || $sender != 'EMERGENCY') {
            $datas = $db_maker->select('AUTOGATE_MAKER.MAKE')
                ->from('AUTOGATE_MAKER')
                ->where('SENDER', $sender)->get()->result();
        } else {
            $datas = $db_maker->select('AUTOGATE_MAKER.MAKE')
                ->from('AUTOGATE_MAKER')
                ->get()->result();
        }

        $arr = [];
        foreach ($datas as $data) {
            array_push($arr, $data->MAKE);
        }

        if ($sender != 'IKT' && $sender != 'EMERGENCY') {

            $datasource = $db_maker->distinct()->select('BL_NUMBER, BL_NUMBER_DATE')
                ->from('CARTOS_CARGO')
                ->where_in('MAKE_NAME', $arr)
                ->where("UPPER(BL_NUMBER) like '%" . strtoupper($searchTerm) . "%' ")
                ->get()->result_array();

            //            $datasource = $db_maker->select('BL_CARGO_TYPE_MAPPING.BL_NUMBER, CARTOS_CARGO.BL_NUMBER_DATE')
            //                ->from('BL_CARGO_TYPE_MAPPING')
            //                ->join('CARTOS_CARGO','BL_CARGO_TYPE_MAPPING.BL_NUMBER = CARTOS_CARGO.BL_NUMBER','left')
            //                ->where_in('CARTOS_CARGO.MAKE', $arr)
            //                ->where("UPPER(BL_CARGO_TYPE_MAPPING.BL_NUMBER) like '%".strtoupper($searchTerm)."%' ")
            //                ->get()->result_array();
        } else {

            $datasource = $db_maker->distinct()->select('BL_NUMBER, BL_NUMBER_DATE')
                ->from('CARTOS_CARGO')
                ->where("UPPER(BL_NUMBER) like '%" . strtoupper($searchTerm) . "%' ")
                ->get()->result_array();

            //            $datasource = $db_maker->select('BL_CARGO_TYPE_MAPPING.BL_NUMBER, CARTOS_CARGO.BL_NUMBER_DATE')
            //                ->from('BL_CARGO_TYPE_MAPPING')
            //                ->join('CARTOS_CARGO','BL_CARGO_TYPE_MAPPING.BL_NUMBER = CARTOS_CARGO.BL_NUMBER','left')
            //                ->where("UPPER(BL_CARGO_TYPE_MAPPING.BL_NUMBER) like '%".strtoupper($searchTerm)."%' ")
            //                ->get()->result_array();
        }

        $data = array();
        foreach ($datasource as $item) {
            $data[] = array("id" => $item['BL_NUMBER'], "text" => $item['BL_NUMBER'], 'dates' => $item['BL_NUMBER_DATE']);
        }
        return $data;
    }

    public function getInfoBL($searchTerm)
    {
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        // $datasource = $db_maker->select('BL_NUMBER')
        //     ->from('CARTOS_CARGO')
        //     ->where("UPPER(BL_NUMBER) like '%".strtoupper($searchTerm)."%' ")
        //     ->count_all_results();
        $query = "SELECT
                COUNT(CASE WHEN DTS_LEFT IS NULL THEN 1 END) AS REMAINING_CARGO ,
                COUNT(BL_NUMBER) AS TOTAL_CARGO
            FROM CARTOS_CARGO cc 
            WHERE UPPER(BL_NUMBER) like '%" . strtoupper($searchTerm) . "%'";
        $datasource = $db_maker->query($query)->result_array();
        return $datasource;
    }

    public function get_vin($sender, $searchTerm = "")
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        if ($sender != 'IKT' || $sender != 'EMERGENCY') {
            $datas = $db_maker->select('AUTOGATE_MAKER.MAKE')
                ->from('AUTOGATE_MAKER')
                ->where('SENDER', $sender)->get()->result();
        } else {
            $datas = $db_maker->select('AUTOGATE_MAKER.MAKE')
                ->from('AUTOGATE_MAKER')
                ->get()->result();
        }

        $arr = [];
        foreach ($datas as $data) {
            array_push($arr, $data->MAKE);
        }

        if ($sender != 'IKT' || $sender != 'EMERGENCY') {
            // if($sender == 'NSDS') {
            //     $datasource = $db_maker->select("MODEL_NAME as CODE, MODEL_IKT as DESCRIPTION ")
            //         ->from("MAPPING")
            //         ->where("UPPER(MODEL_NAME) like '%".strtoupper($searchTerm)."%' ")
            //         ->or_where("UPPER(MODEL_IKT) like '%".strtoupper($searchTerm)."%' ")
            //         ->get()->result_array();
            // } else {
            // $datasource = $db_ctos->select('T_MODEL.CODE, T_MODEL.DESCRIPTION,T_MODEL.STATUS')
            //     ->from('T_MAKE')
            //     ->join('T_MODEL', 'T_MODEL.MAKE_ID = T_MAKE.ID')
            //     ->where_in('T_MAKE.CODE', $arr)
            //     ->where("UPPER(T_MODEL.CODE) like '%".strtoupper($searchTerm)."%' ")
            //     ->where("T_MODEL.STATUS",0)
            //     ->get()->result_array();
            // }
            $datasource = $db_ctos->select('VIN')
                ->from('T_VEHICLE')
                ->where("UPPER(VIN) like '%" . strtoupper($searchTerm) . "%' ")
                ->get()->result_array();
        } else {
            // $nsds = $db_maker->select("MODEL_NAME as CODE, MODEL_IKT as DESCRIPTION ")
            //     ->from("MAPPING")
            //     ->where("UPPER(MODEL_NAME) like '%".strtoupper($searchTerm)."%' ")
            //     ->or_where("UPPER(MODEL_IKT) like '%".strtoupper($searchTerm)."%' ")
            //     ->get()->result_array();
            // $all = $db_ctos->select('T_MODEL.CODE, T_MODEL.DESCRIPTION,T_MODEL.STATUS')
            //     ->from('T_MAKE')
            //     ->join('T_MODEL', 'T_MODEL.MAKE_ID = T_MAKE.ID')
            //     ->where("UPPER(T_MODEL.CODE) like '%".strtoupper($searchTerm)."%' ")
            //     ->where("T_MODEL.STATUS",0)
            //     ->get()->result_array();

            // $datasource = array_merge($nsds,$all);
            $datasource = $db_ctos->select('VIN')
                ->from('T_VEHICLE')
                ->where("UPPER(VIN) like '%" . strtoupper($searchTerm) . "%' ")
                ->get()->result_array();
        }

        $data = array();
        foreach ($datasource as $item) {
            $data[] = array(
                "id" => $item['VIN'],
                "text" => $item['VIN']
            );
        }

        return $data;
    }

    public function get_model($sender, $searchTerm = "")
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        if ($sender != 'IKT' || $sender != 'EMERGENCY') {
            $datas = $db_maker->select('AUTOGATE_MAKER.MAKE')
                ->from('AUTOGATE_MAKER')
                ->where('SENDER', $sender)->get()->result();
        } else {
            $datas = $db_maker->select('AUTOGATE_MAKER.MAKE')
                ->from('AUTOGATE_MAKER')
                ->get()->result();
        }

        $arr = [];
        foreach ($datas as $data) {
            array_push($arr, $data->MAKE);
        }

        if ($sender != 'IKT' && $sender != 'EMERGENCY') {
            if ($sender == 'NSDS') {
                $datasource = $db_maker->select("MODEL_NAME as CODE, MODEL_IKT as DESCRIPTION ")
                    ->from("MAPPING")
                    ->where("UPPER(MODEL_NAME) like '%" . strtoupper($searchTerm) . "%' ")
                    ->or_where("UPPER(MODEL_IKT) like '%" . strtoupper($searchTerm) . "%' ")
                    ->get()->result_array();
            } else {
                $datasource = $db_ctos->select('T_MODEL.CODE, T_MODEL.DESCRIPTION,T_MODEL.STATUS')
                    ->from('T_MAKE')
                    ->join('T_MODEL', 'T_MODEL.MAKE_ID = T_MAKE.ID')
                    ->where_in('T_MAKE.CODE', $arr)
                    ->where("UPPER(T_MODEL.CODE) like '%" . strtoupper($searchTerm) . "%' ")
                    ->where("T_MODEL.STATUS", 0)
                    ->get()->result_array();
            }
        } else {
            $nsds = $db_maker->select("MODEL_NAME as CODE, MODEL_IKT as DESCRIPTION ")
                ->from("MAPPING")
                ->where("UPPER(MODEL_NAME) like '%" . strtoupper($searchTerm) . "%' ")
                ->or_where("UPPER(MODEL_IKT) like '%" . strtoupper($searchTerm) . "%' ")
                ->get()->result_array();
            $all = $db_ctos->select('T_MODEL.CODE, T_MODEL.DESCRIPTION,T_MODEL.STATUS')
                ->from('T_MAKE')
                ->join('T_MODEL', 'T_MODEL.MAKE_ID = T_MAKE.ID')
                ->where("UPPER(T_MODEL.CODE) like '%" . strtoupper($searchTerm) . "%' ")
                ->where("T_MODEL.STATUS", 0)
                ->get()->result_array();

            $datasource = array_merge($nsds, $all);
        }

        $data = array();
        foreach ($datasource as $item) {
            $data[] = array("id" => $item['CODE'], "text" => $item['CODE'] . ' - ' . $item['DESCRIPTION']);
        }
        return $data;
    }

    public function get_model_by_vin($searchTerm)
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $db_maker = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        $query = "SELECT a.VIN, 
                         d.CODE, 
                         d.DESCRIPTION, 
                         a.MODEL_ID, 
                         a.HANDLINGUNIT_ID, 
                         b.CATEGORY5, 
                         c.CODE AS CODE_DEST, 
                         c.NAME AS NAME_DEST,
                         '-' AS CONTROLLER_NAME,
                         '-' AS CONSIGNEE_NAME
                    FROM T_VEHICLE a 
                LEFT JOIN T_HANDLINGUNIT b 
                    ON a.HANDLINGUNIT_ID  = b.ID
                LEFT JOIN T_LOCATION c 
                    ON b.CATEGORY5 = c.CODE
                LEFT JOIN T_MODEL d
                    ON a.MODEL_ID = d.ID
                WHERE a.VIN = '$searchTerm'";

        $datasource = $db_ctos->query($query)->result_array();
        $data = array();
        foreach ($datasource as $item) {
            $data[] = array(
                // 'modelVal' => !empty($item['CODE']) ? $item['CODE'] . ' - ' . $item['DESCRIPTION'] : '',
                'modelVal' => !empty($item['CODE']) ? $item['CODE'] : '',
                'destinateVal' => !empty($item['CODE_DEST']) ? $item['CODE_DEST'] . ' - ' . $item['NAME_DEST'] : '',
                'controllVal' => !empty($item['CONTROLLER_NAME']) ? $item['CONTROLLER_NAME'] : '',
                'consigneeVal' => !empty($item['CONSIGNEE_NAME']) ? $item['CONSIGNEE_NAME'] : '',
            );
        }
        return $data;
    }

    public function getDestination($searchTerm = "")
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $datasource = $db_ctos->select('T_LOCATION.NAME, T_LOCATION.CODE')
            ->from('T_LOCATION')
            ->where("UPPER(T_LOCATION.CODE) like '%" . strtoupper($searchTerm) . "%' ")
            ->or_where("UPPER(T_LOCATION.NAME) like '%" . strtoupper($searchTerm) . "%' ")
            ->get()->result_array();
        $data = array();
        foreach ($datasource as $item) {
            $data[] = array("id" => $item['CODE'], "text" => $item['CODE'] . ' - ' . $item['NAME']);
        }
        return $data;
    }

    public function getControlling($searchTerm = "")
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $datasource = $db_ctos->select('T_ORGANIZATION.NAME, T_ORGANIZATION.CODE')
            ->from('T_ORGANIZATION')
            ->where("UPPER(T_ORGANIZATION.CODE) like '%" . strtoupper($searchTerm) . "%' ")
            ->where("T_ORGANIZATION.STATUS", 0)
            //            ->or_where("UPPER(T_ORGANIZATION.NAME) like '%".strtoupper($searchTerm)."%' ")
            ->get()->result_array();
        $data = array();
        foreach ($datasource as $item) {
            $data[] = array("id" => $item['CODE'], "text" => $item['CODE'] . ' - ' . $item['NAME']);
        }
        return $data;
    }

    public function getTruckInfoWithLicensePlate($param)
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);

        $datas = $db_ctos->select('T_TRUCK.CODE  truck_code, T_TRUCK.LICENSEPLATE, T_TRUCK.NAME  driver_name, CARRIERS.CODE  carrier_code, CARRIERS.NAME  carrier_name, OWNERS.CODE  owner_code, OWNERS.NAME  owner_name')
            ->from('T_TRUCK')
            ->join('T_ORGANIZATION CARRIERS', 'CARRIERS.ID = T_TRUCK.CARRIER_ID')
            ->join('T_ORGANIZATION OWNERS', 'OWNERS.ID = T_TRUCK.OWNER_ID')
            ->where('T_TRUCK.LICENSEPLATE', $param)->get()->result();

        return $datas;
    }

    public function getListEticket($sender)
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $db_ilcs = $this->load->database(ILCS_TPS_ONLINE, TRUE);
        $maker = [];
        $get_maker = $db_ilcs->select('MAKE')
            ->from('AUTOGATE_MAKER')
            ->where('SENDER', $sender)
            ->get()
            ->result();

        foreach ($get_maker as $data) {
            array_push($maker, $data->MAKE);
        }

        $maker_string = implode("','", $maker);
        $add_payload = "SELECT
                A.CATEGORY3,
                (case when count( DISTINCT CATEGORY2)>1 then 'BACKLOAD'
                      ELSE MAX(CATEGORY2) END)  CATEGORY2,
                A.NR,
                A.LASTCHANGE,
                A.LICENSEPLATE,
                A.CODE
            FROM (
                     select DISTINCT
                         T_HANDLINGUNIT.CATEGORY3,
                         T_HANDLINGUNIT.CATEGORY2,
                         T_VISIT.NR,
                         T_VISIT.LASTCHANGE,
                         T_TRUCK.LICENSEPLATE,
                         T_TRUCK.CODE
                     from T_HANDLINGUNIT
                              join T_HANDLINGUNITONTRIP ON T_HANDLINGUNITONTRIP.HANDLINGUNIT_ID = T_HANDLINGUNIT.ID
                              join T_TRIP ON T_TRIP.ID = T_HANDLINGUNITONTRIP.TRIP_ID
                              join T_VISIT ON T_VISIT.ID = T_TRIP.VISIT_ID
                              join T_TRUCK ON T_VISIT.TRUCK_ID = T_TRUCK.ID
                     where T_VISIT.LASTCHANGE > current_timestamp -3
                       AND          T_VISIT.ARRIVAL is NULL
                       AND T_HANDLINGUNITONTRIP.DTSDELETED is NULL
                       AND T_VISIT.NR LIKE 'TRK%' --AND T_TRUCK.CODE='B9783FEH'
                     union
                     select
                         t_visit.categoryfield5 AS CATEGORY3,
                         'IMPORT' AS CATEGORY2,
                         t_visit.nr AS NR,
                         t_visit.lastchange AS LASTCHANGE,
                         T_TRUCK.LICENSEPLATE AS LICENSEPLATE,
                         T_TRUCK.CODE AS CODE  from t_visit
                                                        join T_TRUCK ON T_VISIT.TRUCK_ID = T_TRUCK.ID
                     where T_VISIT.LASTCHANGE > current_timestamp -3 AND t_visit.additionalinformation  LIKE '%IMPORT%'
                       and t_visit.VISITSTATUS<5
                 ) A
            
            
         ";

        if ($sender != 'IKT' || $sender != 'EMERGENCY') {
            $add_payload .= "WHERE A.CATEGORY3 IN ('$maker_string')";
        }

        $add_payload .= "group by
                A.CATEGORY3,
                A.NR,
                A.LASTCHANGE,
                A.LICENSEPLATE,
                A.CODE ORDER BY A.LASTCHANGE DESC";

        $datas = $db_ctos->query($add_payload);


        return $datas;
    }

    public function eticket($sender)
    {
        $list_ticket = $this->getListEticket($sender);

        return $list_ticket;
    }

    public function getMakers()
    {
        $db_ilcs = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        $query = "SELECT MAKE, SENDER from AUTOGATE_MAKER";

        $getData = $db_ilcs->query($query)->result();
        return $getData;
    }

    public function getMakersImpExp($makerNi, $senderNi)
    {
        $db_ilcs = $this->load->database(ILCS_TPS_ONLINE, TRUE);

        $query = "SELECT MAKE, SENDER, EXPORT, IMPORT from AUTOGATE_MAKER WHERE MAKE= '$makerNi' AND SENDER='$senderNi' ";

        $getData = $db_ilcs->query($query)->result();
        return $getData;
    }

    public function getDocID($sender, $maker = null)
    {
        $db_ilcs = $this->load->database(ILCS_TPS_ONLINE, TRUE);
        $make = null;
        $docID = null;
        if ($maker) {
            $make = "AND MAKE = '$maker'";
        }

        $query = "SELECT MAKE,SENDER, SEQ, TO_CHAR(LASTDATE,'YYYY-MM-DD') dates, LASTDATE from AUTOGATE_MAKER WHERE SENDER = '$sender' " . $make . " ORDER BY SENDER";

        $getData = $db_ilcs->query($query)->result();

        if ($getData) {
            // top 1
            $date = date('d/m/Y H:i:s');
            if ($getData[0]->DATES == date('Y-m-d')) {
                $seq = intval($getData[0]->SEQ) + 1;
            } else {
                $seq = 1;
            }
            $docID =  "INT" . date('Y') . date('m') . date('d') . str_pad($seq, 6, '0', STR_PAD_LEFT) . $getData[0]->MAKE;

            $db_ilcs->trans_start();
            $datas = array(
                'SEQ' => $seq,
            );
            $db_ilcs->set('LASTDATE', "to_date('$date','dd/mm/yyyy hh24:mi:ss')", false);
            $db_ilcs->where('MAKE', $getData[0]->MAKE)->where('SENDER', $getData[0]->SENDER)->update('AUTOGATE_MAKER', $datas);

            $db_ilcs->trans_complete();
        }

        return $docID;
    }

    public function getListVinByMaker($sender, $searchTerm = "")
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $db_ilcs = $this->load->database(ILCS_TPS_ONLINE, TRUE);
        $maker = [];
        $get_maker = $db_ilcs->select('MAKE')
            ->from('AUTOGATE_MAKER')
            ->where('SENDER', $sender)
            ->get()
            ->result();

        foreach ($get_maker as $data) {
            array_push($maker, $data->MAKE);
        }

        $datas = $db_ctos->select('T_HANDLINGUNIT.CATEGORY2, T_HANDLINGUNIT.CODE CODES, T_HANDLINGUNITONTRIP.HANDLINGUNIT_ID, T_TRIP.ID, T_VISIT.NR, T_TRUCK.LICENSEPLATE, T_TRUCK.CODE')
            ->from('T_HANDLINGUNIT')
            ->join('T_HANDLINGUNITONTRIP', 'T_HANDLINGUNITONTRIP.HANDLINGUNIT_ID = T_HANDLINGUNIT.ID')
            ->join('T_TRIP', 'T_TRIP.ID = T_HANDLINGUNITONTRIP.TRIP_ID')
            ->join('T_VISIT', 'T_VISIT.ID = T_TRIP.VISIT_ID')
            ->join('T_TRUCK', 'T_VISIT.TRUCK_ID = T_TRUCK.ID')
            ->where_in('T_HANDLINGUNIT.CATEGORY3', $maker)
            ->where('T_VISIT.LASTCHANGE > (SELECT ADD_MONTHS(SYSDATE, -1) FROM dual)')
            ->where('T_VISIT.ARRIVAL is NULL')
            ->where('T_HANDLINGUNITONTRIP.DTSDELETED is NULL')
            ->or_where("UPPER(T_HANDLINGUNIT.CODE) like '%" . strtoupper($searchTerm) . "%' ")
            ->like('T_VISIT.NR', 'TRK', 'both')
            ->get()->result_array();

        $data = array();
        foreach ($datas as $item) {
            $data[] = array("id" => $item['CODES'], "text" => $item['CODES']);
        }
        return $data;
    }

    public function getListVIN($sender, $type, $searchTerm = "")
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $db_ilcs = $this->load->database(ILCS_TPS_ONLINE, TRUE);
        $maker = [];
        $get_maker = $db_ilcs->select('MAKE')
            ->from('AUTOGATE_MAKER')
            ->where('SENDER', $sender)
            ->get()
            ->result();

        foreach ($get_maker as $data) {
            array_push($maker, $data->MAKE);
        }

        if ($sender == 'IKT' || $sender == 'EMERGENCY') {
            $datas = $db_ctos->select('T_HANDLINGUNIT.CATEGORY3, T_HANDLINGUNIT.CATEGORY2, T_HANDLINGUNIT.CODE VIN')
                ->from('T_HANDLINGUNIT')
                ->where('T_HANDLINGUNIT.CATEGORY2', $type)
                //                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->where("UPPER(T_HANDLINGUNIT.CODE) like '%" . strtoupper($searchTerm) . "%' ")
                ->get()->result_array();
        } else {
            $datas = $db_ctos->select('T_HANDLINGUNIT.CATEGORY3, T_HANDLINGUNIT.CATEGORY2, T_HANDLINGUNIT.CODE VIN')
                ->from('T_HANDLINGUNIT')
                ->where('T_HANDLINGUNIT.CATEGORY2', $type)
                //                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->where("UPPER(T_HANDLINGUNIT.CODE) like '%" . strtoupper($searchTerm) . "%' ")
                ->where('T_HANDLINGUNIT.LASTCHANGE > (select sysdate -7  as add_day from dual)')
                ->where_in('T_HANDLINGUNIT.CATEGORY3', $maker)
                ->get()->result_array();
        }
        $data = array();
        foreach ($datas as $item) {
            $data[] = array("id" => $item['VIN'], "text" => $item['VIN'] . ' - ' . $item['CATEGORY3']);
        }

        return $data;
    }


    public function op_mTruck($payload)
    {
        $out = new StdClass();

        try {
            $client = $this->get_soap_client();
            $response = $client->op_mtruck($payload);

            $out->success = true;
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

    public function updateAsosiasi($payload)
    {
        $out = new StdClass();

        try {
            $client = $this->get_soap_client2();
            $response = $client->op_UpdateAsosiasiBL($payload);

            $out->success = true;
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

    public function getDataVesInfo($TRKVisitID)
    {
        $db_ctos = $this->load->database(ILCS_CTOS_QAS, TRUE);

        $add_payload = "SELECT additionalinformation FROM t_visit WHERE nr = '$TRKVisitID'";

        $get_additional = $db_ctos->query($add_payload)->row();

        if ($get_additional->ADDITIONALINFORMATION == 'EXPORT') {
            $query = "SELECT DISTINCT
           transportmeanname,
           externalreferencein,
           externalreferenceout,
           inwardbc11nr,
           inwardbc11date,
           outwardbc11nr,
           outwardbc11date 
        FROM
           t_visit 
        WHERE
           nr=(SELECT DISTINCT visit_id_1 FROM codeco_coarri WHERE visit_id_2 = '$TRKVisitID')";
        } else {
            $query = " SELECT DISTINCT
           transportmeanname,
           externalreferencein,
           externalreferenceout,
           inwardbc11nr,
           inwardbc11date,
           outwardbc11nr,
           outwardbc11date 
        FROM
           t_visit 
        WHERE
           nr=(SELECT DISTINCT visit_id_2 FROM codeco_coarri WHERE visit_id_1 = '$TRKVisitID')";
        }

        $getData = $db_ctos->query($query)->row();
        return $getData;
    }

    public function op_inquiryCM($payload)
    {
        $out = new StdClass();

        try {
            $client = $this->get_soap_client();
            $response = $client->op_inquiryCM($payload);

            $out->success = true;
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

    public function op_cekDoc($payload)
    {
        $out = new StdClass();

        try {
            $client = $this->get_soap_client2();
            $response = $client->op_cekDoc($payload);

            $out->success = true;
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

    public function ann_import_bl($payload)
    {
        $out = new StdClass();
        try {
            // $client = $this->get_soap_client();
            $client = $this->get_soap_client2();
            $response = $client->op_AnnounceTruckImport($payload);

            $out->success = true;
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

    public function op_insertCargo($payload) {
        $out = new StdClass();
        try {
            $response = $this->get_soap_client2()->op_insertCargo($payload);
            $out->success = true;
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
            $out->msg = 'Internal Server Error :</br>'.$e;
        }

        return $out;
    }

    public function op_updateCargo($input) {
        // $db_ctos = $this->load->database(ILCS_CARTOS, TRUE); //prod
        $db_ctos = $this->load->database(ILCS_TPS_ONLINE, TRUE); //dev

        $current = "SELECT JUMLAH_CARGO, CURRENT_CARGO FROM MST_JUMLAH_CARGO 
                            WHERE CUSTOMS_NUMBER = '".$input['noDocU']."'
                                AND CUSTOMS_DATE = '".$input['tglDocU']."'
                                AND NPWP = '".$input['NPWPU']."'
                                AND KD_DOK = '".$input['kdDocU']."'";
        $cargo = $db_ctos->query($current)->row();
        
        $responsearray = [];
        if($input['totalCargo'] < $cargo->JUMLAH_CARGO) {
            $response = new stdClass;
            $response->Code = '204';
            $response->Msg = 'Warning: Tidak boleh memasukan jumlah cargo lebih kecil dari total cargo!';
            $responsearray['success'] = 1;
            $responsearray['response'] = $response;

            return $responsearray;
        } else {
            $currentCargo = $input['totalCargo'] - $cargo->JUMLAH_CARGO + $cargo->CURRENT_CARGO;
            $query = "UPDATE
                            MST_JUMLAH_CARGO
                        SET
                            UPDATE_TIME = SYSDATE,
                            JUMLAH_CARGO  = '".$input['totalCargo']."',
                            CURRENT_CARGO  = '".$currentCargo."',
                            MERK_KMS  = '".$input['merkKemasan']."',
                            DOCUMENT_PENDUKUNG  = '".$input['docFile']."'
                        WHERE
                            CUSTOMS_NUMBER = '".$input['noDocU']."'
                            AND CUSTOMS_DATE = '".$input['tglDocU']."'
                            AND NPWP = '".$input['NPWPU']."'
                            AND KD_DOK = '".$input['kdDocU']."'";
            // echo "<pre>";
            // print_r($query);
            // exit;
            $getData = $db_ctos->query($query);

            // create array response
            if($getData) {
                $response = new stdClass;
                $response->Code = '200';
                $response->Msg = 'Success Update!';
                $responsearray['success'] = 1;
                $responsearray['response'] = $response;
            } else {
                $response = new stdClass;
                $response->Code = '204';
                $response->Msg = 'Failed Update!';
                $responsearray['success'] = 1;
                $responsearray['response'] = $response;
            }
            
            return $responsearray;
        }
    }

    public function get_list_document($sender) {
        // $db_ctos = $this->load->database(ILCS_CARTOS, TRUE); //prod
        $db_ctos = $this->load->database(ILCS_TPS_ONLINE, TRUE); //dev
        $param = "";
        
        if($sender != "IKT") {
            $param .= " WHERE SENDER = '$sender' ";
        }
        
        // $query = "SELECT * from MST_JENIS_DOKUMEN ORDER BY ID + 0";
        $query = "SELECT 
                        a.CUSTOMS_NUMBER AS NOMOR_DOKUMEN,
                        a.CUSTOMS_DATE AS TANGGAL,
                        a.NPWP, a.KD_DOK AS KODE,
                        CONCAT(CONCAT(b.ID, ' - '), b.DOC_TYPE) AS TIPE_DOKUMEN,
                        a.JUMLAH_CARGO,
                        a.MERK_KMS,
                        a.DOCUMENT_PENDUKUNG
                    FROM MST_JUMLAH_CARGO a
                    JOIN MST_JENIS_DOKUMEN b
                        ON a.KD_DOK = b.ID
                    $param";
        // echo "<pre>";
        // print_r($query);
        // exit;
        $getData = $db_ctos->query($query)->result();
        
        return $getData;
    }

    public function count_all_document() {
        // $db_ctos = $this->load->database(ILCS_CARTOS, TRUE); //prod
        $db_ctos = $this->load->database(ILCS_TPS_ONLINE, TRUE); //dev
        $query = "SELECT COUNT(1) as total_all FROM MST_JUMLAH_CARGO";
        $getData = $db_ctos->query($query)->row();
        
        return $getData->TOTAL_ALL;
    }

    public function count_filtered_document() {
        // $db_ctos = $this->load->database(ILCS_CARTOS, TRUE); //prod
        $db_ctos = $this->load->database(ILCS_TPS_ONLINE, TRUE); //dev
        $query = "SELECT COUNT(1) as total_filtered FROM MST_JUMLAH_CARGO";
        $getData = $db_ctos->query($query)->row();

        return $getData->TOTAL_FILTERED;
    }

    public function selectDoc() {
        // $db_ctos = $this->load->database(ILCS_CARTOS, TRUE); //prod
        $db_ctos = $this->load->database(ILCS_TPS_ONLINE, TRUE); //dev
        $param = "";

        $sender = $this->userauth->getLoginData()->sender;
        if($sender != 'IKT') {
            $param .= " WHERE SENDER = '$sender'";
        } else {
            // $param .= " WHERE CURRENT_CARGO > 0";
            $param .= "";
        }
        
        $query = "SELECT CUSTOMS_NUMBER FROM MST_JUMLAH_CARGO $param";
        $getData = $db_ctos->query($query)->result();
        
        return $getData;
    }

    public function getDocument($noDoc, $getSisa = null) {
        // $db_ctos = $this->load->database(ILCS_CARTOS, TRUE); //prod
        $db_ctos = $this->load->database(ILCS_TPS_ONLINE, TRUE); //dev

        if($getSisa != "" || $getSisa != null) {
            $getSisaX = explode('R4ha51A', $getSisa);
            $counter = array_count_values($getSisaX);
            foreach ($counter as $doc => $row) {
                if($doc == $noDoc) {
                    $minCargo = $row - 1;
                    // $queryC = "SELECT CUSTOMS_NUMBER, CUSTOMS_DATE, NPWP, KD_DOK, JUMLAH_CARGO, (SELECT CURRENT_CARGO - $row AS SISA_CARGO FROM MST_JUMLAH_CARGO WHERE CUSTOMS_NUMBER = '".$noDoc."') AS SISA_CARGO FROM MST_JUMLAH_CARGO WHERE CUSTOMS_NUMBER = '".$noDoc."'";
                    $queryC = "SELECT CUSTOMS_NUMBER, CUSTOMS_DATE, NPWP, KD_DOK, JUMLAH_CARGO, (SELECT CURRENT_CARGO - $minCargo AS SISA_CARGO FROM MST_JUMLAH_CARGO WHERE CUSTOMS_NUMBER = '".$noDoc."') AS SISA_CARGO FROM MST_JUMLAH_CARGO WHERE CUSTOMS_NUMBER = '".$noDoc."'";
                    // echo $queryC;
                    // exit;
                    $getData = $db_ctos->query($queryC)->row();
                }
            }
        } else {
            // $query = "SELECT CUSTOMS_NUMBER, CUSTOMS_DATE, NPWP, KD_DOK, JUMLAH_CARGO, CURRENT_CARGO - 1 AS SISA_CARGO FROM MST_JUMLAH_CARGO WHERE CUSTOMS_NUMBER = '".$noDoc."'";
            $query = "SELECT CUSTOMS_NUMBER, CUSTOMS_DATE, NPWP, KD_DOK, JUMLAH_CARGO, CURRENT_CARGO AS SISA_CARGO FROM MST_JUMLAH_CARGO WHERE CUSTOMS_NUMBER = '".$noDoc."'";
            $getData = $db_ctos->query($query)->row();
        }
        
        return $getData;
    }

    // public function uploadBulk($data) {
    //     $db_ctos = $this->load->database(ILCS_CARTOS, TRUE); //prod
    //     $db_ctos = $this->load->database(ILCS_TPS_ONLINE, TRUE); //dev
    //     $queries = [];

    //     foreach ($data as $var) {
    //         $queries[] = "UPDATE
    //                 MST_JUMLAH_CARGO
    //             SET
    //             UPDATE_TIME = SYSDATE,
    //             JUMLAH_CARGO  = '".$var['totalCargo']."'
    //         WHERE
    //             CUSTOMS_NUMBER = '".$var['noDoc']."'";
    //     }

    //     foreach ($queries as $query) {
    //         $getData[] = $db_ctos->query($query);
    //     }

    //     $responsearray = [];
    //     foreach ($getData as $result) {
    //         if($result) {
    //             $response = new stdClass;
    //             $response->Code = '200';
    //             $response->Msg = 'Success Update!';
    //             $responsearray['success'] = 1;
    //             $responsearray['response'] = $response;
    //         } else {
    //             $response = new stdClass;
    //             $response->Code = '204';
    //             $response->Msg = 'Failed Update!';
    //             $responsearray['success'] = 1;
    //             $responsearray['response'] = $response;
    //         }
    //     }

    //     return $responsearray;
    // }

    public function getOldFile($input) {
        // $db_ctos = $this->load->database(ILCS_CARTOS, TRUE); //prod
        $db_ctos = $this->load->database(ILCS_TPS_ONLINE, TRUE); //dev
        $current = "SELECT DOCUMENT_PENDUKUNG FROM MST_JUMLAH_CARGO 
                            WHERE CUSTOMS_NUMBER = '".$input['noDocU']."'
                                AND CUSTOMS_DATE = '".$input['tglDocU']."'
                                AND NPWP = '".$input['NPWPU']."'
                                AND KD_DOK = '".$input['kdDocU']."'";
        $cargo = $db_ctos->query($current)->row();

        return $cargo->DOCUMENT_PENDUKUNG;
    }
}
