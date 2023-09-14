<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Laporan extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
	private function get_root_cat($auth){
		// Todo: Add Auth Processing Here
		$root_cat = array(
			'cartos' => 'Car Terminal',
			// 'billing' => 'Billing'
		);
		
		return $root_cat;
	}
	
	private function check_root_cat_privilege($cat_id){
		$root_cat = $this->get_root_cat($this->auth);
		
		return isset($root_cat[$cat_id]);
	}	
	
	public function index(){
		$view = array(
			'root_cat' => $this->get_root_cat($this->auth)
		);
		
		$this->load->view('backend/pages/laporan/switchboard', $view);
	}
	
	public function get_sub_cat($token = NULL){
		$out = new StdClass();
		$cat_id = strtolower(post('cat_id'));
		
		if($this->check_root_cat_privilege($cat_id)){
			$mod = model('report/'.$cat_id);
			
			$out->success = true;
			$out->sub_cat = $mod->get_sub_cat($this->auth);
		}else{
			$out->success = false;
			$out->msg = 'Kategori tidak ada atau anda tidak memiliki hak akses terhadap laporan ini';
		}
		
		echo json_encode($out);
	}
	
	public function get_form($token = NULL){		
		$auth = $this->auth;
		$cat_id = strtolower(post('cat_id'));
		$sub_cat_id = strtolower(post('sub_cat_id'));
		
		if($this->check_root_cat_privilege($cat_id)){
			$mod = model('report/'.$cat_id);
			
			$sub_cat = $mod->get_sub_cat($auth);
			if(isset($sub_cat[$sub_cat_id])){
				$view = array(
					'datasource' => $mod->get_datasource($sub_cat_id, $auth)
				);
				
				$this->load->view('backend/reports/form/'.$cat_id.'/'.$sub_cat_id, $view);
			}else{
				$this->load->view('backend/reports/form/no_rights');
			}
		}else{
			$this->load->view('backend/reports/form/no_rights');
		}
	}
	
	public function execute_form($token = NULL){
		$auth = $this->auth;
		$cat_id = strtolower(post('cat_id'));
		$sub_cat_id = strtolower(post('sub_cat_id'));
		
		if($this->check_root_cat_privilege($cat_id)){
			$mod = model('report/'.$cat_id);
			
			$sub_cat = $mod->get_sub_cat($auth);
			
			if(isset($sub_cat[$sub_cat_id])){
				$result = $mod->{$sub_cat_id}($auth);
								
				if(empty($result->custom_view)){
					// Default View
					$this->load->view('backend/reports/output/default', $result);
				}else{
					//print_r($result);
					//die;
					// Custom View
					$this->load->view('backend/reports/output/'.$result->custom_view, $result);
				}
			}else{
				$this->load->view('backend/reports/output/no_rights');
			}
		}else{
			$this->load->view('backend/reports/output/no_rights');
		}
	}
}

/* End of file laporan.php */
/* Location: ./application/controllers/laporan.php */