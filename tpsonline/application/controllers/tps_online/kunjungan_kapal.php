<?php
class Kunjungan_Kapal extends CI_Controller{
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
		redirect('tps_online/kunjungan_kapal/listview');
	}
	
	
	/** 
	 * Listview
	 * Halaman utama modul delivery request, menampilkan daftar delivery request yang sudah pernah
	 * dilakukan dan sebagai launcher untuk membuat delivery request baru ataupun tindakan-tindakan
	 * lain terhadap delivery request yang sudah dilakukan.
	 */
	public function listview(){	
		$num_args = func_num_args();
		$get_args = func_get_args();

		// Load Model & Parsing Parameter untuk sorting, searching dan paging
		$mod = model('tps_online/kunjungan_kapal_model');
		
		$mod->set_db($this->get_db());
		
		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data
		$res = $mod->select($this->auth->id);
		$cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource,
		);

		$this->load->view('backend/pages/tps_online/kunjungan_kapal/listview', $data);
	}
	
	public function view($id = NULL){
		$num_args = func_num_args();
		$get_args = func_get_args();
		
		$grid_state = '';
		for($i = 1; $i < $num_args; $i++){
			$grid_state .= $get_args[$i].'/';
		}
		
		if(!$grid_state){
			$grid_state = 'tps_online/kunjungan_kapal/listview';
		}
	
		$db = $this->get_db();
		
		$mod = model('tps_online/kunjungan_kapal_model');
		$mod->set_db($db);
		
		$view = array(
			'grid_state' => $grid_state
		);
		
		if($row = $mod->get($id)){
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('INWARD_BC11', 'Nomor Inward BC 1.1', 'required');
				$val->set_rules('INWARD_BC11_DATE', 'Tanggal Inward BC 1.1', 'required');
				$val->set_rules('OUTWARD_BC11', 'Nomor Outward BC 1.1', 'required');
				$val->set_rules('OUTWARD_BC11_DATE', 'Tanggal Outward BC 1.1', 'required');
				$val->set_rules('LOAD_PORT', 'Load Port', 'required');
				$val->set_rules('TRANSIT_PORT', 'Transit Port', 'required');
				$val->set_rules('DISCHARGER_PORT', 'Discharge Port', 'required');
				//$val->set_rules('NEXT_PORT', 'Next Port', 'required');
				
				if($val->run()){
					$mod->update($id);
					$view['info_msg'] = 'Sukses edit data kunjungan kapal';
				}else{
					$view['error_msg'] = validation_errors();
				}
				
				$row->INWARD_BC11 = post('INWARD_BC11');
				$row->INWARD_BC11_DATE = post('INWARD_BC11_DATE');
				$row->OUTWARD_BC11 = post('OUTWARD_BC11');
				$row->OUTWARD_BC11_DATE = post('OUTWARD_BC11_DATE');
				$row->LOAD_PORT = post('LOAD_PORT');
				$row->TRANSIT_PORT = post('TRANSIT_PORT');
				$row->DISCHARGER_PORT = post('DISCHARGER_PORT');
				//$row->NEXT_PORT = post('NEXT_PORT');
			}
			
			$view['kunjungan'] = $row;
			
			$this->load->view('backend/pages/tps_online/kunjungan_kapal/view', $view);
		}else{
			redirect('tps_online/kunjungan_kapal/listview/404');
		}
	}
	
	public function finalize($id = NULL){
		$db = $this->get_db();
		
		$mod = model('tps_online/kunjungan_kapal_model');
		$kargo = model('tps_online/kargo_model');
		
		$mod->set_db($db);
		$kargo->set_db($db);
		
		$view = array(
			
		);
		
		if($row = $mod->get($id)){
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('INWARD_BC11', 'Nomor Inward BC 1.1', 'required');
				$val->set_rules('INWARD_BC11_DATE', 'Tanggal Inward BC 1.1', 'required');
				$val->set_rules('OUTWARD_BC11', 'Nomor Outward BC 1.1', 'required');
				$val->set_rules('OUTWARD_BC11_DATE', 'Tanggal Outward BC 1.1', 'required');
				$val->set_rules('LOAD_PORT', 'Load Port', 'required');
				$val->set_rules('TRANSIT_PORT', 'Transit Port', 'required');
				$val->set_rules('DISCHARGER_PORT', 'Discharge Port', 'required');
				$val->set_rules('NEXT_PORT', 'Next Port', 'required');
				
				if($val->run()){
					$mod->update($id);
					$view['info_msg'] = 'Sukses edit data kunjungan kapal';
				}else{
					$view['error_msg'] = validation_errors();
				}
				
				$row->INWARD_BC11 = post('INWARD_BC11');
				$row->INWARD_BC11_DATE = post('INWARD_BC11_DATE');
				$row->OUTWARD_BC11 = post('OUTWARD_BC11');
				$row->OUTWARD_BC11_DATE = post('OUTWARD_BC11_DATE');
				$row->LOAD_PORT = post('LOAD_PORT');
				$row->TRANSIT_PORT = post('TRANSIT_PORT');
				$row->DISCHARGER_PORT = post('DISCHARGER_PORT');
				$row->NEXT_PORT = post('NEXT_PORT');
			}
			
			$view['kunjungan'] = $row;
			$view['unsent'] = $kargo->select_unsent($id);
			
			$this->load->view('backend/pages/tps_online/kunjungan_kapal/finalize', $view);
		}else{
			redirect('tps_online/kunjungan_kapal/listview/404');
		}
	}
	
	public function get($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VISIT_ID' => post('VISIT_ID')
			);
			
			$db = $this->get_db();
			
			$data = $db->select('VISIT_ID, VISIT_NAME, ETA, ETD, LOAD_PORT, DISCHARGER_PORT')->where($where)->get('CARTOS_SHIP_VISIT')->row();
			
			if($data){
				$data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
				$data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';
				
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
	
	
	
}