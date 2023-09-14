<?php
class Pelanggan extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
	private function get_db(){
		if(!$this->local_db) $this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
		$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		
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
		$mod = model('tps_online/pelanggan_model');
		
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
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/tps_online/pelanggan/listview', $data);
	}
	
	public function add(){
		$db = $this->get_db();
		
		$mod = model('manifest/manifest_model');
		$mod->set_db($db);
		
		$view = array();
		
		if(is_post_request()){
			$parser = model('manifest/parser/excel_parser_manifest');
			$result = $parser->parse('manifest_file');
			
			// Enhance Result
			$auth = $this->auth;
			$result->user_id = $auth->id;
			$result->username = $auth->username;
			$result->cuscar_request_id = uniqid();
			
			if($result->status){
				$thrower = model('manifest/api/edifact_message_hub');
				$throw_result = $thrower->throw_cuscar($result);
				
				if($throw_result->success){
					$result->generated_filename = uniqid();
					move_uploaded_file($_FILES['manifest_file']['tmp_name'], MANIFEST_FILE_STORE_BASE.'/'.$result->generated_filename);
					
					$mod->insert($result);
					
					$view['success_msg'] = 'Sukses kirim file';
				}else{
					$view['error_header'] = 'Kami Sudah Berusaha :(';
					$view['error_msg'] = $throw_result->msg;
				}
			}else{
				if(isset($result->parser_error)){
					if(isset($result->error_header)){
						$view['error_header'] = $result->error_header;
					}
					$view['parser_error'] = $result->parser_error;
				}else{
					$view['error_msg'] = 'Kami harus memastikan datanya memenuhi standar. Harap perbaiki hal-hal yang tercantum dalam rincian.';
				}
			}
			
			$view['result'] = $result;
		}
		
		$this->load->helper('inflector');
		$this->load->view('backend/pages/tps_online/kunjungan_kapal/add', $view);
	}
	
	public function view($id = NULL){
		$num_args = func_num_args();
		$get_args = func_get_args();
		
		$grid_state = '';
		for($i = 1; $i < $num_args; $i++){
			$grid_state .= $get_args[$i].'/';
		}
		
		if(!$grid_state){
			$grid_state = 'tps_online/pelanggan/listview';
		}
	
		$db = $this->get_db();
		
		$mod = model('tps_online/pelanggan_model');
		$mod->set_db($db);
		
		$view = array(
			'grid_state' => $grid_state
		);
		
		if($row = $mod->get($id)){
			$view['pelanggan'] = $row;
			
			$this->load->view('backend/pages/tps_online/pelanggan/view', $view);
		}else{
			redirect('tps_online/pelanggan/listview/404');
		}
	}
	
	public function edit($id = NULL){
		$num_args = func_num_args();
		$get_args = func_get_args();
		
		$grid_state = '';
		for($i = 1; $i < $num_args; $i++){
			$grid_state .= $get_args[$i].'/';
		}
		
		if(!$grid_state){
			$grid_state = 'tps_online/pelanggan/listview';
		}
	
		$db = $this->get_db();
		
		$mod = model('tps_online/pelanggan_model');
		$mod->set_db($db);
		
		$edit = array(
			'grid_state' => $grid_state
		);
		
		if($row = $mod->get($id)){
			$edit['pelanggan'] = $row;
			
			$this->load->view('backend/pages/tps_online/pelanggan/edit', $edit);
		}else{
			redirect('tps_online/pelanggan/listview/404');
		}
	}
	
	public function deletes($id = NULL){
		$num_args = func_num_args();
		$get_args = func_get_args();
		
		$grid_state = '';
		for($i = 1; $i < $num_args; $i++){
			$grid_state .= $get_args[$i].'/';
		}
		
		if(!$grid_state){
			$grid_state = 'tps_online/pelanggan/listview';
		}
	
		$db = $this->get_db();
		
		$mod = model('tps_online/pelanggan_model');
		$mod->set_db($db);
		
		$delete = array(
			'grid_state' => $grid_state
		);
		
		if($row = $mod->get($id)){
			$delete['pelanggan'] = $row;
			
			$this->load->view('backend/pages/tps_online/pelanggan/delete', $delete);
		}else{
			redirect('tps_online/pelanggan/listview/404');
		}
	}
	
	public function hapus($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
			
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('NAMA_PERUSAHAAN', 'Nama Perusahaan', 'required');
				$val->set_rules('NPWP', 'NPWP', 'required');
				
				
				if($val->run()){
					$db = $this->get_db();
					
					$id = post('ID');
					$nama_perusahaan = post('NAMA_PERUSAHAAN');
					$npwp = post ('NPWP');
					
					$upd = array(
								'FLAG_DELETED' => 1
							);
					$db->where('ID', $id)->update('MST_PELANGGAN', $upd);
					
					$db->trans_complete();
					
					if($db->trans_status()){
						$out->success = true;
						$out->msg = 'Berhasil Hapus data perusahaan';
					}else{
						$out->success = false;
						$out->msg = 'Gagal Hapus ke database, tidak ada data yang di update';
					}
				}else{
					$out->success = false;
					$out->msg = validation_errors();
				}		
			}else{
				$out->success = false;
				$out->msg = 'Anda harus menggunakan POST request';
			}
			
			echo @json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function update($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
			
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('NAMA_PERUSAHAAN', 'Nama Perusahaan', 'required');
				$val->set_rules('NPWP', 'NPWP', 'required');
				
				
				if($val->run()){
					$db = $this->get_db();
					
					$id = post('ID');
					$nama_perusahaan = post('NAMA_PERUSAHAAN');
					$npwp = post ('NPWP');
					
					$upd = array(
								'NAMA_PERUSAHAAN' => $nama_perusahaan,
								'NPWP' => $npwp
							);
					$db->where('ID', $id)->update('MST_PELANGGAN', $upd);
					
					$db->trans_complete();
					
					if($db->trans_status()){
						$out->success = true;
						$out->msg = 'Berhasil Update data perusahaan';
					}else{
						$out->success = false;
						$out->msg = 'Gagal update ke database, tidak ada data yang di update';
					}
				}else{
					$out->success = false;
					$out->msg = validation_errors();
				}		
			}else{
				$out->success = false;
				$out->msg = 'Anda harus menggunakan POST request';
			}
			
			echo @json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function simpan($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
			
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('NAMA_PERUSAHAAN', 'Nama Perusahaan', 'required');
				$val->set_rules('NPWP', 'NPWP', 'required');
				
				
				if($val->run()){
					$db = $this->get_db();
					
					$nama_perusahaan = post('NAMA_PERUSAHAAN');
					$npwp = post ('NPWP');
					
					$insrt = array(
								'NAMA_PERUSAHAAN' => $nama_perusahaan,
								'NPWP' => $npwp
							);
					$db->insert('MST_PELANGGAN', $insrt);
					
					$db->trans_complete();
					
					if($db->trans_status()){
						$out->success = true;
						$out->msg = 'Berhasil insert data perusahaan';
					}else{
						$out->success = false;
						$out->msg = 'Gagal input ke database, tidak ada data yang di update';
					}
				}else{
					$out->success = false;
					$out->msg = validation_errors();
				}		
			}else{
				$out->success = false;
				$out->msg = 'Anda harus menggunakan POST request';
			}
			
			echo @json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
}