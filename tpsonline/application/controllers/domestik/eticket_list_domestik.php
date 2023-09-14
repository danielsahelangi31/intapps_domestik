<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*Himakom Jaya selamanya*/
class Eticket_List_Domestik extends CI_Controller {

    public function __construct(){
        parent::__construct();

        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }
    }

    public function index(){
        $this->load->view('domestik/backend/pages/eticket/eticket_list_domestik');
    }

    public function getData()
    {
        // echo "ini controller get data"; exit();
        $mod = model('domestik/etickets_domestik');
        $books = $mod->getData();
        $data = array();
        $no = $_POST['start'];
        foreach($books->result() as $index => $r) 
        {
            $db_car = $this->load->database('integrasi_cardom_dev', TRUE);
            
            $suratJalan = $db_car->query("SELECT SURAT_JALAN_PATH FROM M_TRUCK mt WHERE TID = '".$r->TRUCK_VISIT_ID_NEW."'")->result();
            
            $buttonDelete = "";

            if($r->STATUS_GATE == "") {
                $buttonDelete = "<a href='" . base_url() . "domestik/eticket_list_domestik/hapusRow/" . $r->TRUCK_VISIT_ID_NEW . "' class='btn btn-danger' >Hapus</a>";
            }
            
            if($r->DIRECTION_NEW == "RETURN CARGO"){             
                $action = "<a href='" . base_url() . "domestik/update_vin_domestik/detail/" . $r->TRUCK_VISIT_ID_NEW . "' type='button' class='btn btn-success'>Detail</a>
                <a download type='button' href='" . base_url() . "domestik/eticket_list_domestik/print_rc/" . $r->TRUCK_VISIT_ID_NEW . "/" . $r->TRUCK_CODE_NEW .  "' type='button' class='btn btn-primary'>Download</a>
                <a download href='".site_url($suratJalan[0]->SURAT_JALAN_PATH)."' type='button' class='btn btn-primary' style='margin-left: 5px;' title='Download Surat Jalan'><i class='glyphicon glyphicon-download-alt'></i></a>
                ".$buttonDelete;
                                
            } else {
                $action = "<a href='" . base_url() . "domestik/update_vin_domestik/detail/" . $r->TRUCK_VISIT_ID_NEW . "' type='button' class='btn btn-success'>Detail</a>
                <a download type='button' href='" . base_url() . "domestik/eticket_list_domestik/print_rc/" . $r->TRUCK_VISIT_ID_NEW . "/" . $r->TRUCK_CODE_NEW . "' type='button' class='btn btn-primary'>Download</a>
                ".$buttonDelete;     
            }
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $r->TRUCK_CODE_NEW;
            $row[] = $r->LICENSE_PLATE_NEW;
            $row[] = $r->TRUCK_VISIT_ID_NEW;
            $row[] = $r->DIRECTION_NEW;
            if($r->TRUCK_CODE_NEW == "SELFDRIVE") 
            {
                $queryBrand = "SELECT BRAND FROM CAR_LIST_CAR WHERE DOC_TRANSFERID = (SELECT DOC_TRANSFERID FROM TR_ASSOSIATION WHERE TRX = '".$r->TRUCK_VISIT_ID_NEW."')";
                $brand = $db_car->query($queryBrand)->result(); 
                $row[] = $brand[0]->BRAND;
            } else {
                $db_ilcsCartos = $this->load->database('ilcs_cartos', TRUE);
                $queryMerk = "SELECT MERK FROM STID_MST_TRUCK WHERE CARTOS_TRUCKCODE = '".$r->TRUCK_CODE_NEW."'";
                $merk = $db_ilcsCartos->query($queryMerk)->result();
                $row[] = $merk[0]->MERK;
            }
            $time = getDate(strtotime($r->LAST_ACTIVITY_NEW));
            $row[] = $r->LAST_ACTIVITY_NEW." ".$r->LAST_TIME_ACTIVITY_NEW;
            $row[] = $action;
            $data[] = $row;
        }

        // var_dump($books); exit();

        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => null,
            "recordsFiltered" =>'', //intval($mod->count_total($this->userauth->getLoginData()->sender)->result_array[0]["TOTS"]),
            "data" => $data
        ];

        echo json_encode($output);
    }

    public function print_rc($truck_visit_id,$truckCode){
        $this->load->library('M_pdf');

        $mods = model('domestik/rc_model_domestik');
        $res = $mods->get_rc_print($truck_visit_id);
        $stid = $mods->get_stid_number($truckCode);
        
        if($res){
                    $data = array(
                        'datas' => $res,
                        'datass' => $stid,
                    );
                 
            ini_set('memory_limit', '256M');
            $html = $this->load->view('domestik/backend/pages/eticket/announce/eticket_print', $data, true);
            $this->m_pdf->pdf->WriteHTML($html);
            $output = $truck_visit_id. '.pdf';
            $this->m_pdf->pdf->Output($output, "I");
        }
    }

    public function pdfGateOut($visitID)
	{
		$this->load->library('M_pdf');

		ini_set('memory_limit', '256M');
		$html = $this->load->view('domestik/backend/reports/getPdf/gate_out_domestik', $visitID, true);
        
		$this->m_pdf->pdf->WriteHTML($html);
		$output = $visitID. '.pdf';
		$this->m_pdf->pdf->Output($output, "I");
	}

    public function hapusRow($trx) 
    {
        $mod = model('domestik/etickets_domestik'); 
        $response = $mod->hapusRow($trx);
        echo "<script>alert('Data sudah terhapus'); window.location.href='".site_url('domestik/eticket_list_domestik')."'</script>";
    }
}
