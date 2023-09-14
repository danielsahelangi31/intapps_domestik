<?php
class FormManual extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('Model_dashboard'
								
                            	));	
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
    private function get_db() {
        if (!$this->local_db) {
            $this->local_db = $this->load->database(IKT_FRIDAY, TRUE);
			// $this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'DD/MM/YY HH24:MI'");
			// $this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YY'");
          
        }

        return $this->local_db;
    }
	
	/** 
	 * Index
	 */
	public function index(){
		redirect('FormManual/form_manual');
	}

	public function form_manual(){	

		// $data['datamanual'] = $this->Model_dashboard->getVESEL(); 
	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_form_manual');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        // Layout Data
		$data = array(			
			'TYPE_VESEL' => $this->Model_dashboard->get_data_vessel(),
			'TYPE_SHIFT' => $this->Model_dashboard->select_type_shift(),
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datamanual' => $res->datamanual,
			'kunjung' => $res->kunjung
		);
		// $mod = model('tps_online/model_form_manual');
		$this->load->view('backend/pages/dashboard/form_manual',$data);
	}
    

    public function form_detail($id = NULL) {
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'FormManual/form_manual';
        }

        $db = $this->get_db();

        $mod = model('tps_online/model_form_manual');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {
            if (is_post_request()) {
//                 $this->load->library('form_validation');

                $val = $this->form_validation;
                $val->set_rules('COMMENCE', 'Commence', 'required');
                $val->set_rules('COMPLETED', 'Completed', 'required');
//                $val->set_rules('OUTWARD_BC11', 'Nomor Outward BC 1.1', 'required');
//                $val->set_rules('OUTWARD_BC11_DATE', 'Tanggal Outward BC 1.1', 'required');
                // $val->set_rules('LOAD_PORT', 'Load Port', 'required');
                // $val->set_rules('TRANSIT_PORT', 'Transit Port', 'required');
                // $val->set_rules('DISCHARGER_PORT', 'Discharge Port', 'required');
                // $val->set_rules('NEXT_PORT', 'Next Port', 'required');

                if ($val->run()) {
                    $mod->update($id);
                    $view['info_msg'] = 'Sukses edit data kunjungan kapal';
                } else {
                    $view['error_msg'] = validation_errors();
                }

                $row->COMMENCE = post('COMMENCE');
                $row->COMPLETED = post('COMPLETED');
// //                $row->OUTWARD_BC11 = post('OUTWARD_BC11');
// //                $row->OUTWARD_BC11_DATE = post('OUTWARD_BC11_DATE');
//                 $row->PBM = post('PBM');
//                 $row->ETA = post('ETA');
//                 $row->ETB = post('ETB');
// 				$row->ETD = post('ETD');
                //$row->NEXT_PORT = post('NEXT_PORT');
            }

            $view['kunjung'] = $row;

            $this->load->view('backend/pages/dashboard/form_detail', $view);
        } else {
            redirect('tps_online/internasional_inbound/listview/404');
        }
    }

	public function get($token = NULL) {
        if ($this->auth->token == $token) {
            $out = new StdClass();

            $where = array(
                'VESSEL_NAME' => post('VESSEL_NAME')
            );

            $db = $this->get_db();

            $data = $db->select('VESSEL_NAME, VOYAGE, ETA, ETB, ETD')->where($where)->get('VES_VOYAGE')->row();

            if ($data) {
                $data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
                $data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';

                $out->success = true;
                $out->datasource = $data;
            } else {
                $out->success = false;
                $out->msg = 'Tidak dapat menemukan Visit ID: ' . post('VISIT_ID');
            }

            echo json_encode($out);
        } else {
            echo 'INVALID TOKEN';
        }
    }

	public function get_vesel($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VESSEL_NAME' => post('VESSEL_NAME'),
				'PBM' => post('PBM'),
				'VOYAGE' => post('VOYAGE') 
			);
			
			$db = $this->get_db();
			
			$data = $this->Model_dashboard->getVESEL($where['VESSEL_NAME']);
	
			if($data){
				
				$data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
                $data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';	
				$data->ATA = $data->ATA ? date('d-M-Y H:i', strtotime($data->ATA)) : '-';
                $data->ATB = $data->ATB ? date('d-M-Y H:i', strtotime($data->ATB)) : '-';	
				$out->success = true;
				//$out->datasource = $data;
				$out->datasource = $data;
				$out->msg = 'Dapat menemukan Nama Kapal: '.post(datasource);
			}else{
				$out->success = false;
				$out->msg = 'Tidak dapat menemukan Nama Kapal: '.post('VESSEL_NAME');
			}
			
			echo json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
		public function load_data_vessel($token = null)
	{
		if($this->auth->token == $token){
			$db = $this->get_db();
			
			$this->load->model('Model_dashboard');
		
			$model = $this->Model_dashboard->get_data_vessel(); // $_POST['model_name'], $_POST['perusahaan']
			
			
			header('Content-Type: application/json');
			echo json_encode($model);
		}
		else{
			var_dump($_REQUEST);
			echo json_encode('INVALID TOKEN');	
		}
	}
	
}