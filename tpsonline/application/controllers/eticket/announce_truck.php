<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Announce_Truck extends CI_Controller
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
        $mod = model('etickets');
        $data['makers']         = $mod->getMakers();
        $data['dokumen']        = $mod->getKdDok();
        $data['dokumen_import'] = $mod->get_dok('EXPORT');
        $data['dokumen_export'] = $mod->get_dok('IMPORT');
        $data['list_no_doc']    = $mod->selectDoc();
        $this->load->view('backend/pages/eticket/announce/create_announce_truck', $data);
    }

    private function isEmptyRow($row)
    {
        foreach ($row as $cell) {
            if (null !== $cell) return false;
        }
        return true;
    }

    public function saveData()
    {
        $mod = model('etickets');
        $visitID = null;
        $vinResponseInfo = null;
        $blResponseInfo = null;
        $customs = null;
        // $customs = array(
        //     'message' => 'Truck info not found'
        // );
        $makers = $mod->getMakers();
        $dokumen = $mod->getKdDok();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->userauth->getLoginData()->sender == 'IKT') {
                $doc = $mod->getDocID(explode('_', $this->input->post('typeIKT'))[1], explode('_', $this->input->post('typeIKT'))[0]);
            } else {
                $doc = $mod->getDocID($this->userauth->getLoginData()->sender);
            }

            if ($doc) {
                $this->load->library(array('form_validation'));
                if ($this->userauth->getLoginData()->sender == 'IKT') {
                    $this->form_validation->set_rules('typeIKT', 'Sender', 'required');
                }
                if ($this->input->post('length_vin') > 0) {
                    $this->form_validation->set_rules('truckCode', 'Truck Code', 'required');
                    $this->form_validation->set_rules('directionType', 'Direction Type', 'required');
                    for ($i = 1; $i <= intval($this->input->post('length_vin')); $i++) {
                        $makerNi = $mod->getMakersImpExp(explode('_', $this->input->post('typeIKT'))[0], explode('_', $this->input->post('typeIKT'))[1]);
                        $resMakerNi = json_encode($makerNi[0], JSON_FORCE_OBJECT);
                        $arr = json_decode($resMakerNi, true);
                        $resExp = $arr["EXPORT"];
                        if ($this->userauth->getLoginData()->sender == 'TAM' || $this->userauth->getLoginData()->sender == 'OTHER' || $this->userauth->getLoginData()->sender == 'SGMW' || $this->userauth->getLoginData()->sender == 'HONDAA') {
                            $this->form_validation->set_rules('noDok' . $i, 'Nomor Dokumen', 'required');
                            $this->form_validation->set_rules('tglNpe' . $i, 'Tanggal NPE', 'required');
                        } elseif ($this->userauth->getLoginData()->sender == 'IKT') {
                            if ($resExp == 0) {
                                $this->form_validation->set_rules('noDok' . $i, 'Nomor Dokumen', 'required');
                                $this->form_validation->set_rules('tglNpe' . $i, 'Tanggal NPE', 'required');
                            }
                        }
                        $this->form_validation->set_rules('VinNumber' . $i, 'VIN Number', 'required');
                        $this->form_validation->set_rules('models' . $i, 'Models', 'required');
                        $this->form_validation->set_rules('destinate' . $i, 'Destination', 'required');
                        $this->form_validation->set_rules('controlling_org' . $i, 'Controlling Organization', 'required');
                        $this->form_validation->set_rules('consignee' . $i, 'Consignee', 'required');
                        // $this->form_validation->set_rules('noDok'.$i, 'Nomor Dokumen', 'required');
                        // $this->form_validation->set_rules('npwp'.$i, 'NPWP', 'required');
                        // $this->form_validation->set_rules('tglNpe'.$i, 'Tanggal NPE', 'required');
                    }
                }
                if ($this->input->post('length_bl') > 0) {
                    for ($i = 1; $i <= intval($this->input->post('length_bl')); $i++) {
                        $makerNi = $mod->getMakersImpExp(explode('_', $this->input->post('typeIKT'))[0], explode('_', $this->input->post('typeIKT'))[1]);
                        $resMakerNi = json_encode($makerNi[0], JSON_FORCE_OBJECT);
                        $arr = json_decode($resMakerNi, true);
                        $resImp = $arr["IMPORT"];
                        if ($this->userauth->getLoginData()->sender == 'SGMW' || $this->userauth->getLoginData()->sender == 'EVLS' || $this->userauth->getLoginData()->sender == 'OTHER' || $this->userauth->getLoginData()->sender == 'HONDAA') {
                            $this->form_validation->set_rules('noDok' . $i, 'Nomor Dokumen', 'required');
                            $this->form_validation->set_rules('tglDok' . $i, 'Tanggal Dokumen', 'required');
                        } elseif ($this->userauth->getLoginData()->sender == 'IKT') {
                            if ($resImp == 0) {
                                $this->form_validation->set_rules('noDok' . $i, 'Nomor Dokumen', 'required');
                                $this->form_validation->set_rules('tglDok' . $i, 'Tanggal Dokumen', 'required');
                            }
                        }
                        $this->form_validation->set_rules('BLNumber' . $i, 'BL Number', 'required');
                        $this->form_validation->set_rules('kdDok' . $i, 'Kode Dokumen', 'required');
                        $this->form_validation->set_rules('npwp' . $i, 'NPWP', 'required');
                    }
                }

                if ($this->form_validation->run() != 0 && ($this->input->post('length_vin') > 0 || $this->input->post('length_bl') > 0)) {
                    $vin = [];
                    $truckRequest = [];

                    $truckInfo = $mod->getTruckInfo($this->input->post('truckCode'));

                    // kosong
                    if (intval($this->input->post('length_vin')) === 0 && intval($this->input->post('length_bl')) === 0) {
                        $customs = array(
                            'status' => 'Failed',
                            'message' => 'BL / VIN Required'
                        );
                        echo json_encode($customs);
                        die();
                    }
                    if ($truckInfo) {
                        if (intval($this->input->post('length_vin')) > 0) {
                            for ($i = 1; $i <= intval($this->input->post('length_vin')); $i++) {
                                $vin['vinDetail'][] = array(
                                    'VinNumber' => $this->input->post('VinNumber' . $i),
                                    'Direction' => 'EXPORT',
                                    'DirectionType' => $this->input->post('directionType'),
                                    'Fuel' => strtoupper($this->input->post('fuel' . $i)),
                                    'Model' => $this->input->post('models' . $i),
                                    'Destination' => $this->input->post('destinate' . $i),
                                    'Controlling_Organization' => $this->input->post('controlling_org' . $i),
                                    'Consignee' => $this->input->post('consignee' . $i),
                                    'NoDok' => $this->input->post('noDok' . $i),
                                    'KdDok' => $this->input->post('kdDok_export' . $i),
                                    'NPWP' => $this->input->post('npwp' . $i),
                                    'TglDok' => $this->input->post('tglNpe' . $i)
                                );
                                // $cekNoDok = $vin['vinDetail'][0]['NoDok'];
                                // $cekTglDok = $vin['vinDetail'][0]['TglDok'];
                                // $resNoDok = substr($cekNoDok,-4);
                                // $resTglDok = substr($cekTglDok,0,4);
                                // if($resNoDok != $resTglDok) {
                                //     $customs = array(
                                //         'message' => 'Tahun di No Npe dan Tanggal NPE Harus Sama'
                                //     );
                                //     $data = array(
                                //         'visitID' => $visitID,
                                //         'vinResponseInfo' => $vinResponseInfo,
                                //         'blResponseInfo' => $blResponseInfo,
                                //         'makers' => $makers,
                                //         'customs' => $customs,
                                //         'dokumen' => $dokumen
                                //     );

                                //     $this->load->view('backend/pages/eticket/announce/create_announce_truck',$data);
                                //     return false;
                                // }
                            }
                        } else {
                            $vin['vinDetail'][] = array(
                                'VinNumber' => null,
                                'Direction' => null,
                                'DirectionType' => null,
                                'Fuel' => null,
                                'Model' => null,
                                'Destination' => null,
                                'Controlling_Organization' => null,
                                'Consignee' => null,
                                'NoDok' => null,
                                'KdDok' => null,
                                'NPWP' => null,
                                'TglDok' => null
                            );
                        }


                        if (intval($this->input->post('length_bl')) > 0) {
                            for ($i = 1; $i <= intval($this->input->post('length_bl')); $i++) {
                                $bls['BLDetail'][] = array(
                                    'BLNumber' => $this->input->post('BLNumber' . $i),
                                    'NoDok' => $this->input->post('noDok' . $i),
                                    'TglDok' => $this->input->post('tglDok' . $i),
                                    'KdDok' => $this->input->post('kdDok' . $i),
                                    'NPWP' => $this->input->post('npwp' . $i)
                                );
                            }
                        } else {
                            $bls['BLDetail'][] = array(
                                'BLNumber' => null,
                                'NoDok' => null,
                                'TglDok' => null,
                                'KdDok' => null,
                                'NPWP' => null,
                            );
                        }

                        $truckRequest['announceTruckInfo'][] = array(
                            'truckInfo' => array(
                                'visitID' => $this->input->post('truck_visit_id') ? strtoupper($this->input->post('truck_visit_id')) : null,  //isi optional
                                'Truck_LicensePlate' => $truckInfo[0]->TRUCK_CODE,
                                'Truck_Drivername' => $truckInfo[0]->DRIVER_NAME ? $truckInfo[0]->DRIVER_NAME : null,
                                'Truck_Driverphonenumber' => $this->input->post('driverPhoneNumber'),
                                'Truck_Carrier' => $truckInfo[0]->CARRIER_CODE,
                            ),
                            'VinInfo' => $vin,
                            'BLInfo' => $bls
                        );

                        $payload = array(
                            'ADCMessageHeader' => array(
                                'DocumentTransferId' => $doc,
                                'MessageType' => 'ANNOUNCE_TRUCK',
                                'Sender' => $this->userauth->getLoginData()->sender == 'IKT' ? $this->input->post('typeIKT') : 'IKT_' . $this->userauth->getLoginData()->sender,
                                'Receiver' => 'CARTOS',
                                'SentTime' => date("Ymdhis")
                            ),
                            'ADCMessageBody' => array(
                                'AnnounceTruckReqest' => $truckRequest
                            )
                        );

                        $getData = $mod->ann_import_bl($payload);

                        $visitID = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->visitid : null;
                        $vinResponseInfo = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->vinResponseInfo : null;
                        $blResponseInfo = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->blResponseInfo : null;

                        $data = array(
                            'status' => 'Success',
                            'visitID' => $visitID,
                            'vinResponseInfo' => $vinResponseInfo,
                            'blResponseInfo' => $blResponseInfo,
                        );
                        echo json_encode($data);

                        $this->logger
                            ->user($this->userauth->getLoginData()->username)
                            ->function_name($this->router->fetch_method())
                            ->comment('Announce Truck ' . $visitID)
                            ->new_value(json_encode($getData))
                            ->log();
                    } else {
                        $customs = array(
                            'status'  => 'Failed',
                            'message' => 'Truck info not found'
                        );
                        echo json_encode($customs);
                    }
                } elseif ($_FILES['upload_vin_excel']['name']) {
                    $this->form_validation->set_rules('truckCode', 'Truck Code', 'required');
                    if ($this->form_validation->run() != FALSE) {
                        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
                        $csvreader = new PHPExcel_Reader_Excel2007();
                        $path = $_FILES["upload_vin_excel"]["tmp_name"];
                        $loadcsv = $csvreader->load($path);
                        $tmp_code = array();
                        $dataArray;

                        foreach ($loadcsv->getWorksheetIterator() as $worksheet) {
                            $sheetName = $worksheet->getTitle();
                            $highestRow = $worksheet->getHighestRow();
                            $highestColumn = $worksheet->getHighestColumn();

                            if ($sheetName == 'Export') {
                                for ($row = 2; $row <= $highestRow; $row++) {
                                    $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                                    if ($this->isEmptyRow(reset($rowData))) {
                                        continue;
                                    }
                                    $vin = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                                    $controllingCode = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                                    $consigneeCode = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

                                    $dataArray['vinNumber'][] = $vin;
                                    $dataArray['direction'][] = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                                    $dataArray['directionType'][] = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                                    $dataArray['fuel'][] = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                                    $dataArray['modelCode'][] = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                                    $dataArray['destinationCode'][] = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                                    $dataArray['controllingCode'][] = $controllingCode;
                                    $dataArray['consigneeCode'][] = $consigneeCode;
                                    $dataArray['noNpe'][] = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                                    $dataArray['tglNpe'][] = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                                    $dataArray['npwp'][] = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                                    $dataArray['kdDoc_export'][] = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                                }
                            }
                            if ($sheetName == 'Import') {
                                for ($row = 2; $row <= $highestRow; $row++) {
                                    $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                                    if ($this->isEmptyRow(reset($rowData))) {
                                        continue;
                                    }
                                    $blNumber = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                                    $dataArray['blNumber'][] = $blNumber;
                                    $dataArray['noDoc'][]    = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                                    $dataArray['tglDoc'][]   = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                                    $dataArray['kdDoc'][]    = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                                    $dataArray['npwpBl'][]   = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                                }
                            }
                        }
                        $countBill = count($dataArray['blNumber']);
                        $countVin  = count($dataArray['vinNumber']);

                        if ($countVin >= 1) {
                            if (!$controllingCode || !$consigneeCode) {
                                $customs = [
                                    'status' => 'Failed',
                                    'message' => $vin . ' Controlling/Consignee Tidak Boleh Kosong'
                                ];
                                echo json_encode($customs);
                                die();
                            }
                            for ($i = 0; $i < $countVin; $i++) {
                                $vinNumber = $dataArray['vinNumber'][$i];
                                $direction = $dataArray['direction'][$i];
                                $directionType = $dataArray['directionType'][$i];
                                $fuel = $dataArray['fuel'][$i];
                                $modelCode = $dataArray['modelCode'][$i];
                                $destinationCode = $dataArray['destinationCode'][$i];
                                $controllingCodeX = $dataArray['controllingCode'][$i];
                                $consigneeCodeX = $dataArray['consigneeCode'][$i];
                                $noNpe = $dataArray['noNpe'][$i];
                                $tglNpe = $dataArray['tglNpe'][$i];
                                $npwp = $dataArray['npwp'][$i];
                                $kdDoc_export = $dataArray['kdDoc_export'][$i];

                                if (in_array($dataArray['truck_license_plate'], $tmp_code)) {
                                    $truckRequest['announceTruckInfo'][array_search($dataArray['truck_license_plate'], $tmp_code)]['VinInfo']['vinDetail'][] =  array(
                                        'VinNumber' => $vinNumber ? strtoupper($vinNumber) : $vinNumber,
                                        'Direction' => $direction ? strtoupper($direction) : $direction,
                                        'DirectionType' => $directionType ? strtoupper($directionType) : $directionType,
                                        'Fuel' => $fuel ? strtoupper($fuel) : $fuel,
                                        'Model' => $modelCode ? strtoupper($modelCode) : $modelCode,
                                        'Destination' => $destinationCode ? strtoupper($destinationCode) : $destinationCode,
                                        'Controlling_Organization' => $controllingCodeX ? strtoupper($controllingCodeX) : $controllingCodeX,
                                        'Consignee' => $consigneeCodeX ? strtoupper($consigneeCodeX) : $consigneeCodeX,
                                        'NoDok' => $noNpe ? strtoupper($noNpe) : $noNpe,
                                        'KdDok' => $kdDoc_export ? strtoupper($kdDoc_export) : $kdDoc_export,
                                        'TglDok' => $tglNpe ? strtoupper($tglNpe) : $tglNpe,
                                        'NPWP' => $npwp ? strtoupper($npwp) : $npwp
                                    );
                                } else {
                                    if ($countBill >= 1) {
                                        for ($i = 0; $i < $countBill; $i++) {
                                            $cekCargo = $this->countCargo($dataArray['blNumber'][$i]);
                                            if ($cekCargo == 0) {
                                                $customs = array(
                                                    'status'  => 'Warning'
                                                );
                                                echo json_encode($customs);
                                                die();
                                            } else {
                                                array_push($tmp_code, $dataArray['truckPlate']);
                                                $bls['BLDetail'][] = array(
                                                    'BLNumber' => $dataArray['blNumber'][$i],
                                                    'NoDok' => $dataArray['noDoc'][$i],
                                                    'TglDok' => $dataArray['tglDoc'][$i],
                                                    'KdDok' => $dataArray['kdDoc'][$i],
                                                    'NPWP' => $dataArray['npwpBl'][$i],
                                                );
                                            }
                                        }
                                    } else if ($countBill < 1) {
                                        array_push($tmp_code, $dataArray['truck_license_plate']);
                                        $bls['BLDetail'][] = array(
                                            'BLNumber' => null,
                                            'NoDok' => null,
                                            'TglDok' => null,
                                            'KdDok' => null,
                                            'NPWP' => null,
                                        );
                                    }
                                    $truckRequest['announceTruckInfo'][] = array(
                                        'truckInfo' => array(
                                            'visitID' => $this->input->post('truck_visit_id') ? strtoupper($this->input->post('truck_visit_id')) : null,  //isi optional
                                            'Truck_LicensePlate' => trim(strtoupper($this->input->post('truckCode'))),
                                            'Truck_Drivername' => $driverName ? $driverName : null,
                                            'Truck_Driverphonenumber' => $this->input->post('driverPhoneNumber') ? $this->input->post('driverPhoneNumber') : null,
                                            'Truck_Carrier' => $truckCarrierCode ? strtoupper($truckCarrierCode) : null
                                        ),
                                        'VinInfo' => array(
                                            'vinDetail' => array(
                                                array(
                                                    'VinNumber' => $vinNumber ? strtoupper($vinNumber) : $vinNumber,
                                                    'Direction' => $direction ? strtoupper($direction) : $direction,
                                                    'DirectionType' => $directionType ? strtoupper($directionType) : $directionType,
                                                    'Fuel' => $fuel ? strtoupper($fuel) : $fuel,
                                                    'Model' => $modelCode ? strtoupper($modelCode) : $modelCode,
                                                    'Destination' => $destinationCode ? strtoupper($destinationCode) : $destinationCode,
                                                    'Controlling_Organization' => $controllingCodeX ? strtoupper($controllingCodeX) : $controllingCodeX,
                                                    'Consignee' => $consigneeCodeX ? strtoupper($consigneeCodeX) : $consigneeCodeX,
                                                    'NoDok' => $noNpe ? strtoupper($noNpe) : $noNpe,
                                                    'KdDok' => $kdDoc_export ? strtoupper($kdDoc_export) : $kdDoc_export,
                                                    'TglDok' => $tglNpe ? strtoupper($tglNpe) : $tglNpe,
                                                    'NPWP' => $npwp ? strtoupper($npwp) : $npwp

                                                )
                                            )
                                        ),
                                        'BLInfo' => $bls
                                    );
                                }
                            }
                        } else if ($countVin < 1) {
                            if (!$blNumber) {
                                $customs = [
                                    'status' => 'Failed',
                                    'message' => 'Vin / BL Number Tidak Boleh Kosong'
                                ];
                                echo json_encode($customs);
                                die();
                            }
                            for ($i = 0; $i < $countBill; $i++) {
                                $cekCargo = $this->countCargo($dataArray['blNumber'][$i]);
                                if ($cekCargo == 0) {
                                    $customs = array(
                                        'status'  => 'Warning'
                                    );
                                    echo json_encode($customs);
                                    die();
                                } else {
                                    array_push($tmp_code, $dataArray['truckPlate']);
                                    $bls['BLDetail'][] = array(
                                        'BLNumber' => $dataArray['blNumber'][$i],
                                        'NoDok' => $dataArray['noDoc'][$i],
                                        'TglDok' => $dataArray['tglDoc'][$i],
                                        'KdDok' => $dataArray['kdDoc'][$i],
                                        'NPWP' => $dataArray['npwpBl'][$i],
                                    );
                                }
                            }
                            $truckRequest['announceTruckInfo'][] = array(
                                'truckInfo' => array(
                                    'visitID' => $this->input->post('truck_visit_id') ? strtoupper($this->input->post('truck_visit_id')) : null,  //isi optional
                                    'Truck_LicensePlate' => trim(strtoupper($this->input->post('truckCode'))),
                                    'Truck_Drivername' => $driverName ? $driverName : null,
                                    'Truck_Driverphonenumber' => $this->input->post('driverPhoneNumber') ? $this->input->post('driverPhoneNumber') : null,
                                    'Truck_Carrier' => $truckCarrierCode ? strtoupper($truckCarrierCode) : null
                                ),
                                'VinInfo' => array(
                                    'vinDetail' => array(
                                        array(
                                            'VinNumber' => null,
                                            'Direction' => null,
                                            'DirectionType' => null,
                                            'Fuel' => null,
                                            'Model' => null,
                                            'Destination' => null,
                                            'Controlling_Organization' => null,
                                            'Consignee' => null,
                                            'NoDok' => null,
                                            'KdDok' => null,
                                            'TglDok' => null,
                                            'NPWP' => null
                                        )
                                    )
                                ),
                                'BLInfo' => $bls
                            );
                        }

                        $payload = array(
                            'ADCMessageHeader' => array(
                                'DocumentTransferId' => $doc,
                                'MessageType' => 'ANNOUNCE_TRUCK',
                                'Sender' => $this->userauth->getLoginData()->sender == 'IKT' ? $this->input->post('typeIKT') : 'IKT_' . $this->userauth->getLoginData()->sender,
                                'Receiver' => 'CARTOS',
                                'SentTime' => date("Ymdhis")
                            ),
                            'ADCMessageBody' => array(
                                'AnnounceTruckReqest' => $truckRequest
                            )
                        );
                        $getData = $mod->ann_import_bl($payload);

                        $visitID = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->visitid : null;
                        $vinResponseInfo = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->vinResponseInfo : null;
                        $blResponseInfo = $getData ? $getData->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo[0]->blResponseInfo : null;

                        $dataInfo = [
                            'status' => 'Success',
                            'visitID' => $visitID,
                            'vinResponseInfo' => $vinResponseInfo,
                            'blResponseInfo' => $blResponseInfo,
                        ];
                        echo json_encode($dataInfo);
                        // if( $visitID && $this->input->post('length_bl') > 0){
                        //     for ($n = 1 ; $n <=intval($this->input->post('length_bl')) ; $n++){
                        //         $models = model('return_cargo/rc_model');
                        //         $models->create_import(
                        //             $visitID,
                        //             $this->input->post('truckCode'),
                        //             $this->input->post('BLNumber'.$n),
                        //             null,
                        //             json_encode($vinResponseInfo));
                        //     }
                        // }

                        $this->logger
                            ->user($this->userauth->getLoginData()->username)
                            ->function_name($this->router->fetch_method())
                            ->comment('Excel Announce Truck ' . $visitID)
                            ->new_value(json_encode($getData))
                            ->log();
                    } else {
                        $customs = array(
                            'status'  => 'Failed',
                            'message' => 'Truck Code Tidak Boleh Kosong'
                        );
                        echo json_encode($customs);
                        die();
                    }
                } else {
                    $customs = array(
                        'status'  => 'Failed',
                        'message' => 'Data Tidak Boleh Kosong'
                    );
                    echo json_encode($customs);
                    die();
                }
            }
        }
    }

    public function getListBL()
    {
        $mod = model('etickets');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->get_list_bl($this->userauth->getLoginData()->sender, $searchTerm);
        echo json_encode($response);
    }

    private function countCargo($blNumber)
    {
        $mod = model('etickets');
        $response = $mod->getInfoBL($blNumber);
        $dataRes = (int)$response[0]['REMAINING_CARGO'];
        return json_encode($dataRes);
    }

    public function getInfoBL()
    {
        $mod = model('etickets');

        $searchTerm = $this->input->post('truckCode');
        $response = $mod->getInfoBL($searchTerm);

        echo json_encode($response);
    }

    public function getNpwp()
    {
        $mod = model('etickets');

        $response = $mod->getNPWP($this->userauth->getLoginData()->sender);

        echo json_encode($response);
    }

    public function getExpImpMaker()
    {
        $mod = model('etickets');

        $makerNi = $this->input->post('makerNi');
        $senderNi = $this->input->post('senderNi');
        $response = $mod->getMakersImpExp($makerNi, $senderNi);

        echo json_encode($response);
    }

    public function getVin()
    {

        $mod = model('etickets');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->get_vin($this->userauth->getLoginData()->sender, $searchTerm);

        echo json_encode($response);
    }

    public function getModel()
    {

        $mod = model('etickets');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->get_model($this->userauth->getLoginData()->sender, $searchTerm);

        echo json_encode($response);
    }

    public function getModelByVin()
    {

        $mod = model('etickets');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->get_model_by_vin($searchTerm);

        echo json_encode($response);
    }

    public function getDestination()
    {
        $mod = model('etickets');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getDestination($searchTerm);

        echo json_encode($response);
    }

    public function getControlling()
    {
        $mod = model('etickets');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getControlling($searchTerm);

        echo json_encode($response);
    }

    public function getKdDok()
    {
        $mod = model('etickets');

        // $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getKdDok();
        echo json_encode($response);
    }

    public function getNoDocument() {
        $mod = model('etickets');
        $response = $mod->selectDoc();

        echo json_encode($response);
    }

    public function getJumlahCargo($doc, $sisa = null) {
        $mod = model('etickets');
        $no_doc = str_replace('B4tA5', '/', $doc);
        
        // $response = "";
        if($sisa != null) {
            $sisaValid = str_replace('B4tA5', '/', $sisa);
            $response = $mod->getDocument($no_doc, $sisaValid);
        } else {
            $response = $mod->getDocument($no_doc);
        }

        echo json_encode($response);
    }
}
