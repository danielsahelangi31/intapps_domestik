<?php
/** Input Manual Tarif TW
  *	Modul untuk menambahkan input manual tarif tw berdasarkan kondisi ideal dan non-idea, serta terminal dan tahun
  *
  */

class tarif_tw extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/Model_tarif'
								
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
		redirect('tps_online/tarif_tw/listview');
	}


	public function listview(){	

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_tarif');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);
	
	
	
        // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datatarif' => $res->datatarif,		
			'kunjung' => $res->kunjung
		);

		$this->load->view('backend/pages/tps_online/tarif_tw/listview',$data);
	}



    public function view($id = NULL) {
      
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {

            $grid_state = 'tps_online/tarif_tw/listview';
        }

        $db = $this->get_db();

        $mod = model('tps_online/Model_tarif');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {

		    $view = array(			
            
            );
            $view['kunjung'] = $row;
            $this->load->view('backend/pages/tps_online/tarif_tw/view', $view);
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
		
            echo json_encode($out);
        } else {
            echo 'INVALID TOKEN';
        }
    }

  	  public function new() {
	
			$out = new StdClass();
			$db = $this->get_db();
			$this->db = $this->get_db();
			$mod = model('tps_online/Model_tarif');       
			$con = $this->load->database('ikt_postgree', TRUE);
			$mod->set_db($db);       

            $komoditi = "'KOMODITI'";
			$query = 'SELECT "KODE","NAMA","TYPE" from "DASHBOARD_MST_PELAYANAN"
					 WHERE "TYPE" = '.$komoditi.'
			';

			$datarkap = $con->query($query)->result();
			$out->datarkap=$datarkap;
			 $data['datarkap'] = $datarkap;	
				
			 $pelayanan = "'PELAYANAN'";
			 $query = 'SELECT "KODE","NAMA","TYPE" from "DASHBOARD_MST_PELAYANAN"
					  WHERE "TYPE" = '.$pelayanan.'
			 ';
 
			 $dataPelayanan = $con->query($query)->result();
			 $out->dataPelayanan=$dataPelayanan;
			  $data['dataPelayanan'] = $dataPelayanan;	

			  $golongan = "'GOLONGAN'";
			  $query = 'SELECT "KODE","NAMA","TYPE" from "DASHBOARD_MST_PELAYANAN"
					   WHERE "TYPE" = '.$golongan.'
			  ';
  
			  $dataGolongan = $con->query($query)->result();
			  $out->dataGolongan=$dataGolongan;
			   $data['dataGolongan'] = $dataGolongan;	
 
			  
            $view = array(			
				'datasource' => $datasource,
				'datafield' => $dataField,
				'datarkap' => $datarkap,
				'dataPelayanan' => $dataPelayanan,
				'dataGolongan' => $dataGolongan
            );       
  			
            $this->load->view('backend/pages/tps_online/tarif_tw/new', $view);
		
    }



	
	public function finalize1($terminal = '', $komoditi = '', $tahun='') {
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

		 $terminal = str_replace('%20',' ',$terminal);
		 $komoditi = str_replace('%20',' ',$komoditi);
		 $pelayanan = str_replace('%20',' ',$pelayanan);
		 $golongan = str_replace('%20',' ',$golongan);
		 $tahun = str_replace('%20',' ',$tahun);

		$terminal = "'$terminal'";
		$komoditi = "'$komoditi'";
		$pelayanan = "'$pelayanan'";
		$golongan = "'$golongan'";
		$tahun = "'$tahun'";		

		$databm = 'SELECT  "TERMINAL", 
						   "KOMODITI", 
						   "PELAYANAN", 
						   "GOLONGAN", 
						   "TAHUN", 
						   "TARIF_1", 
						   "TARIF_2",
                           "TYPE",
                           "ID_TARIF"					 

							FROM  "DASHBOARD_TARIF_TW"	
							WHERE "TERMINAL"='.$terminal.' AND "KOMODITI"='.$komoditi.' AND "TAHUN" = '.$tahun.'';					
						
						
		$databm = $con->query($databm)-> result();
		$out->databm=$databm;
		 $data['databm'] = $databm;	

		 $view = array(			
			'databm' => $databm,
			'grid_state' => $grid_state
		
		);
   

            $this->load->view('backend/pages/tps_online/tarif_tw/finalize', $view);
   
    }

	public function finalize($id) {
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

		 $terminal = str_replace('%20',' ',$terminal);
		 $komoditi = str_replace('%20',' ',$komoditi);
		 $pelayanan = str_replace('%20',' ',$pelayanan);
		 $golongan = str_replace('%20',' ',$golongan);
		 $golongan = str_replace('%3C','<',$golongan);
		 $golongan = str_replace('%3E','>',$golongan);	
		 $tahun = str_replace('%20',' ',$tahun);
	
		$terminal = "'$terminal'";
		$komoditi = "'$komoditi'";
		$pelayanan = "'$pelayanan'";
		$golongan = "'$golongan'";
		$tahun = "'$tahun'";		

		$databm = 'SELECT  "TERMINAL", 
						   "KOMODITI", 
						   "PELAYANAN", 
						   "GOLONGAN", 
						   "TAHUN", 
						   "TARIF_1", 
						   "TARIF_2",
                           "TYPE",
                           "ID_TARIF"					 

							FROM  "DASHBOARD_TARIF_TW"	
							WHERE "ID_TARIF"='.$id.'';					
						
						
		$databm = $con->query($databm)-> result();
		$out->databm=$databm;
		 $data['databm'] = $databm;	

		 $view = array(			
			'databm' => $databm,
			'grid_state' => $grid_state
		
		);
   

            $this->load->view('backend/pages/tps_online/tarif_tw/finalize', $view);
   
    }

	public function finalize3($terminal = '', $komoditi = '', $pelayanan='', $tahun='') {
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

		 $terminal = str_replace('%20',' ',$terminal);
		 $komoditi = str_replace('%20',' ',$komoditi);
		 $pelayanan = str_replace('%20',' ',$pelayanan);
		 $golongan = str_replace('%20',' ',$golongan);
		 $tahun = str_replace('%20',' ',$tahun);

		$terminal = "'$terminal'";
		$komoditi = "'$komoditi'";
		$pelayanan = "'$pelayanan'";
		$golongan = "'$golongan'";
		$tahun = "'$tahun'";		

		$databm = 'SELECT  "TERMINAL", 
						   "KOMODITI", 
						   "PELAYANAN", 
						   "GOLONGAN", 
						   "TAHUN", 
						   "TARIF_1", 
						   "TARIF_2",
                           "TYPE",
                           "ID_TARIF"					 

							FROM  "DASHBOARD_TARIF_TW"	
							WHERE "TERMINAL"='.$terminal.' AND "KOMODITI"='.$komoditi.' AND "PELAYANAN" = '.$pelayanan.' AND "TAHUN" = '.$tahun.'';					
						
						
		$databm = $con->query($databm)-> result();
		$out->databm=$databm;
		 $data['databm'] = $databm;	

		 $view = array(			
			'databm' => $databm,
			'grid_state' => $grid_state
		
		);
   

            $this->load->view('backend/pages/tps_online/tarif_tw/finalize', $view);
   
    }

	public function finalize4($id) {
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

		 $terminal = str_replace('%20',' ',$terminal);
		 $komoditi = str_replace('%20',' ',$komoditi);
		 $pelayanan = str_replace('%20',' ',$pelayanan);
		 $golongan = str_replace('%20',' ',$golongan);
		 $tahun = str_replace('%20',' ',$tahun);

		$terminal = "'$terminal'";
		$komoditi = "'$komoditi'";
		$pelayanan = "'$pelayanan'";
		$golongan = "'$golongan'";
		$tahun = "'$tahun'";		

		$databm = 'SELECT  "TERMINAL", 
						   "KOMODITI", 
						   "PELAYANAN", 
						   "GOLONGAN", 
						   "TAHUN", 
						   "TARIF_1", 
						   "TARIF_2",
                           "TYPE",
                           "ID_TARIF"					 

							FROM  "DASHBOARD_TARIF_TW"	
							WHERE "ID_TARIF" ='.$id.'';					
						
						
		$databm = $con->query($databm)-> result();
		$out->databm=$databm;
		 $data['databm'] = $databm;	

		 $view = array(			
			'databm' => $databm,
			'grid_state' => $grid_state
		
		);
   

            $this->load->view('backend/pages/tps_online/tarif_tw/finalize', $view);
   
    }



}