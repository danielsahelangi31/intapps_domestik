<?php
/** Dashboard 
  *	Halaman landing ketika user berhasil login
  *
  */
class DashboardReal extends CI_Controller{
	private $local_db;
	public function __construct(){
		parent::__construct();
		$this->load->model(array('cargo',
                              	'rekap_data',
                              	'log_autogate'
                            	));
		
// 		Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}

	
	private function get_db(){
		if(!$this->local_db){
			$this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
			$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
			$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		}
		
		return $this->local_db;
	}

	/** 
	 * Index
	 * Di Halaman ini system akan menampilkan ucapan selamat datang dan jadwal kapal
	 */
	public function index($visit_id = NULL){
		// $db = $this->get_db();
		
		// $kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		// $kunjungan_kapal->set_db($db);
		
		$data = array(
			'VISIT_ID' => $visit_id,
			'VISIT_ID_DS' => $this->Model_dashboard->select_ds(array('FLAG_SEND' => 0)),
			'TYPE_CARGO_DS' => $this->Model_dashboard->select_type_cargo(array('STATUS' => 'Y')),
			'ALL_KAPAL' => $this->Model_dashboard->getAllVisit()
		);

		
		
		$this->load->view('backend/pages/dashboard/dashboard_view', $data);
	}


	//Detail Dasboard
	public function detail_dasboard(){
		
		$data['data'] = $this->Model_dashboard->getDataDetail(); 
		
		// print_r($data);die();
		$this->load->view('backend/pages/dashboard/detail_dasboard',$data);
	}



	public function index_uc($visit_id = NULL){
		// $db = $this->get_db();
		
		// $kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		// $kunjungan_kapal->set_db($db);
		
		$data = array(
			'VISIT_ID' => $visit_id,
			'VISIT_ID_DS' => $this->Model_dashboard->select_ds(array('FLAG_SEND' => 0)),
			'TYPE_CARGO_DS' => $this->Model_dashboard->select_type_cargo(array('STATUS' => 'Y')),
			'ALL_KAPAL' => $this->Model_dashboard->getAllVisit()
		);
		
		$this->load->view('backend/pages/dashboard/dashboard_uc_browser', $data);
	}
	
	public function get($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VISIT_ID' => post('VISIT_ID')
			);
			
			$db = $this->get_db();
			
			$data = $this->Model_dashboard->getVisitKapal($where['VISIT_ID']);
			
			if($data){
				$data->ARRIVAL = $data->ARRIVAL ? date('d-M-Y', strtotime($data->ARRIVAL)) : '-';
				$data->OPERATIONAL = $data->OPERATIONAL ? date('d-M-Y', strtotime($data->OPERATIONAL)) : '-';
				$data->COMPLETION = $data->COMPLETION ? date('d-M-Y', strtotime($data->COMPLETION)) : '-';
				$data->DEPARTURE = $data->DEPARTURE ? date('d-M-Y', strtotime($data->DEPARTURE)) : '-';
				
				$out->success = true;
				$out->datasource = $data;
			}else{
				$out->success = false;
				$out->msg = 'Tidak dapat menemukan Visit ID: '.post('VISIT_ID');
			}
			
			echo json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function get_vin($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VISIT_ID' => post('VISIT_ID')
			);
			
			$db = $this->get_db();
			
			$data = $this->Model_dashboard->getVINEI($where['VISIT_ID']);
			
			if($data){				
				$out->success = true;
				$out->datasource = $data;
			}else{
				$out->success = false;
				$out->msg = 'Tidak dapat menemukan VIN pada Visit ID: '.post('VISIT_ID');
			}
			
			echo json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function get_type($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VISIT_ID' => post('VISIT_ID')
			);
			
			$db = $this->get_db();
			
			$data = $this->Model_dashboard->getTypeVin($where['VISIT_ID']);
			
			if($data){				
				$out->success = true;
				$out->datasource = $data;
			}else{
				$out->success = false;
				$out->msg = 'Tidak dapat menemukan TYPE VIN pada Visit ID: '.post('VISIT_ID');
			}
			
			echo json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function get_npe($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VISIT_ID' => post('VISIT_ID')
			);
			
			$db = $this->get_db();
			
			$data = $this->Model_dashboard->getNPE($where['VISIT_ID']);
			
			if($data){				
				$out->success = true;
				$out->datasource = $data;
			}else{
				$out->success = false;
				$out->msg = 'Tidak dapat menemukan NPE pada Visit ID: '.post('VISIT_ID');
			}
			
			echo json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function get_data_jumlah($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VISIT_ID' => post('VISIT_ID')
			);
			
			$db = $this->get_db();
			
			$data = $this->Model_dashboard->getJumlah($where['VISIT_ID']);
			
			if($data){				
				$out->success = true;
				$out->datasource = $data;
			}else{
				$out->success = false;
				$out->msg = 'Tidak dapat menemukan JUMLAH pada Visit ID: '.post('VISIT_ID');
			}
			
			echo json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function get_data_sum($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VISIT_ID' => post('VISIT_ID')
			);
			
			$db = $this->get_db();
			
			$data = $this->Model_dashboard->getDataSum($where['VISIT_ID']);
			
			if($data){				
				$out->success = true;
				$out->datasource = $data;
			}else{
				$out->success = false;
				$out->msg = 'Tidak dapat menemukan data pada Visit ID: '.post('VISIT_ID');
			}
			
			echo json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}

