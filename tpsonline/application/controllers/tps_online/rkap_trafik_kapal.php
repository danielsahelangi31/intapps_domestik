<?php
/** Input Manual RKAP Trafik Kapal
  *	Modul untuk menambahkan input manual RKAP trafik kapal berdasarkan tahun dan terminal
  *
  */

class rkap_trafik_kapal extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/Model_lap_trafik_kapal'
								
                            	));	
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
    private function get_db() {
        if (!$this->local_db) {
            $this->local_db = $this->load->database('ikt_postgree', TRUE);
	
        }

        return $this->local_db;
    }
	
	/** 
	 * Index
	 */
	public function index(){
		redirect('tps_online/rkap_trafik_kapal/listview');
	}


	public function listview(){	

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_trafik_kapal');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);
	
	
	
        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datatrafik' => $res->datatrafik,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung
		);

		$this->load->view('backend/pages/tps_online/rkap_trafik_kapal/listview',$data);
	}



    public function view($id = NULL) {
      
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {

            $grid_state = 'tps_online/rkap_trafik_kapal/listview';
        }

        $db = $this->get_db();

        $mod = model('tps_online/Model_rkap_trafik_kapal');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {
            
		    $atd =$row -> ATD; 
		
            $view = array(			
               	
            );
            $view['kunjung'] = $row;
            $this->load->view('backend/pages/tps_online/rkap_trafik_kapal/view', $view);
        }

    }

    public function get($token = NULL) {
        if ($this->auth->token == $token) {
            $out = new StdClass();

            $where = array(
                'VISIT_NAME' => post('VISIT_NAME')
            );

            $db = $this->get_db();

            $data = $db->select('PERIODE, SHIPPING_AGENT, INSERT_DATE')->where($where)->get('MART_TRF_KAPAL')->row();	
       
            if ($data) {
                $data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
                $data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';

                $out->success = true;
                $out->datalap = $data;
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
			$mod = model('tps_online/Model_rkap_trafik_kapal');       
			$con = $this->load->database('ikt_postgree', TRUE);
			$mod->set_db($db);   

            $view = array(			
				'datasource' => $datasource,
				'datafield' => $dataField    
            );
       
			$this->db->where("VISIT_NAME",$VISIT_NAME);
			$VISIT_NAME = '';
			$data = array('view'=>$view, 'VISIT_NAME'=>$VESSEL_NAME);

			
            $this->load->view('backend/pages/tps_online/rkap_trafik_kapal/new', $view);
		
    }

	public function neww() {
	
		$out = new StdClass();
		$db = $this->get_db();
		$this->db = $this->get_db();
		$mod = model('tps_online/Model_berthing');       

		$mod->set_db($db);       

		$vesse_id = str_replace('%20',' ',$_REQUEST['id']);
		$voyage = $_REQUEST['voyage'];

		 // Content Data
		$datasource_modal =  $this->db->select('VISIT_ID, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, VESSEL_CODE')->order_by("VESSEL_CODE", "ASC")->get('STAGING_CARTOS_SHIP_VISIT')->result();

		$datasource = $db->select('VISIT_ID, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, VESSEL_CODE')->where('VISIT_NAME',$vesse_id)->where('VOYAGE_IN',$voyage)->get('STAGING_CARTOS_SHIP_VISIT')->result();
	
		$dataField = array('VISIT_ID'=>'', ' VISIT_NAME'=>'', 'VOYAGE_IN'=>'', 'VOYAGE_OUT'=>'', 'VESSEL_CODE'=>'');

		$view = array(			
			'datasource' => $datasource_modal,
			'datafield'	=> $datasource[0]
	
		);
   
		$this->db->where("VISIT_NAME",$vesse_id);
	
		$data= array('view'=>$view, 'VISIT_NAME'=>$vesse_id);

	

		$this->load->view('backend/pages/tps_online/laporan/new', $data);
	
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
            $grid_state = 'tps_online/laporan/finalize';
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
						   "KADE_AKHIR"

							FROM  "DASHBOARD_BERTHING_PLAN"	
							WHERE "VESSEL_NAME"='.$id.' AND "VOYAGE_IN"='.$voyage.' ';					
						
						
		$databm = $con->query($databm)-> result();
		$out->databm=$databm;
		 $data['databm'] = $databm;	

		 $view = array(			
			'databm' => $databm,
			'grid_state' => $grid_state
		
		);
   

            $this->load->view('backend/pages/tps_online/laporan/finalize', $view);

    }

	
}