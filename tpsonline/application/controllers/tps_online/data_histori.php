<?php
class Data_histori extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
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
	 */
	public function index(){
		redirect('tps_online/data_histori/listview');
		// $this->listview();
	}
	
	
	/** 
	 * Listview
	 * Halaman utama modul delivery request, menampilkan daftar delivery request yang sudah pernah
	 * dilakukan dan sebagai launcher untuk membuat delivery request baru ataupun tindakan-tindakan
	 * lain terhadap delivery request yang sudah dilakukan.
	 */
	public function listview(){	
		
		// Layout Data
		$data = array(
			'test' => 'test' 
		);

		$this->load->view('backend/pages/tps_online/data_histori/listview', $data);
	}
	
	public function assign_bl($visit_id = NULL){
		$db = $this->get_db();
		
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);
		
		$data = array(
			'VISIT_ID' => $visit_id,
			'VISIT_ID_DS' => $kunjungan_kapal->select_ds(array('FLAG_SEND' => 0)),
			'TYPE_CARGO_DS' => $kunjungan_kapal->select_type_cargo(array('STATUS' => 'Y'))
		);
		$this->load->view('backend/pages/tps_online/consignment/assign_bl', $data);
	}
	
	public function get_data($token){
		if($this->auth->token == $token){
			$tahun = $_POST['tahun'];
			$db = $this->get_db();
			$this->load->model('tps_online/data_histori_model'); 
			// var_dump($_POST['tahun']);die;
			
			$level1 = $this->data_histori_model->data_level1($tahun);
			
			echo json_encode($level1);	
		}
		else{
			var_dump($_REQUEST);die;
			echo json_encode('INVALID TOKEN');	
		}
		
	}

	
	
	
	
	
	
	
	
}
