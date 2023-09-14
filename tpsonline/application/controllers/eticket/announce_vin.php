<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announce_Vin extends CI_Controller {

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

    public function index(){

        $mod = model('etickets');
        $docTransferID = null;
        $vinResponseInfo = null;
        $makers = $mod->getMakers();

        if (post()){

            if($this->userauth->getLoginData()->sender == 'IKT'){
                $this->form_validation->set_rules('typeIKT', 'Sender', 'required');
            }
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('DocumentTransferId', 'Doc Transer ID', 'required');

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

            if ( $this->form_validation->run() != 0){
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
                }else{

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
                            $directionType = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                            $fuel = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                            $modelCode = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                            $destinationCode = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                            $controllingCode = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                            $consigneeCode = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

                            $vin['vinDetail'][] = array(
                                'VinNumber' => $vinNumber,
                                'Direction' => $direction,
                                'Directiontype' => $directionType,
                                'Fuel' => $fuel,
                                'Model' => $modelCode,
                                'Destination' => $destinationCode,
                                'Controlling_Organization' => $controllingCode,
                                'Consignee' => $consigneeCode
                            );

                        }
                    }

                }


                $payload = array(
                    'ADCMessageHeader' => array(
                        'DocumentTransferId' => $this->input->post('DocumentTransferId'),
                        'MessageType' => 'ANNOUNCE_VIN',
                        'Sender' => $this->userauth->getLoginData()->sender == 'IKT' ? $this->input->post('typeIKT') : $this->userauth->getLoginData()->sender,
                        'Receiver' => 'CARTOS',
                        'SentTime' => date("Ymdhis")
                    ),
                    'ADCMessageBody' => array(
                        'AnnounceVinReqest' => array(
                            'VinInfo' => $vin
                        )
                    )
                );
                $getData = $mod->OpAnnounceVin($payload);
                $docTransferID = $getData? $getData->response->ADCMessageHeader->DocumentTransferId : null;
                $vinResponseInfo = $getData? $getData->response->ADCAcknowledgeBody->AnnounceVinResponse->vinResponseInfo : null;

                $this->logger
                    ->user($this->userauth->getLoginData()->username)
                    ->function_name($this->router->fetch_method())
                    ->comment('Announce VIN')
                    ->new_value(json_encode($getData))
                    ->log();

            }

        }

        $data = array(
            'docTransferID' => $docTransferID,
            'vinResponseInfo' => $vinResponseInfo,
            'makers' => $makers,
        );

        $this->load->view('backend/pages/eticket/announce/create_announce_vin',$data);
    }

}

