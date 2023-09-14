<?php
/** Input Manual RKAP Arus Barang
  *	Modul untuk menambahkan input manual RKAP arus barang berdasarkan tahun dan terminal
  *
  */

class rkap_arus_barang extends CI_Controller{
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
        $mod = model('tps_online/Model_rkap_arus_barang');

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
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}

	
	public function listview1($terminal){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	


        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
        $tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminals = $terminal;
		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";

			$db_ilcs = $this->load->database('ikt_postgree', TRUE);

			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER","id_barang"  
						FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE "TERMINAL" LIKE '.$terminal.'     
					    ORDER BY "id_barang" ASC     
										
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
			$out->databarang=$databarang;
			$data['databarang'] = $databarang;
		
			$query1 = 'SELECT count("id_barang") AS "numRows" FROM "DASHBOARD_RKAP_ARUS_BARANG" 
			WHERE "TERMINAL" LIKE '.$terminal.'
			';

			$databrg = $db_ilcs->query($query1)->row()->numRows;
        	$out->databrg=$databrg;
			$data['databrg'] = $databrg;

			$cfg->totalPage = (int) ceil ($databrg/ $cfg->rowPerPage); 			
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals;


			$x = ($cfg->currPage*10)-$cfg->rowPerPage;

			for($i=0;$i<$cfg->rowPerPage;$i++){
			if ($cfg->currPage == $i){
				$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER","id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
				WHERE "TERMINAL" LIKE '.$terminal.'     
				ORDER BY "id_barang" ASC     
				LIMIT '.$cfg->rowPerPage.'
				OFFSET '.$x.' 
			';

			$databarang = $db_ilcs->query($query)->result_array();
			$out->databarang=$databarang;
			$data['databarang'] = $databarang;
				}
			}
		
        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}

	public function listview2($jenis){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
   		$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
		$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE "JENIS" LIKE '.$jenis.'     
					  	ORDER BY "id_barang" ASC   
         				
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
			$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/10);		
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $jeniss;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE "JENIS" LIKE '.$jenis.'     
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				}

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis	

		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}

	public function listview3($tahun){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
     	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE "TAHUN" LIKE '.$tahun.'     
				 		ORDER BY "id_barang" ASC       
         			  	
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $tahuns;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE "TAHUN" LIKE '.$tahun.'     
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				}

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}

	public function listview4($komoditi){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
     	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE "KOMODITI" LIKE '.$komoditi.'     
						ORDER BY "id_barang" ASC     
         			  	
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $komoditis;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE "KOMODITI" LIKE '.$komoditi.'     
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				}
		

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}

	public function listview5($terminal, $jenis){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
    	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE "TERMINAL" LIKE '.$terminal.' AND "JENIS" LIKE '.$jenis.'   
				 		ORDER BY "id_barang" ASC      
         			  	
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
			$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $jeniss;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE "TERMINAL" LIKE '.$terminal.' AND "JENIS" LIKE '.$jenis.'    
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 
		

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview6($terminal, $tahun){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
     	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE "TERMINAL" LIKE '.$terminal.' AND "TAHUN" LIKE '.$tahun.'   
						ORDER BY "id_barang" ASC    
         			  
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $tahuns;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE "TERMINAL" LIKE '.$terminal.' AND "TAHUN" LIKE '.$tahun.'    
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 
		

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview7($terminal, $komoditi){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
    	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					  WHERE "TERMINAL" LIKE '.$terminal.' AND "KOMODITI" LIKE '.$komoditi.'   
				 	  ORDER BY "id_barang" ASC    
         			  
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $komoditis;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE "TERMINAL" LIKE '.$terminal.' AND "KOMODITI" LIKE '.$komoditi.'    
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 
		

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview8($jenis, $tahun){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
     	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE "JENIS" LIKE '.$jenis.' AND "TAHUN" LIKE '.$tahun.'   
				 		ORDER BY "id_barang" ASC  
         			    
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $jeniss .'/'. $tahuns;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE "JENIS" LIKE '.$jenis.' AND "TAHUN" LIKE '.$tahun.'    
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 
		

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview9($jenis, $komoditi){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
     	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE "JENIS" LIKE '.$jenis.' AND "KOMODITI" LIKE '.$komoditi.'   
				 		ORDER BY "id_barang" ASC 
         			    
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $jeniss .'/'. $komoditis;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE "JENIS" LIKE '.$jenis.' AND "KOMODITI" LIKE '.$komoditi.'     
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 
		
        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview10($tahun, $komoditi){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
     	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER","id_barang" 
					   FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					   WHERE "TAHUN" LIKE '.$tahun.' AND "KOMODITI" LIKE '.$komoditi.'   
					   ORDER BY "id_barang" ASC 
         			  
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
			$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $tahuns .'/'. $komoditis;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER","id_barang" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG"  
				    WHERE "TAHUN" LIKE '.$tahun.' AND "KOMODITI" LIKE '.$komoditi.'   
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview11($terminal, $jenis, $tahun){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
     	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER","id_barang"
					    FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE  "TERMINAL" LIKE '.$terminal.' AND "JENIS" LIKE '.$jenis.' AND "TAHUN" LIKE '.$tahun.'  
						ORDER BY "id_barang" ASC     
         			  
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $jeniss .'/'. $tahuns;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER","id_barang"
					 FROM "DASHBOARD_RKAP_ARUS_BARANG"  
				     WHERE  "TERMINAL" LIKE '.$terminal.' AND "JENIS" LIKE '.$jenis.' AND "TAHUN" LIKE '.$tahun.'     
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview12($terminal, $jenis, $komoditi){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
    	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  
						FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE  "TERMINAL" LIKE '.$terminal.' AND "JENIS" LIKE '.$jenis.' AND "KOMODITI" LIKE '.$komoditi.'  
						ORDER BY "id_barang" ASC
         			  
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage);
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $jeniss .'/'. $komoditis;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  
					FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE  "TERMINAL" LIKE '.$terminal.' AND "JENIS" LIKE '.$jenis.' AND "KOMODITI" LIKE '.$komoditi.'     
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview13($terminal, $tahun, $komoditi){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
     	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  
						FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE  "TERMINAL" LIKE '.$terminal.' AND "TAHUN" LIKE '.$tahun.' AND "KOMODITI" LIKE '.$komoditi.'  
				 		ORDER BY "id_barang" ASC  
         			    
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $tahuns .'/'. $komoditis;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){
					$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  
					FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					WHERE  "TERMINAL" LIKE '.$terminal.' AND "TAHUN" LIKE '.$tahun.' AND "KOMODITI" LIKE '.$komoditi.'    
					ORDER BY "id_barang" ASC     
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
	
				$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
				$data['databarang'] = $databarang;
					}
				} 

		

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview14($jenis, $tahun, $komoditi){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
        $tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  
						FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					    WHERE  "JENIS" LIKE '.$jenis.' AND "TAHUN" LIKE '.$tahun.' AND "KOMODITI" LIKE '.$komoditi.'  
				 	    ORDER BY "id_barang" ASC    
         			  
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $jeniss .'/'. $tahuns .'/'. $komoditis;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;

			for($i=0;$i<$cfg->rowPerPage;$i++){
			if ($cfg->currPage == $i){
				$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  
				FROM "DASHBOARD_RKAP_ARUS_BARANG"  
				WHERE  "JENIS" LIKE '.$jenis.' AND "TAHUN" LIKE '.$tahun.' AND "KOMODITI" LIKE '.$komoditi.'  
				ORDER BY "id_barang" ASC     
				LIMIT '.$cfg->rowPerPage.'
				OFFSET '.$x.' 
			';

			$databarang = $db_ilcs->query($query)->result_array();
			$out->databarang=$databarang;
			$data['databarang'] = $databarang;
				}
			}

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}
	public function listview15($terminal,$jenis, $tahun, $komoditi){

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_rkap_arus_barang');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);	
	

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);		

        $terminal = str_replace('%20',' ',$terminal);
        $jenis = str_replace('%20',' ',$jenis);
        $tahun = str_replace('%20',' ',$tahun);
		$komoditi = str_replace('%20',' ',$komoditi);

		$terminal = strtoupper($terminal);
        $jenis = strtoupper($jenis);
    	$tahun = strtoupper($tahun);
        $komoditi = strtoupper($komoditi);

		$jeniss = $jenis;
		$terminals = $terminal;
        $tahuns = $tahun;
        $komoditis = $komoditi;

		$terminal = "'%$terminal%'";
		$jenis = "'%$jenis%'";
        $tahun = "'%$tahun%'";
        $komoditi = "'%$komoditi%'";
	
	
		$db_ilcs = $this->load->database('ikt_postgree', TRUE);
			$query = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  
					  FROM "DASHBOARD_RKAP_ARUS_BARANG"  
					  WHERE  "TERMINAL" LIKE '.$terminal.' AND "JENIS" LIKE '.$jenis.' AND "TAHUN" LIKE '.$tahun.' AND "KOMODITI" LIKE '.$komoditi.'  
					  ORDER BY "id_barang" ASC   
         			  
					';
	
			$databarang = $db_ilcs->query($query)->result_array();
				$out->databarang=$databarang;
			 $data['databarang'] = $databarang;
		
			$k1 = count($databarang); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
			$cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $jeniss .'/'. $tahuns .'/'. $komoditis;

			$x = ($cfg->currPage*10)-$cfg->rowPerPage;

			for($i=0;$i<$cfg->rowPerPage;$i++){
			if ($cfg->currPage == $i){
				$query = 'SELECT  DISTINCT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER", "id_barang"  
				 FROM "DASHBOARD_RKAP_ARUS_BARANG"  
				 WHERE  "TERMINAL" LIKE '.$terminal.' AND "JENIS" LIKE '.$jenis.' AND "TAHUN" LIKE '.$tahun.' AND "KOMODITI" LIKE '.$komoditi.' 
				ORDER BY "id_barang" ASC     
				LIMIT '.$cfg->rowPerPage.'
				OFFSET '.$x.' 
			';

			$databarang = $db_ilcs->query($query)->result_array();
			$out->databarang=$databarang;
			$data['databarang'] = $databarang;
				}
			}

        // Layout Data
		$data = array(		
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'databarang' => $res->databarang,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung,
			'databarang' => $databarang,
			'jeniss' => $jeniss,
			'terminals' => $terminals,
			'tahuns' => $tahuns,
			'komoditis' => $komoditis
		);

		$this->load->view('backend/pages/tps_online/rkap_arus_barang/listview',$data);
	}

    public function view($id = NULL) {
      
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {

            $grid_state = 'tps_online/rkap_arus_barang/listview';
        }

        $db = $this->get_db();

        $mod = model('tps_online/Model_rkap_arus_barang');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {
			
		    $atd =$row -> ATD; 
	
            $view = array(			
              
            );
            $view['kunjung'] = $row;
            $this->load->view('backend/pages/tps_online/rkap_arus_barang/view', $view);
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
			$mod = model('tps_online/Model_rkap_arus_barang');       
			$con = $this->load->database('ikt_postgree', TRUE);
			$mod->set_db($db);       

			if ($_FILES['upload_vin_excel']['name']) {
				$this->form_validation->set_rules('rkap', 'RKAP', 'required');
				if ( $this->form_validation->run() != 0){
					$vin = [];
	
					if (empty($_FILES['upload_vin_excel']['name']))
					{
						if(intval($this->input->post('length_vin')) > 0){
							for ($i = 1 ; $i <=intval($this->input->post('length_vin')) ; $i++){
								$vin['vinDetail'][] = array(
									'TERMINAL' => $this->input->post('TERMINAL'.$i),
									'JENIS' => $this->input->post('JENIS'.$i),
									'TAHUN' => $this->input->post('TAHUN'.$i),
									'KOMODITI' => $this->input->post('KOMODITI'.$i),
									'SATUAN' => $this->input->post('SATUAN'.$i),
									'JANUARI' => $this->input->post('JANUARI'.$i),
									'FEBRUARI' => $this->input->post('FEBRUARI'.$i),
									'MARET' => $this->input->post('MARET'.$i),
									'APRIL' => $this->input->post('APRIL'.$i),
									'MEI' => $this->input->post('MEI'.$i),
									'JUNI' => $this->input->post('JUNI'.$i),
									'JULI' => $this->input->post('JULI'.$i),
									'AGUSTUS' => $this->input->post('AGUSTUS'.$i),
									'SEPTEMBER' => $this->input->post('SEPTEMBER'.$i),
									'OKTOBER' => $this->input->post('OKTOBER'.$i),
									'NOVEMBER' => $this->input->post('NOVEMBER'.$i),
									'DESEMBER' => $this->input->post('DESEMBER'.$i)
								);
							}
						}else{
							$vin['vinDetail'][] = array(
								'TERMINAL' => null,
								'JENIS' => null,
								'TAHUN' => null,
								'KOMODITI' => null,
								'SATUAN' => null,
								'JANUARI' => null,
								'FEBRUARI' => null,
								'MARET' => null,
								'APRIL' => null,
								'MEI' => null,
								'JUNI' => null,
								'JULI' => null,
								'AGUSTUS' => null,
								'SEPTEMBER' => null,
								'OKTOBER' => null,
								'NOVEMBER' => null,
								'DESEMBER' => null
							);
						}
					}else{
	
						include APPPATH.'third_party/PHPExcel/PHPExcel.php';
						$csvreader = new PHPExcel_Reader_Excel2007();			
						$path = $_FILES["upload_vin_excel"]["tmp_name"];
						$loadcsv = $csvreader->load($path);
	
						$tmp_code = array();
	
	
						foreach($loadcsv->getWorksheetIterator() as $worksheet)
						{
							$highestRow = $worksheet->getHighestRow();
							$highestColumn = $worksheet->getHighestColumn();
							for($row=2; $row<=$highestRow; $row++)
							{
								$terminal = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
								$tahun = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
								$jenis = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
								$komoditi = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
								$satuan = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
								$januari = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
								$februari = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
								$maret = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
								$april = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
								$mei = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
								$juni = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
								$juli = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
								$agustus = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
								$september = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
								$oktober = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
								$november = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
								$desember = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
	
								$vin['vinDetail'][] = array(
									'TERMINAL' => $terminal,
									'JENIS' => $jenis,
									'TAHUN' => $tahun,
									'KOMODITI' => $komoditi,
									'SATUAN' => $satuan,
									'JANUARI' => $januari,
									'FEBRUARI' => $februari,
									'MARET' => $maret,
									'APRIL' => $april,
									'MEI' => $mei,
									'JUNI' => $juni,
									'JULI' => $juli,
									'AGUSTUS' => $agustus,
									'SEPTEMBER' => $september,
									'OKTOBER' => $oktober,
									'NOVEMBER' => $november,
									'DESEMBER' => $desember
								);
	
							}
						}
	
					}
	
	
					$payload = array(
						'ADCMessageHeader' => array(
							'DocumentTransferId' => $this->input->post('DocumentTransferId'),
							'MessageType' => 'ANNOUNCE_VIN',
							'Sender' => $this->userauth->getLoginData()->sender == 'IKT' ? $this->input->post('typeIKT') : $this->userauth->getLoginData()->sender,
							'Receiver' => 'CARTOS',
							'SentTime' => date("Ymdhis")
						),
						'ADCMessageBody' => array(
							'AnnounceVinReqest' => array(
								'VinInfo' => $vin
							)
						)
					);
	
					$getData = $mod->OpAnnounceVin($payload);
					$docTransferID = $getData? $getData->response->ADCMessageHeader->DocumentTransferId : null;
					$vinResponseInfo = $getData? $getData->response->ADCAcknowledgeBody->AnnounceVinResponse->vinResponseInfo : null;
	
					$this->logger
						->user($this->userauth->getLoginData()->username)
						->function_name($this->router->fetch_method())
						->comment('Announce VIN')
						->new_value(json_encode($getData))
						->log();
	
				} else {
					$customs = array(
						'status'  => 'Failed',
						'message' => 'Truck Code Tidak Boleh Kosong'
					);
					echo json_encode($customs);
					die();
				}
			}
            $view = array(			
				'datasource' => $datasource,
				'datafield' => $dataField    
            );
       

			
            $this->load->view('backend/pages/tps_online/rkap_arus_barang/new', $view);
		
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