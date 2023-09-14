<?php
class Notifikasi extends CI_Controller{
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
		redirect('tps_online/notifikasi/listview');
	}

	public function listview(){	
		
		$this->load->view('backend/pages/tps_online/notifikasi/listview');
	}

	public function get_data($token){
		if($this->auth->token == $token){
			$db = $this->get_db();
			$this->load->model('tps_online/data_histori_model'); 
			// var_dump($_POST['tahun']);die;
			
			$level1 = $this->data_histori_model->data_level1($_POST['tahun'],$_POST['bulan']);
			
			echo json_encode($level1);	
		}
		else{
			var_dump($_REQUEST);die;
			echo json_encode('INVALID TOKEN');	
		}
		
	}

	public function summary_detail($visit_id)
    {
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/internasional_outbound/listview';
        }

        $where = array(
            'VISIT_ID' => $visit_id
        );

        $db = $this->get_db();

        $data = $db->select('VISIT_ID, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, ETA, ETD, LOAD_PORT, DISCHARGER_PORT, VESSEL_STATUS')->where($where)->get('CARTOS_SHIP_VISIT')->row();

        if ($data) {
            $data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
            $data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';

            $mod = model('tps_online/internasional_outbound_model');

            $mod->set_db($db);
            $result = $mod->getSummaryDetail($visit_id);

            $cargo = array();
            if(count($result) > 0) {
                foreach($result as $obj) {
                    $cargo[$obj->BL_NUMBER]['databl'] = array(
                        'BL_NUMBER' => $obj->BL_NUMBER,
                        'BL_NUMBER_DATE' => $obj->BL_NUMBER_DATE,
                        'MASTER_BL_NUMBER' => $obj->MASTER_BL_NUMBER,
                        'MASTER_BL_NUMBER_DATE' => $obj->MASTER_BL_NUMBER_DATE,
                        'CUSTOMS_NUMBER' => $obj->CUSTOMS_NUMBER
                    );

                    $cargo[$obj->BL_NUMBER]['datakargo'][] = $obj;
                }
            }

            // print_r($cargo);die;
            $params['cargo'] = $cargo;
            $params['grid_state'] = $grid_state;
            $params['data'] = $data;
            $this->load->view('backend/pages/tps_online/internasional_outbound/summary_detail', $params);
        } else {
            redirect('tps_online/internasional_outbound/listview/404');
        }
    }

	public function view($visit_id){
		 $num_args = func_num_args();
        $get_args = func_get_args();
		
		$mod = model('tps_online/notifikasi_model');

		$mod->set_db($this->get_db());

		$cfg = $mod->parseParameter($num_args, $get_args);
		

		// Apply Config
		$mod->terapkanConfig($cfg);
		$post = $this->input->post();
		//print_r($this->input->post());
		// Content Data
		$res = $mod->select_by_visit($this->auth->id,$visit_id);

		$cfg->totalPage = (int) ceil(count($res) / $cfg->rowPerPage);
		// $cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);
		//echo @$post['month'];
		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource,
			'bulan' => @$post['month'],
			'year' => @$post['year']
		);

		$this->load->view('backend/pages/tps_online/notifikasi/view_by_visit', $data);
	}
	public function list_bl($visit_id){
		$this->db = $this->get_db();
		
	/*	$this->db->where('VISIT_ID',$visit_id);
		$this->db->select("BL_NUMBER,COUNT(VIN) as JML_VIN,sum(case when type_cargo = 'CBU' then 1 else 0 end) JML_CAR,
		sum(case when type_cargo <> 'CBU' and type_cargo is not null then 1 else 0 end) JML_KMS");
		$this->db->select("sum(nvl(is_valid_car, '0')) JML_VALID_CAR");
		$this->db->select("sum(nvl(is_valid_kms, '0')) JML_VALID_KMS");
		$this->db->group_by('BL_NUMBER');
		$list = $this->db->get('CARTOS_CARGO')->result();*/
		$sql = "select BL_NUMBER, count(*) JML_VIN, sum(case when type_cargo = 'CBU' then 1 else 0 end) JML_CAR, sum(case when type_cargo is not null then 1 else 0 end) JML_KMS, sum(nvl(is_valid_car,0)) JML_VALID_CAR, sum(nvl(is_valid_kms,0)) JML_VALID_KMS from cartos_cargo where DIRECTION = 1 AND VISIT_ID = '$visit_id' group by BL_NUMBER";

		$query = $this->db->query($sql);
		$list = $query->result();
		foreach($list as $k=>$v){
			/*$this->db->where('BL_NUMBER',$v->BL_NUMBER);
			$this->db->where('IS_VALID_CAR',1);
			$validCAR = $this->db->get('CARTOS_CARGO')->result();
			$list[$k]->VALID_CAR = count($validCAR);
			
			$this->db->where('BL_NUMBER',$v->BL_NUMBER);
			$this->db->where('IS_VALID_KMS',1);
			$validKMS = $this->db->get('CARTOS_CARGO')->result();
			$list[$k]->VALID_KMS = count($validKMS);*/
			$car_ready = 0;
			$kms_ready = 0;
			//echo 'CAR = '.$v->JML_VALID_CAR.' - '.$v->JML_CAR;
			if($v->JML_VALID_CAR==$v->JML_CAR){
				$car_ready = 1;
			}
			//echo $car_ready .' | ';
			//echo 'KMS = '.$v->JML_VALID_KMS.' - '.$v->JML_KMS;
			if($v->JML_VALID_KMS==$v->JML_KMS){
				$kms_ready = 1;
			}
			//echo $kms_ready .'<br>';
			$ready = $car_ready + $kms_ready;
			//echo $ready;
			$list[$k]->IS_READY = $ready;
		}
		$data['list'] = $list;
		$data['visitID'] = $visit_id;
		$this->load->view('backend/pages/tps_online/notifikasi/list_bl', $data);
	}
	public function detail($vinID){
		 $num_args = func_num_args();
        $get_args = func_get_args();
		$mod = model('tps_online/notifikasi_model');

		$mod->set_db($this->get_db());

		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);
		$post = $this->input->post();
		//print_r($this->input->post());
		// Content Data
		$res = $mod->select_by_vin($this->auth->id,$vinID);
		// $cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);
		//echo @$post['month'];
		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource,
			'bulan' => @$post['month'],
			'year' => @$post['year']
		);

		$this->load->view('backend/pages/tps_online/notifikasi/view_by_visit', $data);
	}
	public function bl_detail($idBL,$visit_id,$type){
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/notifikasi_model');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // Content Data
        $res = $mod->selectHistory($this->auth->id,$idBL,$visit_id,$type);
        $cfg->totalPage = (int) ceil(count($res) / $cfg->rowPerPage);

        //echo $res->actualRows;
        //print_r ($res);
        //die;
        // Layout Data
        $data = array(
            'history' => true,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datasource' => $res->datasource
        );
        // print_r($data);die;
        $this->load->view('backend/pages/tps_online/notifikasi/detail_bl', $data);
	}
	public function bl_details($idBL){
		 $num_args = func_num_args();
        $get_args = func_get_args();
		$mod = model('tps_online/notifikasi_model');

		$mod->set_db($this->get_db());

		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);
		$post = $this->input->post();
		//print_r($this->input->post());
		// Content Data
		$res = $mod->select_by_bl($this->auth->id,$idBL);
		// $cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);
		//echo @$post['month'];
		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource,
			'bulan' => @$post['month'],
			'year' => @$post['year']
		);

		$this->load->view('backend/pages/tps_online/notifikasi/view_by_visit', $data);
	
	}
	public function view_by_vin($id = NULL,$blnumber) {
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
			$base = site_url();
            $grid_state = str_replace($base,'',$_SERVER['HTTP_REFERER']);
        }

        $db = $this->get_db();

        $mod = model('tps_online/kargolist_internasional_inbound_model');
        $mod->set_db($db);

        $kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );
        
        if ($row = $mod->get_($id,$blnumber)) {
        	
            $view['kargo'] = $row;
			$view['id_bl'] = $id;
			$view['blnumber'] = $blnumber;
			$type_cargo = $kunjungan_kapal->select_type_cargo(array('STATUS' => 'Y'));
			$view['TYPE_CARGO_DS'] = $type_cargo;

            $this->load->view('backend/pages/tps_online/notifikasi/view_bl', $view);
        } else {
            redirect('tps_online/kargolist_internasional_inbound/listview/404');
        }
    }
	public function edit_bl($id = NULL,$blnumber) {
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
			$base = site_url();
            $grid_state = str_replace($base,'',$_SERVER['HTTP_REFERER']);
        }

        $db = $this->get_db();

        $mod = model('tps_online/kargolist_internasional_inbound_model');
        $mod->set_db($db);
		
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);
		
		

        $view = array(
            'grid_state' => $grid_state
        );
		if($this->input->post()){
			$post = $this->input->post();
			
			$this->db = $this->get_db();
			$this->db->where('VIN',$id);
			$this->db->update('CARTOS_CARGO',$post);
			
			redirect($grid_state);
		}
        if ($row = $mod->get_($id,$blnumber)) {
            $view['kargo'] = $row;
			$view['id_bl'] = $id;
			$type_cargo = $kunjungan_kapal->select_type_cargo(array('STATUS' => 'Y'));
			$view['TYPE_CARGO_DS'] = $type_cargo;

            $this->load->view('backend/pages/tps_online/notifikasi/edit_bl', $view);
        } else {
            redirect('tps_online/kargolist_internasional_inbound/listview/404');
        }
    }

    public function updateNow(){
    	$output = array();
    	$this->db = $this->get_db();
    	$where = array(
    		'VISIT_ID' => $_POST['visit_id'],
    		'VIN' => $_POST['vin']
    	);
    	$this->db->trans_start();
    	$this->db->set($_POST['name'], $_POST['val']);
		$this->db->where($where);
		$this->db->update('CARTOS_CARGO'); 
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
		    $this->db->trans_rollback();
		    $output = array(
		    	'status' => false,
		    	'message' => 'Gagal Update data'
		    );
		    echo json_encode($output);
		} 
		else {
		    $this->db->trans_commit();
		    $output = array(
		    	'status' => true,
		    	'message' => 'sukses update data',
		    	'data_loc' =>$_POST['name'],
		    	'new_data' => $_POST['val']

		    );
		    echo json_encode($output);
		}
    	

    }
	public function input_bl($visit_ID=NULL,$bl_no = NULL){
		$db = $this->get_db();
		$this->db = $this->get_db();

		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);
		
		$datasource = $this->db->select('VISIT_ID, VISIT_NAME, ETA, ETD')->where('ETA IS NOT NULL', NULL, FALSE)->order_by("ETA", "desc")->get('CARTOS_SHIP_VISIT')->result();
		$data['datasource'] = $datasource;	

		$this->db->where("VISIT_ID",$visit_ID);
		$blList = $this->db->get('BL_CARGO')->result();
		$data['blList']= $blList;
		$data['button'] = 'Tambahkan';
		if($bl_no!= NULL){
			$this->db->where("MASTER_BL_NUMBER",$bl_no);
			$detail = $this->db->get('BL_CARGO')->result();
			foreach($detail as $k=>$v){
				$detail[$k]->TGL_BL = date("d-m-Y",strtotime($v->TGL_BL));
			}
			$data['detail'] = $detail;
			$data['button'] = 'Update';
		}
		
		$data['VISIT_ID']=$visit_ID;
		$data['TYPE_CARGO_DS'] = $kunjungan_kapal->select_type_cargo(array('STATUS' => 'Y'));
		if($this->input->post()){
			$post = $this->input->post();
			$post['TGL_BL'] = $this->date_render($post['TGL_BL']);
			
			//print_r($post);
			if($bl_no==NULL){
				$post['CREATED_DATE'] = date("Y-m-d H:i:s");
				$this->db->insert('BL_CARGO',$post);
			}
			else{
				$this->db->where("MASTER_BL_NUMBER",$bl_no);
				$this->db->update('BL_CARGO',$post);
			}
			redirect("tps_online/notifikasi/input_bl/".$visit_ID);
		}
		
		$this->load->view('backend/pages/tps_online/consignment/input_bl',$data);
	}
	public function hapus_bl($visit_ID=NULL,$bl_no=NULL){
		$db = $this->get_db();
		$this->db = $this->get_db();
		
		$this->db->where("MASTER_BL_NUMBER",$bl_no);
		$this->db->delete("BL_CARGO");
		
		redirect("tps_online/notifikasi/input_bl/".$visit_ID);
	}
	function date_render($date){
		$tgl = explode("-",$date);
		$tgl_baru = $tgl[2].'-'.$tgl[1].'-'.$tgl[0];
		return $tgl_baru;
	}
	public function lepas_bl($bl_no=NULL,$vin=NULL){
		$db = $this->get_db();
		$this->db = $this->get_db();
		$data=array();
		if($this->input->post()){
			$post = $this->input->post();
			redirect('tps_online/notifikasi/lepas_bl/'.$post['bl_no']);
			/*echo '<pre>';
			print_r($list);
			echo '</pre>';*/
		}
		if($bl_no!=NULL){
			$this->db->where('BL_NUMBER',str_replace("%20"," ",$bl_no));
			$list = $this->db->get('CARTOS_CARGO')->result();
			$data['bl_no'] = str_replace("%20"," ",$bl_no);
			$data['list'] = $list;
		}
		
		if($vin!=NULL){
			echo $vin;
			$this->db->where('VIN',$vin);
			$this->db->update('CARTOS_CARGO',array('BL_NUMBER'=>''));
			redirect('tps_online/notifikasi/lepas_bl/'.$bl_no);
		}
		
		$this->load->view('backend/pages/tps_online/notifikasi/lepas_bl',$data);
	}
	public function kirim_tpsonline(){
		
		$num_args = func_num_args();
		$get_args = func_get_args();
		
		//print_r($get_args);

		// Load Model & Parsing Parameter untuk sorting, searching dan paging
		$mod = model('tps_online/notifikasi_model');

		$mod->set_db($this->get_db());
		
		$cfg = $mod->parseParameterNotifikasi($num_args, $get_args);
		
		// Apply Config
		$mod->terapkanConfig($cfg);
		$post = $this->input->post();
		//print_r($this->input->post());
		//echo'test';
		//exit;
		// Content Data
		
		$res = $mod->select_search($this->auth->id,@$post['month'],@$post['year']);
		
		$cfg->totalPage = (int) ceil(count($res) / $cfg->rowPerPage);
		// $cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);
		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource,
			'bulan' => @$post['month'],
			'year' => @$post['year']
		);
		
		
		$this->load->view('backend/pages/tps_online/notifikasi/kirim_tpsonline', $data);
	}
	public function kirim_tps($bl_number,$visit_id){
		$this->db = $this->get_db();
		$this->db->where('BL_NUMBER',$bl_number);
		$this->db->update('CARTOS_CARGO',array('IS_READY'=>1));
		
		redirect('tps_online/notifikasi/list_bl/'.$visit_id);
	}


	// ==== notifikasi export ==== //

	public function listview_export()
	{
		$this->load->view('backend/pages/tps_online/notifikasi/listview_export');
	}

	public function get_data_export($token){
		if($this->auth->token == $token){
			$db = $this->get_db();
			$this->load->model('tps_online/data_histori_model'); 
			
			$model = $this->data_histori_model->query_export($_POST['tahun'],$_POST['bulan']);
			
			echo json_encode($model);
		}
		else{
			var_dump($_REQUEST);die;
			echo json_encode('INVALID TOKEN');	
		}
		
	}

	public function view_export($visit_id){
		$num_args = func_num_args();
        $get_args = func_get_args();
		
		$mod = model('tps_online/notifikasi_model');

		$mod->set_db($this->get_db());

		$cfg = $mod->parseParameter($num_args, $get_args);
		

		// Apply Config
		$mod->terapkanConfig($cfg);
		$post = $this->input->post();
		//print_r($this->input->post());
		// Content Data
		$res = $mod->select_by_visit_export($this->auth->id,$visit_id);
		
		$cfg->totalPage = (int) ceil(count($res) / $cfg->rowPerPage);
		// $cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);
		//echo @$post['month'];
		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource,
			'bulan' => @$post['month'],
			'year' => @$post['year']
		);

		$this->load->view('backend/pages/tps_online/notifikasi/view_by_visit_export', $data);
	}

	public function bl_detail_export($idBL,$visit_id,$type){
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/notifikasi_model');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // Content Data
        $res = $mod->selectHistory($this->auth->id,$idBL,$visit_id,$type);
        $cfg->totalPage = (int) ceil(count($res) / $cfg->rowPerPage);

        //echo $res->actualRows;
        //print_r ($res);
        //die;
        // Layout Data
        $data = array(
            'history' => true,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datasource' => $res->datasource
        );
        // print_r($data);die;
        $this->load->view('backend/pages/tps_online/notifikasi/detail_bl_export', $data);
	}

	public function view_by_vin_export($id = NULL,$blnumber) {
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
			$base = site_url();
            $grid_state = str_replace($base,'',$_SERVER['HTTP_REFERER']);
        }

        $db = $this->get_db();

        $mod = model('tps_online/kargolist_internasional_inbound_model');
        $mod->set_db($db);

        $kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );
        
        if ($row = $mod->get_($id,$blnumber)) {
        	
            $view['kargo'] = $row;
			$view['id_bl'] = $id;
			$view['blnumber'] = $blnumber;
			$type_cargo = $kunjungan_kapal->select_type_cargo(array('STATUS' => 'Y'));
			$view['TYPE_CARGO_DS'] = $type_cargo;

            $this->load->view('backend/pages/tps_online/notifikasi/view_bl', $view);
        } else {
            redirect('tps_online/kargolist_internasional_inbound/listview/404');
        }
    }
}
