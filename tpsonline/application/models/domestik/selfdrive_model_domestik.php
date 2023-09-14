<?php

class Selfdrive_Model_Domestik extends CI_Model 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function getVin() 
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE); 
        $query = "SELECT VIN_CODE FROM M_CATEGORY"; 
        $result = $db_car->query($query)->result(); 
        return $result; 
    }
    
    public function getSearchVin($sender, $searchTerm="") 
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $id_user = $this->userauth->getLoginData()->id_user;
        
        $query = "";
        if($this->input->post("dir") == "D")
        {
            $query = "SELECT VIN FROM CAR_LIST_CAR clc WHERE EI = 'D' AND VIN LIKE '%".strtoupper($searchTerm)."%' AND DOC_TRANSFERID NOT IN (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION ta WHERE TRX IN (SELECT TID FROM M_TRUCK))";
        } else if($this->input->post("dir") == "L") 
        {
            $query = "SELECT VIN FROM CAR_LIST_CAR clc WHERE EI = 'L' AND VIN LIKE '%".strtoupper($searchTerm)."%' AND DOC_TRANSFERID NOT IN (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION ta WHERE TRX IN (SELECT TID FROM M_TRUCK))";
        }
        
        $src = $db_car->query($query)->result();
        $data = array();
        
        foreach($src as $q) 
        {
            $data[] = array("id" => $q->VIN, "text" => $q->VIN);
        }
        return $data; 
    }

    public function getDataSelf() 
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE); 
        $value = $this->input->post('cari');
        $query = "SELECT  clc.VIN, mc.VIN_CODE, mc.NAME, 
        clc.FUEL_TYPE, 
        mp.PORT_CODE, (mp.PORT_CODE || '~' || mp.PORT_NAME) AS PORT_NAME,
        mo.ID AS ORG_ID, (mo.NAME || ' (' || mo.CODE || ')') AS SHIPPING_LINE
        FROM CAR_LIST_CAR clc
        JOIN M_CATEGORY mc
        ON clc.ID_CATEGORY_INTAPPS  = mc.ID_CATEGORY  
        JOIN M_PORT mp
        ON clc.POD = mp.PORT_CODE 
        JOIN M_ORGANIZATION mo ON TO_CHAR(mo.ID) = clc.CONSIGNEE 
        WHERE clc.VIN = '".strtoupper($value)."'"; 
        
        $result = $db_car->query($query)->result(); 
        return $result; 

        // $src = $db_car->select("M_CATEGORY.VIN_CODE, (M_CATEGORY.VIN_CODE || '~' || M_CATEGORY.NAME) AS NAME, CAR_LIST_CAR.FUEL_TYPE, M_PORT.PORT_CODE, (M_PORT.PORT_CODE || '~' || M_PORT.PORT_NAME) AS PORT_NAME, M_ORGANIZATION.ID AS ORG_ID, (M_ORGANIZATION.NAME || ' (' || M_ORGANIZATION.CODE || ')~' || M_ORGANIZATION.ID) AS SHIPPING_LINE", FALSE)
        // ->from('CAR_LIST_CAR')
        // ->join('M_CATEGORY', 'CAR_LIST_CAR.ID_CATEGORY = M_CATEGORY.ID_CATEGORY')
        // ->join('M_PORT', 'CAR_LIST_CAR.POD = M_PORT.PORT_CODE')
        // ->join('M_ORGANIZATION', 'CAR_LIST_CAR.CONSIGNEE = TO_CHAR(M.ORGANIZATION.ID)', )
        // ->where("CAR_LIST_CAR.VIN", $value)
        // ->get()
        // ->result_array();

        // $data = array();
        // foreach($src as $q) 
        // {
        //     $data[] = array("VIN_CODE" => $q['VIN_CODE'], "NAME" => $q['NAME'], "FUEL_TYPE" => $q['FUEL_TYPE'], "PORT_CODE" => $q['PORT_CODE'], "PORT_NAME" => $q['PORT_NAME'], "SHIPPING_LINE" => $q["SHIPPING_LINE"]);
        // }
        // return $data; 
    }

    public function getModelOutbound($searchTerm) 
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $src = $db_car->select("VIN_CODE, ID_CATEGORY, NAME", FALSE)
        ->from('M_CATEGORY')
        ->where("NAME LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)
        ->get()
        ->result_array();
        $data = array();
        foreach($src as $q) 
        {
            $data[] = array("id" => $q['ID_CATEGORY'], "text" => $q['NAME'], "value" => $q['ID_CATEGORY']);
        }
        return $data; 
    }

    //ADMIN ONLY
    public function getShippingLine($searchTerm) 
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $src = $db_car->select("ID, CODE, (NAME || ' (' || CODE || ')') AS NAME", FALSE)
        ->from('M_ORGANIZATION')
        ->where('TYPE', 'SHIPPING_LINE')
        ->where("(NAME || ' (' || CODE || ')') LIKE '%".strtoupper($searchTerm)."%'", NULL, FALSE)
        ->get()
        ->result_array();
        $data = array();
        foreach($src as $q) 
        {
            $data[] = array("id" => $q['ID'], "text" => $q['NAME'], "value" => $q['CODE']);
        }
        return $data; 
    }

    public function getVinOutbound()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
        $vin = $this->input->post('vin');
        $query = "SELECT VIN_CODE, ID_CATEGORY, NAME FROM M_CATEGORY WHERE VIN_CODE = '".strtoupper($vin)."'"; 
        $getVinModel = $db_car->query($query)->result();
        return $getVinModel;   
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

    public function insert_self_drive()
    {
        // print($this->input->post("vesselCode")); exit();
        // koneksi ke database 
        $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);

        // 2. memperoleh TID
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
        $resultTrx = $db_car->query($queryTrx)->row();
        
        $idUser = $this->userauth->getLoginData()->id_user;
        
        $msgVin = "";

        if($this->input->post('direction') == "L")
        {   
            $shippingLine = $db_car->query("SELECT NAME
            FROM M_ORGANIZATION 
            WHERE ID = '".$this->input->post('sl')."'")->row();
            // print_r($shippingLine); exit();
    
            $v_sao = $shippingLine->NAME; 
            
            $getCar = $db_car->query("SELECT NAME, NVL(LENGTH , 0) AS LENGTH, NVL(WIDTH, 0) AS WIDTH,
            NVL(HEIGHT, 0) AS HEIGHT, NVL(WEIGHT, 0) AS WEIGHT
            FROM M_CATEGORY 
            WHERE ID_CATEGORY = '".$this->input->post('model')."'")->row();
            // print_r($getCar); exit();
            $modelName = $getCar->NAME;
            $width = $getCar->WIDTH;
            // print_r($width); exit();
            $length = $getCar->LENGTH;
            $height = $getCar->HEIGHT;
            $weight = $getCar->WEIGHT;
            
            $query ="
            BEGIN PRC_ADD_CAR_LIST_CAR(
                '".$this->input->post('vin')."',
                '".$this->input->post('direction')."',
                '".$this->input->post('directionType')."',
                '".$this->input->post('model')."',
                '".$this->input->post('fuelOut')."',
                '".$this->input->post('portCode')."',
                '',
                '".$this->input->post('sl')."',
                '',
                '".$v_sao."',
                '',
                '',
                '".$this->input->post('docTransferId')."',
                '".$width."',
                '".$length."',
                '".$height."',
                '".$weight."',
                '".$this->input->post('vesselCode')."',
                '".$idUser."',
                :out,
                :out1
            );
            END;";
            // print_r($query); exit();
            
            $stmt = oci_parse($conn, $query );
            // print_r(oci_execute($stmt)); exit();
            oci_bind_by_name($stmt, ":out", $out,300);
            oci_bind_by_name($stmt, ":out1", $out1,300);
    
            $exec = oci_execute($stmt);
            
            $result = array(
            'isError' => $out1,
            'message' => $out);
    
            $msgVin = $result['message'];

            if($out1 == 1) {
                print_r($msgVin);
                exit(); 
            } else {

                // memperoleh type truck dari m truck stid 
                $queryTypeTruck = "SELECT TYPE_TRUCK FROM M_TRUCK_STID mts WHERE PLAT_NO = 'SELFDRIVE'";
                $TypeTruck = $db_car->query($queryTypeTruck)->row();

                // proses insert M_TRUCK
                $queryTruck = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, VESSEL_CODE, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, INSERT_VIA, TID, ID_TERMINAL, ORGANIZATION_ID_INTAPPS, DRIVER_NAME, ETICKET_TYPE, CREATE_DATE, CREATED_BY, ID_TRUCK) VALUES ('".$this->input->post('truckCode')."', '".$TypeTruck->TYPE_TRUCK."', '".$TypeTruck->TYPE_TRUCK."', '".$this->input->post('vesselCode')."', '".$this->input->post('driverPhoneNumber')."','', SYSDATE, 'INTAPPS','".$resultTrx->TID."', '400', '".$this->input->post('sl')."','".$this->input->post('driverName')."','".$this->input->post('direction')."', SYSDATE,'".$idUser."', '')";
                $parseTruck = oci_parse($conn, $queryTruck);
                $execTruck = oci_execute($parseTruck);

                
                // proses insert TR_ASSOSIATION
                $queryTR = "INSERT INTO TR_ASSOSIATION(TRUCK_CODE, DOC_TRANSFERID, TRX, STATUS, CREATE_DATE, CREATED_BY) VALUES ('".$this->input->post('truckCode')."', '".$this->input->post('docTransferId')."', '".$resultTrx->TID."', '1',  SYSDATE, '".$idUser."')";
                $parseTr = oci_parse($conn, $queryTR);
                $execTr = oci_execute($parseTr);
                
                if($execTruck == 1 && $execTr == 1) 
                {
                    print_r("Sukses menyimpan doc transfer id : ".$this->input->post('docTransferId')." dengan vin ".$this->input->post('vin'));
                    exit();
                } else {
                    print_r("DATA GAGAL DISIMPAN");
                    exit();
                }
            }
        } elseif($this->input->post('direction') == "D") 
        {
            // print($this->input->post('sl')); exit();
            $query = "UPDATE CAR_LIST_CAR SET DOC_TRANSFERID = '".$this->input->post('docTransferId')."' WHERE VIN = '".$this->input->post('vin')."'";
            $stmt = oci_parse($conn, $query);
            $exec = oci_execute($stmt);

            // memperoleh type truck dari m truck stid 
            $queryTypeTruck = "SELECT TYPE_TRUCK FROM M_TRUCK_STID mts WHERE PLAT_NO = 'SELFDRIVE'";
            $TypeTruck = $db_car->query($queryTypeTruck)->row();
            
            // proses insert M_TRUCK
            $queryTruck = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, VESSEL_CODE, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, INSERT_VIA, TID, ID_TERMINAL, ORGANIZATION_ID_INTAPPS, DRIVER_NAME, ETICKET_TYPE, CREATE_DATE, CREATED_BY, ID_TRUCK, STATUS_GATE) VALUES ('".$this->input->post('truckCode')."', '".$TypeTruck->TYPE_TRUCK."', '".$TypeTruck->TYPE_TRUCK."', '".$this->input->post('vesselCode')."', '".$this->input->post('driverPhoneNumber')."', SYSDATE, SYSDATE, 'INTAPPS','".$resultTrx->TID."', '400', '".$this->input->post('sl')."','".$this->input->post('driverName')."','".$this->input->post('direction')."', SYSDATE,'".$idUser."', '', 'IN')";
            $parseTruck = oci_parse($conn, $queryTruck);
            $execTruck = oci_execute($parseTruck);
            
            // proses insert TR_ASSOSIATION
            $queryTR = "INSERT INTO TR_ASSOSIATION(TRUCK_CODE, DOC_TRANSFERID, TRX, STATUS, CREATE_DATE, CREATED_BY) VALUES ('".$this->input->post('truckCode')."', '".$this->input->post('docTransferId')."', '".$resultTrx->TID."', '1',  SYSDATE, '".$idUser."')";
            $parseTr = oci_parse($conn, $queryTR);
            $execTr = oci_execute($parseTr);
            
            if($exec == 1 && $execTruck == 1 && $execTr == 1) 
            {
                print_r("Sukses menyimpan doc transfer id : ".$this->input->post('docTransferId')." dengan vin ".$this->input->post('vin'));
                exit();
            } else {
                print_r("DATA GAGAL DISIMPAN");
                exit();
            }
        }
    }
}
