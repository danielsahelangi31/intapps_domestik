<?php
/** Delivery Request Ocean Going 
  *	Modul untuk Freight Forwarder untuk melakukan kegiatan delivery ocean going
  *
  */
class Trucking extends CI_Controller{
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
		redirect('trucking/listview');
	}
	
	/** 
	 * Listview
	 * Halaman utama modul trucking request, menampilkan daftar trucking request yang sudah pernah
	 * dilakukan dan sebagai launcher untuk membuat trucking request baru ataupun tindakan-tindakan
	 * lain terhadap trucking request yang sudah dilakukan.
	 */
	public function listview(){
		$num_args = func_num_args();
		$get_args = func_get_args();

		// Load Model & Parsing Parameter untuk sorting, searching dan paging
		$mod = model('Delivery_Truck_Assignment_Model');
		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data
		$res = $mod->select($this->auth->trucking_company_id);
		$cfg->totalPage	= (int) ceil($res->actualRows / $cfg->rowPerPage);
		$cfg->totalPage = 1;

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/trucking/listview', $data);
	}

	/** 
	 * Pilih Supir.
	 */
	public function driver_assign($id = NULL){
		$auth = $this->auth;
		
		if($id){
			$mod = model('delivery_truck_assignment_model');
			$trucking_request = $mod->get($id, $auth);
			
			$data = array(
				'trucking_request' => $trucking_request,
			);
			
			if($trucking_request){
				if(is_post_request()){
					$this->load->library('form_validation');
					
					$this->form_validation->set_rules('nama_supir', 'Nama Supir', 'required');
					$this->form_validation->set_rules('truck_id', 'Truck ID / Plat Nomor', 'required');
					$this->form_validation->set_rules('hp_supir', 'Nomor HP', 'required');
					
					$out = $mod->assign_truck($id, $trucking_request, $auth);
					
					if($out->success){
						$data['info_msg'] = 'Berhasil set truck';
					}else{
						$data['error_msg'] = $out->msg;
					}
				}
				
				$this->load->view('backend/pages/trucking/assign_driver', $data);
			}else{
				redirect('trucking/listview');
			}
		}else{
			redirect('trucking/listview');
		}
	}

	/** 
	 * Reset Supir
	 */
	public function driver_reset(){
		$data = array();
		$this->load->view('backend/pages/trucking/reset_driver', $data);
	}

}