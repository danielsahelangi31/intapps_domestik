<?php
class Berthing_time extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/Model_berthing'
								
                            	));	
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
    private function get_db() {
        if (!$this->local_db) {
            $this->local_db = $this->load->database('ikt_postgree', TRUE);
			// $this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'DD/MM/YY HH24:MI'");
			// $this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YY'");
          
        }

        return $this->local_db;
    }
	
	/** 
	 * Index
	 */
	public function index(){
		redirect('tps_online/berthing_time/listview');
	}


	public function listview(){	

		// $data['datamanual'] = $this->Model_dashboard->getVESEL(); 
	// die;
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_berthing');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

	
        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        // Layout Data
		$data = array(			
			'TYPE_KADE' => $this->Model_berthing->select_type_kade(),
			// 'TYPE_SHIFT' => $this->Model_dashboard->select_type_shift(),
			'TYPE_ACTIVITY' => $this->Model_dashboard->select_type_activity(),
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databerth' => $res->databerth,
			'dataplan' => $res->dataplan,
			'datasource' => $res->datasource,
			'kunjung' => $res->kunjung
		);

		$this->load->view('backend/pages/tps_online/berthing_time/listview',$data);
	}



    public function view($id = NULL) {
      
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/berthing_time/listview';
        }

        $db = $this->get_db();

        $mod = model('tps_online/Model_berthing');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {
            if (is_post_request()) {
               $this->load->library('form_validation');

                $val = $this->form_validation;
				$val->set_rules('PBM', 'PBM', 'required');
                $val->set_rules('KADE_DERMAGA', 'KADE DERMAGA', 'required');
                $val->set_rules('RENCANA_BONGKAR', 'RENCANA BONGKAR', 'required');
                $val->set_rules('RENCANA_MUAT', 'RENCANA MUAT', 'required');
            

                if ($val->run()) {
                    $mod->update($id);
                     $view['info_msg'] = 'Sukses edit data';
                } else {
                    $view['error_msg'] = validation_errors();
                }

                $row->KADE_DERMAGA = post('KADE_DERMAGA');
                $row->RENCANA_BONGKAR = post('RENCANA_BONGKAR');
                $row->RENCANA_MUAT = post('RENCANA_MUAT');
                $row->PBM = post('PBM');
                $row->NAMA_KAPAL = post('NAMA_KAPAL');
                $row->VOYAGE = post('VOYAGE');        
                $row->ETA = post('ETA');  
                $row->ATA = post('ATA');  
                $row->ETB = post('ETB');  
                $row->ATB = post('ATB'); 
                $row->ETD = post('ETD');  
                $row->ATD = post('ATD');  
                $row->SHIFT = post('SHIFT');
                $row->ACTIVITY = post ('ACTIVITY');
                $row->TIME_END = post('TIME_END');
                $row->REALISASI_BONGKAR = post ('REALISASI_BONGKAR');
                $row->REALISASI_MUAT = post('REALISASI_MUAT');   
				
			

          }

		    $atd =$row -> ATD; 
			// $xy =  date('DD-MM-YY', post('ATD'));
			// echo $xy;
        	// die();
            $view = array(			
                'TYPE_SHIFT' => $this->Model_dashboard->select_type_shift(),
                'TYPE_ACTIVITY' => $this->Model_dashboard->select_type_activity()	
            );
            $view['kunjung'] = $row;
            $this->load->view('backend/pages/tps_online/berthing_time/view', $view);
         } else {
            redirect('tps_online/berthing_time/404');
        }
    }

    public function get($token = NULL) {
        if ($this->auth->token == $token) {
            $out = new StdClass();

            $where = array(
                'VIS' => post('VISIT_NAME')
            );

            $db = $this->get_db();

            $data = $db->select('VISIT_ID, VISIT_NAME, VESSEL_NAME, VOYAGE_IN, VOYAGE_OUT, ETA, ETD')->where($where)->get('STAGING_CARTOS_SHIP_VISIT')->row();
			$dataplan = $db->select('VISIT_ID, VISIT_NAME, VESSEL_NAME, VOYAGE_IN, VOYAGE_OUT, KADE_NAME, KADE_AWAL, KADE_AKHIR')->where($where)->get('DASHBOARD_BERTHING_PLAN')->row();

       
            if ($data) {
                $data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
                $data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';

                $out->success = true;
                $out->databerth = $data;
				$out->dataplan = $dataplan;
            } else {
                $out->success = false;
                $out->msg = 'Tidak dapat menemukan Visit ID: ' . post('VISIT_ID');
            }

            echo json_encode($out);
        } else {
            echo 'INVALID TOKEN';
        }
    }

  	  public function new() {
	
			$out = new StdClass();
			$db = $this->get_db();
			$this->db = $this->get_db();
			$mod = model('tps_online/Model_berthing');       
			$con = $this->load->database('ikt_postgree', TRUE);
			$mod->set_db($db);       

             // Apply Config
			//  $mod->terapkanConfig($cfg);

			
			$datasource = 'SELECT  *					
			FROM  "STAGING_CARTOS_SHIP_VISIT"
			WHERE "VISIT_NAME" IS NOT NULL;
			';
			$datasource = $con->query($datasource)-> result();
			$out->datasource=$datasource;
			// var_dump($datasource);die();
			 // // Content Data
			//  $datasource = $this->db->select('VISIT_ID IS, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, VESSEL_CODE')->where('VISIT_NAME', NULL, FALSE)->order_by("VESSEL_CODE", "asc")->get('STAGGING_CARTOS_SHIP_VISIT')->result();
			 $data['datasource'] = $datasource;	
			 $dataField = array('VISIT_ID'=>'', ' VISIT_NAME'=>'', 'VOYAGE_IN'=>'', 'VOYAGE_OUT'=>'', 'VESSEL_CODE'=>'');

			

         $query = 'SELECT * from "STAGING_CARTOS_SHIP_VISIT"';

        $databerth = $con->query($query)->result();
        $out->databerth=$databerth;
         $data['databerth'] = $databerth;	
     
            $view = array(			
				'datasource' => $datasource,
				'datafield' => $dataField  ,
				'databerth'=> $databerth 
            );
       
			$this->db->where("VISIT_NAME",$VISIT_NAME);
			$VISIT_NAME = '';
			$data = array('view'=>$view, 'VISIT_NAME'=>$VESSEL_NAME);

		  // var_dump($view['databerth']);die();
            $this->load->view('backend/pages/tps_online/berthing_time/new', $data);
		
    }

	public function neww() {
	
		$out = new StdClass();
		$db = $this->get_db();
		$this->db = $this->get_db();
		$mod = model('tps_online/Model_berthing');       

		$mod->set_db($db);       

		$vesse_id = str_replace('%20',' ',$_REQUEST['id']);
		$voyage = $_REQUEST['voyage'];
		
		 // Apply Config
		//  $mod->terapkanConfig($cfg);


		 // // Content Data
		$datasource_modal =  $this->db->select('VISIT_ID, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, VESSEL_CODE')->order_by("VESSEL_CODE", "ASC")->get('STAGING_CARTOS_SHIP_VISIT')->result();

		$datasource = $db->select('VISIT_ID, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, VESSEL_CODE')->where('VISIT_NAME',$vesse_id)->where('VOYAGE_IN',$voyage)->get('STAGING_CARTOS_SHIP_VISIT')->result();
	
		$dataField = array('VISIT_ID'=>'', ' VISIT_NAME'=>'', 'VOYAGE_IN'=>'', 'VOYAGE_OUT'=>'', 'VESSEL_CODE'=>'');

		$view = array(			
			'datasource' => $datasource_modal,
			'datafield'	=> $datasource[0]
	
		);
   
		$this->db->where("VISIT_NAME",$vesse_id);
		// $data['VESSEL_NAME']=$vesse_id;
		$data= array('view'=>$view, 'VISIT_NAME'=>$vesse_id);

	

		$this->load->view('backend/pages/tps_online/berthing_time/new', $data);
	
}

	public function gett($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VISIT_NAME' => post('VISIT_NAME')
			);
			
			$db = $this->get_db();
			
			$data = $db->select('ID_VESSEL, VESSEL_NAME, VOYAGE, PBM, ID_KADE')->where($where)->get('VES_VOYAGE')->row();
			
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

	
	public function finalize($id = NULL, $voyage = NULL) {
		$num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/berthing_time/finalize';
        }

		$con = $this->load->database('ikt_postgree', TRUE);

		$out = new StdClass();
        $db = $this->get_db();
		$this->db = $this->get_db();
		$mod = model('tps_online/model_berthing');       

		$mod->set_db($db);       

		$cfg = $mod->parseParameter($num_args, $get_args);
		 // Apply Config
		 $mod->terapkanConfig($cfg);

		 // // Content Data
		//  $databm = $this->db->select('NAMA_KAPAL, VOYAGE, PBM, ETA, ETB, ETD')->order_by("NAMA_KAPAL", "desc")->get('DASHBOARD_BM_HEADER')->result();
		$id_fin = str_replace('%20',' ',$id);
		$id = "'$id_fin'";
		$voyage = "'$voyage'";

		$databm = 'SELECT  "VISIT_ID", 
						   "VESSEL_CODE", 
						   "VESSEL_NAME", 
						   "VOYAGE_IN", 
						   "VOYAGE_OUT", 
						   "KADE_NAME", 
						   "KADE_AWAL", 
						   "KADE_AKHIR",
						   "id_berthing"

							FROM  "DASHBOARD_BERTHING_PLAN"	
							WHERE "VESSEL_NAME"='.$id.' AND "VOYAGE_IN"='.$voyage.' ';					
						
						
		$databm = $con->query($databm)-> result();
		$out->databm=$databm;
		 $data['databm'] = $databm;	
        
        // $data = $db->select('PERIODE_BULAN, CREATED_DATE, LQ_GATE_1_BACK_KCY, LQ_GATE_1_QUARANTINE, LQ_GATE_2, LQ_GATE_3, CARGO_DEFECT')->get('DASHBOARD_ZERO_DEFECT')->row();

        // if ($data) {      

        //     $out->success = true;
        //     $out->datasource = $data;
        // } else {
        //     $out->success = false;
        //     $out->msg = 'Tidak dapat menemukan Visit ID: ' . post('PERIODE_BULAN');
        // }

        // echo json_encode($out);

		 $view = array(			
			'databm' => $databm,
			'grid_state' => $grid_state
		
		);
   

            $this->load->view('backend/pages/tps_online/berthing_time/finalize', $view);
        // } else {
        //     redirect('tps_online/form/listview/404');
        // }
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
	
		public function load_data_vessels($token = null)
	{
		if($this->auth->token == $token){
			$db = $this->get_db();
			
			$this->load->model('Model_dashboard');
		
			$model = $this->model_dashboard->get_data_vessels(); // $_POST['model_name'], $_POST['perusahaan']
			
			
			header('Content-Type: application/json');
			echo json_encode($model);
		}
		else{
			var_dump($_REQUEST);
			echo json_encode('INVALID TOKEN');	
		}
	}
}