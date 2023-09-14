<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asosiasi_By_Truck_Code extends CI_Controller {

    public function __construct(){
        parent::__construct();

        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }
    }

    public function index(){
        $this->load->library(array('form_validation'));
        if (post()){
            $this->form_validation->set_rules('truck_code', 'Truck Code', 'required');
            $this->form_validation->set_rules('type_gate', 'Type Gate', 'required');
            if ( $this->form_validation->run() != 0){
                redirect('eticket/asosiasi_by_truck_code/detail/'.$this->input->post('truck_code').'/'.$this->input->post('type_gate'));
            }
        }

        $this->load->view('backend/pages/eticket/cek_asosiasi/by_truck_code');
    }

    public function detail($truck_code,$type){
        $mod = model('etickets');
        $res = $mod->getAsosiasiByTruckCode($this->userauth->getLoginData()->sender,$type,$truck_code);

        $data = array(
            'InfoTrip' => $res ? $res->response->InfoTrip[0] : null,
            'InfoTruck' => $res ? $res->response->InfoTruck[0] : null,
            'ListVIN' => $res? $res->response->ListVIN: null,
            'response' => $res ? $res->response : null
        );

        $this->load->view('backend/pages/eticket/cek_asosiasi/by_truck_detail',$data);
    }
}
