<?php
class Data_Approval extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
	private function get_db(){
		if(!$this->local_db) $this->local_db = $this->load->database(ILCS_MASTER_REFERENCE_DB, TRUE);
		return $this->local_db;
	}
	
	/** 
	 * Index
	 */
	public function index(){
		redirect('ilcs_master_reference/data_approval/listview');
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
		$mod = model('ilcs_master_reference/master_data_approval');
		$mod->set_db($this->get_db());
		
		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data
		$res = $mod->select($this->auth->freight_forwarder_id);
		$cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/ilcs_master_reference/data_approval/listview', $data);
	}
	
	public function view($id = NULL){
		$db = $this->get_db();
		
		$mod = model('ilcs_master_reference/master_data_approval');
		$mod->set_db($db);
		
		model('ilcs_master_reference/data_model/data_model');
		
		$view = array();
		
		$row = $mod->get($id);
		if($row->success){
			$approval_data = $row->datasource;
		
			$data = new $approval_data->entity;
			$data->get_tree($approval_data->ilcs_id, $db);
			
			$view['approval_data'] = $approval_data;
			$view['data'] = $data;
		}else{
			redirect('ilcs_master_reference/data_approval/listview/404');
		}
		
		$this->load->helper('inflector');
		$this->load->view('backend/pages/ilcs_master_reference/data_approval/view', $view);
	}
	
	public function get_lookup_config(){
		$out = new StdClass();
		model('ilcs_master_reference/data_model/data_model');
		
		$entity = $this->input->post('entity');
		
		// Validate Entity
		if(in_array($entity, Data_Model::$entities)){
			$out->success = true;
			$out->fields = $entity::$lookup_fields;
		}else{
			$out->success = false;
			$out->msg = 'Invalid Entities';
		}
		
		echo json_encode($out);
	}
	
	public function lookup_query(){
		$out = new StdClass();
		
		$db = $this->get_db();
		model('ilcs_master_reference/data_model/data_model');
		
		$entity = $this->input->post('entity');
		$lookup_field = $this->input->post('lookup_field');
		$lookup_value = $this->input->post('lookup_value');
		
		// Validate Entity
		if(in_array($entity, Data_Model::$entities)){
			Data_Model::prepare_lookup($db, $entity, $param, $fields);
			
			$out->success = true;
			$out->datasource = Data_Model::lookup();
		}else{
			$out->success = false;
			$out->msg = 'Invalid Entities';
		}
		
		echo json_encode($out);
	}
	
	
	
	
	
	public function test_prop(){
		model('ilcs_master_reference/data_model/data_model');
		$entity = 'vessel';
		var_dump($entity::$lookup_fields);
	}	
	
	
	
	public function test_model(){
		$db = $this->load->database('ilcs_master_reference', TRUE);
		$mod = model('ilcs_master_reference/master_data_approval');
		$mod->set_db($db);
		$mod->db->limit(4);
		var_dump($mod->get_brekele());
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function view_test(){
		$data = array(
			'obj' => array(
				'bujug' => 'alskgjdsla',
				'buneng' => 'dslgkjdsalg',
				'two' => array(
					'kampret' => 'oke',	
					'three' => array(
						'weleh' => 'adlkgjdsa',
						'four' => array(
							'walaah' => 'sldkgjds',
							'widih' => 'sdlkgjdsg',
						),
						'sip' => 'dslkgjsadkl',
						
					),
					'brekele' => 'dsigods',
				)
			)
		);
		
		$data = json_decode(json_encode($data));
		
		var_dump($data);
		
		$this->load->view('research/one', $data);
	}
	
	public function test(){
		$db = $this->load->database('ilcs_master_reference', TRUE);
		$mod = model('ilcs_master_reference/data_model/data_model');
		
		$test = new port();
		
		$test->get_tree(1, $db);
		
		var_dump($test);
	}
	
}