<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Doikt extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library('logger');
        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }
    }

    public function index() {
        $this->load->library(array('form_validation'));

        $this->load->view('backend/pages/do_ikt/index');
    }

    function getDoIkt()
    {
        $mod = model('do_ikt/doikt_model');
        $list = $mod->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->MSG_ID;
            $row[] = $field->DO_NO;
            $row[] = $field->BL_NO;
            $row[] = $field->VIN;
            $row[] = $field->CONSIGNEE;
            $row[] = $field->CUSTOMER;
            $row[] = $field->CARRIER;
            $row[] = $field->VESSEL_NAME;
            $row[] = $field->VESSEL_CALLSIGN;
            $row[] = $field->VESSEL_VOYAGE_IN;
            $row[] = $field->VESSEL_VOYAGE_OUT;
            $row[] = $field->PORT_LOADING;
            $row[] = $field->PORT_DISCHARGE;
            $row[] = $field->ATA;
            $row[] = $field->VIN_DESCRIPTION;
            $row[] = $field->RECORD_TIME;
            $row[] = $field->GROSS_WEIGHT;
            $row[] = $field->DO_RELEASE_DATE;
            $row[] = $field->DO_EXPIRED_DATE;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $mod->count_all(),
            "recordsFiltered" => $mod->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

}
