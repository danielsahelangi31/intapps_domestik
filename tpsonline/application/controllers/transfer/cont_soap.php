<?php
class Cont_Soap extends CI_Controller{
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
	
	/** 
	 * Index
	 */
	public function index(){
		redirect('transfer/cont_soap/request_data');
	}
	
	public function request_data(){
		$num_args = func_num_args();
		$get_args = func_get_args();
		$info = $this->input->post('VISIT_ID');

		// Load Model & Parsing Parameter untuk sorting, searching dan paging
		$mod = model('transfer/soap_model');
		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data
		$res = $mod->get_data($info);
		$cfg->totalPage	= (int) ceil($res->actualRows / $cfg->rowPerPage);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'datasource' => $res->datasource,
			'success' => $res->success,
			'error_msg' => $res->success ? NULL : $res->msg,
		);
		
		$this->load->view('backend/pages/transfer/transfer/request', $data);
	}

	}
?>