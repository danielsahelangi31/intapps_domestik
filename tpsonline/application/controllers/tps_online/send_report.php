<?php
class Send_report extends CI_Controller{
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
		redirect('tps_online/send_report/listview');
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
		$mod = model('tps_online/report_model');
		
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

		$this->load->view('backend/pages/tps_online/sending_report/listview', $data);
	}
	
	public function view($id = NULL){
		$num_args = func_num_args();
		$get_args = func_get_args();
		$cat_id = strtolower(post('cat_id'));
		
		$grid_state = '';
		for($i = 1; $i < $num_args; $i++){
			$grid_state .= $get_args[$i].'/';
		}
		
		if(!$grid_state){
			$grid_state = 'tps_online/send_report/listview';
		}
	
		$db = $this->get_db();
		
		$mod = model('tps_online/report_model');
		$mod->set_db($db);
		
		$view = array(
			'grid_state' => $grid_state
		);
		
		if($row = $mod->get($id)){
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('SVC_INSTANCE', 'Method', 'required');
				$val->set_rules('ID_TRX', 'REF Number', 'required');
				$val->set_rules('BL_NUMBER', 'Nomor BL', 'required');
				$val->set_rules('VISIT_ID', 'Visit ID', 'required');
				$val->set_rules('COUNTERS', 'Pengiriman Ke', 'required');
				$val->set_rules('SUM_CARGO', 'Jumlah Kargo', 'required');
				
				if($val->run()){
					$mod->update($id);
					$view['info_msg'] = 'berhasil send data pengiriman';
				}else{
					$view['error_msg'] = validation_errors();
				}
				
				$row->SVC_INSTANCE = post('SVC_INSTANCE');
				$row->ID_TRX = post('ID_TRX');
				$row->BL_NUMBER = post('BL_NUMBER');
				$row->VISIT_ID = post('VISIT_ID');
				$row->COUNTERS = post('COUNTERS');
				$row->SUM_CARGO = post('SUM_CARGO');
			}
			
			$view['log'] = $row->log;
			$view['cargo'] = $row->cargo;
			
			$this->load->view('backend/pages/tps_online/sending_report/view', $view);
		}else{
			redirect('tps_online/sending_report/listview/404');
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
	
	public function getCargoCTOS()
	{
		
		$view=array();
	
		if(is_post_request()) {
			$bl_number = post('NO_BL_AWB');
			
			if($bl_number){
				$api = model('tps_online/api/sppb_api');
				
				$result = $api->get_data($bl_number);
					
					if($result->success){
						$view['datasource'] = $result->response;
						
					}else{
						$view['error_msg'] = $result->msg;
						
					}
				
			}else{
				$view['error_msg'] = 'No BL yang di inginkan Kosong';
			}
		}
		
		$this->load->view('backend/pages/tps_online/SPPB/view', $view);
	}
	
}