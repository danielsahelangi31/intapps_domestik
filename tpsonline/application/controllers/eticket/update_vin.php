<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update_Vin extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library('logger');
        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }
    }

    public function index(){
        $this->load->library(array('form_validation'));
        if (post()){
            $this->form_validation->set_rules('truck_id', 'Truck Visit ID', 'required');
            if ( $this->form_validation->run() != 0){
                redirect('eticket/update_vin/detail/'.$this->input->post('truck_id'));
            }
        }

        $this->load->view('backend/pages/eticket/update_vin');
    }

    public function cek_dokumen($kegiatan)
    {
        $mod        = model('etickets');
        $NUM        = $this->input->post('NUM');
        $NO_DOK     = $this->input->post('NO_DOK');
        $TGL_DOK    = $this->input->post('TGL_DOK');
        $KD_DOK     = $this->input->post('KD_DOK');
        $NPWP       = $this->input->post('NPWP');
        $payload = array(
            'Sender'    => $this->userauth->getLoginData()->sender,
            'Trip'      => $kegiatan,
            'Detail'    => array(
                'Num'       => $NUM,
                'NoDoc'     => $NO_DOK,
                'TglDoc'    => $TGL_DOK,
                'KdDoc'     => $KD_DOK,
                'NPWP'      => $NPWP
            )
        );

        $getData = $mod->op_cekDoc($payload);

        if($getData->response->Code == 200){
            echo "OK";
        }else{
            echo $getData->response->Msg;
        }
    }

    public function detail($param){
        $mod = model('etickets');
        $res = $mod->getAsosiasiByTruckVisitID($param);
        $imp = $mod->getImport($param);

        $import = [];
        $export = [];

        foreach ($res as $item){
            if($item->DIRECTION != '1'){
                array_push($export, $item);
            }
        }

        foreach ($imp as $items){
            array_push($import,$items);
        }

        $getData= null;
        if (post()){
            //add import bl
//            $mod->deleteBL($this->input->post('import_checkbox'),$param);

            //cari selisih nya dulu, maka yg selisih itu berarti tidak dipilih dan berikan nilai 0
            //data import
            $dataImport = [];
            $dataExport = [];
            $userImport = [];
            $userExport = [];
            $listVIN = [];
            $payRev = [];
            $payAdd = [];
            $paySav = [];

            $blRev = [];
            $blAdd = [];
            $blSav = [];

            if(isset($imp)){
                foreach ($imp as $datas){
                    array_push($dataImport,$datas->BL_NUMBER);
                }
            }


            if($this->input->post('import_checkbox')){
                foreach ($this->input->post('import_checkbox') as $inputs_check){
                    array_push($userImport,$inputs_check);
                }
            }


            //cari selisih
//           res - input = yg diremove where not in remove
//            input - res = yg nambah
//            sisanya adalah tetap

                $removeImport = array_diff($dataImport,$userImport);
                $addImpor = array_diff($userImport,$dataImport);
                $saveImpor = array_intersect($userImport,$dataImport);
//

                foreach ($removeImport as $revs){
                    $blRev[] = array(
                        'BL' => $revs,
                        'Status' => strval(0),
                    );
                }
//
                foreach ($addImpor as $adds){
                    $blAdd[] = array(
                        'BL' => $adds,
                        'Status' => strval(1),
                    );
                }
//
                foreach ($saveImpor as $savs){
                    $blSav[] = array(
                        'BL' => $savs,
                        'Status' => strval(1),
                    );
                }

            $bl_tmp = array_merge($blAdd,$blRev);
            $bls = array_merge($bl_tmp,$blSav);

            //data eksport

            if(isset($res)){
                foreach ($export as $item){
                    array_push($dataExport,$item->VIN);
                }
            }


            if($this->input->post('export_checkbox')){
                foreach ($this->input->post('export_checkbox') as $input){
                    array_push($userExport,$input);
                }
            }

            $removeExport = array_diff($dataExport,$userExport);
            $addExport = array_diff($userExport,$dataExport);
            $saveExport = array_intersect($userExport,$dataExport);

            foreach ($removeExport as $rev){
                $payRev[] = array(
                    'Vin' => $rev,
                    'Status' => strval(0),
                    'Trip' => 'EXPORT'
                );
            }

            foreach ($addExport as $add){
                $payAdd[] = array(
                    'Vin' => $add,
                    'Status' => strval(1),
                    'Trip' => 'EXPORT'
                );
            }

            foreach ($saveExport as $sav){
                $paySav[] = array(
                    'Vin' => $sav,
                    'Status' => strval(1),
                    'Trip' => 'EXPORT'
                );
            }



            $all_tmp = array_merge($payAdd,$payRev);
            $all = array_merge($all_tmp,$paySav);

            $payload = array(
                'HeaderCancelAsosiasi' => array(
                    'Sender' => $this->userauth->getLoginData()->sender
                ),
                'BodyCancelRequest'=> array(
                    'VisitID' => $this->input->post('visit_id'),
                    'ListVin' => array(
                        'VinInfo' => $all
                    ),
                    'ListBL'  => array(
                        'BLInfo' => $bls
                    )
                )
            );


            $getData = $mod->updateAsosiasi($payload);

            if($getData->success){
                $this->session->set_flashdata('responses', $getData->response->UpdateAsosiasi);
            }

            $this->logger
                ->user($this->userauth->getLoginData()->username)
                ->function_name($this->router->fetch_method())
                ->comment('Update VIN '.$param)
                ->old_value(json_encode($res))
                ->new_value(json_encode($getData))
                ->log();

            redirect($this->uri->uri_string());

        }

        $data = array(
            'npwp' => $mod->getNPWP($this->userauth->getLoginData()->sender),
            'dokumen_import' => $mod->get_dok('EXPORT'),
            'dokumen_export' => $mod->get_dok('IMPORT'),
            'visitID' => $param,
            'imports' => $import,
            'exports' => $export,
        );

        $this->load->view('backend/pages/eticket/update_vin_detail',$data);
    }

    public function getListVIN($type){
        $mod = model('etickets');
        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getListVIN($this->userauth->getLoginData()->sender,$type,$searchTerm);

        echo json_encode($response);
    }


}
