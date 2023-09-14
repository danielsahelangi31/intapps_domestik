<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inquiry extends CI_Controller {

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

        $this->load->view('backend/pages/doc_inquiry/index');
    }

    function get_docs()
    {
        $mod = model('doc_inquiry/inquiry_model');
        $list = $mod->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->VIN;
            $row[] = $field->MAKE;
            $row[] = $field->DESTINATION;
            $row[] = $field->MODEL;
            $row[] = $field->ENGINE_NO;
            $row[] = $field->VESSEL_NAME;
            $row[] = $field->ETD;
            $row[] = $field->NO_PEB;
            $row[] = $field->TGL_PEB;
            $row[] = $field->CUSTOMS_NUMBER;

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
