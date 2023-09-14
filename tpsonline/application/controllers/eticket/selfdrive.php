<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Selfdrive extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('logger');
        // Dapatkan data login
        if (!$this->auth = $this->userauth->getLoginData()) {
            redirect(LOGIN_PAGE);
        }
    }

    public function index()
    {
        $self_mod = model('selfdrive/selfdrive_model');
        $mod = model('etickets');
        $makers = $mod->getMakers();
        $trucks = $self_mod->getTruckSelf();
        $dokumen = $mod->getKdDok();
        $responses = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->load->library(array('form_validation'));
            if ($this->userauth->getLoginData()->sender == 'IKT') {
                $this->form_validation->set_rules('typeIKT', 'Sender', 'required');
            }
            $this->form_validation->set_rules('driver_name', 'Driver Name', 'required');
            if (empty($_FILES['upload_identify']['name'])) {
                $this->form_validation->set_rules('upload_identify', 'Upload Identify', 'required');
            }
            $this->form_validation->set_rules('trip_id', 'Trip', 'required');
            // if ($this->input->post('trip_id') == "IMPORT") {
            //     $this->form_validation->set_rules('noDok', 'Nomor Dokumen', 'required');
            //     $this->form_validation->set_rules('tglDok', 'Tanggal Dokumen', 'required');
            // }
            $makerNi = $mod->getMakersImpExp(explode('_', $this->input->post('typeIKT'))[0], explode('_', $this->input->post('typeIKT'))[1]);
            $resMakerNi = json_encode($makerNi[0], JSON_FORCE_OBJECT);
            $arr = json_decode($resMakerNi, true);
            $resExp = $arr["EXPORT"];
            if ($this->userauth->getLoginData()->sender == 'TAM' || $this->userauth->getLoginData()->sender == 'OTHER' || $this->userauth->getLoginData()->sender == 'SGMW' || $this->userauth->getLoginData()->sender == 'HONDAA') {
                $this->form_validation->set_rules('noDok', 'Nomor Dokumen', 'required');
                $this->form_validation->set_rules('tglDok', 'Tanggal Dokumen', 'required');
            } elseif ($this->userauth->getLoginData()->sender == 'IKT') {
                if ($resExp == 0) {
                    $this->form_validation->set_rules('noDok', 'Nomor Dokumen', 'required');
                    $this->form_validation->set_rules('tglDok', 'Tanggal Dokumen', 'required');
                }
            }
            if ($this->userauth->getLoginData()->sender != 'EMERGENCY') {
                $this->form_validation->set_rules('kdDok', 'Kode Dokumen', 'required');
                $this->form_validation->set_rules('npwp', 'NPWP', 'required');
            }
            $this->form_validation->set_rules('vin_request', 'VIN', 'required');
            $this->form_validation->set_rules('truckCode', 'Truck', 'required');

            if ($this->form_validation->run() != 0) {
                $payload = array(
                    // 'Sender' => post('typeIKT'),
                    'Sender' => $this->userauth->getLoginData()->sender == 'IKT' ? $this->input->post('typeIKT') : 'IKT_' . $this->userauth->getLoginData()->sender,
                    'TruckCode' => post('truckCode'),
                    'DriverName' => post('driver_name'),
                    'VIN' => post('vin_request'),
                    'Trip' => strtoupper(post('trip_id')),
                    'NoDok' => post('noDok'),
                    'TglDok' => post('tglDok'),
                    'KdDok' => post('kdDok'),
                    'NPWP' => post('npwp'),
                );
                $list = $self_mod->submit_request($payload);

                if ($list->response) {
                    $responses = $list->response;
                    if ($responses->responcode == '200') {
                        $this->logger
                            ->user($this->userauth->getLoginData()->username)
                            ->function_name($this->router->fetch_method())
                            ->comment('Selfdrive ' . $responses->InfoTruck->VisitID)
                            ->new_value(json_encode($list))
                            ->log();
                        $this->load->library('ftp');
                        $configs['hostname'] = '172.16.254.219';
                        $configs['username'] = 'eticketikt';
                        $configs['password'] = 'IKT@X7f6';
                        $configs['port']     = 21;
                        $configs['debug']    = TRUE;
                        $this->ftp->connect($configs);

                        $nmfile_vin               =  $responses->InfoTruck->VisitID;
                        $config1['upload_path']   = FILE_PATH; //$_SERVER['DOCUMENT_ROOT'].'/Intapps/dokumen_bc/assets/csv/';
                        $config1['file_name']     = $nmfile_vin;
                        $config1['allowed_types'] = 'pdf';
                        $config['max_size']       = 2048;

                        $this->upload->initialize($config1);
                        $this->load->library('upload', $config1);

                        $this->upload->do_upload('upload_identify');
                        $this->upload->data();

                        $file_stats =  $this->ftp->upload(FILE_PATH . $nmfile_vin . '.pdf', SELFDRIVE_IDENTIFIER . $nmfile_vin . '.pdf', 'ascii', 0775);

                        $this->load->helper("file");
                        unlink(FILE_PATH . $nmfile_vin . '.pdf');
                        $this->ftp->close();
                    }
                }
            }
        }

        $data = array(
            'makers' => $makers,
            'trucks' => $trucks,
            'responses' => $responses,
            'dokumen' => $dokumen
        );
        $this->load->view('backend/pages/eticket/selfdrive/index', $data);
    }

    public function getVINSeldrive()
    {
        $mod = model('selfdrive/selfdrive_model');

        $searchTerm = $this->input->post('searchTerm');
        $maker = $this->input->post('maker') ? explode('_', $this->input->post('maker'))[1] : $this->userauth->getLoginData()->sender;
        $trip = $this->input->post('trip');
        $list = $mod->getListVINSelfDrive($maker, $trip, $searchTerm);

        echo json_encode($list);
    }

    public function view_doc_self($visit)
    {
        $this->load->library('ftp');
        $this->load->helper('download');
        $this->load->helper('file');
        $configs['hostname'] = '172.16.254.219';
        $configs['username'] = 'eticketikt';
        $configs['password'] = 'IKT@X7f6';
        $configs['port']     = 21;
        $configs['debug']    = TRUE;
        $this->ftp->connect($configs);

        $name = $visit . '.pdf';

        $temp_path = $temp_file = tempnam(sys_get_temp_dir(), $name);
        $this->ftp->download(SELFDRIVE_IDENTIFIER . $visit . '.pdf', $temp_path);

        $data = file_get_contents($temp_path);
        force_download($name, $data);
        unlink($temp_path);
    }
}
