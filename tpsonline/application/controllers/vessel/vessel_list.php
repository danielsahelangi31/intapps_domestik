<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*Himakom Jaya selamanya*/
class Vessel_List extends CI_Controller {

    public function __construct(){
        parent::__construct();

        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }
    }

    public function index(){
        $this->load->view('vessel/backend/pages/vessel/vessel_list');
    }

    // public function get_items()
    // {
    //     $mod = model('etickets_list');

    //     $books = $mod->get_datatables($this->userauth->getLoginData()->sender);
    //     $data = array();
    //     $no = $_POST['start'];
    //     foreach($books->result() as $index => $r) {
    //         $identity = 'No Data';
    //         if($r->DESCRIPTION == 'SELFDRIVE'){
    //             $identity = '<a href="javascript:void(0)" onclick="window.open('."'".base_url()."eticket/selfdrive/view_doc_self/".$r->NR."'".","."'"."_blank"."'".')">Download</a>';
    //         }
    //         if($r->VISITSTATUS == '5' && $r->CATEGORY2 == 'IMPORT')
    //             $action = "<a href='" . base_url() . "eticket/update_vin/detail/" . $r->NR . "' type='button' class='btn btn-success'>Detail</a><a href='" . base_url() . "/tps_online/consignment/downloadPdf/" . $r->NR . "' type='button' class='btn btn-primary' style='margin-left: 5px;'>Download</a><a download href='" . base_url() . "/tps_online/consignment/pdfGateOut/" . $r->NR . "' type='button' class='btn btn-primary' style='margin-left: 5px;' title='Download Surat Jalan'><i class='glyphicon glyphicon-download-alt'></i></a>";
    //         else
    //             $action = "<a href='" . base_url() . "eticket/update_vin/detail/" . $r->NR . "' type='button' class='btn btn-success'>Detail</a><a href='" . base_url() . "/tps_online/consignment/downloadPdf/" . $r->NR . "' type='button' class='btn btn-primary' style='margin-left: 5px;'>Download</a>";
    //         $no++;
    //         $row = array();
    //         $row[] = $no;
    //         $row[] = $r->CODE;
    //         $row[] = $r->LICENSEPLATE;
    //         $row[] = $r->DRIVER;
    //         $row[] = $identity;
    //         $row[] = $r->NR;
    //         $row[] = $r->CATEGORY2;
    //         $row[] = $r->CATEGORY3;
    //         $row[] = DateTime::createFromFormat( 'd-M-y h.i.s.u A', $r->LASTCHANGE )->format('d-M-Y h:i A');
    //         $row[] = $action;

    //         $data[] = $row;
    //     }

    //     $output = array(
    //         "draw" => $_POST['draw'],
    //         "recordsTotal" => null,
    //         "recordsFiltered" => intval($mod->count_total($this->userauth->getLoginData()->sender)->result_array[0]["TOTS"]),
    //         "data" => $data
    //     );

    //     echo json_encode($output);
    // }

}
