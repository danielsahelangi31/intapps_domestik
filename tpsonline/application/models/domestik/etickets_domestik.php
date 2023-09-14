<?php

class Etickets_domestik extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getPort()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $query = "SELECT PORT_CODE, (PORT_CODE || ' ~ ' ||PORT_NAME) AS NAME FROM M_PORT";
        $getPortData = $db_car->query($query)->result();
        return $getPortData;
    }

    public function getSearchPort($sender, $searchTerm="")
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $src = $db_car->select("PORT_CODE, (PORT_CODE || ' ~ ' ||PORT_NAME) AS NAME")->from('M_PORT')->where("(PORT_CODE || '~' || PORT_NAME) LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)->get()->result_array();
        $data = array();
        foreach($src as $q)
        {
            $data[] = array("id" => $q['PORT_CODE'], "text" => $q['NAME'], "value" => $q['PORT_CODE']);
        }
        return $data;
    }

    function uploadDataVin($vinNumber,$direction,$fuel,$modelCode,$finalLocation,$consigneeCode,$shippingInbound,$shippingOutbound,$idDocument)
    {

        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');

        $queryDuit = "SELECT NVL(LENGTH , 0) AS LENGTH, NVL(WIDTH, 0) AS WIDTH,
        NVL(HEIGHT, 0) AS HEIGHT, NVL(WEIGHT, 0) AS WEIGHT
        FROM M_CATEGORY
        WHERE ID_CATEGORY = '".$modelCode."'";
        $hasil = $db_car->query($queryDuit)->row();

        $length = $hasil->LENGTH;
        $width = $hasil->WIDTH;
        $height = $hasil->HEIGHT;
        $weight = $hasil->WEIGHT;
        $directionType = 'DOMESTIC';

        $query ="
        BEGIN PRC_ADD_CAR_LIST_CAR(
             '".$vinNumber."',
             '".$direction."',
             '".$directionType."',
             '".$modelCode."',
             '".$fuel."',
             '".$finalLocation."',
             '',
             '".$consigneeCode."',
             '".$shippingInbound."',
             '".$shippingOutbound."',
             '',
             '',
             '".$idDocument."',
             '".$width."',
             '".$length."',
             '".$height."',
             '".$weight."',
             '',
             '".$this->userauth->getLoginData()->id_user."',
            :out,
            :out1
        );

        END;";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":out", $out,300);
        oci_bind_by_name($stmt, ":out1", $out1,300);

        oci_execute($stmt);

        $result = array(
            'isError' => $out,
            'message' => $out1);

            // ini_set("display_errors", "On");
            // error_reporting(E_ALL);
            echo "<pre>\n";
            echo "\n"; echo "\n";
            print_r($result['isError']);
            echo "</pre>\n";

        return $result;

      }

      function uploadDataTruckOutbound($vinNumber,$direction,$fuel,$modelCode,$finalLocation,$consigneeCode,$shippingInbound,$shippingOutbound,$idDocument,$truckCode, $truckType, $truckCompany,$vesselName,$driverPhoneNumber,$truckData,$eticketType)
     {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');

        $queryDuit = "SELECT NVL(LENGTH , 0) AS LENGTH, NVL(WIDTH, 0) AS WIDTH,
        NVL(HEIGHT, 0) AS HEIGHT, NVL(WEIGHT, 0) AS WEIGHT
        FROM M_CATEGORY
        WHERE ID_CATEGORY = '".$modelCode."'";
        $hasil = $db_car->query($queryDuit)->row();

        $length = $hasil->LENGTH;
        $width = $hasil->WIDTH;
        $height = $hasil->HEIGHT;
        $weight = $hasil->WEIGHT;
        $directionType = 'DOMESTIC';

        $query ="
        BEGIN PRC_ADD_CAR_LIST_CAR(
             '".$vinNumber."',
             '".$direction."',
             '".$directionType."',
             '".$modelCode."',
             '".$fuel."',
             '".$finalLocation."',
             '',
             '".$consigneeCode."',
             '".$shippingInbound."',
             '".$shippingOutbound."',
             '',
             '',
             '".$idDocument."',
             '".$width."',
             '".$length."',
             '".$height."',
             '".$weight."',
             '',
             '".$this->userauth->getLoginData()->id_user."',
            :out,
            :out1
        );

        END;";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":out", $out,300);
        oci_bind_by_name($stmt, ":out1", $out1,300);

        oci_execute($stmt);

        $result = array(
            'isError' => $out,
            'message' => $out1);

        ini_set("display_errors", "On");
        // error_reporting(E_ALL);
        echo "<pre>\n";
        echo "\n"; echo "\n";
        print_r($result['isError']);
        echo "</pre>\n";
        // echo $out1;

        $tidQuery = "SELECT 'TRX' || ('TRUCKCODEYANGDIFORM') || TO_CHAR(SYSDATE, 'DDMMYY') || UPPER(DBMS_RANDOM.STRING('L',5))
        AS TID FROM DUAL";
        $tidRow = $db_car->query($tidQuery)->row();
        $TID = $tidRow->TID;

        $userId = $this->userauth->getLoginData()->id_user;

        if ($eticketType == 'on'){
            $eticketType = 'D'; //Inbound
       } else {
            $eticketType = 'L'; //Outbound
       }

        //outbound
        if($out1 == 0){
         echo $eticketType;
         if ($eticketType == 'L'){
            $queryCountTid = "SELECT COUNT(*) AS TID_COUNT from M_TRUCK
            WHERE TID='".$TID."'";
            $countTid = $db_car->query($queryCountTid)->row();

           if ($countTid->TID_COUNT > 0){
            echo 'ke delete';
            $deleteCar = "DELETE FROM CAR_LIST_CAR
            WHERE DOC_TRANSFERID = '".$idDocument."'";
            $dataCar = $db_car->query($deleteCar);

            $deleteGate = "DELETE FROM R_GATE_CAR
            WHERE DOC_TRANSFERID = '".$idDocument."'";
            $dataGate = $db_car->query($deleteGate);

            $deleteQuay = "DELETE FROM R_QUAY_CAR
            WHERE DOC_TRANSFERID = '".$idDocument."'";
            $dataQuay = $db_car->query($deleteQuay);

            return array(
                'message' => "Gagal Insert Data Truck (Duplikat TID)",
                'isError' => true,
            );

           } else {
               echo 'insert data';

               $lastIdMTruck = $db_car->query("SELECT ID_TRUCK from M_TRUCK ORDER BY ID_TRUCK DESC FETCH FIRST 1 ROWS ONLY")->row()->ID_TRUCK;

               $queryTruck = "SELECT TRUCK_TYPE,TRUCK_COMPANY_CODE FROM M_TRUCK_STID
               WHERE TRUCK_CODE ='".$truckCode."' AND IS_ACTIVE = 1";
               $dataTruck = $db_car->query($queryTruck)->row();

               $truckTypes = $dataTruck->TRUCK_TYPE;
               $truckCompanies = $dataTruck->TRUCK_COMPANY_CODE;

               $insertQuery_M_TRUCK = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, VESSEL_CODE, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, TID, ETICKET_TYPE, CREATED_BY, ID_TRUCK)
               VALUES ('".$truckCode."', '".$truckTypes."', '".$truckCompanies."',
               '".$vesselName."', '".$driverPhoneNumber."', SYSDATE, SYSDATE, '$TID',
               '".$eticketType."', '$userId',  $lastIdMTruck)";

               $insertQuery_TR_ASSOCIATION = "INSERT INTO TR_ASSOSIATION (TRUCK_CODE, DOC_TRANSFERID, CREATE_DATE, TRX, CREATED_BY)
               VALUES ('".$truckCode."', '".$idDocument."', SYSDATE, '$TID', '$userId')";

               $db_car->trans_begin();
               $db_car->query($insertQuery_TR_ASSOCIATION);
               $db_car->query($insertQuery_M_TRUCK);

               $db_car->trans_complete();

               if($db_car->trans_status()){
                   echo "Berhasil";
                   $out->success = true;
                   $out->msg = 'Berhasil insert data';
               }else{
                   echo "Gagal!";
                   $out->success = false;
                   $out->msg = 'Gagal input ke database, tidak ada data yang di update';
               }

           }
         }
        }

        //inbound
        if($out1 == 1){
            echo $eticketType;
            if ($eticketType == 'D'){
            $queryCountVin = "SELECT COUNT(*) AS VIN_COUNT FROM CAR_LIST_CAR
            WHERE VIN ='".$vinNumber."'";
            $countVin = $db_car->query($queryCountVin)->row();

            if ($countVin->VIN_COUNT > 0){
                echo 'sini vin';
                $updateQuery = "UPDATE CAR_LIST_CAR
                SET DOC_TRANSFERID = '".$idDocument."'
                WHERE VIN ='".$vinNumber."'";
                $updateVin = $db_car->query($updateQuery);

                $lastIdMTruck = $db_car->query("SELECT ID_TRUCK from M_TRUCK ORDER BY ID_TRUCK DESC FETCH FIRST 1 ROWS ONLY")->row()->ID_TRUCK;

                $queryTruck = "SELECT TRUCK_TYPE,TRUCK_COMPANY_CODE FROM M_TRUCK_STID
                WHERE TRUCK_CODE ='".$truckCode."' AND IS_ACTIVE = 1";
                $dataTruck = $db_car->query($queryTruck)->row();

                $truckTypes = $dataTruck->TRUCK_TYPE;
                $truckCompanies = $dataTruck->TRUCK_COMPANY_CODE;

                $insertQuery_M_TRUCK = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, VESSEL_CODE, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, TID, ETICKET_TYPE, CREATED_BY, ID_TRUCK)
                VALUES ('".$truckCode."', '".$truckTypes."', '".$truckCompanies."',
                '".$vesselName."', '".$driverPhoneNumber."', SYSDATE, SYSDATE, '$TID',
                '".$eticketType."', '$userId',  $lastIdMTruck)";

                $insertQuery_TR_ASSOCIATION = "INSERT INTO TR_ASSOSIATION (TRUCK_CODE, DOC_TRANSFERID, CREATE_DATE, TRX, CREATED_BY)
                VALUES ('".$truckCode."', '".$idDocument."', SYSDATE, '$TID', '$userId')";

                $db_car->trans_begin();
                $db_car->query($insertQuery_TR_ASSOCIATION);
                $db_car->query($insertQuery_M_TRUCK);

                $db_car->trans_complete();

                if($db_car->trans_status()){
                    echo "Berhasil";
                    $out->success = true;
                    $out->msg = 'Berhasil insert data';
                }else{
                    echo "Gagal!";
                    $out->success = false;
                    $out->msg = 'Gagal input ke database, tidak ada data yang di update';
                }


            }
          }
        }

        // echo $result['isError'];
        // echo $result['message'];
        return $result;
     }

      function insertTruckVINList($data){
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $result = array();
        $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');

        foreach($data as $item) {
            $associationCount = $db_car->query("SELECT COUNT(*) AS ASSOCIATION_COUNT FROM CAR_LIST_CAR WHERE DOC_TRANSFERID NOT IN 
            (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE DOC_TRANSFERID IN 
            (SELECT DOC_TRANSFERID FROM CAR_LIST_CAR) AND STATUS = 1) AND VIN = '".$item['vin']."'")->row()->ASSOCIATION_COUNT;
            if($associationCount >= 1){
                $db_car->trans_begin();
                $updateQuery = "UPDATE CAR_LIST_CAR
                    SET DOC_TRANSFERID = '".$item['docTfId']."'
                    WHERE VIN = '".strtoupper($item['vin'])."'";
                    $db_car->query($updateQuery);
                    if ($db_car->trans_status() === FALSE) {
                        $db_car->trans_rollback();
                        $result = array(
                            'message' => "Gagal dalam update VIN DocTfId",
                            'isError' => true,
                        );
                    } else {
                        $db_car->trans_commit();
                        $result = array(
                            'message' => "Suskes Update VIN DocTfId",
                            'isError' => false,
                        );
                    }
            }else{
                $queryDuit = "SELECT ID_CATEGORY, NVL(LENGTH , 0) AS LENGTH, NVL(WIDTH, 0) AS WIDTH,
                NVL(HEIGHT, 0) AS HEIGHT, NVL(WEIGHT, 0) AS WEIGHT
                FROM M_CATEGORY
                WHERE NAME = '".$item['model']."' FETCH FIRST 1 ROWS ONLY";
                $hasil = $db_car->query($queryDuit)->row();
    
                $category_id = $hasil->ID_CATEGORY;
                $length = $hasil->LENGTH;
                $width = $hasil->WIDTH;
                $height = $hasil->HEIGHT;
                $weight = $hasil->WEIGHT;
    
                $shippingLineNameSAI = $item['direction'] == 'D' ? $item['shippingLineName'] : "";
                $shippingLineNameSAO = $item['direction'] == 'L' ? $item['shippingLineName'] : "";
    
    
                $query ="
                BEGIN PRC_ADD_CAR_LIST_CAR(
                '".strtoupper($item['vin'])."',
                '".$item['direction']."',
                '".$item['directionType']."',
                '$category_id',
                '".$item['fuel']."',
                '".$item['destination']."',
                '',
                '".$item['shippingLineId']."',
                '".$shippingLineNameSAI."',
                '".$shippingLineNameSAO."',
                '',
                '',
                '".$item['docTfId']."',
                '".$width."',
                '".$length."',
                '".$height."',
                '".$weight."',
                '".$item['idVvd']."',
                '".$this->userauth->getLoginData()->id_user."',
                :out,
                :out1
                );
                END;";
               
    
                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ":out", $out,300);
                oci_bind_by_name($stmt, ":out1", $out1,300);
    
                oci_execute($stmt);
                $result = array(
                    'message' => $out,
                    'isError' => $out1);
                if($out1 == '1'){
                    //ERROR
                    break;
                }
            }

        }
      return $result;
    }

    function insertTruckData($truckData){

        // print_r($truckData);

        $userId = $this->userauth->getLoginData()->id_user;
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $tidQuery = $db_car->query("SELECT 'TRX' || '".$truckData['truckCode']."' || TO_CHAR(SYSDATE, 'DDMMYY') || UPPER(DBMS_RANDOM.STRING('L',5)) AS TID FROM DUAL");

        if ($tidQuery->num_rows() > 0)
        {
            $tidRow = $tidQuery->row();
            $TID = $tidRow->TID;

            $tidCount = $db_car->query("SELECT COUNT(*) AS TID_COUNT FROM M_TRUCK
            WHERE TID = '".$TID."' ")->row();

            if($tidCount->TID_COUNT > 0){
                $deleteVinQuery1 = "DELETE CAR_LIST_CAR WHERE DOC_TRANSFERID = '".$truckData['docTfId']."'";
                $deleteVinQuery2 = "DELETE R_GATE_CAR WHERE DOC_TRANSFERID = '".$truckData['docTfId']."'";
                $deleteVinQuery3 = "DELETE R_QUAY_CAR WHERE DOC_TRANSFERID = '".$truckData['docTfId']."'";
                $db_car->query($deleteVinQuery1);
                $db_car->query($deleteVinQuery2);
                $db_car->query($deleteVinQuery3);
                return array(
                    'message' => "Gagal Insert Data Truck (Duplikat TID)",
                    'isError' => true,
                );
            } else {

                $lastIdMTruck = $db_car->query("SELECT ID_TRUCK from M_TRUCK ORDER BY ID_TRUCK DESC FETCH FIRST 1 ROWS ONLY")->row()->ID_TRUCK;
                //Bau baunya ini idmtruck bakal diganti

                $lastIdMTruck = $lastIdMTruck + 1;
                
                //TODO ?? kenapa ini bisa disini ? BACKUP
                // $truckSTID = $db_car->query("SELECT ID from M_TRUCK_STID
                // WHERE TRUCK_CODE = '".$truckData['truckCode']."' ORDER BY ID DESC FETCH FIRST 1 ROWS ONLY")->row()->ID;

                $insertQuery_M_TRUCK = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, VESSEL_CODE, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, TID, ETICKET_TYPE, CREATED_BY, ID_TRUCK, INSERT_VIA, ID_TERMINAL, ORGANIZATION_ID_INTAPPS) VALUES ('".$truckData['truckCode']."', '".$truckData['truckType']."', '".$truckData['truckCompanyCode']."', '".$truckData['vesselCode']."', '".$truckData['driverPhoneNumber']."', NULL, SYSDATE, '$TID', '".$truckData['eticketType']."', '$userId',  $lastIdMTruck, 'INTAPPS', 400, '".implode("," ,$truckData['organizationId'])."')";

                $insertQuery_TR_ASSOCIATION = "INSERT INTO TR_ASSOSIATION (TRUCK_CODE, DOC_TRANSFERID, CREATE_DATE, TRX, CREATED_BY) VALUES ('".$truckData['truckCode']."', '".$truckData['docTfId']."', SYSDATE, '$TID', '$userId')";


                $db_car->trans_begin();
                $db_car->query($insertQuery_TR_ASSOCIATION);
                $db_car->query($insertQuery_M_TRUCK);
                if($truckData['eticketType'] == 'D'){
                    $updateQuery = "UPDATE CAR_LIST_CAR
                    SET DOC_TRANSFERID = '".$truckData['docTfId']."'
                    WHERE VIN IN (".sprintf("'%s'", implode("','" ,$truckData['listedVin'])).")";
                    $db_car->query($updateQuery);
                }

                if ($db_car->trans_status() === FALSE) {
                    $db_car->trans_rollback();
                    return array(
                        'message' => "Gagal Insert Data Truck",
                        'isError' => true,
                    );
                } else {
                    $db_car->trans_commit();
                    return array(
                        'message' => "Suskes Insert Data Truck",
                        'isError' => false,
                    );
                }
            }
        }
    }

    function insert_create_announce_vin($data){
        $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $result = array();

        foreach($data as $d) {
            $shippingLine = $db_car->query("SELECT NAME
            FROM M_ORGANIZATION
            WHERE ID = '".$d['idShippingLine']."'")->row();

            $v_sai = $d['direction'] == 'D' ? $shippingLine->NAME : '';
            $v_sao = $d['direction'] == 'L' ? $shippingLine->NAME : '';

            $getCar = $db_car->query("SELECT ID_CATEGORY, NVL(LENGTH , 0) AS LENGTH, NVL(WIDTH, 0) AS WIDTH,
            NVL(HEIGHT, 0) AS HEIGHT, NVL(WEIGHT, 0) AS WEIGHT
            FROM M_CATEGORY
            WHERE NAME = '".$d['model']."'")->row();
            $idCategory = $getCar->ID_CATEGORY;
            $width = $getCar->WIDTH;
            $length = $getCar->LENGTH;
            $height = $getCar->HEIGHT;
            $weight = $getCar->WEIGHT;
            $idUser = $this->userauth->getLoginData()->id_user;

            $query ="
            BEGIN PRC_ADD_CAR_LIST_CAR(
                '".$d['vinNum']."',
                '".$d['direction']."',
                '".$d['directionType']."',
                '".$idCategory."',
                '".$d['fuel']."',
                '".$d['portCode']."',
                '',
                '".$d['idShippingLine']."',
                '".$v_sai."',
                '".$v_sao."',
                '',
                '',
                '".$d['documentTransferId']."',
                '".$width."',
                '".$length."',
                '".$height."',
                '".$weight."',
                '',
                '".$idUser."',
                :out,
                :out1
            );
            END;";

            $stmt = oci_parse($conn, $query );
            oci_bind_by_name($stmt, ":out", $out,300);
            oci_bind_by_name($stmt, ":out1", $out1,300);

            oci_execute($stmt);

            $queryCount = "SELECT COUNT(VIN) AS jumlah_vin FROM CAR_LIST_CAR clc WHERE DOC_TRANSFERID = '".$d['documentTransferId']."'";
            $hasilCount = $db_car->query($queryCount)->row();

            $pesan = ""; 

            if($out == "VIN Sudah Ada Sebelumnya : ".$d['vinNum']) {
                $pesan = 'Gagal menyimpan vin '.$d["vinNum"].' di vin info ke '.$d["id"].": "."Vin sudah ada sebelumnya";
            } else if ($out == "POD tidak ditemukan") {
                $pesan = 'Gagal menyimpan vin '.$d["vinNum"].' di vin info ke '.$d["id"].": ".$out;
            } else {
                $pesan = 'Gagal menyimpan vin '.$d["vinNum"].' di vin info ke '.$d["id"];
            }

            $result = array(
              'isError' => $out1,
              'message' => $out1 == '1' ? $pesan : "",
              'doc' => $d['documentTransferId'],
              'jumlah_vin'=> $hasilCount->JUMLAH_VIN
            );

            if($out1 == '1'){
                //ERROR
                break;
            }
        }
        return $result;
    }

    public function getCategory()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $query = "SELECT VIN_CODE, NAME FROM M_CATEGORY";
        $getCategoryData = $db_car->query($query)->result();
        return $getCategoryData;
    }

    public function getSearchCategory($sender, $searchTerm)
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $src = $db_car->select("DISTINCT NAME, ID_CATEGORY", FALSE)
        ->from('M_CATEGORY')
        ->where("NAME LIKE '%".strtoupper($searchTerm)."%' FETCH FIRST 1 ROWS ONLY", NULL, FALSE)
        // ->or_like('VIN_CODE', strtoupper($searchTerm))
        ->get()
        ->result_array();
        $data = array();
        foreach($src as $q)
        {
            $data[] = array("id" => $q['ID_CATEGORY'], "text" => $q['NAME'], "value" => $q['ID_CATEGORY']);
        }
        return $data;
    }


    public function getTruckVesselName($searchTerm)
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $auth = $this->userauth->getLoginData();
        $src = "";

        if($auth->intapps_type == "ADMIN"){
            $src = $db_car->select("ID_VVD, (VESSEL_NAME || ' ' || VOY_IN || '/' || VOY_OUT || '~' || ETB) AS VESSEL_NAME", FALSE)
            ->from('VES_VOYAGE')
            ->where("(VESSEL_NAME || ' ' || VOY_IN || '/' || VOY_OUT || '~' || ETB) LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)
            ->where('ETA >= TRUNC(SYSDATE) - 60',NULL, FALSE) //60 Testing Only , 11 Production
            ->get()
            ->result_array();
        } else {
            $org_code = $db_car->query("select CODE from M_ORGANIZATION WHERE ID = '".$auth->intapps_type."' ")->row()->CODE;

            $src = $db_car->select("ID_VVD, (VESSEL_NAME || ' ' || VOY_IN || '/' || VOY_OUT || '~' || ETB) AS VESSEL_NAME", FALSE)
            ->from('VES_VOYAGE')
            ->where("(VESSEL_NAME || ' ' || VOY_IN || '/' || VOY_OUT || '~' || ETB) LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)
            ->where('ETA >= TRUNC(SYSDATE) - 60',NULL, FALSE) //60 Testing Only , 11 Production
            ->where('SL', $org_code)
            ->get()
            ->result_array();
        }

        $data = array();
        foreach($src as $q)
        {
            $data[] = array("id_vvd" => $q['ID_VVD'], "vessel_name" => $q['VESSEL_NAME']);
        }

        return $data;
    }

    public function getTruckCodeList($searchTerm)
    {
        $db_cartos = $this->load->database('ilcs_cartos', TRUE);
        $db_cardom = $this->load->database('integrasi_cardom_dev', TRUE);
        $data = array();

        $src1 = $db_cardom->select('PLAT_NO')
        ->from('M_TRUCK_STID')
        ->where("TYPE_TRUCK NOT IN ('SELFDRIVE')",NULL, FALSE)
        ->where('STATUS_CARD', 'active')
        ->like('PLAT_NO', strtoupper($searchTerm))
        ->get()
        ->result_array();

        $src2 = $db_cartos->select('PLAT_NO')
        ->from('STID_MST_TRUCK')
        ->where('STATUS_CARD', 'active')
        ->like('PLAT_NO', strtoupper($searchTerm))
        ->get()
        ->result_array();

        foreach($src1 as $q)
        {
            $data[] = array("truck_code" => $q['PLAT_NO']);
        }
        

        foreach($src2 as $q)
        {
            $data[] = array("truck_code" => $q['PLAT_NO']);
        }

        return $data;
    }

    public function getTruckCodeData($searchTerm)
    {
        $db_cartos = $this->load->database('ilcs_cartos', TRUE);
        $db_cardom = $this->load->database('integrasi_cardom_dev', TRUE);
        $data = array();
        
        $queryCartos = $db_cartos->query("SELECT
        TYPE_TRUCK,
        COMPANY_NAME,
        CASE
            WHEN TYPE_TRUCK = '1' THEN 'Trailer'
            WHEN TYPE_TRUCK = '2' THEN 'Truk Umum'
            WHEN TYPE_TRUCK = '3' THEN 'Truk Tangki'
            WHEN TYPE_TRUCK = '4' THEN 'Truk Mixer'
            WHEN TYPE_TRUCK = '5' THEN 'Truk Car Carrier'
            WHEN TYPE_TRUCK = '6' THEN 'Truk Box'
            ELSE TYPE_TRUCK
        END AS TYPE_TRUCK_NEW
        FROM (
            SELECT DISTINCT TYPE_TRUCK, COMPANY_NAME FROM STID_MST_TRUCK
            WHERE STID_MST_TRUCK.PLAT_NO = '$searchTerm'
        )");

        if ($queryCartos->num_rows() > 0){
            $typeTruck = $queryCartos->row()->TYPE_TRUCK_NEW;
            $companyName = $queryCartos->row()->COMPANY_NAME;


            $data[] = array("truck_type" => $typeTruck, "truck_company_name" => $companyName);

            return $data;
        } else {
            $queryCardom = $db_cardom->query("SELECT
                TYPE_TRUCK,
                COMPANY_NAME,
                CASE
                    WHEN TYPE_TRUCK = '1' THEN 'Trailer'
                    WHEN TYPE_TRUCK = '2' THEN 'Truk Umum'
                    WHEN TYPE_TRUCK = '3' THEN 'Truk Tangki'
                    WHEN TYPE_TRUCK = '4' THEN 'Truk Mixer'
                    WHEN TYPE_TRUCK = '5' THEN 'Truk Car Carrier'
                    WHEN TYPE_TRUCK = '6' THEN 'Truk Box'
                    ELSE TYPE_TRUCK
                END AS TYPE_TRUCK_NEW
            FROM (
                SELECT DISTINCT TYPE_TRUCK, COMPANY_NAME FROM M_TRUCK_STID
                WHERE M_TRUCK_STID.PLAT_NO = '$searchTerm'
            )")->row();

            $typeTruck = $queryCardom->TYPE_TRUCK_NEW;
            $companyName = $queryCardom->COMPANY_NAME;

            $data[] = array("truck_type" => $typeTruck, "truck_company_name" => $companyName);

            return $data;
        }
    }

    public function checkTruckActivity($truckCode)
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $activityCount = $db_car->query("SELECT COUNT(*) AS ACTIVITY_COUNT FROM M_TRUCK WHERE NO_POL = '".$truckCode."' AND TRUNC(DATE_INSERTED) = TRUNC(SYSDATE)")->row()->ACTIVITY_COUNT;

        if($activityCount >= 1){
            $activityCountToday = $db_car->query("SELECT COUNT(*) AS ACTIVITY_COUNT FROM M_TRUCK WHERE NO_POL = '".$truckCode."' AND OUT_TERMINAL_DATE IS NULL AND TRUNC(DATE_INSERTED) = TRUNC(SYSDATE)")->row()->ACTIVITY_COUNT;
            if($activityCountToday >= 1){
                return array(
                    'message' => "Maaf Truck Sedang Dipakai dan Aktivas Belum Selesai",
                    'isError' => true,
                );
            }else {
                return array(
                    'message' => "Boleh Insert Data Truck",
                    'isError' => false,
                );
            }
        }else {
            return array(
                'message' => "Boleh Insert Data Truck",
                'isError' => false,
            );
        }

    }

    public function vinModel()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $vin = $this->input->post('vin');
        $query = "SELECT VIN_CODE, ID_CATEGORY, NAME FROM M_CATEGORY WHERE VIN_CODE = '".strtoupper($vin)."'";
        $getVinModel = $db_car->query($query)->result();
        return $getVinModel;
    }

    public function getShippingLine()
    {
        $auth = $this->userauth->getLoginData();
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);

        if($auth->intapps_type == "ADMIN")
        {
            $query = "SELECT ID, NAME FROM M_ORGANIZATION WHERE TYPE = 'SHIPPING_LINE'";
            $data = $db_car->query($query)->result();
            return $data;
        } else
        {
            $query = "SELECT ID, CODE, (NAME || ' (' || CODE || ')' || '~' || ID) AS NAME FROM M_ORGANIZATION WHERE ID = '$auth->intapps_type'";
            $data = $db_car->query($query)->result();
            return $data;
        }
    }

    public function getSearchShippingLine($searchTerm="")
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $query = $db_car->select("ID, CODE, (NAME || ' (' || CODE || ')') AS NAME", FALSE)
                        ->from('M_ORGANIZATION')
                        ->where('TYPE', 'SHIPPING_LINE')
                        ->where("(NAME || ' (' || CODE || ')') LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)
                        ->get()
                        ->result_array();
        // $query = $db_car->select('ID, NAME')->from('M_ORGANIZATION')->where("NAME LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)->where("TYPE = 'SHIPPING_LINE'")->get()->result_array();
        $data = array();
        foreach($query as $q)
        {
            $data[] = array("id" => $q['ID'], "text" => $q['NAME'], "value" => $q['ID']);
        }
        return $data;
    }

    public function getSearchVin($searchTerm, $isInbound)
    {
        $auth = $this->userauth->getLoginData();
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $src = "";
       
        if($isInbound == "true"){
            if($auth->intapps_type == "ADMIN"){
                $src = $db_car->select('VIN')->from('CAR_LIST_CAR')
                ->like('VIN', strtoupper($searchTerm))
                ->where('EI', 'D')
                // ->where('STATUS', 'ON TERMINAL')
                ->where('DOC_TRANSFERID NOT IN (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE DOC_TRANSFERID IN (SELECT DOC_TRANSFERID FROM CAR_LIST_CAR) AND STATUS = 1)', NULL, FALSE)
                ->get()
                ->result_array();
            }else{
                $src = $db_car->select('VIN')->from('CAR_LIST_CAR')
                ->like('VIN', strtoupper($searchTerm))
                ->where('EI', 'D')
                // ->where('STATUS', 'ON TERMINAL')
                ->where('DOC_TRANSFERID NOT IN (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE DOC_TRANSFERID IN (SELECT DOC_TRANSFERID FROM CAR_LIST_CAR) AND STATUS = 1)', NULL, FALSE)
                ->where('CREATE_BY', $auth->id_user)
                ->get()
                ->result_array();
            }
        } else {
            if($auth->intapps_type == "ADMIN"){
                $src = $db_car->select('VIN')->from('CAR_LIST_CAR')
                ->like('VIN', strtoupper($searchTerm))
                ->where('EI', 'L')
                ->where('STATUS', 'ANNOUNCEMENT')
                ->where('DOC_TRANSFERID NOT IN (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE DOC_TRANSFERID IN (SELECT DOC_TRANSFERID FROM CAR_LIST_CAR) AND STATUS = 1)', NULL, FALSE)
                ->get()
                ->result_array();
            }else{
                $src = $db_car->select('VIN')->from('CAR_LIST_CAR')
                ->like('VIN', strtoupper($searchTerm))
                ->where('EI', 'L')
                ->where('STATUS', 'ANNOUNCEMENT')
                ->where('DOC_TRANSFERID NOT IN (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE DOC_TRANSFERID IN (SELECT DOC_TRANSFERID FROM CAR_LIST_CAR) AND STATUS = 1)', NULL, FALSE)
                ->where('CREATE_BY', $auth->id_user)
                ->get()
                ->result_array();
            }
        }
        
        
        $data = array();
        foreach($src as $q)
        {
            $data[] = array("id" => $q['VIN'], "text" => $q['VIN']);
        }
        return $data;
    }

    public function getVinData($searchTerm)
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $query = "SELECT  clc.VIN, mc.VIN_CODE, mc.NAME,
        clc.FUEL_TYPE,
        mp.PORT_CODE, (mp.PORT_CODE || '~' || mp.PORT_NAME) AS PORT_NAME,
        mo.ID AS ORG_ID, (mo.NAME || ' (' || mo.CODE || ')~' || mo.ID) AS SHIPPING_LINE
        FROM CAR_LIST_CAR clc
        JOIN M_CATEGORY mc
        ON clc.ID_CATEGORY_INTAPPS = mc.ID_CATEGORY
        JOIN M_PORT mp
        ON clc.POD = mp.PORT_CODE
        JOIN M_ORGANIZATION mo ON TO_CHAR(mo.ID) = clc.CONSIGNEE
        WHERE clc.VIN = '".strtoupper($searchTerm)."'";



        $result = $db_car->query($query)->result();
        return $result;

        // $src = $db_car->select("M_CATEGORY.VIN_CODE, (M_CATEGORY.VIN_CODE || '~' || M_CATEGORY.NAME) AS NAME, CAR_LIST_CAR.FUEL_TYPE, M_PORT.PORT_CODE, (M_PORT.PORT_CODE || '~' || M_PORT.PORT_NAME) AS PORT_NAME", FALSE)
        // ->from('CAR_LIST_CAR')
        // ->join('M_CATEGORY', 'CAR_LIST_CAR.ID_CATEGORY = M_CATEGORY.ID_CATEGORY')
        // ->join('M_PORT', 'CAR_LIST_CAR.POD = M_PORT.PORT_CODE')
        // ->where("CAR_LIST_CAR.VIN", $searchTerm)
        // ->get()
        // ->result_array();

        // $data = array();
        // foreach($src as $q)
        // {
        //     $data[] = array("VIN_CODE" => $q['VIN_CODE'], "NAME" => $q['NAME'], "FUEL_TYPE" => $q['FUEL_TYPE'], "PORT_CODE" => $q['PORT_CODE'], "PORT_NAME" => $q['PORT_NAME'] );
        // }
        // return $data;
    }

    public function getVinOutboundData($searchTerm)
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $query = "SELECT NAME FROM M_CATEGORY WHERE VIN_CODE = '".strtoupper($searchTerm)."'";
        $getVinModel = $db_car->query($query)->result();
        return $getVinModel;
    }

    public function getModelOutbound($searchTerm)
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $src = $db_car->select("DISTINCT NAME", FALSE)
        ->from('M_CATEGORY')
        ->where("NAME LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)
        ->get()
        ->result_array();
        $data = array();
        foreach($src as $q)
        {
            $data[] = array("id" => $q['NAME'], "text" => $q['NAME']);
        }
        return $data;
    }

    public function getDestinationOutbound($searchTerm){
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $src = $db_car->select("PORT_CODE, (PORT_CODE || '~' || PORT_NAME) AS PORT_NAME", FALSE)
        ->from('M_PORT')
        ->where("(PORT_CODE || '~' || PORT_NAME) LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)
        // ->or_like('VIN_CODE', strtoupper($searchTerm))
        ->get()
        ->result_array();
        $data = array();
        foreach($src as $q)
        {
            $data[] = array("id" => $q['PORT_CODE'], "text" => $q['PORT_NAME']);
        }
        return $data;
    }

    public function getTruckShippingLine($searchTerm)
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $src = $db_car->select("ID, CODE, NAME, (NAME || ' (' || CODE || ')') AS FULL_NAME", FALSE)
        ->from('M_ORGANIZATION')
        ->where('TYPE', 'SHIPPING_LINE')
        ->where("(NAME || ' (' || CODE || ')') LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)
        ->get()
        ->result_array();
        $data = array();
        foreach($src as $q)
        {
            $data[] = array("id" => $q['ID'], "name" => $q['NAME'], "full_name" => $q['FULL_NAME'], "value" => $q['CODE']);
        }
        return $data;
    }

    public function getData()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $auth = $this->userauth->getLoginData();

        $from = $this->input->post('from');
        $to = $this->input->post('to');

        if($from == "" and $to == "") {
            if($auth->intapps_type == "ADMIN")
            {
                $query = "SELECT 
                    NO_POL AS TRUCK_CODE_NEW,
                    NO_POL AS LICENSE_PLATE_NEW,
                    TID AS TRUCK_VISIT_ID_NEW,
                    CASE
                        WHEN ETICKET_TYPE = 'D' THEN 'INBOUND (DISCHARGE)'
                        WHEN ETICKET_TYPE = 'L' THEN 'OUTBOUND (LOADING)'
                        WHEN ETICKET_TYPE = 'R' THEN 'RETURN CARGO'
                        ELSE 'NOT FROM INTAPPS'
                    END AS DIRECTION_NEW,
                    to_char(LAST_ACTIVITY, 'hh24:mi:ss') AS LAST_TIME_ACTIVITY_NEW,
                    LAST_ACTIVITY AS LAST_ACTIVITY_NEW, 
                    STATUS_GATE
                FROM M_TRUCK
                ORDER BY LAST_ACTIVITY DESC FETCH NEXT 350 ROWS ONLY";
                $hasil = $db_car->query($query);
                return $hasil;
            } else
            {
                $query = "SELECT 
                    NO_POL AS TRUCK_CODE_NEW,
                    NO_POL AS LICENSE_PLATE_NEW,
                    TID AS TRUCK_VISIT_ID_NEW,
                    CASE
                        WHEN ETICKET_TYPE = 'D' THEN 'INBOUND (DISCHARGE)'
                        WHEN ETICKET_TYPE = 'L' THEN 'OUTBOUND (LOADING)'
                        WHEN ETICKET_TYPE = 'R' THEN 'RETURN CARGO'
                        ELSE 'NOT FROM INTAPPS'
                    END AS DIRECTION_NEW,
                    to_char(LAST_ACTIVITY, 'hh24:mi:ss') AS LAST_TIME_ACTIVITY_NEW,
                    LAST_ACTIVITY AS LAST_ACTIVITY_NEW,
                    STATUS_GATE
                FROM M_TRUCK
                WHERE CREATED_BY = '1547'
                ORDER BY LAST_ACTIVITY DESC FETCH NEXT 350 ROWS ONLY";
                $hasil = $db_car->query($query);
                return $hasil;
            }
        } else {
            if($auth->intapps_type == "ADMIN")
            {
                $query = "SELECT 
                    NO_POL AS TRUCK_CODE_NEW,
                    NO_POL AS LICENSE_PLATE_NEW,
                    TID AS TRUCK_VISIT_ID_NEW,
                    CASE
                        WHEN ETICKET_TYPE = 'D' THEN 'INBOUND (DISCHARGE)'
                        WHEN ETICKET_TYPE = 'L' THEN 'OUTBOUND (LOADING)'
                        WHEN ETICKET_TYPE = 'R' THEN 'RETURN CARGO'
                        ELSE 'NOT FROM INTAPPS'
                    END AS DIRECTION_NEW,
                    to_char(LAST_ACTIVITY, 'hh24:mi:ss') AS LAST_TIME_ACTIVITY_NEW,
                    LAST_ACTIVITY AS LAST_ACTIVITY_NEW, 
                    STATUS_GATE
                FROM M_TRUCK
                WHERE trunc(DATE_INSERTED) BETWEEN TO_DATE('".$from." 00:00', 'YYYY-MM-DD HH24:MI') AND TO_DATE('".$to." 00:00', 'YYYY-MM-DD HH24:MI')
                ORDER BY LAST_ACTIVITY DESC FETCH NEXT 350 ROWS ONLY";
                $hasil = $db_car->query($query);
                return $hasil;
            } else
            {
                $query = "SELECT 
                    NO_POL AS TRUCK_CODE_NEW,
                    NO_POL AS LICENSE_PLATE_NEW,
                    TID AS TRUCK_VISIT_ID_NEW,
                    CASE
                        WHEN ETICKET_TYPE = 'D' THEN 'INBOUND (DISCHARGE)'
                        WHEN ETICKET_TYPE = 'L' THEN 'OUTBOUND (LOADING)'
                        WHEN ETICKET_TYPE = 'R' THEN 'RETURN CARGO'
                        ELSE 'NOT FROM INTAPPS'
                    END AS DIRECTION_NEW,
                    to_char(LAST_ACTIVITY, 'hh24:mi:ss') AS LAST_TIME_ACTIVITY_NEW,
                    LAST_ACTIVITY AS LAST_ACTIVITY_NEW,
                    STATUS_GATE
                FROM M_TRUCK
                WHERE CREATED_BY = '1547'
                AND trunc(DATE_INSERTED) BETWEEN TO_DATE('".$from." 00:00', 'YYYY-MM-DD HH24:MI') AND TO_DATE('".$to." 00:00', 'YYYY-MM-DD HH24:MI')
                ORDER BY LAST_ACTIVITY DESC FETCH NEXT 350 ROWS ONLY";
                $hasil = $db_car->query($query);
                return $hasil;
            }
        }
        
    }

    public function hapusRow($trx) 
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $db_car->query("DELETE FROM M_TRUCK WHERE STATUS_GATE IS NULL AND TID = '".$trx."'");
        $db_car->query("DELETE FROM TR_ASSOSIATION WHERE TRX = '".$trx."'");
    }

    public function geDataTruck($param)
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        // $query = "SELECT count(B.TRUCK_CODE) jtc, COUNT(C.TID) AS jtid, count(C.ETICKET_TYPE) AS jetic, 
        //           COUNT(C.VESSEL_CODE) AS jvcode, count(D.VESSEL_NAME) jvname, count(D.VOYAGE) AS jvy, 
        //           count(A.VIN) AS jv, count(A.POD) AS jp, count(E.PORT_NAME) AS jpn, A.POD, E.PORT_NAME, A.VIN, 
        //           B.TRUCK_CODE AS TC,
        //           C.TID AS TRUCK_VISIT_ID_NEW,
        //           C.ETICKET_TYPE,
        //           CASE
        //             WHEN ETICKET_TYPE = 'D' THEN 'INBOUND'
        //             WHEN ETICKET_TYPE = 'L' THEN 'OUTBOUND'
        //             ELSE 'NOT FROM INTAPPS'
        //           END AS ETICKET_TYPE_NAME,
        //           A.POD AS DESTINATION_CODE,
        //           E.PORT_NAME AS DESTINATION_NAME_NEW,
        //           C.VESSEL_CODE,
        //           (D.VESSEL_NAME || ' ' || D.VOYAGE) AS VESSEL_NAME_NEW
        //           FROM CAR_LIST_CAR A
        //           LEFT JOIN TR_ASSOSIATION B ON B.DOC_TRANSFERID = A.DOC_TRANSFERID
        //           LEFT JOIN M_TRUCK C ON C.TID = B.TRX
        //           LEFT JOIN VES_VOYAGE D ON D.ID_VVD = C.VESSEL_CODE
        //           LEFT JOIN M_PORT E ON E.PORT_CODE = A.POD
        //           WHERE A.DOC_TRANSFERID = (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE TRX = '".$param."')
        //           GROUP BY B.TRUCK_CODE , C.TID, C.ETICKET_TYPE, 
        //           C.VESSEL_CODE, D.VESSEL_NAME, D.VOYAGE, A.VIN, A.POD, E.PORT_NAME";
        $queryVin = "SELECT VIN, POD FROM CAR_LIST_CAR clc WHERE DOC_TRANSFERID = (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION ta WHERE TRX = '".$param."')";
        $hasilVin = $db_car->query($queryVin)->result(); 
        
        $hasilPort = array();
        for($i = 0; $i < count($hasilVin); $i++) {
            $queryPort = "SELECT PORT_NAME FROM M_PORT mp WHERE PORT_CODE = '".$hasilVin[$i]->POD."'";
            $hasilPort[] = $db_car->query($queryPort)->row();
        }

        $dataTruck = $db_car->query("SELECT VESSEL_CODE, CASE WHEN ETICKET_TYPE = 'D' THEN 'INBOUND' WHEN ETICKET_TYPE = 'L' THEN 'OUTBOUND' ELSE 'NOT FROM INTAPPS' END AS DIRECTION FROM M_TRUCK mt WHERE TID = '".$param."'")->row();
        $dataTr = $db_car->query("SELECT TRUCK_CODE FROM TR_ASSOSIATION WHERE TRX = '".$param."'")->row();
        $dataVessel = $db_car->query("SELECT (VESSEL_NAME || ' ' || VOYAGE) AS NAME FROM VES_VOYAGE vv WHERE ID_VVD = '".$dataTruck->VESSEL_CODE."'")->result();
        
        $hasil = array(
            "vin" => $hasilVin,
            "tr" => $dataTr,
            "truck" => $dataTruck,
            "vessel" => $dataVessel[0]->NAME, 
            "port" => $hasilPort
        );

        // for($i = 0; $i < count($hasil["vin"]); $i++) {
        //     print_r($hasil["vin"][$i]);
        //     print_r($hasil["port"][$i]);
        // } exit();
        return $hasil;
    }

    public function dataShipping()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $auth = $this->userauth->getLoginData();

        $query = "SELECT ID, CODE, (NAME || '(' || CODE || ')') AS NAME FROM M_ORGANIZATION WHERE ID = '".$auth->intapps_type."'";
        $hasil = $db_car->query($query)->result();
        return $hasil;
    }

    public function insertVinEticket()
    {
        $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);

        $vinNum = $this->input->post('dataVinNum');
        $direction = $this->input->post('dataDirection') == "INBOUND" ? "D" : "L";
        $directionType = $this->input->post('dataDirectionType');
        $idCategory = $this->input->post('dataIdCategory');
        $fuel = $this->input->post('dataFuel');
        $portCode = $this->input->post('dataPortCode');
        $idShippingLine = $this->input->post('dataIdShippingLine');
        $trx = $this->input->post('dataTruckVisit');
        $truckCode = $this->input->post('dataTruckCode');

        $shippingLine = $db_car->query("SELECT NAME
        FROM M_ORGANIZATION 
        WHERE ID = '".$idShippingLine."'")->row();
        
        $v_sai = $direction == 'D' ? $shippingLine->NAME : '';
        $v_sao = $direction == 'L' ? $shippingLine->NAME : '';
        
        $queryDoc = "SELECT DOC_TRANSFERID FROM TR_ASSOSIATION ta WHERE TRX = '".$trx."'";
        $resultDoc = $db_car->query($queryDoc)->row();
        $documentTransferId = $resultDoc->DOC_TRANSFERID;

        $queryVc = "SELECT VESSEL_CODE FROM M_TRUCK WHERE TID = '".$trx."'";
        $resultVc = $db_car->query($queryVc)->row();
        $vc = $resultVc->VESSEL_CODE;
        
        $getCar = $db_car->query("SELECT NVL(LENGTH , 0) AS LENGTH, NVL(WIDTH, 0) AS WIDTH,
        NVL(HEIGHT, 0) AS HEIGHT, NVL(WEIGHT, 0) AS WEIGHT
        FROM M_CATEGORY 
        WHERE ID_CATEGORY = '".$idCategory."'")->row();
        $width = $getCar->WIDTH;
        $length = $getCar->LENGTH;
        $height = $getCar->HEIGHT;
        $weight = $getCar->WEIGHT;
        $idUser = $this->userauth->getLoginData()->id_user;

        $exis = $db_car->query("SELECT COUNT(*) AS EXIS FROM CAR_LIST_CAR WHERE VIN = '".$vinNum."'")->row();
        if ($exis->EXIS != 0) {
            $result = array(
                "exis" => 1,
                "pesan" => "Vin sudah ada sebelumnya"
            ); 
            return $result;
        } else {
            $query ="";
            if($trx){
                $query ="
                BEGIN PRC_ADD_CAR_LIST_CAR(
                    '".$vinNum."',
                    '".$direction."',
                    '".$directionType."',
                    '".$idCategory."',
                    '".$fuel."',
                    '".$portCode."',
                    '',
                    '".$idShippingLine."',
                    '".$v_sai."',
                    '".$v_sao."',
                    '',
                    '',
                    '".$documentTransferId."',
                    '".$width."',
                    '".$length."',
                    '".$height."',
                    '".$weight."',
                    '".$vc."',
                    '".$idUser."',
                    :out,
                    :out1
                );
                END;";
            } else {
                $query ="
                BEGIN PRC_ADD_CAR_LIST_CAR(
                    '".$vinNum."',
                    '".$direction."',
                    '".$directionType."',
                    '".$idCategory."',
                    '".$fuel."',
                    '".$portCode."',
                    '',
                    '".$idShippingLine."',
                    '".$v_sai."',
                    '".$v_sao."',
                    '',
                    '',
                    '".$documentTransferId."',
                    '".$width."',
                    '".$length."',
                    '".$height."',
                    '".$weight."',
                    '',
                    '".$idUser."',
                    :out,
                    :out1
                );
                END;";
            }
            
            $stmt = oci_parse($conn, $query );
            oci_bind_by_name($stmt, ":out", $out,300);
            oci_bind_by_name($stmt, ":out1", $out1,300);
    
            oci_execute($stmt);
            
            $result = array(
                'isError' => $out1,
                'message' => $out);

            return $result;
        }
    }
}
