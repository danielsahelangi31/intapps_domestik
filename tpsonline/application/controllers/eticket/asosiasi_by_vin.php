<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asosiasi_By_Vin extends CI_Controller {

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
            $mod = model('etickets');
            $vin = [];
            $this->form_validation->set_rules('no_vin1', 'VIN pertama', 'required');
            if ( $this->form_validation->run() != 0){

                if(intval($this->input->post('length_vin')) > 0){
                    for ($i = 1 ; $i <=intval($this->input->post('length_vin')) ; $i++){
                        $vin[] = $this->input->post('no_vin'.$i);
                    }
                }else{
                    $vin[] = null;
                }

            }

            $payload = array(
                'Sender' => $this->userauth->getLoginData()->sender,
                'ListVIN' => $vin
            );

            $getData = $mod->op_inquiryCM($payload);

            if($getData->response->RespMessage){
                $this->session->set_flashdata('responses', $getData->response->DataInquiry);
            }

            $this->logger
                ->user($this->userauth->getLoginData()->username)
                ->function_name($this->router->fetch_method())
                ->comment('Check Asosiasi by VIN')
                ->log();

            redirect($this->uri->uri_string());
        }

        $this->load->view('backend/pages/eticket/cek_asosiasi/by_vin');
    }
}
