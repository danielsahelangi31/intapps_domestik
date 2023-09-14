<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Announce_Truck_Domestik extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('form_validation'));
        //        $this->load->library('excel');
        $this->load->helper(array('form'));
        $this->load->library('logger');
        // Dapatkan data login
        if (!$this->auth = $this->userauth->getLoginData()) {
            redirect(LOGIN_PAGE);
        }
    }

    public function index()
    {     
        $mod = model('domestik/etickets_domestik');
        // $mod = model('etickets');
        // $docTransferID = null;
        // $vinResponseInfo = null;
        // $makers = $mod->getMakers();

        if(isset($_REQUEST)){         

            if($this->userauth->getLoginData()->sender == 'IKT'){
                $this->form_validation->set_rules('typeIKT', 'Sender', 'required');
            }
            $this->load->library(array('form_validation'));
            // $this->form_validation->set_rules('truckCode', 'Truck Code', 'required');    
            // $this->form_validation->set_rules('vesselName', 'Vessel Name', 'required');

            if($this->input->post('length_vin') > 0){
                for ($i = 1 ; $i <=intval($this->input->post('length_vin')) ; $i++){
                    $this->form_validation->set_rules('VinNumber'.$i, 'VIN Number', 'required');
                    $this->form_validation->set_rules('direction'.$i, 'Direction', 'required');
                    $this->form_validation->set_rules('directionType'.$i, 'Direction Type', 'required');
                    $this->form_validation->set_rules('models'.$i, 'Models', 'required');
                    $this->form_validation->set_rules('destinate'.$i, 'Destination', 'required');
                    $this->form_validation->set_rules('controlling_org'.$i, 'Controlling Organization', 'required');
                    $this->form_validation->set_rules('consignee'.$i, 'Consignee', 'required');
                }
            }else{
                if (empty($_FILES['upload_vin_excel']['name']))
                {
                    $this->form_validation->set_rules('upload_vin_excel', 'Upload Document', 'required');
                }
            }

            if ($this->form_validation->run() == FALSE) {
                          
                $vin = [];

                if (empty($_FILES['upload_vin_excel']['name']))  
                {
                    if(intval($this->input->post('length_vin')) > 0){
                        for ($i = 1 ; $i <=intval($this->input->post('length_vin')) ; $i++){
                            $vin['vinDetail'][] = array(
                                'VinNumber' => $this->input->post('VinNumber'.$i),
                                'Direction' => $this->input->post('direction'.$i),
                                'Directiontype' => $this->input->post('directionType'.$i),
                                'Fuel' => $this->input->post('fuel'.$i),
                                'Model' => $this->input->post('models'.$i),
                                'Destination' => $this->input->post('destinate'.$i),
                                'Controlling_Organization' => $this->input->post('controlling_org'.$i),
                                'Consignee' => $this->input->post('consignee'.$i)
                            );
                        }
                    }else{
                        $vin['vinDetail'][] = array(
                            'VinNumber' => null,
                            'Direction' => null,
                            'Directiontype' => null,
                            'Fuel' => null,
                            'Model' => null,
                            'Destination' => null,
                            'Controlling_Organization' => null,
                            'Consignee' => null
                        );
                    }
                }elseif ($_FILES['upload_vin_excel']['name']) {                

                    include APPPATH.'third_party/PHPExcel/PHPExcel.php';
                    $csvreader = new PHPExcel_Reader_Excel2007();
                    // $objPHPExcel->setActiveSheetIndex(1);
                    $path = $_FILES["upload_vin_excel"]["tmp_name"];
                    $loadcsv = $csvreader->load($path);

                    $tmp_code = array();

                    $truckCode = post('truckCode');
                    $idDocument = post('DocumentTransferId');
                    $db_car = $this->load->database('integrasi_cardom_dev', TRUE);   
                    $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');          
                 
                    $activityCount = $db_car->query("SELECT COUNT(*) AS ACTIVITY_COUNT FROM M_TRUCK WHERE NO_POL = '".$truckCode."' AND TRUNC(DATE_INSERTED) = TRUNC(SYSDATE)")->row()->ACTIVITY_COUNT;
                    $activityCountToday = $db_car->query("SELECT COUNT(*) AS ACTIVITY_COUNT FROM M_TRUCK WHERE NO_POL = '".$truckCode."' AND OUT_TERMINAL_DATE IS NULL AND TRUNC(DATE_INSERTED) = TRUNC(SYSDATE)")->row()->ACTIVITY_COUNT;
                        
                    if($activityCount >= 1 || $activityCountToday >= 1){
                            echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                            echo "\n"; echo "\n"; echo "\n";  
                            echo '<span style="font-size:18px";><b>Gagal mengunggah file, Truck :'.$truckCode.' Sudah Dipakai dan Aktivitas Belum Selesai</b></span>';                                                  
                            echo "</pre>\n"; 
                    }else {

                    $eticketType = post('checkboxInbound'); 
                    
         
                    foreach($loadcsv->getWorksheetIterator() as $worksheet)
                    {
                        $highestRow = $worksheet->getHighestRow();
                        $highestColumn = $worksheet->getHighestColumn();

                        for($row=2; $row<=$highestRow; $row++)
                        {                      
                            $vinNumber = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                            // $direction = $worksheet->getCellByColumnAndRow(1, $row)->getValue();                   
                            $fuel = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                            $modelCode = $worksheet->getCellByColumnAndRow(2, $row)->getValue();  
                            $finalLocation = $worksheet->getCellByColumnAndRow(3, $row)->getValue();            
                            $consigneeCode = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                            // $shippingInbound = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                            // $shippingOutbound = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

                            $vin['vinDetail'][] = array(
                                'VinNumber' => $vinNumber,
                                'Direction' => $direction,                       
                                'Fuel' => $fuel,
                                'Model' => $modelCode, 
                                'FinalLocation' => $finalLocation,                      
                                'Consignee' => $consigneeCode,
                                'Shipping_Agent_Inbound' => $shippingInbound,
                                'Shipping_Agent_Outbound' => $shippingOutbound,
                            );   
                            
                            $idShipping = $this->userauth->getLoginData()->intapps_type;
                            if ($this->userauth->getLoginData()->intapps_type !== 'ADMIN'){
                                $queryConsignee = "SELECT ID,NAME FROM M_ORGANIZATION WHERE TYPE = 'SHIPPING_LINE' AND ID = '$idShipping'";                       
                                $dataConsignee = $db_car->query($queryConsignee)->row(); 

                                if($eticketType == 'on'){
                                    $shippingInbound = $dataConsignee->NAME;
                                } else if($eticketType == ''){
                                    $shippingOutbound = $dataConsignee->NAME;
                                }  

                            } else if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){                                       
                                $queryConsignee = "SELECT ID,NAME FROM M_ORGANIZATION WHERE TYPE = 'SHIPPING_LINE' AND NAME = '".$consigneeCode."'";
                                $dataConsignee = $db_car->query($queryConsignee)->row(); 
                           
                                if($eticketType == 'on'){
                                    $shippingInbound = $dataConsignee->NAME;
                                } else if($eticketType == ''){
                                    $shippingOutbound = $dataConsignee->NAME;
                                }              
                                
                            }

                            $idDocument = post('DocumentTransferId');
                            $truckCode = post('truckCode');
                            $truckType = post('truckType');
                            $truckCompany = post('truckCompany');
                            $vesselName = post('vesselName');
                            $driverPhoneNumber = post('driverPhoneNumber');                      
                            $eticketType = post('checkboxInbound');                                            
                  
                            $vinNumber = trim($vinNumber);
                            $s = ucfirst($vinNumber);                   
                            $vinNumber = preg_replace('/\s+/', '', $s);

                            echo $vinNumber;echo 'vins';echo $eticketType;
                            if(preg_match('/[^A-Za-z0-9\_]/', $vinNumber)){
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Vin :'.$vinNumber.' hanya diperbolehkan huruf,angka dan garis_bawah</b></span>';                                              
                                echo "</pre>\n"; 

                                $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataCar = $db_car->query($deleteCar);
                                
                                $deleteGate = "DELETE FROM R_GATE_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataGate = $db_car->query($deleteGate);
                    
                                $deleteQuay = "DELETE FROM R_QUAY_CAR
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataQuay = $db_car->query($deleteQuay); 
                                
                                $deleteAss = "DELETE FROM TR_ASSOSIATION
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataAss = $db_car->query($deleteAss);

                                $deleteTruck = "DELETE FROM M_TRUCK
                                WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                $dataTruck = $db_car->query($deleteTruck);
                                break;
                         
                              } else {   
                            // $getData = $mod->uploadDataTruckOutbound($vinNumber,$direction,$fuel,$modelCode,$finalLocation,$consigneeCode,$shippingInbound,$shippingOutbound,$idDocument, $truckCode, $truckType,$truckCompany,$vesselName,$driverPhoneNumber,$data,$eticketType);
                                                          
                            $queryData = "SELECT ID_CATEGORY FROM M_CATEGORY WHERE NAME = '".$modelCode."'";
                            $dataModel = $db_car->query($queryData)->row();                            

                            $queryDuit = "SELECT NVL(LENGTH , 0) AS LENGTH, NVL(WIDTH, 0) AS WIDTH,
                            NVL(HEIGHT, 0) AS HEIGHT, NVL(WEIGHT, 0) AS WEIGHT
                            FROM M_CATEGORY 
                            WHERE ID_CATEGORY IN (SELECT ID_CATEGORY FROM M_CATEGORY WHERE NAME = '".$modelCode."')";
                            $hasil = $db_car->query($queryDuit)->row();
                            
                            $length = $hasil->LENGTH;
                            $width = $hasil->WIDTH;
                            $height = $hasil->HEIGHT;
                            $weight = $hasil->WEIGHT;
                            $directionType = 'DOMESTIC';

                            $queryFinal = "SELECT PORT_CODE FROM M_PORT WHERE PORT_NAME = '".$finalLocation."'";
                            $dataFinal = $db_car->query($queryFinal)->row();   
                         
                            if ($eticketType == 'on'){
                                $eticketType = 'D'; //Inbound
                                $direction = 'D';
                           } else if ($eticketType == ''){
                                $eticketType = 'L'; //Outbound
                                $direction = 'L';
                           } 

                      
                            $dataAss = $db_car->query("SELECT COUNT(*) AS COUNT_ASS FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = (SELECT DOC_TRANSFERID  from CAR_LIST_CAR WHERE VIN = '$vinNumber') AND STATUS = 1")->row()->COUNT_ASS;  
                            
                            if($vinNumber == '' && $fuel == '' && $modelCode == '' && $finalLocation == ''){                          
                                break;
                            }else if($dataAss > 0){
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, VIN : '.$vinNumber.' tidak bisa digunakan karena sudah diassosiasikan</b></span>';                                              
                                echo "</pre>\n";               
                                break; 
                            }else if ($truckCode == '' && $vesselName == '' && $truckType == '' && $truckCompany == ''){                               
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Field Mandatory harus diisi pada Vin :'.$vinNumber.'</b></span>';                                       
                                echo "</pre>\n";                                                             
                                break;                                                    
                            } else if($vinNumber == ''){  
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";                    
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Vin harus diisi</b></span>';                                           
                                echo "</pre>\n"; 
                                $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataCar = $db_car->query($deleteCar);
                                
                                $deleteGate = "DELETE FROM R_GATE_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataGate = $db_car->query($deleteGate);
                    
                                $deleteQuay = "DELETE FROM R_QUAY_CAR
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataQuay = $db_car->query($deleteQuay); 
                                
                                $deleteAss = "DELETE FROM TR_ASSOSIATION
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataAss = $db_car->query($deleteAss);

                                $deleteTruck = "DELETE FROM M_TRUCK
                                WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                $dataTruck = $db_car->query($deleteTruck);

                                break;
                               }else if($vinNumber == '' && $eticketType == 'on'){
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo 'sini ges2';
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Vin harus diisi</b></span>';                                         
                                echo "</pre>\n"; 
                                $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataCar = $db_car->query($deleteCar);
                                
                                $deleteGate = "DELETE FROM R_GATE_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataGate = $db_car->query($deleteGate);
                    
                                $deleteQuay = "DELETE FROM R_QUAY_CAR
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataQuay = $db_car->query($deleteQuay); 
                                
                                $deleteAss = "DELETE FROM TR_ASSOSIATION
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataAss = $db_car->query($deleteAss);

                                $deleteTruck = "DELETE FROM M_TRUCK
                                WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                $dataTruck = $db_car->query($deleteTruck);

                                break;
                               } else if($direction == '' && $eticketType == 'L'){   
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Direction harus diisi pada Vin :'.$vinNumber.'</b></span>';                                    
                                echo "</pre>\n"; 

                                $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataCar = $db_car->query($deleteCar);
                                
                                $deleteGate = "DELETE FROM R_GATE_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataGate = $db_car->query($deleteGate);
                    
                                $deleteQuay = "DELETE FROM R_QUAY_CAR
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataQuay = $db_car->query($deleteQuay); 
                                
                                $deleteAss = "DELETE FROM TR_ASSOSIATION
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataAss = $db_car->query($deleteAss);

                                $deleteTruck = "DELETE FROM M_TRUCK
                                WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                $dataTruck = $db_car->query($deleteTruck);

                                break;
                               } else if($modelCode == '' && $eticketType == 'L'){      
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Model harus diisi pada Vin :'.$vinNumber.'</b></span>';                                            
                                echo "</pre>\n";

                                $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataCar = $db_car->query($deleteCar);
                                
                                $deleteGate = "DELETE FROM R_GATE_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataGate = $db_car->query($deleteGate);
                    
                                $deleteQuay = "DELETE FROM R_QUAY_CAR
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataQuay = $db_car->query($deleteQuay); 
                                
                                $deleteAss = "DELETE FROM TR_ASSOSIATION
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataAss = $db_car->query($deleteAss);

                                $deleteTruck = "DELETE FROM M_TRUCK
                                WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                $dataTruck = $db_car->query($deleteTruck);

                                break;
                               } else if($finalLocation == '' && $eticketType == 'L'){     
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Final Location harus diisi pada Vin :'.$vinNumber.'</b></span>';                                               
                                echo "</pre>\n"; 

                                $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataCar = $db_car->query($deleteCar);
                                
                                $deleteGate = "DELETE FROM R_GATE_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataGate = $db_car->query($deleteGate);
                    
                                $deleteQuay = "DELETE FROM R_QUAY_CAR
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataQuay = $db_car->query($deleteQuay); 
                                
                                $deleteAss = "DELETE FROM TR_ASSOSIATION
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataAss = $db_car->query($deleteAss);

                                $deleteTruck = "DELETE FROM M_TRUCK
                                WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                $dataTruck = $db_car->query($deleteTruck);
                                break;
                               } else if($consigneeCode == '' && $eticketType == 'L' && $this->userauth->getLoginData()->intapps_type === 'ADMIN'){       
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Shipping line harus diisi pada Vin :'.$vinNumber.'</b></span>';                                               
                                echo "</pre>\n"; 

                                $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataCar = $db_car->query($deleteCar);
                                
                                $deleteGate = "DELETE FROM R_GATE_CAR 
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataGate = $db_car->query($deleteGate);
                    
                                $deleteQuay = "DELETE FROM R_QUAY_CAR
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataQuay = $db_car->query($deleteQuay); 
                                
                                $deleteAss = "DELETE FROM TR_ASSOSIATION
                                WHERE DOC_TRANSFERID = '".$idDocument."'";
                                $dataAss = $db_car->query($deleteAss);

                                $deleteTruck = "DELETE FROM M_TRUCK
                                WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                $dataTruck = $db_car->query($deleteTruck);
                                break; 
                               } else if($truckCode == ''){                                   
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Truck Code harus diisi</b></span>';                                           
                                echo "</pre>\n";                                                             
                                break;    
                               } else if($vesselName == ''){                                   
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Vessel Name harus diisi</b></span>';                                            
                                echo "</pre>\n";                                                             
                                break;                                                                                                             
                            } else if ($eticketType == '' || $eticketType == 'L' && ($modelCode != '' && $finalLocation != '')){
                            $query ="
                            BEGIN PRC_ADD_CAR_LIST_CAR(
                                 '".$vinNumber."',
                                 '".$direction."',  
                                 '".$directionType."',   
                                 '".$dataModel->ID_CATEGORY."', 
                                 '".$fuel."',          
                                 '".$dataFinal->PORT_CODE."',         
                                 '',
                                 '".$dataConsignee->ID."',
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
                    
                                $updateQuery = "UPDATE CAR_LIST_CAR
                                SET ID_VVD = '".$vesselName."' 
                                WHERE VIN ='".$vinNumber."'";
                                $updateVvid = $db_car->query($updateQuery);   
                            

                                ini_set("display_errors", "On");
                                // error_reporting(E_ALL);
                                // echo "<pre>\n";
                                // echo "\n"; echo "\n";                             
                                // // print_r($result['isError']);
                                // echo "</pre>\n";
                            
                        
                          //  }

                           if($out1 == '0' && $eticketType == 'D'){
                            //ERROR
                            break;
                           }
                            if($out1 == '1' && $eticketType == 'L'){
                                //ERROR
                                break;
                            }                     
                        } else if ($eticketType == 'D' || $eticketType == 'on'  && ($modelCode == '' && $finalLocation == '' && $consigneeCode == '')){
                                                                        
                                $queryCountVin = "SELECT COUNT(*) AS VIN_COUNT FROM CAR_LIST_CAR
                                WHERE VIN ='".$vinNumber."'";
                                $countVin = $db_car->query($queryCountVin)->row()->VIN_COUNT;                           
                            
                                if ($countVin > 0){    
                                   
                                    $orgId = $db_car->query("SELECT CONSIGNEE FROM CAR_LIST_CAR WHERE VIN ='".$vinNumber."'")->row()->CONSIGNEE;         
                             
                                    $updateQuery = "UPDATE CAR_LIST_CAR
                                    SET DOC_TRANSFERID = '".$idDocument."' 
                                    WHERE VIN ='".$vinNumber."'";
                                    $updateVin = $db_car->query($updateQuery);  
                
                                } 
                              } 
                            }                  
                          }              
   
                          $dataVin = "SELECT VIN,CONSIGNEE from CAR_LIST_CAR 
                          WHERE DOC_TRANSFERID ='".$idDocument."'";
                          $dataCar = $db_car->query($dataVin)->row();

                          $vinNumber = $dataCar->VIN;
                          $organizationId = $dataCar->CONSIGNEE;

                          $tidQuery = "SELECT 'TRX' || '$truckCode' || TO_CHAR(SYSDATE, 'DDMMYY') || UPPER(DBMS_RANDOM.STRING('L',5)) 
                          AS TID FROM DUAL";
                          $tidRow = $db_car->query($tidQuery)->row();
                          $TID = $tidRow->TID;   
                  
                            //INBOUND
                            if(($eticketType == 'on' || $eticketType == 'D')) { 
                                if($dataAss == 0){           
                                if(preg_match('/[^A-Za-z0-9\_]/', $vinNumber)){
                                    $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                    WHERE DOC_TRANSFERID = '".$idDocument."'";
                                    $dataCar = $db_car->query($deleteCar);
                                    
                                    $deleteGate = "DELETE FROM R_GATE_CAR 
                                    WHERE DOC_TRANSFERID = '".$idDocument."'";
                                    $dataGate = $db_car->query($deleteGate);
                        
                                    $deleteQuay = "DELETE FROM R_QUAY_CAR
                                    WHERE DOC_TRANSFERID = '".$idDocument."'";
                                    $dataQuay = $db_car->query($deleteQuay); 
                                    
                                    $deleteAss = "DELETE FROM TR_ASSOSIATION
                                    WHERE DOC_TRANSFERID = '".$idDocument."'";
                                    $dataAss = $db_car->query($deleteAss);

                                    $deleteTruck = "DELETE FROM M_TRUCK
                                    WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                    $dataTruck = $db_car->query($deleteTruck);
                                } else {
                                   
                                    $userId = $this->userauth->getLoginData()->id_user;  

                                    $lastIdMTruck = $db_car->query("SELECT ID_TRUCK from M_TRUCK ORDER BY ID_TRUCK DESC FETCH FIRST 1 ROWS ONLY")->row()->ID_TRUCK;         
                              
                                    $db_cartos = $this->load->database('ilcs_cartos', TRUE);   
                                    $queryTruck = "SELECT TYPE_TRUCK,COMPANY_NAME FROM STID_MST_TRUCK smt 
                                    WHERE PLAT_NO ='".$truckCode."'";
                                    $dataTruck = $db_cartos->query($queryTruck)->row();                   
                        
                                    $truckTypes = $dataTruck->TYPE_TRUCK;
                                    $truckCompanies = $dataTruck->COMPANY_NAME;    
                                    
                                    $tidQuery = "SELECT 'TRX' || '$truckCode' || TO_CHAR(SYSDATE, 'DDMMYY') || UPPER(DBMS_RANDOM.STRING('L',5)) 
                                    AS TID FROM DUAL";
                                    $tidRow = $db_car->query($tidQuery)->row();
                                    $TID = $tidRow->TID;   
                            
                                    $userId = $this->userauth->getLoginData()->id_user;  
                            
                                    $insertQuery_M_TRUCK = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, VESSEL_CODE, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, TID, ETICKET_TYPE, CREATED_BY, ID_TRUCK,INSERT_VIA,ID_TERMINAL,ORGANIZATION_ID_INTAPPS) 
                                    VALUES ('".$truckCode."', '".$truckTypes."', '".$truckCompanies."', 
                                    '".$vesselName."', '".$driverPhoneNumber."', '', SYSDATE, '$TID', 
                                    '".$eticketType."', '$userId',  $lastIdMTruck, 'INTAPPS', 400, $organizationId)";
                     
                                    $insertQuery_TR_ASSOCIATION = "INSERT INTO TR_ASSOSIATION (TRUCK_CODE, DOC_TRANSFERID, CREATE_DATE, TRX, CREATED_BY) 
                                    VALUES ('".$truckCode."', '".$idDocument."', SYSDATE, '$TID', '$userId')";

                                $countVin = "SELECT count(VIN) AS VIN from CAR_LIST_CAR 
                                WHERE DOC_TRANSFERID ='".$idDocument."'";
                                $dataVin = $db_car->query($countVin)->row()->VIN;

                                $db_car->trans_begin();
                                $db_car->query($insertQuery_TR_ASSOCIATION);
                                $db_car->query($insertQuery_M_TRUCK);  
                                
                                $db_car->trans_complete();
                
                                if($db_car->trans_status() && $dataVin > 0){
                                    echo '<span>"<pre style="background-color:green;text-align:center;color:white;>\n";</span>';
                                    echo "\n"; echo "\n"; echo "\n";     
                                    echo '<span style="font-size:18px";color:white;><b>Sukses menyimpan document transfer id : '.$idDocument.' dengan vin sebanyak '.$dataVin.'</b></span>';
                                    echo "</pre>\n"; echo "\n"; echo "\n";                                      
                                    $out->success = true;
                                    $out->msg = 'Berhasil insert data';
                                }else{
                                    echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                    echo "\n"; echo "\n"; echo "\n";  
                                    echo '<span style="font-size:18px";><b>Gagal insert data Outbound</b></span>';
                                    echo "\n"; echo "\n";                                 
                                    $out->success = false;
                                    $out->msg = 'Gagal input ke database, tidak ada data yang di update';
                                }
                               }
                              }
                            }
                                             
                           

                            if ($eticketType == 'on'){
                                $eticketType = 'D'; //Inbound
                           } else if ($eticketType == ''){
                                $eticketType = 'L'; //Outbound
                           }  
                      
                         

                        //OUTBOUND
                            if ($eticketType == '' || $eticketType == 'L') {                        
                                $queryCountTid = "SELECT COUNT(*) AS TID_COUNT from M_TRUCK 
                                WHERE TID='".$TID."'";
                                $countTid = $db_car->query($queryCountTid)->row();
                               
                               if ($countTid->TID_COUNT > 0){                              
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
                    
                               } else if ($truckCode !== '' && $vesselName !== '' ){
                                   if($dataAss == 0){                                                            
                                    if(preg_match('/[^A-Za-z0-9\_]/', $vinNumber)){
                                        $deleteCar = "DELETE FROM CAR_LIST_CAR 
                                        WHERE DOC_TRANSFERID = '".$idDocument."'";
                                        $dataCar = $db_car->query($deleteCar);
                                        
                                        $deleteGate = "DELETE FROM R_GATE_CAR 
                                        WHERE DOC_TRANSFERID = '".$idDocument."'";
                                        $dataGate = $db_car->query($deleteGate);
                            
                                        $deleteQuay = "DELETE FROM R_QUAY_CAR
                                        WHERE DOC_TRANSFERID = '".$idDocument."'";
                                        $dataQuay = $db_car->query($deleteQuay); 
                                        
                                        $deleteAss = "DELETE FROM TR_ASSOSIATION
                                        WHERE DOC_TRANSFERID = '".$idDocument."'";
                                        $dataAss = $db_car->query($deleteAss);
    
                                        $deleteTruck = "DELETE FROM M_TRUCK
                                        WHERE TID = (SELECT TRX FROM TR_ASSOSIATION WHERE DOC_TRANSFERID = '".$idDocument."')";
                                        $dataTruck = $db_car->query($deleteTruck);
                                    } else {
                                       
                                        if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){  
                                                    
                                         $dataVin = "SELECT CONSIGNEE from CAR_LIST_CAR 
                                         WHERE DOC_TRANSFERID ='".$idDocument."'";
                                         $dataConsignees = $db_car->query($dataVin)->row()->CONSIGNEE;                                  
             
                                         $tidQuery = "SELECT 'TRX' || '$truckCode' || TO_CHAR(SYSDATE, 'DDMMYY') || UPPER(DBMS_RANDOM.STRING('L',5)) 
                                         AS TID FROM DUAL";
                                         $tidRow = $db_car->query($tidQuery)->row();
                                         $TID = $tidRow->TID;   
                                 
                                         $userId = $this->userauth->getLoginData()->id_user;  
 
                                    $lastIdMTruck = $db_car->query("SELECT ID_TRUCK from M_TRUCK ORDER BY ID_TRUCK DESC FETCH FIRST 1 ROWS ONLY")->row()->ID_TRUCK;
                              
                                    $db_cartos = $this->load->database('ilcs_cartos', TRUE);   
 
                                    $queryTruck = "SELECT TYPE_TRUCK,UNIQUE_ID FROM STID_MST_TRUCK smt 
                                    WHERE PLAT_NO ='".$truckCode."'";
                                    $dataTruck = $db_cartos->query($queryTruck)->row();                   
                        
                                    $truckTypes = $dataTruck->TYPE_TRUCK;
                                    $truckCompanies = $dataTruck->UNIQUE_ID;                    
                          
                                    $insertQuery_M_TRUCK = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, VESSEL_CODE, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, TID, ETICKET_TYPE, CREATED_BY, ID_TRUCK,INSERT_VIA,ID_TERMINAL,ORGANIZATION_ID_INTAPPS) 
                                    VALUES ('".$truckCode."', '".$truckTypes."', '".$truckCompanies."', 
                                    '".$vesselName."', '".$driverPhoneNumber."', '', SYSDATE, '$TID', 
                                    '".$eticketType."', '$userId',  $lastIdMTruck, 'INTAPPS', 400, $dataConsignees)";
                     
                                    $insertQuery_TR_ASSOCIATION = "INSERT INTO TR_ASSOSIATION (TRUCK_CODE, DOC_TRANSFERID, CREATE_DATE, TRX, CREATED_BY) 
                                    VALUES ('".$truckCode."', '".$idDocument."', SYSDATE, '$TID', '$userId')";
                                                 
                                    $countVin = "SELECT count(VIN) AS VIN from CAR_LIST_CAR 
                                     WHERE DOC_TRANSFERID ='".$idDocument."'";
                                     $dataVin = $db_car->query($countVin)->row()->VIN;
 
                                     $db_car->trans_begin();
                                     $db_car->query($insertQuery_TR_ASSOCIATION);
                                     $db_car->query($insertQuery_M_TRUCK);  
                                     
                                     $db_car->trans_complete();
                     
                                     if($db_car->trans_status() && $dataVin > 0){
                                        echo '<span>"<pre style="background-color:green;text-align:center;color:white;>\n";</span>';
                                        echo "\n"; echo "\n"; echo "\n";     
                                        echo '<span style="font-size:18px";color:white;><b>Sukses menyimpan document transfer id : '.$idDocument.' dengan vin sebanyak '.$dataVin.'</b></span>';
                                        echo "</pre>\n"; echo "\n"; echo "\n";                                                                    
                                        $out->success = true;
                                        $out->msg = 'Berhasil insert data';
                                    }else{
                                        echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                        echo "\n"; echo "\n"; echo "\n";  
                                        echo '<span style="font-size:18px";><b>Gagal insert data Outbound</b></span>';
                                        echo "</pre>\n";echo "\n"; echo "\n";     
                                        $out->success = false;
                                        $out->msg = 'Gagal input ke database, tidak ada data yang di update';
                                    }   
                                } else {
                                    $dataVin = "SELECT VIN,CONSIGNEE from CAR_LIST_CAR 
                                    WHERE DOC_TRANSFERID ='".$idDocument."'";
                                    $dataCar = $db_car->query($dataVin)->row();
        
                                    $vinNumber = $dataCar->VIN;

                                    $tidQuery = "SELECT 'TRX' || '$truckCode' || TO_CHAR(SYSDATE, 'DDMMYY') || UPPER(DBMS_RANDOM.STRING('L',5)) 
                                    AS TID FROM DUAL";
                                    $tidRow = $db_car->query($tidQuery)->row();
                                    $TID = $tidRow->TID;   
                            
                                    $userId = $this->userauth->getLoginData()->id_user;                

                                   $lastIdMTruck = $db_car->query("SELECT ID_TRUCK from M_TRUCK ORDER BY ID_TRUCK DESC FETCH FIRST 1 ROWS ONLY")->row()->ID_TRUCK;
                             
                                   $db_cartos = $this->load->database('ilcs_cartos', TRUE);   

                                   $queryTruck = "SELECT TYPE_TRUCK,UNIQUE_ID FROM STID_MST_TRUCK smt 
                                   WHERE PLAT_NO ='".$truckCode."'";
                                   $dataTruck = $db_cartos->query($queryTruck)->row();                   
                       
                                   $truckTypes = $dataTruck->TYPE_TRUCK;
                                   $truckCompanies = $dataTruck->UNIQUE_ID;                    
                       
                                   $insertQuery_M_TRUCK = "INSERT INTO M_TRUCK (NO_POL, TRUCK_TYPE, TRUCK_COMPANY_CODE, VESSEL_CODE, DRIVER_PHONE, IN_TERMINAL_DATE, LAST_ACTIVITY, TID, ETICKET_TYPE, CREATED_BY, ID_TRUCK,INSERT_VIA,ID_TERMINAL,ORGANIZATION_ID_INTAPPS) 
                                   VALUES ('".$truckCode."', '".$truckTypes."', '".$truckCompanies."', 
                                   '".$vesselName."', '".$driverPhoneNumber."', '', SYSDATE, '$TID', 
                                   '".$eticketType."', '$userId',  $lastIdMTruck, 'INTAPPS', 400, $dataConsignee->ID)";
                    
                                   $insertQuery_TR_ASSOCIATION = "INSERT INTO TR_ASSOSIATION (TRUCK_CODE, DOC_TRANSFERID, CREATE_DATE, TRX, CREATED_BY) 
                                   VALUES ('".$truckCode."', '".$idDocument."', SYSDATE, '$TID', '$userId')";
                                                
                                   $countVin = "SELECT count(VIN) AS VIN from CAR_LIST_CAR 
                                    WHERE DOC_TRANSFERID ='".$idDocument."'";
                                    $dataVin = $db_car->query($countVin)->row()->VIN;

                                    $db_car->trans_begin();
                                    $db_car->query($insertQuery_TR_ASSOCIATION);
                                    $db_car->query($insertQuery_M_TRUCK);  
                                    
                                    $db_car->trans_complete();
                    
                                    if($db_car->trans_status() && $dataVin > 0){
                                        echo '<span>"<pre style="background-color:green;text-align:center;color:white;>\n";</span>';
                                        echo "\n"; echo "\n"; echo "\n";     
                                        echo '<span style="font-size:18px";color:white;><b>Sukses menyimpan document transfer id : '.$idDocument.' dengan vin sebanyak '.$dataVin.'</b></span>';
                                        echo "</pre>\n"; echo "\n"; echo "\n";                                  
                                        $out->success = true;
                                        $out->msg = 'Berhasil insert data';
                                   }else{
                                        echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                        echo "\n"; echo "\n"; echo "\n";  
                                        echo '<span style="font-size:18px";><b>Gagal insert data Outbound</b></span>';                            
                                        echo "Gagal, insert data Outbound";
                                        echo "</pre>\n";   
                                       $out->success = false;
                                       $out->msg = 'Gagal input ke database, tidak ada data yang di update';
                                   }   
                                }  
                               }               
                               }                          
                             }
                            }                              
                  
                       // }                         
                            // $  
                            
                        }
                }
                }


                // $payload = array(
                //     'ADCMessageHeader' => array(
                //         'DocumentTransferId' => $this->input->post('DocumentTransferId'),
                //         'MessageType' => 'ANNOUNCE_VIN',
                //         'Sender' => $this->userauth->getLoginData()->sender == 'IKT' ? $this->input->post('typeIKT') : $this->userauth->getLoginData()->sender,
                //         'Receiver' => 'CARTOS',
                //         'SentTime' => date("Ymdhis")
                //     ),
                //     'ADCMessageBody' => array(
                //         'AnnounceVinReqest' => array(
                //             'VinInfo' => $vin
                //         )
                //     )
                // );
                // $getData = $mod->OpAnnounceVin($payload);

                // $mod = model('domestik/etickets_domestik');
                // $getData = $mod->coba_ora();
                
                // $idDocument = post('DocumentTransferId');
                // echo $idDocument;

                // $getData = $mod->uploadData($vinNumber,$direction,$fuel,$modelCode,$finalLocation,$consigneeCode,$shippingInbound,$shippingOutbound,$idDocument);

                // // $getData = $mod->coba_oras();
      
                // $vinResponseInfo = $getData? $getData->response->ADCAcknowledgeBody->AnnounceVinResponse->vinResponseInfo : null;

                // $this->logger
                //     ->user($this->userauth->getLoginData()->username)
                //     ->function_name($this->router->fetch_method())
                //     ->comment('Announce VIN')
                //     ->new_value(json_encode($getData))
                //     ->log();

            }

        }
        $docTransferID = $idDocument;;
        $data = array(
            'docTransferID' => $docTransferID,
            // 'vinResponseInfo' => $vinResponseInfo       
        );

        // $mod = model('domestik/etickets_domestik');
        $port = $mod->getPort();
        $cat = $mod->getCategory();
        $ship = $mod->getShippingLine();

        $data = array(
            'ship' => $ship,
            'cat' => $cat,
            'port' => $port
        );
        // $mod = model('etickets');
        // $data['makers']         = $mod->getMakers();
        // $data['dokumen']        = $mod->getKdDok();
        // $data['dokumen_import'] = $mod->get_dok('EXPORT');
        // $data['dokumen_export'] = $mod->get_dok('IMPORT');
        // $data['list_no_doc']    = $mod->selectDoc();
        $this->load->view('domestik/backend/pages/eticket/announce/create_announce_truck_domestik', $data);
        // $this->load->view('domestik/backend/pages/eticket/announce/create_announce_truck_domestik', $data);
    }

    // private function isEmptyRow($row)
    // {
    //     foreach ($row as $cell) {
    //         if (null !== $cell) return false;
    //     }
    //     return true;
    // }

    // public function saveData()
    // {
    //     $mod = model('etickets');
    //     $visitID = null;
    //     $vinResponseInfo = null;
    //     $blResponseInfo = null;
    //     $customs = null;
    //     // $customs = array(
    //     //     'message' => 'Truck info not found'
    //     // );
    //     $makers = $mod->getMakers();
    //     $dokumen = $mod->getKdDok();

    //     if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //         if ($this->userauth->getLoginData()->sender == 'IKT') {
    //             $doc = $mod->getDocID(explode('_', $this->input->post('typeIKT'))[1], explode('_', $this->input->post('typeIKT'))[0]);
    //         } else {
    //             $doc = $mod->getDocID($this->userauth->getLoginData()->sender);
    //         }

    //         if ($doc) {
    //             $this->load->library(array('form_validation'));
    //             if ($this->userauth->getLoginData()->sender == 'IKT') {
    //                 $this->form_validation->set_rules('typeIKT', 'Sender', 'required');
    //             }
    //             if ($this->input->post('length_vin') > 0) {
    //                 $this->form_validation->set_rules('truckCode', 'Truck Code', 'required');
    //                 $this->form_validation->set_rules('directionType', 'Direction Type', 'required');
    //                 for ($i = 1; $i <= intval($this->input->post('length_vin')); $i++) {
    //                     $makerNi = $mod->getMakersImpExp(explode('_', $this->input->post('typeIKT'))[0], explode('_', $this->input->post('typeIKT'))[1]);
    //                     $resMakerNi = json_encode($makerNi[0], JSON_FORCE_OBJECT);
    //                     $arr = json_decode($resMakerNi, true);
    //                     $resExp = $arr["EXPORT"];
    //                     if ($this->userauth->getLoginData()->sender == 'TAM' || $this->userauth->getLoginData()->sender == 'OTHER' || $this->userauth->getLoginData()->sender == 'SGMW' || $this->userauth->getLoginData()->sender == 'HONDAA') {
    //                         $this->form_validation->set_rules('noDok' . $i, 'Nomor Dokumen', 'required');
    //                         $this->form_validation->set_rules('tglNpe' . $i, 'Tanggal NPE', 'required');
    //                     } elseif ($this->userauth->getLoginData()->sender == 'IKT') {
    //                         if ($resExp == 0) {
    //                             $this->form_validation->set_rules('noDok' . $i, 'Nomor Dokumen', 'required');
    //                             $this->form_validation->set_rules('tglNpe' . $i, 'Tanggal NPE', 'required');
    //                         }
    //                     }
    //                     $this->form_validation->set_rules('VinNumber' . $i, 'VIN Number', 'required');
    //                     $this->form_validation->set_rules('models' . $i, 'Models', 'required');
    //                     $this->form_validation->set_rules('destinate' . $i, 'Destination', 'required');
    //                     $this->form_validation->set_rules('controlling_org' . $i, 'Controlling Organization', 'required');
    //                     $this->form_validation->set_rules('consignee' . $i, 'Consignee', 'required');
    //                     // $this->form_validation->set_rules('noDok'.$i, 'Nomor Dokumen', 'required');
    //                     // $this->form_validation->set_rules('npwp'.$i, 'NPWP', 'required');
    //                     // $this->form_validation->set_rules('tglNpe'.$i, 'Tanggal NPE', 'required');
    //                 }
    //             }
    //             if ($this->input->post('length_bl') > 0) {
    //                 for ($i = 1; $i <= intval($this->input->post('length_bl')); $i++) {
    //                     $makerNi = $mod->getMakersImpExp(explode('_', $this->input->post('typeIKT'))[0], explode('_', $this->input->post('typeIKT'))[1]);
    //                     $resMakerNi = json_encode($makerNi[0], JSON_FORCE_OBJECT);
    //                     $arr = json_decode($resMakerNi, true);
    //                     $resImp = $arr["IMPORT"];
    //                     if ($this->userauth->getLoginData()->sender == 'SGMW' || $this->userauth->getLoginData()->sender == 'EVLS' || $this->userauth->getLoginData()->sender == 'OTHER' || $this->userauth->getLoginData()->sender == 'HONDAA') {
    //                         $this->form_validation->set_rules('noDok' . $i, 'Nomor Dokumen', 'required');
    //                         $this->form_validation->set_rules('tglDok' . $i, 'Tanggal Dokumen', 'required');
    //                     } elseif ($this->userauth->getLoginData()->sender == 'IKT') {
    //                         if ($resImp == 0) {
    //                             $this->form_validation->set_rules('noDok' . $i, 'Nomor Dokumen', 'required');
    //                             $this->form_validation->set_rules('tglDok' . $i, 'Tanggal Dokumen', 'required');
    //                         }
    //                     }
    //                     $this->form_validation->set_rules('BLNumber' . $i, 'BL Number', 'required');
    //                     $this->form_validation->set_rules('kdDok' . $i, 'Kode Dokumen', 'required');
    //                     $this->form_validation->set_rules('npwp' . $i, 'NPWP', 'required');
    //                 }
    //             }

    //             if ($this->form_validation->run() != 0 && ($this->input->post('length_vin') > 0 || $this->input->post('length_bl') > 0)) {
    //                 $vin = [];
    //                 $truckRequest = [];

    //                 $truckInfo = $mod->getTruckInfo($this->input->post('truckCode'));

    //                 // kosong
    //                 if (intval($this->input->post('length_vin')) === 0 && intval($this->input->post('length_bl')) === 0) {
    //                     $customs = array(
    //                         'status' => 'Failed',
    //                         'message' => 'BL / VIN Required'
    //                     );
    //                     echo json_encode($customs);
    //                     die();
    //                 }
    //                 if ($truckInfo) {
    //                     if (intval($this->input->post('length_vin')) > 0) {
    //                         for ($i = 1; $i <= intval($this->input->post('length_vin')); $i++) {
    //                             $vin['vinDetail'][] = array(
    //                                 'VinNumber' => $this->input->post('VinNumber' . $i),
    //                                 'Direction' => 'EXPORT',
    //                                 'DirectionType' => $this->input->post('directionType'),
    //                                 'Fuel' => strtoupper($this->input->post('fuel' . $i)),
    //                                 'Model' => $this->input->post('models' . $i),
    //                                 'Destination' => $this->input->post('destinate' . $i),
    //                                 'Controlling_Organization' => $this->input->post('controlling_org' . $i),
    //                                 'Consignee' => $this->input->post('consignee' . $i),
    //                                 'NoDok' => $this->input->post('noDok' . $i),
    //                                 'KdDok' => $this->input->post('kdDok_export' . $i),
    //                                 'NPWP' => $this->input->post('npwp' . $i),
    //                                 'TglDok' => $this->input->post('tglNpe' . $i)
    //                             );
    //                             // $cekNoDok = $vin['vinDetail'][0]['NoDok'];
    //                             // $cekTglDok = $vin['vinDetail'][0]['TglDok'];
    //                             // $resNoDok = substr($cekNoDok,-4);
    //                             // $resTglDok = substr($cekTglDok,0,4);
    //                             // if($resNoDok != $resTglDok) {
    //                             //     $customs = array(
    //                             //         'message' => 'Tahun di No Npe dan Tanggal NPE Harus Sama'
    //                             //     );
    //                             //     $data = array(
    //                             //         'visitID' => $visitID,
    //                             //         'vinResponseInfo' => $vinResponseInfo,
    //                             //         'blResponseInfo' => $blResponseInfo,
    //                             //         'makers' => $makers,
    //                             //         'customs' => $customs,
    //                             //         'dokumen' => $dokumen
    //                             //     );

    //                             //     $this->load->view('backend/pages/eticket/announce/create_announce_truck',$data);
    //                             //     return false;
    //                             // }
    //                         }
    //                     } else {
    //                         $vin['vinDetail'][] = array(
    //                             'VinNumber' => null,
    //                             'Direction' => null,
    //                             'DirectionType' => null,
    //                             'Fuel' => null,
    //                             'Model' => null,
    //                             'Destination' => null,
    //                             'Controlling_Organization' => null,
    //                             'Consignee' => null,
    //                             'NoDok' => null,
    //                             'KdDok' => null,
    //                             'NPWP' => null,
    //                             'TglDok' => null
    //                         );
    //                     }


    //                     if (intval($this->input->post('length_bl')) > 0) {
    //                         for ($i = 1; $i <= intval($this->input->post('length_bl')); $i++) {
    //                             $bls['BLDetail'][] = array(
    //                                 'BLNumber' => $this->input->post('BLNumber' . $i),
    //                                 'NoDok' => $this->input->post('noDok' . $i),
    //                                 'TglDok' => $this->input->post('tglDok' . $i),
    //                                 'KdDok' => $this->input->post('kdDok' . $i),
    //                                 'NPWP' => $this->input->post('npwp' . $i)
    //                             );
    //                         }
    //                     } else {
    //                         $bls['BLDetail'][] = array(
    //                             'BLNumber' => null,
    //                             'NoDok' => null,
    //                             'TglDok' => null,
    //                             'KdDok' => null,
    //                             'NPWP' => null,
    //                         );
    //                     }

    //                     $truckRequest['announceTruckInfo'][] = array(
    //                         'truckInfo' => array(
    //                             'visitID' => $this->input->post('truck_visit_id') ? strtoupper($this->input->post('truck_visit_id')) : null,  //isi optional
    //                             'Truck_LicensePlate' => $truckInfo[0]->TRUCK_CODE,
    //                             'Truck_Drivername' => $truckInfo[0]->DRIVER_NAME ? $truckInfo[0]->DRIVER_NAME : null,
    //                             'Truck_Driverphonenumber' => $this->input->post('driverPhoneNumber'),
    //                             'Truck_Carrier' => $truckInfo[0]->CARRIER_CODE,
    //                         ),
    //                         'VinInfo' => $vin,
    //                         'BLInfo' => $bls
    //                     );

    //                     $payload = array(
    //                         'ADCMessageHeader' => array(
    //                             'DocumentTransferId' => $doc,
    //                             'MessageType' => 'ANNOUNCE_TRUCK',
    //                             'Sender' => $this->userauth->getLoginData()->sender == 'IKT' ? $this->input->post('typeIKT') : 'IKT_' . $this->userauth->getLoginData()->sender,
    //                             'Receiver' => 'CARTOS',
    //                             'SentTime' => date("Ymdhis")
    //                         ),
    //                         'ADCMessageBody' => array(
    //                             'AnnounceTruckReqest' => $truckRequest
    //                         )
    //                     );

    //                     $getData = $mod->ann_import_bl($payload);

    //                     $visitID = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->visitid : null;
    //                     $vinResponseInfo = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->vinResponseInfo : null;
    //                     $blResponseInfo = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->blResponseInfo : null;

    //                     $data = array(
    //                         'status' => 'Success',
    //                         'visitID' => $visitID,
    //                         'vinResponseInfo' => $vinResponseInfo,
    //                         'blResponseInfo' => $blResponseInfo,
    //                     );
    //                     echo json_encode($data);

    //                     $this->logger
    //                         ->user($this->userauth->getLoginData()->username)
    //                         ->function_name($this->router->fetch_method())
    //                         ->comment('Announce Truck ' . $visitID)
    //                         ->new_value(json_encode($getData))
    //                         ->log();
    //                 } else {
    //                     $customs = array(
    //                         'status'  => 'Failed',
    //                         'message' => 'Truck info not found'
    //                     );
    //                     echo json_encode($customs);
    //                 }
    //             } elseif ($_FILES['upload_vin_excel']['name']) {
    //                 $this->form_validation->set_rules('truckCode', 'Truck Code', 'required');
    //                 if ($this->form_validation->run() != FALSE) {
    //                     include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
    //                     $csvreader = new PHPExcel_Reader_Excel2007();
    //                     $path = $_FILES["upload_vin_excel"]["tmp_name"];
    //                     $loadcsv = $csvreader->load($path);
    //                     $tmp_code = array();
    //                     $dataArray;

    //                     foreach ($loadcsv->getWorksheetIterator() as $worksheet) {
    //                         $sheetName = $worksheet->getTitle();
    //                         $highestRow = $worksheet->getHighestRow();
    //                         $highestColumn = $worksheet->getHighestColumn();

    //                         if ($sheetName == 'Export') {
    //                             for ($row = 2; $row <= $highestRow; $row++) {
    //                                 $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
    //                                 if ($this->isEmptyRow(reset($rowData))) {
    //                                     continue;
    //                                 }
    //                                 $vin = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
    //                                 $controllingCode = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
    //                                 $consigneeCode = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

    //                                 $dataArray['vinNumber'][] = $vin;
    //                                 $dataArray['direction'][] = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    //                                 $dataArray['directionType'][] = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    //                                 $dataArray['fuel'][] = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    //                                 $dataArray['modelCode'][] = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    //                                 $dataArray['destinationCode'][] = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
    //                                 $dataArray['controllingCode'][] = $controllingCode;
    //                                 $dataArray['consigneeCode'][] = $consigneeCode;
    //                                 $dataArray['noNpe'][] = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
    //                                 $dataArray['tglNpe'][] = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
    //                                 $dataArray['npwp'][] = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
    //                                 $dataArray['kdDoc_export'][] = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
    //                             }
    //                         }
    //                         if ($sheetName == 'Import') {
    //                             for ($row = 2; $row <= $highestRow; $row++) {
    //                                 $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
    //                                 if ($this->isEmptyRow(reset($rowData))) {
    //                                     continue;
    //                                 }
    //                                 $blNumber = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
    //                                 $dataArray['blNumber'][] = $blNumber;
    //                                 $dataArray['noDoc'][]    = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
    //                                 $dataArray['tglDoc'][]   = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
    //                                 $dataArray['kdDoc'][]    = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
    //                                 $dataArray['npwpBl'][]   = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
    //                             }
    //                         }
    //                     }
    //                     $countBill = count($dataArray['blNumber']);
    //                     $countVin  = count($dataArray['vinNumber']);

    //                     if ($countVin >= 1) {
    //                         if (!$controllingCode || !$consigneeCode) {
    //                             $customs = [
    //                                 'status' => 'Failed',
    //                                 'message' => $vin . ' Controlling/Consignee Tidak Boleh Kosong'
    //                             ];
    //                             echo json_encode($customs);
    //                             die();
    //                         }
    //                         for ($i = 0; $i < $countVin; $i++) {
    //                             $vinNumber = $dataArray['vinNumber'][$i];
    //                             $direction = $dataArray['direction'][$i];
    //                             $directionType = $dataArray['directionType'][$i];
    //                             $fuel = $dataArray['fuel'][$i];
    //                             $modelCode = $dataArray['modelCode'][$i];
    //                             $destinationCode = $dataArray['destinationCode'][$i];
    //                             $controllingCodeX = $dataArray['controllingCode'][$i];
    //                             $consigneeCodeX = $dataArray['consigneeCode'][$i];
    //                             $noNpe = $dataArray['noNpe'][$i];
    //                             $tglNpe = $dataArray['tglNpe'][$i];
    //                             $npwp = $dataArray['npwp'][$i];
    //                             $kdDoc_export = $dataArray['kdDoc_export'][$i];

    //                             if (in_array($dataArray['truck_license_plate'], $tmp_code)) {
    //                                 $truckRequest['announceTruckInfo'][array_search($dataArray['truck_license_plate'], $tmp_code)]['VinInfo']['vinDetail'][] =  array(
    //                                     'VinNumber' => $vinNumber ? strtoupper($vinNumber) : $vinNumber,
    //                                     'Direction' => $direction ? strtoupper($direction) : $direction,
    //                                     'DirectionType' => $directionType ? strtoupper($directionType) : $directionType,
    //                                     'Fuel' => $fuel ? strtoupper($fuel) : $fuel,
    //                                     'Model' => $modelCode ? strtoupper($modelCode) : $modelCode,
    //                                     'Destination' => $destinationCode ? strtoupper($destinationCode) : $destinationCode,
    //                                     'Controlling_Organization' => $controllingCodeX ? strtoupper($controllingCodeX) : $controllingCodeX,
    //                                     'Consignee' => $consigneeCodeX ? strtoupper($consigneeCodeX) : $consigneeCodeX,
    //                                     'NoDok' => $noNpe ? strtoupper($noNpe) : $noNpe,
    //                                     'KdDok' => $kdDoc_export ? strtoupper($kdDoc_export) : $kdDoc_export,
    //                                     'TglDok' => $tglNpe ? strtoupper($tglNpe) : $tglNpe,
    //                                     'NPWP' => $npwp ? strtoupper($npwp) : $npwp
    //                                 );
    //                             } else {
    //                                 if ($countBill >= 1) {
    //                                     for ($i = 0; $i < $countBill; $i++) {
    //                                         $cekCargo = $this->countCargo($dataArray['blNumber'][$i]);
    //                                         if ($cekCargo == 0) {
    //                                             $customs = array(
    //                                                 'status'  => 'Warning'
    //                                             );
    //                                             echo json_encode($customs);
    //                                             die();
    //                                         } else {
    //                                             array_push($tmp_code, $dataArray['truckPlate']);
    //                                             $bls['BLDetail'][] = array(
    //                                                 'BLNumber' => $dataArray['blNumber'][$i],
    //                                                 'NoDok' => $dataArray['noDoc'][$i],
    //                                                 'TglDok' => $dataArray['tglDoc'][$i],
    //                                                 'KdDok' => $dataArray['kdDoc'][$i],
    //                                                 'NPWP' => $dataArray['npwpBl'][$i],
    //                                             );
    //                                         }
    //                                     }
    //                                 } else if ($countBill < 1) {
    //                                     array_push($tmp_code, $dataArray['truck_license_plate']);
    //                                     $bls['BLDetail'][] = array(
    //                                         'BLNumber' => null,
    //                                         'NoDok' => null,
    //                                         'TglDok' => null,
    //                                         'KdDok' => null,
    //                                         'NPWP' => null,
    //                                     );
    //                                 }
    //                                 $truckRequest['announceTruckInfo'][] = array(
    //                                     'truckInfo' => array(
    //                                         'visitID' => $this->input->post('truck_visit_id') ? strtoupper($this->input->post('truck_visit_id')) : null,  //isi optional
    //                                         'Truck_LicensePlate' => trim(strtoupper($this->input->post('truckCode'))),
    //                                         'Truck_Drivername' => $driverName ? $driverName : null,
    //                                         'Truck_Driverphonenumber' => $this->input->post('driverPhoneNumber') ? $this->input->post('driverPhoneNumber') : null,
    //                                         'Truck_Carrier' => $truckCarrierCode ? strtoupper($truckCarrierCode) : null
    //                                     ),
    //                                     'VinInfo' => array(
    //                                         'vinDetail' => array(
    //                                             array(
    //                                                 'VinNumber' => $vinNumber ? strtoupper($vinNumber) : $vinNumber,
    //                                                 'Direction' => $direction ? strtoupper($direction) : $direction,
    //                                                 'DirectionType' => $directionType ? strtoupper($directionType) : $directionType,
    //                                                 'Fuel' => $fuel ? strtoupper($fuel) : $fuel,
    //                                                 'Model' => $modelCode ? strtoupper($modelCode) : $modelCode,
    //                                                 'Destination' => $destinationCode ? strtoupper($destinationCode) : $destinationCode,
    //                                                 'Controlling_Organization' => $controllingCodeX ? strtoupper($controllingCodeX) : $controllingCodeX,
    //                                                 'Consignee' => $consigneeCodeX ? strtoupper($consigneeCodeX) : $consigneeCodeX,
    //                                                 'NoDok' => $noNpe ? strtoupper($noNpe) : $noNpe,
    //                                                 'KdDok' => $kdDoc_export ? strtoupper($kdDoc_export) : $kdDoc_export,
    //                                                 'TglDok' => $tglNpe ? strtoupper($tglNpe) : $tglNpe,
    //                                                 'NPWP' => $npwp ? strtoupper($npwp) : $npwp

    //                                             )
    //                                         )
    //                                     ),
    //                                     'BLInfo' => $bls
    //                                 );
    //                             }
    //                         }
    //                     } else if ($countVin < 1) {
    //                         if (!$blNumber) {
    //                             $customs = [
    //                                 'status' => 'Failed',
    //                                 'message' => 'Vin / BL Number Tidak Boleh Kosong'
    //                             ];
    //                             echo json_encode($customs);
    //                             die();
    //                         }
    //                         for ($i = 0; $i < $countBill; $i++) {
    //                             $cekCargo = $this->countCargo($dataArray['blNumber'][$i]);
    //                             if ($cekCargo == 0) {
    //                                 $customs = array(
    //                                     'status'  => 'Warning'
    //                                 );
    //                                 echo json_encode($customs);
    //                                 die();
    //                             } else {
    //                                 array_push($tmp_code, $dataArray['truckPlate']);
    //                                 $bls['BLDetail'][] = array(
    //                                     'BLNumber' => $dataArray['blNumber'][$i],
    //                                     'NoDok' => $dataArray['noDoc'][$i],
    //                                     'TglDok' => $dataArray['tglDoc'][$i],
    //                                     'KdDok' => $dataArray['kdDoc'][$i],
    //                                     'NPWP' => $dataArray['npwpBl'][$i],
    //                                 );
    //                             }
    //                         }
    //                         $truckRequest['announceTruckInfo'][] = array(
    //                             'truckInfo' => array(
    //                                 'visitID' => $this->input->post('truck_visit_id') ? strtoupper($this->input->post('truck_visit_id')) : null,  //isi optional
    //                                 'Truck_LicensePlate' => trim(strtoupper($this->input->post('truckCode'))),
    //                                 'Truck_Drivername' => $driverName ? $driverName : null,
    //                                 'Truck_Driverphonenumber' => $this->input->post('driverPhoneNumber') ? $this->input->post('driverPhoneNumber') : null,
    //                                 'Truck_Carrier' => $truckCarrierCode ? strtoupper($truckCarrierCode) : null
    //                             ),
    //                             'VinInfo' => array(
    //                                 'vinDetail' => array(
    //                                     array(
    //                                         'VinNumber' => null,
    //                                         'Direction' => null,
    //                                         'DirectionType' => null,
    //                                         'Fuel' => null,
    //                                         'Model' => null,
    //                                         'Destination' => null,
    //                                         'Controlling_Organization' => null,
    //                                         'Consignee' => null,
    //                                         'NoDok' => null,
    //                                         'KdDok' => null,
    //                                         'TglDok' => null,
    //                                         'NPWP' => null
    //                                     )
    //                                 )
    //                             ),
    //                             'BLInfo' => $bls
    //                         );
    //                     }

    //                     $payload = array(
    //                         'ADCMessageHeader' => array(
    //                             'DocumentTransferId' => $doc,
    //                             'MessageType' => 'ANNOUNCE_TRUCK',
    //                             'Sender' => $this->userauth->getLoginData()->sender == 'IKT' ? $this->input->post('typeIKT') : 'IKT_' . $this->userauth->getLoginData()->sender,
    //                             'Receiver' => 'CARTOS',
    //                             'SentTime' => date("Ymdhis")
    //                         ),
    //                         'ADCMessageBody' => array(
    //                             'AnnounceTruckReqest' => $truckRequest
    //                         )
    //                     );
    //                     $getData = $mod->ann_import_bl($payload);

    //                     $visitID = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->visitid : null;
    //                     $vinResponseInfo = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->vinResponseInfo : null;
    //                     $blResponseInfo = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->blResponseInfo : null;

    //                     $dataInfo = [
    //                         'status' => 'Success',
    //                         'visitID' => $visitID,
    //                         'vinResponseInfo' => $vinResponseInfo,
    //                         'blResponseInfo' => $blResponseInfo,
    //                     ];
    //                     echo json_encode($dataInfo);
    //                     // if( $visitID && $this->input->post('length_bl') > 0){
    //                     //     for ($n = 1 ; $n <=intval($this->input->post('length_bl')) ; $n++){
    //                     //         $models = model('return_cargo/rc_model');
    //                     //         $models->create_import(
    //                     //             $visitID,
    //                     //             $this->input->post('truckCode'),
    //                     //             $this->input->post('BLNumber'.$n),
    //                     //             null,
    //                     //             json_encode($vinResponseInfo));
    //                     //     }
    //                     // }

    //                     $this->logger
    //                         ->user($this->userauth->getLoginData()->username)
    //                         ->function_name($this->router->fetch_method())
    //                         ->comment('Excel Announce Truck ' . $visitID)
    //                         ->new_value(json_encode($getData))
    //                         ->log();
    //                 } else {
    //                     $customs = array(
    //                         'status'  => 'Failed',
    //                         'message' => 'Truck Code Tidak Boleh Kosong'
    //                     );
    //                     echo json_encode($customs);
    //                     die();
    //                 }
    //             } else {
    //                 $customs = array(
    //                     'status'  => 'Failed',
    //                     'message' => 'Data Tidak Boleh Kosong'
    //                 );
    //                 echo json_encode($customs);
    //                 die();
    //             }
    //         }
    //     }
    // }

    // public function getListBL()
    // {
    //     $mod = model('etickets');

    //     $searchTerm = $this->input->post('searchTerm');
    //     $response = $mod->get_list_bl($this->userauth->getLoginData()->sender, $searchTerm);
    //     echo json_encode($response);
    // }

    // private function countCargo($blNumber)
    // {
    //     $mod = model('etickets');
    //     $response = $mod->getInfoBL($blNumber);
    //     $dataRes = (int)$response[0]['REMAINING_CARGO'];
    //     return json_encode($dataRes);
    // }

    // public function getInfoBL()
    // {
    //     $mod = model('etickets');

    //     $searchTerm = $this->input->post('truckCode');
    //     $response = $mod->getInfoBL($searchTerm);

    //     echo json_encode($response);
    // }

    // public function getNpwp()
    // {
    //     $mod = model('etickets');

    //     $response = $mod->getNPWP($this->userauth->getLoginData()->sender);

    //     echo json_encode($response);
    // }

    // public function getExpImpMaker()
    // {
    //     $mod = model('etickets');

    //     $makerNi = $this->input->post('makerNi');
    //     $senderNi = $this->input->post('senderNi');
    //     $response = $mod->getMakersImpExp($makerNi, $senderNi);

    //     echo json_encode($response);
    // }

    // public function getVin()
    // {

    //     $mod = model('etickets');

    //     $searchTerm = $this->input->post('searchTerm');
    //     $response = $mod->get_vin($this->userauth->getLoginData()->sender, $searchTerm);

    //     echo json_encode($response);
    // }

    public function getVesselName()
    {
        $mod = model('domestik/etickets_domestik');

        $searchTerm = $this->input->post('searchTerm');
        
        $response = $mod->getTruckVesselName($searchTerm);
        
        echo json_encode($response);
    }

    public function getTruckCodeList()
    {
        $mod = model('domestik/etickets_domestik');

        $searchTerm = $this->input->post('searchTerm');
        
        $response = $mod->getTruckCodeList($searchTerm);
        
        echo json_encode($response);
    }

    public function getTruckCodeData()
    {
        $mod = model('domestik/etickets_domestik');

        $searchTerm = $this->input->post('searchTerm');
        
        $response = $mod->getTruckCodeData($searchTerm);
        
        echo json_encode($response);
    }

    public function getSearchVin() 
    {
        $selfmod = model('domestik/etickets_domestik');

        $searchTerm = $this->input->post('searchTerm');
        $isInbound = $this->input->post('isInbound');
        // echo $isInbound;
        // exit();
        $response = $selfmod->getSearchVin($searchTerm, $isInbound);

        echo json_encode($response);
    }

    public function getVinData() 
    {
        $selfmod = model('domestik/etickets_domestik');
        $searchTerm = $this->input->post('searchTerm');

        $response = $selfmod->getVinData($searchTerm);
        echo json_encode($response);
    }

    public function getVinOutboundData() 
    {
        $selfmod = model('domestik/etickets_domestik');
        $searchTerm = $this->input->post('searchTerm');

        $data = $selfmod->getVinOutboundData($searchTerm); 
        echo json_encode($data); 
    }

    public function getTruckOutboundModel()
    {
        $mod = model('domestik/etickets_domestik');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getModelOutbound($searchTerm);

        echo json_encode($response);
    }

    public function getTruckOutboundDestination()
    {

        $mod = model('domestik/etickets_domestik');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getDestinationOutbound($searchTerm);

        echo json_encode($response);
    }

    public function getTruckShippingLine()
    {

        $mod = model('domestik/etickets_domestik');
        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getTruckShippingLine($searchTerm);

        echo json_encode($response);
    }

    public function checkTruckActivity()
    {

        $mod = model('domestik/etickets_domestik');
        $truckCode = $this->input->post('truckCode');
        $response = $mod->checkTruckActivity($truckCode);

        echo json_encode($response);
    }

    public function insertTruckVINList(){
        $mod = model('domestik/etickets_domestik');
        $data = $this->input->post('dataInsert');
        $response = $mod->insertTruckVINList($data);
        echo json_encode($response);
    }

    public function insertTruckData(){
        $mod = model('domestik/etickets_domestik');
        $data = $this->input->post('truckData');
        $response = $mod->insertTruckData($data);
        echo json_encode($response);
    }

    // public function getModel()
    // {

    //     $mod = model('etickets');

    //     $searchTerm = $this->input->post('searchTerm');
    //     $response = $mod->get_model($this->userauth->getLoginData()->sender, $searchTerm);

    //     echo json_encode($response);
    // }

    // public function getModelByVin()
    // {

    //     $mod = model('etickets');

    //     $searchTerm = $this->input->post('searchTerm');
    //     $response = $mod->get_model_by_vin($searchTerm);

    //     echo json_encode($response);
    // }

    // public function getDestination()
    // {
    //     $mod = model('etickets');

    //     $searchTerm = $this->input->post('searchTerm');
    //     $response = $mod->getDestination($searchTerm);

    //     echo json_encode($response);
    // }

    // public function getControlling()
    // {
    //     $mod = model('etickets');

    //     $searchTerm = $this->input->post('searchTerm');
    //     $response = $mod->getControlling($searchTerm);

    //     echo json_encode($response);
    // }

    // public function getKdDok()
    // {
    //     $mod = model('etickets');

    //     // $searchTerm = $this->input->post('searchTerm');
    //     $response = $mod->getKdDok();
    //     echo json_encode($response);
    // }

    // public function getNoDocument() {
    //     $mod = model('etickets');
    //     $response = $mod->selectDoc();

    //     echo json_encode($response);
    // }

    // public function getJumlahCargo($doc, $sisa = null) {
    //     $mod = model('etickets');
    //     $no_doc = str_replace('B4tA5', '/', $doc);
        
    //     // $response = "";
    //     if($sisa != null) {
    //         $sisaValid = str_replace('B4tA5', '/', $sisa);
    //         $response = $mod->getDocument($no_doc, $sisaValid);
    //     } else {
    //         $response = $mod->getDocument($no_doc);
    //     }

    //     echo json_encode($response);
    // }
}
