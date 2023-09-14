<?php
class Manual_sppb extends CI_Controller{
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

		$db = $this->get_db();
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);
		$visitID = $kunjungan_kapal->select_ds(array('FLAG_SEND' => 0));
		// $blauto = $kunjungan_kapal->select_all_bl();
		// var_dump($blauto);die;
		foreach ($visitID as $row) {
			$dataAuto[] = $row->VISIT_ID.' '.$row->VISIT_NAME;
		}
		// foreach ($blauto as $row) {
		// 	$bl[] = $row->BL_NUMBER;
		// }
		
		$data = array(
			"visitAuto" => $dataAuto

		);
		$this->load->view('backend/pages/tps_online/SPPB/manual_input_sppb',$data);
	}

	public function getBL(){

		$db = $this->get_db();
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);
		
		$blauto = $kunjungan_kapal->select_all_bl($_REQUEST['term']);
		foreach ($blauto as $row) {
			$bl[] = $row->BL_NUMBER;
		}

		
		echo json_encode($bl);
	}

	public function autoFill(){
		$db = $this->get_db();
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);

		if ($_POST['type'] === 'visit') {
			$a = explode(' ', $_POST['vis'],2);
			$visit_id = $a[0]; // get visit name
			$autofill_bl = $kunjungan_kapal->autofill_from_vis($visit_id);
			
			echo json_encode($autofill_bl);
		}
		else {
			$autofill = $kunjungan_kapal->autofill_from_bl($_POST['bl']);
			
			echo json_encode($autofill);	
		}
		
	}

	public function simpan($token){
		if ($this->auth->token == $token) {
			// no_sppb
			// date_sppb
			// no_bl
			// date_bl
			// no_bc11
			// date_bc11
			// no_pos_bc11
			// npwpCon
			// namaCon
			// merk_kemasan
			// jumlah_kemasan
			// jenis_kemasan
			// visit_id
			$data = new StdClass();
			$db = $this->get_db();
			$a = explode(' ', post('visit_id'),2);
			$visit_id = $a[1]; // get visit name
			
			$car = $db->query('SELECT CAR_SPPB_SEQ.NEXTVAL as CAR FROM DUAL')->row();
			$tgl_proses = $db->query("select TO_CHAR(SYSDATE,'DD-MON-YY hh24:mi:ss') as TGL from dual")->row();
			// var_dump($tgl_proses);die;
			$insrt_header = array(
				'CAR' => $car->CAR,
				'NO_SPPB' => post('no_sppb'),
				'TGL_SPPB' => date('m/d/Y',strtotime(post('date_sppb'))),

				'NO_BL_AWB' => post('no_bl'),
				'TGL_BL_AWB' => date('m/d/Y',strtotime(post('date_bl'))),
				'NO_BC11' => post('no_bc11'),
				'TGL_BC11' => date('m/d/Y',strtotime(post('date_bc11'))),
				'NO_POS_BC11' => post('no_pos_bc11'),
				'NPWP_IMP' => post('npwpCon'),
				'NAMA_IMP' => post('namaCon'),
				'NM_ANGKUT' => $visit_id
				
			);
			$insrt_detail = array(
				'CAR' => $car->CAR,
				'JNS_KMS' => post('jenis_kemasan'),
				'MERK_KMS' => post('merk_kemasan'),

				'JML_KMS' => post('jumlah_kemasan')
			);

			$db->trans_start();

			$db->insert('CARTOS_TPS_SPPB_PIB_H', $insrt_header);

			$db->insert('CARTOS_TPS_SPPB_PIB_DK', $insrt_detail);

			$db->trans_complete();

			if($db->trans_status()){
				$data->success = true;
				$data->msg = 'Berhasil insert data ';
			}else{
				$data->success = false;
				$data->msg = 'Gagal input ke database, tidak ada data yang di update';
			}
			echo json_encode($data);
		}
		

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