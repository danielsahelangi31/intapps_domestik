<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Return_Cargo extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library('logger');
        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }
    }

    public function request_return(){
        $this->load->view('backend/pages/eticket/return_cargo/request_return_cargo');
    }

    public function get_items()
    {

        $data_exist = model('return_cargo/rc_item_model');
        $data_vin = $data_exist->get_req_exist();
        $mod = model('return_cargo/rc_model');
        $list = $mod->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $check = $mod->get_rc_status($field->VIN);
//            var_dump($check);
//            $check[0]->RC_STATUS ==
            if(count($check) > 1){
                if ($check[0]["RC_STATUS"] == '1'){
                    $action = '
                        <a onclick="rejectReturn('."'".$check[0]["RC_NO_REQ"]."'".","."'".$field->VIN."'".')" type="button" class="btn btn-danger">Cancel</a>
                        <a onclick="requestPrint('."'".$check[0]["RC_NO_REQ"]."'".')" type="button" class="btn btn-success">Print Out</a>
                    ';
                }else{
                    $action = 'Approved';
                }

            }else{
                $action = '<a onclick="requestInputData('."'".$field->VIN."'".')" type="button" class="btn btn-primary">Request</a>';
            }

//            if(in_array($field->VIN,$data_vin)){
//                $action = 'Requested';
//            }else{
//                $action = '<a onclick="requestInputData('."'".$field->VIN."'".')" type="button" class="btn btn-primary">Request</a>';
//            }

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->VIN;
            $row[] = $field->CATEGORY3;
            $row[] = $field->DESCRIPTION ? $field->DESCRIPTION : 'No Data';
            $row[] = $field->DAMAGECOUNT > 0 ? 'Damaged' : 'No Damage' ;
            $row[] = $field->HOLDCOUNT > 0 ? 'Yes' : 'No' ;
            $row[] = $action;
            $row[] = $field->LOCALSTATUS;

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

    public function submit_item(){
        if(post()){
            $this->load->library(array('form_validation'));

            if($this->userauth->getLoginData()->sender == 'IKT'){
                if (empty($_FILES['browse_ktp_sim']['name']))
                {
                    $this->form_validation->set_rules('browse_ktp_sim', 'Driver Identity', 'required');
                }
            }

            $this->form_validation->set_rules('truckCode', 'Truck Code', 'required');
            $this->form_validation->set_rules('driverName', 'Driver Name', 'required');


            if ( $this->form_validation->run() != 0){
                $mod_damage = model('return_cargo/rc_model');
                $mod = model('return_cargo/rc_item_model');

                $damage = $mod_damage->get_damage(post('vin_request'));
                $payload = array(
                    'Sender' => $this->userauth->getLoginData()->username,
                    'Vin' => post('vin_request'),
                    'DamageStatus' => $damage[0]["STATUS"],
                    'truckCode' => post('truckCode'),
                    'driverName' => post('driverName'),
                );

                $list = $mod->submit_request($payload);

                if($list->response->StatusCode == "200" && $this->userauth->getLoginData()->sender == 'IKT'){
                    $this->logger
                        ->user($this->userauth->getLoginData()->username)
                        ->function_name($this->router->fetch_method())
                        ->comment('Return Cargo '.$list->response->RCNumberReq)
                        ->new_value(json_encode($list))
                        ->log();
                    $this->load->library('ftp');
                    $configs['hostname'] = '172.16.254.219';
                    $configs['username'] = 'eticketikt';
                    $configs['password'] = 'IKT@X7f6';
                    $configs['port']     = 21;
                    $configs['debug']        = TRUE;
                    $this->ftp->connect($configs);

                    $nmfile_vin                 =  $list->response->RCNumberReq;
                    $config1['upload_path']      = FILE_PATH; //$_SERVER['DOCUMENT_ROOT'].'/Intapps/dokumen_bc/assets/csv/';
                    $config1['file_name']        = $nmfile_vin;
                    $config1['allowed_types']    = 'pdf';

                    $this->upload->initialize($config1);
                    $this->load->library('upload', $config1);

                    $this->upload->do_upload('browse_ktp_sim');
                    $this->upload->data();

                    $file_stats =  $this->ftp->upload(FILE_PATH.$nmfile_vin.'.pdf', RC_IDENTIFIER .$nmfile_vin.'.pdf', 'ascii', 0775);

                    if($file_stats){
                        $mod->update_file_stats($nmfile_vin);
                    }

                    $this->load->helper("file");
                    unlink(FILE_PATH.$nmfile_vin.'.pdf');
                    $this->ftp->close();
                }

                echo json_encode($list);

            }else{
                $errors = validation_errors();
                echo json_encode([
                    'response' => array(
                        'StatusCode' => 400,
                        'RCStatus'=>$errors
                    )
                ]);
            }
        }
    }

    public function approval_return_cargo(){
        $this->load->view('backend/pages/eticket/return_cargo/approval_return_cargo');
    }

    public function get_approval_items(){
        $mod = model('return_cargo/rc_item_model');
        $list = $mod->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $field) {

            if($field->FILE_STATS){
                $ktp = '
                        <a onclick="window.open('."'".base_url()."eticket/return_cargo/view_file_stats/".$field->RC_NO_REQ."'".","."'"."_blank"."'".')">View</a>
                        <a onclick="file_stats('."'".$field->RC_NO_REQ."'".')">Edit</a>
                    ';
            }else{
                $ktp ='
                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                        <a onclick="file_stats('."'".$field->RC_NO_REQ."'".')" class="btn btn-primary">Browse</a>
                    </div>
                    ';
            }

            if($field->DOC_STATS){
                $doc = '
                        <a onclick="window.open('."'".base_url()."eticket/return_cargo/view_doc_stats/".$field->RC_NO_REQ."'".","."'"."_blank"."'".')">View</a>
                        <a onclick="doc_stats('."'".$field->RC_NO_REQ."'".')">Edit</a>
                    ';
            }else{
                $doc ='
                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                        <a onclick="doc_stats('."'".$field->RC_NO_REQ."'".')" class="btn btn-primary">Browse</a>
                    </div>
                    ';
            }

            if($field->RC_STATUS == '1'){
                $action = '
                    <a onclick="approveReturn('."'".$field->RC_NO_REQ."'".","."'".$field->VIN."'".')" type="button" class="btn btn-primary">Approve</a>
                    <a onclick="rejectReturn('."'".$field->RC_NO_REQ."'".","."'".$field->VIN."'".')" type="button" class="btn btn-danger">Reject</a> 
                    <a onclick="requestPrint('."'".$field->RC_NO_REQ."'".')" type="button" class="btn btn-success">Print Out</a>
                ';
            }elseif ($field->RC_STATUS == '2'){
                $action = '<a onclick="requestPrint('."'".$field->RC_NO_REQ."'".')" type="button" class="btn btn-success">Print Out</a>';
            }elseif ($field->RC_STATUS == '3'){
                $action = 'Rejected';
            }
            $no++;
            $row = array();
            $maker = $mod->getMakerAndModel($field->VIN);
            $row[] = $no;
            $row[] = $field->RC_NO_REQ;
            $row[] = $field->CREATED_DT;
            $row[] = $field->VIN ? $field->VIN : 'No Data';
            $row[] = $maker[0]->MAKER ? $maker[0]->MAKER : 'No Data';
            $row[] = $maker[0]->MODEL ? $maker[0]->MODEL : 'No Data';
            $row[] = $field->TRUCK_CODE ? $field->TRUCK_CODE : 'No Data';
            $row[] = $field->DRIVER ? $field->DRIVER : 'No Data';
            $row[] = $ktp;
            $row[] = $field->DAMAGE_STATUS ? $field->DAMAGE_STATUS : 'No Data' ;
            $row[] = $doc;
            $row[] = $action;

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

    public function approve_request(){
        $mod = model('return_cargo/rc_item_model');
        $payload = array(
            'Key' => 'R3turnC4r901KT',
            'UserUpdate' => $this->userauth->getLoginData()->username,
            'RCNumberReq' => post('rc_no_req'),
            'RCStatus' => '2',
            'Vin' => post('vins'),
        );
        $list = $mod->submit_approval($payload);
        $this->logger
            ->user($this->userauth->getLoginData()->username)
            ->function_name($this->router->fetch_method())
            ->comment('Approval RC Request '.post('rc_no_req'))
            ->new_value(json_encode($list))
            ->log();
        echo json_encode($list);
    }

    public function reject_request(){
        $mod = model('return_cargo/rc_item_model');
        $payload = array(
            'Key' => 'R3turnC4r901KT',
            'UserUpdate' => $this->userauth->getLoginData()->username,
            'RCNumberReq' => post('rc_no_req'),
            'RCStatus' => '3',
            'Vin' => post('vins'),
        );
        $list = $mod->submit_rejected($payload);
        $this->logger
            ->user($this->userauth->getLoginData()->username)
            ->function_name($this->router->fetch_method())
            ->comment('Reject RC Request '.post('rc_no_req'))
            ->new_value(json_encode($list))
            ->log();
        echo json_encode($list);
    }

    public function getTruckInfo(){
        $mod = model('return_cargo/rc_item_model');

        $searchTerm = $this->input->post('searchTerm');
        $response = $mod->getTruckInformation($searchTerm);

        echo json_encode($response);
    }

    public function getCarrierByTruck(){
        $mod = model('return_cargo/rc_item_model');

        $searchTerm = $this->input->post('truckCode');
        $response = $mod->getCarrierByTruck($searchTerm);

        echo json_encode($response);
    }

    public function submit_print(){

        $mod = model('return_cargo/rc_item_model');
        $response = $mod->submit_print();

        $result[] = array(
            "status" => $response,
            "data" => array(
                "rc_no_req" => post('id_form')
            )
        );

        echo json_encode($result);
    }

    public function print_rc($no_rc_req){
        $this->load->library('M_pdf');

        $mod = model('return_cargo/rc_item_model');
        $res = $mod->getDataPrint($no_rc_req);

        if($res){
                    $data = array(
                        'datas' => $res,
                    );
            ini_set('memory_limit', '256M');
            $html = $this->load->view('backend/pages/eticket/return_cargo/rc_print', $data, true);
            $this->m_pdf->pdf->WriteHTML($html);
            $output = $no_rc_req. '.pdf';
            $this->m_pdf->pdf->Output($output, "I");
        }

        $this->logger
            ->user($this->userauth->getLoginData()->username)
            ->function_name($this->router->fetch_method())
            ->comment('print return cargo '.$no_rc_req)
            ->log();

//        $this->logger
//            ->user($this->userauth->getLoginData()->username)
//            ->function_name($this->router->fetch_method())
//            ->comment('print '.$TRKVisitID)
//            ->log();
    }

    public function upload_file_stats(){
        try{
            $mod = model('return_cargo/rc_item_model');
            $this->load->library('ftp');
            $configs['hostname'] = '172.16.254.219';
            $configs['username'] = 'eticketikt';
            $configs['password'] = 'IKT@X7f6';
            $configs['port']     = 21;
            $configs['debug']        = TRUE;
            $this->ftp->connect($configs);

            $nmfile_vin                 =  post('no_rc');
            $config1['upload_path']      = FILE_PATH; //$_SERVER['DOCUMENT_ROOT'].'/Intapps/dokumen_bc/assets/csv/';
            $config1['file_name']        = $nmfile_vin;
            $config1['allowed_types']    = 'pdf';

            $this->upload->initialize($config1);
            $this->load->library('upload', $config1);

            $this->upload->do_upload('browse_ktp_sim');
            $this->upload->data();

            $file_stats =  $this->ftp->upload(FILE_PATH.$nmfile_vin.'.pdf', RC_IDENTIFIER .$nmfile_vin.'.pdf', 'ascii', 0775);

            if($file_stats){
                $list = $mod->update_file_stats($nmfile_vin);
                $this->logger
                    ->user($this->userauth->getLoginData()->username)
                    ->function_name($this->router->fetch_method())
                    ->comment('upload file stats return cargo '.$nmfile_vin)
                    ->log();
            }

            $this->load->helper("file");
            unlink(FILE_PATH.$nmfile_vin.'.pdf');
            $this->ftp->close();

            echo json_encode(array(
                'statusCode' => 1,
                'message' => 'success'
            ));
        }catch (Exception $e){
            echo json_encode(array(
                'statusCode' => 0,
                'message' => $e->getMessage()
            ));
        }
    }

    public function view_file_stats($no_rc_req){
        $this->load->library('ftp');
        $this->load->helper('download');
        $this->load->helper('file');
        $configs['hostname'] = '172.16.254.219';
        $configs['username'] = 'eticketikt';
        $configs['password'] = 'IKT@X7f6';
        $configs['port']     = 21;
        $configs['debug']        = TRUE;
        $this->ftp->connect($configs);

        $name = 'identified_'.$no_rc_req.'.pdf';

        $temp_path = $temp_file = tempnam(sys_get_temp_dir(), $name);
        $this->ftp->download(RC_IDENTIFIER.$no_rc_req.'.pdf', $temp_path);

        $data = file_get_contents($temp_path);
        force_download($name, $data);
        unlink($temp_path);
    }

    public function upload_doc_stats(){
        try{
            $mod = model('return_cargo/rc_item_model');
            $this->load->library('ftp');
            $configs['hostname'] = '172.16.254.219';
            $configs['username'] = 'eticketikt';
            $configs['password'] = 'IKT@X7f6';
            $configs['port']     = 21;
            $configs['debug']        = TRUE;
            $this->ftp->connect($configs);

            $nmfile_vin                 =  post('no_rc_doc');
            $config1['upload_path']      = FILE_PATH; //$_SERVER['DOCUMENT_ROOT'].'/Intapps/dokumen_bc/assets/csv/';
            $config1['file_name']        = $nmfile_vin;
            $config1['allowed_types']    = 'pdf';

            $this->upload->initialize($config1);
            $this->load->library('upload', $config1);

            $this->upload->do_upload('docs_file');
            $this->upload->data();

            $file_stats =  $this->ftp->upload(FILE_PATH.$nmfile_vin.'.pdf', RC_DOCUMENTS .$nmfile_vin.'.pdf', 'ascii', 0775);

            if($file_stats){
                $list = $mod->update_doc_stats($nmfile_vin);
                $this->logger
                    ->user($this->userauth->getLoginData()->username)
                    ->function_name($this->router->fetch_method())
                    ->comment('Upload Doc RC '.$nmfile_vin)
                    ->new_value(json_encode($list))
                    ->log();
            }

            $this->load->helper("file");
            unlink(FILE_PATH.$nmfile_vin.'.pdf');
            $this->ftp->close();

            echo json_encode(array(
                'statusCode' => 1,
                'message' => 'success'
            ));
        }catch (Exception $e){
            echo json_encode(array(
                'statusCode' => 0,
                'message' => $e->getMessage()
            ));
        }
    }

    public function view_doc_stats($no_rc_req){
        $this->load->library('ftp');
        $this->load->helper('download');
        $this->load->helper('file');
        $configs['hostname'] = '172.16.254.219';
        $configs['username'] = 'eticketikt';
        $configs['password'] = 'IKT@X7f6';
        $configs['port']     = 21;
        $configs['debug']        = TRUE;
        $this->ftp->connect($configs);

        $name = 'documents_'.$no_rc_req.'.pdf';

        $temp_path = $temp_file = tempnam(sys_get_temp_dir(), $name);
        $this->ftp->download(RC_DOCUMENTS.$no_rc_req.'.pdf', $temp_path);
        $this->logger
            ->user($this->userauth->getLoginData()->username)
            ->function_name($this->router->fetch_method())
            ->comment('view_doc_stats RC  '.$no_rc_req)
            ->log();

        $data = file_get_contents($temp_path);
        force_download($name, $data);
        unlink($temp_path);
    }

}
