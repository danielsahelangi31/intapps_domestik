<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_Data_Truck extends CI_Controller {

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
        $this->load->library(array('form_validation'));
        if (post()){
            $this->form_validation->set_rules('truck_code', 'Truck Code', 'required');
            if ( $this->form_validation->run() != 0){
                redirect('eticket/master_data_truck/get_detail/'.$this->input->post('truck_code'));
            }
        }

        $this->load->view('backend/pages/eticket/master/data_truck');
    }

    public function get_detail($param){
        $mod = model('etickets');
        $datas = $mod->getTruckInfo($param);
        $carrier = $mod->getControlling();
        $owner = $mod->getControlling();
        $stats = null;

        $this->load->library(array('form_validation'));
        if (post()){
            $this->form_validation->set_rules('truck_code', 'Truck Code', 'required');
            $this->form_validation->set_rules('license_plate', 'Truck Code', 'required');
            $this->form_validation->set_rules('desc_type', 'Truck Code', 'required');
            if ( $this->form_validation->run() != 0){

                $payload = array(
                    'Sender' => $this->userauth->getLoginData()->sender,
                    'code' => $this->input->post('truck_code'),
                    'licenseplate' => $this->input->post('license_plate'),
                    'name' => $this->input->post('driver_name'),
                    'owner' => $this->input->post('owner_code'),
                    'carrier' => $this->input->post('carrier_code'),
                    'description' => $this->input->post('desc_type'),
                );
                $res = $mod->op_mTruck($payload);
                $stats = $res ? $res->response->returnMessage : null;
                $this->session->set_flashdata('stats', $stats);
                $this->logger
                    ->user($this->userauth->getLoginData()->username)
                    ->function_name($this->router->fetch_method())
                    ->comment('Update Data '.$param)
                    ->old_value(json_encode($datas))
                    ->new_value(json_encode($res))
                    ->log();
                redirect($this->uri->uri_string());
            }
        }

        $data = array(
            'datas' => $datas[0],
            'carriers' => $carrier,
            'owners' => $owner,
        );

        $this->load->view('backend/pages/eticket/master/truck_detail',$data);
    }
}
