<?php
class Consignment2 extends CI_Controller{
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
		redirect('tps_online/consignment/listview');
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
		$mod = model('tps_online/kargo_model');
		
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

		$this->load->view('backend/pages/tps_online/kargo/listview', $data);
	}
	
	public function assign_bl($visit_id = NULL){
		$db = $this->get_db();
		
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);
		
		$data = array(
			'VISIT_ID' => $visit_id,
			'VISIT_ID_DS' => $kunjungan_kapal->select_ds(array('FLAG_SEND' => 0))
		);
		$this->load->view('backend/pages/tps_online/consignment/assign_bl', $data);
	}
	
	public function get_bulk_vin($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
			
			$VISIT_ID = post('VISIT_ID');
			$VIN = post('VIN');
			
			$vins = array_unique(explode(" ", str_replace("\n", ' ', $VIN)));
			
			$db = $this->get_db();
			$data = $db	->select('VIN, VISIT_ID, MODEL_NAME, MAKE_NAME, DIRECTION')
						->where_in('VIN', $vins)->get('CARTOS_CARGO')->result();
			
			if($data){
				$errors = array();
				$founded = array();
				$datasource = array();
				
				foreach($data as $row){
					$founded[] = $row->VIN;
					
					if($row->VISIT_ID == $VISIT_ID || $row->VISIT_ID == NULL){
						$datasource[] = $row;
					}else{
						$errors[] = 'VIN '.$row->VIN.' tidak ada dalam VISIT_ID '.$VISIT_ID.' silakan cek kembali';
					}
				}
				
				if(count($vins) == count($datasource)){
					$out->success = true;
					$out->datasource = $datasource;
				}else{
					foreach($vins as $vin){
						if(!in_array($vin, $founded)){
							$errors[] = 'VIN '.$row->VIN.' tidak ditemukan dalam database';
						}
					}
				
					$out->success = false;
					$out->errors = $errors;
					$out->datasource = $datasource;
				}
			}else{
				$out->success = false;
				$out->errors = array('Tidak ada satupun VIN yang ditemukan. Cek kembali VISIT ID dan VIN yang anda masukkan.');
			}
			
			echo @json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function get_vin($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
			
			$VISIT_ID = post('VISIT_ID');
			$VIN = post('VIN');
			
			$where = array(
				'VIN' => substr($VIN, 0, 128)
			);
			
			$db = $this->get_db();
			$data = $db	->select('VIN, VISIT_ID, MODEL_NAME, MAKE_NAME, DIRECTION')
						->where($where)
						->get('CARTOS_CARGO')->row();
			if($data){
				if($data->VISIT_ID == NULL || $data->VISIT_ID == $VISIT_ID){			
					$out->success = true;
					$out->datasource = $data;
				}else{
					$out->success = false;
					$out->msg = 'VIN '.$VIN.' tidak ada dalam VISIT_ID '.$VISIT_ID.' silakan cek kembali';
				}
			}else{
				$out->success = false;
				$out->msg = 'Data VIN yang anda cari tidak ada. Cek VISIT ID dan VIN yang anda masukkan.';
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
				$val->set_rules('VISIT_ID', 'Visit ID', 'required');
				$val->set_rules('BL_NUMBER', 'Nomor BL', 'required');
				$val->set_rules('BL_NUMBER_DATE', 'Tanggal BL', 'required');
				$val->set_rules('VIN[]', 'VIN', 'required');
				
				if($val->run()){
					$db = $this->get_db();
					
					$vin = post('VIN');
					$visit = post('VISIT_ID');
					
					if(is_array($vin)){
						
						$upd = array(
							'BL_NUMBER' => post('BL_NUMBER'),
							'BL_NUMBER_DATE' => date('Y-m-d', strtotime(post('BL_NUMBER_DATE'))),
							'DTS_SET_CONSIGNMENT' => date('Y-m-d H:i:s')
							//'VISIT_ID' => post('VISIT_ID')
						);
						
						$db->trans_start();
						
						foreach($vin as $item){
							if($row = $db->where('VIN', $item)->get('CARTOS_CARGO')->row()){
								if($row->VISIT_ID == post('VISIT_ID') || $row->VISIT_ID == NULL || $row->VISIT_ID == ''){
									$db->where('VIN', $item)->update('CARTOS_CARGO', $upd);
								}
							}
						}
						
						$db->trans_complete();
						
						if($db->trans_status()){
							$out->success = true;
							$out->msg = 'Berhasil update data BL';
						}else{
							$out->success = false;
							$out->msg = 'Gagal input ke database, tidak ada data yang di update';
						}
					}else{
						$out->success = false;
						$out->msg = 'VIN harus berupa array';
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