<?php
/** Laporan Trafik/Arus Kedatangan Kapal
  *	Modul untuk mengunduh laporan trafik/arus kedatangan Kapal berdasarkan tahun dan terminal
  *
  */

class lap_trafik_kapal extends CI_Controller{
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
		redirect('tps_online/lap_trafik_kapal/listview');
	}


	public function listview(){	
	
		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_lap_trafik_kapal');

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
            'datalap' => $res->datalap,
			'dataplan' => $res->dataplan,
			'kunjung' => $res->kunjung
		);

		$this->load->view('backend/pages/tps_online/lap_trafik_kapal/listview',$data);
	}



    public function view($id = NULL) {
      
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/laporan/listview';
        }

        $db = $this->get_db();

        $mod = model('tps_online/Model_berthing');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {      
		    $atd =$row -> ATD; 

            $view = array(			
                
            );
            $view['kunjung'] = $row;
            $this->load->view('backend/pages/tps_online/laporan/view', $view);
         } else {
            redirect('tps_online/form/listview/404');
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
			$mod = model('tps_online/Model_berthing');       
			$con = $this->load->database('ikt_postgree', TRUE);
			$mod->set_db($db);    

			$datasource = 'SELECT  "VISIT_ID", 
					"VISIT_NAME", 
					"VOYAGE_IN",
					"VOYAGE_OUT", 
					"VESSEL_CODE"
					
			FROM  "STAGING_CARTOS_SHIP_VISIT"
			WHERE "VISIT_NAME" IS NOT NULL;
			';
			$datasource = $con->query($datasource)-> result();
			$out->datasource=$datasource;
	
			$data['datasource'] = $datasource;	
			 $dataField = array('VISIT_ID'=>'', ' VISIT_NAME'=>'', 'VOYAGE_IN'=>'', 'VOYAGE_OUT'=>'', 'VESSEL_CODE'=>'');

			
            $view = array(			
				'datasource' => $datasource,
				'datafield' => $dataField    
            );
       
			$this->db->where("VISIT_NAME",$VISIT_NAME);
			$VISIT_NAME = '';
			$data = array('view'=>$view, 'VISIT_NAME'=>$VESSEL_NAME);

			
            $this->load->view('backend/pages/tps_online/laporan/new', $data);
		
    }

	public function neww() {
	
		$out = new StdClass();
		$db = $this->get_db();
		$this->db = $this->get_db();
		$mod = model('tps_online/Model_berthing');       

		$mod->set_db($db);       

		$vesse_id = str_replace('%20',' ',$_REQUEST['id']);
		$voyage = $_REQUEST['voyage'];

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

	public function export_laporan_xls($id,$end)
	{

			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Laporan_Trafik_Kedatangan_Kapal')						
								   ->setTitle("Laporan_Trafik_Kedatangan_Kapal")
								   ->setSubject("Laporan_Trafik_Kedatangan_Kapal")
								   ->setDescription("Laporan_Trafik_Kedatangan_Kapal")
								   ->setKeywords("Data_Trafik_Kedatangan_Kapal");
		
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
			$excel->setActiveSheetIndex(0)->mergeCells('A1:A2')->setCellValue('A1', "No"); // Set kolom A3 dengan tulisan "NO"
			$excel->setActiveSheetIndex(0)->mergeCells('B1:B2')->setCellValue('B1', "");
			$excel->setActiveSheetIndex(0)->mergeCells('C1:C2')->setCellValue('C2', "");
			$excel->setActiveSheetIndex(0)->mergeCells('B1:C1')->setCellValue('C1', "Uraian");
			$excel->setActiveSheetIndex(0)->mergeCells('B2:C2')->setCellValue('B2', "");	

			$excel->setActiveSheetIndex(0)->mergeCells('D1:D2')->setCellValue('D1', "Satuan");
			$excel->setActiveSheetIndex(0)->mergeCells('E1:P1')->setCellValue('E1', "REALISASI PERIODE BERJALAN IKT");
			$excel->setActiveSheetIndex(0)->setCellValue('A3', "1");
			$excel->setActiveSheetIndex(0)->mergeCells('B3:C3')->setCellValue('B3', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('D3', "3");
			$excel->setActiveSheetIndex(0)->mergeCells('E3:P3')->setCellValue('E3', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('E2', "Januari"); 
			$excel->setActiveSheetIndex(0)->setCellValue('F2', "Februari"); 
			$excel->setActiveSheetIndex(0)->setCellValue('G2', "Maret"); 
			$excel->setActiveSheetIndex(0)->setCellValue('H2', "April"); 
			$excel->setActiveSheetIndex(0)->setCellValue('I2', "Mei"); 
			$excel->setActiveSheetIndex(0)->setCellValue('J2', "Juni"); 
			$excel->setActiveSheetIndex(0)->setCellValue('K2', "Juli"); 
			$excel->setActiveSheetIndex(0)->setCellValue('L2', "Agustus"); 
			$excel->setActiveSheetIndex(0)->setCellValue('M2', "September"); 
			$excel->setActiveSheetIndex(0)->setCellValue('N2', "Oktober"); 
			$excel->setActiveSheetIndex(0)->setCellValue('O2', "November"); 
			$excel->setActiveSheetIndex(0)->setCellValue('P2', "Desember"); 
			$excel->setActiveSheetIndex(0)->mergeCells('Q1:Q2')->setCellValue('Q1', "Realisasi Tahun 2022");
			$excel->setActiveSheetIndex(0)->mergeCells('R1:R2')->setCellValue('R1', "Realisasi Tahun 2021");
			$excel->setActiveSheetIndex(0)->mergeCells('S1:S2')->setCellValue('S1', "RKAP Tahun 2022");
			$excel->setActiveSheetIndex(0)->mergeCells('B4:C4')->setCellValue('B4', "KAPAL");
			$excel->setActiveSheetIndex(0)->setCellValue('A5', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A7', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A9', "3");
	
			$excel->setActiveSheetIndex(0)->setCellValue('C5', "RORO");
	
			$excel->setActiveSheetIndex(0)->setCellValue('C7', "TONGKANG");
			$excel->setActiveSheetIndex(0)->setCellValue('C9', "LAINNYA");
			$excel->setActiveSheetIndex(0)->setCellValue('D5', "Call");

			$excel->setActiveSheetIndex(0)->setCellValue('D6', "GT");
			$excel->setActiveSheetIndex(0)->setCellValue('D7', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D8', "GT");

			$excel->setActiveSheetIndex(0)->setCellValue('D9', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D10', "GT");
			$excel->setActiveSheetIndex(0)->mergeCells('A16:C16')->setCellValue('A16', "JUMLAH KUNJUNGAN KAPAL");
	
			$excel->setActiveSheetIndex(0)->setCellValue('D16', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D17', "GT");

			$excel->getActiveSheet()->getStyle('A16')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(11); 
			$excel->getActiveSheet()->getStyle('A1:Q1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('A2:Q2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A16:S16')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A17:S17')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('Q1:Q2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('R1:R2')->getFont()->setBold(true);			
			$excel->getActiveSheet()->getStyle('S1:S2')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A1:A17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('B1:B17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('C1:C17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('D1:D17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('E1:E17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('F1:F17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('G1:G17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H1:H17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('I1:I17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('J1:J17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('K1:K17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('L1:L17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('M1:M17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('N1:N17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('O1:O17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('P1:P17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('Q1:Q17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('R1:R17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('S1:S17')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('A3:S3')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A4:S4')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A5:S5')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A6:S6')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A7:S7')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A8:S8')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A9:S9')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A10:S10')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A11:S11')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A12:S12')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A13:S13')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A14:S14')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A15:S15')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A16:S16')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A17:S17')->applyFromArray($style_row);
	

			$excel->getActiveSheet()->getStyle('A16:S16')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A17:S17')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('E5:Z5')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E6:Z6')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E7:Z7')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");			
			$excel->getActiveSheet()->getStyle('E8:Z8')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E9:Z9')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E10:Z10')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E11:Z11')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E12:Z12')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E13:Z13')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E14:Z14')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E15:Z15')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E16:Z16')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E17:Z17')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E18:Z18')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E19:Z19')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");


			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('tps_online/Model_lap_trafik_kapal');
			$con = $this->load->database('ikt_postgree', TRUE);
	
			$model = $this->Model_lap_trafik_kapal->get_data_laporan($id,$end);		

			$cont = count($model['data']);
			$x = 0;
			while($x < $cont) {
				$PERIODE = $model["data"][$x]['periode'];		
				 $x++;			

			$TAHUN = explode('-', $PERIODE);
			$YEAR = $TAHUN[0];
			$OLD = $YEAR - 1; 
			
			
			if (!empty($YEAR)) {
			$excel->setActiveSheetIndex(0)->mergeCells('Q1:Q2')->setCellValue('Q1', "Realisasi Tahun $YEAR");
			$excel->setActiveSheetIndex(0)->mergeCells('R1:R2')->setCellValue('R1', "Realisasi Tahun $OLD");
			$excel->setActiveSheetIndex(0)->mergeCells('S1:S2')->setCellValue('S1', "RKAP Tahun $YEAR");
			}

			$y1 = "$YEAR-01";	
			$y2 = "$YEAR-12";	
			$old = "'$y1'";
			$ago = "'$y2'";
			$dates = "'yyyy-mm'";
			$dataKapal = 'SELECT mk."JN_KAPAL", count (mk."NM_KAPAL")
			FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
			WHERE to_char(mtk."PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.'
			group by mk."JN_KAPAL"
			';
			$data = $con->query($dataKapal)-> result_array();

			for($i=0;$i<count($data);$i++){
			if ($data[$i]['JN_KAPAL'] == '09'){							
					$excel->setActiveSheetIndex(0)->setCellValue('C11', "LCT");
					$excel->setActiveSheetIndex(0)->setCellValue('D11', "Call");
					$excel->setActiveSheetIndex(0)->setCellValue('D12', "GT");
			} else if(empty($data[$i]['JN_KAPAL'])) {
				$excel->setActiveSheetIndex(0)->setCellValue('C11', "");
				$excel->setActiveSheetIndex(0)->setCellValue('D11', "");
				$excel->setActiveSheetIndex(0)->setCellValue('D12', "");
			}

			if ($data[$i]['JN_KAPAL'] == '01'){							
				$excel->setActiveSheetIndex(0)->setCellValue('C13', "CARGO");
				$excel->setActiveSheetIndex(0)->setCellValue('D13', "Call");
				$excel->setActiveSheetIndex(0)->setCellValue('D14', "GT");
			} else if(empty($data[$i]['JN_KAPAL'])) {
				$excel->setActiveSheetIndex(0)->setCellValue('C13', "");
				$excel->setActiveSheetIndex(0)->setCellValue('D13', "");
				$excel->setActiveSheetIndex(0)->setCellValue('D14', "");
			}
		}
			$tp = "'5TP1'";
			$tp3 = "'5TP3'";
			$tp4 = "'5TP4'";
			$expr = "'EXPR'";
			$tp5 = "'5TP5'";
			$roro = "'10'";
			$lct = "'09'";
			$tongkang =  "'08'";
			$lain = "'23'";
			$cargo = "'01'";
			$penumpang = "'02'";
			$dates = "'yyyy-mm'";
			$dom = "'DOM'";
			$rkap = "'RKAP'";

			if ($PERIODE == ''.$YEAR.'-01'){				
		
				$bulan1 = 'Januari';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data1 = $con->query($dataRoro)-> result_array();
				if ($data1){							
				$RORO1 = $data1[0]['count'];
				} else if (empty($data1))  {		
				$RORO1 = 0;
				}
				
				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data1 = $con->query($dataTongkang)-> result_array();
				if ($data1){							
				$TONGKANG1 = $data1[0]['count'];				
				} else if (empty($data1))  {		
				$TONGKANG1 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data1 = $con->query($dataLain)-> result_array();
				if ($data1){							
				$LAIN1 = $data1[0]['count'];				
				} else if (empty($data1))  {		
				$LAIN1 = 0;
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data1 = $con->query($dataLCT)-> result_array();
				if ($data1){							
				$LCT1 = $data1[0]['count'];				
				} else if (empty($data1))  {		
				$LCT1 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data1 = $con->query($dataCargo)-> result_array();
				if ($data1){							
				$CARGO1 = $data1[0]['count'];				
				} else if (empty($data1))  {		
				$CARGO1 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data1 = $con->query($dataRorogt)-> result_array();
				if ($data1){							
				$RORO_GT1 = $data1[0]['sum'];
				} else if (empty($data1))  {		
				$RORO_GT1 = 0;
				}

				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data1 = $con->query($dataTongkanggt)-> result_array();
				if ($data1){							
				$TONGKANG_GT1 = $data1[0]['sum'];
				} else if (empty($data1))  {		
				$TONGKANG_GT1 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data1 = $con->query($dataLaingt)-> result_array();
				if ($data1){							
				$LAIN_GT1 = $data1[0]['sum'];
				} else if (empty($data1))  {		
				$LAIN_GT1 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data1 = $con->query($dataLctgt)-> result_array();
				if ($data1){							
				$LCT_GT1 = $data1[0]['sum'];
				} else if (empty($data1))  {		
				$LCT_GT1 = 0;
				}

				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data1 = $con->query($dataCargogt)-> result_array();
				if ($data1){							
				$CARGO_GT1 = $data1[0]['sum'];
				} else if (empty($data1))  {		
				$CARGO_GT1 = 0;
				}
	
			}
	
			if ($PERIODE == ''.$YEAR.'-02'){			
		
				$bulan2 = 'Februari';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data2 = $con->query($dataRoro)-> result_array();
				if ($data2){							
				$RORO2 = $data2[0]['count'];
				} else if (empty($data2))  {		
				$RORO2 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data2 = $con->query($dataLCT)-> result_array();
				if ($data2){							
				$LCT2 = $data2[0]['count'];				
				} else if (empty($data2))  {		
				$LCT2 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data2 = $con->query($dataCargo)-> result_array();
				if ($data2){							
				$CARGO2 = $data2[0]['count'];				
				} else if (empty($data2))  {		
				$CARGO2 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data2 = $con->query($dataRorogt)-> result_array();
				if ($data2){							
				$RORO_GT2 = $data2[0]['sum'];
				} else if (empty($data2))  {		
				$RORO_GT2 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data2 = $con->query($dataLctgt)-> result_array();
				if ($data2){							
				$LCT_GT2 = $data2[0]['sum'];
				} else if (empty($data2))  {		
				$LCT_GT2 = 0;
				}

				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data2 = $con->query($dataCargogt)-> result_array();
				if ($data2){							
				$CARGO_GT2 = $data2[0]['sum'];
				} else if (empty($data2))  {		
				$CARGO_GT2 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data2 = $con->query($dataTongkang)-> result_array();
				if ($data2){							
				$TONGKANG2 = $data2[0]['count'];				
				} else if (empty($data2))  {		
				$TONGKANG2 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data2 = $con->query($dataLain)-> result_array();
				if ($data2){							
				$LAIN2 = $data2[0]['count'];				
				} else if (empty($data2))  {		
				$LAIN2 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data2 = $con->query($dataTongkanggt)-> result_array();
				if ($data2){							
				$TONGKANG_GT2 = $data2[0]['sum'];
				} else if (empty($data2))  {		
				$TONGKANG_GT2 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data2 = $con->query($dataLaingt)-> result_array();
				if ($data2){							
				$LAIN_GT2 = $data2[0]['sum'];
				} else if (empty($data2))  {		
				$LAIN_GT2 = 0;
				}

			}

			if ($PERIODE == ''.$YEAR.'-03'){
			
				$bulan3 = 'Maret';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data3 = $con->query($dataRoro)-> result_array();
				if ($data3){							
				$RORO3 = $data3[0]['count'];
				} else if (empty($data3))  {		
				$RORO3 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data3 = $con->query($dataLCT)-> result_array();
				if ($data3){							
				$LCT3 = $data3[0]['count'];				
				} else if (empty($data3))  {		
				$LCT3 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data3 = $con->query($dataCargo)-> result_array();
				if ($data3){							
				$CARGO3 = $data3[0]['count'];				
				} else if (empty($data3))  {		
				$CARGO3 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data3 = $con->query($dataRorogt)-> result_array();
				if ($data3){							
				$RORO_GT3 = $data3[0]['sum'];
				} else if (empty($data3))  {		
				$RORO_GT3 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data3 = $con->query($dataLctgt)-> result_array();
				if ($data3){							
				$LCT_GT3 = $data3[0]['sum'];
				} else if (empty($data3))  {		
				$LCT_GT3 = 0;
				}
				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data3 = $con->query($dataCargogt)-> result_array();
				if ($data3){							
				$CARGO_GT3 = $data3[0]['sum'];
				} else if (empty($data3))  {		
				$CARGO_GT3 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data3 = $con->query($dataTongkang)-> result_array();
				if ($data3){							
				$TONGKANG3 = $data3[0]['count'];				
				} else if (empty($data3))  {		
				$TONGKANG3 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data3 = $con->query($dataLain)-> result_array();
				if ($data3){							
				$LAIN3 = $data3[0]['count'];				
				} else if (empty($data3))  {		
				$LAIN3 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data3 = $con->query($dataTongkanggt)-> result_array();
				if ($data3){							
				$TONGKANG_GT3 = $data3[0]['sum'];
				} else if (empty($data3))  {		
				$TONGKANG_GT3 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data3 = $con->query($dataLaingt)-> result_array();
				if ($data3){							
				$LAIN_GT3 = $data3[0]['sum'];
				} else if (empty($data3))  {		
				$LAIN_GT3 = 0;
				}

			} 

			if ($PERIODE == ''.$YEAR.'-04'){	
			
				$bulan4 = 'April';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data4 = $con->query($dataRoro)-> result_array();
				if ($data4){							
				$RORO4 = $data4[0]['count'];
				} else if (empty($data4))  {		
				$RORO4 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data4 = $con->query($dataLCT)-> result_array();
				if ($data4){							
				$LCT4 = $data4[0]['count'];				
				} else if (empty($data4))  {		
				$LCT4 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data4 = $con->query($dataCargo)-> result_array();
				if ($data4){							
				$CARGO4 = $data4[0]['count'];				
				} else if (empty($data4))  {		
				$CARGO4 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data4 = $con->query($dataRorogt)-> result_array();
				if ($data4){							
				$RORO_GT4 = $data4[0]['sum'];
				} else if (empty($data4))  {		
				$RORO_GT4 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data4 = $con->query($dataLctgt)-> result_array();
				if ($data4){							
				$LCT_GT4 = $data4[0]['sum'];
				} else if (empty($data4))  {		
				$LCT_GT4 = 0;
				}

				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data4 = $con->query($dataCargogt)-> result_array();
				if ($data4){							
				$CARGO_GT4 = $data4[0]['sum'];
				} else if (empty($data4))  {		
				$CARGO_GT4 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data4 = $con->query($dataTongkang)-> result_array();
				if ($data4){							
				$TONGKANG4 = $data4[0]['count'];				
				} else if (empty($data4))  {		
				$TONGKANG4 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data4 = $con->query($dataLain)-> result_array();
				if ($data4){							
				$LAIN4 = $data4[0]['count'];				
				} else if (empty($data4))  {		
				$LAIN4 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data4 = $con->query($dataTongkanggt)-> result_array();
				if ($data4){							
				$TONGKANG_GT4 = $data4[0]['sum'];
				} else if (empty($data4))  {		
				$TONGKANG_GT4 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data4 = $con->query($dataLaingt)-> result_array();
				if ($data4){							
				$LAIN_GT4 = $data4[0]['sum'];
				} else if (empty($data4))  {		
				$LAIN_GT4 = 0;
				}

			}
		
			if ($PERIODE == ''.$YEAR.'-05'){	
		
				$bulan5 = 'Mei';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data5 = $con->query($dataRoro)-> result_array();
				if ($data5){							
				$RORO5 = $data5[0]['count'];
				} else if (empty($data5))  {		
				$RORO5 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data5 = $con->query($dataLCT)-> result_array();
				if ($data5){							
				$LCT5 = $data5[0]['count'];				
				} else if (empty($data5))  {		
				$LCT5 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data5 = $con->query($dataCargo)-> result_array();
				if ($data5){							
				$CARGO5 = $data5[0]['count'];				
				} else if (empty($data5))  {		
				$CARGO5 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data5 = $con->query($dataRorogt)-> result_array();
				if ($data5){							
				$RORO_GT5 = $data5[0]['sum'];
				} else if (empty($data5))  {		
				$RORO_GT5 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data5 = $con->query($dataLctgt)-> result_array();
				if ($data5){							
				$LCT_GT5 = $data5[0]['sum'];
				} else if (empty($data5))  {		
				$LCT_GT5 = 0;
				}
				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data5 = $con->query($dataCargogt)-> result_array();
				if ($data5){							
				$CARGO_GT5 = $data5[0]['sum'];
				} else if (empty($data5))  {		
				$CARGO_GT5 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data5 = $con->query($dataTongkang)-> result_array();
				if ($data5){							
				$TONGKANG5 = $data5[0]['count'];				
				} else if (empty($data5))  {		
				$TONGKANG5 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data5 = $con->query($dataLain)-> result_array();
				if ($data5){							
				$LAIN5 = $data5[0]['count'];				
				} else if (empty($data5))  {		
				$LAIN5 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data5 = $con->query($dataTongkanggt)-> result_array();
				if ($data5){							
				$TONGKANG_GT5 = $data5[0]['sum'];
				} else if (empty($data5))  {		
				$TONGKANG_GT5 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data5 = $con->query($dataLaingt)-> result_array();
				if ($data5){							
				$LAIN_GT5 = $data5[0]['sum'];
				} else if (empty($data5))  {		
				$LAIN_GT5 = 0;
				}

			}
			
			if ($PERIODE == ''.$YEAR.'-06'){	
			
				$bulan6 = 'Juni';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data6 = $con->query($dataRoro)-> result_array();
				if ($data6){							
				$RORO6 = $data6[0]['count'];
				} else if (empty($data6))  {		
				$RORO6 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data6 = $con->query($dataLCT)-> result_array();
				if ($data6){							
				$LCT6 = $data6[0]['count'];				
				} else if (empty($data6))  {		
				$LCT6 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data6 = $con->query($dataCargo)-> result_array();
				if ($data6){							
				$CARGO6 = $data6[0]['count'];				
				} else if (empty($data6))  {		
				$CARGO6 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data6 = $con->query($dataRorogt)-> result_array();
				if ($data6){							
				$RORO_GT6 = $data6[0]['sum'];
				} else if (empty($data6))  {		
				$RORO_GT6 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data6 = $con->query($dataLctgt)-> result_array();
				if ($data6){							
				$LCT_GT6 = $data6[0]['sum'];
				} else if (empty($data6))  {		
				$LCT_GT6 = 0;
				}
				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data6 = $con->query($dataCargogt)-> result_array();
				if ($data6){							
				$CARGO_GT6 = $data6[0]['sum'];
				} else if (empty($data6))  {		
				$CARGO_GT6 = 0;
				}
				
				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data6 = $con->query($dataTongkang)-> result_array();
				if ($data6){							
				$TONGKANG6 = $data6[0]['count'];				
				} else if (empty($data6))  {		
				$TONGKANG6 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data6 = $con->query($dataLain)-> result_array();
				if ($data6){							
				$LAIN6 = $data6[0]['count'];				
				} else if (empty($data6))  {		
				$LAIN6 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data6 = $con->query($dataTongkanggt)-> result_array();
				if ($data6){							
				$TONGKANG_GT6 = $data6[0]['sum'];
				} else if (empty($data6))  {		
				$TONGKANG_GT6 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data6 = $con->query($dataLaingt)-> result_array();
				if ($data6){							
				$LAIN_GT6 = $data6[0]['sum'];
				} else if (empty($data6))  {		
				$LAIN_GT6 = 0;
				}

			}

			if ($PERIODE == ''.$YEAR.'-07'){			
				$bulan7 = 'Juli';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data7 = $con->query($dataRoro)-> result_array();
				if ($data7){							
				$RORO7 = $data7[0]['count'];
				} else if (empty($data7))  {		
				$RORO7 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data7 = $con->query($dataLCT)-> result_array();
				if ($data7){							
				$LCT7 = $data7[0]['count'];				
				} else if (empty($data7))  {		
				$LCT7 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data7 = $con->query($dataCargo)-> result_array();
				if ($data7){							
				$CARGO7 = $data7[0]['count'];				
				} else if (empty($data7))  {		
				$CARGO7 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data7 = $con->query($dataRorogt)-> result_array();
				if ($data7){							
				$RORO_GT7 = $data7[0]['sum'];
				} else if (empty($data7))  {		
				$RORO_GT7 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data7 = $con->query($dataLctgt)-> result_array();
				if ($data7){							
				$LCT_GT7 = $data7[0]['sum'];
				} else if (empty($data7))  {		
				$LCT_GT7 = 0;
				}
				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data7 = $con->query($dataCargogt)-> result_array();
				if ($data7){							
				$CARGO_GT7 = $data7[0]['sum'];
				} else if (empty($data7))  {		
				$CARGO_GT7 = 0;
				}	

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data7 = $con->query($dataTongkang)-> result_array();
				if ($data7){							
				$TONGKANG7 = $data7[0]['count'];				
				} else if (empty($data7))  {		
				$TONGKANG7 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data7 = $con->query($dataLain)-> result_array();
				if ($data7){							
				$LAIN7 = $data7[0]['count'];				
				} else if (empty($data7))  {		
				$LAIN7 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data7 = $con->query($dataTongkanggt)-> result_array();
				if ($data7){							
				$TONGKANG_GT7 = $data7[0]['sum'];
				} else if (empty($data7))  {		
				$TONGKANG_GT7 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data7 = $con->query($dataLaingt)-> result_array();
				if ($data7){							
				$LAIN_GT7 = $data7[0]['sum'];
				} else if (empty($data7))  {		
				$LAIN_GT7 = 0;
				}

			} 

			if ($PERIODE == ''.$YEAR.'-08'){	
				
				$bulan8 = 'Agustus';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data8 = $con->query($dataRoro)-> result_array();
				if ($data8){							
				$RORO8 = $data8[0]['count'];
				} else if (empty($data8))  {		
				$RORO8 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data8 = $con->query($dataLCT)-> result_array();
				if ($data8){							
				$LCT8 = $data8[0]['count'];				
				} else if (empty($data8))  {		
				$LCT8 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data8 = $con->query($dataCargo)-> result_array();
				if ($data8){							
				$CARGO8 = $data8[0]['count'];				
				} else if (empty($data8))  {		
				$CARGO8 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data8 = $con->query($dataRorogt)-> result_array();
				if ($data8){							
				$RORO_GT8 = $data8[0]['sum'];
				} else if (empty($data8))  {		
				$RORO_GT8 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data8 = $con->query($dataLctgt)-> result_array();
				if ($data8){							
				$LCT_GT8 = $data8[0]['sum'];
				} else if (empty($data8))  {		
				$LCT_GT8 = 0;
				}
				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data8 = $con->query($dataCargogt)-> result_array();
				if ($data8){							
				$CARGO_GT8 = $data8[0]['sum'];
				} else if (empty($data8))  {		
				$CARGO_GT8 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data8 = $con->query($dataTongkang)-> result_array();
				if ($data8){							
				$TONGKANG8 = $data8[0]['count'];				
				} else if (empty($data8))  {		
				$TONGKANG8 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data8 = $con->query($dataLain)-> result_array();
				if ($data8){							
				$LAIN8 = $data8[0]['count'];				
				} else if (empty($data8))  {		
				$LAIN8 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data8 = $con->query($dataTongkanggt)-> result_array();
				if ($data8){							
				$TONGKANG_GT8 = $data8[0]['sum'];
				} else if (empty($data8))  {		
				$TONGKANG_GT8 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data8 = $con->query($dataLaingt)-> result_array();
				if ($data8){							
				$LAIN_GT8 = $data8[0]['sum'];
				} else if (empty($data8))  {		
				$LAIN_GT8 = 0;
				}

			} 
			
			if ($PERIODE == ''.$YEAR.'-09'){
	
				$bulan9 = 'September';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data9 = $con->query($dataRoro)-> result_array();
				if ($data9){							
				$RORO9 = $data9[0]['count'];
				} else if (empty($data9))  {		
				$RORO9 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data9 = $con->query($dataLCT)-> result_array();
				if ($data9){							
				$LCT9 = $data9[0]['count'];				
				} else if (empty($data9))  {		
				$LCT9 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data9 = $con->query($dataCargo)-> result_array();
				if ($data9){							
				$CARGO9 = $data9[0]['count'];				
				} else if (empty($data9))  {		
				$CARGO9 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data9 = $con->query($dataRorogt)-> result_array();
				if ($data9){							
				$RORO_GT9 = $data9[0]['sum'];
				} else if (empty($data9))  {		
				$RORO_GT9 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data9 = $con->query($dataLctgt)-> result_array();
				if ($data9){							
				$LCT_GT9 = $data9[0]['sum'];
				} else if (empty($data9))  {		
				$LCT_GT9 = 0;
				}
				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data9 = $con->query($dataCargogt)-> result_array();
				if ($data9){							
				$CARGO_GT9 = $data9[0]['sum'];
				} else if (empty($data9))  {		
				$CARGO_GT9 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data9 = $con->query($dataTongkang)-> result_array();
				if ($data9){							
				$TONGKANG9 = $data9[0]['count'];				
				} else if (empty($data9))  {		
				$TONGKANG9 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data9 = $con->query($dataLain)-> result_array();
				if ($data9){							
				$LAIN9 = $data9[0]['count'];				
				} else if (empty($data9))  {		
				$LAIN9 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data9 = $con->query($dataTongkanggt)-> result_array();
				if ($data9){							
				$TONGKANG_GT9 = $data9[0]['sum'];
				} else if (empty($data9))  {		
				$TONGKANG_GT9 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data9 = $con->query($dataLaingt)-> result_array();
				if ($data9){							
				$LAIN_GT9 = $data9[0]['sum'];
				} else if (empty($data9))  {		
				$LAIN_GT9 = 0;
				}

			} 
		
			if ($PERIODE == ''.$YEAR.'-10'){
		
				$bulan10 = 'Oktober';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data10 = $con->query($dataRoro)-> result_array();
				if ($data10){							
				$RORO10 = $data10[0]['count'];
				} else if (empty($data10))  {		
				$RORO10 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data10 = $con->query($dataLCT)-> result_array();
				if ($data10){							
				$LCT10 = $data10[0]['count'];				
				} else if (empty($data10))  {		
				$LCT10 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data10 = $con->query($dataCargo)-> result_array();
				if ($data10){							
				$CARGO10 = $data10[0]['count'];				
				} else if (empty($data10))  {		
				$CARGO10 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data10 = $con->query($dataRorogt)-> result_array();
				if ($data10){							
				$RORO_GT10 = $data10[0]['sum'];
				} else if (empty($data10))  {		
				$RORO_GT10 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data10 = $con->query($dataLctgt)-> result_array();
				if ($data10){							
				$LCT_GT10 = $data10[0]['sum'];
				} else if (empty($data10))  {		
				$LCT_GT10 = 0;
				}

				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data10 = $con->query($dataCargogt)-> result_array();
				if ($data10){							
				$CARGO_GT10 = $data10[0]['sum'];
				} else if (empty($data10))  {		
				$CARGO_GT10 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data10 = $con->query($dataTongkang)-> result_array();
				if ($data10){							
				$TONGKANG10 = $data10[0]['count'];				
				} else if (empty($data10))  {		
				$TONGKANG10 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data10 = $con->query($dataLain)-> result_array();
				if ($data10){							
				$LAIN10 = $data10[0]['count'];				
				} else if (empty($data10))  {		
				$LAIN10 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data10 = $con->query($dataTongkanggt)-> result_array();
				if ($data10){							
				$TONGKANG_GT10 = $data10[0]['sum'];
				} else if (empty($data10))  {		
				$TONGKANG_GT10 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data10 = $con->query($dataLaingt)-> result_array();
				if ($data10){							
				$LAIN_GT10 = $data10[0]['sum'];
				} else if (empty($data10))  {		
				$LAIN_GT10 = 0;
				}

			} 
			if ($PERIODE == ''.$YEAR.'-11'){	
	
				$bulan11 = 'November';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data11 = $con->query($dataRoro)-> result_array();
				if ($data11){							
				$RORO11 = $data11[0]['count'];
				} else if (empty($data11))  {		
				$RORO11 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data11 = $con->query($dataLCT)-> result_array();
				if ($data11){							
				$LCT11 = $data11[0]['count'];				
				} else if (empty($data11))  {		
				$LCT11 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data11 = $con->query($dataCargo)-> result_array();
				if ($data11){							
				$CARGO11 = $data11[0]['count'];				
				} else if (empty($data11))  {		
				$CARGO11 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data11 = $con->query($dataRorogt)-> result_array();
				if ($data11){							
				$RORO_GT11 = $data11[0]['sum'];
				} else if (empty($data11))  {		
				$RORO_GT11 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data11 = $con->query($dataLctgt)-> result_array();
				if ($data11){							
				$LCT_GT11 = $data11[0]['sum'];
				} else if (empty($data11))  {		
				$LCT_GT11 = 0;
				}
				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data11 = $con->query($dataCargogt)-> result_array();
				if ($data11){							
				$CARGO_GT11 = $data11[0]['sum'];
				} else if (empty($data11))  {		
				$CARGO_GT11 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data11 = $con->query($dataTongkang)-> result_array();
				if ($data11){							
				$TONGKANG11 = $data11[0]['count'];				
				} else if (empty($data11))  {		
				$TONGKANG11 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data11 = $con->query($dataLain)-> result_array();
				if ($data11){							
				$LAIN11 = $data11[0]['count'];				
				} else if (empty($data11))  {		
				$LAIN11 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data11 = $con->query($dataTongkanggt)-> result_array();
				if ($data11){							
				$TONGKANG_GT11 = $data11[0]['sum'];
				} else if (empty($data11))  {		
				$TONGKANG_GT11 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data11 = $con->query($dataLaingt)-> result_array();
				if ($data11){							
				$LAIN_GT11 = $data11[0]['sum'];
				} else if (empty($data11))  {		
				$LAIN_GT11 = 0;
				}

			}

			if ($PERIODE == ''.$YEAR.'-12'){
			
				$bulan12 = 'Desember';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"			
				';
				$data12 = $con->query($dataRoro)-> result_array();
				if ($data12){							
				$RORO12 = $data12[0]['count'];
				} else if (empty($data12))  {		
				$RORO12 = 0;
				}
				
				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data12 = $con->query($dataLCT)-> result_array();
				if ($data12){							
				$LCT12 = $data12[0]['count'];				
				} else if (empty($data12))  {		
				$LCT12 = 0;
				}

				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
				';
				$data12 = $con->query($dataCargo)-> result_array();
				if ($data12){							
				$CARGO12 = $data12[0]['count'];				
				} else if (empty($data12))  {		
				$CARGO12 = 0;
				}		
				
				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"';
										
				$data12 = $con->query($dataRorogt)-> result_array();
				if ($data12){							
				$RORO_GT12 = $data12[0]['sum'];
				} else if (empty($data12))  {		
				$RORO_GT12 = 0;
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"';
										
				$data12 = $con->query($dataLctgt)-> result_array();
				if ($data12){							
				$LCT_GT12 = $data12[0]['sum'];
				} else if (empty($data12))  {		
				$LCT_GT12 = 0;
				}

				
				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"';
										
				$data12 = $con->query($dataCargogt)-> result_array();
				if ($data12){							
				$CARGO_GT12 = $data12[0]['sum'];
				} else if (empty($data12))  {		
				$CARGO_GT12 = 0;
				}

				$dataTongkang = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data12 = $con->query($dataTongkang)-> result_array();
				if ($data12){							
				$TONGKANG12 = $data12[0]['count'];				
				} else if (empty($data12))  {		
				$TONGKANG12 = 0;
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"
				';
				$data12 = $con->query($dataLain)-> result_array();
				if ($data12){							
				$LAIN12 = $data12[0]['count'];				
				} else if (empty($data12))  {		
				$LAIN12 = 0;
				}
				
				$dataTongkanggt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
			JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tongkang.' and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data12 = $con->query($dataTongkanggt)-> result_array();
				if ($data12){							
				$TONGKANG_GT12 = $data12[0]['sum'];
				} else if (empty($data12))  {		
				$TONGKANG_GT12 = 0;
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'   and "TERMINAL" = '.$dom.' and not "SOURCE" = '.$rkap.'
				GROUP BY mk."JN_KAPAL"';
										
				$data12 = $con->query($dataLaingt)-> result_array();
				if ($data12){							
				$LAIN_GT12 = $data12[0]['sum'];
				} else if (empty($data12))  {		
				$LAIN_GT12 = 0;
				}

			}

					$terminal = 'DOMESTIK';
					$satuan = 'UNIT';
					$gt = 'GT';
					$terminal = "'$terminal'";
					$satuan = "'$satuan'";
					$gt = "'$gt'";
					$YEAR = "'$YEAR'";

					$conr = $this->load->database('ikt_postgree', TRUE);
					$dataRkap = 'SELECT "TERMINAL","PELAYARAN","TAHUN", "SATUAN", "JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
							from "DASHBOARD_RKAP_TRAFFIK"
							WHERE "TERMINAL" = '.$terminal.' and "TAHUN" = '.$YEAR.' and "SATUAN" = '.$satuan.'';
				
					$datar = $conr->query($dataRkap)-> result_array();
					if ($datar){
					   $total_rkap = $datar[0]['JANUARI'] + $datar[0]['FEBRUARI'] + $datar[0]['MARET'] + $datar[0]['APRIL'] + $datar[0]['MEI']+ $datar[0]['JUNI']
					 + $datar[0]['JULI']+ $datar[0]['AGUSTUS']+ $datar[0]['SEPTEMBER']+ $datar[0]['OKTOBER']+ $datar[0]['NOVEMBER']+ $datar[0]['DESEMBER'];
					} else if(empty($datar)) {	
						$total_rkap = 0;
					}	
			
					$dataRkapgt = 'SELECT "TERMINAL","PELAYARAN","TAHUN", "SATUAN", "JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
							from "DASHBOARD_RKAP_TRAFFIK"
					WHERE "TERMINAL" = '.$terminal.' and "TAHUN" = '.$YEAR.' and "SATUAN" = '.$gt.'';				
					$datar = $conr->query($dataRkapgt)-> result_array();
					if ($datar){
					   $total_rkap_GT = $datar[0]['JANUARI'] + $datar[0]['FEBRUARI'] + $datar[0]['MARET'] + $datar[0]['APRIL'] + $datar[0]['MEI']+ $datar[0]['JUNI']
					 + $datar[0]['JULI']+ $datar[0]['AGUSTUS']+ $datar[0]['SEPTEMBER']+ $datar[0]['OKTOBER']+ $datar[0]['NOVEMBER']+ $datar[0]['DESEMBER'];
					} else if(empty($datar)) {	
						$total_rkap_GT = 0;
					}	
					
		}
	

			if ($OLD){
				$x = "$OLD-01";	
				$y = "$OLD-12";	
				$old = "'$x'";
				$ago = "'$y'";
				$dataRorot = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$roro.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
				';
				$datat = $con->query($dataRorot)-> result_array();
				if ($datat){				
					$ROROT = $datat[0]['count'];			
			
				}else if(empty($datat)) {
					$ROROT = 0;				
				}

				$dataLctt = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$lct.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
					';
				$datat = $con->query($dataLctt)-> result_array();
				if ($datat){				
					$LCTT = $datat[0]['count'];			
			
				}else if(empty($datat)) {
					$LCTT = 0;				
				}

				$dataCargot = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$cargo.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
				';
				$datat = $con->query($dataCargot)-> result_array();
				if ($datat){				
					$CARGOT = $datat[0]['count'];			
			
				}else if(empty($datat)) {
					$CARGOT = 0;				
				}

				$dataTongkangt = 'SELECT COUNT("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$tongkang.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
				';
				$datat = $con->query($dataTongkangt)-> result_array();
				if ($datat){				
					$TONGKANGT = $datat[0]['count'];			
			
				}else if(empty($datat)) {
					$TONGKANGT = 0;				
				}

				$dataLaint = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$lain.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
				';
				$datat = $con->query($dataLaint)-> result_array();
				if ($datat){				
					$LAINT = $datat[0]['count'];			
			
				}else if(empty($datat)) {
					$LAINT = 0;				
				}

				$dataROROgtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$roro.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
					';
				$datat = $con->query($dataROROgtt)-> result_array();
				if ($datat){				
					$ROROT_GT = $datat[0]['sum'];			
			
				}else if(empty($datat)) {
					$ROROT_GT = 0;				
				}

				$dataLCTgtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$lct.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
					';
				$datat = $con->query($dataLCTgtt)-> result_array();
				if ($datat){				
					$LCTT_GT = $datat[0]['sum'];			
			
				}else if(empty($datat)) {
					$LCTT_GT = 0;				
				}

				$dataCargogtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$cargo.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
					';
				$datat = $con->query($dataCargogtt)-> result_array();
				if ($datat){				
					$CARGOT_GT = $datat[0]['sum'];			
			
				}else if(empty($datat)) {
					$CARGOT_GT = 0;				
				}

				$dataTongkanggtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$tongkang.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
					';
				$datat = $con->query($dataTongkanggtt)-> result_array();
				if ($datat){				
					$TONGKANGT_GT = $datat[0]['sum'];			
			
				}else if(empty($datat)) {
					$TONGKANGT_GT = 0;				
				}

				$dataLaingtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."KD_KAPAL" 
				WHERE mk."JN_KAPAL" = '.$lain.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				GROUP BY mk."JN_KAPAL"
					';
				$datat = $con->query($dataLaingtt)-> result_array();
				if ($datat){				
					$LAINT_GT = $datat[0]['sum'];			
			
				}else if(empty($datat)) {
					$LAINT_GT = 0;				
				}
			}	

				
				if (empty($bulan1)){
					$excel->setActiveSheetIndex(0)->setCellValue('E5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('E6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('E7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('E8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('E9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('E10', '0'); 
				} else if ($bulan1 == 'Januari'){
					$excel->setActiveSheetIndex(0)->setCellValue('E5', $RORO1);
					$excel->setActiveSheetIndex(0)->setCellValue('E6', $RORO_GT1);
					$excel->setActiveSheetIndex(0)->setCellValue('E7', $TONGKANG1);
					$excel->setActiveSheetIndex(0)->setCellValue('E8', $TONGKANG_GT1);
					$excel->setActiveSheetIndex(0)->setCellValue('E9', $LAIN1); 
					$excel->setActiveSheetIndex(0)->setCellValue('E10', $LAIN_GT1); 
					if (!empty($LCT1) || !empty($LCT_GT1)){
					$excel->setActiveSheetIndex(0)->setCellValue('E11', $LCT1); 
					$excel->setActiveSheetIndex(0)->setCellValue('E12', $LCT_GT1); 
					} else if (empty($LCT1) || empty($LCT_GT1)) {
						$excel->setActiveSheetIndex(0)->setCellValue('E11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('E12', ''); 	
					}
					if (!empty($CARGO1) || !empty($CARGO_GT1) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('E13', $CARGO1); 
					$excel->setActiveSheetIndex(0)->setCellValue('E14', $CARGO_GT1); 
					} else if (empty($CARGO1) || empty($CARGO_GT1) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('E13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('E14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('E16', '=SUM(E5+E7+E9+E13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('E17', '=SUM(E6+E8+E10+E14)'); 
				} 

				if (empty($bulan2)){
					$excel->setActiveSheetIndex(0)->setCellValue('F5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('F6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('F7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('F8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('F9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('F10', '0'); 
				} else if ($bulan2 == 'Februari'){
					$excel->setActiveSheetIndex(0)->setCellValue('F5', $RORO2);
					$excel->setActiveSheetIndex(0)->setCellValue('F6', $RORO_GT2);
					$excel->setActiveSheetIndex(0)->setCellValue('F7', $TONGKANG2);
					$excel->setActiveSheetIndex(0)->setCellValue('F8', $TONGKANG_GT2);
					$excel->setActiveSheetIndex(0)->setCellValue('F9', $LAIN2); 
					$excel->setActiveSheetIndex(0)->setCellValue('F10', $LAIN_GT2); 
					if (!empty($LCT2) || !empty($LCT_GT2) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('F11', $LCT2); 
					$excel->setActiveSheetIndex(0)->setCellValue('F12', $LCT_GT2); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('F11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('F12', ''); 	
					}
					if (!empty($CARGO2) || !empty($CARGO_GT2) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('F13', $CARGO2); 
					$excel->setActiveSheetIndex(0)->setCellValue('F14', $CARGO_GT2); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('F13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('F14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('F16', '=SUM(F5+F7+F9+F13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F17', '=SUM(F6+F8+F10+F14)');
					
				}
				
				if (empty($bulan3)){
					$excel->setActiveSheetIndex(0)->setCellValue('G5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('G6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('G7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('G8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('G9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('G10', '0'); 
				} else if ($bulan3 == 'Maret'){
					$excel->setActiveSheetIndex(0)->setCellValue('G5', $RORO3);
					$excel->setActiveSheetIndex(0)->setCellValue('G6', $RORO_GT3);
					$excel->setActiveSheetIndex(0)->setCellValue('G7', $TONGKANG3);
					$excel->setActiveSheetIndex(0)->setCellValue('G8', $TONGKANG_GT3);
					$excel->setActiveSheetIndex(0)->setCellValue('G9', $LAIN3); 
					$excel->setActiveSheetIndex(0)->setCellValue('G10', $LAIN_GT3); 
					if (!empty($LCT3) || !empty($LCT_GT3)) {
					$excel->setActiveSheetIndex(0)->setCellValue('G11', $LCT3); 
					$excel->setActiveSheetIndex(0)->setCellValue('G12', $LCT_GT3); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('G11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('G12', ''); 	
					}
					if (!empty($CARGO3) || !empty($CARGO_GT3) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('G13', $CARGO3); 
					$excel->setActiveSheetIndex(0)->setCellValue('G14', $CARGO_GT3); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('G13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('G14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('G16', '=SUM(G5+G7+G9+G13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('G17', '=SUM(G6+G8+G10+G14)'); 
				}
				if (empty($bulan4)){
					$excel->setActiveSheetIndex(0)->setCellValue('H5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('H6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('H7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('H8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('H9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('H10', '0'); 
				} else if ($bulan4 == 'April'){
					$excel->setActiveSheetIndex(0)->setCellValue('H5', $RORO4);
					$excel->setActiveSheetIndex(0)->setCellValue('H6', $RORO_GT4);
					$excel->setActiveSheetIndex(0)->setCellValue('H7', $TONGKANG4);
					$excel->setActiveSheetIndex(0)->setCellValue('H8', $TONGKANG_GT4);
					$excel->setActiveSheetIndex(0)->setCellValue('H9', $LAIN4); 
					$excel->setActiveSheetIndex(0)->setCellValue('H10', $LAIN_GT4); 
					if (!empty($LCT4) || !empty($LCT_GT4) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('H11', $LCT4); 
					$excel->setActiveSheetIndex(0)->setCellValue('H12', $LCT_GT4); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('H11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('H12', ''); 	
					}
					if (!empty($CARGO4) || !empty($CARGO_GT4) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('H13', $CARGO4); 
					$excel->setActiveSheetIndex(0)->setCellValue('H14', $CARGO_GT4); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('H13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('H14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('H16', '=SUM(H5+H7+H9+H13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('H17', '=SUM(H6+H8+H10+H14)'); 
				}
				if (empty($bulan5)){
					$excel->setActiveSheetIndex(0)->setCellValue('I5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('I6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('I7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('I8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('I9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('I10', '0'); 
				} else if ($bulan5 == 'Mei'){
					$excel->setActiveSheetIndex(0)->setCellValue('I5', $RORO5);
					$excel->setActiveSheetIndex(0)->setCellValue('I6', $RORO_GT5);
					$excel->setActiveSheetIndex(0)->setCellValue('I7', $TONGKANG5);
					$excel->setActiveSheetIndex(0)->setCellValue('I8', $TONGKANG_GT5);
					$excel->setActiveSheetIndex(0)->setCellValue('I9', $LAIN5); 
					$excel->setActiveSheetIndex(0)->setCellValue('I10', $LAIN_GT5); 
					if (!empty($LCT5) || !empty($LCT_GT5) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('I11', $LCT5); 
					$excel->setActiveSheetIndex(0)->setCellValue('I12', $LCT_GT5); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('I11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('I12', ''); 	
					}
					if (!empty($CARGO5) || !empty($CARGO_GT5) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('I13', $CARGO5); 
					$excel->setActiveSheetIndex(0)->setCellValue('I14', $CARGO_GT5);
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('I13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('I14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('I16', '=SUM(I5+I7+I9+I13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('I17', '=SUM(I6+I8+I10+I14)');  
				}
			
				if (empty($bulan6)){
					$excel->setActiveSheetIndex(0)->setCellValue('J5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('J6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('J7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('J8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('J9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('J10', '0'); 
				} else if ($bulan6 == 'Juni'){
					$excel->setActiveSheetIndex(0)->setCellValue('J5', $RORO6);
					$excel->setActiveSheetIndex(0)->setCellValue('J6', $RORO_GT6);
					$excel->setActiveSheetIndex(0)->setCellValue('J7', $TONGKANG6);
					$excel->setActiveSheetIndex(0)->setCellValue('J8', $TONGKANG_GT6);
					$excel->setActiveSheetIndex(0)->setCellValue('J9', $LAIN6); 
					$excel->setActiveSheetIndex(0)->setCellValue('J10', $LAIN_GT6); 

					if (!empty($LCT6) || !empty($LCT_GT6) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('J11', $LCT6); 
					$excel->setActiveSheetIndex(0)->setCellValue('J12', $LCT_GT6); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('J11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('J12', ''); 	
					}
					if (!empty($CARGO6) || !empty($CARGO_GT6)) {
					$excel->setActiveSheetIndex(0)->setCellValue('J13', $CARGO6); 
					$excel->setActiveSheetIndex(0)->setCellValue('J14', $CARGO_GT6); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('J13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('J14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('J16', '=SUM(J5+J7+J9+J13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('J17', '=SUM(J6+J8+J10+J14)');  
				} 
				if (empty($bulan7)){
					$excel->setActiveSheetIndex(0)->setCellValue('K5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('K6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('K7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('K8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('K9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('K10', '0'); 
				} else if ($bulan7 == 'Juli'){
					$excel->setActiveSheetIndex(0)->setCellValue('K5', $RORO7);
					$excel->setActiveSheetIndex(0)->setCellValue('K6', $RORO_GT7);
					$excel->setActiveSheetIndex(0)->setCellValue('K7', $TONGKANG7);
					$excel->setActiveSheetIndex(0)->setCellValue('K8', $TONGKANG_GT7);
					$excel->setActiveSheetIndex(0)->setCellValue('K9', $LAIN7); 
					$excel->setActiveSheetIndex(0)->setCellValue('K10', $LAIN_GT7);

					if (!empty($LCT7) || !empty($LCT_GT7)){
					$excel->setActiveSheetIndex(0)->setCellValue('K11', $LCT7); 
					$excel->setActiveSheetIndex(0)->setCellValue('K12', $LCT_GT7); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('K11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('K12', ''); 	
					}
					if (!empty($CARGO7) || !empty($CARGO_GT7)) {
					$excel->setActiveSheetIndex(0)->setCellValue('K13', $CARGO7); 
					$excel->setActiveSheetIndex(0)->setCellValue('K14', $CARGO_GT7); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('K13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('K14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('K16', '=SUM(K5+K7+K9+K13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('K17', '=SUM(K6+K8+K10+K14)');  
				} 
		
				if (empty($bulan8)){
					$excel->setActiveSheetIndex(0)->setCellValue('L5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L10', '0'); 
				} else if ($bulan8 == 'Agustus'){
					$excel->setActiveSheetIndex(0)->setCellValue('L5', $RORO8);
					$excel->setActiveSheetIndex(0)->setCellValue('L6', $RORO_GT8);
					$excel->setActiveSheetIndex(0)->setCellValue('L7', $TONGKANG8);
					$excel->setActiveSheetIndex(0)->setCellValue('L8', $TONGKANG_GT8);
					$excel->setActiveSheetIndex(0)->setCellValue('L9', $LAIN8); 
					$excel->setActiveSheetIndex(0)->setCellValue('L10', $LAIN_GT8);
					
					if (!empty($LCT8) || !empty($LCT_GT8) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('L11', $LCT8); 
					$excel->setActiveSheetIndex(0)->setCellValue('L12', $LCT_GT8); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('L11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('L12', ''); 	
					}
					if (!empty($CARGO8) || !empty($CARGO_GT8)) {
					$excel->setActiveSheetIndex(0)->setCellValue('L13', $CARGO8); 
					$excel->setActiveSheetIndex(0)->setCellValue('L14', $CARGO_GT8); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('L13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('L14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('L16', '=SUM(L5+L7+L9+L13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('L17', '=SUM(L6+L8+L10+L14)');  
				}
				
				if (empty($bulan9)){
					$excel->setActiveSheetIndex(0)->setCellValue('M5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('M6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('M7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('M8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('M9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('M10', '0'); 
				} else if ($bulan9 == 'September'){
					$excel->setActiveSheetIndex(0)->setCellValue('M5', $RORO9);
					$excel->setActiveSheetIndex(0)->setCellValue('M6', $RORO_GT9);
					$excel->setActiveSheetIndex(0)->setCellValue('M7', $TONGKANG9);
					$excel->setActiveSheetIndex(0)->setCellValue('M8', $TONGKANG_GT9);
					$excel->setActiveSheetIndex(0)->setCellValue('M9', $LAIN9); 
					$excel->setActiveSheetIndex(0)->setCellValue('M10', $LAIN_GT9);
					if (!empty($LCT9) || !empty($LCT_GT9) ) { 
					$excel->setActiveSheetIndex(0)->setCellValue('M11', $LCT9); 
					$excel->setActiveSheetIndex(0)->setCellValue('M12', $LCT_GT9); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('M11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('M12', ''); 	
					}
					if (!empty($CARGO9) || !empty($CARGO_GT9)) {
					$excel->setActiveSheetIndex(0)->setCellValue('M13', $CARGO9); 
					$excel->setActiveSheetIndex(0)->setCellValue('M14', $CARGO_GT9);
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('M13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('M14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('M16', '=SUM(M5+M7+M9+M13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('M17', '=SUM(M6+M8+M10+M14)');  
				}
				
				if (empty($bulan10)){
					$excel->setActiveSheetIndex(0)->setCellValue('N5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('N6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('N7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('N8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('N9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('N10', '0'); 
				} else if ($bulan10 == 'Oktober'){
					$excel->setActiveSheetIndex(0)->setCellValue('N5', $RORO10);
					$excel->setActiveSheetIndex(0)->setCellValue('N6', $RORO_GT10);
					$excel->setActiveSheetIndex(0)->setCellValue('N7', $TONGKANG10);
					$excel->setActiveSheetIndex(0)->setCellValue('N8', $TONGKANG_GT10);
					$excel->setActiveSheetIndex(0)->setCellValue('N9', $LAIN10); 
					$excel->setActiveSheetIndex(0)->setCellValue('N10', $LAIN_GT10); 
					if (!empty($LCT10) || !empty($LCT_GT10)) {
					$excel->setActiveSheetIndex(0)->setCellValue('N11', $LCT10); 
					$excel->setActiveSheetIndex(0)->setCellValue('N12', $LCT_GT10); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('N11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('N12', ''); 	
					}
					if (!empty($CARGO10) || !empty($CARGO_GT10)) {
					$excel->setActiveSheetIndex(0)->setCellValue('N13', $CARGO10); 
					$excel->setActiveSheetIndex(0)->setCellValue('N14', $CARGO_GT10); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('N13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('N14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('N16', '=SUM(N5+N7+N9+N13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('N17', '=SUM(N6+N8+N10+N14)');  
				}
				
				if (empty($bulan11)){
					$excel->setActiveSheetIndex(0)->setCellValue('O5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('O6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('O7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('O8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('O9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('O10', '0'); 
				} else if ($bulan11 ==  'November'){
					$excel->setActiveSheetIndex(0)->setCellValue('O5', $RORO11);
					$excel->setActiveSheetIndex(0)->setCellValue('O6', $RORO_GT11);
					$excel->setActiveSheetIndex(0)->setCellValue('O7', $TONGKANG11);
					$excel->setActiveSheetIndex(0)->setCellValue('O8', $TONGKANG_GT11);
					$excel->setActiveSheetIndex(0)->setCellValue('O9', $LAIN11); 
					$excel->setActiveSheetIndex(0)->setCellValue('O10', $LAIN_GT11);
					if (!empty($LCT11) || !empty($LCT_GT11) ) { 
					$excel->setActiveSheetIndex(0)->setCellValue('O11', $LCT11); 
					$excel->setActiveSheetIndex(0)->setCellValue('O12', $LCT_GT11); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('O11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('O12', ''); 	
					}
					if (!empty($CARGO11) || !empty($CARGO_GT11)) {
					$excel->setActiveSheetIndex(0)->setCellValue('O13', $CARGO11); 
					$excel->setActiveSheetIndex(0)->setCellValue('O14', $CARGO_GT11); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('O13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('O14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('O16', '=SUM(O5+O7+O9+O13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('O17', '=SUM(O6+O8+O10+O14)');  
				}

				
				if (empty($bulan12)){
					$excel->setActiveSheetIndex(0)->setCellValue('P5', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('P6', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('P7', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('P8', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('P9', '0');
					$excel->setActiveSheetIndex(0)->setCellValue('P10', '0'); 
				} else if ($bulan12 == 'Desember'){
					$excel->setActiveSheetIndex(0)->setCellValue('P5', $RORO12);
					$excel->setActiveSheetIndex(0)->setCellValue('P6', $RORO_GT12);
					$excel->setActiveSheetIndex(0)->setCellValue('P7', $TONGKANG12);
					$excel->setActiveSheetIndex(0)->setCellValue('P8', $TONGKANG_GT12);
					$excel->setActiveSheetIndex(0)->setCellValue('P9', $LAIN12); 
					$excel->setActiveSheetIndex(0)->setCellValue('P10', $LAIN_GT12); 
					if (!empty($LCT12) || !empty($LCT_GT12) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('P11', $LCT12); 
					$excel->setActiveSheetIndex(0)->setCellValue('P12', $LCT_GT12); 
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('P11', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('P12', ''); 	
					}
					if (!empty($CARGO12) || !empty($CARGO_GT12)) {
					$excel->setActiveSheetIndex(0)->setCellValue('P13', $CARGO12); 
					$excel->setActiveSheetIndex(0)->setCellValue('P14', $CARGO_GT12);
					} else {
						$excel->setActiveSheetIndex(0)->setCellValue('P13', ''); 
						$excel->setActiveSheetIndex(0)->setCellValue('P14', ''); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('P16', '=SUM(P5+P7+P9+P13)'); 
					$excel->setActiveSheetIndex(0)->setCellValue('P17', '=SUM(P6+P8+P10+P14)');  
				}
			
				$excel->setActiveSheetIndex(0)->setCellValue('Q5', '=SUM(E5:P5)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q6', '=SUM(E6:P6)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q7', '=SUM(E7:P7)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q8', '=SUM(E8:P8)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q9', '=SUM(E9:P9)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q10', '=SUM(E10:P10)');
				if (!empty($LCT1) || !empty($LCT_GT1) ) {
				$excel->setActiveSheetIndex(0)->setCellValue('Q11', '=SUM(E11:P11)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q12', '=SUM(E12:P12)');
				}
				if (!empty($CARGO1) || !empty($CARGO_GT1)) {
				$excel->setActiveSheetIndex(0)->setCellValue('Q13', '=SUM(E13:P13)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q14', '=SUM(E14:P14)');
				}				
				$excel->setActiveSheetIndex(0)->setCellValue('Q16', '=SUM(E16:P16)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q17', '=SUM(E17:P17)');
			
				$excel->setActiveSheetIndex(0)->setCellValue('R5', $ROROT);
				$excel->setActiveSheetIndex(0)->setCellValue('R6', $ROROT_GT);
				$excel->setActiveSheetIndex(0)->setCellValue('R7', $TONGKANGT);
				$excel->setActiveSheetIndex(0)->setCellValue('R8', $TONGKANGT_GT);
				$excel->setActiveSheetIndex(0)->setCellValue('R9', $LAINT);
				$excel->setActiveSheetIndex(0)->setCellValue('R10', $LAINT_GT);
				if (!empty($LCTT) || !empty($LCTT_GT) ) {
				$excel->setActiveSheetIndex(0)->setCellValue('R11', $LCTT); 
				$excel->setActiveSheetIndex(0)->setCellValue('R12', $LCTT_GT); 
				}
				if (!empty($CARGOT) || !empty($CARGOT_GT)) {
				$excel->setActiveSheetIndex(0)->setCellValue('R13', $CARGOT);
				$excel->setActiveSheetIndex(0)->setCellValue('R14', $CARGOT_GT);
				}
				$excel->setActiveSheetIndex(0)->setCellValue('R16', '=SUM(R5+R7+R9+R13)'); 
				$excel->setActiveSheetIndex(0)->setCellValue('R17', '=SUM(R6+R8+R10+R14)'); 
				$excel->setActiveSheetIndex(0)->setCellValue('S16', $total_rkap);
				$excel->setActiveSheetIndex(0)->setCellValue('S17', $total_rkap_GT);
		

			// // Set width kolom
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(10); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(25); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(25); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('E')->setWidth(15); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('F')->setWidth(15); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('G')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('H')->setWidth(15); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('I')->setWidth(15); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('J')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('K')->setWidth(15); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('L')->setWidth(15); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('M')->setWidth(15); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('N')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('O')->setWidth(15); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
			$excel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
			
			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Laporan_Trafik_Kedatangan_Kapal");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Laporan_Trafik_Kedatangan_Kapal_DOM_'.$id.'_'.$end.'.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');
	

  }

  
	public function export_laporan_intr($id,$end,$terminal)
	{
		
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Laporan_Trafik_Kedatangan_Kapal')							
								   ->setTitle("Laporan_Trafik_Kedatangan_Kapal")
								   ->setSubject("Laporan_Trafik_Kedatangan_Kapal")
								   ->setDescription("Laporan_Trafik_Kedatangan_Kapal")
								   ->setKeywords("Data_Trafik_Kedatangan_Kapal");
		
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
			$excel->setActiveSheetIndex(0)->mergeCells('A1:A2')->setCellValue('A1', "No"); // Set kolom A3 dengan tulisan "NO"
			$excel->setActiveSheetIndex(0)->mergeCells('B1:B2')->setCellValue('B1', "");
			$excel->setActiveSheetIndex(0)->mergeCells('C1:C2')->setCellValue('C2', "");
			$excel->setActiveSheetIndex(0)->mergeCells('B1:C1')->setCellValue('C1', "Uraian");
			$excel->setActiveSheetIndex(0)->mergeCells('B2:C2')->setCellValue('B2', "");	

			$excel->setActiveSheetIndex(0)->mergeCells('D1:D2')->setCellValue('D1', "Satuan");
			$excel->setActiveSheetIndex(0)->mergeCells('E1:P1')->setCellValue('E1', "REALISASI PERIODE BERJALAN IKT");
			$excel->setActiveSheetIndex(0)->setCellValue('A3', "1");
			$excel->setActiveSheetIndex(0)->mergeCells('B3:C3')->setCellValue('B3', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('D3', "3");
			$excel->setActiveSheetIndex(0)->mergeCells('E3:P3')->setCellValue('E3', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('E2', "Januari"); 
			$excel->setActiveSheetIndex(0)->setCellValue('F2', "Februari"); 
			$excel->setActiveSheetIndex(0)->setCellValue('G2', "Maret"); 
			$excel->setActiveSheetIndex(0)->setCellValue('H2', "April"); 
			$excel->setActiveSheetIndex(0)->setCellValue('I2', "Mei"); 
			$excel->setActiveSheetIndex(0)->setCellValue('J2', "Juni"); 
			$excel->setActiveSheetIndex(0)->setCellValue('K2', "Juli"); 
			$excel->setActiveSheetIndex(0)->setCellValue('L2', "Agustus"); 
			$excel->setActiveSheetIndex(0)->setCellValue('M2', "September"); 
			$excel->setActiveSheetIndex(0)->setCellValue('N2', "Oktober"); 
			$excel->setActiveSheetIndex(0)->setCellValue('O2', "November"); 
			$excel->setActiveSheetIndex(0)->setCellValue('P2', "Desember"); 
			$excel->setActiveSheetIndex(0)->mergeCells('Q1:Q2')->setCellValue('Q1', "Realisasi Tahun 2022");
			$excel->setActiveSheetIndex(0)->mergeCells('R1:R2')->setCellValue('R1', "Realisasi Tahun 2021");
			$excel->setActiveSheetIndex(0)->mergeCells('S1:S2')->setCellValue('S1', "RKAP Tahun 2022");
			$excel->setActiveSheetIndex(0)->mergeCells('B4:C4')->setCellValue('B4', "KAPAL");
			$excel->setActiveSheetIndex(0)->setCellValue('A5', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A7', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A9', "3");
			$excel->setActiveSheetIndex(0)->setCellValue('A11', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('A13', "5");
			$excel->setActiveSheetIndex(0)->setCellValue('A15', "6");
			$excel->setActiveSheetIndex(0)->setCellValue('C5', "RORO");
			$excel->setActiveSheetIndex(0)->setCellValue('C7', "LCT");
			$excel->setActiveSheetIndex(0)->setCellValue('C9', "CARGO");
			$excel->setActiveSheetIndex(0)->setCellValue('C11', "CURAH KERING");
			$excel->setActiveSheetIndex(0)->setCellValue('C13', "TANKER");
			$excel->setActiveSheetIndex(0)->setCellValue('C15', "LAINNYA");
			$excel->setActiveSheetIndex(0)->setCellValue('D5', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D6', "GT");
			$excel->setActiveSheetIndex(0)->setCellValue('D7', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D8', "GT");
			$excel->setActiveSheetIndex(0)->setCellValue('D9', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D10', "GT");
			$excel->setActiveSheetIndex(0)->setCellValue('D11', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D12', "GT");
			$excel->setActiveSheetIndex(0)->setCellValue('D13', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D14', "GT");
			$excel->setActiveSheetIndex(0)->setCellValue('D15', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D16', "GT");
	
			$excel->setActiveSheetIndex(0)->mergeCells('A18:C18')->setCellValue('A18', "JUMLAH KUNJUNGAN KAPAL");
			$excel->setActiveSheetIndex(0)->setCellValue('D18', "Call");
			$excel->setActiveSheetIndex(0)->setCellValue('D19', "GT");

			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(11); 
			$excel->getActiveSheet()->getStyle('A1:S1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('A2:S2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('Q1:Q2')->applyFromArray($style_col);

			$excel->getActiveSheet()->getStyle('R1:R2')->applyFromArray($style_col);

			$excel->getActiveSheet()->getStyle('A1:A19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('B1:B19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('C1:C19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('D1:D19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('E1:E19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('F1:F19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('G1:G19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H1:H19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('I1:I19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('J1:J19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('K1:K19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('L1:L19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('M1:M19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('N1:N19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('O1:O19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('P1:P19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('Q1:Q19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('R1:R19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('S1:S19')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('A3:S3')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A4:S4')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A5:S5')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A6:S6')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A7:S7')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A8:S8')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A9:S9')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A10:S10')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A11:S11')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A12:S12')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A13:S13')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A14:S14')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A15:S15')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A16:S16')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A17:S17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A18:S18')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A19:S19')->applyFromArray($style_row);
			
	
			$excel->getActiveSheet()->getStyle('A18:S18')->getFont()->setBold(true);		
			$excel->getActiveSheet()->getStyle('A19:S19')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('E5:Z5')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E6:Z6')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E7:Z7')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");			
			$excel->getActiveSheet()->getStyle('E8:Z8')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E9:Z9')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E10:Z10')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E11:Z11')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E12:Z12')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E13:Z13')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E14:Z14')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E15:Z15')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E16:Z16')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E17:Z17')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E18:Z18')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E19:Z19')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");

			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('tps_online/Model_lap_trafik_kapal');
			$con = $this->load->database('ikt_postgree', TRUE);
				
			$model = $this->Model_lap_trafik_kapal->get_data_intr($id,$end);

			$cont = count($model['data']);
			$x = 0;
 
				while($x < $cont) {		
				$PERIODE = $model["data"][$x]['periode'];
	
				$x++;
	
			$TAHUN = explode('-', $PERIODE);
			$YEAR = $TAHUN[0];
			$MM = $TAHUN[1];
			$OLD = $YEAR - 1;		
			
			if (!empty($YEAR)) {
			$excel->setActiveSheetIndex(0)->mergeCells('Q1:Q2')->setCellValue('Q1', "Realisasi Tahun $YEAR");
			$excel->setActiveSheetIndex(0)->mergeCells('R1:R2')->setCellValue('R1', "Realisasi Tahun $OLD");
			$excel->setActiveSheetIndex(0)->mergeCells('S1:S2')->setCellValue('S1', "RKAP Tahun $YEAR");
			}
			$tp = "'5TP2'";
			$dates = "'yyyy-mm'";				
			$roro = "'10'";			
			$lct = "'09'";
			$cargo = "'01'";		
			$tanker = "'03'";
			$curah_kering = "'20'";
			$lain = "'23'";
			$tpt1 = 'TPT1';
			$tpt2 = 'TPT2';
			$tpt1 = "'$tpt1'";
			$tpt2 = "'$tpt2'";

			if ($PERIODE == ''.$YEAR.'-01'){	
				$bulan1 = 'Januari';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data1 = $con->query($dataRoro)-> result_array();
				if ($dataRoro){								
				$RORO1 = $data1[0]['count'];		
		
				
				} else if (empty($data1))  {		
					$RORO1 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data1 = $con->query($dataLCT)-> result_array();
				if ($data1){								
				$LCT1 = $data1[0]['count'];		
	
				
				} else if (empty($data1))  {		
					$LCT1 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data1 = $con->query($dataCargo)-> result_array();
				if ($data1){								
				$CARGO1 = $data1[0]['count'];		
		
				
				} else if (empty($data1))  {		
					$CARGO1 = 0;				
			 
				}

				$dataCurahkering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"';
				$data1 = $con->query($dataCurahkering)-> result_array();
				if ($data1){								
				$CURKER1 = $data1[0]['count'];		
			
				
				} else if (empty($data1))  {		
					$CURKER1 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data1 = $con->query($dataTanker)-> result_array();
				if ($data1){								
				$TANKER1 = $data1[0]['count'];		
			  
				
				} else if (empty($data1))  {		
					$TANKER1 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data1 = $con->query($dataLain)-> result_array();
				if ($data1){								
				$LAIN1 = $data1[0]['count'];		
			  
				
				} else if (empty($data1))  {		
					$LAIN1 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data11 = $con->query($dataLaingt)-> result_array();
				if ($data11){								
					$LAIN1_GT = $data11[0]['sum'];			
			
				
				} else if (empty($data11))  {		
					$LAIN1_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data11 = $con->query($dataRorogt)-> result_array();
				if ($data11){								
					$RORO1_GT = $data11[0]['sum'];			
			
				
				} else if (empty($data11))  {		
					$RORO1_GT = 0;
			
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data11 = $con->query($dataLctgt)-> result_array();
				if ($data11){								
					$LCT1_GT = $data11[0]['sum'];			
					
				
				} else if (empty($data11))  {		
					$LCT1_GT = 0;
			
				}

				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data11 = $con->query($dataCargogt)-> result_array();
				if ($data11){								
					$CARGO1_GT = $data11[0]['sum'];			
		
				} else if (empty($data11))  {		
					$CARGO1_GT = 0;
			
				}

				$dataCurahkeringgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data11 = $con->query($dataCurahkeringgt)-> result_array();
				if ($data11){								
					$CURKER1_GT = $data11[0]['sum'];			
				
				
				} else if (empty($data11))  {		
					$CURKER1_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data11 = $con->query($dataTankergt)-> result_array();
				if ($data11){								
					$TANKER1_GT = $data11[0]['sum'];			
	
				
				} else if (empty($data11))  {		
					$TANKER1_GT = 0;
		
				}


	
			}
		
			if ($PERIODE == ''.$YEAR.'-02'){					
				$bulan2 = 'Februari';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data2 = $con->query($dataRoro)-> result_array();
				if ($data2){								
				$RORO2 = $data2[0]['count'];		
		
				
				} else if (empty($data2))  {		
					$RORO2 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data2 = $con->query($dataLCT)-> result_array();
				if ($data2){								
				$LCT2 = $data2[0]['count'];		
	
				
				} else if (empty($data2))  {		
					$LCT2 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data2 = $con->query($dataCargo)-> result_array();
				if ($data2){								
				$CARGO2 = $data2[0]['count'];		
		
				
				} else if (empty($data2))  {		
					$CARGO2 = 0;				
			 
				}

				$dataCurahkering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data2 = $con->query($dataCurahkering)-> result_array();
				if ($data2){								
				$CURKER2 = $data2[0]['count'];		
			
				
				} else if (empty($data2))  {		
					$CURKER2 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data2 = $con->query($dataTanker)-> result_array();
				if ($data2){								
				$TANKER2 = $data2[0]['count'];		
			  
				
				} else if (empty($data2))  {		
					$TANKER2 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data2 = $con->query($dataLain)-> result_array();
				if ($data2){								
					$LAIN2 = $data2[0]['count'];		
			  
				
				} else if (empty($data2))  {		
					$LAIN2 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data22 = $con->query($dataLaingt)-> result_array();
				if ($data22){								
					$LAIN2_GT = $data22[0]['sum'];			
			
				
				} else if (empty($data22))  {		
					$LAIN2_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data22 = $con->query($dataRorogt)-> result_array();
				if ($data22){								
					$RORO2_GT = $data22[0]['sum'];			
			
				
				} else if (empty($data2))  {		
					$RORO2_GT = 0;
			
				}

				$dataLctgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data22 = $con->query($dataLctgt)-> result_array();
				if ($data22){								
					$LCT2_GT = $data22[0]['sum'];			
					
				
				} else if (empty($data22))  {		
					$LCT2_GT = 0;
			
				}

				$dataCargogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data22 = $con->query($dataCargogt)-> result_array();
				if ($data22){								
					$CARGO2_GT = $data22[0]['sum'];			
		
				} else if (empty($data22))  {		
					$CARGO2_GT = 0;
			
				}

				$dataCurahkeringgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data22 = $con->query($dataCurahkeringgt)-> result_array();
				if ($data22){								
					$CURKER2_GT = $data22[0]['sum'];			
				
				
				} else if (empty($data22))  {		
					$CURKER2_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data22 = $con->query($dataTankergt)-> result_array();
				if ($data22){								
					$TANKER2_GT = $data22[0]['sum'];			
	
				
				} else if (empty($data22))  {		
					$TANKER2_GT = 0;
		
				}
				
			}

			if ($PERIODE == ''.$YEAR.'-03'){			
				$bulan3 = 'Maret';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data3 = $con->query($dataRoro)-> result_array();
				if ($data3){								
				$RORO3 = $data3[0]['count'];		
		
				
				} else if (empty($data3))  {		
					$RORO3 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data3 = $con->query($dataLCT)-> result_array();
				if ($data3){								
				$LCT3 = $data3[0]['count'];		
	
				
				} else if (empty($data3))  {		
					$LCT3 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data3 = $con->query($dataCargo)-> result_array();
				if ($data3){								
				$CARGO3 = $data3[0]['count'];		
		
				
				} else if (empty($data3))  {		
					$CARGO3 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data3 = $con->query($dataCurahKering)-> result_array();
				if ($data3){								
				$CURKER3 = $data3[0]['count'];		
			
				
				} else if (empty($data3))  {		
					$CURKER3 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data3 = $con->query($dataTanker)-> result_array();
				if ($data3){								
				$TANKER3 = $data3[0]['count'];		
			  
				
				} else if (empty($data3))  {		
					$TANKER3 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data3 = $con->query($dataLain)-> result_array();
				if ($data3){								
					$LAIN3 = $data3[0]['count'];		
			  
				
				} else if (empty($data3))  {		
					$LAIN3 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data33 = $con->query($dataLaingt)-> result_array();
				if ($data33){								
					$LAIN3_GT = $data33[0]['sum'];			
			
				
				} else if (empty($data33))  {		
					$LAIN3_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data33 = $con->query($dataRorogt)-> result_array();
				if ($data33){								
					$RORO3_GT = $data33[0]['sum'];			
			
				
				} else if (empty($data3))  {		
					$RORO3_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data33 = $con->query($dataLCTgt)-> result_array();
				if ($data33){								
					$LCT3_GT = $data33[0]['sum'];			
					
				
				} else if (empty($data3))  {		
					$LCT3_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data33 = $con->query($dataCargogt)-> result_array();
				if ($data33){								
					$CARGO3_GT = $data33[0]['sum'];			
		
				} else if (empty($data3))  {		
					$CARGO3_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data33 = $con->query($CurahKeringt)-> result_array();
				if ($data33){								
					$CURKER3_GT = $data33[0]['sum'];			
				
				
				} else if (empty($data3))  {		
					$CURKER3_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data33 = $con->query($dataTankergt)-> result_array();
				if ($data33){								
					$TANKER3_GT = $data33[0]['sum'];			
	
				
				} else if (empty($data3))  {		
					$TANKER3_GT = 0;
		
				}
			} 

			if ($PERIODE == ''.$YEAR.'-04'){		
				$bulan4 = 'April';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data4 = $con->query($dataRoro)-> result_array();
				if ($data4){								
				$RORO4 = $data4[0]['count'];		
		
				
				} else if (empty($data4))  {		
					$RORO4 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data4 = $con->query($dataLCT)-> result_array();
				if ($data4){								
				$LCT4 = $data4[0]['count'];		
	
				
				} else if (empty($data4))  {		
					$LCT4 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data4 = $con->query($dataCargo)-> result_array();
				if ($data4){								
				$CARGO4 = $data4[0]['count'];		
		
				
				} else if (empty($data4))  {		
					$CARGO4 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data4 = $con->query($dataCurahKering)-> result_array();
				if ($data4){								
				$CURKER4 = $data4[0]['count'];		
			
				
				} else if (empty($data4))  {		
					$CURKER4 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data4 = $con->query($dataTanker)-> result_array();
				if ($data4){								
				$TANKER4 = $data4[0]['count'];		
			  
				
				} else if (empty($data4))  {		
					$TANKER4 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data4 = $con->query($dataLain)-> result_array();
				if ($data4){								
					$LAIN4 = $data4[0]['count'];		
			  
				
				} else if (empty($data4))  {		
					$LAIN4 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data44 = $con->query($dataLaingt)-> result_array();
				if ($data44){								
					$LAIN4_GT = $data44[0]['sum'];			
			
				
				} else if (empty($data4))  {		
					$LAIN4_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data44 = $con->query($dataRorogt)-> result_array();
				if ($data44){								
					$RORO4_GT = $data44[0]['sum'];			
			
				
				} else if (empty($data4))  {		
					$RORO4_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data44 = $con->query($dataLCTgt)-> result_array();
				if ($data44){								
					$LCT4_GT = $data44[0]['sum'];			
					
				
				} else if (empty($data4))  {		
					$LCT4_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data44 = $con->query($dataCargogt)-> result_array();
				if ($data44){								
					$CARGO4_GT = $data44[0]['sum'];			
		
				} else if (empty($data4))  {		
					$CARGO4_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data44 = $con->query($CurahKeringt)-> result_array();
				if ($data44){								
					$CURKER4_GT = $data44[0]['sum'];			
				
				
				} else if (empty($data4))  {		
					$CURKER4_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data44 = $con->query($dataTankergt)-> result_array();
				if ($data44){								
					$TANKER4_GT = $data44[0]['sum'];			
	
				
				} else if (empty($data4))  {		
					$TANKER4_GT = 0;
		
				}
			}
		
			if ($PERIODE == ''.$YEAR.'-05'){
			
				$bulan5 = 'Mei';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data5 = $con->query($dataRoro)-> result_array();
				if ($data5){								
				$RORO5 = $data5[0]['count'];		
		
				
				} else if (empty($data5))  {		
					$RORO5 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data5 = $con->query($dataLCT)-> result_array();
				if ($data5){								
				$LCT5 = $data5[0]['count'];		
	
				
				} else if (empty($data5))  {		
					$LCT5 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data5 = $con->query($dataCargo)-> result_array();
				if ($data5){								
				$CARGO5 = $data5[0]['count'];		
		
				
				} else if (empty($data5))  {		
					$CARGO5 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data5 = $con->query($dataCurahKering)-> result_array();
				if ($data5){								
				$CURKER5 = $data5[0]['count'];		
			
				
				} else if (empty($data5))  {		
					$CURKER5 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data5 = $con->query($dataTanker)-> result_array();
				if ($data5){								
				$TANKER5 = $data5[0]['count'];		
			  
				
				} else if (empty($data5))  {		
					$TANKER5 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data5 = $con->query($dataLain)-> result_array();
				if ($data5){								
					$LAIN5 = $data5[0]['count'];		
			  
				
				} else if (empty($data5))  {		
					$LAIN5 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data55 = $con->query($dataLaingt)-> result_array();
				if ($data55){								
					$LAIN5_GT = $data55[0]['sum'];			
			
				
				} else if (empty($data5))  {		
					$LAIN5_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data55 = $con->query($dataRorogt)-> result_array();
				if ($data55){								
					$RORO5_GT = $data55[0]['sum'];			
			
				
				} else if (empty($data5))  {		
					$RORO5_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data55 = $con->query($dataLCTgt)-> result_array();
				if ($data55){								
					$LCT5_GT = $data55[0]['sum'];			
					
				
				} else if (empty($data5))  {		
					$LCT5_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data55 = $con->query($dataCargogt)-> result_array();
				if ($data55){								
					$CARGO5_GT = $data55[0]['sum'];			
		
				} else if (empty($data5))  {		
					$CARGO5_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data55 = $con->query($CurahKeringt)-> result_array();
				if ($data55){								
					$CURKER5_GT = $data55[0]['sum'];			
				
				
				} else if (empty($data5))  {		
					$CURKER5_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data55 = $con->query($dataTankergt)-> result_array();
				if ($data55){								
					$TANKER5_GT = $data55[0]['sum'];			
	
				
				} else if (empty($data5))  {		
					$TANKER5_GT = 0;
		
				}
			}
			
			if ($PERIODE == ''.$YEAR.'-06'){	
		
				$bulan6 = 'Juni';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data6 = $con->query($dataRoro)-> result_array();
				if ($data6){								
				$RORO6 = $data6[0]['count'];		
		
				
				} else if (empty($data6))  {		
					$RORO6 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data6 = $con->query($dataLCT)-> result_array();
				if ($data6){								
				$LCT6 = $data6[0]['count'];		
	
				
				} else if (empty($data6))  {		
					$LCT6 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data6 = $con->query($dataCargo)-> result_array();
				if ($data6){								
				$CARGO6 = $data6[0]['count'];		
		
				
				} else if (empty($data6))  {		
					$CARGO6 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data6 = $con->query($dataCurahKering)-> result_array();
				if ($data6){								
				$CURKER6 = $data6[0]['count'];		
			
				
				} else if (empty($data6))  {		
					$CURKER6 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data6 = $con->query($dataTanker)-> result_array();
				if ($data6){								
				$TANKER6 = $data6[0]['count'];		
			  
				
				} else if (empty($data6))  {		
					$TANKER6 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data6 = $con->query($dataLain)-> result_array();
				if ($data6){								
					$LAIN6 = $data6[0]['count'];		
			  
				
				} else if (empty($data6))  {		
					$LAIN6 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data66 = $con->query($dataLaingt)-> result_array();
				if ($data66){								
					$LAIN6_GT = $data66[0]['sum'];			
			
				
				} else if (empty($data6))  {		
					$LAIN6_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data66 = $con->query($dataRorogt)-> result_array();
				if ($data66){								
					$RORO6_GT = $data66[0]['sum'];			
			
				
				} else if (empty($data6))  {		
					$RORO6_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data66 = $con->query($dataLCTgt)-> result_array();
				if ($data66){								
					$LCT6_GT = $data66[0]['sum'];			
					
				
				} else if (empty($data6))  {		
					$LCT6_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data66 = $con->query($dataCargogt)-> result_array();
				if ($data66){								
					$CARGO6_GT = $data66[0]['sum'];			
		
				} else if (empty($data6))  {		
					$CARGO6_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data66 = $con->query($CurahKeringt)-> result_array();
				if ($data66){								
					$CURKER6_GT = $data66[0]['sum'];			
				
				
				} else if (empty($data6))  {		
					$CURKER6_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data66 = $con->query($dataTankergt)-> result_array();
				if ($data66){								
					$TANKER6_GT = $data66[0]['sum'];			
	
				
				} else if (empty($data6))  {		
					$TANKER6_GT = 0;
		
				}
			}

			if ($PERIODE == ''.$YEAR.'-07'){			
				$bulan7 = 'Juli';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data7 = $con->query($dataRoro)-> result_array();
				if ($data7){								
				$RORO7 = $data7[0]['count'];		
		
				
				} else if (empty($data7))  {		
					$RORO7 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data7 = $con->query($dataLCT)-> result_array();
				if ($data7){								
				$LCT7 = $data7[0]['count'];		
	
				
				} else if (empty($data7))  {		
					$LCT7 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data7 = $con->query($dataCargo)-> result_array();
				if ($data7){								
				$CARGO7 = $data7[0]['count'];		
		
				
				} else if (empty($data7))  {		
					$CARGO7 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data7 = $con->query($dataCurahKering)-> result_array();
				if ($data7){								
				$CURKER7 = $data7[0]['count'];		
			
				
				} else if (empty($data7))  {		
					$CURKER7 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data7 = $con->query($dataTanker)-> result_array();
				if ($data7){								
				$TANKER7 = $data7[0]['count'];		
			  
				
				} else if (empty($data7))  {		
					$TANKER7 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data7 = $con->query($dataLain)-> result_array();
				if ($data7){								
					$LAIN7 = $data7[0]['count'];		
			  
				
				} else if (empty($data7))  {		
					$LAIN7 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data77 = $con->query($dataLaingt)-> result_array();
				if ($data77){								
					$LAIN7_GT = $data77[0]['sum'];			
			
				
				} else if (empty($data7))  {		
					$LAIN7_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data77 = $con->query($dataRorogt)-> result_array();
				if ($data77){								
					$RORO7_GT = $data77[0]['sum'];			
			
				
				} else if (empty($data7))  {		
					$RORO7_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data77 = $con->query($dataLCTgt)-> result_array();
				if ($data77){								
					$LCT7_GT = $data77[0]['sum'];			
					
				
				} else if (empty($data7))  {		
					$LCT7_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data77 = $con->query($dataCargogt)-> result_array();
				if ($data77){								
					$CARGO7_GT = $data77[0]['sum'];			
		
				} else if (empty($data7))  {		
					$CARGO7_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data77 = $con->query($CurahKeringt)-> result_array();
				if ($data77){								
					$CURKER7_GT = $data77[0]['sum'];			
				
				
				} else if (empty($data7))  {		
					$CURKER7_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data77 = $con->query($dataTankergt)-> result_array();
				if ($data77){								
					$TANKER7_GT = $data77[0]['sum'];			
	
				
				} else if (empty($data7))  {		
					$TANKER7_GT = 0;
		
				}
						

			} 
		
			if ($PERIODE == ''.$YEAR.'-08'){	
					
				$bulan8 = 'Agustus';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data8 = $con->query($dataRoro)-> result_array();
				if ($data8){								
				$RORO8 = $data8[0]['count'];		
		
				
				} else if (empty($data8))  {		
					$RORO8 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data8 = $con->query($dataLCT)-> result_array();
				if ($data8){								
				$LCT8 = $data8[0]['count'];		
	
				
				} else if (empty($data8))  {		
					$LCT8 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data8 = $con->query($dataCargo)-> result_array();
				if ($data8){								
				$CARGO8 = $data8[0]['count'];		
		
				
				} else if (empty($data8))  {		
					$CARGO8 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data8 = $con->query($dataCurahKering)-> result_array();
				if ($data8){								
				$CURKER8 = $data8[0]['count'];		
			
				
				} else if (empty($data8))  {		
					$CURKER8 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data8 = $con->query($dataTanker)-> result_array();
				if ($data8){								
				$TANKER8 = $data8[0]['count'];		
			  
				
				} else if (empty($data8))  {		
					$TANKER8 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data8 = $con->query($dataLain)-> result_array();
				if ($data8){								
					$LAIN8 = $data8[0]['count'];		
			  
				
				} else if (empty($data8))  {		
					$LAIN8 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data88 = $con->query($dataLaingt)-> result_array();
				if ($data88){								
					$LAIN8_GT = $data88[0]['sum'];			
			
				
				} else if (empty($data88))  {		
					$LAIN8_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data88 = $con->query($dataRorogt)-> result_array();
				if ($data88){								
					$RORO8_GT = $data88[0]['sum'];			
			
				
				} else if (empty($data8))  {		
					$RORO8_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data88 = $con->query($dataLCTgt)-> result_array();
				if ($data88){								
					$LCT8_GT = $data88[0]['sum'];			
					
				
				} else if (empty($data8))  {		
					$LCT8_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data88 = $con->query($dataCargogt)-> result_array();
				if ($data88){								
					$CARGO8_GT = $data88[0]['sum'];			
		
				} else if (empty($data8))  {		
					$CARGO8_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data88 = $con->query($CurahKeringt)-> result_array();
				if ($data88){								
					$CURKER8_GT = $data88[0]['sum'];			
				
				
				} else if (empty($data8))  {		
					$CURKER8_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data88 = $con->query($dataTankergt)-> result_array();
				if ($data88){								
					$TANKER8_GT = $data88[0]['sum'];			
	
				
				} else if (empty($data8))  {		
					$TANKER8_GT = 0;
		
				}
			
			} 
			
			if ($PERIODE == ''.$YEAR.'-09'){
			
				$bulan9 = 'September';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data9 = $con->query($dataRoro)-> result_array();
				if ($data9){								
				$RORO9 = $data9[0]['count'];		
		
				
				} else if (empty($data9))  {		
					$RORO9 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data9 = $con->query($dataLCT)-> result_array();
				if ($data9){								
				$LCT9 = $data9[0]['count'];		
	
				
				} else if (empty($data9))  {		
					$LCT9 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data9 = $con->query($dataCargo)-> result_array();
				if ($data9){								
				$CARGO9 = $data9[0]['count'];		
		
				
				} else if (empty($data9))  {		
					$CARGO9 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data9 = $con->query($dataCurahKering)-> result_array();
				if ($data9){								
				$CURKER9 = $data9[0]['count'];		
			
				
				} else if (empty($data9))  {		
					$CURKER9 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data9 = $con->query($dataTanker)-> result_array();
				if ($data9){								
				$TANKER9 = $data9[0]['count'];		
			  
				
				} else if (empty($data9))  {		
					$TANKER9 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data9 = $con->query($dataLain)-> result_array();
				if ($data9){								
					$LAIN9 = $data9[0]['count'];		
			  
				
				} else if (empty($data9))  {		
					$LAIN9 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data99 = $con->query($dataLaingt)-> result_array();
				if ($data99){								
					$LAIN9_GT = $data99[0]['sum'];			
			
				
				} else if (empty($data99))  {		
					$LAIN9_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data99 = $con->query($dataRorogt)-> result_array();
				if ($data99){								
					$RORO9_GT = $data99[0]['sum'];			
			
				
				} else if (empty($data9))  {		
					$RORO9_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data99 = $con->query($dataLCTgt)-> result_array();
				if ($data99){								
					$LCT9_GT = $data99[0]['sum'];			
					
				
				} else if (empty($data9))  {		
					$LCT9_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data99 = $con->query($dataCargogt)-> result_array();
				if ($data99){								
					$CARGO9_GT = $data99[0]['sum'];			
		
				} else if (empty($data9))  {		
					$CARGO9_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data99 = $con->query($CurahKeringt)-> result_array();
				if ($data99){								
					$CURKER9_GT = $data99[0]['sum'];			
				
				
				} else if (empty($data9))  {		
					$CURKER9_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data99 = $con->query($dataTankergt)-> result_array();
				if ($data99){								
					$TANKER9_GT = $data99[0]['sum'];			
	
				
				} else if (empty($data9))  {		
					$TANKER9_GT = 0;
		
				}
						

			} 
	
			if ($PERIODE == ''.$YEAR.'-10'){
	
				$bulan10 = 'Oktober';
				$PERIODE = "'$PERIODE'";
				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data10 = $con->query($dataRoro)-> result_array();
				if ($data10){								
				$RORO10 = $data10[0]['count'];		
		
				
				} else if (empty($data10))  {		
					$RORO10 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data10 = $con->query($dataLCT)-> result_array();
				if ($data10){								
				$LCT10 = $data10[0]['count'];		
	
				
				} else if (empty($data10))  {		
					$LCT10 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data10 = $con->query($dataCargo)-> result_array();
				if ($data10){								
				$CARGO10 = $data10[0]['count'];		
		
				
				} else if (empty($data10))  {		
					$CARGO10 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data10 = $con->query($dataCurahKering)-> result_array();
				if ($data10){								
				$CURKER10 = $data10[0]['count'];		
			
				
				} else if (empty($data10))  {		
					$CURKER10 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data10 = $con->query($dataTanker)-> result_array();
				if ($data10){								
				$TANKER10 = $data10[0]['count'];		
			  
				
				} else if (empty($data10))  {		
					$TANKER10 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data10 = $con->query($dataLain)-> result_array();
				if ($data10){								
					$LAIN10 = $data10[0]['count'];		
			  
				
				} else if (empty($data10))  {		
					$LAIN10 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1010 = $con->query($dataLaingt)-> result_array();
				if ($data1010){								
					$LAIN10_GT = $data1010[0]['sum'];			
			
				
				} else if (empty($data1010))  {		
					$LAIN10_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data10 = $con->query($dataRorogt)-> result_array();
				if ($data10){								
					$RORO10_GT = $data10[0]['sum'];			
			
				
				} else if (empty($data10))  {		
					$RORO10_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1010 = $con->query($dataLCTgt)-> result_array();
				if ($data1010){								
					$LCT10_GT = $data1010[0]['sum'];			
					
				
				} else if (empty($data1010))  {		
					$LCT10_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1010 = $con->query($dataCargogt)-> result_array();
				if ($data1010){								
					$CARGO10_GT = $data1010[0]['sum'];			
		
				} else if (empty($data1010))  {		
					$CARGO10_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1010 = $con->query($CurahKeringt)-> result_array();
				if ($data1010){								
					$CURKER10_GT = $data1010[0]['sum'];			
				
				
				} else if (empty($data1010))  {		
					$CURKER10_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1010 = $con->query($dataTankergt)-> result_array();
				if ($data1010){								
					$TANKER10_GT = $data1010[0]['sum'];			
	
				
				} else if (empty($data1010))  {		
					$TANKER10_GT = 0;
		
				}
						

			} 
			if ($PERIODE == ''.$YEAR.'-11'){	
	
				$bulan11 = 'November';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data11 = $con->query($dataRoro)-> result_array();
				if ($data11){								
				$RORO11 = $data11[0]['count'];		
		
				
				} else if (empty($data11))  {		
					$RORO11 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data11 = $con->query($dataLCT)-> result_array();
				if ($data11){								
				$LCT11 = $data11[0]['count'];		
	
				
				} else if (empty($data11))  {		
					$LCT11 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data11 = $con->query($dataCargo)-> result_array();
				if ($data11){								
				$CARGO11 = $data11[0]['count'];		
		
				
				} else if (empty($data11))  {		
					$CARGO11 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data11 = $con->query($dataCurahKering)-> result_array();
				if ($data11){								
				$CURKER11 = $data11[0]['count'];		
			
				
				} else if (empty($data11))  {		
					$CURKER11 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data11 = $con->query($dataTanker)-> result_array();
				if ($data11){								
				$TANKER11 = $data11[0]['count'];		
			  
				
				} else if (empty($data11))  {		
					$TANKER11 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data11 = $con->query($dataLain)-> result_array();
				if ($data11){								
					$LAIN11 = $data11[0]['count'];		
			  
				
				} else if (empty($data11))  {		
					$LAIN11 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1111 = $con->query($dataLaingt)-> result_array();
				if ($data1111){								
					$LAIN11_GT = $data1111[0]['sum'];			
			
				
				} else if (empty($data1111))  {		
					$LAIN11_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data11 = $con->query($dataRorogt)-> result_array();
				if ($data11){								
					$RORO11_GT = $data11[0]['sum'];			
			
				
				} else if (empty($data11))  {		
					$RORO11_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1111 = $con->query($dataLCTgt)-> result_array();
				if ($data1111){								
					$LCT11_GT = $data1111[0]['sum'];			
					
				
				} else if (empty($data1111))  {		
					$LCT11_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1111 = $con->query($dataCargogt)-> result_array();
				if ($data1111){								
					$CARGO11_GT = $data1111[0]['sum'];			
		
				} else if (empty($data1111))  {		
					$CARGO11_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1111 = $con->query($CurahKeringt)-> result_array();
				if ($data1111){								
					$CURKER11_GT = $data1111[0]['sum'];			
				
				
				} else if (empty($data1111))  {		
					$CURKER11_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1111 = $con->query($dataTankergt)-> result_array();
				if ($data1111){								
					$TANKER11_GT = $data1111[0]['sum'];			
	
				
				} else if (empty($data1111))  {		
					$TANKER11_GT = 0;
		
				}
			}

			if ($PERIODE == ''.$YEAR.'-12'){
			
				$bulan12 = 'Desember';
				$PERIODE = "'$PERIODE'";

				$dataRoro = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data12 = $con->query($dataRoro)-> result_array();
				if ($data12){								
				$RORO12 = $data12[0]['count'];		
		
				
				} else if (empty($data12))  {		
					$RORO12 = 0;				
		
				}

				$dataLCT = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data12 = $con->query($dataLCT)-> result_array();
				if ($data12){								
				$LCT12 = $data12[0]['count'];		
	
				
				} else if (empty($data12))  {		
					$LCT12 = 0;				
		
				}

				
				$dataCargo = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data12 = $con->query($dataCargo)-> result_array();
				if ($data12){								
				$CARGO12 = $data12[0]['count'];		
		
				
				} else if (empty($data12))  {		
					$CARGO12 = 0;				
			 
				}

				$dataCurahKering = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data12 = $con->query($dataCurahKering)-> result_array();
				if ($data12){								
				$CURKER12 = $data12[0]['count'];		
			
				
				} else if (empty($data12))  {		
					$CURKER12 = 0;				
	
				}

				$dataTanker = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data12 = $con->query($dataTanker)-> result_array();
				if ($data12){								
				$TANKER12 = $data12[0]['count'];		
			  
				
				} else if (empty($data12))  {		
					$TANKER12 = 0;				
			    
				}

				$dataLain = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"	
		
				';
				$data12 = $con->query($dataLain)-> result_array();
				if ($data12){								
					$LAIN12 = $data12[0]['count'];		
			  
				
				} else if (empty($data12))  {		
					$LAIN12 = 0;				
			    
				}

				$dataLaingt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lain.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1212 = $con->query($dataLaingt)-> result_array();
				if ($data1212){								
					$LAIN12_GT = $data1212[0]['sum'];			
			
				
				} else if (empty($data1212))  {		
					$LAIN12_GT = 0;
			
				}

				$dataRorogt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$roro.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1212 = $con->query($dataRorogt)-> result_array();
				if ($data1212){								
					$RORO12_GT = $data1212[0]['sum'];			
			
				
				} else if (empty($data1212))  {		
					$RORO12_GT = 0;
			
				}

				$dataLCTgt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$lct.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1212 = $con->query($dataLCTgt)-> result_array();
				if ($data1212){								
					$LCT12_GT = $data1212[0]['sum'];			
					
				
				} else if (empty($data1212))  {		
					$LCT12_GT = 0;
			
				}

				$dataCargogt= 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$cargo.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1212 = $con->query($dataCargogt)-> result_array();
				if ($data1212){								
					$CARGO12_GT = $data1212[0]['sum'];			
		
				} else if (empty($data1212))  {		
					$CARGO12_GT = 0;
			
				}

				$CurahKeringt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$curah_kering.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1212 = $con->query($CurahKeringt)-> result_array();
				if ($data1212){								
					$CURKER12_GT = $data1212[0]['sum'];			
				
				
				} else if (empty($data1212))  {		
					$CURKER12_GT = 0;
			
				}

				$dataTankergt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
				FROM "MART_TRF_KAPAL" mtk 
				JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
				WHERE to_char(mtk."PERIODE" ,'.$dates.') = '.$PERIODE.' and mk."JN_KAPAL" = '.$tanker.'
				GROUP BY mk."JN_KAPAL"
		
				';
				$data1212 = $con->query($dataTankergt)-> result_array();
				if ($data1212){								
					$TANKER12_GT = $data1212[0]['sum'];			
	
				
				} else if (empty($data1212))  {		
					$TANKER12_GT = 0;
		
				}
			}

				$terminal = 'INTERNASIONAL';
				$satuan = 'UNIT';
				$gt = 'GT';
				$terminal = "'$terminal'";
				$satuan = "'$satuan'";
				$gt = "'$gt'";
				$YEAR = "'$YEAR'";

				$conr = $this->load->database('ikt_postgree', TRUE);
				$dataRkapunit = 'SELECT "TERMINAL","PELAYARAN","TAHUN", "SATUAN", "JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
							from "DASHBOARD_RKAP_TRAFFIK"
						WHERE "TERMINAL" = '.$terminal.' and "TAHUN" = '.$YEAR.' and "SATUAN" = '.$satuan.'
						';
					
						$datar = $conr->query($dataRkapunit)-> result_array();
						if ($datar){
							$totalRkap = $datar[0]['JANUARI'] + $datar[0]['FEBRUARI'] + $datar[0]['MARET'] + $datar[0]['APRIL'] + $datar[0]['MEI']+ $datar[0]['JUNI']
						 + $datar[0]['JULI']+ $datar[0]['AGUSTUS']+ $datar[0]['SEPTEMBER']+ $datar[0]['OKTOBER']+ $datar[0]['NOVEMBER']+ $datar[0]['DESEMBER'];
						} else if(empty($datar)) {	
							$totalRkap = 0;
						}
		
						$dataRkapgt = 'SELECT "TERMINAL","PELAYARAN","TAHUN", "SATUAN", "JANUARI", "FEBRUARI", "MARET", "APRIL", "MEI", "JUNI", "JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
							from "DASHBOARD_RKAP_TRAFFIK"
						WHERE "TERMINAL" = '.$terminal.' and "TAHUN" = '.$YEAR.' and "SATUAN" = '.$gt.'
						';
					
						$datar = $conr->query($dataRkapgt)-> result_array();
						if ($datar){
							$totalRkap_GT = $datar[0]['JANUARI'] + $datar[0]['FEBRUARI'] + $datar[0]['MARET'] + $datar[0]['APRIL'] + $datar[0]['MEI']+ $datar[0]['JUNI']
						 + $datar[0]['JULI']+ $datar[0]['AGUSTUS']+ $datar[0]['SEPTEMBER']+ $datar[0]['OKTOBER']+ $datar[0]['NOVEMBER']+ $datar[0]['DESEMBER'];
						} else if(empty($datar)) {	
							$totalRkap_GT = 0;
						}
		
						
						
				}
			
				if ($OLD){
					$x = "$OLD-01";	
					$y = "$OLD-12";	
					$old = "'$x'";
					$ago = "'$y'";
					

					$dataRorot = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$roro.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	
			
					';
					$data12 = $con->query($dataRorot)-> result_array();
					if ($data12){								
					$ROROT = $data12[0]['count'];		
			
					
					} else if (empty($data12))  {		
						$ROROT = 0;				
			
					}
	
					$dataLctt = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$lct.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	
			
					';
					$data12 = $con->query($dataLctt)-> result_array();
					if ($data12){								
					$LCTT = $data12[0]['count'];		
		
					
					} else if (empty($data12))  {		
						$LCTT = 0;				
			
					}
	
					
					$dataCargot = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$cargo.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	
			
					';
					$data12 = $con->query($dataCargot)-> result_array();
					if ($data12){								
					$CARGOT = $data12[0]['count'];		
			
					
					} else if (empty($data12))  {		
						$CARGOT = 0;				
				 
					}
	
					$dataCurahkeringt = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$curah_kering.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	
			
					';
					$data12 = $con->query($dataCurahkeringt)-> result_array();
					if ($data12){								
						$CURKERT = $data12[0]['count'];		
				
					
					} else if (empty($data12))  {		
						$CURKERT = 0;				
		
					}
	
					$dataTankert = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$tanker.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	
			
					';
					$data12 = $con->query($dataTankert)-> result_array();
					if ($data12){								
					$TANKERT = $data12[0]['count'];		
				  
					
					} else if (empty($data12))  {		
						$TANKERT = 0;				
					
					}


					$dataLaint = 'SELECT COUNT("TOTAL_FREQ_UNIT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$lain.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	
			
					';
					$data12 = $con->query($dataLaint)-> result_array();
					if ($data12){								
					$LAINT = $data12[0]['count'];		
				  
					
					} else if (empty($data12))  {		
						$LAINT = 0;				
					
					}

					$dataRorogtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$roro.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	
					';
					$data_total1 = $con->query($dataRorogtt)-> result_array();
					if ($data_total1){
						$ROROT_GT = $data_total1[0]['sum'];					
					
				
					}else if (empty($data_total1)) {
						$ROROT_GT  = 0;	
					
					}
	
	
					$dataLctgtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$lct.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	 
					';
					$data_total2 = $con->query($dataLctgtt)-> result_array();
					if ($data_total2){
						$LCT_GT = $data_total2[0]['sum'];					
					
				
					}else if (empty($data_total2)){
						$LCT_GT  = 0;	
			
					}

					$dataCargogtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$cargo.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	 
					';
					$data_total3 = $con->query($dataCargogtt)-> result_array();
					if ($data_total2){
						$CARGOT_GT = $data_total3[0]['sum'];					
					
				
					}else if (empty($data_total3)){
						$CARGOT_GT  = 0;	
			
						
					}

					$dataCurahkeringgtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$curah_kering.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	
					';
					$data_total4 = $con->query($dataCurahkeringgtt)-> result_array();
					if ($data_total4){
						$CURKERT_GT = $data_total4[0]['sum'];					
					
				
					}else if (empty($data_total4)){
						$CURKERT_GT  = 0;	
			
					}

					$dataTankergtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$tanker.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	 
					';
					$data_total5 = $con->query($dataTankergtt)-> result_array();
					if ($data_total5){
						$TANKERT_GT = $data_total5[0]['sum'];					
					
				
					}else if (empty($data_total5)){
						$TANKERT_GT  = 0;	
			
					}

					$dataLaingtt = 'SELECT SUM("TOTAL_FREQ_GT"), "JN_KAPAL" 
					FROM "MART_TRF_KAPAL" mtk 
					JOIN "MST_KAPAL" mk on mtk."ID" = mk."CALL_SIGN" 
					WHERE mk."JN_KAPAL" = '.$lain.' and to_char(mtk."PERIODE" ,'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
					GROUP BY mk."JN_KAPAL"	  
					';
					$data_total5 = $con->query($dataLaingtt)-> result_array();
					if ($data_total5){
						$LAINT_GT = $data_total5[0]['sum'];					
					
				
					}else if (empty($data_total5)){
						$LAINT_GT  = 0;	
			
					}
				}

				if (empty($bulan1)){
					$excel->setActiveSheetIndex(0)->setCellValue('E5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('E6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('E10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('E12', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('E15', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('E16', ''); 
				} else if ($bulan1 == 'Januari'){
					$excel->setActiveSheetIndex(0)->setCellValue('E5', $RORO1);
					$excel->setActiveSheetIndex(0)->setCellValue('E6', $RORO1_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('E7', $LCT1); 
					$excel->setActiveSheetIndex(0)->setCellValue('E8', $LCT1_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('E9', $CARGO1); 
					$excel->setActiveSheetIndex(0)->setCellValue('E10', $CARGO1_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('E11', $CURKER1); 
					$excel->setActiveSheetIndex(0)->setCellValue('E12', $CURKER1_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('E13', $TANKER1); 
					$excel->setActiveSheetIndex(0)->setCellValue('E14', $TANKER1_GT);
					if (!empty($LAIN1) || !empty($LAIN1_GT) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('E15', $LAIN1); 
					$excel->setActiveSheetIndex(0)->setCellValue('E16', $LAIN1_GT); 
					} else if (empty($LAIN1) || empty($LAIN1_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('E15', '0'); 
						$excel->setActiveSheetIndex(0)->setCellValue('E16', '0'); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('E18', '=E5+E7+E9+E11+E13+E15'); 
					$excel->setActiveSheetIndex(0)->setCellValue('E19', '=E6+E8+E10+E12+E14+E16'); 
				} 

				if (empty($bulan2)){
					$excel->setActiveSheetIndex(0)->setCellValue('F5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F12', ''); 
				} else if ($bulan2 == 'Februari'){
					$excel->setActiveSheetIndex(0)->setCellValue('F5', $RORO2);	
					$excel->setActiveSheetIndex(0)->setCellValue('F6', $RORO2_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('F7', $LCT2); 
					$excel->setActiveSheetIndex(0)->setCellValue('F8', $LCT2_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('F9', $CARGO2); 
					$excel->setActiveSheetIndex(0)->setCellValue('F10', $CARGO2_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('F11', $CURKER2); 
					$excel->setActiveSheetIndex(0)->setCellValue('F12', $CURKER2_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('F13', $TANKER2); 
					$excel->setActiveSheetIndex(0)->setCellValue('F14', $TANKER2_GT); 
					if (!empty($LAIN2) || !empty($LAIN2_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('F15', $LAIN2); 
						$excel->setActiveSheetIndex(0)->setCellValue('F16', $LAIN2_GT); 
					} else if (empty($LAIN2) || empty($LAIN2_GT) ) { 
						$excel->setActiveSheetIndex(0)->setCellValue('F15', '0'); 
						$excel->setActiveSheetIndex(0)->setCellValue('F16', '0'); 	
					}
					$excel->setActiveSheetIndex(0)->setCellValue('F18', '=F5+F7+F9+F11+F13+F15'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F19', '=F6+F8+F10+F12+F14+F16'); 
				}
				
				if (empty($bulan3)){
					$excel->setActiveSheetIndex(0)->setCellValue('G5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G12', ''); 
				} else if ($bulan3 == 'Maret'){
					$excel->setActiveSheetIndex(0)->setCellValue('G5', $RORO3);
					$excel->setActiveSheetIndex(0)->setCellValue('G6', $RORO3_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('G7', $LCT3); 
					$excel->setActiveSheetIndex(0)->setCellValue('G8', $LCT3_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('G9', $CARGO3); 
					$excel->setActiveSheetIndex(0)->setCellValue('G10', $CARGO3_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('G11', $CURKER3); 
					$excel->setActiveSheetIndex(0)->setCellValue('G12', $CURKER3_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('G13', $TANKER3); 
					$excel->setActiveSheetIndex(0)->setCellValue('G14', $TANKER3_GT); 
					if (!empty($LAIN3) || !empty($LAIN3_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('G15', $LAIN3); 
						$excel->setActiveSheetIndex(0)->setCellValue('G16', $LAIN3_GT); 
						} else if (empty($LAIN3) || empty($LAIN3_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('G15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('G16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('G18', '=G5+G7+G9+G11+G13+G15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('G19', '=G6+G8+G10+G12+G14+G16'); 
				}
				if (empty($bulan4)){
					$excel->setActiveSheetIndex(0)->setCellValue('H5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H12', ''); 
				} else if ($bulan4 == 'April'){
					$excel->setActiveSheetIndex(0)->setCellValue('H5', $RORO4);
					$excel->setActiveSheetIndex(0)->setCellValue('H6', $RORO4_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('H7', $LCT4); 
					$excel->setActiveSheetIndex(0)->setCellValue('H8', $LCT4_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('H9', $CARGO4); 
					$excel->setActiveSheetIndex(0)->setCellValue('H10', $CARGO4_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('H11', $CURKER4); 
					$excel->setActiveSheetIndex(0)->setCellValue('H12', $CURKER4_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('H13', $TANKER4); 
					$excel->setActiveSheetIndex(0)->setCellValue('H14', $TANKER4_GT); 
						if (!empty($LAIN4) || !empty($LAIN4_GT) ) {
							$excel->setActiveSheetIndex(0)->setCellValue('H15', $LAIN4); 
							$excel->setActiveSheetIndex(0)->setCellValue('H16', $LAIN4_GT); 
						} else if (empty($LAIN4) || empty($LAIN4_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('H15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('H16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('H18', '=H5+H7+H9+H11+H13+H15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('H19', '=H6+H8+H10+H12+H14+H16'); 
				}
				if (empty($bulan5)){
					$excel->setActiveSheetIndex(0)->setCellValue('I5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I12', ''); 
				} else if ($bulan5 == 'Mei'){
					$excel->setActiveSheetIndex(0)->setCellValue('I5', $RORO5);
					$excel->setActiveSheetIndex(0)->setCellValue('I6', $RORO5_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('I7', $LCT5); 
					$excel->setActiveSheetIndex(0)->setCellValue('I8', $LCT5_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('I9', $CARGO5); 
					$excel->setActiveSheetIndex(0)->setCellValue('I10', $CARGO5_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('I11', $CURKER5); 
					$excel->setActiveSheetIndex(0)->setCellValue('I12', $CURKER5_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('I13', $TANKER5); 
					$excel->setActiveSheetIndex(0)->setCellValue('I14', $TANKER5_GT); 
					if (!empty($LAIN5) || !empty($LAIN5_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('I15', $LAIN5); 
						$excel->setActiveSheetIndex(0)->setCellValue('I16', $LAIN5_GT); 
						} else if (empty($LAIN5) || empty($LAIN5_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('I15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('I16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('I18', '=I5+I7+I9+I11+I13+I15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('I19', '=I6+I8+I10+I12+I14+I16');  
				}
				if (empty($bulan6)){
					$excel->setActiveSheetIndex(0)->setCellValue('J5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J12', ''); 
				} else if ($bulan6 == 'Juni'){
					$excel->setActiveSheetIndex(0)->setCellValue('J5', $RORO6);
					$excel->setActiveSheetIndex(0)->setCellValue('J6', $RORO6_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('J7', $LCT6); 
					$excel->setActiveSheetIndex(0)->setCellValue('J8', $LCT6_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('J9', $CARGO6); 
					$excel->setActiveSheetIndex(0)->setCellValue('J10', $CARGO6_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('J11', $CURKER6); 
					$excel->setActiveSheetIndex(0)->setCellValue('J12', $CURKER6_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('J13', $TANKER6); 
					$excel->setActiveSheetIndex(0)->setCellValue('J14', $TANKER6_GT); 
					if (!empty($LAIN6) || !empty($LAIN6_GT) ) {
					$excel->setActiveSheetIndex(0)->setCellValue('J15', $LAIN6); 
					$excel->setActiveSheetIndex(0)->setCellValue('J16', $LAIN6_GT); 
						} else if (empty($LAIN6) || empty($LAIN6_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('J15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('J16', '0'); 	
						}
					$excel->setActiveSheetIndex(0)->setCellValue('J18', '=J5+J7+J9+J11+J13+J15'); 
					$excel->setActiveSheetIndex(0)->setCellValue('J19', '=J6+J8+J10+J12+J14+J16'); 
				} 
				if (empty($bulan7)){
					$excel->setActiveSheetIndex(0)->setCellValue('K5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K12', ''); 
				} else if ($bulan7 == 'Juli'){
					$excel->setActiveSheetIndex(0)->setCellValue('K5', $RORO7);
					$excel->setActiveSheetIndex(0)->setCellValue('K6', $RORO7_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('K7', $LCT7); 
					$excel->setActiveSheetIndex(0)->setCellValue('K8', $LCT7_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('K9', $CARGO7); 
					$excel->setActiveSheetIndex(0)->setCellValue('K10', $CARGO7_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('K11', $CURKER7); 
					$excel->setActiveSheetIndex(0)->setCellValue('K12', $CURKER7_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('K13', $TANKER7); 
					$excel->setActiveSheetIndex(0)->setCellValue('K14', $TANKER7_GT); 
					if (!empty($LAIN7) || !empty($LAIN7_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('K15', $LAIN7); 
						$excel->setActiveSheetIndex(0)->setCellValue('K16', $LAIN7_GT); 
						} else if (empty($LAIN7) || empty($LAIN7_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('K15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('K16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('K18', '=K5+K7+K9+K11+K13+K15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('K19', '=K6+K8+K10+K12+K14+K16'); 
				} 
		
				if (empty($bulan8)){
					$excel->setActiveSheetIndex(0)->setCellValue('L5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L12', ''); 
				} else if ($bulan8 == 'Agustus'){
					$excel->setActiveSheetIndex(0)->setCellValue('L5', $RORO8);
					$excel->setActiveSheetIndex(0)->setCellValue('L6', $RORO8_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('L7', $LCT8); 
					$excel->setActiveSheetIndex(0)->setCellValue('L8', $LCT8_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('L9', $CARGO8); 
					$excel->setActiveSheetIndex(0)->setCellValue('L10', $CARGO8_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('L11', $CURKER8); 
					$excel->setActiveSheetIndex(0)->setCellValue('L12', $CURKER8_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('L13', $TANKER8); 
					$excel->setActiveSheetIndex(0)->setCellValue('L14', $TANKER8_GT); 
					if (!empty($LAIN8) || !empty($LAIN8_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('L15', $LAIN8); 
						$excel->setActiveSheetIndex(0)->setCellValue('L16', $LAIN8_GT); 
						} else if (empty($LAIN8) || empty($LAIN8_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('L15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('L16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('L18', '=L5+L7+L9+L11+L13+L15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('L19', '=L6+L8+L10+L12+L14+L16'); 
				}
				
				if (empty($bulan9)){
					$excel->setActiveSheetIndex(0)->setCellValue('M5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M12', ''); 
				} else if ($bulan9 == 'September'){
					$excel->setActiveSheetIndex(0)->setCellValue('M5', $RORO9);
					$excel->setActiveSheetIndex(0)->setCellValue('M6', $RORO9_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('M7', $LCT9); 
					$excel->setActiveSheetIndex(0)->setCellValue('M8', $LCT9_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('M9', $CARGO9); 
					$excel->setActiveSheetIndex(0)->setCellValue('M10', $CARGO9_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('M11', $CURKER9); 
					$excel->setActiveSheetIndex(0)->setCellValue('M12', $CURKER9_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('M13', $TANKER9); 
					$excel->setActiveSheetIndex(0)->setCellValue('M14', $TANKER9_GT); 
					if (!empty($LAIN9) || !empty($LAIN9_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('M15', $LAIN9); 
						$excel->setActiveSheetIndex(0)->setCellValue('M16', $LAIN9_GT); 
						} else if (empty($LAIN9) || empty($LAIN9_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('M15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('M16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('M18', '=M5+M7+M9+M11+M13+M15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('M19', '=M6+M8+M10+M12+M14+M16'); 
				}
				
				if (empty($bulan10)){
					$excel->setActiveSheetIndex(0)->setCellValue('N5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N12', ''); 
				} else if ($bulan10 == 'Oktober'){
					$excel->setActiveSheetIndex(0)->setCellValue('N5', $RORO10);
					$excel->setActiveSheetIndex(0)->setCellValue('N6', $RORO10_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('N7', $LCT10); 
					$excel->setActiveSheetIndex(0)->setCellValue('N8', $LCT10_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('N9', $CARGO10); 
					$excel->setActiveSheetIndex(0)->setCellValue('N10', $CARGO10_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('N11', $CURKER10); 
					$excel->setActiveSheetIndex(0)->setCellValue('N12', $CURKER10_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('N13', $TANKER10); 
					$excel->setActiveSheetIndex(0)->setCellValue('N14', $TANKER10_GT); 
					if (!empty($LAIN10) || !empty($LAIN10_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('N15', $LAIN10); 
						$excel->setActiveSheetIndex(0)->setCellValue('N16', $LAIN10_GT); 
						} else if (empty($LAIN10) || empty($LAIN10_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('N15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('N16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('N18', '=N5+N7+N9+N11+N13+N15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('N19', '=N6+N8+N10+N12+N14+N16'); 
				}
				
				if (empty($bulan11)){
					$excel->setActiveSheetIndex(0)->setCellValue('O5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O12', ''); 
				} else if ($bulan11 ==  'November'){
					$excel->setActiveSheetIndex(0)->setCellValue('O5', $RORO11);
					$excel->setActiveSheetIndex(0)->setCellValue('O6', $RORO11_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('O7', $LCT11); 
					$excel->setActiveSheetIndex(0)->setCellValue('O8', $LCT11_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('O9', $CARGO11); 
					$excel->setActiveSheetIndex(0)->setCellValue('O10', $CARGO11_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('O11', $CURKER11); 
					$excel->setActiveSheetIndex(0)->setCellValue('O12', $CURKER11_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('O13', $TANKER11); 
					$excel->setActiveSheetIndex(0)->setCellValue('O14', $TANKER11_GT); 
					if (!empty($LAIN11) || !empty($LAIN11_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('O15', $LAIN11); 
						$excel->setActiveSheetIndex(0)->setCellValue('O16', $LAIN11_GT); 
						} else if (empty($LAIN11) || empty($LAIN11_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('O15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('O16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('O18', '=O5+O7+O9+O11+O13+O15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('O19', '=O6+O8+O10+O12+O14+O16'); 
				}
				
				if (empty($bulan12)){
					$excel->setActiveSheetIndex(0)->setCellValue('P5', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P12', ''); 
				} else if ($bulan12 == 'Desember'){
					$excel->setActiveSheetIndex(0)->setCellValue('P5', $RORO12);
					$excel->setActiveSheetIndex(0)->setCellValue('P6', $RORO12_GT);			
					$excel->setActiveSheetIndex(0)->setCellValue('P7', $LCT12); 
					$excel->setActiveSheetIndex(0)->setCellValue('P8', $LCT12_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('P9', $CARGO12); 
					$excel->setActiveSheetIndex(0)->setCellValue('P10', $CARGO12_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('P11', $CURKER12); 
					$excel->setActiveSheetIndex(0)->setCellValue('P12', $CURKER12_GT); 
					$excel->setActiveSheetIndex(0)->setCellValue('P13', $TANKER12); 
					$excel->setActiveSheetIndex(0)->setCellValue('P14', $TANKER12_GT); 
					if (!empty($LAIN12) || !empty($LAIN12_GT) ) {
						$excel->setActiveSheetIndex(0)->setCellValue('P15', $LAIN12); 
						$excel->setActiveSheetIndex(0)->setCellValue('P16', $LAIN12_GT); 
						} else if (empty($LAIN12) || empty($LAIN12_GT) ) { 
							$excel->setActiveSheetIndex(0)->setCellValue('P15', '0'); 
							$excel->setActiveSheetIndex(0)->setCellValue('P16', '0'); 	
						}
						$excel->setActiveSheetIndex(0)->setCellValue('P18', '=P5+P7+P9+P11+P13+P15'); 
						$excel->setActiveSheetIndex(0)->setCellValue('P19', '=P6+P8+P10+P12+P14+P16'); 
				}
			
				$excel->setActiveSheetIndex(0)->setCellValue('Q5', '=SUM(E5:P5)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q6', '=SUM(E6:P6)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q7', '=SUM(E7:P7)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q8', '=SUM(E8:P8)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q9', '=SUM(E9:P9)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q10', '=SUM(E10:P10)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q11', '=SUM(E11:P11)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q12', '=SUM(E12:P12)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q13', '=SUM(E13:P13)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q14', '=SUM(E14:P14)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q15', '=SUM(E15:P15)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q16', '=SUM(E16:P16)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q18', '=SUM(E18:P18)');
				$excel->setActiveSheetIndex(0)->setCellValue('Q19', '=SUM(E19:P19)');				
				$excel->setActiveSheetIndex(0)->setCellValue('R5', $ROROT);	
				$excel->setActiveSheetIndex(0)->setCellValue('R6', $ROROT_GT);	
				$excel->setActiveSheetIndex(0)->setCellValue('R7', $LCTT);	
				$excel->setActiveSheetIndex(0)->setCellValue('R8', $LCT_GT);
				$excel->setActiveSheetIndex(0)->setCellValue('R9', $CARGOT);	
				$excel->setActiveSheetIndex(0)->setCellValue('R10', $CARGOT_GT);	
				$excel->setActiveSheetIndex(0)->setCellValue('R11', $CURKERT);	
				$excel->setActiveSheetIndex(0)->setCellValue('R12', $CURKERT_GT);	
				$excel->setActiveSheetIndex(0)->setCellValue('R13', $TANKERT);	
				$excel->setActiveSheetIndex(0)->setCellValue('R14', $TANKERT_GT);	
			
					$excel->setActiveSheetIndex(0)->setCellValue('R15', $LAINT); 
					$excel->setActiveSheetIndex(0)->setCellValue('R16', $LAINT_GT); 
					
					$excel->setActiveSheetIndex(0)->setCellValue('R18', '=R5+R7+R9+R11+R13+R15'); 
					$excel->setActiveSheetIndex(0)->setCellValue('R19', '=R6+R8+R10+R12+R14+R16'); 
				$excel->setActiveSheetIndex(0)->setCellValue('S18', $totalRkap);
				$excel->setActiveSheetIndex(0)->setCellValue('S19', $totalRkap_GT);
	

			// // Set width kolom
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(10); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(25); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(25); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('E')->setWidth(15); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('F')->setWidth(15); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('G')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('H')->setWidth(15); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('I')->setWidth(15); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('J')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('K')->setWidth(15); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('L')->setWidth(15); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('M')->setWidth(15); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('N')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('O')->setWidth(15); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
            $excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
			$excel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
			
			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Laporan_Trafik_Kedatangan_Kapal");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Laporan_Trafik_Kedatangan_Kapal_INTR_'.$id.'_'.$end.'.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');

	
  }
}