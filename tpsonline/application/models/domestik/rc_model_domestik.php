<?php 

class Rc_model_domestik extends CI_Model
{
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

    public function getTableItems(){
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $auth = $this->userauth->getLoginData();
        $idUser = $this->userauth->getLoginData()->id_user;
        $query = "";

        if($auth->intapps_type == "ADMIN"){
            $query = $db_car->query("SELECT 
            DISTINCT A.VIN AS VIN_NEW,
            A.MODEL AS MODEL_NEW,
            A.DATE_INPUT,
            NVL(A.ID_CATEGORY_INTAPPS , 0) AS MODEL_CODE,
            NVL(A.CONSIGNEE , ' ') AS SHIPPING_CODE_NEW,
            NVL(B.NAME , ' ') AS SHIPPING_NAME_NEW,
            A.STATUS AS STATUS_NEW,
            NVL(A.DOC_TRANSFERID , ' ') AS DOC_TRANSFERID ,
            NVL(D.VESSEL_CODE , ' ') AS VESSEL_CODE_NEW,
            (E.VESSEL_NAME || ' ' || E.VOYAGE) AS VESSEL_NAME_NEW
            FROM CAR_LIST_CAR A
            LEFT JOIN M_ORGANIZATION B ON TO_CHAR(B.ID) = A.CONSIGNEE
            LEFT JOIN TR_ASSOSIATION C ON C.DOC_TRANSFERID = A.DOC_TRANSFERID
            LEFT JOIN M_TRUCK D ON D.TID = C.TRX
            LEFT JOIN VES_VOYAGE E ON E.ID_VVD = D.VESSEL_CODE
            WHERE A.CREATE_BY IS NOT NULL
            -- AND A.DOC_TRANSFERID NOT IN (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE DOC_TRANSFERID IN (SELECT DOC_TRANSFERID FROM CAR_LIST_CAR))
            AND A.EI = 'L'
            AND A.STATUS = 'ON TERMINAL' 
            ORDER BY A.DATE_INPUT DESC");
        } else {
            $query = $db_car->query("SELECT 
            A.VIN AS VIN_NEW,
            A.MODEL AS MODEL_NEW,
            A.DATE_INPUT,
            NVL(A.ID_CATEGORY_INTAPPS , 0) AS MODEL_CODE,
            NVL(A.CONSIGNEE , ' ') AS SHIPPING_CODE_NEW,
            NVL(B.NAME , ' ') AS SHIPPING_NAME_NEW,
            A.STATUS AS STATUS_NEW,
            NVL(A.DOC_TRANSFERID , ' ') AS DOC_TRANSFERID ,
            NVL(D.VESSEL_CODE , ' ') AS VESSEL_CODE_NEW,
            (E.VESSEL_NAME || ' ' || E.VOYAGE) AS VESSEL_NAME_NEW
            FROM CAR_LIST_CAR A
            LEFT JOIN M_ORGANIZATION B ON TO_CHAR(B.ID) = A.CONSIGNEE
            LEFT JOIN TR_ASSOSIATION C ON C.DOC_TRANSFERID = A.DOC_TRANSFERID
            LEFT JOIN M_TRUCK D ON D.TID = C.TRX
            LEFT JOIN VES_VOYAGE E ON E.ID_VVD = D.VESSEL_CODE
            WHERE A.STATUS = 'ON TERMINAL'
            -- AND A.DOC_TRANSFERID NOT IN (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE DOC_TRANSFERID IN (SELECT DOC_TRANSFERID FROM CAR_LIST_CAR))
            AND A.EI = 'L'
            AND A.CREATE_BY = '$idUser' 
            ORDER BY A.DATE_INPUT DESC");
        }

        if ($query->num_rows() > 0){
            return $query->result();
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
                    'message' => "Maaf Truck Sudah Dipakai dan Aktivas Belum Selesai",
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

    function insertRCTruckData($truckData){
        $userId = $this->userauth->getLoginData()->id_user;
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $tidQuery = $db_car->query("SELECT 'TRX' || '".$truckData['truckCode']."' || TO_CHAR(SYSDATE, 'DDMMYY') || UPPER(DBMS_RANDOM.STRING('L',5)) AS TID FROM DUAL");

        if ($tidQuery->num_rows() > 0)
        {
            $tidRow = $tidQuery->row();
            $TID = $tidRow->TID;

                $lastIdMTruck = $db_car->query("SELECT ID_TRUCK from M_TRUCK ORDER BY ID_TRUCK DESC FETCH FIRST 1 ROWS ONLY")->row()->ID_TRUCK;

                $lastIdMTruck = $lastIdMTruck + 1;

                $insertQuery_M_TRUCK = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, DRIVER_NAME, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, TID, ETICKET_TYPE, CREATED_BY, ID_TRUCK, INSERT_VIA, ID_TERMINAL, KTP_PATH, SURAT_JALAN_PATH) VALUES ('".$truckData['truckCode']."', '".$truckData['truckType']."', '".$truckData['truckCompanyCode']."', '".$truckData['driverName']."', '".$truckData['driverPhone']."', NULL, SYSDATE, '$TID', '".$truckData['eticketType']."', '$userId',  $lastIdMTruck, 'INTAPPS', 400, '".$truckData['ktp_path']."', '".$truckData['srj_path']."')";

                $insertQuery_TR_ASSOCIATION = "INSERT INTO TR_ASSOSIATION (TRUCK_CODE, DOC_TRANSFERID, CREATE_DATE, TRX, CREATED_BY) VALUES ('".$truckData['truckCode']."', '".$truckData['docTfId']."', SYSDATE, '$TID', '$userId')";

                $updateQuery = "UPDATE CAR_LIST_CAR
                SET DOC_TRANSFERID = '".$truckData['docTfId']."',
                EI = 'BM'
                WHERE VIN IN (".sprintf("'%s'", implode("','" ,$truckData['listedVin'])).")";


                $db_car->trans_begin();
                $db_car->query($insertQuery_TR_ASSOCIATION);
                $db_car->query($insertQuery_M_TRUCK);
                $db_car->query($updateQuery);     
                
                if ($db_car->trans_status() === FALSE) {
                    $db_car->trans_rollback();
                    return array(
                        'message' => "Gagal Insert Return Cargo",
                        'isError' => true,
                    );
                } else {
                    $db_car->trans_commit();
                    return array(
                        'message' => "Suskes Insert Return Cargo",
                        'isError' => false,
                    );
                }
        } 
    }

    function insertRCSelfdriveData($truckData){
        $userId = $this->userauth->getLoginData()->id_user;
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);

        $queryTrx = "SELECT 'TRXSLFRV'|| SUBSTR((CAST(NILAI_BARU AS VARCHAR(10))),2,3) ||
         TO_CHAR(SYSDATE, 'DDMMYY') || UPPER(DBMS_RANDOM.STRING('L',5))
         AS TID
         FROM (
          SELECT
           CASE
            WHEN HITUNG = 0 THEN 1001
            ELSE
             ((
              SELECT 
               CAST(SUBSTR(TID, 9, 3) AS INTEGER)
              FROM M_TRUCK
              WHERE SUBSTR(TID, 1, 8) = 'TRXSLFRV'
              AND TO_CHAR(DATE_INSERTED, 'YYYY-MM-DD') = TO_CHAR(SYSDATE, 'YYYY-MM-DD')
              ORDER BY SUBSTR(TID, 1, 11) DESC
              fetch first 1 rows only) + 1001)
            END AS NILAI_BARU 
            FROM (
             SELECT 
              COUNT(TID) AS HITUNG 
              FROM M_TRUCK
              WHERE SUBSTR(TID, 1, 8) = 'TRXSLFRV'
              AND TO_CHAR(DATE_INSERTED, 'YYYY-MM-DD') = TO_CHAR(SYSDATE, 'YYYY-MM-DD')
              ORDER BY SUBSTR(TID, 1, 11) DESC
            ) A
         ) A2";
        $TID = $db_car->query($queryTrx)->row()->TID;

        $lastIdMTruck = $db_car->query("SELECT ID_TRUCK from M_TRUCK ORDER BY ID_TRUCK DESC FETCH FIRST 1 ROWS ONLY")->row()->ID_TRUCK;

        $lastIdMTruck = $lastIdMTruck + 1;
        
        $insertQuery_M_TRUCK = "INSERT INTO M_TRUCK (NO_POL, STATUS_GATE, DRIVER_NAME, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, INSERT_VIA, TID, ID_TERMINAL, ETICKET_TYPE, CREATED_BY, ID_TRUCK, KTP_PATH, SURAT_JALAN_PATH) VALUES ('".$truckData['truckCode']."', 'IN', '".$truckData['driverName']."', '".$truckData['driverPhone']."', SYSDATE, SYSDATE, 'INTAPPS', '$TID', 400, '".$truckData['eticketType']."', '$userId', $lastIdMTruck, '".$truckData['ktp_path']."', '".$truckData['srj_path']."')";

        $insertQuery_TR_ASSOCIATION = "INSERT INTO TR_ASSOSIATION (TRUCK_CODE, DOC_TRANSFERID, CREATE_DATE, TRX, CREATED_BY) VALUES ('".$truckData['truckCode']."', '".$truckData['docTfId']."', SYSDATE, '$TID', '$userId')";

        $updateQuery = "UPDATE CAR_LIST_CAR
        SET DOC_TRANSFERID = '".$truckData['docTfId']."',
        EI = 'BM'
        WHERE VIN IN (".sprintf("'%s'", implode("','" ,$truckData['listedVin'])).")";

        $db_car->trans_begin();
        $db_car->query($insertQuery_TR_ASSOCIATION);
        $db_car->query($insertQuery_M_TRUCK);
        $db_car->query($updateQuery);     
                
        if ($db_car->trans_status() === FALSE) {
            $db_car->trans_rollback();
            return array(
                'message' => "Gagal Insert Return Cargo",
                'isError' => true,
            );
        } else {
            $db_car->trans_commit();
            return array(
                'message' => "Suskes Insert Return Cargo",
                'isError' => false,
            );
        }
    }

    public function get_rc_print($truck_visit_id){

        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);   
        $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');
       
        $queryPrint = "SELECT 
        C.TID AS TRUCK_VISIT_ID_NEW,
        C.ETICKET_TYPE AS DIRECTION_TYPE,
        CASE 
            WHEN ETICKET_TYPE = 'D' THEN 'INBOUND'
            WHEN ETICKET_TYPE = 'L' THEN 'OUTBOUND'
            WHEN ETICKET_TYPE = 'R' THEN 'RETURN CARGO'
            -- ELSE 'NOT FROM INTAPPS'
        END AS DIRECTION,
        A.VIN, 
        A.POD AS DESTINATION_CODE,
        E.PORT_NAME AS DESTINATION_NAME_NEW,
        C.VESSEL_CODE,
        (D.VESSEL_NAME || ' ' || D.VOYAGE) AS VESSEL_NAME_NEW,
        C.TRUCK_COMPANY_CODE AS TRUCKING_COMPANY_STID,
        C.TRUCK_TYPE AS TRUCK_TYPE_STID,
        NVL(C.DRIVER_NAME, '-') AS TRUCK_DRIVER,
        --C.STID_NUMBER,
        C.NO_POL AS LICENSE_PLATE
    FROM CAR_LIST_CAR A
    LEFT JOIN TR_ASSOSIATION B ON B.DOC_TRANSFERID = A.DOC_TRANSFERID
    LEFT JOIN M_TRUCK C ON C.TID = B.TRX
    LEFT JOIN VES_VOYAGE D ON D.ID_VVD = C.VESSEL_CODE
    LEFT JOIN M_PORT E ON E.PORT_CODE = A.POD 
    WHERE A.DOC_TRANSFERID = (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE TRX = '$truck_visit_id')";
        
    
        $hasil = $db_car->query($queryPrint)->result_array();

        return $hasil;
       

    }
    public function get_stid_number($truckCode){
        $db_car = $this->load->database('ilcs_cartos', TRUE);
        
        $queryStid = "SELECT TID AS STID_NUMBER FROM STID_MST_TRUCK
        WHERE PLAT_NO = '$truckCode'";

        $dataStid = $db_car->query($queryStid)->result_array();

        return $dataStid;
      
    }
       
}