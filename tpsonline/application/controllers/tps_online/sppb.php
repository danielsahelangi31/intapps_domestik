<?php
class Sppb extends CI_Controller{
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
		redirect('tps_online/sppb/listview');
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
		$mod = model('tps_online/sppb_model');
		
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

		$this->load->view('backend/pages/tps_online/SPPB/listview', $data);
	}
	
	public function view($id = NULL){
		$num_args = func_num_args();
		$get_args = func_get_args();
		$cat_id = strtolower(post('cat_id'));
		
		$where = array(
				'NO_BL_AWB' => $id
			);
			
		$db = $this->get_db();
		$sppb_no = $db->select('DISTINCT(NO_SPPB)')->where($where)->get('CARTOS_TPS_SPPB_PIB_H')->row();
		$array = get_object_vars($sppb_no);
		$sppb_number = $array['NO_SPPB'];
		
		$grid_state = '';
		for($i = 1; $i < $num_args; $i++){
			$grid_state .= $get_args[$i].'/';
		}
		
		if(!$grid_state){
			$grid_state = 'tps_online/sppb/listview';
		}
	
		$db = $this->get_db();
		$mod = model('tps_online/sppb_model');
		$mod->set_db($db);
		
		$view = array(
			'grid_state' => $grid_state
		);
		
		if($row = $mod->get($id,$sppb_number)){
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('NO_BL_AWB', 'Nomor BL', 'required');
				$val->set_rules('TGL_BL_AWB', 'Tanggal BL', 'required');
				$val->set_rules('NO_SPPB', 'Nomor SPPB', 'required');
				$val->set_rules('TGL_SPPB', 'Tanggal SPPB', 'required');
				$val->set_rules('NPWP_IMP', 'NPWP Importir', 'required');
				$val->set_rules('NAMA_IMP', 'Importir', 'required');
				
				if($val->run()){
					$mod->update($id);
					$view['info_msg'] = 'Sukses Release data SPPB';
				}else{
					$view['error_msg'] = validation_errors();
				}
				
				$row->NO_BL_AWB = post('NO_BL_AWB');
				$row->TGL_BL_AWB = post('TGL_BL_AWB');
				$row->NO_SPPB = post('NO_SPPB');
				$row->TGL_SPPB = post('TGL_SPPB');
				$row->NPWP_IMP = post('NPWP_IMP');
				$row->NAMA_IMP = post('NAMA_IMP');
			}
			
			$view['sppb'] = $row->sppb;
			$view['cargo'] = $row->cargo;
			
			$this->load->view('backend/pages/tps_online/SPPB/view', $view);
		}else{
			redirect('tps_online/sppb/listview/404');
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
		
		$grid_state='';
		
		if(!$grid_state){
			$grid_state = 'tps_online/sppb/listview';
		}
		
		$view=array(
			'grid_state' => $grid_state
		);
	
		if(is_post_request()) 
		{
			
			$car = post('CAR');
			$bl_number = post('NO_BL_AWB');
			$sppb_number = post('NO_SPPB');
			$sppb_number_date = post('TGL_SPPB');
			$sum_cargo = post('JUMLAH_CARGO');
			
			if($bl_number)
			{
				$api = model('tps_online/api/sppb_api');
				
				$result = $api->get_data($car,$bl_number,$sppb_number,$sppb_number_date,$sum_cargo);
					
					if($result->success)
					{
						
						$db = $this->get_db();
						$mod = model('tps_online/sppb_model');
						$mod->set_db($db);
						
						$row = $mod->get($bl_number,$sppb_number);
						
							if($result->response->returnCode == 200)
							{
								$view['success_msg'] = 'Data berhasil RELEASE';
							}
							else
							{
								$view['error_msg'] = 'Data tidak berhasil RELEASE';
							}
							
						$view['sppb'] = $row->sppb;
						$view['cargo'] = $row->cargo;
							
					}
						
				$view['datasource'] = $result->response;
						
			}
			else
			{
				$view['error_msg'] = $result->msg;
						
			}
				
		}
		else
		{
				$view['error_msg'] = 'No BL yang di inginkan Kosong';
			
		}
			
		$this->load->view('backend/pages/tps_online/SPPB/view', $view);
	}
	
}