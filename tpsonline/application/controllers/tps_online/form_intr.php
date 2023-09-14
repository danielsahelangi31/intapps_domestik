<?php
/** Input Manual Monitoring BM Internasional
  *	Modul untuk menambahkan activity monitoring bm internasional tiap kapal dan laporan activity bm internasional
  *
  */

class form_intr extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();

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
	//  */
	public function index(){
		redirect('tps_online/form_intr/listview');
	}


	public function listview(){	

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/model_form_intr');

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
            'dataintr' => $res->dataintr,
			'kunjung' => $res->kunjung,
			'vesel' => $res->vesel
		);
	
		$this->load->view('backend/pages/tps_online/form_intr/listview',$data);
	}

	
	public function load_data_bm($token = null)
	{
		if($this->auth->token == $token){
			$db = $this->get_db();
			
			$this->load->model('tps_online/model_form');
		
			$model = $this->model_form->get_data_form();
			
			
			header('Content-Type: application/json');
			echo json_encode($model);
		}
		else{
			echo json_encode('INVALID TOKEN');	
		}
	}

	public function get_data_form()
	{
		$con = $this->load->database('ikt_postgree', TRUE);

		$dataForm = "select SHIFT, ACTIVITY, REALISASI_BONGKAR, REALISASI_MUAT, REMAINING_BONGKAR, REMAINING_MUAT
		from DASHBOARD_BM_DETAIL
		";

		$data = $con->query($dataForm);
	
		$res = array(
	                "data"  => $data->result_array()
	                
	            );

	    return $res;
	}


    public function view($id = NULL) {
      
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/form_intr/listview';
        }

        $db = $this->get_db();

        $mod = model('tps_online/model_form_intr');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {
            $view = array(			
                'TYPE_SHIFT' => $mod->select_type_shift(),
                'TYPE_ACTIVITY' => $mod->select_type_activity()	
            );
            $view['kunjung'] = $row;
			$ETB = $row->ETB;
			$ETB = str_replace(' ','T',$ETB);
			$ETA = $row->ETA;
			$ETA = str_replace(' ','T',$ETA);
			$ETD = $row->ETD;
			$ETD = str_replace(' ','T',$ETD);
			$TIME_START = $row->TIME_START;
			$TIME_START = str_replace(' ','T',$TIME_START);
			$TIME_END = $row->TIME_END;
			$TIME_END = str_replace(' ','T',$TIME_END);

			$ATB = $row->ATB;
			$ATB = str_replace(' ','T',$ATB);
			$ATA = $row->ATA;
			$ATA = str_replace(' ','T',$ATA);
			$ATD = $row->ATD;
			$ATD = str_replace(' ','T',$ATD);
			$COMMENCE = $row->COMMENCE;
			$COMMENCE = str_replace(' ','T',$COMMENCE);
			$COMPLETE = $row->COMPLETE;
			$COMPLETE = str_replace(' ','T',$COMPLETE);
			$data = array(
				'view'=>$view, 
				'TYPE_SHIFT' => $mod->select_type_shift(),
                'TYPE_ACTIVITY' => $mod->select_type_activity(),
				'ETB'=>$ETB,			
				'ETD'=>$ETD,
				'ETA'=>$ETA,
				'TIME_START'=>$TIME_START,
				'TIME_END'=>$TIME_END,
				'ATB'=>$ATB,			
				'ATD'=>$ATD,
				'ATA'=>$ATA,
				'COMMENCE'=>$COMMENCE,
				'COMPLETE'=>$COMPLETE,
			);
			$data['kunjung'] = $row;		
            $this->load->view('backend/pages/tps_online/form_intr/view', $data);
		
        } 
    }

    public function get($token = NULL) {
        if ($this->auth->token == $token) {
            $out = new StdClass();

            $where = array(
                'VESSEL_NAME' => post('VESSEL_NAME')
            );

            $db = $this->get_db();

            $data = $db->select('VESSEL_NAME, VOY_IN, ETA, ETB, ETD')->where($where)->get('STAGING_VES_VOYAGE')->row();

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

    public function finalize($id = NULL, $voyage = NULL) {
		
		$num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/form_intr/finalize';
        }

		$con = $this->load->database('ikt_postgree', TRUE);

		$out = new StdClass();
        $db = $this->get_db();
		$this->db = $this->get_db();
		$mod = model('tps_online/model_form_intr');       

		$mod->set_db($db);       

		$cfg = $mod->parseParameter($num_args, $get_args);
		 // Apply Config
		 $mod->terapkanConfig($cfg);

		 $id_fin = str_replace('%20',' ',$id);
		  $id = "'$id_fin'";
		  $voyage = "'$voyage'";

		 $databm = 'SELECT dbh."ID_MONITORING_HEADER",dbh."NAMA_KAPAL",dbh."VOYAGE",dbh."KADE_DERMAGA",dbh."PBM",dbh."RENCANA_BONGKAR",
		 			dbh."RENCANA_MUAT",dbh."ETA",dbh."ETB",dbh."ETD",dbh."ATA",dbh."ATB",dbh."ATD",dbh."COMMENCE",dbh."COMPLETE",dbh."ID_HEADER",
					dbd."ID_MONITORING_DETAIL",dbd."ID_MONITORING_HEADER",dbd."TANGGAL_TIME",dbd."SHIFT",dbd."ACTIVITY",dbd."TIME_START",dbd."TIME_END",
					dbd."REALISASI_BONGKAR",dbd."REALISASI_MUAT",dbd."WORKING_HOURBT",dbd."TOTAL_NOT",dbd."TOTAL_IT",dbd."ET",dbd."BWT",dbd."USH",dbd."ET_BT",
					dbd."nama_kapal",dbd."voyage",dbd."TERMINAL"
		 			FROM "DASHBOARD_BM_HEADER" dbh 
					inner JOIN "DASHBOARD_BM_DETAIL" dbd on dbh."ID_HEADER" = dbd."ID_MONITORING_DETAIL"
					and dbd."voyage" = dbh."VOYAGE" 
					WHERE dbh."NAMA_KAPAL"= '.$id.' 
					AND dbh."VOYAGE"='.$voyage.' 
				    ORDER BY "ID_MONITORING_DETAIL" ASC';
			
		$databm = $con->query($databm)-> result();
	
		$out->databm=$databm;
		$data['databm'] = $databm;	
	
		
		 $view = array(			
			'databm' => $databm,
			'grid_state' => $grid_state
		
		);
   

            $this->load->view('backend/pages/tps_online/form_intr/finalize', $view);
  
    }

	
	public function new($id = NULL, $voyage = NULL) {
	
			$out = new StdClass();
			$db = $this->get_db();
			$this->db = $this->get_db();
			$mod = model('tps_online/model_form_intr');       

			$mod->set_db($db);       
			$con = $this->load->database('ikt_postgree', TRUE);	     
						
			$vessel_id = str_replace('%20',' ',$id);
			$vessel_id = "'$vessel_id'";
			$voyages = $voyage;
			$voyages = "'$voyages'";
			$datasource = 'SELECT dbh."ID_MONITORING_HEADER",dbh."NAMA_KAPAL",dbh."VOYAGE",dbh."KADE_DERMAGA",dbh."PBM",dbh."RENCANA_BONGKAR",
			dbh."RENCANA_MUAT",dbh."ETA",dbh."ETB",dbh."ETD",dbh."ATA",dbh."ATB",dbh."ATD",dbh."COMMENCE",dbh."COMPLETE",dbh."ID_HEADER"			
			FROM  "DASHBOARD_BM_HEADER"	dbh
			WHERE "NAMA_KAPAL" = '.$vessel_id.' AND "VOYAGE" = '.$voyages.'
			';
			$datasource = $con->query($datasource)-> result();
			$out->datasource=$datasource;

			$x = count($datasource)-1;

			$data['datasource'][0][0][$x]= $datasource;	

			$ATAH = $datasource[$x]->ATA;
			$ATAH = str_replace(' ','T',$ATAH);
	
			$ATBH = $datasource[$x]->ATB;
			$ATBH = str_replace(' ','T',$ATBH);

			$ATDH = $datasource[$x]->ATD;
			$ATDH = str_replace(' ','T',$ATDH);
			
			$COMMENCEH = $datasource[$x]->COMMENCE;
			$COMMENCEH = str_replace(' ','T',$COMMENCEH);
				
			$COMPLETEH = $datasource[$x]->COMPLETE;
			$COMPLETEH = str_replace(' ','T',$COMPLETEH);

			$ETBH = $datasource[$x]->ETB;
			$ETBH = str_replace(' ','T',$ETBH);

			if ($ATAH == '1970-01-01T07:00:00'){
				$ATAH = NULL;
			}

			if ($ATBH == '1970-01-01T07:00:00'){
				$ATBH = NULL;
			}
	
			if ($ATDH == '1970-01-01T07:00:00'){
				$ATDH = NULL;
			}

			if ($COMMENCEH == '1970-01-01T07:00:00'){
				$COMMENCEH = NULL;
			}

			if ($COMPLETEH == '1970-01-01T07:00:00'){
				$COMPLETEH = NULL;
			}

			if ($ETBH == '1970-01-01T07:00:00'){
				$ETBH = NULL;
			}
	
			 $dataField = array('VESSEL_CODE'=>'', 'VISIT_NAME'=>'', 'VOYAGE_IN'=>'');

			 if ($row = $mod->gets($id, $voyage)) {
				$ETB = $row->ETB;
				$ETB = str_replace(' ','T',$ETB);
				$ETA = $row->ETA;
				$ETA = str_replace(' ','T',$ETA);
				$ETD = $row->ETD;
				$ETD = str_replace(' ','T',$ETD);	
				$ATB = $row->ATB;
				$ATB = str_replace(' ','T',$ATB);
				$ATA = $row->ATA;
				$ATA = str_replace(' ','T',$ATA);
				$ATD = $row->ATD;
				$ATD = str_replace(' ','T',$ATD);
				$ARRIVAL = $row->ARRIVAL;				
				$ARRIVAL = date('Y-m-d H:i',strtotime($ARRIVAL));
				$ARRIVAL = str_replace(' ','T',$ARRIVAL);		
				$OPERATIONAL = $row->OPERATIONAL;			
				$OPERATIONAL = date('Y-m-d H:i',strtotime($OPERATIONAL));
				$OPERATIONAL = str_replace(' ','T',$OPERATIONAL);

				  $view = array(			
				'datasource' => $datasource,
				'datafield' => $dataField,
                'TYPE_SHIFT' => $mod->select_type_shift(),			
                'TYPE_ACTIVITY' => $mod->select_type_activity()	
            );
       
			$this->db->where("VESSEL_NAME",$VESSEL_NAME);
			$VESSEL_NAME = '';

				$data = array(
					'view'=>$view, 
					'TYPE_SHIFT' => $mod->select_type_shift(),
					'TYPE_ACTIVITY' => $mod->select_type_activity(),
					'ETB'=>$ETB,			
					'ETD'=>$ETD,
					'ETA'=>$ETA,			
					'ATB'=>$ATB,			
					'ATD'=>$ATD,
					'ATA'=>$ATA,
					'ARRIVAL'=>$ARRIVAL,
					'OPERATIONAL'=>$OPERATIONAL,
					'VESSEL_NAME'=>$VESSEL_NAME,
					'datasource' => $datasource,
					'ATAH'=>$ATAH,
					'ATBH'=>$ATBH,			
					'ATDH'=>$ATDH,
					'COMPLETEH'=>$COMPLETEH,
					'COMMENCEH'=>$COMMENCEH,
					'ETBH'=>$ETBH,	
			
				);
				$data['vesel'] = $row;
			
            $this->load->view('backend/pages/tps_online/form_intr/new', $data);
			 }
    }

	public function neww() {
	
		$out = new StdClass();
		$db = $this->get_db();
		$this->db = $this->get_db();
		$mod = model('tps_online/model_form_intr');       

		$mod->set_db($db);       

		$vesse_id = str_replace('%20',' ',$_REQUEST['id']);
		$voyage = $_REQUEST['voyage'];

		 // // Content Data
		 $datasource_modal =  $this->db->select('VISIT_ID, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, VESSEL_CODE, ETA')
		 ->order_by("VESSEL_CODE", "ASC")
		 ->where('VESSEL_CODE IS NOT NULL')
		 ->where('ETA IS NOT NULL')
		 ->get('STAGING_CARTOS_SHIP_VISIT')->result();

		 $datasource = $db->select('VISIT_ID, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, VESSEL_CODE, ETA')
		 ->where('VISIT_NAME',$vesse_id)
		 ->where('VOYAGE_IN',$voyage)
		 ->where('VESSEL_CODE IS NOT NULL')
		 ->where('ETA IS NOT NULL')
		 ->get('STAGING_CARTOS_SHIP_VISIT')->result();
	 
		 $dataField = array('VISIT_ID'=>'', ' VISIT_NAME'=>'', 'VOYAGE_IN'=>'', 'VOYAGE_OUT'=>'', 'VESSEL_CODE'=>'');
 
		 $view = array(			
			 'datasource' => $datasource_modal,
			 'datafield'	=> $datasource[0],
			 'TYPE_SHIFT' => $mod->select_type_shift(),		
			 'TYPE_ACTIVITY' => $mod->select_type_activity()
	 
		 );
	
		 $this->db->where("VISIT_NAME",$vesse_id);

		 $data= array('view'=>$view, 'VISIT_NAME'=>$vesse_id);
 

		$this->load->view('backend/pages/tps_online/form_intr/new', $data);
	
}

	public function gett($token = NULL){
		if($this->auth->token == $token){
			$out = new StdClass();
			
			$where = array(
				'VESSEL_NAME' => post('VESSEL_NAME')
			);
			
			$db = $this->get_db();
			
			$data = $db->select('ID_VESSEL, VESSEL_NAME, VOYAGE, PBM, ID_KADE')->where($where)->get('STAGING_VES_VOYAGE')->row();
			
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

    public function export_form_xls($id,$voyage)
	{
	
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Laporan Monitoring Bongkar Muat')					
								   ->setTitle("Laporan Monitoring")
								   ->setSubject("Monitoring Bongkar Muat'")
								   ->setDescription("Laporan Data Monitoring ")
								   ->setKeywords("Data Bongkar Muat");
		
			// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
			
			$style_col = array(
				'font' => array('bold' => true), // Set font nya jadi bold
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
				),
				'borders' => array(
					'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
					'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
					'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
					'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
				)
			);

			// Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
			$style_row = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
				),
				'borders' => array(
					'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
					'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
					'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
					'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
				)
			);
	
			$style = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				)
			);

			$border_style= array('borders' => array('right' => array('style' => 
			PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => '766f6e'),)));
		
	
			// Buat header tabel nya pada baris ke 3
			$excel->setActiveSheetIndex(0)->mergeCells('A1:I1')->setCellValue('A1', "MONITORING BONGKAR/MUAT KAPAL"); // Set kolom A3 dengan tulisan "NO"
			$excel->setActiveSheetIndex(0)->setCellValue('A3', "NAMA KAPAL"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A4', "VOYAGE"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A5', "KADE/DERMAGA"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A6', "RENCANA BONGKAR"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A7', "RENCANA MUAT"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A8', "TOTAL RENCANA BM"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A9', "PBM"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A10', "ETA"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A11', "ETB"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A12', "ETD"); 
			$excel->setActiveSheetIndex(0)->setCellValue('A13', "COMMENCE"); 
			$excel->setActiveSheetIndex(0)->mergeCells('A34:E34')->setCellValue('A34', "SUMMARY SHIFT"); 
			$excel->setActiveSheetIndex(0)->setCellValue('D10', "ATA"); 
			$excel->setActiveSheetIndex(0)->setCellValue('D11', "ATB"); 
			$excel->setActiveSheetIndex(0)->setCellValue('D12', "ATD"); 
			$excel->setActiveSheetIndex(0)->setCellValue('D13', "COMPLETE"); 

			$excel->setActiveSheetIndex(0)->mergeCells('A17:A18')->setCellValue('A17', "TANGGAL"); 
			$excel->setActiveSheetIndex(0)->mergeCells('B17:B18')->setCellValue('B17', "SHIFT"); 
	
			$excel->setActiveSheetIndex(0)->mergeCells('C17:D17')->setCellValue('C17', "WAKTU");
			$excel->setActiveSheetIndex(0)->setCellValue('C18', "START"); 		
			$excel->setActiveSheetIndex(0)->setCellValue('D18', "END"); 

			$excel->setActiveSheetIndex(0)->mergeCells('E17:E18')->setCellValue('E17', "ACTIVITY"); 
			$excel->setActiveSheetIndex(0)->mergeCells('F17:F18')->setCellValue('F17', "WORKING_HOUR(BT)"); 
			
			$excel->setActiveSheetIndex(0)->setCellValue('G17', "NOT"); 
			$excel->setActiveSheetIndex(0)->setCellValue('H17', "IT"); 
			$excel->setActiveSheetIndex(0)->setCellValue('G18', "TOTAL NOT");  
			$excel->setActiveSheetIndex(0)->setCellValue('H18', "TOTAL IT"); 
			$excel->setActiveSheetIndex(0)->mergeCells('I17:I18')->setCellValue('I17', "ET"); 
			$excel->setActiveSheetIndex(0)->mergeCells('J17:J18')->setCellValue('J17', "BWT"); 
			$excel->setActiveSheetIndex(0)->mergeCells('K17:L17')->setCellValue('K17', "REALISASI"); 	
			$excel->setActiveSheetIndex(0)->setCellValue('K18', "BONGKAR"); 		
			$excel->setActiveSheetIndex(0)->setCellValue('L18', "MUAT");  
			$excel->setActiveSheetIndex(0)->mergeCells('M17:N17')->setCellValue('M17', "REMAINING"); 
			$excel->setActiveSheetIndex(0)->setCellValue('M18', "BONGKAR"); 		
			$excel->setActiveSheetIndex(0)->setCellValue('N18', "MUAT"); 
			$excel->setActiveSheetIndex(0)->mergeCells('O17:O18')->setCellValue('O17', "TOTAL"); 
			$excel->setActiveSheetIndex(0)->mergeCells('P17:P18')->setCellValue('P17', "USH"); 
			$excel->setActiveSheetIndex(0)->mergeCells('Q17:Q18')->setCellValue('Q17', "USH(GROSS)"); 
			$excel->setActiveSheetIndex(0)->mergeCells('R17:R18')->setCellValue('R17', "BT(Progress)"); 
			$excel->setActiveSheetIndex(0)->mergeCells('S17:S18')->setCellValue('S17', "ET/BT(%)");
		
			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); 
			$excel->getActiveSheet()->getStyle('B3:B1000')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('E10:E1000')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('A1:A18')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('D10:D13')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A1:A18')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A17:Z17')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A18:Z18')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('A34:E34')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A34:S34')->applyFromArray($style_row, $style_col);;
			$excel->getActiveSheet()->getStyle('A17:A18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('B17:B18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('C17:C18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('D17:D18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('E17:E18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('F17:F18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('G17:G18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('H17:H18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('I17:I18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('J17:J18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('K17:K18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('L17:L18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('M17:M18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('N17:N18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('O17:O18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('P17:P18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('Q17:Q18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('R17:R18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('S17:S18')->applyFromArray($style_col);
			
			$excel->getActiveSheet()->getStyle('A17:S17')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('A18:S18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('A17:A18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('B17:B18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('C17:C18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('D17:D18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('E17:E18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('F17:F18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('G17:G18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('H17:H18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('I17:I18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('J17:J18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('K17:K18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('L17:L18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('M17:M18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('N17:N18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('O17:O18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('P17:P18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('Q17:Q18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('R17:R18')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('S17:S18')->applyFromArray($style_col);

	
			$excel->getActiveSheet()->getStyle('A19:A50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('B19:B50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('C19:C50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('D19:D50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('E19:E50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('F19:F50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('G19:G50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H19:H50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('I19:I50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('J19:J50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('K19:K50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('L19:L50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('M19:M50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('N19:N50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('O19:O50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('P19:P50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('Q19:Q50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('R19:R50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('S19:S50')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('I19:I33')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
			$excel->getActiveSheet()->getStyle('P19:P33')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER );
			$excel->getActiveSheet()->getStyle('Q19:Q33')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_NUMBER );
			$excel->getActiveSheet()->getStyle('R19:R33')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
		
			$excel->getActiveSheet()->getStyle('J19:J33')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
			$excel->getActiveSheet()->getStyle('S19:S33')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);
			$excel->getActiveSheet()->getStyle('F34')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
			$excel->getActiveSheet()->getStyle('G34')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
			$excel->getActiveSheet()->getStyle('H34')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
			$excel->getActiveSheet()->getStyle('I34')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
			$excel->getActiveSheet()->getStyle('J34')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_DATE_TIME3 );
		

			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('tps_online/Model_form');
			

			$id_fin = str_replace('%20',' ',$id);
			$id = "'$id_fin'";
			$voyage = "'$voyage'";
			$model = $this->Model_form->get_data_form($id, $voyage);

			// $no = 1; // Untuk penomoran tabel, di awal set dengan 1
			$numrow = 19; // Set baris pertama untuk isi tabel adalah baris ke 4
			$numrows = 20;
			$row = 18;
			$rows = 19;
			$BTT =  date('H:i', strtotime(($model['data'][0]['WORKING_HOURBT'])));
			$BTPP = date('H:i', strtotime(($model['data'][0]['BT'])));
			$BTP = date('H:i', strtotime(($model['data'][1]['BT'])));
	
			foreach($model['data'] as $data){ // Lakukan looping pada variabel siswa
			
				$BT = date('H:i', strtotime($data['WORKING_HOURBT']));
				$BWT = date('H:i', strtotime($data['BWT']));
				$ET = date('H:i', strtotime($data['ET']));
				$TIME_START = date('H:i', strtotime($data['TIME_START']));
				$TIME_END = date('H:i', strtotime($data['TIME_END']));
				$TANGGAL = date('d M Y', strtotime($data['TANGGAL_TIME']));
				$TOTAL_NOT= date('H:i', strtotime($data['TOTAL_NOT']));
				$TOTAL_IT = date('H:i', strtotime($data['TOTAL_IT']));
				$BTPRO = date('H:i', strtotime($data['BT']));
				
				$ET_BT = $data['ET_BT']/100;		
	
				if ($data['SHIFT'] == '' || $data['SHIFT'] == NULL){
					$data['SHIFT'] = '-';
				} 

				if ($data['ATD'] == '1970-01-01 07:00:00'){
					$data['ATD'] = '-';
				}
				if ($data['ATB'] == '1970-01-01 07:00:00'){
					$data['ATB'] = '-';
				}

				if ($data['ATA'] == '1970-01-01 07:00:00'){
					$data['ATA'] = '-';
				}

				if ($data['ETA'] == '1970-01-01 07:00:00'){
					$data['ETA'] = '-';
				}

				if ($data['ETB'] == '1970-01-01 07:00:00'){
					$data['ETB'] = '-';
				}
				if ($data['ETD'] == '1970-01-01 07:00:00'){
					$data['ETD'] = '-';
				}
				if ($data['COMMENCE'] == '1970-01-01 07:00:00'){
					$data['COMMENCE'] = '-';
				}

				if ($data['COMPLETE'] == '1970-01-01 07:00:00'){
					$data['COMPLETE'] = '-';
				}

				$excel->setActiveSheetIndex(0)->setCellValue('B3', $data['NAMA_KAPAL']);
				$excel->setActiveSheetIndex(0)->setCellValue('B4', $data['VOYAGE']);	
				$excel->setActiveSheetIndex(0)->setCellValue('B5', $data['KADE_DERMAGA']);
				$excel->setActiveSheetIndex(0)->setCellValue('B6', $data['RENCANA_BONGKAR']);		
				$excel->setActiveSheetIndex(0)->setCellValue('B7', $data['RENCANA_MUAT']);	
				$excel->setActiveSheetIndex(0)->setCellValue('B8', $data['TOTAL_BM']);
				$excel->setActiveSheetIndex(0)->setCellValue('B9', '-');	
				$excel->setActiveSheetIndex(0)->setCellValue('B10', $data['ETA']);
				$excel->setActiveSheetIndex(0)->setCellValue('B11', $data['ETB']);	
				$excel->setActiveSheetIndex(0)->setCellValue('B12', $data['ETD']);
				$excel->setActiveSheetIndex(0)->setCellValue('B13', $data['COMMENCE']);
				$excel->setActiveSheetIndex(0)->setCellValue('E10', $data['ATA']);
				$excel->setActiveSheetIndex(0)->setCellValue('E11', $data['ATB']);	
				$excel->setActiveSheetIndex(0)->setCellValue('E12', $data['ATD']);
				$excel->setActiveSheetIndex(0)->setCellValue('E13', $data['COMPLETE']);

				$excel->setActiveSheetIndex(0)->setCellValue('M19', $data['RENCANA_BONGKAR']);
				$excel->setActiveSheetIndex(0)->setCellValue('N19', $data['RENCANA_MUAT']);

				$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $TANGGAL);		
				$excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $data['SHIFT']);		
				$excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $TIME_START);	
				$excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $TIME_END);	
				$excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $data['ACTIVITY']);
				$excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $BT);
				$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $TOTAL_NOT); 
				$excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $TOTAL_IT); 

				$excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, $ET);
				$excel->setActiveSheetIndex(0)->setCellValue('J'.$numrow, $BWT); 
                $excel->setActiveSheetIndex(0)->setCellValue('K'.$numrow, $data['REALISASI_BONGKAR']);
                $excel->setActiveSheetIndex(0)->setCellValue('L'.$numrow, $data['REALISASI_MUAT']);
				$excel->setActiveSheetIndex(0)->setCellValue('M'.$numrow, $data['REMAINING_BONGKAR']);
                $excel->setActiveSheetIndex(0)->setCellValue('N'.$numrow, $data['REMAINING_MUAT']);
		
				$excel->setActiveSheetIndex(0)->setCellValue('O'.$numrow, $data['TOTAL']);
				if($data['TOTAL'] == '0' || $ET == '00:00'){
					$excel->setActiveSheetIndex(0)->setCellValue('P'.$numrow, '0');
					$excel->setActiveSheetIndex(0)->setCellValue('Q'.$numrow, '0');
				} else {
					$excel->setActiveSheetIndex(0)->setCellValue('P'.$numrow, '=O'.$rows.'/(I'.$rows.'* 24)');
					$excel->setActiveSheetIndex(0)->setCellValue('Q'.$numrow, '=O'.$rows.'/(J'.$rows.'* 24)');
				}
				$excel->setActiveSheetIndex(0)->setCellValue('R'.$numrow, $BTPRO);
				$excel->setActiveSheetIndex(0)->setCellValue('S'.$numrow, $ET_BT); 

				$excel->setActiveSheetIndex(0)->setCellValue('F34', '=F19+F20+F21+F22+F23+F24+F25+F26+F27+F28+F29+F30+F31+F32+F33');
				$excel->setActiveSheetIndex(0)->setCellValue('G34', '=G19+G20+G21+G22+G23+G24+G25+G26+G27+G28+G29+G30+G31+G32+G33');
				$excel->setActiveSheetIndex(0)->setCellValue('H34', '=H19+H20+H21+H22+H23+H24+H25+H26+H27+H28+H29+H30+H31+H32+H33');
				$excel->setActiveSheetIndex(0)->setCellValue('I34', '=I19+I20+I21+I22+I23+I24+I25+I26+I27+I28+I29+I30+I31+I32+I33');
				$excel->setActiveSheetIndex(0)->setCellValue('J34', '=J19+J20+J21+J22+J23+J24+J25+J26+J27+J28+J29+J30+J31+J32+J33');
			

				$excel->setActiveSheetIndex(0)->setCellValue('C6', "Unit");
				$excel->setActiveSheetIndex(0)->setCellValue('C7', "Unit");	
				$excel->setActiveSheetIndex(0)->setCellValue('C8', "Unit");
				$excel->setActiveSheetIndex(0)->setCellValue('C10', "WIB");
				$excel->setActiveSheetIndex(0)->setCellValue('C11', "WIB");	
				$excel->setActiveSheetIndex(0)->setCellValue('C12', "WIB");
				$excel->setActiveSheetIndex(0)->setCellValue('C13', "WIB");
				$excel->setActiveSheetIndex(0)->setCellValue('F10', "WIB");
				$excel->setActiveSheetIndex(0)->setCellValue('F11', "WIB");	
				$excel->setActiveSheetIndex(0)->setCellValue('F12', "WIB");
				$excel->setActiveSheetIndex(0)->setCellValue('F13', "WIB");
						
				// $no++; // Tambah 1 setiap kali looping
				$numrow++; // Tambah 1 setiap kali looping
				$numrows++; 
				$row++;
				$rows++;
			}

			// Set width kolom
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(20); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(20); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(20); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(20); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('E')->setWidth(20); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('F')->setWidth(20); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('G')->setWidth(20); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('H')->setWidth(20); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('I')->setWidth(20); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('J')->setWidth(20); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('K')->setWidth(20); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('L')->setWidth(20); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('M')->setWidth(20); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('N')->setWidth(20); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('O')->setWidth(20); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
            $excel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
			$excel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
			
			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("MONITORING_BONGKAR_MUAT");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="BM_MONITORING_INTR_'.$id.'_'.$voyage.'.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->save('php://output');
		


	}
}