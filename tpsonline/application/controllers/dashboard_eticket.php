<?php

/** Dashboard 
 *	Halaman landing ketika user berhasil login
 *
 */
class Dashboard_Eticket extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			'cargo',
			'rekap_data',
			'log_autogate'
		));

		// 		Dapatkan data login
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
	}

	/** 
	 * Index
	 * Di Halaman ini system akan menampilkan ucapan selamat datang dan jadwal kapal
	 */

	public function chart_rc(){
		$bulan 		 	= $this->input->post('bulan',TRUE);
		$tahun 		 	= $this->input->post('tahun',TRUE);
		$mod 			= model('dashboard');
        $data        	= $mod->chart_rc($bulan, $tahun);
        echo json_encode($data);
    }

    public function report_rc($rc_status, $bulan, $tahun)
	{
		if($rc_status == '1') $ket = 'Request';
		else if($rc_status == '2') $ket = 'Approved';
		else if($rc_status == '3') $ket = 'Reject';
		else $ket = '';
		$view['ket'] 		= $ket;
		$view['rc_status'] 	= $rc_status;
		$view['bulan'] 		= $bulan;
		$view['tahun'] 		= $tahun;

        $this->load->view('backend/pages/report/r_return_cargo', $view);
	}

	 public function report_visit_id($status, $bulan, $tahun)
	{
		if($status == '0') $ket = 'Announce';
		else if($status == '2') $ket = 'Arrived';
		else if($status == '3') $ket = 'Operational';
		else if($status == '4') $ket = 'Completed';
		else if($status == '10') $ket = 'Deleted';
		else $ket = '';
		$view['ket'] 		= $ket;
		$view['status'] 	= $status;
		$view['bulan'] 		= $bulan;
		$view['tahun'] 		= $tahun;

        $this->load->view('backend/pages/report/r_visit_id', $view);
	}

	public function report_eticket($status, $bulan, $tahun)
	{
		$view['status'] 	= $status;
		$view['bulan'] 		= $bulan;
		$view['tahun'] 		= $tahun;

        $this->load->view('backend/pages/report/r_error_eticket', $view);
	}

	public function report_truck($maker, $bulan, $tahun)
	{
		if($maker == 'EVLS') $ket = 'TOYOTA';
		else if($maker == 'ADLES') $ket = 'DAIHATSU';
		else if($maker == 'MMKI') $ket = 'MITSUBISHI';
		else if($maker == 'NSDS') $ket = 'SUZUKI';
		else $ket = 'Other';
		$view['ket'] 		= $ket;
		$view['maker'] 		= $maker;
		$view['bulan'] 		= $bulan;
		$view['tahun'] 		= $tahun;

        $this->load->view('backend/pages/report/r_announce_truck', $view);
	}

	public function report_vin($maker, $bulan, $tahun)
	{
		if($maker == 'EVLS') $ket = 'TOYOTA';
		else if($maker == 'ADLES') $ket = 'DAIHATSU';
		else if($maker == 'MMKI') $ket = 'MITSUBISHI';
		else if($maker == 'NSDS') $ket = 'SUZUKI';
		else $ket = 'Other';
		$view['ket'] 		= $ket;
		$view['maker'] 		= $maker;
		$view['bulan'] 		= $bulan;
		$view['tahun'] 		= $tahun;

        $this->load->view('backend/pages/report/r_announce_vin', $view);
	}

    public function get_rc($rc_status, $bulan, $tahun)
    {
        $mod        = model('report/return_cargo');
        $mod_maker  = model('return_cargo/rc_item_model');
        $list       = $mod->get_datatables($rc_status, $bulan, $tahun);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        $get_maker  = $mod_maker->getMakerAndModel($field->VIN);

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->VIN;
            $row[] = $get_maker[0]->MAKER;
            $row[] = $field->TRUCK_CODE;
            $row[] = $field->CREATED_DT;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $mod->count_all($rc_status, $bulan, $tahun),
            "recordsFiltered" => $mod->count_filtered($rc_status, $bulan, $tahun),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_eticket($status, $bulan, $tahun)
    {
        $mod = model('report/error_eticket');
        $list = $mod->get_datatables($status, $bulan, $tahun);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->LICENCE_PLATE;
            $row[] = $field->SENDER;
            $row[] = $field->RESPON_MSG;
            $row[] = $field->RECORD_TIME;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $mod->count_all($status, $bulan, $tahun),
            "recordsFiltered" => $mod->count_filtered($status, $bulan, $tahun),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_truck($maker, $bulan, $tahun)
    {
        $mod = model('report/announce_truck');
        $list = $mod->get_datatables($maker, $bulan, $tahun);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	if($field->RESPON_CODE == '200') $status = 'Sukses';
        	else $status = 'Gagal';
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->LICENCE_PLATE;
            $row[] = $field->SENDER;
            $row[] = $status;
            $row[] = $field->RESPON_MSG;
            $row[] = $field->RECORD_TIME;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $mod->count_all($maker, $bulan, $tahun),
            "recordsFiltered" => $mod->count_filtered($maker, $bulan, $tahun),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_vin($maker, $bulan, $tahun)
    {
        $mod = model('report/announce_vin');
        $list = $mod->get_datatables($maker, $bulan, $tahun);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	if($field->RESPON_CODE == '200') $status = 'Sukses';
        	else $status = 'Gagal';
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->DOCUMENT_TRANSFERID;
            $row[] = $field->FUEL;
            $row[] = $status;
            $row[] = $field->SENDER;
            $row[] = $field->RECORD_TIME;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $mod->count_all($maker, $bulan, $tahun),
            "recordsFiltered" => $mod->count_filtered($maker, $bulan, $tahun),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_visit_id($status, $bulan, $tahun)
    {
        $mod = model('report/visit_id');
        $list = $mod->get_datatables($status, $bulan, $tahun);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->NR;
            $row[] = $field->CATEGORY3;
            $row[] = $field->TRUCK_ID;
            $row[] = $field->LASTCHANGE;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $mod->count_all($status, $bulan, $tahun),
            "recordsFiltered" => $mod->count_filtered($status, $bulan, $tahun),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function chart_visit_id(){
        $bulan 		 	= $this->input->post('bulan',TRUE);
		$tahun 		 	= $this->input->post('tahun',TRUE);
		$mod 			= model('dashboard');
        $data        	= $mod->chart_visit_id($bulan, $tahun);
        echo json_encode($data);
    }

    public function chart_eticket(){
        $bulan 		 	= $this->input->post('bulan',TRUE);
		$tahun 		 	= $this->input->post('tahun',TRUE);
		$mod 			= model('dashboard');
        $data        	= $mod->chart_eticket($bulan, $tahun);
        echo json_encode($data);
    }

    public function chart_truck(){
        $bulan 		 	= $this->input->post('bulan',TRUE);
		$tahun 		 	= $this->input->post('tahun',TRUE);
		$mod 			= model('dashboard');
        $data        	= $mod->chart_truck($bulan, $tahun);
        echo json_encode($data);
    }

    public function chart_vin(){
        $bulan 		 	= $this->input->post('bulan',TRUE);
		$tahun 		 	= $this->input->post('tahun',TRUE);
		$mod 			= model('dashboard');
        $data        	= $mod->chart_vin($bulan, $tahun);
        echo json_encode($data);
    }

    public function chart_maker($maker, $bulan, $tahun){
		$mod 			= model('report/announce_truck');
        $data        	= $mod->chart_maker($maker, $bulan, $tahun);
        echo json_encode($data);
    }

    public function chart_maker_vin($maker, $bulan, $tahun){
		$mod 			= model('report/announce_vin');
        $data        	= $mod->chart_maker($maker, $bulan, $tahun);
        echo json_encode($data);
    }

	public function index()
	{
		$view 			= array();
		$view['bulan'] 	= ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        $this->load->view('backend/pages/dashboard/index_eticket', $view);
	}

	function passwordUpdate()
	{
		$this->load->library(array('form_validation'));

		$mod = model('usermodel');
		$res = $mod->details($this->auth->id);
		$data = array('datasource' => $res->datasource);

		if (post()) {
			$this->form_validation->set_rules('oldpassword', 'Password Lama', 'required|callback_oldpassword_check');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

			if ($this->form_validation->run() == TRUE) {
				if ($this->usermodel->updatePassword())
					redirect('dashboard_eticket');
			}
		}

		$this->load->view('backend/pages/dashboard/update_password', $data);
	}

	public function oldpassword_check($str)
	{
		$mod = model('usermodel');
		$res = $mod->getPassword($this->auth->id);

		if (md5($str) != $res->password) {
			$this->form_validation->set_message('oldpassword_check', 'Password sekarang masih salah input');
			return FALSE;
		} else
			return TRUE;
	}
}
