<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Master_Document extends CI_Controller {

    // define constant variables
    private $username = '1nt4pp5_1lcs';
    private $password = '0nly1nt4pp5';
    private $path_sourch_ftp = './assets/file_storage/';

    public function __construct(){
        parent::__construct();
        $this->load->library(['form_validation']);
        // $this->load->helper(['form']);
        $this->load->helper(array('form', 'url'));
        $this->load->library('logger');

        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }
    }

    public function index() {
        $mod = model('etickets');
        $data['dokumen_import'] = $mod->get_dok('IMPORT');
        $this->load->library(['form_validation']);
        
        $this->load->view('backend/pages/eticket/master/data_document', $data);
    }

    public function list_document($sender) {
        $mod = model('etickets');
        
        $list = $mod->get_list_document($sender);
        $data = [];
        $no = $_POST['start'];
        foreach ($list as $field) {
            $data_list = $field->NOMOR_DOKUMEN.'B4tA5'.$field->TANGGAL.'B4tA5'.$field->NPWP.'B4tA5'.$field->KODE.'B4tA5'.$field->TIPE_DOKUMEN.'B4tA5'.$field->MERK_KMS.'B4tA5'.$field->JUMLAH_CARGO;
            $action = '<a onclick="requestUpdateData('."'".$data_list."'".')" type="button" class="btn btn-warning" data-toggle="tooltip" title="Update Document"><span class="glyphicon glyphicon-pencil"></span></a>';
            if($field->DOCUMENT_PENDUKUNG) {
                $action .= "&nbsp;";
                $action .= '<a onclick="window.open('."'".base_url()."eticket/master_document/getFtpFile/".$field->DOCUMENT_PENDUKUNG."'".","."'"."'".')" target="_blank" type="button" class="btn btn-success" data-toggle="tooltip" title="Download Document Pendukung"><span class="glyphicon glyphicon-download-alt"></span></a>';
            }
            
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $field->NOMOR_DOKUMEN;
            $row[] = $field->TANGGAL;
            $row[] = $field->NPWP;
            $row[] = $field->TIPE_DOKUMEN;
            $row[] = $field->MERK_KMS;
            $row[] = $field->JUMLAH_CARGO;
            $row[] = $action;

            $data[] = $row;
        }

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $mod->count_all_document(),
            "recordsFiltered" => $mod->count_filtered_document(),
            "data" => $data,
        ];
        // print_r($data);

        //output dalam format JSON
        echo json_encode($output);
    }

    public function submit() {
        $mod = model('etickets');

        // condition create or update
        if($this->input->post('flag') == 'update') {
            $this->load->library('ftp');
            $configs['hostname'] = '172.16.254.219';
            $configs['username'] = 'eticketikt';
            $configs['password'] = 'IKT@X7f6';
            $configs['port']     = 21;
            $configs['debug']    = TRUE;
            $this->ftp->connect($configs);

            $file                    = $_FILES['docFile'];
            $file_name               = str_replace(" ", "_", $file['name']);
            $_POST['docFile']        = time().'_'.$file_name;
            $file_doc                = $_POST['docFile'];
            $config['upload_path']   = $this->path_sourch_ftp;
            $config['file_name']     = $file_doc;
            $config['allowed_types'] = 'pdf|jpeg|jpg|png';

            $this->upload->initialize($config);
            $this->load->library('upload', $config);

            if($file['error'] == 0) {
                $this->upload->do_upload('docFile');
                $this->upload->data();

                $file_stats =  $this->ftp->upload(FILE_PATH.$file_doc, CG_DOCUMENTS .$file_doc, 'ascii', 0775);
                if($file_stats) {
                    if(file_exists($this->path_sourch_ftp . $file_doc)) {
                        unlink($this->path_sourch_ftp . $file_doc);
                    }
                    
                    // update data
                    $output = $mod->op_updateCargo($this->input->post());

                    echo json_encode($output);
                }
            } else if($file['error'] == 7) {
                // $this->upload->display_errors(); // untuk debugging display error
                $responsearray = [];
                $response = new stdClass;
                $response->Code = '7';
                $response->Msg = 'Cannot write file.';
                $responsearray['success'] = 1;
                $responsearray['response'] = $response;

                echo json_encode($responsearray);
                die();
            } else if($file['error'] == 4) {
                // $this->upload->display_errors(); // untuk debugging display error
                $responsearray = [];
                $response = new stdClass;
                $response->Code = '4';
                $response->Msg = 'No file was uploaded.';
                $responsearray['success'] = 1;
                $responsearray['response'] = $response;

                echo json_encode($responsearray);
                die();
            } else {
                // $this->upload->display_errors(); // untuk debugging display error
                $responsearray = [];
                $response = new stdClass;
                $response->Code = '204';
                $response->Msg = 'Format file tidak support.';
                $responsearray['success'] = 1;
                $responsearray['response'] = $response;

                echo json_encode($responsearray);
                die();
            }
        } else {
            // insert data
            $payload = [
                'Username'    => $this->username,
                'Password'    => $this->password,
                'Sender'      => $this->userauth->getLoginData()->sender,
                'NoDoc'       => $this->input->post('noDoc'),
                'TglDoc'      => $this->input->post('tglDoc'),
                'KdDoc'       => $this->input->post('kdDoc'),
                'NPWP'        => $this->input->post('NPWP'),
                'TotalCargo'  => $this->input->post('totalCargo'),
                'Merk_KMS'    => $this->input->post('merkKemasan'),
            ];
            
            $output = $mod->op_insertCargo($payload);
            
            echo json_encode($output);
        }
    }

    public function getFtpFile($file) {
        $this->load->library('ftp');
        $this->load->helper(['download', 'file']);
        $configs['hostname'] = '172.16.254.219';
        $configs['username'] = 'eticketikt';
        $configs['password'] = 'IKT@X7f6';
        $configs['port']     = 21;
        $configs['debug']    = TRUE;
        $this->ftp->connect($configs);
        
        $temp_path = tempnam(sys_get_temp_dir(), $file);
        $this->ftp->download(CG_DOCUMENTS.$file, $temp_path);
        
        $data = file_get_contents($temp_path);
        force_download($file, $data);
        unlink($temp_path);
    }

    // private function isEmptyRow($row)
    // {
    //     foreach ($row as $cell) {
    //         if (null !== $cell) return false;
    //     }
    //     return true;
    // }

    // public function upload_doc() {
    //     // import excel formatter
    //     include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
    //     $csvreader = new PHPExcel_Reader_Excel2007();

    //     // params
    //     $mod = model('etickets');
    //     $file = $_FILES['uploadBulk'];
    //     $path = $file["tmp_name"];
    //     $loadexcel = $csvreader->load($path);
    //     $dataArray = [];
        
    //     // get data from excel
    //     foreach ($loadexcel->getWorksheetIterator() as $list) {
    //         $sheetName = $list->getTitle();
    //         $highestRow = $list->getHighestRow();
    //         $highestColumn = $list->getHighestColumn();
            
    //         if ($sheetName == 'Upload') {
    //             for ($row = 2; $row <= $highestRow; $row++) {
    //                 $rowData = $list->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
    //                 if ($this->isEmptyRow(reset($rowData))) {
    //                     continue;
    //                 }
    //                 $noDoc = $list->getCellByColumnAndRow(0, $row)->getValue();
    //                 $totalCargo = $list->getCellByColumnAndRow(1, $row)->getValue();
    //                 $dataArray['noDoc'][] = $noDoc;
    //                 $dataArray['totalCargo'][] = $totalCargo;
    //             }
    //         }
    //     }

    //     // remapping data
    //     $list = [];
    //     foreach ($dataArray as $field => $value) {
    //         foreach ($value as $k => $val) {
    //             $list[$k][$field] = $val;
    //         }
    //     }

    //     if(empty($dataArray) || count($dataArray['noDoc']) < 1 || count($dataArray['totalCargo']) < 1) {
    //         $responsearray = [];
    //         $response = new stdClass;
    //         $response->Code = '204';
    //         $response->Msg = 'No Dokumen atau Total Cargo tidak boleh kosong!';
    //         $responsearray['success'] = 1;
    //         $responsearray['response'] = $response;
    //         // $response = [
    //         //     'success' => 'Failed',
    //         //     'message' => 'No Dokumen atau Total Cargo tidak boleh kosong!',
    //         // ];
            
    //         echo json_encode($responsearray);
    //         die();
    //     } else {
    //         $output = $mod->uploadBulk($list);
    //         echo json_encode($output);
    //     }
    // }
}