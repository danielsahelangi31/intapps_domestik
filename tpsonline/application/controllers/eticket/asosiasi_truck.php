<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asosiasi_Truck extends CI_Controller {

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
            $this->form_validation->set_rules('truck_id', 'Truck Visit ID', 'required');
            if ( $this->form_validation->run() != 0){
                redirect('eticket/update_vin/detail/'.$this->input->post('truck_id'));
            }
        }

        $this->load->view('backend/pages/eticket/create_associate');
    }

    public function detail($param){
        $mod = model('etickets');
        $res = $mod->getAsosiasiByTruckVisitID($this->userauth->getLoginData()->sender,$param);


        $data = array(
            'datas' => $res ? $res->response->itrAsosiasi[0] : null
        );

        $this->load->view('backend/pages/eticket/update_vin_detail',$data);
    }
}
