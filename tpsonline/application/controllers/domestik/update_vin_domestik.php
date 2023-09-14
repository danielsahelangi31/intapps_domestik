<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update_Vin_Domestik extends CI_Controller {

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
                redirect('domestik/update_vin/detail/'.$this->input->post('truck_id'));
            }
        }

        $this->load->view('domestik/backend/pages/eticket/update_vin_domestik');
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
        $mod = model('domestik/etickets_domestik');
        $res = $mod->geDataTruck($param);
        $data = array(
            'truck' => $param,
            'res' => $res            
        );
        
        $this->load->view('domestik/backend/pages/eticket/update_vin_detail_domestik', $data);
    }

    public function getListVIN($type){
        $mod = model('etickets');
        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getListVIN($this->userauth->getLoginData()->sender,$type,$searchTerm);

        echo json_encode($response);
    }

    public function dataShipping()
    {
        $mod = model('domestik/etickets_domestik');
        $response = $mod->dataShipping();
        echo json_encode($response);
    }

    public function insertVinEticket() {
        // print_r("ini kepanggil"); exit();
        $mod = model('domestik/etickets_domestik');
        $hasil = $mod->insertVinEticket();
        echo json_encode($hasil);
    }

}