		// =============dashboard inventory=======================



	//--------- data truk in----------
	public function visit_truk_in()
	{
		$data['truk_in'] = $this->Model_dashboard->get_truk_in();

		$this->load->view('backend/pages/dashboard/view_visit_truk_in',$data);
	}

	public function vin_detail_truk_in($visitID)
	{

		$data['vin'] = $this->Model_dashboard->get_vin_truk_in($visitID);

		$this->load->view('backend/pages/dashboard/view_vin_truk_in',$data);
	}

	public function vin_truk_in()
	{
		$data['vin_ti'] = $this->Model_dashboard->get_vin_announced_ti();

		$this->load->view('backend/pages/dashboard/vin_all_ti',$data);
	}
	// ---------- data truk in ----------






	// ---------- data truk out ----------
	public function visit_truk_out()
	{
		$data['truk_out'] = $this->Model_dashboard->get_truk_out();


		$this->load->view('backend/pages/dashboard/view_visit_truk_out',$data);
	}

	public function vin_detail_truk_out($visitID)
	{
		$data['vin'] = $this->Model_dashboard->get_vin_truk_out($visitID);

		$this->load->view('backend/pages/dashboard/view_vin_truk_out',$data);
	}

	public function vin_truk_out()
	{
		$data['vin_to'] = $this->Model_dashboard->get_vin_announced_to();

		$this->load->view('backend/pages/dashboard/vin_all_to',$data);
	}
	// ----------------data truk out-------------


	// ---------- data terminal in ----------
	public function non_npe_detail()
	{
		$data['non_npe'] = $this->Model_dashboard->get_non_npe();

		$this->load->view('backend/pages/dashboard/view_detail_non_npe',$data);
	}

	public function npe_detail()
	{
		$data['npe'] = $this->Model_dashboard->get_npe();

		$this->load->view('backend/pages/dashboard/view_detail_npe',$data);

	}

	public function jumlah_vin_terminal_in()
	{
		$data['vin_terminal_in'] = $this->Model_dashboard->get_vin_terminal_in();

		$this->load->view('backend/pages/dashboard/view_jumlah_terminal_in',$data);
	}
	// ---------- data terminal in ----------




	// ---------- data terminal out ----------
	public function non_sppb_detail()
	{
		$data['non_sppb'] = $this->Model_dashboard->get_non_sppb();

		$this->load->view('backend/pages/dashboard/view_non_sppb_detail',$data);
	}
	public function sppb_detail()
	{
		$data['sppb'] = $this->Model_dashboard->get_sppb();

		$this->load->view('backend/pages/dashboard/view_sppb_detail',$data);
	}

	public function jumlah_vin_terminal_out()
	{
		$data['vin_terminal_out'] = $this->Model_dashboard->get_vin_terminal_out();

		$this->load->view('backend/pages/dashboard/view_jumlah_terminal_out',$data);
	}
	// ---------- data terminal out ----------

	



	// ---------- data vessel in ----------
	public function visit_vessel_export()
	{
		$data['visit_vessel_export'] = $this->Model_dashboard->get_visit_vessel_export();

		// print_r($data);die();

		$this->load->view('backend/pages/dashboard/view_visit_vessel_export',$data);
	}

	public function vin_detail_vessel_in($visitID)
	{
		$data['vin'] = $this->Model_dashboard->get_vin_vessel_in($visitID);

		$this->load->view('backend/pages/dashboard/view_vin_vessel_in',$data);
	}

	public function vin_vessel_export()
	{
		$data['vin_vessel_export'] = $this->Model_dashboard->get_vin_vessel_export();

		$this->load->view('backend/pages/dashboard/view_vin_vessel_export',$data);
	}
	// ---------- data vessel in ----------




	// ---------- data vessel out ----------
	public function visit_vessel_import()
	{
		$data['visit_vessel_import'] = $this->Model_dashboard->get_visit_vessel_import();


		$this->load->view('backend/pages/dashboard/view_visit_vessel_import',$data);
	}

	public function vin_detail_vessel_out($visitID)
	{
		$data['vin'] = $this->Model_dashboard->get_vin_vessel_out($visitID);

		$this->load->view('backend/pages/dashboard/view_vin_truk_out',$data);
	}

	public function vin_vessel_import()
	{
		$data['vin_vessel_import'] = $this->Model_dashboard->get_vin_vessel_import();

		$this->load->view('backend/pages/dashboard/view_vin_vessel_import',$data);
	}
	// ---------- data vessel out ----------//


}