<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announce_Vin_Domestik extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->helper(array('form'));
        $this->load->library('logger');
        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }

    }

    public function controller_baru(){
      echo "ini Controller Baru.";
      $mod = model('domestik/etickets_domestik');
      $mod->coba_ora();
    }

    public function insert_create_announce_vin(){
        $mod = model('domestik/etickets_domestik');
        $data = $this->input->post('data');
        $hasil = $mod->insert_create_announce_vin($data);
        echo json_encode($hasil);
    }

    public function index(){
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
            // $this->form_validation->set_rules('DocumentTransferId', 'Doc Transer ID', 'required');

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
                    $this->form_validation->set_rules('upload_vin_excel', 'Document', 'required');
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
                }else {                          
                    include APPPATH.'third_party/PHPExcel/PHPExcel.php';
                    $csvreader = new PHPExcel_Reader_Excel2007();
                    // $objPHPExcel->setActiveSheetIndex(1);
                    $path = $_FILES["upload_vin_excel"]["tmp_name"];
                    $loadcsv = $csvreader->load($path);

                    $tmp_code = array();
            
                    foreach($loadcsv->getWorksheetIterator() as $worksheet)
                    {
                   
                        $highestRow = $worksheet->getHighestRow();                      
                        $highestColumn = $worksheet->getHighestColumn();
                     
                        for($row=2; $row<=$highestRow; $row++)
                        {
                            $vinNumber = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                            $direction = $worksheet->getCellByColumnAndRow(1, $row)->getValue();                   
                            $fuel = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                            $modelCode = $worksheet->getCellByColumnAndRow(3, $row)->getValue();  
                            $finalLocation = $worksheet->getCellByColumnAndRow(4, $row)->getValue();            
                            $consigneeCode = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                            $shippingInbound = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                            $shippingOutbound = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

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
                
                            $idDocument = post('DocumentTransferId');
                   
                            // $getData = $mod->uploadDataVin($vinNumber,$direction,$fuel,$modelCode,$finalLocation,$consigneeCode,$shippingInbound,$shippingOutbound,$idDocument);
                            $db_car = $this->load->database('integrasi_cardom_dev', TRUE);   
                            $conn = oci_connect('CARDOM', 'cardom', '10.8.1.238:1522/friday01');

                            $idShipping = $this->userauth->getLoginData()->intapps_type;
                            if ($this->userauth->getLoginData()->intapps_type !== 'ADMIN'){
                                $queryConsignee = "SELECT ID,NAME FROM M_ORGANIZATION WHERE TYPE = 'SHIPPING_LINE' AND ID = '$idShipping'";                       
                                $dataConsignee = $db_car->query($queryConsignee)->row(); 

                                if($direction == 'INBOUND(DISCHARGE)'){
                                    $shippingInbound = $dataConsignee->NAME;
                                } else if($direction == 'OUTBOUND(LOADING)'){
                                    $shippingOutbound = $dataConsignee->NAME;
                                }  

                            } else if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){                                       
                                $queryConsignee = "SELECT ID,NAME FROM M_ORGANIZATION WHERE TYPE = 'SHIPPING_LINE' AND NAME = '".$consigneeCode."'";
                                $dataConsignee = $db_car->query($queryConsignee)->row(); 
                           
                                if($direction == 'INBOUND(DISCHARGE)'){
                                    $shippingInbound = $dataConsignee->NAME;
                                } else if($direction == 'OUTBOUND(LOADING)'){
                                    $shippingOutbound = $dataConsignee->NAME;
                                }             
                                
                            }

                            $vinNumber = trim($vinNumber);
                            $s = ucfirst($vinNumber);                   
                            $vinNumber = preg_replace('/\s+/', '', $s);
                            // $vinNumber = preg_replace('/[^a-zA-Z0-9.]/', '', $vinNumber);
                      
                            if(preg_match('/[^A-Za-z0-9\_]/', $vinNumber)){
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Vin :'.$vinNumber.' hanya diperbolehkan huruf,angka dan garis_bawah</b></span>';                                       
                                echo "</pre>\n"; 
                                break;
                         
                              } else {                           
                       
                                             
                            if($direction == 'INBOUND(DISCHARGE)'){
                                $direction = 'D';
                            } else if($direction == 'OUTBOUND(LOADING)'){
                                $direction = 'L';
                            }

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
                                              
                            if($vinNumber == '' && $direction == '' && $fuel == '' && $modelCode == '' &&$finalLocation == ''){  
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
                                break;
                               } else if($direction == ''){   
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Direction harus diisi pada Vin: '.$vinNumber,'</b></span>';                                          
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
                                break;
                               } else if($modelCode == ''){   
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Model harus diisi pada Vin: '.$vinNumber,'</b></span>';                                           
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
                                break;
                               } else if($finalLocation == ''){   
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, finalLocation harus diisi pada Vin: '.$vinNumber,'</b></span>';                                          
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

                                break;
                               } else if($consigneeCode == '' && $this->userauth->getLoginData()->intapps_type === 'ADMIN'){   
                                echo '<span>"<pre style="background-color:red;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";  
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, Shipping line harus diisi pada Vin: '.$vinNumber,'</b></span>';                                       
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

                                break;                                
                               } else {
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

                            // if($out1 == 1){
                            //     //ERROR
                            //     break;
                            // } 

                            if($out1 == 1){  
                                echo "<pre style='background-color:red;text-align:center;color:white';>\n";                            
                                echo "\n"; echo "\n"; echo "\n"; 
                                echo '<span style="font-size:18px";><b>Gagal mengunggah file, VIN : '.$vinNumber.' sudah ada sebelumnya</b></span>';
                                echo "</pre>\n"; 
                            }                 
                                    
                           // return $result;
                        }
           
                          
                   
                            // $
                            
                            }
                        }
                             $result = array(
                            'isError' => $out,
                            'message' => $out1);
              
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
                            
                            }
                            $countVin = "SELECT count(VIN) AS VIN from CAR_LIST_CAR 
                            WHERE DOC_TRANSFERID ='".$idDocument."'";
                            $dataVin = $db_car->query($countVin)->row()->VIN; 
                

                            ini_set("display_errors", "On");
                            // // error_reporting(E_ALL);
                            // echo "<pre>\n";
                            // echo "\n"; echo "\n";                                            
                        
                            $queryVins = "SELECT VIN FROM CAR_LIST_CAR WHERE VIN ='".$vinNumber."'";
                            $readyVins = $db_car->query($queryVins)->row()->VIN;                   
                

                            if($out1 == 0 && $dataVin > 0){  
                                echo '<span>"<pre style="background-color:green;text-align:center;color:white;>\n";</span>';
                                echo "\n"; echo "\n"; echo "\n";     
                                echo '<span style="font-size:18px";color:white;><b>Sukses menyimpan document transfer id : '.$idDocument.' dengan vin sebanyak '.$dataVin.'</b></span>';
                                echo "\n"; echo "\n";   
                                echo "</pre>\n";   
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

        $this->load->view('domestik/backend/pages/eticket/announce/create_announce_vin_domestik', $data);
    }

    public function getCategory()
    {

        $mod = model('domestik/etickets_domestik');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getSearchCategory($this->userauth->getLoginData()->sender, $searchTerm);

        echo json_encode($response);
    }

    public function getPort()
    {

        $mod = model('domestik/etickets_domestik');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getSearchPort($this->userauth->getLoginData()->sender, $searchTerm);

        echo json_encode($response);
    }

    public function vinModel()
    {
        $this->load->model('domestik/etickets_domestik');
        $data = $this->etickets_domestik->vinModel();
        echo json_encode($data);
    }

    public function getSearchShippingLine()
    {
        $this->load->model('domestik/etickets_domestik');
        $searchTerm = $this->input->post('searchTerm');
        $data = $this->etickets_domestik->getSearchShippingLine($searchTerm);
        echo json_encode($data);
    }

}
