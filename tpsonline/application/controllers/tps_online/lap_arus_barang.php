<?php
/** Laporan Trafik/Arus Barang
  *	Modul untuk mengunduh laporan trafik atau arus barang berdasarkan tahun dan terminal
  *
  */

class lap_arus_barang extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/Model_laparusbarang'
								
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
		redirect('tps_online/laporan/listview');
	}


	public function listview(){	

		$num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/Model_laparusbarang');

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

		$this->load->view('backend/pages/tps_online/lap_arus_barang/listview',$data);
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

	public function export_laporan_xls($id,$end, $terminal)
	{
		
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Laporan_Trafik_Arus_Barang')						
								   ->setTitle("Laporan_Trafik_Arus_Barang")
								   ->setSubject("Laporan_Trafik_Arus_Barang'")
								   ->setDescription("Laporan_Data_Trafik_Arus_Barang ")
								   ->setKeywords("Data_Trafik_Arus_Barang");
		
			// Buat sebuah variabel untuk menampung pengaturan style dari header ta	bel
		
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
			$excel->setActiveSheetIndex(0)->setCellValue('C1', "Uraian");
			$excel->setActiveSheetIndex(0)->mergeCells('C1:D2')->setCellValue('B1', "");
			$excel->setActiveSheetIndex(0)->mergeCells('B2:D2')->setCellValue('B2', "");
			$excel->setActiveSheetIndex(0)->mergeCells('D1:D2')->setCellValue('D1', "");

			$excel->setActiveSheetIndex(0)->mergeCells('E1:E2')->setCellValue('E1', "Satuan");
			$excel->setActiveSheetIndex(0)->mergeCells('F1:Q1')->setCellValue('F1', "REALISASI PERIODE BERJALAN IKT");
			$excel->setActiveSheetIndex(0)->setCellValue('A3', "1");
			$excel->setActiveSheetIndex(0)->mergeCells('B3:C3')->setCellValue('B3', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('D3', "3");
			$excel->setActiveSheetIndex(0)->mergeCells('E3:P3')->setCellValue('E3', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('F2', "Januari"); 
			$excel->setActiveSheetIndex(0)->setCellValue('G2', "Februari"); 
			$excel->setActiveSheetIndex(0)->setCellValue('H2', "Maret"); 
			$excel->setActiveSheetIndex(0)->setCellValue('I2', "April"); 
			$excel->setActiveSheetIndex(0)->setCellValue('J2', "Mei"); 
			$excel->setActiveSheetIndex(0)->setCellValue('K2', "Juni"); 
			$excel->setActiveSheetIndex(0)->setCellValue('L2', "Juli"); 
			$excel->setActiveSheetIndex(0)->setCellValue('M2', "Agustus"); 
			$excel->setActiveSheetIndex(0)->setCellValue('N2', "September"); 
			$excel->setActiveSheetIndex(0)->setCellValue('O2', "Oktober"); 
			$excel->setActiveSheetIndex(0)->setCellValue('P2', "November"); 
			$excel->setActiveSheetIndex(0)->setCellValue('Q2', "Desember"); 
			$excel->setActiveSheetIndex(0)->mergeCells('R1:R2')->setCellValue('R1', "Realisasi Tahun 2022");
			$excel->setActiveSheetIndex(0)->mergeCells('B4:C4')->setCellValue('B4', "DALAM NEGERI");
			$excel->setActiveSheetIndex(0)->setCellValue('A6', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A9', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A12', "3");
			$excel->setActiveSheetIndex(0)->setCellValue('A15', "4");

			$excel->setActiveSheetIndex(0)->setCellValue('A19', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A22', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A25', "3");
			$excel->setActiveSheetIndex(0)->setCellValue('A28', "4");
			$excel->setActiveSheetIndex(0)->mergeCells('C5:D5')->setCellValue('C5', "BONGKAR");
			$excel->setActiveSheetIndex(0)->setCellValue('D6', "KENDARAAN (CBU)");
			$excel->setActiveSheetIndex(0)->setCellValue('E6', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E7', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D9', "TRUK/BUS");
			$excel->setActiveSheetIndex(0)->setCellValue('E9', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E10', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D12', "ALAT BERAT");
			$excel->setActiveSheetIndex(0)->setCellValue('E12', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E13', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D15', "MOTOR");
			$excel->setActiveSheetIndex(0)->setCellValue('E15', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E16', "M3");

			$excel->setActiveSheetIndex(0)->mergeCells('C18:D18')->setCellValue('C18', "MUAT");		
			$excel->setActiveSheetIndex(0)->setCellValue('D19', "KENDARAAN (CBU)");
			$excel->setActiveSheetIndex(0)->setCellValue('E19', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E20', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D22', "TRUK/BUS");
			$excel->setActiveSheetIndex(0)->setCellValue('E22', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E23', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D25', "ALAT BERAT");
			$excel->setActiveSheetIndex(0)->setCellValue('E25', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E26', "M3");		
			$excel->setActiveSheetIndex(0)->setCellValue('D28', "MOTOR");
			$excel->setActiveSheetIndex(0)->setCellValue('E28', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E29', "M3");

			$excel->setActiveSheetIndex(0)->mergeCells('A31:D31')->setCellValue('A31', "JUMLAH BARANG DALAM NEGERI");
			$excel->setActiveSheetIndex(0)->mergeCells('A32:D32')->setCellValue('A32', "");	
	
			$excel->setActiveSheetIndex(0)->setCellValue('E31', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E32', "M3");
		

			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(11); 
			$excel->getActiveSheet()->getStyle('A1:Q1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('A2:Q2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C18')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C21')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B41')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C42')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C55')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A67')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('R1:R67')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('A31:S31')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A32:S32')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A33:S33')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('R3:R32')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('Q1:Q2')->applyFromArray($style_col);	
			$excel->getActiveSheet()->getStyle('A1:A32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('B1:B32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('C1:C32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('D1:D32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('E1:E32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('F1:F32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('G1:G32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H1:H32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('I1:I32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('J1:J32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('K1:K32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('L1:L32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('M1:M32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('N1:N32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('O1:O32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('P1:P32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('Q1:Q32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('R1:R32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('S1:S32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('T1:T32')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('A3:T3')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A4:T4')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A5:T5')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A6:T6')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A7:T7')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A8:T8')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A9:T9')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A10:T10')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A11:T11')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A12:T12')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A13:T13')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A14:T14')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A15:T15')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A16:T16')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A17:T17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A18:T18')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A19:T19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A20:T20')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A21:T21')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A22:T22')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A23:T23')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A24:T24')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A25:T25')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A26:T26')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A27:T27')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A28:T28')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A29:T29')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A30:T30')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A31:T31')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A32:T32')->applyFromArray($style_row);				

			$excel->getActiveSheet()->getStyle('S1:S32')->getFont()->setBold(true);			
			$excel->getActiveSheet()->getStyle('T1:T32')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('F6:Z6')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('F7:Z7')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");			
			$excel->getActiveSheet()->getStyle('F8:Z8')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F9:Z9')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F10:Z10')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F11:Z11')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F12:Z12')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F13:Z13')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F14:Z14')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F15:Z15')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F16:Z16')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('F17:Z17')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('F18:Z18')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F19:Z19')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F20:Z20')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('F21:Z21')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F22:Z22')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F23:Z23')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F24:Z24')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F25:Z25')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F26:Z26')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F27:Z27')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F28:Z28')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F29:Z29')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F30:Z30')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F31:Z31')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F32:Z32')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");

		
			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya			

			$this->load->model('tps_online/Model_laparusbarang');
			$con = $this->load->database('ikt_postgree', TRUE);
		
			$model = $this->Model_laparusbarang->get_data_laparusbarang($id,$end);

			$cont = count($model['data']);
			$x = 0;
 
			while($x < $cont) {
				 $PERIODE = $model["data"][$x]['periode'];	
				$x++;
		
			$TAHUN = explode('-', $PERIODE);
			$YEAR = $TAHUN[0];
			$MM = $TAHUN[1];
			$OLD = $YEAR -1;
		
			if (!empty($YEAR)) {			
				$excel->setActiveSheetIndex(0)->mergeCells('R1:R2')->setCellValue('R1', "Realisasi Tahun $YEAR");
				$excel->setActiveSheetIndex(0)->mergeCells('S1:S2')->setCellValue('S1', "Realisasi Tahun $OLD");			
				$excel->setActiveSheetIndex(0)->mergeCells('T1:T2')->setCellValue('T1', "RKAP Tahun $YEAR");			
			} 
	
			$dates = "'yyyy-mm'";
			$ekspor  = "'EKSPOR'";
			$impor = "'IMPOR'";
			$e = "'E'";
			$m = "'M'";
			$i = "'I'";
			$bongkar = "'BONGKAR'";
			$muat = "'MUAT'";
			$generalcargo = "'GENERAL CARGO'";	
			$generalcar = "'%GC %'";
			$alatberat = "'ALAT BERAT'";
			$truckbus = "'TRUCK/BUS'"; 
			$cbuu ="'%PASSENGER CAR%'";
			$sepedamotor ="'MOTOR'";
			$mobil ="'MOBIL'";
			$unit = "'UNIT'";
			$m3 = "'M3'";
			$domestik = 'DOMESTIK';
			$internasional = 'CAR01';
			$domestik = "'$domestik'";			
			$internasional = "'$internasional'";
			$cardom = "'CARDOM'";
			if ($PERIODE == ''.$YEAR.'-01'){				

				$bulan01 = 'Januari';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT01 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM01 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT01 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU01 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataMobil = $con->query($dataKendaraan)-> result_array();
				 $KT01  = $dataMobil[0]['UNIT'];

				 $dataKendaraan2 = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataMobil2 = $con->query($dataKendaraan2)-> result_array();		
				 $KU01  = $dataMobil2[0]['M3'];	

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT01  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU01  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU01  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM01  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT01 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU01 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT01 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM01 = $dataMuatCargo[0]['M3'];
	
				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT01 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU01 = $dataMuatCbu[0]['M3'];
		
				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT01 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU01 = $dataMuatTruck[0]['M3'];		

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU01 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM01 = $dataMuatMotor[0]['M3'];	
		
			}
			
			if ($PERIODE == ''.$YEAR.'-02'){				

				$bulan02 = 'Februari';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT02 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM02 = $dataGeneralCargo[0]['M3'];	
			
				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT02 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU02 = $dataAlatBerat[0]['M3'];				 
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT02  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU02  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT02  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU02  = $dataTruckBus[0]['M3'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU02  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM02  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT02 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU02 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT02 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM02 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT02 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'), SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU02 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT02 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'), SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU02 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU02 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'), SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM02 = $dataMuatMotor[0]['M3'];	

			
			}
			if ($PERIODE == ''.$YEAR.'-03'){				

				$bulan03 = 'Maret';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT03 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'), SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM03 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT03 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'), SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU03 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT03  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'), SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU03  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT03  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'), SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU03  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU03  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM03  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT03 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU03 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT03 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3"from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM03 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT03 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU03 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT03 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU03 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU03 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM03 = $dataMuatMotor[0]['M3'];	

			}

			if ($PERIODE == ''.$YEAR.'-04'){				

				$bulan04 = 'April';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT04 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM04 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT04 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU04 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT04  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU04  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT04  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU04  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU04  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM04  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT04 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU04 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT04 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM04 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT04 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU04 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT04 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU04 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU04 = $dataMuatMotor[0]['UNIT'];

				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM04 = $dataMuatMotor[0]['M3'];	

			}
			
			if ($PERIODE == ''.$YEAR.'-05'){				

				$bulan05 = 'Mei';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT05 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM05 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT05 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU05 = $dataAlatBerat[0]['M3'];
		
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT05  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU05  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT05  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU05  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU05  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM05  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT05 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU05 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT05 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM05 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT05 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU05 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT05 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU05 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU05 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM05 = $dataMuatMotor[0]['M3'];	
	
			}

			if ($PERIODE == ''.$YEAR.'-06'){				

				$bulan06 = 'Juni';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT06 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM06 = $dataGeneralCargo[0]['M3'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT06 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU06 = $dataAlatBerat[0]['M3'];
		
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT06  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU06  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT06  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU06  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU06  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM06  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT06 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU06 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT06 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM06 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT06 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU06 = $dataMuatCbu[0]['M3'];
				
				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT06 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU06 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU06 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM06 = $dataMuatMotor[0]['M3'];	

			}

			if ($PERIODE == ''.$YEAR.'-07'){				

				$bulan07 = 'Juli';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT07 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM07 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT07 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU07 = $dataAlatBerat[0]['M3'];
				
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT07  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU07  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT07  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU07  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU07  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM07  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT07 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU07 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT07 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM07 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT07 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU07 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT07 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU07 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU07 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM07 = $dataMuatMotor[0]['M3'];	

			}

			if ($PERIODE == ''.$YEAR.'-08'){				

				$bulan08 = 'Agustus';
			$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT08 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM08 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT08 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU08 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT08  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU08  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT08  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU08  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU08  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM08  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT08 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU08 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT08 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM08 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT08 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU08 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT08 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU08 = $dataMuatTruck[0]['M3'];	
	
				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU08 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM08 = $dataMuatMotor[0]['M3'];	

			}

			if ($PERIODE == ''.$YEAR.'-09'){			
			
				$bulan09 = 'September';		
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT09 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM09 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT09 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU09 = $dataAlatBerat[0]['M3'];
				
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT09  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU09  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT09  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU09  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU09  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM09  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT09 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU09 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT09 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM09 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT09 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU09 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT09 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU09 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU09 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM09 = $dataMuatMotor[0]['M3'];	

			}
			if ($PERIODE == ''.$YEAR.'-10'){				

				$bulan10 =  'Oktober';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT10 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM10 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT10 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU10 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT10  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU10  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT10  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU10  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU10  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM10  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT10 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU10 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT10 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM10 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT10 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU10 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT10 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU10 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU10 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM10 = $dataMuatMotor[0]['M3'];	

			}

			if ($PERIODE == ''.$YEAR.'-11'){				

				$bulan11 = 'November';
				$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT11 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.'and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM11 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT11 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU11 = $dataAlatBerat[0]['M3'];
				
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT11  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU11  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT11  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU11  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataMotor = $con->query($dataMotor)-> result_array();
				 $MU11  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM11  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT11 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU11 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT11 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM11 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT11 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU11 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT11 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU11 = $dataMuatTruck[0]['M3'];	
	
				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU11 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM11 = $dataMuatMotor[0]['M3'];	

			}
				if ($PERIODE == ''.$YEAR.'-12'){				

					$bulan12 = 'Desember';
					$PERIODE = "'$PERIODE'";
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNT12 = $dataGeneralCargo[0]['UNIT'];
	
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNM12 = $dataGeneralCargo[0]['M3'];	
				
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AT12 = $dataAlatBerat[0]['UNIT'];
	
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AU12 = $dataAlatBerat[0]['M3'];
				
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
					 $KT12  = $dataKendaraan[0]['UNIT'];
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
					 $KU12  = $dataKendaraan[0]['M3'];

					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 	 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TT12  = $dataTruckBus[0]['UNIT'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TU12  = $dataTruckBus[0]['M3']; 
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				     where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
					';
					 $dataMotor = $con->query($dataMotor)-> result_array();
					 $MU12  = $dataMotor[0]['UNIT'];
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
					 $dataMotor = $con->query($dataMotor)-> result_array();		
					 $MM12  = $dataMotor[0]['M3']; 
	
					//MUAT		
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
					$MAT12 = $dataMuatAlber[0]['UNIT'];
	
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
	
					$MAU12 = $dataMuatAlber[0]['M3'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCT12 = $dataMuatCargo[0]['UNIT'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCM12 = $dataMuatCargo[0]['M3'];

					 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 	 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	 
					 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
					 $MKT12 = $dataMuatCbu[0]['UNIT'];
	
					$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
					$MKU12 = $dataMuatCbu[0]['M3'];

					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTT12 = $dataMuatTruck[0]['UNIT'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTU12 = $dataMuatTruck[0]['M3'];	

					 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 	 where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMU12 = $dataMuatMotor[0]['UNIT'];
	
	
					$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 	where "TERMINAL" = '.$domestik.' and "SOURCE" = '.$cardom.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
				    $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMM12 = $dataMuatMotor[0]['M3'];	
	
			}		
	
					$conr = $this->load->database('ikt_postgree', TRUE);   
					
					$terminal = 'DOMESTIK';
					$unit = 'UNIT';	
					$jenis1 = 'BONGKAR';
					$jenis2 = 'MUAT';					
					$ton = 'TON';			
					$m3 = 'M3';	

					$terminal = "'$terminal'";
					$unit = "'$unit'";
					$YEAR = "'$YEAR'";
					$jenis1 = "'$jenis1'";
					$jenis2 = "'$jenis2'";
					$ton = "'$ton'";
					$m3 = "'$m3'";
			;	
					$komoditi1 = 'GENERAL CARGO';
					$komoditi1 = "'$komoditi1'";
					$datar1 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi1.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar1 = $conr->query($datar1)-> result_array();

					if ($datar1){
						$totalRkap1 = $datar1[0]['JANUARI'] + $datar1[0]['FEBRUARI'] + $datar1[0]['MARET'] + $datar1[0]['APRIL'] + $datar1[0]['MEI']+ $datar1[0]['JUNI']
					 + $datar1[0]['JULI']+ $datar1[0]['AGUSTUS']+ $datar1[0]['SEPTEMBER']+ $datar1[0]['OKTOBER']+ $datar1[0]['NOVEMBER']+ $datar1[0]['DESEMBER'];
					} else if(empty($datar1)) {	
						$totalRkap1 = 0;
					}
			
					$datar2 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi1.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar2 = $conr->query($datar2)-> result_array();
					if ($datar2){
						$totalRkap2 = $datar2[0]['JANUARI'] + $datar2[0]['FEBRUARI'] + $datar2[0]['MARET'] + $datar2[0]['APRIL'] + $datar2[0]['MEI']+ $datar2[0]['JUNI']
					 + $datar2[0]['JULI']+ $datar2[0]['AGUSTUS']+ $datar2[0]['SEPTEMBER']+ $datar2[0]['OKTOBER']+ $datar2[0]['NOVEMBER']+ $datar2[0]['DESEMBER'];
					} else if(empty($datar2)) {	
						$totalRkap2 = 0;
					}

					$komoditi2 = 'TRUCK/BUS';
					$komoditi2 = "'$komoditi2'";
					$datar3 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi2.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar3 = $conr->query($datar3)-> result_array();
					if ($datar3){
						$totalRkap3 = $datar3[0]['JANUARI'] + $datar3[0]['FEBRUARI'] + $datar3[0]['MARET'] + $datar3[0]['APRIL'] + $datar3[0]['MEI']+ $datar3[0]['JUNI']
					 + $datar3[0]['JULI']+ $datar3[0]['AGUSTUS']+ $datar3[0]['SEPTEMBER']+ $datar3[0]['OKTOBER']+ $datar3[0]['NOVEMBER']+ $datar3[0]['DESEMBER'];
					} else if(empty($datar3)) {	
						$totalRkap3 = 0;
					}

					$datar4 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi2.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar4 = $conr->query($datar4)-> result_array();
					if ($datar4){
						$totalRkap4 = $datar4[0]['JANUARI'] + $datar4[0]['FEBRUARI'] + $datar4[0]['MARET'] + $datar4[0]['APRIL'] + $datar4[0]['MEI']+ $datar4[0]['JUNI']
					 + $datar4[0]['JULI']+ $datar4[0]['AGUSTUS']+ $datar4[0]['SEPTEMBER']+ $datar4[0]['OKTOBER']+ $datar4[0]['NOVEMBER']+ $datar4[0]['DESEMBER'];
					} else if(empty($datar4)) {	
						$totalRkap4 = 0;
					}				

					$komoditi3 = 'ALAT BERAT';
					$komoditi3 = "'$komoditi3'";
					$datar5 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi3.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar5 = $conr->query($datar5)-> result_array();
					if ($datar5){
						$totalRkap5 = $datar5[0]['JANUARI'] + $datar5[0]['FEBRUARI'] + $datar5[0]['MARET'] + $datar5[0]['APRIL'] + $datar5[0]['MEI']+ $datar5[0]['JUNI']
					 + $datar5[0]['JULI']+ $datar5[0]['AGUSTUS']+ $datar5[0]['SEPTEMBER']+ $datar5[0]['OKTOBER']+ $datar5[0]['NOVEMBER']+ $datar5[0]['DESEMBER'];
					} else if(empty($datar5)) {	
						$totalRkap5 = 0;
					}
;
					$datar6 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi3.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar6 = $conr->query($datar6)-> result_array();
					if ($datar6){
						$totalRkap6 = $datar6[0]['JANUARI'] + $datar6[0]['FEBRUARI'] + $datar6[0]['MARET'] + $datar6[0]['APRIL'] + $datar6[0]['MEI']+ $datar6[0]['JUNI']
					 + $datar6[0]['JULI']+ $datar6[0]['AGUSTUS']+ $datar6[0]['SEPTEMBER']+ $datar6[0]['OKTOBER']+ $datar6[0]['NOVEMBER']+ $datar6[0]['DESEMBER'];
					} else if(empty($datar6)) {	
						$totalRkap6 = 0;
					}

					$komoditi4 = 'MOBIL';
					$komoditi4 = "'$komoditi4'";
					$datar7 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi4.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar7 = $conr->query($datar7)-> result_array();
					if ($datar7){
						$totalRkap7 = $datar7[0]['JANUARI'] + $datar7[0]['FEBRUARI'] + $datar7[0]['MARET'] + $datar7[0]['APRIL'] + $datar7[0]['MEI']+ $datar7[0]['JUNI']
					 + $datar7[0]['JULI']+ $datar7[0]['AGUSTUS']+ $datar7[0]['SEPTEMBER']+ $datar7[0]['OKTOBER']+ $datar7[0]['NOVEMBER']+ $datar7[0]['DESEMBER'];
					} else if(empty($datar7)) {	
						$totalRkap7 = 0;
					}

					$datar8 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi4.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar8 = $conr->query($datar8)-> result_array();
					if ($datar8){
						$totalRkap8 = $datar8[0]['JANUARI'] + $datar8[0]['FEBRUARI'] + $datar8[0]['MARET'] + $datar8[0]['APRIL'] + $datar8[0]['MEI']+ $datar8[0]['JUNI']
					 + $datar8[0]['JULI']+ $datar8[0]['AGUSTUS']+ $datar8[0]['SEPTEMBER']+ $datar8[0]['OKTOBER']+ $datar8[0]['NOVEMBER']+ $datar8[0]['DESEMBER'];
					} else if(empty($datar8)) {	
						$totalRkap8 = 0;
					}
					
					
					$komoditi5 = 'MOTOR';
					$komoditi5 = "'$komoditi5'";
					$datar9 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi5.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar9 = $conr->query($datar9)-> result_array();
					if ($datar9){
						$totalRkap9 = $datar9[0]['JANUARI'] + $datar9[0]['FEBRUARI'] + $datar9[0]['MARET'] + $datar9[0]['APRIL'] + $datar9[0]['MEI']+ $datar9[0]['JUNI']
					 + $datar9[0]['JULI']+ $datar9[0]['AGUSTUS']+ $datar9[0]['SEPTEMBER']+ $datar9[0]['OKTOBER']+ $datar9[0]['NOVEMBER']+ $datar9[0]['DESEMBER'];
					} else if(empty($datar9)) {	
						$totalRkap9 = 0;
					}

					$datar10 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
					and "KOMODITI" = '.$komoditi5.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar10 = $conr->query($datar10)-> result_array();
					if ($datar10){
						$totalRkap10 = $datar10[0]['JANUARI'] + $datar10[0]['FEBRUARI'] + $datar10[0]['MARET'] + $datar10[0]['APRIL'] + $datar10[0]['MEI']+ $datar10[0]['JUNI']
					 + $datar10[0]['JULI']+ $datar10[0]['AGUSTUS']+ $datar10[0]['SEPTEMBER']+ $datar10[0]['OKTOBER']+ $datar10[0]['NOVEMBER']+ $datar10[0]['DESEMBER'];
					} else if(empty($datar10)) {	
						$totalRkap10 = 0;
					}


				//MUAT
					$komoditi11 = 'GENERAL CARGO';
					$komoditi11 = "'$komoditi11'";
					$datar11 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi11.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar11 = $conr->query($datar11)-> result_array();
					if ($datar11){
						$totalRkap11 = $datar11[0]['JANUARI'] + $datar11[0]['FEBRUARI'] + $datar11[0]['MARET'] + $datar11[0]['APRIL'] + $datar11[0]['MEI']+ $datar11[0]['JUNI']
					 + $datar11[0]['JULI']+ $datar11[0]['AGUSTUS']+ $datar11[0]['SEPTEMBER']+ $datar11[0]['OKTOBER']+ $datar11[0]['NOVEMBER']+ $datar11[0]['DESEMBER'];
					} else if(empty($datar11)) {	
						$totalRkap11 = 0;
					}

					$datar12 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi11.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar12 = $conr->query($datar12)-> result_array();
					if ($datar12){
						$totalRkap12 = $datar12[0]['JANUARI'] + $datar12[0]['FEBRUARI'] + $datar12[0]['MARET'] + $datar12[0]['APRIL'] + $datar12[0]['MEI']+ $datar12[0]['JUNI']
					 + $datar12[0]['JULI']+ $datar12[0]['AGUSTUS']+ $datar12[0]['SEPTEMBER']+ $datar12[0]['OKTOBER']+ $datar12[0]['NOVEMBER']+ $datar12[0]['DESEMBER'];
					} else if(empty($datar12)) {	
						$totalRkap12 = 0;
					}

					$komoditi21 = 'TRUCK/BUS';
					$komoditi21 = "'$komoditi21'";
					$datar21 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi2.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar21 = $conr->query($datar21)-> result_array();
					if ($datar21){
						$totalRkap21 = $datar21[0]['JANUARI'] + $datar21[0]['FEBRUARI'] + $datar21[0]['MARET'] + $datar21[0]['APRIL'] + $datar21[0]['MEI']+ $datar21[0]['JUNI']
					 + $datar21[0]['JULI']+ $datar21[0]['AGUSTUS']+ $datar21[0]['SEPTEMBER']+ $datar21[0]['OKTOBER']+ $datar21[0]['NOVEMBER']+ $datar21[0]['DESEMBER'];
					} else if(empty($datar21)) {	
						$totalRkap21 = 0;
					}

					$datar22 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi21.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar22 = $conr->query($datar22)-> result_array();
					if ($datar22){
						$totalRkap22 = $datar22[0]['JANUARI'] + $datar22[0]['FEBRUARI'] + $datar22[0]['MARET'] + $datar22[0]['APRIL'] + $datar22[0]['MEI']+ $datar22[0]['JUNI']
					 + $datar22[0]['JULI']+ $datar22[0]['AGUSTUS']+ $datar22[0]['SEPTEMBER']+ $datar22[0]['OKTOBER']+ $datar22[0]['NOVEMBER']+ $datar22[0]['DESEMBER'];
					} else if(empty($datar22)) {	
						$totalRkap22 = 0;
					}

					$komoditi31 = 'ALAT BERAT';
					$komoditi31 = "'$komoditi31'";
					$datar31 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi31.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar31 = $conr->query($datar31)-> result_array();
					if ($datar31){
						$totalRkap31 = $datar31[0]['JANUARI'] + $datar31[0]['FEBRUARI'] + $datar31[0]['MARET'] + $datar31[0]['APRIL'] + $datar31[0]['MEI']+ $datar31[0]['JUNI']
					 + $datar31[0]['JULI']+ $datar31[0]['AGUSTUS']+ $datar31[0]['SEPTEMBER']+ $datar31[0]['OKTOBER']+ $datar31[0]['NOVEMBER']+ $datar31[0]['DESEMBER'];
					} else if(empty($datar31)) {	
						$totalRkap31 = 0;
					}
					$datar32 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi31.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar32 = $conr->query($datar32)-> result_array();
					if ($datar32){
						$totalRkap32 = $datar32[0]['JANUARI'] + $datar32[0]['FEBRUARI'] + $datar32[0]['MARET'] + $datar32[0]['APRIL'] + $datar32[0]['MEI']+ $datar32[0]['JUNI']
					 + $datar32[0]['JULI']+ $datar32[0]['AGUSTUS']+ $datar32[0]['SEPTEMBER']+ $datar32[0]['OKTOBER']+ $datar32[0]['NOVEMBER']+ $datar32[0]['DESEMBER'];
					} else if(empty($datar32)) {	
						$totalRkap32 = 0;
					}

					$komoditi41 = 'MOBIL';
					$komoditi41 = "'$komoditi41'";
					$datar41 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					 FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi41.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar41 = $conr->query($datar41)-> result_array();
					if ($datar41){
						$totalRkap41 = $datar41[0]['JANUARI'] + $datar41[0]['FEBRUARI'] + $datar41[0]['MARET'] + $datar41[0]['APRIL'] + $datar41[0]['MEI']+ $datar41[0]['JUNI']
					 + $datar41[0]['JULI']+ $datar41[0]['AGUSTUS']+ $datar41[0]['SEPTEMBER']+ $datar41[0]['OKTOBER']+ $datar41[0]['NOVEMBER']+ $datar41[0]['DESEMBER'];
					} else if(empty($datar41)) {	
						$totalRkap41 = 0;
					}
					$datar42 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi41.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar42 = $conr->query($datar42)-> result_array();
					if ($datar42){
						$totalRkap42 = $datar42[0]['JANUARI'] + $datar42[0]['FEBRUARI'] + $datar42[0]['MARET'] + $datar42[0]['APRIL'] + $datar42[0]['MEI']+ $datar42[0]['JUNI']
					 + $datar42[0]['JULI']+ $datar42[0]['AGUSTUS']+ $datar42[0]['SEPTEMBER']+ $datar42[0]['OKTOBER']+ $datar42[0]['NOVEMBER']+ $datar42[0]['DESEMBER'];
					} else if(empty($datar42)) {	
						$totalRkap42 = 0;
					}

					$komoditi51 = 'MOTOR';
					$komoditi51 = "'$komoditi51'";
					$datar51 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi51.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
					$datar51 = $conr->query($datar51)-> result_array();
					if ($datar51){
						$totalRkap51 = $datar51[0]['JANUARI'] + $datar51[0]['FEBRUARI'] + $datar51[0]['MARET'] + $datar51[0]['APRIL'] + $datar51[0]['MEI']+ $datar51[0]['JUNI']
					 + $datar51[0]['JULI']+ $datar51[0]['AGUSTUS']+ $datar51[0]['SEPTEMBER']+ $datar51[0]['OKTOBER']+ $datar51[0]['NOVEMBER']+ $datar51[0]['DESEMBER'];
					} else if(empty($datar51)) {	
						$totalRkap51 = 0;
					}

					$datar52 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
					FROM "DASHBOARD_RKAP_ARUS_BARANG" 
					where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
					and "KOMODITI" = '.$komoditi51.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
					$datar52 = $conr->query($datar52)-> result_array();
					if ($datar52){
						$totalRkap52 = $datar52[0]['JANUARI'] + $datar52[0]['FEBRUARI'] + $datar52[0]['MARET'] + $datar52[0]['APRIL'] + $datar52[0]['MEI']+ $datar52[0]['JUNI']
					 + $datar52[0]['JULI']+ $datar52[0]['AGUSTUS']+ $datar52[0]['SEPTEMBER']+ $datar52[0]['OKTOBER']+ $datar52[0]['NOVEMBER']+ $datar52[0]['DESEMBER'];
					} else if(empty($datar52)) {	
						$totalRkap52 = 0;
					}

				}
							$x = "$OLD-01";	
							$y = "$OLD-12";	
							$old = "'$x'";
							$ago = "'$y'";
							$datest = "'yyyy-mm'";
							$OLD = "'$OLD'";

							$datat = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.' and "SOURCE" = '.$cardom.' 					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat = $con->query($datat)-> result_array();
							$ATT = $datat[0]['UNIT'];

							$datat = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$alatberat.'  and  "SOURCE" = '.$cardom.' 						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat = $con->query($datat)-> result_array();
							$AUT = $datat[0]['M3'];

							$datat1 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.' and "SOURCE" = '.$cardom.'  						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat1 = $con->query($datat1)-> result_array();
							$TTT = $datat1[0]['UNIT'];

							$datat1 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$truckbus.'  and "SOURCE" = '.$cardom.' 						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat1 = $con->query($datat1)-> result_array();					
							$TUT = $datat1[0]['M3'];	

							$datat2 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SOURCE" = '.$cardom.' 					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';

							$datat2 = $con->query($datat2)-> result_array();
							$KTT = $datat2[0]['UNIT'];

							$datat2 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$mobil.' and "SOURCE" = '.$cardom.'  					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';

							$datat2 = $con->query($datat2)-> result_array();
							$KUT = $datat2[0]['M3'];				
							

							$datat3 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.' and "SOURCE" = '.$cardom.' 						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						    ';		

							$datat3 = $con->query($datat3)-> result_array();
							$GCTT = $datat3[0]['UNIT'];

							$datat3 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$generalcargo.'  and "SOURCE" = '.$cardom.' 						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';		

							$datat3 = $con->query($datat3)-> result_array();				
							$GCMT = $datat3[0]['M3'];

							$datat31= 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SOURCE" = '.$cardom.'  					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';			

							$datat31 = $con->query($datat31)-> result_array();
							$SMUT = $datat31[0]['UNIT'];

							$datat31= 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$bongkar.' and "KOMODITI" = '.$sepedamotor.' and "SOURCE" = '.$cardom.' 						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						    ';			

							$datat31 = $con->query($datat31)-> result_array();						
							$SMMT = $datat31[0]['M3'];

							$datat4 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.' and "SOURCE" = '.$cardom.' 					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';			
		
							$datat4 = $con->query($datat4)-> result_array();
							$MATT = $datat4[0]['UNIT'];

							$datat4 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$alatberat.'  and "SOURCE" = '.$cardom.'  						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';			
		
							$datat4 = $con->query($datat4)-> result_array();
							$MAUT = $datat4[0]['M3'];	

							$datat5 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcargo.' and "SOURCE" = '.$cardom.'  					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat5 = $con->query($datat5)-> result_array();
							$MGCTT = $datat5[0]['UNIT'];

							$datat5 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$generalcargo.'  and "SOURCE" = '.$cardom.' 					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat5 = $con->query($datat5)-> result_array();						
							$MGCMT = $datat5[0]['M3'];

							$datat6 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.' and "SOURCE" = '.$cardom.' 					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat6 = $con->query($datat6)-> result_array();
							$MKTT = $datat6[0]['UNIT'];

							$datat6 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$mobil.'  and "SOURCE" = '.$cardom.'  						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';

							$datat6 = $con->query($datat6)-> result_array();
							$MKUT = $datat6[0]['M3'];

							$datat7 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SOURCE" = '.$cardom.' 						
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						    ';	

							$datat7 = $con->query($datat7)-> result_array();
							$MTTT = $datat7[0]['UNIT'];

							$datat7 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$truckbus.' and "SOURCE" = '.$cardom.' 					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';	

							$datat7 = $con->query($datat7)-> result_array();
							$MTUT = $datat7[0]['M3'];	

							$datat8 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' 	and "SOURCE" = '.$cardom.' 					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat8 = $con->query($datat8)-> result_array();
							$SMIT = $datat8[0]['UNIT'];

							$datat8 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
							where "TERMINAL" = '.$domestik.' and "JENIS" = '.$muat.' and "KOMODITI" = '.$sepedamotor.' and "SOURCE" = '.$cardom.' 					
							and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
							';
		
							$datat8 = $con->query($datat8)-> result_array();
							$SMIU = $datat8[0]['M3'];	
	
				if (empty($bulan01)){
					$excel->setActiveSheetIndex(0)->setCellValue('F6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F9', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F12', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F13', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F15', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F16', ''); 
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('F22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F29', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('F32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F33', '');
			

				} else if ($bulan01 == 'Januari'){
					$excel->setActiveSheetIndex(0)->setCellValue('F6', $KT01);
					$excel->setActiveSheetIndex(0)->setCellValue('F7', $KU01);
					$excel->setActiveSheetIndex(0)->setCellValue('F9', $TT01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F10', $TU01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F12', $AT01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F13', $AU01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F15', $MU01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F16', $MM01); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('F19', $MKT01);
					$excel->setActiveSheetIndex(0)->setCellValue('F20', $MKU01);
					$excel->setActiveSheetIndex(0)->setCellValue('F22', $MTT01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F23', $MTU01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F25', $MAT01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F26', $MAU01);
					$excel->setActiveSheetIndex(0)->setCellValue('F28', $SMU01); 
					$excel->setActiveSheetIndex(0)->setCellValue('F29', $SMM01); 
				
					$excel->setActiveSheetIndex(0)->setCellValue('F31', '=F6+F9+F12+F15+F19+F22+F25+F28');	
					$excel->setActiveSheetIndex(0)->setCellValue('F32', '=F7+F10+F13+F16+F20+F23+F26+F29');
					

				}
				if (empty($bulan02)){
					$excel->setActiveSheetIndex(0)->setCellValue('G6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G9', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G12', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G13', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G15', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G16', ''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('G19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G29', ''); 						
					$excel->setActiveSheetIndex(0)->setCellValue('G31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('G32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('G35', '');	
				} else if ($bulan02 == 'Februari'){
					$excel->setActiveSheetIndex(0)->setCellValue('G6', $KT02);
					$excel->setActiveSheetIndex(0)->setCellValue('G7', $KU02);
					$excel->setActiveSheetIndex(0)->setCellValue('G9', $TT02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G10', $TU02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G12', $AT02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G13', $AU02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G15', $MU02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G16', $MM02); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('G19', $MKT02);
					$excel->setActiveSheetIndex(0)->setCellValue('G20', $MKU02);
					$excel->setActiveSheetIndex(0)->setCellValue('G22', $MTT02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G23', $MTU02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G25', $MAT02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G26', $MAU02);
					$excel->setActiveSheetIndex(0)->setCellValue('G28', $SMU02); 
					$excel->setActiveSheetIndex(0)->setCellValue('G29', $SMM02); 
			
					$excel->setActiveSheetIndex(0)->setCellValue('G31', '=G6+G9+G12+G15+G19+G22+G25+G28');	
					$excel->setActiveSheetIndex(0)->setCellValue('G32', '=G7+G10+G13+G16+G20+G23+G26+G29');
								

				}
				if (empty($bulan03)){
					$excel->setActiveSheetIndex(0)->setCellValue('H6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H9', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H12', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H13', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H15', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H16', ''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('H19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H29', '');		
					$excel->setActiveSheetIndex(0)->setCellValue('H31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('H32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('H35', '');
				
				} else if ($bulan03 == 'Maret'){
					$excel->setActiveSheetIndex(0)->setCellValue('H6', $KT03);
					$excel->setActiveSheetIndex(0)->setCellValue('H7', $KU03);
					$excel->setActiveSheetIndex(0)->setCellValue('H9', $TT03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H10', $TU03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H12', $AT03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H13', $AU03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H15', $MU03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H16', $MM03); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('H19', $MKT03);
					$excel->setActiveSheetIndex(0)->setCellValue('H20', $MKU03);
					$excel->setActiveSheetIndex(0)->setCellValue('H22', $MTT03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H23', $MTU03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H25', $MAT03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H26', $MAU03);
					$excel->setActiveSheetIndex(0)->setCellValue('H28', $SMU03); 
					$excel->setActiveSheetIndex(0)->setCellValue('H29', $SMM03); 
			
					$excel->setActiveSheetIndex(0)->setCellValue('H31', '=H6+H9+H12+H15+H19+H22+H25+H28');	
					$excel->setActiveSheetIndex(0)->setCellValue('H32', '=H7+H10+H13+H16+H20+H23+H26+H29');
				
				}

				if (empty($bulan04)){
					$excel->setActiveSheetIndex(0)->setCellValue('I6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I9','');
					$excel->setActiveSheetIndex(0)->setCellValue('I10','');
					$excel->setActiveSheetIndex(0)->setCellValue('I12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('I19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I29', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I31', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('I32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('I35', '');
				} else if ($bulan04 == 'April'){
					$excel->setActiveSheetIndex(0)->setCellValue('I6', $KT04);
					$excel->setActiveSheetIndex(0)->setCellValue('I7', $KU04);
					$excel->setActiveSheetIndex(0)->setCellValue('I9', $TT04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I10', $TU04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I12', $AT04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I13', $AU04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I15', $MU04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I16', $MM04); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('I19', $MKT04);
					$excel->setActiveSheetIndex(0)->setCellValue('I20', $MKU04);
					$excel->setActiveSheetIndex(0)->setCellValue('I22', $MTT04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I23', $MTU04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I25', $MAT04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I26', $MAU04);
					$excel->setActiveSheetIndex(0)->setCellValue('I28', $SMU04); 
					$excel->setActiveSheetIndex(0)->setCellValue('I29', $SMM04); 
			
					$excel->setActiveSheetIndex(0)->setCellValue('I31', '=I6+I9+I12+I15+I19+I22+I25+I28');	
					$excel->setActiveSheetIndex(0)->setCellValue('I32', '=I7+I10+I13+I16+I20+I23+I26+I29');
			
				}
				if (empty($bulan05)){
					$excel->setActiveSheetIndex(0)->setCellValue('J6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J9','');
					$excel->setActiveSheetIndex(0)->setCellValue('J10','');
					$excel->setActiveSheetIndex(0)->setCellValue('J12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('J19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J29', ''); 			
					$excel->setActiveSheetIndex(0)->setCellValue('J31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('J32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('J35', '');	

				} else if ($bulan05 == 'Mei'){
					$excel->setActiveSheetIndex(0)->setCellValue('J6', $KT05);
					$excel->setActiveSheetIndex(0)->setCellValue('J7', $KU05);
					$excel->setActiveSheetIndex(0)->setCellValue('J9', $TT05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J10', $TU05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J12', $AT05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J13', $AU05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J15', $MU05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J16', $MM05); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('J19', $MKT05);
					$excel->setActiveSheetIndex(0)->setCellValue('J20', $MKU05);
					$excel->setActiveSheetIndex(0)->setCellValue('J22', $MTT05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J23', $MTU05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J25', $MAT05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J26', $MAU05);
					$excel->setActiveSheetIndex(0)->setCellValue('J28', $SMU05); 
					$excel->setActiveSheetIndex(0)->setCellValue('J29', $SMM05); 
			
					$excel->setActiveSheetIndex(0)->setCellValue('J31', '=J6+J9+J12+J15+J19+J22+J25+J28');	
					$excel->setActiveSheetIndex(0)->setCellValue('J32', '=J7+J10+J13+J16+J20+J23+J26+J29');
			
				}
			
				if (empty($bulan06)){
					$excel->setActiveSheetIndex(0)->setCellValue('K6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K9','');
					$excel->setActiveSheetIndex(0)->setCellValue('K10','');
					$excel->setActiveSheetIndex(0)->setCellValue('K12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('K19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K29', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K31', '');				
					$excel->setActiveSheetIndex(0)->setCellValue('K32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K35', '');

				} else if ($bulan06 == 'Juni'){
					$excel->setActiveSheetIndex(0)->setCellValue('K6', $KT06);
					$excel->setActiveSheetIndex(0)->setCellValue('K7', $KU06);
					$excel->setActiveSheetIndex(0)->setCellValue('K9', $TT06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K10', $TU06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K12', $AT06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K13', $AU06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K15', $MU06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K16', $MM06); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('K19', $MKT06);
					$excel->setActiveSheetIndex(0)->setCellValue('K20', $MKU06);
					$excel->setActiveSheetIndex(0)->setCellValue('K22', $MTT06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K23', $MTU06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K25', $MAT06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K26', $MAU06);
					$excel->setActiveSheetIndex(0)->setCellValue('K28', $SMU06); 
					$excel->setActiveSheetIndex(0)->setCellValue('K29', $SMM06); 
			
					$excel->setActiveSheetIndex(0)->setCellValue('K31', '=K6+K9+K12+K15+K19+K22+K25+K28');	
					$excel->setActiveSheetIndex(0)->setCellValue('K32', '=K7+K10+K13+K16+K20+K23+K26+K29');
				
				} 
				if (empty($bulan07)){
					$excel->setActiveSheetIndex(0)->setCellValue('L6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L9','');
					$excel->setActiveSheetIndex(0)->setCellValue('L10','');
					$excel->setActiveSheetIndex(0)->setCellValue('L12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('L19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L22','');
					$excel->setActiveSheetIndex(0)->setCellValue('L23','');
					$excel->setActiveSheetIndex(0)->setCellValue('L25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L29',''); 	
					$excel->setActiveSheetIndex(0)->setCellValue('L31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('L32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L34', '');		
					$excel->setActiveSheetIndex(0)->setCellValue('L35', '');	
				} else if ($bulan07 == 'Juli'){
					$excel->setActiveSheetIndex(0)->setCellValue('L6', $KT07);
					$excel->setActiveSheetIndex(0)->setCellValue('L7', $KU07);
					$excel->setActiveSheetIndex(0)->setCellValue('L9', $TT07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L10', $TU07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L12', $AT07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L13', $AU07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L15', $MU07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L16', $MM07); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('L19', $MKT07);
					$excel->setActiveSheetIndex(0)->setCellValue('L20', $MKU07);
					$excel->setActiveSheetIndex(0)->setCellValue('L22', $MTT07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L23', $MTU07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L25', $MAT07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L26', $MAU07);
					$excel->setActiveSheetIndex(0)->setCellValue('L28', $SMU07); 
					$excel->setActiveSheetIndex(0)->setCellValue('L29', $SMM07); 
			
					$excel->setActiveSheetIndex(0)->setCellValue('L31', '=L6+L9+L12+L15+L19+L22+L25+L28');	
					$excel->setActiveSheetIndex(0)->setCellValue('L32', '=L7+L10+L13+L16+L20+L23+L26+L29');
					
				} 
		
				if (empty($bulan08)){
					$excel->setActiveSheetIndex(0)->setCellValue('M6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M9','');
					$excel->setActiveSheetIndex(0)->setCellValue('M10','');
					$excel->setActiveSheetIndex(0)->setCellValue('M12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('M19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M22','');
					$excel->setActiveSheetIndex(0)->setCellValue('M23','');
					$excel->setActiveSheetIndex(0)->setCellValue('M25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('ML26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M29','');		
					$excel->setActiveSheetIndex(0)->setCellValue('M31', '');		
					$excel->setActiveSheetIndex(0)->setCellValue('M32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('M35', '');	
				} else if ($bulan08 == 'Agustus'){
					$excel->setActiveSheetIndex(0)->setCellValue('M6', $KT08);
					$excel->setActiveSheetIndex(0)->setCellValue('M7', $KU08);
					$excel->setActiveSheetIndex(0)->setCellValue('M9', $TT08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M10', $TU08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M12', $AT08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M13', $AU08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M15', $MU08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M16', $MM08); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('M19', $MKT08);
					$excel->setActiveSheetIndex(0)->setCellValue('M20', $MKU08);
					$excel->setActiveSheetIndex(0)->setCellValue('M22', $MTT08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M23', $MTU08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M25', $MAT08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M26', $MAU08);
					$excel->setActiveSheetIndex(0)->setCellValue('M28', $SMU08); 
					$excel->setActiveSheetIndex(0)->setCellValue('M29', $SMM08); 
			
					$excel->setActiveSheetIndex(0)->setCellValue('M31', '=M6+M9+M12+M15+M19+M22+M25+M28');	
					$excel->setActiveSheetIndex(0)->setCellValue('M32', '=M7+M10+M13+M16+M20+M23+M26+M29');
		

				}
				
				if (empty($bulan09)){
					$excel->setActiveSheetIndex(0)->setCellValue('N6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N9','');
					$excel->setActiveSheetIndex(0)->setCellValue('N10','');
					$excel->setActiveSheetIndex(0)->setCellValue('N12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('N19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N22','');
					$excel->setActiveSheetIndex(0)->setCellValue('N23','');
					$excel->setActiveSheetIndex(0)->setCellValue('N25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N29','');
					$excel->setActiveSheetIndex(0)->setCellValue('N31', '');				
					$excel->setActiveSheetIndex(0)->setCellValue('N32', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('N33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('N35', '');
				} else if ($bulan09 == 'September'){
					$excel->setActiveSheetIndex(0)->setCellValue('N6', $KT09);
					$excel->setActiveSheetIndex(0)->setCellValue('N7', $KU09);
					$excel->setActiveSheetIndex(0)->setCellValue('N9', $TT09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N10', $TU09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N12', $AT09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N13', $AU09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N15', $MU09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N16', $MM09); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('N19', $MKT09);
					$excel->setActiveSheetIndex(0)->setCellValue('N20', $MKU09);
					$excel->setActiveSheetIndex(0)->setCellValue('N22', $MTT09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N23', $MTU09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N25', $MAT09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N26', $MAU09);
					$excel->setActiveSheetIndex(0)->setCellValue('N28', $SMU09); 
					$excel->setActiveSheetIndex(0)->setCellValue('N29', $SMM09); 
			
					$excel->setActiveSheetIndex(0)->setCellValue('N31', '=N6+N9+N12+N15+N19+N22+N25+N28');	
					$excel->setActiveSheetIndex(0)->setCellValue('N32', '=N7+N10+N13+N16+N20+N23+N26+N29');
			
				}
				if (empty($bulan10)){
					$excel->setActiveSheetIndex(0)->setCellValue('O6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O9', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O10','');
					$excel->setActiveSheetIndex(0)->setCellValue('O12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('O19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O22','');
					$excel->setActiveSheetIndex(0)->setCellValue('O23','');
					$excel->setActiveSheetIndex(0)->setCellValue('O25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O29','');
					$excel->setActiveSheetIndex(0)->setCellValue('O31', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O35', '');

				}elseif ($bulan10 == 'Oktober'){
					//BONGKAR
					$excel->setActiveSheetIndex(0)->setCellValue('O6', $KT10);
					$excel->setActiveSheetIndex(0)->setCellValue('O7', $KU10);
					$excel->setActiveSheetIndex(0)->setCellValue('O9', $TT10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O10', $TU10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O12', $AT10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O13', $AU10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O15', $MU10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O16', $MM10); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('O19', $MKT10);
					$excel->setActiveSheetIndex(0)->setCellValue('O20', $MKU10);
					$excel->setActiveSheetIndex(0)->setCellValue('O22', $MTT10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O23', $MTU10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O25', $MAT10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O26', $MAU10);
					$excel->setActiveSheetIndex(0)->setCellValue('O28', $SMU10); 
					$excel->setActiveSheetIndex(0)->setCellValue('O29', $SMM10); 
						
					$excel->setActiveSheetIndex(0)->setCellValue('O31', '=O6+O9+O12+O15+O19+O22+O25+O28');	
					$excel->setActiveSheetIndex(0)->setCellValue('O32', '=O7+O10+O13+O16+O20+O23+O26+O29');
				
			 } 

				if (empty($bulan11)){
					$excel->setActiveSheetIndex(0)->setCellValue('P6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P9','');
					$excel->setActiveSheetIndex(0)->setCellValue('P10','');
					$excel->setActiveSheetIndex(0)->setCellValue('P12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('P19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P22','');
					$excel->setActiveSheetIndex(0)->setCellValue('P23','');
					$excel->setActiveSheetIndex(0)->setCellValue('P25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P29',''); 	
					$excel->setActiveSheetIndex(0)->setCellValue('P31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('P32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P35', '');	
								
				} else if ($bulan11 == 'November'){
					$excel->setActiveSheetIndex(0)->setCellValue('P6', $KT11);
					$excel->setActiveSheetIndex(0)->setCellValue('P7', $KU11);
					$excel->setActiveSheetIndex(0)->setCellValue('P9', $TT11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P10', $TU11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P12', $AT11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P13', $AU11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P15', $MU11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P16', $MM11); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('P19', $MKT11);
					$excel->setActiveSheetIndex(0)->setCellValue('P20', $MKU11);
					$excel->setActiveSheetIndex(0)->setCellValue('P22', $MTT11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P23', $MTU11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P25', $MAT11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P26', $MAU11);
					$excel->setActiveSheetIndex(0)->setCellValue('P28', $SMU11); 
					$excel->setActiveSheetIndex(0)->setCellValue('P29', $SMM11); 
					
					$excel->setActiveSheetIndex(0)->setCellValue('P31', '=P6+P9+P12+P15+P19+P22+P25+P28');	
					$excel->setActiveSheetIndex(0)->setCellValue('P32', '=P7+P10+P13+P16+P20+P23+P26+P29');
				
				}				
			
				if (empty($bulan12)){
					$excel->setActiveSheetIndex(0)->setCellValue('Q6','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q7','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q9','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q10','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('Q19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q22','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q23','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q29',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q31', '');		
					$excel->setActiveSheetIndex(0)->setCellValue('Q32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q35', '');
				} else if ($bulan12 == 'Desember'){
					$excel->setActiveSheetIndex(0)->setCellValue('Q6', $KT12);
					$excel->setActiveSheetIndex(0)->setCellValue('Q7', $KU12);
					$excel->setActiveSheetIndex(0)->setCellValue('Q9', $TT12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q10', $TU12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q12', $AT12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q13', $AU12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q15', $MU12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q16', $MM12); 
			
					// MUAT			
					$excel->setActiveSheetIndex(0)->setCellValue('Q19', $MKT12);
					$excel->setActiveSheetIndex(0)->setCellValue('Q20', $MKU12);
					$excel->setActiveSheetIndex(0)->setCellValue('Q22', $MTT12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q23', $MTU12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q25', $MAT12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q26', $MAU12);
					$excel->setActiveSheetIndex(0)->setCellValue('Q28', $SMU12); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q29', $SMM12); 
								
					$excel->setActiveSheetIndex(0)->setCellValue('Q31', '=Q6+Q9+Q12+Q15+Q19+Q22+Q25+Q28');	
					$excel->setActiveSheetIndex(0)->setCellValue('Q32', '=Q7+Q10+Q13+Q16+Q20+Q23+Q26+Q29');
							
				}

				$excel->setActiveSheetIndex(0)->setCellValue('R6', '=SUM(F6:Q6)');
				$excel->setActiveSheetIndex(0)->setCellValue('R7', '=SUM(F7:Q7)');
				$excel->setActiveSheetIndex(0)->setCellValue('R9', '=SUM(F9:Q9)');
				$excel->setActiveSheetIndex(0)->setCellValue('R10', '=SUM(F10:Q10)');
				$excel->setActiveSheetIndex(0)->setCellValue('R12', '=SUM(F12:Q12)');
				$excel->setActiveSheetIndex(0)->setCellValue('R13', '=SUM(F13:Q13)');
				$excel->setActiveSheetIndex(0)->setCellValue('R15', '=SUM(F15:Q15)');
				$excel->setActiveSheetIndex(0)->setCellValue('R16', '=SUM(F16:Q16)');
				$excel->setActiveSheetIndex(0)->setCellValue('R19', '=SUM(F19:Q19)');
				$excel->setActiveSheetIndex(0)->setCellValue('R20', '=SUM(F20:Q20)');
				$excel->setActiveSheetIndex(0)->setCellValue('R22', '=SUM(F22:Q22)');
				$excel->setActiveSheetIndex(0)->setCellValue('R23', '=SUM(F23:Q23)');
				$excel->setActiveSheetIndex(0)->setCellValue('R25', '=SUM(F25:Q25)');
				$excel->setActiveSheetIndex(0)->setCellValue('R26', '=SUM(F26:Q26)');
				$excel->setActiveSheetIndex(0)->setCellValue('R28', '=SUM(F28:Q28)');
				$excel->setActiveSheetIndex(0)->setCellValue('R29', '=SUM(F29:Q29)');	
				$excel->setActiveSheetIndex(0)->setCellValue('R31', '=SUM(F31:Q31)');
				$excel->setActiveSheetIndex(0)->setCellValue('R32', '=SUM(F32:Q32)');
			
				$excel->setActiveSheetIndex(0)->setCellValue('S6',  $KTT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S7', $KUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S9', $TTT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S10', $TUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S12', $ATT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S13', $AUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S15', $SMUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S16', $SMMT?: '0' );
		
				$excel->setActiveSheetIndex(0)->setCellValue('S19', $MKTT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S20', $MKUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S22', $MTTT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S23', $MTUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S25', $MATT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S26', $MAUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S28', $SMIT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S29', $SMIU?: '0' );

				$excel->setActiveSheetIndex(0)->setCellValue('S31', '=S6+S9+S12+S15+S19+S22+S25+S28');	
				$excel->setActiveSheetIndex(0)->setCellValue('S32', '=S7+S10+S13+S16+S20+S23+S26+S29');
		

				$excel->setActiveSheetIndex(0)->setCellValue('T6',  $totalRkap7);
				$excel->setActiveSheetIndex(0)->setCellValue('T7', $totalRkap8);
				$excel->setActiveSheetIndex(0)->setCellValue('T9', $totalRkap3);
				$excel->setActiveSheetIndex(0)->setCellValue('T10', $totalRkap4);
				$excel->setActiveSheetIndex(0)->setCellValue('T12', $totalRkap5);
				$excel->setActiveSheetIndex(0)->setCellValue('T13', $totalRkap6);
				$excel->setActiveSheetIndex(0)->setCellValue('T15', $totalRkap9);
				$excel->setActiveSheetIndex(0)->setCellValue('T16', $totalRkap10);

				$excel->setActiveSheetIndex(0)->setCellValue('T19', $totalRkap41);
				$excel->setActiveSheetIndex(0)->setCellValue('T20', $totalRkap42);
				$excel->setActiveSheetIndex(0)->setCellValue('T22', $totalRkap21);
				$excel->setActiveSheetIndex(0)->setCellValue('T23', $totalRkap22);
				$excel->setActiveSheetIndex(0)->setCellValue('T25', $totalRkap31);
				$excel->setActiveSheetIndex(0)->setCellValue('T26', $totalRkap32);
				$excel->setActiveSheetIndex(0)->setCellValue('T28', $totalRkap51);
				$excel->setActiveSheetIndex(0)->setCellValue('T29', $totalRkap52);

				$excel->setActiveSheetIndex(0)->setCellValue('T31', '=T6+T9+T12+T15+T19+T22+T25+T28');	
				$excel->setActiveSheetIndex(0)->setCellValue('T32', '=T7+T10+T13+T16+T20+T23+T26+T29');
			

			// // Set width kolom
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(5); // Set width kolom B
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
            $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
			$excel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
			$excel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
			
			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Laporan_Trafik_Arus_Barang");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Laporan_Trafik_Arus_Barang_DOM_'.$id.'_'.$end.'.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');
		


	}

	public function export_laporan_intr($id,$end, $terminal)
	{
		
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Laporan_Trafik_Arus_Barang')								
								   ->setTitle("Laporan_Trafik_Arus_Barang")
								   ->setSubject("Laporan_Trafik_Arus_Barang'")
								   ->setDescription("Laporan_Data_Trafik_Arus_Barang ")
								   ->setKeywords("Data_Trafik_Arus_Barang");
		
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
			$excel->setActiveSheetIndex(0)->setCellValue('C1', "Uraian");
			$excel->setActiveSheetIndex(0)->mergeCells('C1:D2')->setCellValue('B1', "");
			$excel->setActiveSheetIndex(0)->mergeCells('B2:D2')->setCellValue('B2', "");
			$excel->setActiveSheetIndex(0)->mergeCells('D1:D2')->setCellValue('D1', "");

			$excel->setActiveSheetIndex(0)->mergeCells('E1:E2')->setCellValue('E1', "Satuan");
			$excel->setActiveSheetIndex(0)->mergeCells('F1:Q1')->setCellValue('F1', "REALISASI PERIODE BERJALAN IKT");
			$excel->setActiveSheetIndex(0)->setCellValue('A3', "1");
			$excel->setActiveSheetIndex(0)->mergeCells('B3:C3')->setCellValue('B3', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('D3', "3");
			$excel->setActiveSheetIndex(0)->mergeCells('E3:P3')->setCellValue('E3', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('F2', "Januari"); 
			$excel->setActiveSheetIndex(0)->setCellValue('G2', "Februari"); 
			$excel->setActiveSheetIndex(0)->setCellValue('H2', "Maret"); 
			$excel->setActiveSheetIndex(0)->setCellValue('I2', "April"); 
			$excel->setActiveSheetIndex(0)->setCellValue('J2', "Mei"); 
			$excel->setActiveSheetIndex(0)->setCellValue('K2', "Juni"); 
			$excel->setActiveSheetIndex(0)->setCellValue('L2', "Juli"); 
			$excel->setActiveSheetIndex(0)->setCellValue('M2', "Agustus"); 
			$excel->setActiveSheetIndex(0)->setCellValue('N2', "September"); 
			$excel->setActiveSheetIndex(0)->setCellValue('O2', "Oktober"); 
			$excel->setActiveSheetIndex(0)->setCellValue('P2', "November"); 
			$excel->setActiveSheetIndex(0)->setCellValue('Q2', "Desember"); 
			$excel->setActiveSheetIndex(0)->mergeCells('R1:R2')->setCellValue('R1', "Realisasi Tahun 2022");
			$excel->setActiveSheetIndex(0)->mergeCells('B4:C4')->setCellValue('B4', "LUAR NEGERI");
			$excel->setActiveSheetIndex(0)->setCellValue('A6', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A9', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A12', "3");
	
			$excel->setActiveSheetIndex(0)->setCellValue('A19', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A22', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A25', "3");
	
			$excel->setActiveSheetIndex(0)->mergeCells('C5:D5')->setCellValue('C5', "IMPOR");
			$excel->setActiveSheetIndex(0)->setCellValue('D6', "KENDARAAN (CBU)");
			$excel->setActiveSheetIndex(0)->setCellValue('E6', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E7', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D9', "TRUK/BUS");
			$excel->setActiveSheetIndex(0)->setCellValue('E9', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E10', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D12', "ALAT BERAT");
			$excel->setActiveSheetIndex(0)->setCellValue('E12', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E13', "M3");	

			$excel->setActiveSheetIndex(0)->mergeCells('C18:D18')->setCellValue('C18', "EKSPOR");	
			$excel->setActiveSheetIndex(0)->setCellValue('D19', "KENDARAAN (CBU)");
			$excel->setActiveSheetIndex(0)->setCellValue('E19', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E20', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D22', "TRUK/BUS");
			$excel->setActiveSheetIndex(0)->setCellValue('E22', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E23', "M3");
			$excel->setActiveSheetIndex(0)->setCellValue('D25', "ALAT BERAT");
			$excel->setActiveSheetIndex(0)->setCellValue('E25', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E26', "M3");

			$excel->setActiveSheetIndex(0)->mergeCells('A31:D31')->setCellValue('A31', "JUMLAH BARANG LUAR NEGERI");
			$excel->setActiveSheetIndex(0)->mergeCells('A38:D38')->setCellValue('A38', "");
			$excel->setActiveSheetIndex(0)->mergeCells('A39:D39')->setCellValue('A39', "");	

			$excel->setActiveSheetIndex(0)->setCellValue('E31', "UNIT");
			$excel->setActiveSheetIndex(0)->setCellValue('E32', "M3");
			
			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(11); 
			$excel->getActiveSheet()->getStyle('A1:Q1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('A2:Q2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C21')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C18')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B41')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C42')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C55')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A67')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('R1:R67')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('A31:S31')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A32:S32')->getFont()->setBold(true);	

			$excel->getActiveSheet()->getStyle('R3:R32')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('Q1:Q2')->applyFromArray($style_col);		
			$excel->getActiveSheet()->getStyle('A1:A34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('B1:B34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('C1:C34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('D1:D34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('E1:E34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('F1:F34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('G1:G34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H1:H34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('I1:I34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('J1:J34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('K1:K34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('L1:L34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('M1:M34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('N1:N34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('O1:O34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('P1:P34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('Q1:Q34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('R1:R34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('S1:S34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('T1:T34')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('A3:T3')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A4:T4')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A5:T5')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A6:T6')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A7:T7')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A8:T8')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A9:T9')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A10:T10')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A11:T11')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A12:T12')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A13:T13')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A14:T14')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A15:T15')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A16:T16')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A17:T17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A18:T18')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A19:T19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A20:T20')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A21:T21')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A22:T22')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A23:T23')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A24:T24')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A25:T25')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A26:T26')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A27:T27')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A28:T28')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A29:T29')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A30:T30')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A31:T31')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A32:T32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A33:T33')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A34:T34')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('S1:S34')->getFont()->setBold(true);			
			$excel->getActiveSheet()->getStyle('T1:T34')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('F6:Z6')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('F7:Z7')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");			
			$excel->getActiveSheet()->getStyle('F8:Z8')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F9:Z9')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F10:Z10')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F11:Z11')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F12:Z12')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F13:Z13')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F14:Z14')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F15:Z15')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F16:Z16')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('F17:Z17')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('F18:Z18')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F19:Z19')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F20:Z20')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('F21:Z21')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F22:Z22')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F23:Z23')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F24:Z24')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F25:Z25')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F26:Z26')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F27:Z27')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F28:Z28')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F29:Z29')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F30:Z30')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F31:Z31')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F32:Z32')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F33:Z33')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F34:Z34')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F35:Z35')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F36:Z36')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F37:Z37')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('F38:Z38')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
		
		
			$this->load->model('tps_online/Model_laparusbarang');
				$con = $this->load->database('ikt_postgree', TRUE);
				$model = $this->Model_laparusbarang->get_data_laparusintr($id,$end);
				
				$cont = count($model['data']);
				$x = 0;
	
				while($x < $cont) {				
					$PERIODE = $model["data"][$x]['periode'];		
					$x++;
		
				$TAHUN = explode('-', $PERIODE);
				$YEAR = $TAHUN[0];
				$MM = $TAHUN[1];
				$OLD = $YEAR -1;
		
				if (!empty($YEAR)) {			
					$excel->setActiveSheetIndex(0)->mergeCells('R1:R2')->setCellValue('R1', "Realisasi Tahun $YEAR");	
					$excel->setActiveSheetIndex(0)->mergeCells('S1:S2')->setCellValue('S1', "Realisasi Tahun $OLD");			
					$excel->setActiveSheetIndex(0)->mergeCells('T1:T2')->setCellValue('T1', "RKAP Tahun $YEAR");		
				} 			
				
				 $dates = "'yyyy-mm'";
				 $ekspor  = "'EXPORT'";
				 $impor = "'IMPORT'";
				 $e = "'E'";
				 $m = "'M'";
				 $i = "'I'";
				 $generalcargo = "'GC'";	
				 $generalcar = "'%GC %'";
				 $alatberat = "'ALAT BERAT'";
				 $truckbus = "'TRUCK/BUS'"; 
				 $cbuu ="'%PASSENGER CAR%'";
				 $sepedamotor="'MOTOR'";
				 $mobil="'MOBIL'";
				 $unit = "'UNIT'";
				 $m3 = "'M3'";
				 $domestik = 'DOMESTIK';
				 $internasional = 'INTERNASIONAL';
				 $domestik = "'$domestik'";			
				 $internasional = "'$internasional'";
				 $sparepart = "'%SPAREPART%'";;	
				 $sparep = "'%SPT%'";;	
				 $cartos = "'CARTOS'";

				if ($PERIODE == ''.$YEAR.'-01'){				
	
					$bulan01 = 'Januari';
					$PERIODE = "'$PERIODE'";
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNT01 = $dataGeneralCargo[0]['UNIT'];
	
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNM01 = $dataGeneralCargo[0]['M3'];			
	
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AT01 = $dataAlatBerat[0]['UNIT'];
	
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AU01 = $dataAlatBerat[0]['M3'];					 
				
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
					 $KT01  = $dataKendaraan[0]['UNIT'];
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
					 $KU01  = $dataKendaraan[0]['M3'];	
		
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TT01  = $dataTruckBus[0]['UNIT'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TU01  = $dataTruckBus[0]['M3']; 
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
					';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $MU01  = $dataMotor[0]['UNIT'];
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
					 $dataMotor = $con->query($dataMotor)-> result_array();		
					 $MM01  = $dataMotor[0]['M3']; 
	
					//MUAT		
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
					$MAT01 = $dataMuatAlber[0]['UNIT'];
	
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
	
					$MAU01 = $dataMuatAlber[0]['M3'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCT01 = $dataMuatCargo[0]['UNIT'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCM01 = $dataMuatCargo[0]['M3'];
	
					 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	 
					 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
					 $MKT01 = $dataMuatCbu[0]['UNIT'];
	
					$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
					$MKU01 = $dataMuatCbu[0]['M3'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTT01 = $dataMuatTruck[0]['UNIT'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTU01 = $dataMuatTruck[0]['M3'];	
	
					 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMU01 = $dataMuatMotor[0]['UNIT'];
	
	
					$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
				   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMM01 = $dataMuatMotor[0]['M3'];	

			}
	
				if ($PERIODE == ''.$YEAR.'-02'){				
	
					$bulan02 = 'Februari';
					$PERIODE = "'$PERIODE'";
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNT02 = $dataGeneralCargo[0]['UNIT'];
	
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNM02 = $dataGeneralCargo[0]['M3'];		
	
	
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AT02 = $dataAlatBerat[0]['UNIT'];
	
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AU02 = $dataAlatBerat[0]['M3'];
			
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
					 $KT02  = $dataKendaraan[0]['UNIT'];
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
					 $KU02  = $dataKendaraan[0]['M3'];

					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TT02  = $dataTruckBus[0]['UNIT'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TU02  = $dataTruckBus[0]['M3']; 

					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
					';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $MU02  = $dataMotor[0]['UNIT'];
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
					 $dataMotor = $con->query($dataMotor)-> result_array();		
					 $MM02  = $dataMotor[0]['M3']; 

	
					//MUAT		
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
					$MAT02 = $dataMuatAlber[0]['UNIT'];
	
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
	
					$MAU02 = $dataMuatAlber[0]['M3'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCT02 = $dataMuatCargo[0]['UNIT'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCM02 = $dataMuatCargo[0]['M3'];
	
					 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	 
					 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
					 $MKT02 = $dataMuatCbu[0]['UNIT'];
	
					$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
					$MKU02 = $dataMuatCbu[0]['M3'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTT02 = $dataMuatTruck[0]['UNIT'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTU02 = $dataMuatTruck[0]['M3'];				
	
					 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMU02 = $dataMuatMotor[0]['UNIT'];
	
	
					$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
				   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMM02 = $dataMuatMotor[0]['M3'];	
	
				}
							
				if ($PERIODE == ''.$YEAR.'-03'){				
	
					$bulan03 = 'Maret';
					$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT03 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM03 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT03 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU03 = $dataAlatBerat[0]['M3'];
				
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT03  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU03  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT03  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU03  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $MU03  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM03  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT03 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU03 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT03 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM03 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT03 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU03 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT03 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU03 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU03 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM03 = $dataMuatMotor[0]['M3'];	

				}

							
				if ($PERIODE == ''.$YEAR.'-04'){				
	
					$bulan04 = 'April';
					$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT04 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM04 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT04 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU04 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT04  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU04  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT04  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU04  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $MU04  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM04  = $dataMotor[0]['M3']; 


				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT04 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU04 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT04 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'), SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM04 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT04 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU04 = $dataMuatCbu[0]['M3'];
				
				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT04 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU04 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU04 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM04 = $dataMuatMotor[0]['M3'];	

				}
								
						
				if ($PERIODE == ''.$YEAR.'-05'){				
	
					$bulan05 = 'Mei';
					$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT05 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM05 = $dataGeneralCargo[0]['M3'];
	
				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT05 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU05 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT05  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU05  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT05  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU05  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $MU05  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM05  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT05 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU05 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT05 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM05 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT05 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU05 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT05 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU05 = $dataMuatTruck[0]['M3'];	
	
				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU05 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM05 = $dataMuatMotor[0]['M3'];	

				}

						
				if ($PERIODE == ''.$YEAR.'-06'){				
	
					$bulan06 = 'Juni';
					$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT06 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM06 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT06 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU06 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT06  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU06  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT06  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU06  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $MU06  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM06  = $dataMotor[0]['M3']; 


				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT06 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU06 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT06 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM06 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT06 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU06 = $dataMuatCbu[0]['M3'];
				
				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT06 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU06 = $dataMuatTruck[0]['M3'];

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU06 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM06 = $dataMuatMotor[0]['M3'];	

				}

							
				if ($PERIODE == ''.$YEAR.'-07'){				
	
					$bulan07 = 'Juli';
					$PERIODE = "'$PERIODE'";
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNT07 = $dataGeneralCargo[0]['UNIT'];
	
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNM07 = $dataGeneralCargo[0]['M3'];			

					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AT07 = $dataAlatBerat[0]['UNIT'];
	
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AU07 = $dataAlatBerat[0]['M3'];					 
		
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
					 $KT07  = $dataKendaraan[0]['UNIT'];
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
					 $KU07  = $dataKendaraan[0]['M3'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TT07  = $dataTruckBus[0]['UNIT'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TU07  = $dataTruckBus[0]['M3'];	

					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
					';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $MU07  = $dataMotor[0]['UNIT'];
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
					 $dataMotor = $con->query($dataMotor)-> result_array();		
					 $MM07  = $dataMotor[0]['M3']; 
	
					//MUAT		
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
					$MAT07 = $dataMuatAlber[0]['UNIT'];
	
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
	
					$MAU07 = $dataMuatAlber[0]['M3'];

					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCT07 = $dataMuatCargo[0]['UNIT'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCM07 = $dataMuatCargo[0]['M3'];
	
					 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	 
					 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
					 $MKT07 = $dataMuatCbu[0]['UNIT'];
	
					$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
					$MKU07 = $dataMuatCbu[0]['M3'];	

					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTT07 = $dataMuatTruck[0]['UNIT'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTU07 = $dataMuatTruck[0]['M3'];	

					 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMU07 = $dataMuatMotor[0]['UNIT'];
	
	
					$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
				   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMM07 = $dataMuatMotor[0]['M3'];	

				}

							
				if ($PERIODE == ''.$YEAR.'-08'){				
	
					$bulan08 = 'Agustus';
					$PERIODE = "'$PERIODE'";
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNT08 = $dataGeneralCargo[0]['UNIT'];
	
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNM08 = $dataGeneralCargo[0]['M3'];	
				
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AT08 = $dataAlatBerat[0]['UNIT'];
	
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AU08 = $dataAlatBerat[0]['M3'];
				
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
					 $KT08  = $dataKendaraan[0]['UNIT'];
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				 	 where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
					 $KU08  = $dataKendaraan[0]['M3'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TT08  = $dataTruckBus[0]['UNIT'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TU08  = $dataTruckBus[0]['M3']; 
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
					';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $MU08  = $dataMotor[0]['UNIT'];
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
					 $dataMotor = $con->query($dataMotor)-> result_array();		
					 $MM08  = $dataMotor[0]['M3'];
	
	
					//MUAT		
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
					$MAT08 = $dataMuatAlber[0]['UNIT'];
	
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
	
					$MAU08 = $dataMuatAlber[0]['M3'];

					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCT08 = $dataMuatCargo[0]['UNIT'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCM08 = $dataMuatCargo[0]['M3'];

					 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					 where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	 
					 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
					 $MKT08 = $dataMuatCbu[0]['UNIT'];
	
					$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
					$MKU08 = $dataMuatCbu[0]['M3'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTT08 = $dataMuatTruck[0]['UNIT'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTU08 = $dataMuatTruck[0]['M3'];	
	
					 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMU08 = $dataMuatMotor[0]['UNIT'];
	
	
					$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
				   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMM08 = $dataMuatMotor[0]['M3'];	

				}

							
				if ($PERIODE == ''.$YEAR.'-09'){				
	
					$bulan09 = 'September';
					$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT09 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM09 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT09 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU09 = $dataAlatBerat[0]['M3'];
			
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT09  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU09  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT09  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU09  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $MU09  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM09  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT09 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU09 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT09 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM09 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT09 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU09 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT09 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU09 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU09 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM09 = $dataMuatMotor[0]['M3'];	

				}

							
				if ($PERIODE == ''.$YEAR.'-10'){				
	
					$bulan10 = 'Oktober';
					$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT10 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM10 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT10 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU10 = $dataAlatBerat[0]['M3'];
		
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT10  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU10  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT10  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU10  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $MU10  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM10  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT10 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU10 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT10 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM10 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT10 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU10 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT10 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU10 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU10 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM10 = $dataMuatMotor[0]['M3'];	

				}

							
				if ($PERIODE == ''.$YEAR.'-11'){				
	
					$bulan11 = 'November';
					$PERIODE = "'$PERIODE'";
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNT11 = $dataGeneralCargo[0]['UNIT'];
	
					$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')						
					';
					$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
					$GCNM11 = $dataGeneralCargo[0]['M3'];			

					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AT11 = $dataAlatBerat[0]['UNIT'];
	
					 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
					 $AU11 = $dataAlatBerat[0]['M3'];
			
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
					 $KT11  = $dataKendaraan[0]['UNIT'];
					 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')						
					 ';
					 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
					 $KU11  = $dataKendaraan[0]['M3'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TT11  = $dataTruckBus[0]['UNIT'];
	
					 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
								';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $TU11  = $dataTruckBus[0]['M3']; 
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')	
					';
					 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
					 $MU11  = $dataMotor[0]['UNIT'];
	
					 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
					 $dataMotor = $con->query($dataMotor)-> result_array();		
					 $MM11  = $dataMotor[0]['M3']; 
	
					//MUAT		
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
					$MAT11 = $dataMuatAlber[0]['UNIT'];
	
					$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
	
					$MAU11 = $dataMuatAlber[0]['M3'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCT11 = $dataMuatCargo[0]['UNIT'];
	
					$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
					$MGCM11 = $dataMuatCargo[0]['M3'];
		
					 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	 
					 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
					 $MKT11 = $dataMuatCbu[0]['UNIT'];
	
					$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
					$MKU11 = $dataMuatCbu[0]['M3'];	
					
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.'and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTT11 = $dataMuatTruck[0]['UNIT'];
	
					$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
					$MTU11 = $dataMuatTruck[0]['M3'];	
	
					 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					 group by  to_char("PERIODE",'.$dates.')';
	
					$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMU11 = $dataMuatMotor[0]['UNIT'];
	
	
					$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
					group by  to_char("PERIODE",'.$dates.')';
	
				   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
					$SMM11 = $dataMuatMotor[0]['M3'];	
		
				}

							
				if ($PERIODE == ''.$YEAR.'-12'){				
	
					$bulan12 = 'Desember';
					$PERIODE = "'$PERIODE'";
				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNT12 = $dataGeneralCargo[0]['UNIT'];

				$dataGeneralCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')						
				';
				$dataGeneralCargo = $con->query($dataGeneralCargo)-> result_array();
				$GCNM12 = $dataGeneralCargo[0]['M3'];			

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AT12 = $dataAlatBerat[0]['UNIT'];

				 $dataAlatBerat  = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataAlatBerat = $con->query($dataAlatBerat)-> result_array();
				 $AU12 = $dataAlatBerat[0]['M3'];
		
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();
				 $KT12  = $dataKendaraan[0]['UNIT'];
				 $dataKendaraan = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')						
				 ';
				 $dataKendaraan = $con->query($dataKendaraan)-> result_array();		
				 $KU12  = $dataKendaraan[0]['M3'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TT12  = $dataTruckBus[0]['UNIT'];

				 $dataTruckBus = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
							';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $TU12  = $dataTruckBus[0]['M3']; 

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')	
				';
				 $dataTruckBus = $con->query($dataTruckBus)-> result_array();
				 $MU12  = $dataMotor[0]['UNIT'];

				 $dataMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
					where "TERMINAL" = '.$internasional.'  and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.'and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
				 $dataMotor = $con->query($dataMotor)-> result_array();		
				 $MM12  = $dataMotor[0]['M3']; 

				//MUAT		
				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();
				$MAT12 = $dataMuatAlber[0]['UNIT'];

				$dataMuatAlber = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatAlber = $con->query($dataMuatAlber)-> result_array();

				$MAU12 = $dataMuatAlber[0]['M3'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				 $dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCT12 = $dataMuatCargo[0]['UNIT'];

				$dataMuatCargo = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcar.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCargo = $con->query($dataMuatCargo)-> result_array();
				$MGCM12 = $dataMuatCargo[0]['M3'];

				 $dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';
 
				 $dataMuatCbu = $con->query($dataMuatCbu)-> result_array();
				 $MKT12 = $dataMuatCbu[0]['UNIT'];

				$dataMuatCbu = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatCbu = $con->query($dataMuatCbu)-> result_array();			
				$MKU12 = $dataMuatCbu[0]['M3'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTT12 = $dataMuatTruck[0]['UNIT'];

				$dataMuatTruck = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

				$dataMuatTruck = $con->query($dataMuatTruck)-> result_array();
				$MTU12 = $dataMuatTruck[0]['M3'];	

				 $dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
				 where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				 group by  to_char("PERIODE",'.$dates.')';

				$dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMU12 = $dataMuatMotor[0]['UNIT'];


				$dataMuatMotor = 'select to_char("PERIODE",'.$dates.'),SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
				where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  "SATUAN" = '.$unit.' and to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				group by  to_char("PERIODE",'.$dates.')';

			   $dataMuatMotor = $con->query($dataMuatMotor)-> result_array();
				$SMM12 = $dataMuatMotor[0]['M3'];	

				}	
						$conr = $this->load->database('ikt_postgree', TRUE);   
					
						$terminal = 'INTERNASIONAL';
						$unit = 'UNIT';	
						$jenis1 = 'IMPOR';
						$jenis2 = 'EKSPOR';					
						$ton = 'TON';			
						$m3 = 'M3';	
	
						$terminal = "'$terminal'";
						$unit = "'$unit'";
						$YEAR = "'$YEAR'";
						$jenis1 = "'$jenis1'";
						$jenis2 = "'$jenis2'";
						$ton = "'$ton'";
						$m3 = "'$m3'";
					
						$komoditi1 = 'GENERAL CARGO';
						$komoditi1 = "'$komoditi1'";
						$datar1 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi1.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar1 = $conr->query($datar1)-> result_array();
	
						if ($datar1){
							$totalRkap1 = $datar1[0]['JANUARI'] + $datar1[0]['FEBRUARI'] + $datar1[0]['MARET'] + $datar1[0]['APRIL'] + $datar1[0]['MEI']+ $datar1[0]['JUNI']
						 + $datar1[0]['JULI']+ $datar1[0]['AGUSTUS']+ $datar1[0]['SEPTEMBER']+ $datar1[0]['OKTOBER']+ $datar1[0]['NOVEMBER']+ $datar1[0]['DESEMBER'];
						} else if(empty($datar1)) {	
							$totalRkap1 = 0;
						}
				
						$datar2 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi1.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar2 = $conr->query($datar2)-> result_array();
						if ($datar2){
							$totalRkap2 = $datar2[0]['JANUARI'] + $datar2[0]['FEBRUARI'] + $datar2[0]['MARET'] + $datar2[0]['APRIL'] + $datar2[0]['MEI']+ $datar2[0]['JUNI']
						 + $datar2[0]['JULI']+ $datar2[0]['AGUSTUS']+ $datar2[0]['SEPTEMBER']+ $datar2[0]['OKTOBER']+ $datar2[0]['NOVEMBER']+ $datar2[0]['DESEMBER'];
						} else if(empty($datar2)) {	
							$totalRkap2 = 0;
						}
	
						$komoditi2 = 'TRUCK/BUS';
						$komoditi2 = "'$komoditi2'";
						$datar3 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi2.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar3 = $conr->query($datar3)-> result_array();
						if ($datar3){
							$totalRkap3 = $datar3[0]['JANUARI'] + $datar3[0]['FEBRUARI'] + $datar3[0]['MARET'] + $datar3[0]['APRIL'] + $datar3[0]['MEI']+ $datar3[0]['JUNI']
						 + $datar3[0]['JULI']+ $datar3[0]['AGUSTUS']+ $datar3[0]['SEPTEMBER']+ $datar3[0]['OKTOBER']+ $datar3[0]['NOVEMBER']+ $datar3[0]['DESEMBER'];
						} else if(empty($datar3)) {	
							$totalRkap3 = 0;
						}
	
						$datar4 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi2.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar4 = $conr->query($datar4)-> result_array();
						if ($datar4){
							$totalRkap4 = $datar4[0]['JANUARI'] + $datar4[0]['FEBRUARI'] + $datar4[0]['MARET'] + $datar4[0]['APRIL'] + $datar4[0]['MEI']+ $datar4[0]['JUNI']
						 + $datar4[0]['JULI']+ $datar4[0]['AGUSTUS']+ $datar4[0]['SEPTEMBER']+ $datar4[0]['OKTOBER']+ $datar4[0]['NOVEMBER']+ $datar4[0]['DESEMBER'];
						} else if(empty($datar4)) {	
							$totalRkap4 = 0;
						}				
	
						$komoditi3 = 'ALAT BERAT';
						$komoditi3 = "'$komoditi3'";
						$datar5 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi3.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar5 = $conr->query($datar5)-> result_array();
						if ($datar5){
							$totalRkap5 = $datar5[0]['JANUARI'] + $datar5[0]['FEBRUARI'] + $datar5[0]['MARET'] + $datar5[0]['APRIL'] + $datar5[0]['MEI']+ $datar5[0]['JUNI']
						 + $datar5[0]['JULI']+ $datar5[0]['AGUSTUS']+ $datar5[0]['SEPTEMBER']+ $datar5[0]['OKTOBER']+ $datar5[0]['NOVEMBER']+ $datar5[0]['DESEMBER'];
						} else if(empty($datar5)) {	
							$totalRkap5 = 0;
						}
	;
						$datar6 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi3.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar6 = $conr->query($datar6)-> result_array();
						if ($datar6){
							$totalRkap6 = $datar6[0]['JANUARI'] + $datar6[0]['FEBRUARI'] + $datar6[0]['MARET'] + $datar6[0]['APRIL'] + $datar6[0]['MEI']+ $datar6[0]['JUNI']
						 + $datar6[0]['JULI']+ $datar6[0]['AGUSTUS']+ $datar6[0]['SEPTEMBER']+ $datar6[0]['OKTOBER']+ $datar6[0]['NOVEMBER']+ $datar6[0]['DESEMBER'];
						} else if(empty($datar6)) {	
							$totalRkap6 = 0;
						}
	
						$komoditi4 = 'MOBIL';
						$komoditi4 = "'$komoditi4'";
						$datar7 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi4.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar7 = $conr->query($datar7)-> result_array();
						if ($datar7){
							$totalRkap7 = $datar7[0]['JANUARI'] + $datar7[0]['FEBRUARI'] + $datar7[0]['MARET'] + $datar7[0]['APRIL'] + $datar7[0]['MEI']+ $datar7[0]['JUNI']
						 + $datar7[0]['JULI']+ $datar7[0]['AGUSTUS']+ $datar7[0]['SEPTEMBER']+ $datar7[0]['OKTOBER']+ $datar7[0]['NOVEMBER']+ $datar7[0]['DESEMBER'];
						} else if(empty($datar7)) {	
							$totalRkap7 = 0;
						}
	
						$datar8 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi4.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar8 = $conr->query($datar8)-> result_array();
						if ($datar8){
							$totalRkap8 = $datar8[0]['JANUARI'] + $datar8[0]['FEBRUARI'] + $datar8[0]['MARET'] + $datar8[0]['APRIL'] + $datar8[0]['MEI']+ $datar8[0]['JUNI']
						 + $datar8[0]['JULI']+ $datar8[0]['AGUSTUS']+ $datar8[0]['SEPTEMBER']+ $datar8[0]['OKTOBER']+ $datar8[0]['NOVEMBER']+ $datar8[0]['DESEMBER'];
						} else if(empty($datar8)) {	
							$totalRkap8 = 0;
						}	

						$komoditi5 = 'MOTOR';
						$komoditi5 = "'$komoditi5'";
						$datar9 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi5.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar9 = $conr->query($datar9)-> result_array();
						if ($datar9){
							$totalRkap9 = $datar9[0]['JANUARI'] + $datar9[0]['FEBRUARI'] + $datar9[0]['MARET'] + $datar9[0]['APRIL'] + $datar9[0]['MEI']+ $datar9[0]['JUNI']
						 + $datar9[0]['JULI']+ $datar9[0]['AGUSTUS']+ $datar9[0]['SEPTEMBER']+ $datar9[0]['OKTOBER']+ $datar9[0]['NOVEMBER']+ $datar9[0]['DESEMBER'];
						} else if(empty($datar9)) {	
							$totalRkap9 = 0;
						}
	
						$datar10 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis1.' 
						and "KOMODITI" = '.$komoditi5.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar10 = $conr->query($datar10)-> result_array();
						if ($datar10){
							$totalRkap10 = $datar10[0]['JANUARI'] + $datar10[0]['FEBRUARI'] + $datar10[0]['MARET'] + $datar10[0]['APRIL'] + $datar10[0]['MEI']+ $datar10[0]['JUNI']
						 + $datar10[0]['JULI']+ $datar10[0]['AGUSTUS']+ $datar10[0]['SEPTEMBER']+ $datar10[0]['OKTOBER']+ $datar10[0]['NOVEMBER']+ $datar10[0]['DESEMBER'];
						} else if(empty($datar10)) {	
							$totalRkap10 = 0;
						}
	

					//MUAT
						$komoditi11 = 'GENERAL CARGO';
						$komoditi11 = "'$komoditi11'";
						$datar11 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi11.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar11 = $conr->query($datar11)-> result_array();
						if ($datar11){
							$totalRkap11 = $datar11[0]['JANUARI'] + $datar11[0]['FEBRUARI'] + $datar11[0]['MARET'] + $datar11[0]['APRIL'] + $datar11[0]['MEI']+ $datar11[0]['JUNI']
						 + $datar11[0]['JULI']+ $datar11[0]['AGUSTUS']+ $datar11[0]['SEPTEMBER']+ $datar11[0]['OKTOBER']+ $datar11[0]['NOVEMBER']+ $datar11[0]['DESEMBER'];
						} else if(empty($datar11)) {	
							$totalRkap11 = 0;
						}
	
						$datar12 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi11.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar12 = $conr->query($datar12)-> result_array();
						if ($datar12){
							$totalRkap12 = $datar12[0]['JANUARI'] + $datar12[0]['FEBRUARI'] + $datar12[0]['MARET'] + $datar12[0]['APRIL'] + $datar12[0]['MEI']+ $datar12[0]['JUNI']
						 + $datar12[0]['JULI']+ $datar12[0]['AGUSTUS']+ $datar12[0]['SEPTEMBER']+ $datar12[0]['OKTOBER']+ $datar12[0]['NOVEMBER']+ $datar12[0]['DESEMBER'];
						} else if(empty($datar12)) {	
							$totalRkap12 = 0;
						}
	
						$komoditi21 = 'TRUCK/BUS';
						$komoditi21 = "'$komoditi21'";
						$datar21 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi2.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar21 = $conr->query($datar21)-> result_array();
						if ($datar21){
							$totalRkap21 = $datar21[0]['JANUARI'] + $datar21[0]['FEBRUARI'] + $datar21[0]['MARET'] + $datar21[0]['APRIL'] + $datar21[0]['MEI']+ $datar21[0]['JUNI']
						 + $datar21[0]['JULI']+ $datar21[0]['AGUSTUS']+ $datar21[0]['SEPTEMBER']+ $datar21[0]['OKTOBER']+ $datar21[0]['NOVEMBER']+ $datar21[0]['DESEMBER'];
						} else if(empty($datar21)) {	
							$totalRkap21 = 0;
						}
	
						$datar22 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER" 
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi21.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar22 = $conr->query($datar22)-> result_array();
						if ($datar22){
							$totalRkap22 = $datar22[0]['JANUARI'] + $datar22[0]['FEBRUARI'] + $datar22[0]['MARET'] + $datar22[0]['APRIL'] + $datar22[0]['MEI']+ $datar22[0]['JUNI']
						 + $datar22[0]['JULI']+ $datar22[0]['AGUSTUS']+ $datar22[0]['SEPTEMBER']+ $datar22[0]['OKTOBER']+ $datar22[0]['NOVEMBER']+ $datar22[0]['DESEMBER'];
						} else if(empty($datar22)) {	
							$totalRkap22 = 0;
						}
	
						$komoditi31 = 'ALAT BERAT';
						$komoditi31 = "'$komoditi31'";
						$datar31 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi31.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar31 = $conr->query($datar31)-> result_array();
						if ($datar31){
							$totalRkap31 = $datar31[0]['JANUARI'] + $datar31[0]['FEBRUARI'] + $datar31[0]['MARET'] + $datar31[0]['APRIL'] + $datar31[0]['MEI']+ $datar31[0]['JUNI']
						 + $datar31[0]['JULI']+ $datar31[0]['AGUSTUS']+ $datar31[0]['SEPTEMBER']+ $datar31[0]['OKTOBER']+ $datar31[0]['NOVEMBER']+ $datar31[0]['DESEMBER'];
						} else if(empty($datar31)) {	
							$totalRkap31 = 0;
						}
						$datar32 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi31.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar32 = $conr->query($datar32)-> result_array();
						if ($datar32){
							$totalRkap32 = $datar32[0]['JANUARI'] + $datar32[0]['FEBRUARI'] + $datar32[0]['MARET'] + $datar32[0]['APRIL'] + $datar32[0]['MEI']+ $datar32[0]['JUNI']
						 + $datar32[0]['JULI']+ $datar32[0]['AGUSTUS']+ $datar32[0]['SEPTEMBER']+ $datar32[0]['OKTOBER']+ $datar32[0]['NOVEMBER']+ $datar32[0]['DESEMBER'];
						} else if(empty($datar32)) {	
							$totalRkap32 = 0;
						}

						$komoditi41 = 'MOBIL';
						$komoditi41 = "'$komoditi41'";
						$datar41 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi41.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar41 = $conr->query($datar41)-> result_array();
						if ($datar41){
							$totalRkap41 = $datar41[0]['JANUARI'] + $datar41[0]['FEBRUARI'] + $datar41[0]['MARET'] + $datar41[0]['APRIL'] + $datar41[0]['MEI']+ $datar41[0]['JUNI']
						 + $datar41[0]['JULI']+ $datar41[0]['AGUSTUS']+ $datar41[0]['SEPTEMBER']+ $datar41[0]['OKTOBER']+ $datar41[0]['NOVEMBER']+ $datar41[0]['DESEMBER'];
						} else if(empty($datar41)) {	
							$totalRkap41 = 0;
						}
						$datar42 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi41.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar42 = $conr->query($datar42)-> result_array();
						if ($datar42){
							$totalRkap42 = $datar42[0]['JANUARI'] + $datar42[0]['FEBRUARI'] + $datar42[0]['MARET'] + $datar42[0]['APRIL'] + $datar42[0]['MEI']+ $datar42[0]['JUNI']
						 + $datar42[0]['JULI']+ $datar42[0]['AGUSTUS']+ $datar42[0]['SEPTEMBER']+ $datar42[0]['OKTOBER']+ $datar42[0]['NOVEMBER']+ $datar42[0]['DESEMBER'];
						} else if(empty($datar42)) {	
							$totalRkap42 = 0;
						}

						$komoditi51 = 'MOTOR';
						$komoditi51 = "'$komoditi51'";
						$datar51 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi51.' and "SATUAN"='.$unit.' and "TAHUN" = '.$YEAR.'';
						$datar51 = $conr->query($datar51)-> result_array();
						if ($datar51){
							$totalRkap51 = $datar51[0]['JANUARI'] + $datar51[0]['FEBRUARI'] + $datar51[0]['MARET'] + $datar51[0]['APRIL'] + $datar51[0]['MEI']+ $datar51[0]['JUNI']
						+ $datar51[0]['JULI']+ $datar51[0]['AGUSTUS']+ $datar51[0]['SEPTEMBER']+ $datar51[0]['OKTOBER']+ $datar51[0]['NOVEMBER']+ $datar51[0]['DESEMBER'];
						} else if(empty($datar51)) {	
							$totalRkap51 = 0;
						}

						$datar52 = 'SELECT "TERMINAL","JENIS","TAHUN","KOMODITI","SATUAN","JANUARI","FEBRUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER"
						FROM "DASHBOARD_RKAP_ARUS_BARANG" 
						where "TERMINAL" = '.$terminal.' and "JENIS" = '.$jenis2.' 
						and "KOMODITI" = '.$komoditi51.' and "SATUAN"='.$m3.' and "TAHUN" = '.$YEAR.'';
						$datar52 = $conr->query($datar52)-> result_array();
						if ($datar52){
							$totalRkap52 = $datar52[0]['JANUARI'] + $datar52[0]['FEBRUARI'] + $datar52[0]['MARET'] + $datar52[0]['APRIL'] + $datar52[0]['MEI']+ $datar52[0]['JUNI']
						+ $datar52[0]['JULI']+ $datar52[0]['AGUSTUS']+ $datar52[0]['SEPTEMBER']+ $datar52[0]['OKTOBER']+ $datar52[0]['NOVEMBER']+ $datar52[0]['DESEMBER'];
						} else if(empty($datar52)) {	
							$totalRkap52 = 0;
						}
					
					}	
						$x = "$OLD-01";	
						$y = "$OLD-12";	
						$old = "'$x'";
						$ago = "'$y'";
						$datest = "'yyyy-mm'";
						$OLD = "'$OLD'";
						
						$datat = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.'  and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';				
	
						$datat = $con->query($datat)-> result_array();
						$ATT = $datat[0]['UNIT'];

						$datat = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$alatberat.'  and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';				
	
						$datat = $con->query($datat)-> result_array();
						$AUT = $datat[0]['M3'];

						$datat1 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';				
	
						$datat1 = $con->query($datat1)-> result_array();	
						$TTT = $datat1[0]['UNIT'];

						$datat1 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$truckbus.'  and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';
						$datat1 = $con->query($datat1)-> result_array();					
						$TUT = $datat1[0]['M3'];	

						$datat2 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';		

						$datat2 = $con->query($datat2)-> result_array();
						$KTT = $datat2[0]['UNIT'];

						$datat2 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$mobil.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';

						$datat2 = $con->query($datat2)-> result_array();
						$KUT = $datat2[0]['M3'];				
						

						$dataCargot = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';

						$dataCargot = $con->query($dataCargot)-> result_array();
						$GCTT = $dataCargot[0]['UNIT'];

						$dataCargot = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$generalcargo.'  and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';

						$dataCargot = $con->query($dataCargot)-> result_array();
						$GCMT = $dataCargot[0]['M3'];

						$datat31= 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';

						$datat31 = $con->query($datat31)-> result_array();
						$SMUT = $datat31[0]['UNIT'];

						$datat31= 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$impor.' and "KOMODITI" = '.$sepedamotor.'  and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';

						$datat31 = $con->query($datat31)-> result_array();
						$SMMT = $datat31[0]['M3'];

						$datat4 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';
	
						$datat4 = $con->query($datat4)-> result_array();
						$MATT = $datat4[0]['UNIT'];

						$datat4 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$alatberat.'  and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';	
						
						$datat4 = $con->query($datat4)-> result_array();						
						$MAUT = $datat4[0]['M3'];	

						$datat5 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcargo.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';		
	
						$datat5 = $con->query($datat5)-> result_array();
						$MGCTT = $datat5[0]['UNIT'];

						$datat5 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$generalcargo.'  and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';		
	
						$datat5 = $con->query($datat5)-> result_array();
						$MGCMT = $datat5[0]['M3'];

						$datat6 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';			
	
						$datat6 = $con->query($datat6)-> result_array();
						$MKTT = $datat6[0]['UNIT'];

						$datat6 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$mobil.'  and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';	

						$datat6 = $con->query($datat6)-> result_array();
						$MKUT = $datat6[0]['M3'];

						$datat7 = 'select SUM("JUMLAH") "UNIT" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';	
	
						$datat7 = $con->query($datat7)-> result_array();
						$MTTT = $datat7[0]['UNIT'];

						$datat7 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$truckbus.'  and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';	
	
						$datat7 = $con->query($datat7)-> result_array();
						$MTUT = $datat7[0]['M3'];	

						$datat8 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';		
	
						$datat8 = $con->query($datat8)-> result_array();
						$SMIT = $datat8[0]['UNIT'];

						$datat8 = 'select SUM("QTY_M3") "M3" from "MART_RKAP_ARUS_BARANG"
						where "TERMINAL" = '.$internasional.' and "SOURCE" = '.$cartos.' and "JENIS" = '.$ekspor.' and "KOMODITI" = '.$sepedamotor.' and  to_char("PERIODE",'.$datest.') BETWEEN '.$old.' AND '.$ago.' 
						';		
	
						$datat8 = $con->query($datat8)-> result_array();
						$SMIU = $datat8[0]['M3'];	

		
			
				if (empty($bulan01)){
					$excel->setActiveSheetIndex(0)->setCellValue('F6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F9', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F12', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F13', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F15', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F16', ''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('F19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F29', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('F31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('F32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('F35', '');	

				} else if ($bulan01 == 'Januari'){
	
					$excel->setActiveSheetIndex(0)->setCellValue('F6',  $KT01 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F7',  $KU01 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F9',  $TT01 ?:'0');  
					$excel->setActiveSheetIndex(0)->setCellValue('F10', $TU01 ?:'0');  
					$excel->setActiveSheetIndex(0)->setCellValue('F12', $AT01 ?:'0');  
					$excel->setActiveSheetIndex(0)->setCellValue('F13', $AU01 ?:'0');  

			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('F19', $MKT01 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F20', $MKU01 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F22', $MTT01 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F23', $MTU01 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F25', $MAT01 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('F26', $MAU01 ?:'0'); 			 
			
					$excel->setActiveSheetIndex(0)->setCellValue('F31', '=F6+F9+F12+F15+F19+F22+F25+F28');	
					$excel->setActiveSheetIndex(0)->setCellValue('F32', '=F7+F10+F13+F16+F20+F23+F26+F29');
					

				}
				if (empty($bulan02)){
					$excel->setActiveSheetIndex(0)->setCellValue('G6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G9', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G12', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G13', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G15', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G16', ''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('G19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('G29', ''); 						
					$excel->setActiveSheetIndex(0)->setCellValue('G31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('G32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('G34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('G35', '');	
				} else if ($bulan02 == 'Februari'){
					$excel->setActiveSheetIndex(0)->setCellValue('G6', $KT02 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('G7', $KU02 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('G9', $TT02 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('G10', $TU02?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('G12', $AT02?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('G13', $AU02?:'0');

			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('G19', $MKT02 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('G20', $MKU02 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('G22', $MTT02 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('G23', $MTU02 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('G25', $MAT02 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('G26', $MAU02 ?:'0');			
					
					$excel->setActiveSheetIndex(0)->setCellValue('G31', '=G6+G9+G12+G15+G19+G22+G25+G28+G31');	
					$excel->setActiveSheetIndex(0)->setCellValue('G32', '=G7+G10+G13+G16+G20+G23+G26+G29+G32');
					
				

				}
				if (empty($bulan03)){
					$excel->setActiveSheetIndex(0)->setCellValue('H6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H9', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H10', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H12', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H13', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H15', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H16', ''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('H19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('H29', '');		
					$excel->setActiveSheetIndex(0)->setCellValue('H31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('H32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('H34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('H35', '');
				
				} else if ($bulan03 == 'Maret'){
					$excel->setActiveSheetIndex(0)->setCellValue('H6', $KT03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H7', $KU03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H9', $TT03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H10', $TU03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H12', $AT03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H13', $AU03 ?:'0');

			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('H19', $MKT03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H20', $MKU03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H22', $MTT03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H23', $MTU03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H25', $MAT03 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('H26', $MAU03 ?:'0');
					
					$excel->setActiveSheetIndex(0)->setCellValue('H31', '=H6+H9+H12+H15+H19+H22+H25+H28+H31');	
					$excel->setActiveSheetIndex(0)->setCellValue('H32', '=H7+H10+H13+H16+H20+H23+H26+H29+H32');
				
				}

				if (empty($bulan04)){
					$excel->setActiveSheetIndex(0)->setCellValue('I6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I9','');
					$excel->setActiveSheetIndex(0)->setCellValue('I10','');
					$excel->setActiveSheetIndex(0)->setCellValue('I12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('I19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I29', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('I31', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('I32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('I34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('I35', '');
				} else if ($bulan04 == 'April'){
					$excel->setActiveSheetIndex(0)->setCellValue('I6',  $KT04 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('I7',  $KU04 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('I9',  $TT04 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('I10', $TU04 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('I12', $AT04 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('I13', $AU04 ?:'0'); 
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('I19', $MKT04 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('I20', $MKU04 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('I22', $MTT04 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('I23', $MTU04 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('I25', $MAT04 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('I26', $MAU04 ?:'0');	
				
					$excel->setActiveSheetIndex(0)->setCellValue('I31', '=I6+I9+I12+I15+I19+I22+I25+I28');	
					$excel->setActiveSheetIndex(0)->setCellValue('I32', '=I7+I10+I13+I16+I20+I23+I26+I29');
				

				}
				if (empty($bulan05)){
					$excel->setActiveSheetIndex(0)->setCellValue('J6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J9','');
					$excel->setActiveSheetIndex(0)->setCellValue('J10','');
					$excel->setActiveSheetIndex(0)->setCellValue('J12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('J19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('J29', ''); 			
					$excel->setActiveSheetIndex(0)->setCellValue('J31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('J32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('J34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('J35', '');	

				} else if ($bulan05 == 'Mei'){
					$excel->setActiveSheetIndex(0)->setCellValue('J6',  $KT05 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('J7',  $KU05 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('J9',  $TT05 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('J10', $TU05 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('J12', $AT05 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('J13', $AU05 ?:'0'); 
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('J19', $MKT05 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('J20', $MKU05 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('J22', $MTT05 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('J23', $MTU05 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('J25', $MAT05 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('J26', $MAU05 ?:'0');			
							
					$excel->setActiveSheetIndex(0)->setCellValue('J31', '=J6+J9+J12+J15+J19+J22+J25+J28');	
					$excel->setActiveSheetIndex(0)->setCellValue('J32', '=J7+J10+J13+J16+J20+J23+J26+J29');
				

				}
			
				if (empty($bulan06)){
					$excel->setActiveSheetIndex(0)->setCellValue('K6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K9','');
					$excel->setActiveSheetIndex(0)->setCellValue('K10','');
					$excel->setActiveSheetIndex(0)->setCellValue('K12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('K19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K22', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K23', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K25', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K26', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K28', ''); 
					$excel->setActiveSheetIndex(0)->setCellValue('K29', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K31', '');				
					$excel->setActiveSheetIndex(0)->setCellValue('K32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('K35', '');

				} else if ($bulan06 == 'Juni'){
					$excel->setActiveSheetIndex(0)->setCellValue('K6',  $KT06 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('K7',  $KU06 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('K9',  $TT06 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('K10', $TU06 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('K12', $AT06 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('K13', $AU06 ?:'0');
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('K19', $MKT06 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('K20', $MKU06 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('K22', $MTT06 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('K23', $MTU06 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('K25', $MAT06 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('K26', $MAU06 ?:'0');
			
								
					$excel->setActiveSheetIndex(0)->setCellValue('K31', '=K6+K9+K12+K15+K19+K22+K25+K28');	
					$excel->setActiveSheetIndex(0)->setCellValue('K32', '=K7+K10+K13+K16+K20+K23+K26+K29');					
				
				} 
				if (empty($bulan07)){
					$excel->setActiveSheetIndex(0)->setCellValue('L6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L9','');
					$excel->setActiveSheetIndex(0)->setCellValue('L10','');
					$excel->setActiveSheetIndex(0)->setCellValue('L12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('L19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L22','');
					$excel->setActiveSheetIndex(0)->setCellValue('L23','');
					$excel->setActiveSheetIndex(0)->setCellValue('L25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('L29',''); 	
					$excel->setActiveSheetIndex(0)->setCellValue('L31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('L32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('L34', '');		
					$excel->setActiveSheetIndex(0)->setCellValue('L35', '');	
				} else if ($bulan07 == 'Juli'){
					$excel->setActiveSheetIndex(0)->setCellValue('L6',  $KT07 ?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L7',  $KU07 ?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L9',  $TT07 ?: '0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('L10', $TU07 ?: '0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('L12', $AT07 ?: '0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('L13', $AU07 ?: '0'); 
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('L19', $MKT07 ?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L20', $MKU07 ?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L22', $MTT07 ?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L23', $MTU07 ?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L25', $MAT07 ?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('L26', $MAU07 ?: '0');		
							
					$excel->setActiveSheetIndex(0)->setCellValue('L31', '=L6+L9+L12+L15+L19+L22+L25+L28');	
					$excel->setActiveSheetIndex(0)->setCellValue('L32', '=L7+L10+L13+L16+L20+L23+L26+L29');
			


				} 
		
				if (empty($bulan08)){
					$excel->setActiveSheetIndex(0)->setCellValue('M6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M9','');
					$excel->setActiveSheetIndex(0)->setCellValue('M10','' ?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('M12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('M19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M22',''?: '0');
					$excel->setActiveSheetIndex(0)->setCellValue('M23','');
					$excel->setActiveSheetIndex(0)->setCellValue('M25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('ML26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('M29','');		
					$excel->setActiveSheetIndex(0)->setCellValue('M31', '');		
					$excel->setActiveSheetIndex(0)->setCellValue('M32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('M34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('M35', '');	
				} else if ($bulan08 == 'Agustus'){
					$excel->setActiveSheetIndex(0)->setCellValue('M6',  $KT08 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('M7',  $KU08 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('M9',  $TT08 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('M10', $TU08 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('M12', $AT08 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('M13', $AU08 ?:'0'); 
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('M19', $MKT08 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('M20', $MKU08 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('M22', $MTT08 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('M23', $MTU08 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('M25', $MAT08 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('M26', $MAU08 ?:'0');			 
				
					$excel->setActiveSheetIndex(0)->setCellValue('M31', '=M6+M9+M12+M15+M19+M22+M25+M28');	
					$excel->setActiveSheetIndex(0)->setCellValue('M32', '=M7+M10+M13+M16+M20+M23+M26+M29');				

				}
				
				if (empty($bulan09)){
					$excel->setActiveSheetIndex(0)->setCellValue('N6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N9','');
					$excel->setActiveSheetIndex(0)->setCellValue('N10','');
					$excel->setActiveSheetIndex(0)->setCellValue('N12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('N19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N22','');
					$excel->setActiveSheetIndex(0)->setCellValue('N23','');
					$excel->setActiveSheetIndex(0)->setCellValue('N25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('N29','');
					$excel->setActiveSheetIndex(0)->setCellValue('N31', '');				
					$excel->setActiveSheetIndex(0)->setCellValue('N32', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('N33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('N34', '');	
					$excel->setActiveSheetIndex(0)->setCellValue('N35', '');
				} else if ($bulan09 == 'September'){
					$excel->setActiveSheetIndex(0)->setCellValue('N6', $KT09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N7', $KU09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N9', $TT09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N10', $TU09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N12', $AT09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N13', $AU09 ?:'0');
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('N19', $MKT09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N20', $MKU09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N22', $MTT09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N23', $MTU09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N25', $MAT09 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('N26', $MAU09 ?:'0');
	
					$excel->setActiveSheetIndex(0)->setCellValue('N31', '=N6+N9+N12+N15+N19+N22+N25+N28');	
					$excel->setActiveSheetIndex(0)->setCellValue('N32', '=N7+N10+N13+N16+N20+N23+N26+N29');
							

				}
				if (empty($bulan10)){
					$excel->setActiveSheetIndex(0)->setCellValue('O6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O9', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O10','');
					$excel->setActiveSheetIndex(0)->setCellValue('O12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('O19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O22','');
					$excel->setActiveSheetIndex(0)->setCellValue('O23','');
					$excel->setActiveSheetIndex(0)->setCellValue('O25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('O29','');
					$excel->setActiveSheetIndex(0)->setCellValue('O31', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('O35', '');

				}elseif ($bulan10 == 'Oktober'){
					//BONGKAR
					$excel->setActiveSheetIndex(0)->setCellValue('O6',  $KT10 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('O7',  $KU10 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('O9',  $TT10 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('O10', $TU10 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('O12', $AT10 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('O13', $AU10 ?:'0'); 
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('O19', $MKT10 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('O20', $MKU10 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('O22', $MTT10 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('O23', $MTU10 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('O25', $MAT10 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('O26', $MAU10 ?:'0');			 
			
					$excel->setActiveSheetIndex(0)->setCellValue('O31', '=O6+O9+O12+O15+O19+O22+O25+O28');	
					$excel->setActiveSheetIndex(0)->setCellValue('O32', '=O7+O10+O13+O16+O20+O23+O26+O29');
		

			 } 

				if (empty($bulan11)){
					$excel->setActiveSheetIndex(0)->setCellValue('P6', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P7', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P9','');
					$excel->setActiveSheetIndex(0)->setCellValue('P10','');
					$excel->setActiveSheetIndex(0)->setCellValue('P12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('P19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P22','');
					$excel->setActiveSheetIndex(0)->setCellValue('P23','');
					$excel->setActiveSheetIndex(0)->setCellValue('P25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('P29',''); 	
					$excel->setActiveSheetIndex(0)->setCellValue('P31', '');			
					$excel->setActiveSheetIndex(0)->setCellValue('P32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('P35', '');	
								
				} else if ($bulan11 == 'November'){
					$excel->setActiveSheetIndex(0)->setCellValue('P6',  $KT11 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('P7',  $KU11 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('P9',  $TT11 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('P10', $TU11 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('P12', $AT11 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('P13', $AU11 ?:'0'); 
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('P19', $MKT11 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('P20', $MKU11 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('P22', $MTT11 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('P23', $MTU11 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('P25', $MAT11 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('P26', $MAU11 ?:'0');			
						
					$excel->setActiveSheetIndex(0)->setCellValue('P31', '=P6+P9+P12+P15+P19+P22+P25+P28');	
					$excel->setActiveSheetIndex(0)->setCellValue('P32', '=P7+P10+P13+P16+P20+P23+P26+P29');
			
				}				
			
				if (empty($bulan12)){
					$excel->setActiveSheetIndex(0)->setCellValue('Q6','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q7','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q9','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q10','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q12',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q13',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q15',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q16',''); 
					// MUAT
					$excel->setActiveSheetIndex(0)->setCellValue('Q19', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q20', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q22','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q23','');
					$excel->setActiveSheetIndex(0)->setCellValue('Q25',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q26',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q28',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q29',''); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q31', '');		
					$excel->setActiveSheetIndex(0)->setCellValue('Q32', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q33', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q34', '');
					$excel->setActiveSheetIndex(0)->setCellValue('Q35', '');
				} else if ($bulan12 == 'Desember'){
					$excel->setActiveSheetIndex(0)->setCellValue('Q6',  $KT12 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('Q7',  $KU12 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('Q9',  $TT12 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q10', $TU12 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q12', $AT12 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q13', $AU12 ?:'0'); 
			
					// MUAT				
					$excel->setActiveSheetIndex(0)->setCellValue('Q19', $MKT12 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('Q20', $MKU12 ?:'0');
					$excel->setActiveSheetIndex(0)->setCellValue('Q22', $MTT12 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q23', $MTU12 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q25', $MAT12 ?:'0'); 
					$excel->setActiveSheetIndex(0)->setCellValue('Q26', $MAU12 ?:'0');			 
			
				
					$excel->setActiveSheetIndex(0)->setCellValue('Q31', '=Q6+Q9+Q12+Q15+Q19+Q22+Q25+Q28');	
					$excel->setActiveSheetIndex(0)->setCellValue('Q32', '=Q7+Q10+Q13+Q16+Q20+Q23+Q26+Q29');
								
				}

				$excel->setActiveSheetIndex(0)->setCellValue('R6', '=SUM(F6:Q6)');
				$excel->setActiveSheetIndex(0)->setCellValue('R7', '=SUM(F7:Q7)');
				$excel->setActiveSheetIndex(0)->setCellValue('R9', '=SUM(F9:Q9)');
				$excel->setActiveSheetIndex(0)->setCellValue('R10', '=SUM(F10:Q10)');
				$excel->setActiveSheetIndex(0)->setCellValue('R12', '=SUM(F12:Q12)');
				$excel->setActiveSheetIndex(0)->setCellValue('R13', '=SUM(F13:Q13)');		

				$excel->setActiveSheetIndex(0)->setCellValue('R19', '=SUM(F19:Q19)');
				$excel->setActiveSheetIndex(0)->setCellValue('R20', '=SUM(F20:Q20)');
				$excel->setActiveSheetIndex(0)->setCellValue('R22', '=SUM(F22:Q22)');
				$excel->setActiveSheetIndex(0)->setCellValue('R23', '=SUM(F23:Q23)');
				$excel->setActiveSheetIndex(0)->setCellValue('R25', '=SUM(F25:Q25)');
				$excel->setActiveSheetIndex(0)->setCellValue('R26', '=SUM(F26:Q26)');			
			
				$excel->setActiveSheetIndex(0)->setCellValue('R31', '=SUM(F31:Q31)');
				$excel->setActiveSheetIndex(0)->setCellValue('R32', '=SUM(F32:Q32)');

				$excel->setActiveSheetIndex(0)->setCellValue('S6',  $KTT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S7', $KUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S9', $TTT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S10', $TUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S12', $ATT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S13', $AUT?: '0' );		

				$excel->setActiveSheetIndex(0)->setCellValue('S19', $MKTT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S20', $MKUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S22', $MTTT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S23', $MTUT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S25', $MATT?: '0' );
				$excel->setActiveSheetIndex(0)->setCellValue('S26', $MAUT?: '0' );

				$excel->setActiveSheetIndex(0)->setCellValue('S31', '=S6+S9+S12+S15+S19+S22+S25+S28');	
				$excel->setActiveSheetIndex(0)->setCellValue('S32', '=S7+S10+S13+S16+S20+S23+S26+S29');
		
				$excel->setActiveSheetIndex(0)->setCellValue('T6',  $totalRkap7);
				$excel->setActiveSheetIndex(0)->setCellValue('T7', $totalRkap8);
				$excel->setActiveSheetIndex(0)->setCellValue('T9', $totalRkap3);
				$excel->setActiveSheetIndex(0)->setCellValue('T10', $totalRkap4);
				$excel->setActiveSheetIndex(0)->setCellValue('T12', $totalRkap5);
				$excel->setActiveSheetIndex(0)->setCellValue('T13', $totalRkap6);
		
				$excel->setActiveSheetIndex(0)->setCellValue('T19', $totalRkap41);
				$excel->setActiveSheetIndex(0)->setCellValue('T20', $totalRkap42);
				$excel->setActiveSheetIndex(0)->setCellValue('T22', $totalRkap21);
				$excel->setActiveSheetIndex(0)->setCellValue('T23', $totalRkap22);
				$excel->setActiveSheetIndex(0)->setCellValue('T25', $totalRkap31);
				$excel->setActiveSheetIndex(0)->setCellValue('T26', $totalRkap32);

				$excel->setActiveSheetIndex(0)->setCellValue('T31', '=T6+T9+T12+T15+T19+T22+T25+T28');	
				$excel->setActiveSheetIndex(0)->setCellValue('T32', '=T7+T10+T13+T16+T20+T23+T26+T29');
	
	
			// // Set width kolom
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(5); // Set width kolom B
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
            $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
            $excel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
			$excel->getActiveSheet()->getColumnDimension('S')->setWidth(25);
			$excel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
			
			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Laporan_Trafik_Arus_Barang");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Laporan_Trafik_Arus_Barang_INTR_'.$id.'_'.$end.'.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');
		
	

	}
}