<?php
/** Input Manual Safety
  *	Modul untuk menambahkan input manual safety berdasarkan tahun dan terminal
  *
  */
class Zero_safety extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();

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

        redirect('tps_online/zero_safety/listview');       

	}

	public function listview(){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

        $db_ilcs = $this->load->database('ikt_cardom', TRUE);
        $datasource = $db_ilcs->query($query)->result();
        $out->datasource=$datasource;
        $data['datasource'] = $datasource;

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
			'kunjung' => $res->kunjung,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    
    
	public function listview1($terminal){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;   

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TERMINAL" LIKE '.$terminal.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';

        $datazero = $db_ilcs->query($query)->result_array();
        $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
     
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals;     
     
            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TERMINAL" LIKE '.$terminal.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';

                $datazero = $db_ilcs->query($query)->result_array();
                $out->datazero=$datazero;
                $data['datazero'] = $datazero;	
					}
				}
     
        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
         
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    
    public function listview2($periode){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periods%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "PERIODE_BULAN" LIKE '.$periode.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
        ORDER BY "PERIODE_BULAN" ASC      
         
        ';
        $datazero = $db_ilcs->query($query)->result_array();
        $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $periods;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "PERIODE_BULAN" LIKE '.$periode.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';

                $datazero = $db_ilcs->query($query)->result_array();
                $out->datazero=$datazero;
                $data['datazero'] = $datazero;	
					}
				}

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview3($tahun){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TAHUN" LIKE '.$tahun.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
        
        $datazero = $db_ilcs->query($query)->result_array();
        $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $tahuns ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TAHUN" LIKE '.$tahun.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';

                $datazero = $db_ilcs->query($query)->result_array();
                $out->datazero=$datazero;
                $data['datazero'] = $datazero;	
					}
				}
       
        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview4($maker){
	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "MAKER" LIKE '.$maker.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         
        $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $makers;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "MAKER" LIKE '.$maker.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';

                $datazero = $db_ilcs->query($query)->result_array();
                $out->datazero=$datazero;
                $data['datazero'] = $datazero;	
					}
				}

                $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

                $db_ilcs = $this->load->database('ikt_cardom', TRUE);
                $datasource = $db_ilcs->query($query)->result();
                $out->datasource=$datasource;
                $data['datasource'] = $datasource;

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview5($terminal,$periode){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TERMINAL" LIKE '.$terminal.' AND "PERIODE_BULAN" LIKE '.$periode.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $periods ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TERMINAL" LIKE '.$terminal.' AND "PERIODE_BULAN" LIKE '.$periode.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';

                $datazero = $db_ilcs->query($query)->result_array();
                $out->datazero=$datazero;
                $data['datazero'] = $datazero;	
					}
				}

       
        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview6($terminal,$tahun){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TERMINAL" LIKE '.$terminal.' AND "TAHUN" LIKE '.$tahun.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
        
         $datazero = $db_ilcs->query($query)->result_array();   
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $tahuns ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TERMINAL" LIKE '.$terminal.' AND "TAHUN" LIKE '.$tahun.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';

                $datazero = $db_ilcs->query($query)->result_array();
                $out->datazero=$datazero;
                $data['datazero'] = $datazero;	
					}
				}

       
        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview7($terminal,$maker){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TERMINAL" LIKE '.$terminal.' AND "MAKER" LIKE '.$maker.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
        ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals .'/'. $makers ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TERMINAL" LIKE '.$terminal.' AND "MAKER" LIKE '.$maker.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';

                $datazero = $db_ilcs->query($query)->result_array();
                $out->datazero=$datazero;
                $data['datazero'] = $datazero;	
					}
				}

                $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

                $db_ilcs = $this->load->database('ikt_cardom', TRUE);
                $datasource = $db_ilcs->query($query)->result();
                $out->datasource=$datasource;
                $data['datasource'] = $datasource;
       
        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview8($periode,$tahun){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "PERIODE_BULAN" LIKE '.$periode.' AND "TAHUN" LIKE '.$tahun.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $periods .'/'. $tahuns ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "PERIODE_BULAN" LIKE '.$periode.' AND "TAHUN" LIKE '.$tahun.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';

                $datazero = $db_ilcs->query($query)->result_array();
                $out->datazero=$datazero;
                $data['datazero'] = $datazero;	
					}
				}

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview9($periode,$maker){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "PERIODE_BULAN" LIKE '.$periode.' AND "MAKER" LIKE '.$maker.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         
        $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $periods .'/'. $makers ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "PERIODE_BULAN" LIKE '.$periode.' AND "MAKER" LIKE '.$maker.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
                }
            }
       
            $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

            $db_ilcs = $this->load->database('ikt_cardom', TRUE);
            $datasource = $db_ilcs->query($query)->result();
            $out->datasource=$datasource;
            $data['datasource'] = $datasource;

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview10($tahun,$maker){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TAHUN" LIKE '.$tahun.' AND "MAKER" LIKE '.$maker.'
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $tahuns .'/'. $makers ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TAHUN" LIKE '.$tahun.' AND "MAKER" LIKE '.$maker.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
                }
            }
            $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

            $db_ilcs = $this->load->database('ikt_cardom', TRUE);
            $datasource = $db_ilcs->query($query)->result();
            $out->datasource=$datasource;
            $data['datasource'] = $datasource;

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview11($terminal,$periode,$tahun){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TERMINAL" LIKE '.$terminal.' AND "PERIODE_BULAN" LIKE '.$periode.' AND "TAHUN" LIKE '.$tahun.' 
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals.'/'. $periods .'/'. $tahuns ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TERMINAL" LIKE '.$terminal.' AND "PERIODE_BULAN" LIKE '.$periode.' AND "TAHUN" LIKE '.$tahun.' 
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
                }
            }
       
        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview12($terminal,$periode,$maker){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TERMINAL" LIKE '.$terminal.' AND "PERIODE_BULAN" LIKE '.$periode.' AND "MAKER" LIKE '.$maker.' 
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals.'/'. $periods .'/'. $makers ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TERMINAL" LIKE '.$terminal.' AND "PERIODE_BULAN" LIKE '.$periode.' AND "MAKER" LIKE '.$maker.' 
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
                }
            }
      
            $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

            $db_ilcs = $this->load->database('ikt_cardom', TRUE);
            $datasource = $db_ilcs->query($query)->result();
            $out->datasource=$datasource;
            $data['datasource'] = $datasource;

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview13($terminal,$tahun,$maker){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TERMINAL" LIKE '.$terminal.' AND "TAHUN" LIKE '.$tahun.' AND "MAKER" LIKE '.$maker.' 
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals.'/'. $tahuns .'/'. $makers ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TERMINAL" LIKE '.$terminal.' AND "TAHUN" LIKE '.$tahun.' AND "MAKER" LIKE '.$maker.' 
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
                }
            }

            $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

            $db_ilcs = $this->load->database('ikt_cardom', TRUE);
            $datasource = $db_ilcs->query($query)->result();
            $out->datasource=$datasource;
            $data['datasource'] = $datasource;
   
        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview14($periode,$tahun,$maker){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "PERIODE_BULAN" LIKE '.$periode.' AND "TAHUN" LIKE '.$tahun.' AND "MAKER" LIKE '.$maker.' 
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/$cfg->rowPerPage); 
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $periods.'/'. $tahuns .'/'. $makers ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "PERIODE_BULAN" LIKE '.$periode.' AND "TAHUN" LIKE '.$tahun.' AND "MAKER" LIKE '.$maker.'
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
                }
            }
 
            $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

            $db_ilcs = $this->load->database('ikt_cardom', TRUE);
            $datasource = $db_ilcs->query($query)->result();
            $out->datasource=$datasource;
            $data['datasource'] = $datasource;

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}
    public function listview15($terminal,$periode,$tahun,$maker){	

	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_zero_safety');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage); 

        $terminal = str_replace('%20',' ',$terminal);
        $maker = str_replace('%20',' ',$maker);
        $tahun = str_replace('%20',' ',$tahun);
        $periode = str_replace('%20',' ',$periode);

        $terminal = strtoupper($terminal);
        $maker = strtoupper($maker);
        $tahun = strtoupper($tahun);
        $periode = strtoupper($periode);

        $terminals = $terminal;
        $tahuns = $tahun;
        $periods = $periode;
        $makers = $maker;
        $data['terminals'] = $terminals;            
        $data['tahuns'] = $tahuns;            
        $data['periods'] = $periods;            

		$terminal = "'%$terminal%'";
		$periode = "'%$periode%'";
        $tahun = "'%$tahun%'";
        $maker = "'%$maker%'";
  
        $db_ilcs = $this->load->database('ikt_postgree', TRUE);

        $query = 'SELECT    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                            MAX("TAHUN") TAHUN,                      
                            SUM("ACCIDENT") ACCIDENT, 
                            SUM("INCIDENT") INCIDENT, 
                            SUM("UNIT_IMPACT")UNIT_IMPACT,        
                            MAX("TERMINAL") TERMINAL , 
                            MAX("MAKER") MAKER                
                        
         from "DASHBOARD_ZERO_SAFETY"       
         WHERE "TERMINAL" LIKE '.$terminal.' AND "PERIODE_BULAN" LIKE '.$periode.' AND "TAHUN" LIKE '.$tahun.' AND "MAKER" LIKE '.$maker.' 
         GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
         ORDER BY "PERIODE_BULAN" ASC      
         
        ';
         $datazero = $db_ilcs->query($query)->result_array();
         $out->datazero=$datazero;
         $data['datazero'] = $datazero;	
		
			$k1 = count($datazero); 
			$cfg->totalPage = (int) ceil ($k1/10);
            $cfg->pagingURL =   $cfg->pagingURL .'/'. $terminals.'/'. $periods .'/'. $tahuns .'/'. $makers ;

            $x = ($cfg->currPage*10)-$cfg->rowPerPage;
			for($i=0;$i<$cfg->rowPerPage;$i++){
				if ($cfg->currPage == $i){

		       $query = 'SELECT    
                    MAX("PERIODE_BULAN") PERIODE_BULAN, 
                    MAX("TAHUN") TAHUN,                      
                    SUM("ACCIDENT") ACCIDENT, 
                    SUM("INCIDENT") INCIDENT, 
                    SUM("UNIT_IMPACT")UNIT_IMPACT,        
                    MAX("TERMINAL") TERMINAL , 
                    MAX("MAKER") MAKER                
           
                    from "DASHBOARD_ZERO_SAFETY"       
                    WHERE "TERMINAL" LIKE '.$terminal.' AND "PERIODE_BULAN" LIKE '.$periode.' AND "TAHUN" LIKE '.$tahun.' AND "MAKER" LIKE '.$maker.' 
                    GROUP BY "PERIODE_BULAN", "TAHUN", "TERMINAL", "MAKER"
                    ORDER BY "PERIODE_BULAN" ASC  
					LIMIT '.$cfg->rowPerPage.'
					OFFSET '.$x.' 
				';
                }
            }

            $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

            $db_ilcs = $this->load->database('ikt_cardom', TRUE);
            $datasource = $db_ilcs->query($query)->result();
            $out->datasource=$datasource;
            $data['datasource'] = $datasource;

        // Layout Data
		$data = array(			
			
			 'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datazero' => $res->datazero,
		    'kunjung' => $res->kunjung,
            'datazero' => $datazero,
            'terminals' => $terminals,
            'tahuns' => $tahuns,          
            'periods' => $periods,            
            'makers' => $makers,
            'datasource' => $datasource
		);
		
		$this->load->view('backend/pages/tps_online/zero_safety/listview',$data);
	}

    public function view($id = NULL) {
      
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/zero_safety/view';
        }

        $db = $this->get_db();

        $mod = model('tps_online/model_zero_safety');
        $mod->set_db($db);    
        $makers = $mod->getMakers();

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {  
          
            $view = array(	
             
                'makers' => $makers,
            );
            
            $view['kunjung'] = $row;
        
            $this->load->view('backend/pages/tps_online/zero_safety/view', $view);
       
         }
    }

    public function get($token = NULL) {
        if ($this->auth->token == $token) {
            $out = new StdClass();

            $where = array(
                'PERIODE_BULAN' => post('PERIODE_BULAN')
            );

            $db = $this->get_db();

            $data = $db->select('PERIODE_BULAN, TAHUN, LQ_GATE_1_BACK_KCY, LQ_GATE_1_QUARANTINE, LQ_GATE_2, LQ_GATE_3, CARGO_DEFECT')->where($where)->get('DASHBOARD_zero_safety')->row();

            if ($data) {
                $data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
                $data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';

                $out->success = true;
                $out->datasource = $data;
            } else {
                $out->success = false;
                $out->msg = 'Tidak dapat menemukan Visit ID: ' . post('PERIODE_BULAN');
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
        $mod = model('tps_online/model_zero_safety');       

        $mod->set_db($db); 
  
        $db_ilcs = $this->load->database('ikt_cardom', TRUE);

        $query = "SELECT ID_M_BRAND,BRAND from M_BRAND_CAR";

        $datasource = $db_ilcs->query($query)->result();
        $out->datasource=$datasource;
         $data['datasource'] = $datasource;	

        $view = array(			
            'datasource' => $datasource         
        
        );
        
        $this->load->view('backend/pages/tps_online/zero_safety/new', $view);

    }

    public function finalize($id = NULL, $tahun = NULL, $terminal = NULL, $maker = NULL) {
		$num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/zero_safety/finalize';
        }

		$con = $this->load->database('ikt_postgree', TRUE);

		$out = new StdClass();
        $db = $this->get_db();
		$this->db = $this->get_db();
		$mod = model('tps_online/model_form_manual');       

		$mod->set_db($db);       

		$cfg = $mod->parseParameter($num_args, $get_args);
		 // Apply Config
		 $mod->terapkanConfig($cfg);

         $id = "'$id'";
         $tahun = "'$tahun'";
         $terminal = "'$terminal'";
         $maker = "'$maker'";
		 $databm = 'SELECT "PERIODE_BULAN","TAHUN","TERMINAL","MAKER","ACCIDENT","INCIDENT","UNIT_IMPACT","CREATED_DATE","id_zero" 
                    FROM "DASHBOARD_ZERO_SAFETY"
                    WHERE "PERIODE_BULAN"= '.$id.' AND "TAHUN"='.$tahun.' AND "TERMINAL"= '.$terminal.' AND "MAKER" = '.$maker.' ';						
							
						
		$databm = $con->query($databm)-> result();
		$out->databm=$databm;
		 $data['databm'] = $databm;	

		 $view = array(			
			'databm' => $databm,
			'grid_state' => $grid_state
		
		);
   

            $this->load->view('backend/pages/tps_online/zero_safety/finalize', $view);
  
    }

}