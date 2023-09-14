<?php
/** Laporan Produksi dan Pendapatan per Pusat Layanan
  *	Modul untuk mengunduh laporan produksi dan pendapatan per pusat layanan berdasarkan tahun dan terminal
  *
  */

class lap_pendapatan extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/Model_lap_pendapatan'
								
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
        $mod = model('tps_online/Model_lap_pendapatan');

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

		$this->load->view('backend/pages/tps_online/lap_pendapatan/listview',$data);
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
	public function export_pendapatan_dom($id,$end,$type)
	{
		
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Laporan_Pendapatan')							
								   ->setTitle("Laporan_Pendapatan")
								   ->setSubject("Laporan_Pendapatan")
								   ->setDescription("Laporan_Pendapatan")
								   ->setKeywords("Data_Pendapatan");
		
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
			$excel->setActiveSheetIndex(0)->mergeCells('B1:B2')->setCellValue('B2', "");
		
			$excel->setActiveSheetIndex(0)->mergeCells('C1:C2')->setCellValue('C2', "");
			$excel->setActiveSheetIndex(0)->setCellValue('C1', "Layanan");

			$excel->setActiveSheetIndex(0)->mergeCells('D1:D2')->setCellValue('D1', "Satuan");
	
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
			$excel->setActiveSheetIndex(0)->mergeCells('Q1:Q2')->setCellValue('Q2', "REALISASI 2022");
			$excel->setActiveSheetIndex(0)->mergeCells('R1:S1')->setCellValue('R1', "");
			$excel->setActiveSheetIndex(0)->mergeCells('T1:U1')->setCellValue('T1', "");
			$excel->setActiveSheetIndex(0)->setCellValue('R2',"Tarif I");
			$excel->setActiveSheetIndex(0)->setCellValue('S2',"Tarif II");
			$excel->setActiveSheetIndex(0)->setCellValue('T2',"Pendapatan I");
			$excel->setActiveSheetIndex(0)->setCellValue('U2',"Pendapatan II");
			$excel->setActiveSheetIndex(0)->setCellValue('V2',"Pendapatan s.d Desember");

			$excel->setActiveSheetIndex(0)->mergeCells('B3:C3')->setCellValue('B3', "PELAYANAN DOMESTIK");
			$excel->setActiveSheetIndex(0)->setCellValue('A4', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A8', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A24', "3");
			$excel->setActiveSheetIndex(0)->setCellValue('A41', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('A44', "5");
			$excel->setActiveSheetIndex(0)->setCellValue('A50', "6");
		
			$excel->setActiveSheetIndex(0)->setCellValue('C4', "CBU TL");
			$excel->setActiveSheetIndex(0)->setCellValue('C5', "CBU NTL");

			$excel->setActiveSheetIndex(0)->mergeCells('B6:C6')->setCellValue('B6', "TOTAL CBU");	
			$excel->setActiveSheetIndex(0)->setCellValue('C8', "TRUCK / BUS TL");	
			$excel->setActiveSheetIndex(0)->setCellValue('C9', "< 28");
			$excel->setActiveSheetIndex(0)->setCellValue('C10', "> 28 - 33");
			$excel->setActiveSheetIndex(0)->setCellValue('C11', "> 33 - 40");
		    $excel->setActiveSheetIndex(0)->setCellValue('C12', "> 40 - 50");
			$excel->setActiveSheetIndex(0)->setCellValue('C13', "> 50");
			$excel->setActiveSheetIndex(0)->mergeCells('B14:C14')->setCellValue('B14', "TOTAL TRUCK/BUS");
		
			$excel->setActiveSheetIndex(0)->setCellValue('C16', "TRUCK / BUS NTL");
			$excel->setActiveSheetIndex(0)->setCellValue('C17', "< 28");
			$excel->setActiveSheetIndex(0)->setCellValue('C18', "> 28 - 33");
			$excel->setActiveSheetIndex(0)->setCellValue('C19', "> 33 - 40");
		    $excel->setActiveSheetIndex(0)->setCellValue('C20', "> 40 - 50");
			$excel->setActiveSheetIndex(0)->setCellValue('C21', "> 50");

			$excel->setActiveSheetIndex(0)->mergeCells('B22:C22')->setCellValue('B22', "TOTAL TRUCK/BUS");

			$excel->setActiveSheetIndex(0)->setCellValue('C24', "ALAT BERAT TL");
			$excel->setActiveSheetIndex(0)->setCellValue('C32', "ALAT BERAT NTL");

			$excel->setActiveSheetIndex(0)->mergeCells('B31:C31')->setCellValue('B31', "TOTAL ALAT BERAT TL");
			$excel->setActiveSheetIndex(0)->mergeCells('B39:C39')->setCellValue('B39', "TOTAL ALAT BERAT NTL");
			$excel->setActiveSheetIndex(0)->setCellValue('C25', "< 28");
			$excel->setActiveSheetIndex(0)->setCellValue('C26', "> 28 - 33");
			$excel->setActiveSheetIndex(0)->setCellValue('C27', "> 33 - 40");
		    $excel->setActiveSheetIndex(0)->setCellValue('C28', "> 40 - 50");
			$excel->setActiveSheetIndex(0)->setCellValue('C29', "> 50 - 120");
			$excel->setActiveSheetIndex(0)->setCellValue('C30', "> 120");

			$excel->setActiveSheetIndex(0)->setCellValue('C33', "< 28");
			$excel->setActiveSheetIndex(0)->setCellValue('C34', "> 28 - 33");
			$excel->setActiveSheetIndex(0)->setCellValue('C35', "> 33 - 40");
		    $excel->setActiveSheetIndex(0)->setCellValue('C36', "> 40 - 50");
			$excel->setActiveSheetIndex(0)->setCellValue('C37', "> 50 - 120");
			$excel->setActiveSheetIndex(0)->setCellValue('C38', "> 120");

			$excel->setActiveSheetIndex(0)->mergeCells('B41:C41')->setCellValue('B41', "SEPEDA MOTOR TL");
			$excel->setActiveSheetIndex(0)->mergeCells('B42:C42')->setCellValue('B42', "SEPEDA MOTOR NTL");
			$excel->setActiveSheetIndex(0)->mergeCells('B50:C50')->setCellValue('B50', "JASA PENUMPUKAN");

			$excel->setActiveSheetIndex(0)->mergeCells('B44:C44')->setCellValue('B44', "General Cargo");			
			$excel->setActiveSheetIndex(0)->mergeCells('B45:C45')->setCellValue('B45', "Normal");	
			$excel->setActiveSheetIndex(0)->mergeCells('B46:C46')->setCellValue('B46', "Mengganggu");
			$excel->setActiveSheetIndex(0)->mergeCells('B47:C47')->setCellValue('B47', "Berbahaya");
			$excel->setActiveSheetIndex(0)->mergeCells('B48:C48')->setCellValue('B48', "Berbahaya Non Label");

			$excel->setActiveSheetIndex(0)->mergeCells('B51:C51')->setCellValue('B51', "CBU");
			$excel->setActiveSheetIndex(0)->mergeCells('B52:C52')->setCellValue('B52', "Masa II (Hari Ke-6 s/d 7)");
			$excel->setActiveSheetIndex(0)->mergeCells('B53:C53')->setCellValue('B53', "Masa III (Hari Ke-8 dst)");
			
			$excel->setActiveSheetIndex(0)->mergeCells('B54:C54')->setCellValue('B54', "ALAT BERAT");
			$excel->setActiveSheetIndex(0)->mergeCells('B55:C55')->setCellValue('B55', "Masa II (Hari Ke-6 s/d 7)");
			$excel->setActiveSheetIndex(0)->mergeCells('B56:C56')->setCellValue('B56', "Masa III (Hari Ke-8 dst)");
			
			$excel->setActiveSheetIndex(0)->mergeCells('B57:C57')->setCellValue('B57', "TRUCK/BUS");
			$excel->setActiveSheetIndex(0)->mergeCells('B58:C58')->setCellValue('B58', "Masa II (Hari Ke-6 s/d 7)");
			$excel->setActiveSheetIndex(0)->mergeCells('B59:C59')->setCellValue('B59', "Masa III (Hari Ke-8 dst)");
			
			$excel->setActiveSheetIndex(0)->mergeCells('B60:C60')->setCellValue('B60', "GENERAL CARGO");
			$excel->setActiveSheetIndex(0)->mergeCells('B61:C61')->setCellValue('B61', "Masa II (Hari Ke-6 s/d 7)");
			$excel->setActiveSheetIndex(0)->mergeCells('B62:C62')->setCellValue('B62', "Masa III (Hari Ke-8 dst)");
			
			$excel->setActiveSheetIndex(0)->mergeCells('B63:C63')->setCellValue('B63', "SEPEDA MOTOR");
			$excel->setActiveSheetIndex(0)->mergeCells('B64:C64')->setCellValue('B64', "Masa II (Hari Ke-6 s/d 7)");
			$excel->setActiveSheetIndex(0)->mergeCells('B65:C65')->setCellValue('B65', "Masa III (Hari Ke-8 dst)");
			
			$excel->setActiveSheetIndex(0)->setCellValue('D4', 'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D5', 'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D6',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D9',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D10',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D11',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D12',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D13',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D14',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D17',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D18',  'UNIT');

			$excel->setActiveSheetIndex(0)->setCellValue('D19',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D20',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D21',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D22',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D25',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D26',  'UNIT');
	
			$excel->setActiveSheetIndex(0)->setCellValue('D27', 'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D28',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D29',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D30',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D31', 'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D32', 'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D33',  'UNIT');
	
			$excel->setActiveSheetIndex(0)->setCellValue('D35',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D36',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D37',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D38',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D39',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D45',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D46',  'UNIT');

			$excel->setActiveSheetIndex(0)->setCellValue('D41',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D42',  'UNIT');
	
			$excel->setActiveSheetIndex(0)->setCellValue('D47',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D48',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D52',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D53',  'UNIT');			 
	
			$excel->setActiveSheetIndex(0)->setCellValue('D55',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D56',  'UNIT');

			$excel->setActiveSheetIndex(0)->setCellValue('D58',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D59',  'UNIT');
	
			$excel->setActiveSheetIndex(0)->setCellValue('D61',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D62',  'UNIT');	
				
			$excel->setActiveSheetIndex(0)->setCellValue('D64',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D65',  'UNIT');
		
			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(11); 
			$excel->getActiveSheet()->getStyle('A1:V1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('A2:V2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('C4:C8')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B14:C14')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C16')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B22:C22')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B31:C31')->getFont()->setBold(true);	
			$excel->getActiveSheet()->getStyle('C32')->getFont()->setBold(true);	
			$excel->getActiveSheet()->getStyle('B39:C39')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B60:C60')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('Q1:Q2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('Q12')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A1:A65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('B1:B65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('C1:C65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('D1:D65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('E1:E65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('F1:F65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('G1:G65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H1:H65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('I1:I65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('J1:J65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('K1:K65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('L1:L65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('M1:M65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('N1:N65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('O1:O65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('P1:P65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('Q1:Q65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('R1:R65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('S1:S65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('T1:T65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('U1:U65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('V1:V65')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('A3:V3')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A4:V4')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A5:V5')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A6:V6')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A7:V7')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A8:V8')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A9:V9')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A10:V10')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A11:V11')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A12:V12')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A13:V13')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A14:V14')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A15:V15')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A16:V16')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A17:V17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A18:V18')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A19:V19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A20:V20')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A21:V21')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A22:V22')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A23:V23')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A24:V24')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A25:V25')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A26:V26')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A27:V27')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A28:V28')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A29:V29')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A30:V30')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A31:V31')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A32:V32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A33:V33')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A34:V34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A35:V35')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A36:V36')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A37:V37')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A38:V38')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A39:V39')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A40:V40')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A41:V41')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A42:V42')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A43:V43')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A44:V44')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A45:V45')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A46:V46')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A47:V47')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A48:V48')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A49:V49')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A50:V50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A51:V51')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A52:V52')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A53:V53')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A54:V54')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A55:V55')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A56:V56')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A57:V57')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A58:V58')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A59:V59')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A60:V60')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A61:V61')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A62:V62')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A63:V63')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A64:V64')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A65:V65')->applyFromArray($style_row);
		

			$excel->getActiveSheet()->getStyle('Q1:Q70')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B3:C3')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B8:C8')->getFont()->setBold(true);	
			$excel->getActiveSheet()->getStyle('B6:C6')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B24:C24')->getFont()->setBold(true);			
			$excel->getActiveSheet()->getStyle('B33:B34')->getFont()->setBold(true);		
			$excel->getActiveSheet()->getStyle('B41:B44')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('B51:C51')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B54:C54')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B50:B50')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B57:C57')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B63:B63')->getFont()->setBold(true);		
			$excel->getActiveSheet()->getStyle('B66:B66')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('E4:V4')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E5:V5')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E6:V6')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E7:V7')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");			
			$excel->getActiveSheet()->getStyle('E8:V8')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E9:V9')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E10:V10')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E11:V11')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E12:V12')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E13:V13')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E14:V14')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E15:V15')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E16:V16')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E17:V17')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E18:V18')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E19:V19')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E20:V20')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E21:V21')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E22:V22')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E23:V23')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E24:V24')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E25:V25')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E26:V26')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E27:V27')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E28:V28')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E29:V29')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E30:V30')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E31:V31')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E32:V32')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E33:V33')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E34:V34')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E35:V35')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E36:V36')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E37:V37')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E38:V38')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E39:V39')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E40:V40')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E41:V41')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E42:V42')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E43:V43')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E44:V44')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E45:V45')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E46:V46')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E47:V47')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E48:V48')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E49:V49')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E50:V50')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E51:V51')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E52:V52')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E53:V53')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E54:V54')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E55:V55')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E56:V56')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E57:V57')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E58:V58')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E59:V59')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E60:V60')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E61:V61')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E62:V62')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E63:V63')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E64:V64')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E65:V65')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E66:V66')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E67:V67')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E68:V68')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E69:V69')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E70:V70')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
				
			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('tps_online/Model_lap_pendapatan');
			$con = $this->load->database('ikt_postgree', TRUE);
	
			$model = $this->Model_lap_pendapatan->get_data_pendapatan($id,$end);

			$cont = count($model['data']);
			$x = 0;
			while($x < $cont) {
				$PERIODE = $model["data"][$x]['periode'];		
				 $x++;			
		
			$TAHUN = explode('-', $PERIODE);
			$YEAR = $TAHUN[0];
			$OLD = $YEAR - 1; 
			$tipe = str_replace('%20',' ',$type);

			if (!empty($YEAR)) {
				$excel->setActiveSheetIndex(0)->mergeCells('Q1:Q2')->setCellValue('Q1', "Realisasi Tahun $YEAR");
			}
			if ($tipe == 'PER SEMESTER' || $tipe == 'PER TRIWULAN') {
				$excel->setActiveSheetIndex(0)->mergeCells('R1:S1')->setCellValue('R1', $tipe);
				$excel->setActiveSheetIndex(0)->mergeCells('T1:U1')->setCellValue('T1', $tipe);
			
			}
			if ($tipe == 'PER TAHUN'){
				$excel->setActiveSheetIndex(0)->mergeCells('R1:T1')->setCellValue('R1', $tipe);
				$excel->setActiveSheetIndex(0)->setCellValue('R2',"Tarif");
				$excel->setActiveSheetIndex(0)->setCellValue('S2',"Pendapatan");
				$excel->setActiveSheetIndex(0)->setCellValue('T2',"Pendapatan s.d Desember");
				$excel->setActiveSheetIndex(0)->setCellValue('U2',"");
				$excel->setActiveSheetIndex(0)->setCellValue('V2',"");
			}
			$dates = "'yyyy-mm'";
			$luxury = "'%PASSENGER CAR LUXURY%'";
			$kebersihan = "'%KEBERSIHAN CBU%'";
			$passenger = "'PASSENGER CAR'";
			$jasaDermaga = "'JASA DERMAGA'";
			$cargoHandling = "'CARGO HANDLING'";
			$steveDoring = "'STEVEDORING'";

			$masa2 = "'MASA II'";
			$masa3 = "'MASA III'";

			$oppt1 ='OPP/OPT';
			$optA = "'0 s/d 28 TON/M3'";
			$optB = "'28 s/d 33 TON/M3'";
			$optC = "'33 s/d 40 TON/M3'";
			$optD = "'40 s/d 50 TON/M3'";
			$optE = "'50 s/d 80 TON/M3'";
			$optF = "'80 s/d 100 TON/M3'";
			$optG = "'diatas 100 TON/M3'";

			$cbu = "'CBU'";
			$cbuLuxury = "'CBU LUXURY'";	
			$alberTruck = "'ALAT BERAT & TRUCK'";		
			$generalCargo = "'GENERAL CARGO'";

			$cbu_tl = "'%CBU TL%'";
			$cbu_nontl = "'%CBU NON TL%'";
			$cbu_luxurytl = "'CBU LUXURY TL'";
			$cbu_luxuryntl ="'CBU LUXURY NON TL'";
			$truckBustl = "'TRUCK/BUS TL'";
			$truckBusntl = "'TRUCK/BUS NON TL'";
			$alatBerattl = "'ALAT BERAT TL'";
			$alatBeratntl = "'ALAT BERAT NON TL'";
			$motorTl = "'SEPEDA MOTOR TL'";
			$motorNtl = "'SEPEDA MOTOR NON TL'";
			$generalCargo = "'GENERAL CARGO'";

			$truckBustlA = "'28 TON/M3 (TL)'";
			$truckBustlB = "'28-33 TON/M3 (TL)'";
			$truckBustlC = "'33-40 TON/M3 (TL)'";
			$truckBustlD = "'40-50 TON/M3 (TL)'";
			$truckBustlE = "'50 TON/M3 (TL)'";

			$truckBusntlA = "'28 TON/M3 (NON TL)'";
			$truckBusntlB = "'28-33 TON/M3 (NON TL)'";
			$truckBusntlC = "'33-40 TON/M3 (NON TL)'";
			$truckBusntlD = "'40-50 TON/M3 (NON TL)'";
			$truckBusntlE = "'50 TON/M3 (NON TL)'";

			$alberA = "'28 TON/M3 (TL)'";
			$alberB = "'28-33 TON/M3 (TL)'";
			$alberC = "'33-40 TON/M3 (TL)'";
			$alberD = "'40-50 TON/M3 (TL)'";
			$alberE = "'50-120 TON/M3 (TL)'";
			$alberF = "'120 TON/M3 (TL)'";

			$albernA = "'28 TON/M3 (NON TL)'";
			$albernB = "'28-33 TON/M3 (NON TL)'";
			$albernC = "'33-40 TON/M3 (NON TL)'";
			$albernD = "'40-50 TON/M3 (NON TL)'";
			$albernE = "'50-120 TON/M3 (NON TL)'";
			$albernF = "'120 TON/M3 (NON TL)'";
			$albernDD = "'50 TON/M3 (NON TL)'";
			$motorTl = "'SEPEDA MOTOR TL'";
			$motorNtl = "'SEPEDA MOTOR NON TL'";

			$cargoA = "'NORMAL'";
			$cargoB = "'MENGGANGGU'";
			$cargoC = "'BERBAHAYA'";
			$cargoD = "'BERBAHAYA NON LABEL'";

			$masaII = "'MASA II (HARI KE-6 S/D 7)'";
			$masaIII = "'MASA III (HARI KE-8 DST)'";

			$terminalDom = "'DOMESTIK'";
			$domestik = "'PELAYANAN DOMESTIK'";			

			$jasaCbu = "'JASA PENUMPUKAN CBU'";
			$jasaLuxury = "'JASA PENUMPUKAN CBU LUXURY'";			
			$jasaAlber = "'JASA PENUMPUKAN ALAT BERAT'";			
			$jasaTruckbus = "'JASA PENUMPUKAN TRUCK/BUS'";			
			$jasaCargo = "'JASA PENUMPUKAN GENERAL CARGO'";			
			$jasaMotor = "'JASA PENUMPUKAN SEPEDA MOTOR'";

			if ($PERIODE == ''.$YEAR.'-01'){
				
				$bulan1 = 'Januari';
				$PERIODE = "'$PERIODE'";

				$data_cbutl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_tl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_cbutl1)-> result_array();
				if ($data1){							
				$cbuTl1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cbuTl1 = 0;
				}

				$data_cbunontl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_cbunontl1)-> result_array();
				if ($data1){							
				$cbuNontl1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cbuNontl1 = 0;
				}

				$data_cbuluxurytl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_cbuluxurytl1)-> result_array();
				if ($data1){							
				$cbuLuxurytl1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cbuLuxurytl1 = 0;
				}

				$data_cbuluxuryntl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data1 = $con->query($data_cbuluxuryntl1)-> result_array();
				if ($data1){							
				$cbuLuxuryntl1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cbuLuxuryntl1 = 0;
				}

				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBustlA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBustlA1 = 0;
				}
				
				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBustlB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBustlB1 = 0;
				}

				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBustlC1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBustlC1 = 0;
				}
	
				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBustlD1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBustlD1 = 0;
				}

				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBustlE1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBustlE1 = 0;
				}
				
				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBusntlA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBusntlA1 = 0;
				}
				
				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBusntlB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBusntlB1 = 0;
				}

				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBusntlC1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBusntlC1 = 0;
				}
	
				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBusntlD1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBusntlD1 = 0;
				}

				$data_truckbusTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_truckbusTl1)-> result_array();
				if ($data1){							
				$truckBusntlE1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$truckBusntlE1 = 0;
				}
			
				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$albertlA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$albertlA1 = 0;
				}
				
				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$albertlB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$albertlB1 = 0;
				}
			
				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$albertlC1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$albertlC1 = 0;
				}

				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$albertlD1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$albertlD1 = 0;
				}

				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$albertlE1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$albertlE1 = 0;
				}
				
				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$albertlF1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$albertlF1 = 0;
				}

				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$alberntlA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$alberntlA1 = 0;
				}
				
				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$alberntlB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$alberntlB1 = 0;
				}
			
				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$alberntlC1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$alberntlC1 = 0;
				}

				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$alberntlD1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$alberntlD1 = 0;
				}

				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$alberntlE1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$alberntlE1 = 0;
				}

				$data_alberTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_alberTl1)-> result_array();
				if ($data1){							
				$alberntlF1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$alberntlF1 = 0;
				}				
				
				$data_motorTl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_motorTl1)-> result_array();
				if ($data1){							
				$motorTl1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$motorTl1 = 0;
				}

							
				$data_motorNtl1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_motorNtl1)-> result_array();
				if ($data1){							
				$motorNtl1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$motorNtl1 = 0;
				}

				$data_Cargo1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_Cargo1)-> result_array();
				if ($data1){							
				$cargoA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cargoA1 = 0;
				}

				$data_Cargo1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_Cargo1)-> result_array();
				if ($data1){							
				$cargoB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cargoB1 = 0;
				}

				$data_Cargo1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_Cargo1)-> result_array();
				if ($data1){							
				$cargoC1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cargoC1 = 0;
				}

				$data_Cargo1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($data_Cargo1)-> result_array();
				if ($data1){							
				$cargoD1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cargoD1 = 0;
				}

				$jasaCbu1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaCbu1)-> result_array();
				if ($data1){							
				$jasaCbuA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaCbuA1 = 0;
				}

				$jasaCbu1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaCbu1)-> result_array();
				if ($data1){							
				$jasaCbuB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaCbuB1 = 0;
				}

				$jasaCbulux1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaCbulux1)-> result_array();
				if ($data1){							
				$jasaCbuluxA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaCbuluxA1 = 0;
				}

				$jasaCbulux1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaCbulux1)-> result_array();
				if ($data1){							
				$jasaCbuluxB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaCbuluxB1 = 0;
				}

				$jasaAlber1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaAlber1)-> result_array();
				if ($data1){							
				$jasaAlberA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaAlberA1 = 0;
				}

				$jasaAlber1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaAlber1)-> result_array();
				if ($data1){							
				$jasaAlberB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaAlberB1 = 0;
				}		
				
				$jasaTruckbus1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaTruckbus1)-> result_array();
				if ($data1){							
				$jasaTruckbusA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaTruckbusA1 = 0;
				}

				$jasaTruckbus1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaTruckbus1)-> result_array();
				if ($data1){							
				$jasaTruckbusB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaTruckbusB1 = 0;
				}

				$jasaCargo1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaCargo1)-> result_array();
				if ($data1){							
				$jasaCargoA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaCargoA1 = 0;
				}

				$jasaCargo1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaCargo1)-> result_array();
				if ($data1){							
				$jasaCargoB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaCargoB1 = 0;
				}

				$jasaMotor1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaMotor1)-> result_array();
				if ($data1){							
				$jasaMotorA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaMotorA1 = 0;
				}

				$jasaMotor1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data1 = $con->query($jasaMotor1)-> result_array();
				if ($data1){							
				$jasaMotorB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaMotorB1 = 0;
				}

			}

			if ($PERIODE == ''.$YEAR.'-02'){
				$bulan2 = 'Februari';
				$PERIODE = "'$PERIODE'";

				$data_cbutl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_tl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_cbutl2)-> result_array();
				if ($data2){							
				$cbuTl2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cbuTl2 = 0;
				}

				$data_cbunontl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_cbunontl2)-> result_array();
				if ($data2){							
				$cbuNontl2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cbuNontl2 = 0;
				}

				$data_cbuluxurytl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_cbuluxurytl2)-> result_array();
				if ($data2){							
				$cbuLuxurytl2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cbuLuxurytl2 = 0;
				}

				$data_cbuluxuryntl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data2 = $con->query($data_cbuluxuryntl2)-> result_array();
				if ($data2){							
				$cbuLuxuryntl2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cbuLuxuryntl2 = 0;
				}

				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBustlA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBustlA2 = 0;
				}
				
				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBustlB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBustlB2 = 0;
				}

				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBustlC2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBustlC2 = 0;
				}
	
				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBustlD2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBustlD2 = 0;
				}

				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBustlE2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBustlE2 = 0;
				}
				
				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBusntlA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBusntlA2 = 0;
				}
				
				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBusntlB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBusntlB2 = 0;
				}

				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBusntlC2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBusntlC2 = 0;
				}
	
				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBusntlD2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBusntlD2 = 0;
				}

				$data_truckbusTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_truckbusTl2)-> result_array();
				if ($data2){							
				$truckBusntlE2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$truckBusntlE2 = 0;
				}
			
				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$albertlA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$albertlA2 = 0;
				}
				
				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$albertlB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$albertlB2 = 0;
				}
			
				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$albertlC2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$albertlC2 = 0;
				}

				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$albertlD2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$albertlD2 = 0;
				}

				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$albertlE2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$albertlE2 = 0;
				}
				
				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$albertlF2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$albertlF2 = 0;
				}

				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$alberntlA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$alberntlA2 = 0;
				}
				
				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$alberntlB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$alberntlB2 = 0;
				}
			
				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$alberntlC2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$alberntlC2 = 0;
				}

				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$alberntlD2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$alberntlD2 = 0;
				}

				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$alberntlE2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$alberntlE2 = 0;
				}

				$data_alberTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_alberTl2)-> result_array();
				if ($data2){							
				$alberntlF2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$alberntlF2 = 0;
				}				
				
				$data_motorTl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_motorTl2)-> result_array();
				if ($data2){							
				$motorTl2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$motorTl2 = 0;
				}

							
				$data_motorNtl2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_motorNtl2)-> result_array();
				if ($data2){							
				$motorNtl2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$motorNtl2 = 0;
				}

				$data_Cargo2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_Cargo2)-> result_array();
				if ($data2){							
				$cargoA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cargoA2 = 0;
				}

				$data_Cargo2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_Cargo2)-> result_array();
				if ($data2){							
				$cargoB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cargoB2 = 0;
				}

				$data_Cargo2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_Cargo2)-> result_array();
				if ($data2){							
				$cargoC2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cargoC2 = 0;
				}

				$data_Cargo2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($data_Cargo2)-> result_array();
				if ($data2){							
				$cargoD2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cargoD2 = 0;
				}

				$jasaCbu2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaCbu2)-> result_array();
				if ($data2){							
				$jasaCbuA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaCbuA2 = 0;
				}

				$jasaCbu2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaCbu2)-> result_array();
				if ($data2){							
				$jasaCbuB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaCbuB2 = 0;
				}

				$jasaCbulux2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaCbulux2)-> result_array();
				if ($data2){							
				$jasaCbuluxA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaCbuluxA2 = 0;
				}

				$jasaCbulux2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaCbulux2)-> result_array();
				if ($data2){							
				$jasaCbuluxB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaCbuluxB2 = 0;
				}

				$jasaAlber2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaAlber2)-> result_array();
				if ($data2){							
				$jasaAlberA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaAlberA2 = 0;
				}

				$jasaAlber2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaAlber2)-> result_array();
				if ($data2){							
				$jasaAlberB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaAlberB2 = 0;
				}		
				
				$jasaTruckbus2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaTruckbus2)-> result_array();
				if ($data2){							
				$jasaTruckbusA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaTruckbusA2 = 0;
				}

				$jasaTruckbus2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaTruckbus2)-> result_array();
				if ($data2){							
				$jasaTruckbusB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaTruckbusB2 = 0;
				}

				$jasaCargo2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaCargo2)-> result_array();
				if ($data2){							
				$jasaCargoA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaCargoA2 = 0;
				}

				$jasaCargo2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaCargo2)-> result_array();
				if ($data2){							
				$jasaCargoB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaCargoB2 = 0;
				}

				$jasaMotor2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaMotor2)-> result_array();
				if ($data2){							
				$jasaMotorA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaMotorA2 = 0;
				}

				$jasaMotor2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data2 = $con->query($jasaMotor2)-> result_array();
				if ($data2){							
				$jasaMotorB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaMotorB2 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-03'){
				$bulan3 = 'Maret';
				$PERIODE = "'$PERIODE'";

				$data_cbutl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_tl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_cbutl3)-> result_array();
				if ($data3){							
				$cbuTl3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cbuTl3 = 0;
				}

				$data_cbunontl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_cbunontl3)-> result_array();
				if ($data3){							
				$cbuNontl3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cbuNontl3 = 0;
				}

				$data_cbuluxurytl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_cbuluxurytl3)-> result_array();
				if ($data3){							
				$cbuLuxurytl3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cbuLuxurytl3 = 0;
				}

				$data_cbuluxuryntl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data3 = $con->query($data_cbuluxuryntl3)-> result_array();
				if ($data3){							
				$cbuLuxuryntl3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cbuLuxuryntl3 = 0;
				}

				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBustlA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBustlA3 = 0;
				}
				
				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBustlB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBustlB3 = 0;
				}

				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBustlC3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBustlC3 = 0;
				}
	
				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBustlD3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBustlD3 = 0;
				}

				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBustlE3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBustlE3 = 0;
				}
				
				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBusntlA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBusntlA3 = 0;
				}
				
				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBusntlB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBusntlB3 = 0;
				}

				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBusntlC3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBusntlC3 = 0;
				}
	
				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBusntlD3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBusntlD3 = 0;
				}

				$data_truckbusTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_truckbusTl3)-> result_array();
				if ($data3){							
				$truckBusntlE3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$truckBusntlE3 = 0;
				}
			
				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$albertlA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$albertlA3 = 0;
				}
				
				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$albertlB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$albertlB3 = 0;
				}
			
				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$albertlC3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$albertlC3 = 0;
				}

				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$albertlD3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$albertlD3 = 0;
				}

				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$albertlE3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$albertlE3 = 0;
				}
				
				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$albertlF3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$albertlF3 = 0;
				}

				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$alberntlA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$alberntlA3 = 0;
				}
				
				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$alberntlB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$alberntlB3 = 0;
				}
			
				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$alberntlC3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$alberntlC3 = 0;
				}

				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$alberntlD3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$alberntlD3 = 0;
				}

				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$alberntlE3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$alberntlE3 = 0;
				}

				$data_alberTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_alberTl3)-> result_array();
				if ($data3){							
				$alberntlF3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$alberntlF3 = 0;
				}				
				
				$data_motorTl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_motorTl3)-> result_array();
				if ($data3){							
				$motorTl3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$motorTl3 = 0;
				}

							
				$data_motorNtl3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_motorNtl3)-> result_array();
				if ($data3){							
				$motorNtl3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$motorNtl3 = 0;
				}

				$data_Cargo3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_Cargo3)-> result_array();
				if ($data3){							
				$cargoA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cargoA3 = 0;
				}

				$data_Cargo3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_Cargo3)-> result_array();
				if ($data3){							
				$cargoB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cargoB3 = 0;
				}

				$data_Cargo3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_Cargo3)-> result_array();
				if ($data3){							
				$cargoC3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cargoC3 = 0;
				}

				$data_Cargo3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($data_Cargo3)-> result_array();
				if ($data3){							
				$cargoD3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cargoD3 = 0;
				}

				$jasaCbu3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaCbu3)-> result_array();
				if ($data3){							
				$jasaCbuA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaCbuA3 = 0;
				}

				$jasaCbu3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaCbu3)-> result_array();
				if ($data3){							
				$jasaCbuB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaCbuB3 = 0;
				}

				$jasaCbulux3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaCbulux3)-> result_array();
				if ($data3){							
				$jasaCbuluxA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaCbuluxA3 = 0;
				}

				$jasaCbulux3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaCbulux3)-> result_array();
				if ($data3){							
				$jasaCbuluxB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaCbuluxB3 = 0;
				}

				$jasaAlber3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaAlber3)-> result_array();
				if ($data3){							
				$jasaAlberA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaAlberA3 = 0;
				}

				$jasaAlber3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaAlber3)-> result_array();
				if ($data3){							
				$jasaAlberB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaAlberB3 = 0;
				}		
				
				$jasaTruckbus3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaTruckbus3)-> result_array();
				if ($data3){							
				$jasaTruckbusA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaTruckbusA3 = 0;
				}

				$jasaTruckbus3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaTruckbus3)-> result_array();
				if ($data3){							
				$jasaTruckbusB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaTruckbusB3 = 0;
				}

				$jasaCargo3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaCargo3)-> result_array();
				if ($data3){							
				$jasaCargoA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaCargoA3 = 0;
				}

				$jasaCargo3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaCargo3)-> result_array();
				if ($data3){							
				$jasaCargoB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaCargoB3 = 0;
				}

				$jasaMotor3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaMotor3)-> result_array();
				if ($data3){							
				$jasaMotorA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaMotorA3 = 0;
				}

				$jasaMotor3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data3 = $con->query($jasaMotor3)-> result_array();
				if ($data3){							
				$jasaMotorB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaMotorB3 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-04'){
				$bulan4 = 'April';
				$PERIODE = "'$PERIODE'";

				$data_cbutl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_cbutl4)-> result_array();
				if ($data4){							
				$cbuTl4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cbuTl4 = 0;
				}

				$data_cbunontl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_cbunontl4)-> result_array();
				if ($data4){							
				$cbuNontl4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cbuNontl4 = 0;
				}

				$data_cbuluxurytl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_cbuluxurytl4)-> result_array();
				if ($data4){							
				$cbuLuxurytl4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cbuLuxurytl4 = 0;
				}

				$data_cbuluxuryntl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data4 = $con->query($data_cbuluxuryntl4)-> result_array();
				if ($data4){							
				$cbuLuxuryntl4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cbuLuxuryntl4 = 0;
				}

				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBustlA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBustlA4 = 0;
				}
				
				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBustlB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBustlB4 = 0;
				}

				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBustlC4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBustlC4 = 0;
				}
	
				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBustlD4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBustlD4 = 0;
				}

				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBustlE4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBustlE4 = 0;
				}
				
				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBusntlA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBusntlA4 = 0;
				}
				
				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBusntlB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBusntlB4 = 0;
				}

				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBusntlC4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBusntlC4 = 0;
				}
	
				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBusntlD4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBusntlD4 = 0;
				}

				$data_truckbusTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_truckbusTl4)-> result_array();
				if ($data4){							
				$truckBusntlE4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$truckBusntlE4 = 0;
				}
			
				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$albertlA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$albertlA4 = 0;
				}
				
				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$albertlB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$albertlB4 = 0;
				}
			
				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$albertlC4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$albertlC4 = 0;
				}

				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$albertlD4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$albertlD4 = 0;
				}

				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$albertlE4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$albertlE4 = 0;
				}
				
				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$albertlF4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$albertlF4 = 0;
				}

				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$alberntlA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$alberntlA4 = 0;
				}
				
				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$alberntlB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$alberntlB4 = 0;
				}
			
				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$alberntlC4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$alberntlC4 = 0;
				}

				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$alberntlD4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$alberntlD4 = 0;
				}

				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$alberntlE4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$alberntlE4 = 0;
				}

				$data_alberTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_alberTl4)-> result_array();
				if ($data4){							
				$alberntlF4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$alberntlF4 = 0;
				}				
				
				$data_motorTl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_motorTl4)-> result_array();
				if ($data4){							
				$motorTl4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$motorTl4 = 0;
				}

							
				$data_motorNtl4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_motorNtl4)-> result_array();
				if ($data4){							
				$motorNtl4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$motorNtl4 = 0;
				}

				$data_Cargo4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_Cargo4)-> result_array();
				if ($data4){							
				$cargoA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cargoA4 = 0;
				}

				$data_Cargo4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_Cargo4)-> result_array();
				if ($data4){							
				$cargoB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cargoB4 = 0;
				}

				$data_Cargo4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_Cargo4)-> result_array();
				if ($data4){							
				$cargoC4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cargoC4 = 0;
				}

				$data_Cargo4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($data_Cargo4)-> result_array();
				if ($data4){							
				$cargoD4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cargoD4 = 0;
				}

				$jasaCbu4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaCbu4)-> result_array();
				if ($data4){							
				$jasaCbuA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaCbuA4 = 0;
				}

				$jasaCbu4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaCbu4)-> result_array();
				if ($data4){							
				$jasaCbuB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaCbuB4 = 0;
				}

				$jasaCbulux4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaCbulux4)-> result_array();
				if ($data4){							
				$jasaCbuluxA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaCbuluxA4 = 0;
				}

				$jasaCbulux4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaCbulux4)-> result_array();
				if ($data4){							
				$jasaCbuluxB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaCbuluxB4 = 0;
				}

				$jasaAlber4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaAlber4)-> result_array();
				if ($data4){							
				$jasaAlberA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaAlberA4 = 0;
				}

				$jasaAlber4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaAlber4)-> result_array();
				if ($data4){							
				$jasaAlberB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaAlberB4 = 0;
				}		
				
				$jasaTruckbus4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaTruckbus4)-> result_array();
				if ($data4){							
				$jasaTruckbusA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaTruckbusA4 = 0;
				}

				$jasaTruckbus4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaTruckbus4)-> result_array();
				if ($data4){							
				$jasaTruckbusB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaTruckbusB4 = 0;
				}

				$jasaCargo4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaCargo4)-> result_array();
				if ($data4){							
				$jasaCargoA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaCargoA4 = 0;
				}

				$jasaCargo4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaCargo4)-> result_array();
				if ($data4){							
				$jasaCargoB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaCargoB4 = 0;
				}

				$jasaMotor4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaMotor4)-> result_array();
				if ($data4){							
				$jasaMotorA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaMotorA4 = 0;
				}

				$jasaMotor4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data4 = $con->query($jasaMotor4)-> result_array();
				if ($data4){							
				$jasaMotorB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaMotorB4 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-05'){
				$bulan5 = 'Mei';
				$PERIODE = "'$PERIODE'";

				$data_cbutl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
			WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.'  
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_cbutl5)-> result_array();
				if ($data5){							
				$cbuTl5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cbuTl5 = 0;
				}

				$data_cbunontl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_cbunontl5)-> result_array();
				if ($data5){							
				$cbuNontl5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cbuNontl5 = 0;
				}

				$data_cbuluxurytl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_cbuluxurytl5)-> result_array();
				if ($data5){							
				$cbuLuxurytl5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cbuLuxurytl5 = 0;
				}

				$data_cbuluxuryntl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data5 = $con->query($data_cbuluxuryntl5)-> result_array();
				if ($data5){							
				$cbuLuxuryntl5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cbuLuxuryntl5 = 0;
				}

				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBustlA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBustlA5 = 0;
				}
				
				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBustlB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBustlB5 = 0;
				}

				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBustlC5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBustlC5 = 0;
				}
	
				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBustlD5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBustlD5 = 0;
				}

				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBustlE5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBustlE5 = 0;
				}
				
				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBusntlA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBusntlA5 = 0;
				}
				
				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBusntlB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBusntlB5 = 0;
				}

				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBusntlC5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBusntlC5 = 0;
				}
	
				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBusntlD5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBusntlD5 = 0;
				}

				$data_truckbusTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_truckbusTl5)-> result_array();
				if ($data5){							
				$truckBusntlE5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$truckBusntlE5 = 0;
				}
			
				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$albertlA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$albertlA5 = 0;
				}
				
				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$albertlB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$albertlB5 = 0;
				}
			
				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$albertlC5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$albertlC5 = 0;
				}

				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$albertlD5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$albertlD5 = 0;
				}

				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$albertlE5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$albertlE5 = 0;
				}
				
				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$albertlF5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$albertlF5 = 0;
				}

				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$alberntlA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$alberntlA5 = 0;
				}
				
				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$alberntlB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$alberntlB5 = 0;
				}
			
				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$alberntlC5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$alberntlC5 = 0;
				}

				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$alberntlD5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$alberntlD5 = 0;
				}

				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$alberntlE5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$alberntlE5 = 0;
				}

				$data_alberTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_alberTl5)-> result_array();
				if ($data5){							
				$alberntlF5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$alberntlF5 = 0;
				}				
				
				$data_motorTl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_motorTl5)-> result_array();
				if ($data5){							
				$motorTl5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$motorTl5 = 0;
				}

							
				$data_motorNtl5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_motorNtl5)-> result_array();
				if ($data5){							
				$motorNtl5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$motorNtl5 = 0;
				}

				$data_Cargo5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_Cargo5)-> result_array();
				if ($data5){							
				$cargoA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cargoA5 = 0;
				}

				$data_Cargo5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_Cargo5)-> result_array();
				if ($data5){							
				$cargoB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cargoB5 = 0;
				}

				$data_Cargo5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_Cargo5)-> result_array();
				if ($data5){							
				$cargoC5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cargoC5 = 0;
				}

				$data_Cargo5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($data_Cargo5)-> result_array();
				if ($data5){							
				$cargoD5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cargoD5 = 0;
				}

				$jasaCbu5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaCbu5)-> result_array();
				if ($data5){							
				$jasaCbuA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaCbuA5 = 0;
				}

				$jasaCbu5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaCbu5)-> result_array();
				if ($data5){							
				$jasaCbuB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaCbuB5 = 0;
				}

				$jasaCbulux5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaCbulux5)-> result_array();
				if ($data5){							
				$jasaCbuluxA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaCbuluxA5 = 0;
				}

				$jasaCbulux5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaCbulux5)-> result_array();
				if ($data5){							
				$jasaCbuluxB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaCbuluxB5 = 0;
				}

				$jasaAlber5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaAlber5)-> result_array();
				if ($data5){							
				$jasaAlberA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaAlberA5 = 0;
				}

				$jasaAlber5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaAlber5)-> result_array();
				if ($data5){							
				$jasaAlberB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaAlberB5 = 0;
				}		
				
				$jasaTruckbus5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaTruckbus5)-> result_array();
				if ($data5){							
				$jasaTruckbusA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaTruckbusA5 = 0;
				}

				$jasaTruckbus5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaTruckbus5)-> result_array();
				if ($data5){							
				$jasaTruckbusB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaTruckbusB5 = 0;
				}

				$jasaCargo5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaCargo5)-> result_array();
				if ($data5){							
				$jasaCargoA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaCargoA5 = 0;
				}

				$jasaCargo5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaCargo5)-> result_array();
				if ($data5){							
				$jasaCargoB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaCargoB5 = 0;
				}

				$jasaMotor5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaMotor5)-> result_array();
				if ($data5){							
				$jasaMotorA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaMotorA5 = 0;
				}

				$jasaMotor5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data5 = $con->query($jasaMotor5)-> result_array();
				if ($data5){							
				$jasaMotorB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaMotorB5 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-06'){
				$bulan6 = 'Juni';
				$PERIODE = "'$PERIODE'";

				$data_cbutl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
			WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.'  
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_cbutl6)-> result_array();
				if ($data6){							
				$cbuTl6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cbuTl6 = 0;
				}

				$data_cbunontl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_cbunontl6)-> result_array();
				if ($data6){							
				$cbuNontl6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cbuNontl6 = 0;
				}

				$data_cbuluxurytl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_cbuluxurytl6)-> result_array();
				if ($data6){							
				$cbuLuxurytl6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cbuLuxurytl6 = 0;
				}

				$data_cbuluxuryntl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data6 = $con->query($data_cbuluxuryntl6)-> result_array();
				if ($data6){							
				$cbuLuxuryntl6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cbuLuxuryntl6 = 0;
				}

				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBustlA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBustlA6 = 0;
				}
				
				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBustlB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBustlB6 = 0;
				}

				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBustlC6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBustlC6 = 0;
				}
	
				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBustlD6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBustlD6 = 0;
				}

				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBustlE6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBustlE6 = 0;
				}
				
				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBusntlA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBusntlA6 = 0;
				}
				
				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBusntlB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBusntlB6 = 0;
				}

				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBusntlC6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBusntlC6 = 0;
				}
	
				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBusntlD6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBusntlD6 = 0;
				}

				$data_truckbusTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_truckbusTl6)-> result_array();
				if ($data6){							
				$truckBusntlE6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$truckBusntlE6 = 0;
				}
			
				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$albertlA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$albertlA6 = 0;
				}
				
				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$albertlB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$albertlB6 = 0;
				}
			
				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$albertlC6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$albertlC6 = 0;
				}

				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$albertlD6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$albertlD6 = 0;
				}

				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$albertlE6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$albertlE6 = 0;
				}
				
				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$albertlF6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$albertlF6 = 0;
				}

				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$alberntlA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$alberntlA6 = 0;
				}
				
				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$alberntlB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$alberntlB6 = 0;
				}
			
				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$alberntlC6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$alberntlC6 = 0;
				}

				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$alberntlD6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$alberntlD6 = 0;
				}

				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$alberntlE6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$alberntlE6 = 0;
				}

				$data_alberTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_alberTl6)-> result_array();
				if ($data6){							
				$alberntlF6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$alberntlF6 = 0;
				}				
				
				$data_motorTl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_motorTl6)-> result_array();
				if ($data6){							
				$motorTl6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$motorTl6 = 0;
				}

							
				$data_motorNtl6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_motorNtl6)-> result_array();
				if ($data6){							
				$motorNtl6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$motorNtl6 = 0;
				}

				$data_Cargo6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_Cargo6)-> result_array();
				if ($data6){							
				$cargoA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cargoA6 = 0;
				}

				$data_Cargo6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_Cargo6)-> result_array();
				if ($data6){							
				$cargoB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cargoB6 = 0;
				}

				$data_Cargo6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_Cargo6)-> result_array();
				if ($data6){							
				$cargoC6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cargoC6 = 0;
				}

				$data_Cargo6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($data_Cargo6)-> result_array();
				if ($data6){							
				$cargoD6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cargoD6 = 0;
				}

				$jasaCbu6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaCbu6)-> result_array();
				if ($data6){							
				$jasaCbuA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaCbuA6 = 0;
				}

				$jasaCbu6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaCbu6)-> result_array();
				if ($data6){							
				$jasaCbuB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaCbuB6 = 0;
				}

				$jasaCbulux6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaCbulux6)-> result_array();
				if ($data6){							
				$jasaCbuluxA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaCbuluxA6 = 0;
				}

				$jasaCbulux6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaCbulux6)-> result_array();
				if ($data6){							
				$jasaCbuluxB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaCbuluxB6 = 0;
				}

				$jasaAlber6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaAlber6)-> result_array();
				if ($data6){							
				$jasaAlberA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaAlberA6 = 0;
				}

				$jasaAlber6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaAlber6)-> result_array();
				if ($data6){							
				$jasaAlberB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaAlberB6 = 0;
				}		
				
				$jasaTruckbus6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaTruckbus6)-> result_array();
				if ($data6){							
				$jasaTruckbusA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaTruckbusA6 = 0;
				}

				$jasaTruckbus6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaTruckbus6)-> result_array();
				if ($data6){							
				$jasaTruckbusB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaTruckbusB6 = 0;
				}

				$jasaCargo6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaCargo6)-> result_array();
				if ($data6){							
				$jasaCargoA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaCargoA6 = 0;
				}

				$jasaCargo6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaCargo6)-> result_array();
				if ($data6){							
				$jasaCargoB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaCargoB6 = 0;
				}

				$jasaMotor6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaMotor6)-> result_array();
				if ($data6){							
				$jasaMotorA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaMotorA6 = 0;
				}

				$jasaMotor6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data6 = $con->query($jasaMotor6)-> result_array();
				if ($data6){							
				$jasaMotorB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaMotorB6 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-07'){
				$bulan7 = 'Juli';
				$PERIODE = "'$PERIODE'";

				$data_cbutl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
			WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.'  
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_cbutl7)-> result_array();
				if ($data7){							
				$cbuTl7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cbuTl7 = 0;
				}

				$data_cbunontl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_cbunontl7)-> result_array();
				if ($data7){							
				$cbuNontl7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cbuNontl7 = 0;
				}

				$data_cbuluxurytl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_cbuluxurytl7)-> result_array();
				if ($data7){							
				$cbuLuxurytl7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cbuLuxurytl7 = 0;
				}

				$data_cbuluxuryntl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data7 = $con->query($data_cbuluxuryntl7)-> result_array();
				if ($data7){							
				$cbuLuxuryntl7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cbuLuxuryntl7 = 0;
				}

				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBustlA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBustlA7 = 0;
				}
				
				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBustlB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBustlB7 = 0;
				}

				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBustlC7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBustlC7 = 0;
				}
	
				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBustlD7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBustlD7 = 0;
				}

				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBustlE7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBustlE7 = 0;
				}
				
				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBusntlA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBusntlA7 = 0;
				}
				
				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBusntlB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBusntlB7 = 0;
				}

				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBusntlC7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBusntlC7 = 0;
				}
	
				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBusntlD7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBusntlD7 = 0;
				}

				$data_truckbusTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_truckbusTl7)-> result_array();
				if ($data7){							
				$truckBusntlE7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$truckBusntlE7 = 0;
				}
			
				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$albertlA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$albertlA7 = 0;
				}
				
				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$albertlB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$albertlB7 = 0;
				}
			
				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$albertlC7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$albertlC7 = 0;
				}

				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$albertlD7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$albertlD7 = 0;
				}

				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$albertlE7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$albertlE7 = 0;
				}
				
				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$albertlF7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$albertlF7 = 0;
				}

				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$alberntlA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$alberntlA7 = 0;
				}
				
				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$alberntlB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$alberntlB7 = 0;
				}
			
				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$alberntlC7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$alberntlC7 = 0;
				}

				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$alberntlD7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$alberntlD7 = 0;
				}

				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$alberntlE7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$alberntlE7 = 0;
				}

				$data_alberTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_alberTl7)-> result_array();
				if ($data7){							
				$alberntlF7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$alberntlF7 = 0;
				}				
				
				$data_motorTl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_motorTl7)-> result_array();
				if ($data7){							
				$motorTl7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$motorTl7 = 0;
				}

							
				$data_motorNtl7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_motorNtl7)-> result_array();
				if ($data7){							
				$motorNtl7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$motorNtl7 = 0;
				}

				$data_Cargo7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_Cargo7)-> result_array();
				if ($data7){							
				$cargoA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cargoA7 = 0;
				}

				$data_Cargo7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_Cargo7)-> result_array();
				if ($data7){							
				$cargoB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cargoB7 = 0;
				}

				$data_Cargo7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_Cargo7)-> result_array();
				if ($data7){							
				$cargoC7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cargoC7 = 0;
				}

				$data_Cargo7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($data_Cargo7)-> result_array();
				if ($data7){							
				$cargoD7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cargoD7 = 0;
				}

				$jasaCbu7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaCbu7)-> result_array();
				if ($data7){							
				$jasaCbuA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaCbuA7 = 0;
				}

				$jasaCbu7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaCbu7)-> result_array();
				if ($data7){							
				$jasaCbuB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaCbuB7 = 0;
				}

				$jasaCbulux7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaCbulux7)-> result_array();
				if ($data7){							
				$jasaCbuluxA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaCbuluxA7 = 0;
				}

				$jasaCbulux7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaCbulux7)-> result_array();
				if ($data7){							
				$jasaCbuluxB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaCbuluxB7 = 0;
				}

				$jasaAlber7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaAlber7)-> result_array();
				if ($data7){							
				$jasaAlberA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaAlberA7 = 0;
				}

				$jasaAlber7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaAlber7)-> result_array();
				if ($data7){							
				$jasaAlberB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaAlberB7 = 0;
				}		
				
				$jasaTruckbus7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaTruckbus7)-> result_array();
				if ($data7){							
				$jasaTruckbusA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaTruckbusA7 = 0;
				}

				$jasaTruckbus7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaTruckbus7)-> result_array();
				if ($data7){							
				$jasaTruckbusB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaTruckbusB7 = 0;
				}

				$jasaCargo7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaCargo7)-> result_array();
				if ($data7){							
				$jasaCargoA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaCargoA7 = 0;
				}

				$jasaCargo7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaCargo7)-> result_array();
				if ($data7){							
				$jasaCargoB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaCargoB7 = 0;
				}

				$jasaMotor7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaMotor7)-> result_array();
				if ($data7){							
				$jasaMotorA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaMotorA7 = 0;
				}

				$jasaMotor7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data7 = $con->query($jasaMotor7)-> result_array();
				if ($data7){							
				$jasaMotorB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaMotorB7 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-08'){
				$bulan8 = 'Agustus';
				$PERIODE = "'$PERIODE'";

				$data_cbutl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
			WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.'  
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_cbutl8)-> result_array();
				if ($data8){							
				$cbuTl8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cbuTl8 = 0;
				}

				$data_cbunontl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_cbunontl8)-> result_array();
				if ($data8){							
				$cbuNontl8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cbuNontl8 = 0;
				}

				$data_cbuluxurytl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_cbuluxurytl8)-> result_array();
				if ($data8){							
				$cbuLuxurytl8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cbuLuxurytl8 = 0;
				}

				$data_cbuluxuryntl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data8 = $con->query($data_cbuluxuryntl8)-> result_array();
				if ($data8){							
				$cbuLuxuryntl8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cbuLuxuryntl8 = 0;
				}

				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBustlA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBustlA8 = 0;
				}
				
				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBustlB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBustlB8 = 0;
				}

				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBustlC8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBustlC8 = 0;
				}
	
				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBustlD8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBustlD8 = 0;
				}

				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBustlE8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBustlE8 = 0;
				}
				
				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBusntlA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBusntlA8 = 0;
				}
				
				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBusntlB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBusntlB8 = 0;
				}

				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBusntlC8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBusntlC8 = 0;
				}
	
				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBusntlD8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBusntlD8 = 0;
				}

				$data_truckbusTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_truckbusTl8)-> result_array();
				if ($data8){							
				$truckBusntlE8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$truckBusntlE8 = 0;
				}
			
				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$albertlA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$albertlA8 = 0;
				}
				
				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$albertlB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$albertlB8 = 0;
				}
			
				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$albertlC8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$albertlC8 = 0;
				}

				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$albertlD8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$albertlD8 = 0;
				}

				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$albertlE8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$albertlE8 = 0;
				}
				
				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$albertlF8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$albertlF8 = 0;
				}

				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$alberntlA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$alberntlA8 = 0;
				}
				
				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$alberntlB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$alberntlB8 = 0;
				}
			
				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$alberntlC8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$alberntlC8 = 0;
				}

				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$alberntlD8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$alberntlD8 = 0;
				}

				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$alberntlE8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$alberntlE8 = 0;
				}

				$data_alberTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_alberTl8)-> result_array();
				if ($data8){							
				$alberntlF8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$alberntlF8 = 0;
				}				
				
				$data_motorTl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_motorTl8)-> result_array();
				if ($data8){							
				$motorTl8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$motorTl8 = 0;
				}

							
				$data_motorNtl8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_motorNtl8)-> result_array();
				if ($data8){							
				$motorNtl8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$motorNtl8 = 0;
				}

				$data_Cargo8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_Cargo8)-> result_array();
				if ($data8){							
				$cargoA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cargoA8 = 0;
				}

				$data_Cargo8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_Cargo8)-> result_array();
				if ($data8){							
				$cargoB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cargoB8 = 0;
				}

				$data_Cargo8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_Cargo8)-> result_array();
				if ($data8){							
				$cargoC8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cargoC8 = 0;
				}

				$data_Cargo8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($data_Cargo8)-> result_array();
				if ($data8){							
				$cargoD8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cargoD8 = 0;
				}

				$jasaCbu8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaCbu8)-> result_array();
				if ($data8){							
				$jasaCbuA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaCbuA8 = 0;
				}

				$jasaCbu8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaCbu8)-> result_array();
				if ($data8){							
				$jasaCbuB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaCbuB8 = 0;
				}

				$jasaCbulux8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaCbulux8)-> result_array();
				if ($data8){							
				$jasaCbuluxA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaCbuluxA8 = 0;
				}

				$jasaCbulux8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaCbulux8)-> result_array();
				if ($data8){							
				$jasaCbuluxB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaCbuluxB8 = 0;
				}

				$jasaAlber8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaAlber8)-> result_array();
				if ($data8){							
				$jasaAlberA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaAlberA8 = 0;
				}

				$jasaAlber8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaAlber8)-> result_array();
				if ($data8){							
				$jasaAlberB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaAlberB8 = 0;
				}		
				
				$jasaTruckbus8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaTruckbus8)-> result_array();
				if ($data8){							
				$jasaTruckbusA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaTruckbusA8 = 0;
				}

				$jasaTruckbus8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaTruckbus8)-> result_array();
				if ($data8){							
				$jasaTruckbusB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaTruckbusB8 = 0;
				}

				$jasaCargo8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaCargo8)-> result_array();
				if ($data8){							
				$jasaCargoA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaCargoA8 = 0;
				}

				$jasaCargo8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaCargo8)-> result_array();
				if ($data8){							
				$jasaCargoB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaCargoB8 = 0;
				}

				$jasaMotor8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaMotor8)-> result_array();
				if ($data8){							
				$jasaMotorA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaMotorA8 = 0;
				}

				$jasaMotor8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data8 = $con->query($jasaMotor8)-> result_array();
				if ($data8){							
				$jasaMotorB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaMotorB8 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-09'){
				$bulan9 = 'September';
				$PERIODE = "'$PERIODE'";

				$data_cbutl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
			WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.'  
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_cbutl9)-> result_array();
				if ($data9){							
				$cbuTl9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cbuTl9 = 0;
				}

				$data_cbunontl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_cbunontl9)-> result_array();
				if ($data9){							
				$cbuNontl9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cbuNontl9 = 0;
				}

				$data_cbuluxurytl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_cbuluxurytl9)-> result_array();
				if ($data9){							
				$cbuLuxurytl9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cbuLuxurytl9 = 0;
				}

				$data_cbuluxuryntl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data9 = $con->query($data_cbuluxuryntl9)-> result_array();
				if ($data9){							
				$cbuLuxuryntl9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cbuLuxuryntl9 = 0;
				}

				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBustlA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBustlA9 = 0;
				}
				
				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBustlB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBustlB9 = 0;
				}

				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBustlC9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBustlC9 = 0;
				}
	
				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBustlD9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBustlD9 = 0;
				}

				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBustlE9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBustlE9 = 0;
				}
				
				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBusntlA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBusntlA9 = 0;
				}
				
				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBusntlB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBusntlB9 = 0;
				}

				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBusntlC9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBusntlC9 = 0;
				}
	
				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBusntlD9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBusntlD9 = 0;
				}

				$data_truckbusTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_truckbusTl9)-> result_array();
				if ($data9){							
				$truckBusntlE9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$truckBusntlE9 = 0;
				}
			
				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$albertlA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$albertlA9 = 0;
				}
				
				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$albertlB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$albertlB9 = 0;
				}
			
				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$albertlC9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$albertlC9 = 0;
				}

				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$albertlD9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$albertlD9 = 0;
				}

				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$albertlE9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$albertlE9 = 0;
				}
				
				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$albertlF9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$albertlF9 = 0;
				}

				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$alberntlA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$alberntlA9 = 0;
				}
				
				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$alberntlB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$alberntlB9 = 0;
				}
			
				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$alberntlC9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$alberntlC9 = 0;
				}

				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$alberntlD9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$alberntlD9 = 0;
				}

				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$alberntlE9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$alberntlE9 = 0;
				}

				$data_alberTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_alberTl9)-> result_array();
				if ($data9){							
				$alberntlF9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$alberntlF9 = 0;
				}				
				
				$data_motorTl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_motorTl9)-> result_array();
				if ($data9){							
				$motorTl9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$motorTl9 = 0;
				}

							
				$data_motorNtl9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_motorNtl9)-> result_array();
				if ($data9){							
				$motorNtl9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$motorNtl9 = 0;
				}

				$data_Cargo9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_Cargo9)-> result_array();
				if ($data9){							
				$cargoA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cargoA9 = 0;
				}

				$data_Cargo9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_Cargo9)-> result_array();
				if ($data9){							
				$cargoB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cargoB9 = 0;
				}

				$data_Cargo9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_Cargo9)-> result_array();
				if ($data9){							
				$cargoC9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cargoC9 = 0;
				}

				$data_Cargo9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($data_Cargo9)-> result_array();
				if ($data9){							
				$cargoD9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cargoD9 = 0;
				}

				$jasaCbu9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaCbu9)-> result_array();
				if ($data9){							
				$jasaCbuA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaCbuA9 = 0;
				}

				$jasaCbu9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaCbu9)-> result_array();
				if ($data9){							
				$jasaCbuB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaCbuB9 = 0;
				}

				$jasaCbulux9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaCbulux9)-> result_array();
				if ($data9){							
				$jasaCbuluxA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaCbuluxA9 = 0;
				}

				$jasaCbulux9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaCbulux9)-> result_array();
				if ($data9){							
				$jasaCbuluxB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaCbuluxB9 = 0;
				}

				$jasaAlber9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaAlber9)-> result_array();
				if ($data9){							
				$jasaAlberA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaAlberA9 = 0;
				}

				$jasaAlber9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaAlber9)-> result_array();
				if ($data9){							
				$jasaAlberB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaAlberB9 = 0;
				}		
				
				$jasaTruckbus9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaTruckbus9)-> result_array();
				if ($data9){							
				$jasaTruckbusA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaTruckbusA9 = 0;
				}

				$jasaTruckbus9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaTruckbus9)-> result_array();
				if ($data9){							
				$jasaTruckbusB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaTruckbusB9 = 0;
				}

				$jasaCargo9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaCargo9)-> result_array();
				if ($data9){							
				$jasaCargoA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaCargoA9 = 0;
				}

				$jasaCargo9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaCargo9)-> result_array();
				if ($data9){							
				$jasaCargoB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaCargoB9 = 0;
				}

				$jasaMotor9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaMotor9)-> result_array();
				if ($data9){							
				$jasaMotorA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaMotorA9 = 0;
				}

				$jasaMotor9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data9 = $con->query($jasaMotor9)-> result_array();
				if ($data9){							
				$jasaMotorB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaMotorB9 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-10'){
				$bulan10 = 'Oktober';
				$PERIODE = "'$PERIODE'";
				$data_cbutl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
			WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.'  
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_cbutl10)-> result_array();
				if ($data10){							
				$cbuTl10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cbuTl10 = 0;
				}

				$data_cbunontl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_cbunontl10)-> result_array();
				if ($data10){							
				$cbuNontl10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cbuNontl10 = 0;
				}

				$data_cbuluxurytl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_cbuluxurytl10)-> result_array();
				if ($data10){							
				$cbuLuxurytl10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cbuLuxurytl10 = 0;
				}

				$data_cbuluxuryntl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data10 = $con->query($data_cbuluxuryntl10)-> result_array();
				if ($data10){							
				$cbuLuxuryntl10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cbuLuxuryntl10 = 0;
				}

				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBustlA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBustlA10 = 0;
				}
				
				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBustlB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBustlB10 = 0;
				}

				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBustlC10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBustlC10 = 0;
				}
	
				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBustlD10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBustlD10 = 0;
				}

				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBustlE10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBustlE10 = 0;
				}
				
				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBusntlA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBusntlA10 = 0;
				}
				
				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBusntlB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBusntlB10 = 0;
				}

				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBusntlC10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBusntlC10 = 0;
				}
	
				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBusntlD10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBusntlD10 = 0;
				}

				$data_truckbusTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_truckbusTl10)-> result_array();
				if ($data10){							
				$truckBusntlE10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$truckBusntlE10 = 0;
				}
			
				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$albertlA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$albertlA10 = 0;
				}
				
				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$albertlB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$albertlB10 = 0;
				}
			
				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$albertlC10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$albertlC10 = 0;
				}

				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$albertlD10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$albertlD10 = 0;
				}

				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$albertlE10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$albertlE10 = 0;
				}
				
				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$albertlF10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$albertlF10 = 0;
				}

				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$alberntlA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$alberntlA10 = 0;
				}
				
				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$alberntlB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$alberntlB10 = 0;
				}
			
				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$alberntlC10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$alberntlC10 = 0;
				}

				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.'and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$alberntlD10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$alberntlD10 = 0;
				}

				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$alberntlE10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$alberntlE10 = 0;
				}

				$data_alberTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_alberTl10)-> result_array();
				if ($data10){							
				$alberntlF10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$alberntlF10 = 0;
				}				
				
				$data_motorTl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_motorTl10)-> result_array();
				if ($data10){							
				$motorTl10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$motorTl10 = 0;
				}

							
				$data_motorNtl10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_motorNtl10)-> result_array();
				if ($data10){							
				$motorNtl10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$motorNtl10 = 0;
				}

				$data_Cargo10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_Cargo10)-> result_array();
				if ($data10){							
				$cargoA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cargoA10 = 0;
				}

				$data_Cargo10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_Cargo10)-> result_array();
				if ($data10){							
				$cargoB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cargoB10 = 0;
				}

				$data_Cargo10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_Cargo10)-> result_array();
				if ($data10){							
				$cargoC10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cargoC10 = 0;
				}

				$data_Cargo10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($data_Cargo10)-> result_array();
				if ($data10){							
				$cargoD10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cargoD10 = 0;
				}

				$jasaCbu10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaCbu10)-> result_array();
				if ($data10){							
				$jasaCbuA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaCbuA10 = 0;
				}

				$jasaCbu10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaCbu10)-> result_array();
				if ($data10){							
				$jasaCbuB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaCbuB10 = 0;
				}

				$jasaCbulux10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaCbulux10)-> result_array();
				if ($data10){							
				$jasaCbuluxA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaCbuluxA10 = 0;
				}

				$jasaCbulux10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaCbulux10)-> result_array();
				if ($data10){							
				$jasaCbuluxB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaCbuluxB10 = 0;
				}

				$jasaAlber10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaAlber10)-> result_array();
				if ($data10){							
				$jasaAlberA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaAlberA10 = 0;
				}

				$jasaAlber10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaAlber10)-> result_array();
				if ($data10){							
				$jasaAlberB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaAlberB10 = 0;
				}		
				
				$jasaTruckbus10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaTruckbus10)-> result_array();
				if ($data10){							
				$jasaTruckbusA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaTruckbusA10 = 0;
				}

				$jasaTruckbus10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaTruckbus10)-> result_array();
				if ($data10){							
				$jasaTruckbusB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaTruckbusB10 = 0;
				}

				$jasaCargo10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaCargo10)-> result_array();
				if ($data10){							
				$jasaCargoA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaCargoA10 = 0;
				}

				$jasaCargo10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaCargo10)-> result_array();
				if ($data10){							
				$jasaCargoB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaCargoB10 = 0;
				}

				$jasaMotor10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaMotor10)-> result_array();
				if ($data10){							
				$jasaMotorA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaMotorA10 = 0;
				}

				$jasaMotor10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data10 = $con->query($jasaMotor10)-> result_array();
				if ($data10){							
				$jasaMotorB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaMotorB10 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-11'){
				$bulan11 = 'November';
				$PERIODE = "'$PERIODE'";

				$data_cbutl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
			WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.'  
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_cbutl11)-> result_array();
				if ($data11){							
				$cbuTl11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cbuTl11 = 0;
				}

				$data_cbunontl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_cbunontl11)-> result_array();
				if ($data11){							
				$cbuNontl11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cbuNontl11 = 0;
				}

				$data_cbuluxurytl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_cbuluxurytl11)-> result_array();
				if ($data11){							
				$cbuLuxurytl11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cbuLuxurytl11 = 0;
				}

				$data_cbuluxuryntl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data11 = $con->query($data_cbuluxuryntl11)-> result_array();
				if ($data11){							
				$cbuLuxuryntl11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cbuLuxuryntl11 = 0;
				}

				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBustlA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBustlA11 = 0;
				}
				
				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBustlB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBustlB11 = 0;
				}

				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBustlC11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBustlC11 = 0;
				}
	
				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBustlD11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBustlD11 = 0;
				}

				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBustlE11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBustlE11 = 0;
				}
				
				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBusntlA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBusntlA11 = 0;
				}
				
				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBusntlB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBusntlB11 = 0;
				}

				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBusntlC11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBusntlC11 = 0;
				}
	
				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBusntlD11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBusntlD11 = 0;
				}

				$data_truckbusTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_truckbusTl11)-> result_array();
				if ($data11){							
				$truckBusntlE11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$truckBusntlE11 = 0;
				}
			
				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$albertlA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$albertlA11 = 0;
				}
				
				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$albertlB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$albertlB11 = 0;
				}
			
				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$albertlC11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$albertlC11 = 0;
				}

				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$albertlD11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$albertlD11 = 0;
				}

				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$albertlE11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$albertlE11 = 0;
				}
				
				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$albertlF11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$albertlF11 = 0;
				}

				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$alberntlA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$alberntlA11 = 0;
				}
				
				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$alberntlB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$alberntlB11 = 0;
				}
			
				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$alberntlC11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$alberntlC11 = 0;
				}

				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$alberntlD11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$alberntlD11 = 0;
				}

				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$alberntlE11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$alberntlE11 = 0;
				}

				$data_alberTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_alberTl11)-> result_array();
				if ($data11){							
				$alberntlF11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$alberntlF11 = 0;
				}				
				
				$data_motorTl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_motorTl11)-> result_array();
				if ($data11){							
				$motorTl11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$motorTl11 = 0;
				}

							
				$data_motorNtl11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_motorNtl11)-> result_array();
				if ($data11){							
				$motorNtl11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$motorNtl11 = 0;
				}

				$data_Cargo11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_Cargo11)-> result_array();
				if ($data11){							
				$cargoA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cargoA11 = 0;
				}

				$data_Cargo11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_Cargo11)-> result_array();
				if ($data11){							
				$cargoB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cargoB11 = 0;
				}

				$data_Cargo11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_Cargo11)-> result_array();
				if ($data11){							
				$cargoC11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cargoC11 = 0;
				}

				$data_Cargo11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($data_Cargo11)-> result_array();
				if ($data11){							
				$cargoD11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cargoD11 = 0;
				}

				$jasaCbu11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaCbu11)-> result_array();
				if ($data11){							
				$jasaCbuA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaCbuA11 = 0;
				}

				$jasaCbu11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaCbu11)-> result_array();
				if ($data11){							
				$jasaCbuB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaCbuB11 = 0;
				}

				$jasaCbulux11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaCbulux11)-> result_array();
				if ($data11){							
				$jasaCbuluxA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaCbuluxA11 = 0;
				}

				$jasaCbulux11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaCbulux11)-> result_array();
				if ($data11){							
				$jasaCbuluxB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaCbuluxB11 = 0;
				}

				$jasaAlber11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaAlber11)-> result_array();
				if ($data11){							
				$jasaAlberA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaAlberA11 = 0;
				}

				$jasaAlber11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaAlber11)-> result_array();
				if ($data11){							
				$jasaAlberB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaAlberB11 = 0;
				}		
				
				$jasaTruckbus11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaTruckbus11)-> result_array();
				if ($data11){							
				$jasaTruckbusA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaTruckbusA11 = 0;
				}

				$jasaTruckbus11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaTruckbus11)-> result_array();
				if ($data11){							
				$jasaTruckbusB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaTruckbusB11 = 0;
				}

				$jasaCargo11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaCargo11)-> result_array();
				if ($data11){							
				$jasaCargoA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaCargoA11 = 0;
				}

				$jasaCargo11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaCargo11)-> result_array();
				if ($data11){							
				$jasaCargoB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaCargoB11 = 0;
				}

				$jasaMotor11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaMotor11)-> result_array();
				if ($data11){							
				$jasaMotorA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaMotorA11 = 0;
				}

				$jasaMotor11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data11 = $con->query($jasaMotor11)-> result_array();
				if ($data11){							
				$jasaMotorB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaMotorB11 = 0;
				}
			}
		
			if ($PERIODE == ''.$YEAR.'-12'){
				$bulan12 = 'Desember';
				$PERIODE = "'$PERIODE'";

				$data_cbutl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
			WHERE "TERMINAL" ='.$terminalDom.' and  "KOMODITI" like '.$cbu_tl.'  
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_cbutl12)-> result_array();
				if ($data12){							
				$cbuTl12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cbuTl12 = 0;
				}

				$data_cbunontl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" like '.$cbu_nontl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_cbunontl12)-> result_array();
				if ($data12){							
				$cbuNontl12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cbuNontl12 = 0;
				}

				$data_cbuluxurytl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxurytl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_cbuluxurytl12)-> result_array();
				if ($data12){							
				$cbuLuxurytl12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cbuLuxurytl12 = 0;
				}

				$data_cbuluxuryntl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$cbu_luxuryntl.' 
				and "LAYANAN" ='.$domestik.' 
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data12 = $con->query($data_cbuluxuryntl12)-> result_array();
				if ($data12){							
				$cbuLuxuryntl12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cbuLuxuryntl12 = 0;
				}

				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBustlA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBustlA12 = 0;
				}
				
				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBustlB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBustlB12 = 0;
				}

				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBustlC12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBustlC12 = 0;
				}
	
				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBustlD12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBustlD12 = 0;
				}

				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBustl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBustlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBustlE12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBustlE12 = 0;
				}
				
				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBusntlA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBusntlA12 = 0;
				}
				
				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBusntlB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBusntlB12 = 0;
				}

				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBusntlC12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBusntlC12 = 0;
				}
	
				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBusntlD12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBusntlD12 = 0;
				}

				$data_truckbusTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$truckBusntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$truckBusntlE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_truckbusTl12)-> result_array();
				if ($data12){							
				$truckBusntlE12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$truckBusntlE12 = 0;
				}
			
				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$albertlA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$albertlA12 = 0;
				}
				
				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$albertlB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$albertlB12 = 0;
				}
			
				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$albertlC12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$albertlC12 = 0;
				}

				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$albertlD12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$albertlD12 = 0;
				}

				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$albertlE12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$albertlE12 = 0;
				}
				
				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBerattl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$alberF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$albertlF12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$albertlF12 = 0;
				}

				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$alberntlA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$alberntlA12 = 0;
				}
				
				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$alberntlB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$alberntlB12 = 0;
				}
			
				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$alberntlC12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$alberntlC12 = 0;
				}

				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and ("GOLONGAN" = '.$albernD.' 
				or "GOLONGAN" = '.$albernDD.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$alberntlD12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$alberntlD12 = 0;
				}

				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernE.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$alberntlE12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$alberntlE12 = 0;
				}

				$data_alberTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$alatBeratntl.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$albernF.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_alberTl12)-> result_array();
				if ($data12){							
				$alberntlF12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$alberntlF12 = 0;
				}				
				
				$data_motorTl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorTl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_motorTl12)-> result_array();
				if ($data12){							
				$motorTl12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$motorTl12 = 0;
				}

							
				$data_motorNtl12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$motorNtl.' 
				and "LAYANAN" ='.$domestik.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_motorNtl12)-> result_array();
				if ($data12){							
				$motorNtl12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$motorNtl12 = 0;
				}

				$data_Cargo12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_Cargo12)-> result_array();
				if ($data12){							
				$cargoA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cargoA12 = 0;
				}

				$data_Cargo12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_Cargo12)-> result_array();
				if ($data12){							
				$cargoB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cargoB12 = 0;
				}

				$data_Cargo12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_Cargo12)-> result_array();
				if ($data12){							
				$cargoC12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cargoC12 = 0;
				}

				$data_Cargo12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$cargoD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($data_Cargo12)-> result_array();
				if ($data12){							
				$cargoD12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cargoD12 = 0;
				}

				$jasaCbu12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaCbu12)-> result_array();
				if ($data12){							
				$jasaCbuA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaCbuA12 = 0;
				}

				$jasaCbu12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCbu.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaCbu12)-> result_array();
				if ($data12){							
				$jasaCbuB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaCbuB12 = 0;
				}

				$jasaCbulux12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaCbulux12)-> result_array();
				if ($data12){							
				$jasaCbuluxA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaCbuluxA12 = 0;
				}

				$jasaCbulux12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaLuxury.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaCbulux12)-> result_array();
				if ($data12){							
				$jasaCbuluxB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaCbuluxB12 = 0;
				}

				$jasaAlber12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaAlber12)-> result_array();
				if ($data12){							
				$jasaAlberA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaAlberA12 = 0;
				}

				$jasaAlber12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaAlber.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaAlber12)-> result_array();
				if ($data12){							
				$jasaAlberB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaAlberB12 = 0;
				}		
				
				$jasaTruckbus12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaTruckbus12)-> result_array();
				if ($data12){							
				$jasaTruckbusA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaTruckbusA12 = 0;
				}

				$jasaTruckbus12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaTruckbus.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaTruckbus12)-> result_array();
				if ($data12){							
				$jasaTruckbusB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaTruckbusB12 = 0;
				}

				$jasaCargo12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaCargo12)-> result_array();
				if ($data12){							
				$jasaCargoA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaCargoA12 = 0;
				}

				$jasaCargo12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaCargo.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaCargo12)-> result_array();
				if ($data12){							
				$jasaCargoB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaCargoB12 = 0;
				}

				$jasaMotor12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa2.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaMotor12)-> result_array();
				if ($data12){							
				$jasaMotorA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaMotorA12 = 0;
				}

				$jasaMotor12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" ='.$terminalDom.' and "KOMODITI" = '.$jasaMotor.' 
				and "LAYANAN" ='.$domestik.' and "GOLONGAN" = '.$masa3.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'';		
				
				$data12 = $con->query($jasaMotor12)-> result_array();
				if ($data12){							
				$jasaMotorB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaMotorB12 = 0;
				}
			}

			$semester = "'Per Semester'";
			$triwulan = "'Per Triwulan'";
			$tahun = "'Per Tahun'";

			$cargoA = "'NORMAL TL'";
			$cargoB = "'MENGGANGGU TL'";
			$cargoC = "'BERBAHAYA TL'";
			$cargoD = "'BERBAHAYA NON LABEL TL'";

			$terminalDom = "'DOMESTIK'";
			$terminal = $terminalDom;			
			$YEAR = "'$YEAR'";
			$cbuLux = "'CBU LUXURY'";
			$truckbtl = "'TRUCK / BUS TL'";
			$truckbntl = "'TRUCK / BUS NON TL'";
			$albertl = "'ALAT BERAT TL'";
			$alberntl = "'ALAT BERAT NON TL'";
			$motortl = "'SEPEDA MOTOR TL'";
			$motorntl = "'SEPEDA MOTOR NON TL'";
			$gol1 = "'< 28'";
			$gol2 = "'> 28 - 33'";
			$gol3 = "'> 33 - 40'";
			$gol4 = "'> 40 - 50'";
			$gol5 = "'> 50'";
			$gol6 = "'> 50 - 120'";
			$gol7 = "'> 120'";
	
			$masaI = "'PENUMPUKAN MASA I'";
			$masaII = "'MASA II (HARI KE-6 S/D 7)'";
			$masaIII = "'MASA III (HARI KE-8 DST)'";
			$cbu = "'CBU'";
			$cbuLuxury = "'CBU LUXURY'";	
			$alberTruck = "'ALAT BERAT & TRUCK'";
			$oppt = "'OPP/OPT'";
			$generalCargo = "'GENERAL CARGO'";
			$motor = "'SEPEDA MOTOR'";
		
			$jCbu = "'JASA PENUMPUKAN CBU'";
			$jCbulux = "'JASA PENUMPUKAN CBU LUXURY'";
			$jAlber = "'JASA PENUMPUKAN ALAT BERAT'";
			$jTruckbus = "'JASA PENUMPUKAN TRUCK/BUS'";
			$jCargo = "'JASA PENUMPUKAN GENERAL CARGO'";
			$jMotor = "'JASA PENUMPUKAN SEPEDA MOTOR'";
			$cbu_tl = "'CBU TL'";
			$cbu_nontl = "'CBU NON TL'";

			if ($tipe == 'PER SEMESTER'){
				$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbu_tl.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifCbu)-> result_array();
				if ($data1){							
					$tarifAcbutl = $data1[0]['TARIF_1'];
					$tarifBcbutl = $data1[0]['TARIF_2'];
				
				} else if (empty($data1))  {		
					$tarifAcbutl = 0;
					$tarifBcbutl = 0;
			
				}
	
				$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbu_nontl.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifCbu)-> result_array();
				if ($data1){							
					$tarifAcbunontl = $data1[0]['TARIF_1'];
					$tarifBcbunontl = $data1[0]['TARIF_2'];

				} else if (empty($data1))  {		
					$tarifAcbunontl = 0;
					$tarifBcbunontl = 0;		
				}
	
				$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbu_luxurytl.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifCbu)-> result_array();
				if ($data1){							
					$tarifAcbuluxurytl = $data1[0]['TARIF_1'];
					$tarifBcbuluxurytl = $data1[0]['TARIF_2'];
				
				} else if (empty($data1))  {		
					$tarifAcbuluxurytl = 0;
					$tarifBcbuluxurytl = 0;
		
				}
	
				$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbu_luxuryntl.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifCbu)-> result_array();
				if ($data1){							
					$tarifAcbuluxuryntl = $data1[0]['TARIF_1'];
					$tarifBcbuluxuryntl = $data1[0]['TARIF_2'];

				} else if (empty($data1))  {		
					$tarifAcbuluxuryntl = 0;
					$tarifBcbuluxuryntl = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol1.' 
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAtruck1  = $data1[0]['TARIF_1'];
					$tarifBtruck1  = $data1[0]['TARIF_2'];
				
				} else if (empty($data1))  {		
					$tarifAtruck1 = 0;
					$tarifBtruck1 = 0;
	
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol2.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAtruck2  = $data1[0]['TARIF_1'];
					$tarifBtruck2  = $data1[0]['TARIF_2'];

				} else if (empty($data1))  {		
					$tarifAtruck2 = 0;
					$tarifBtruck2 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol3.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAtruck3  = $data1[0]['TARIF_1'];
					$tarifBtruck3  = $data1[0]['TARIF_2'];
			
				} else if (empty($data1))  {		
					$tarifAtruck3 = 0;
					$tarifBtruck3 = 0;		
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol4.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAtruck4  = $data1[0]['TARIF_1'];
					$tarifBtruck4  = $data1[0]['TARIF_2'];

				} else if (empty($data1))  {		
					$tarifAtruck4 = 0;
					$tarifBtruck4 = 0;
			
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol5.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAtruck5  = $data1[0]['TARIF_1'];
					$tarifBtruck5  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAtruck5 = 0;
					$tarifBtruck5 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol1.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAtruckn1  = $data1[0]['TARIF_1'];
					$tarifBtruckn1  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAtruckn1 = 0;
					$tarifBtruckn1 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol2.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAtruckn2  = $data1[0]['TARIF_1'];
					$tarifBtruckn2  = $data1[0]['TARIF_2'];
	
				} else if (empty($data1))  {		
					$tarifAtruckn2 = 0;
					$tarifBtruckn2 = 0;
				}
			
				$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol3.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifTruck)-> result_array();
				if ($data1){							
					$tarifAtruckn3  = $data1[0]['TARIF_1'];
					$tarifBtruckn3  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAtruckn3 = 0;
					$tarifBtruckn3 = 0;
				}
				
				$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol4.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifTruck)-> result_array();
				if ($data1){							
					$tarifAtruckn4  = $data1[0]['TARIF_1'];
					$tarifBtruckn4  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAtruckn4 = 0;
					$tarifBtruckn4 = 0;
				}
	
				$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol4.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifTruck)-> result_array();
				if ($data1){							
					$tarifAtruckn5  = $data1[0]['TARIF_1'];
					$tarifBtruckn5  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAtruckn5 = 0;
					$tarifBtruckn5 = 0;
				}
	
				$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol5.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifTruck)-> result_array();
				if ($data1){							
					$tarifAtruckn5  = $data1[0]['TARIF_1'];
					$tarifBtruckn5  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAtruckn5 = 0;
					$tarifBtruckn5 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol1.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalber1  = $data1[0]['TARIF_1'];
					$tarifBalber1  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalber1 = 0;
					$tarifBalber1 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol2.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalber2  = $data1[0]['TARIF_1'];
					$tarifBalber2  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalber2 = 0;
					$tarifBalber2 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol3.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalber3  = $data1[0]['TARIF_1'];
					$tarifBalber3  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalber3 = 0;
					$tarifBalber3 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol4.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalber4  = $data1[0]['TARIF_1'];
					$tarifBalber4  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalber4 = 0;
					$tarifBalber4 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol6.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalber5  = $data1[0]['TARIF_1'];
					$tarifBalber5  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalber5 = 0;
					$tarifBalber5 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol7.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalber6  = $data1[0]['TARIF_1'];
					$tarifBalber6  = $data1[0]['TARIF_2'];
	
				} else if (empty($data1))  {		
					$tarifAalber6 = 0;
					$tarifBalber6 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol1.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalbern1  = $data1[0]['TARIF_1'];
					$tarifBalbern1  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalbern1 = 0;
					$tarifBalbern1 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol2.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalbern2  = $data1[0]['TARIF_1'];
					$tarifBalbern2  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalbern2 = 0;
					$tarifBalbern2 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol3.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalbern3  = $data1[0]['TARIF_1'];
					$tarifBalbern3  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalbern3 = 0;
					$tarifBalbern3 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol4.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalbern4  = $data1[0]['TARIF_1'];
					$tarifBalbern4  = $data1[0]['TARIF_2'];
			
				} else if (empty($data1))  {		
					$tarifAalbern4 = 0;
					$tarifBalbern4 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol6.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalbern5  = $data1[0]['TARIF_1'];
					$tarifBalbern5  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalbern5 = 0;
					$tarifBalbern5 = 0;
				}
	
				$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol7.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifAlber)-> result_array();
				if ($data1){							
					$tarifAalber6  = $data1[0]['TARIF_1'];
					$tarifBalber6  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAalber6 = 0;
					$tarifBalber6 = 0;
				}
				
				
				$tarifMotor = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$motortl.' 
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifMotor)-> result_array();
				if ($data1){							
					$tarifAmotortl  = $data1[0]['TARIF_1'];
					$tarifBmotortl  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAmotortl = 0;
					$tarifBmotortl = 0;
				}
	
				$tarifMotor = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$motorntl.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifMotor)-> result_array();
				if ($data1){							
					$tarifAmotorntl  = $data1[0]['TARIF_1'];
					$tarifBmotorntl  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAmotorntl = 0;
					$tarifBmotorntl = 0;
				}
	
				$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoA.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifCargo)-> result_array();
				if ($data1){							
					$tarifAcargonormal  = $data1[0]['TARIF_1'];
					$tarifBcargonormal  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAcargonormal = 0;
					$tarifBcargonormal = 0;
				}
	
				$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoB.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifCargo)-> result_array();
				if ($data1){							
					$tarifAcargogB  = $data1[0]['TARIF_1'];
					$tarifBcargogB  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAcargogB = 0;
					$tarifBcargogB = 0;
				}
	
				$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoC.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifCargo)-> result_array();
				if ($data1){							
					$tarifAcargobC  = $data1[0]['TARIF_1'];
					$tarifBcargobC  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAcargobC = 0;
					$tarifBcargobC = 0;
				}
	
				$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoD.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifCargo)-> result_array();
				if ($data1){							
					$tarifAcargolD  = $data1[0]['TARIF_1'];
					$tarifBcargolD  = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAcargolD = 0;
					$tarifBcargolD = 0;
				}
	
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jCbu.' and "GOLONGAN" = '.$masaII.' 
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjcbu2 = $data1[0]['TARIF_1'];
					$tarifBjcbu2 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjcbu2 = 0;
					$tarifBjcbu2 = 0;
				}
	
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jCbu.' and "GOLONGAN" = '.$masaIII.' 
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjcbu3 = $data1[0]['TARIF_1'];
					$tarifBjcbu3 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjcbu3 = 0;
					$tarifBjcbu3 = 0;
				}
	
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jCbulux.' and "GOLONGAN" = '.$masaII.' 
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjcbulux2 = $data1[0]['TARIF_1'];
					$tarifBjcbulux2 = $data1[0]['TARIF_2'];			
				} else if (empty($data1))  {		
					$tarifAjcbulux2 = 0;
					$tarifBjcbulux2 = 0;
				}
	
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jCbulux.' and "GOLONGAN" = '.$masaIII.' 
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjcbulux3 = $data1[0]['TARIF_1'];
					$tarifBjcbulux3 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjcbulux3 = 0;
					$tarifBjcbulux3 = 0;
				}
	
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaII.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjalber2 = $data1[0]['TARIF_1'];
					$tarifBjalber2 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjalber2 = 0;
					$tarifBjalber2 = 0;
				}
	
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaIII.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjalber3 = $data1[0]['TARIF_1'];
					$tarifBjalber3 = $data1[0]['TARIF_2'];			
				} else if (empty($data1))  {		
					$tarifAjalber3 = 0;
					$tarifBjalber3 = 0;
				}
	
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaII.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjtruckbus2 = $data1[0]['TARIF_1'];
					$tarifBjtruckbus2 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjtruckbus2 = 0;
					$tarifBjtruckbus2 = 0;
				}
				
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaIII.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjtruckbus3 = $data1[0]['TARIF_1'];
					$tarifBjtruckbus3 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjtruckbus3 = 0;
					$tarifBjtruckbus3 = 0;
				}
	
				
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jCargo.' and "GOLONGAN" = '.$masaII.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjcargo2 = $data1[0]['TARIF_1'];
					$tarifBjcargo2 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjcargo2 = 0;
					$tarifBjcargo2 = 0;
				}
				
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jCargo.' and "GOLONGAN" = '.$masaIII.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjcargo3 = $data1[0]['TARIF_1'];
					$tarifBjcargo3 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjcargo3 = 0;
					$tarifBjcargo3 = 0;
				}
	
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jMotor.' and "GOLONGAN" = '.$masaII.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjmotor2 = $data1[0]['TARIF_1'];
					$tarifBjmotor2 = $data1[0]['TARIF_2'];
		
				} else if (empty($data1))  {		
					$tarifAjmotor2 = 0;
					$tarifBjmotor2 = 0;
			
				}
				
				$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
				where "TYPE" = '.$semester.' and "KOMODITI" = '.$jMotor.' and "GOLONGAN" = '.$masaIII.'
				and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
				$data1 = $con->query($tarifJasa)-> result_array();
				if ($data1){							
					$tarifAjmotor3 = $data1[0]['TARIF_1'];
					$tarifBjmotor3 = $data1[0]['TARIF_2'];
				} else if (empty($data1))  {		
					$tarifAjmotor3 = 0;
					$tarifBjmotor3 = 0;
				}
	      }

		  if ($tipe == 'PER TRIWULAN'){
			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbu_tl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcbutl = $data1[0]['TARIF_1'];
				$tarifBcbutl = $data1[0]['TARIF_2'];
			
			} else if (empty($data1))  {		
				$tarifAcbutl = 0;
				$tarifBcbutl = 0;
		
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbu_nontl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcbunontl = $data1[0]['TARIF_1'];
				$tarifBcbunontl = $data1[0]['TARIF_2'];

			} else if (empty($data1))  {		
				$tarifAcbunontl = 0;
				$tarifBcbunontl = 0;
	
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbu_luxurytl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcbuluxurytl = $data1[0]['TARIF_1'];
				$tarifBcbuluxurytl = $data1[0]['TARIF_2'];
			
			} else if (empty($data1))  {		
				$tarifAcbuluxurytl = 0;
				$tarifBcbuluxurytl = 0;
	
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbu_luxuryntl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcbuluxuryntl = $data1[0]['TARIF_1'];
				$tarifBcbuluxuryntl = $data1[0]['TARIF_2'];

			} else if (empty($data1))  {		
				$tarifAcbuluxuryntl = 0;
				$tarifBcbuluxuryntl = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol1.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck1  = $data1[0]['TARIF_1'];
				$tarifBtruck1  = $data1[0]['TARIF_2'];
			
			} else if (empty($data1))  {		
				$tarifAtruck1 = 0;
				$tarifBtruck1 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck2  = $data1[0]['TARIF_1'];
				$tarifBtruck2  = $data1[0]['TARIF_2'];

			} else if (empty($data1))  {		
				$tarifAtruck2 = 0;
				$tarifBtruck2 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck3  = $data1[0]['TARIF_1'];
				$tarifBtruck3  = $data1[0]['TARIF_2'];
		
			} else if (empty($data1))  {		
				$tarifAtruck3 = 0;
				$tarifBtruck3 = 0;		
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck4  = $data1[0]['TARIF_1'];
				$tarifBtruck4  = $data1[0]['TARIF_2'];

			} else if (empty($data1))  {		
				$tarifAtruck4 = 0;
				$tarifBtruck4 = 0;
		
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck5  = $data1[0]['TARIF_1'];
				$tarifBtruck5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAtruck5 = 0;
				$tarifBtruck5 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruckn1  = $data1[0]['TARIF_1'];
				$tarifBtruckn1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAtruckn1 = 0;
				$tarifBtruckn1 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruckn2  = $data1[0]['TARIF_1'];
				$tarifBtruckn2  = $data1[0]['TARIF_2'];

			} else if (empty($data1))  {		
				$tarifAtruckn2 = 0;
				$tarifBtruckn2 = 0;
			}
		
			$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifTruck)-> result_array();
			if ($data1){							
				$tarifAtruckn3  = $data1[0]['TARIF_1'];
				$tarifBtruckn3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAtruckn3 = 0;
				$tarifBtruckn3 = 0;
			}
			
			$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifTruck)-> result_array();
			if ($data1){							
				$tarifAtruckn4  = $data1[0]['TARIF_1'];
				$tarifBtruckn4  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAtruckn4 = 0;
				$tarifBtruckn4 = 0;
			}

			$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifTruck)-> result_array();
			if ($data1){							
				$tarifAtruckn5  = $data1[0]['TARIF_1'];
				$tarifBtruckn5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAtruckn5 = 0;
				$tarifBtruckn5 = 0;
			}

			$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifTruck)-> result_array();
			if ($data1){							
				$tarifAtruckn5  = $data1[0]['TARIF_1'];
				$tarifBtruckn5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAtruckn5 = 0;
				$tarifBtruckn5 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber1  = $data1[0]['TARIF_1'];
				$tarifBalber1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber1 = 0;
				$tarifBalber1 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber2  = $data1[0]['TARIF_1'];
				$tarifBalber2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber2 = 0;
				$tarifBalber2 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber3  = $data1[0]['TARIF_1'];
				$tarifBalber3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber3 = 0;
				$tarifBalber3 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber4  = $data1[0]['TARIF_1'];
				$tarifBalber4  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber4 = 0;
				$tarifBalber4 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber5  = $data1[0]['TARIF_1'];
				$tarifBalber5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber5 = 0;
				$tarifBalber5 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber6  = $data1[0]['TARIF_1'];
				$tarifBalber6  = $data1[0]['TARIF_2'];

			} else if (empty($data1))  {		
				$tarifAalber6 = 0;
				$tarifBalber6 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern1  = $data1[0]['TARIF_1'];
				$tarifBalbern1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalbern1 = 0;
				$tarifBalbern1 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern2  = $data1[0]['TARIF_1'];
				$tarifBalbern2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalbern2 = 0;
				$tarifBalbern2 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern3  = $data1[0]['TARIF_1'];
				$tarifBalbern3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalbern3 = 0;
				$tarifBalbern3 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern4  = $data1[0]['TARIF_1'];
				$tarifBalbern4  = $data1[0]['TARIF_2'];
		
			} else if (empty($data1))  {		
				$tarifAalbern4 = 0;
				$tarifBalbern4 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern5  = $data1[0]['TARIF_1'];
				$tarifBalbern5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalbern5 = 0;
				$tarifBalbern5 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber6  = $data1[0]['TARIF_1'];
				$tarifBalber6  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber6 = 0;
				$tarifBalber6 = 0;
			}
			
			
			$tarifMotor = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$motortl.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMotor)-> result_array();
			if ($data1){							
				$tarifAmotortl  = $data1[0]['TARIF_1'];
				$tarifBmotortl  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmotortl = 0;
				$tarifBmotortl = 0;
			}

			$tarifMotor = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$motorntl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMotor)-> result_array();
			if ($data1){							
				$tarifAmotorntl  = $data1[0]['TARIF_1'];
				$tarifBmotorntl  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmotorntl = 0;
				$tarifBmotorntl = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoA.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargonormal  = $data1[0]['TARIF_1'];
				$tarifBcargonormal  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargonormal = 0;
				$tarifBcargonormal = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoB.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargogB  = $data1[0]['TARIF_1'];
				$tarifBcargogB  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargogB = 0;
				$tarifBcargogB = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoC.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargobC  = $data1[0]['TARIF_1'];
				$tarifBcargobC  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargobC = 0;
				$tarifBcargobC = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoD.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargolD  = $data1[0]['TARIF_1'];
				$tarifBcargolD  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargolD = 0;
				$tarifBcargolD = 0;
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jCbu.' and "GOLONGAN" = '.$masaII.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcbu2 = $data1[0]['TARIF_1'];
				$tarifBjcbu2 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjcbu2 = 0;
				$tarifBjcbu2 = 0;
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jCbu.' and "GOLONGAN" = '.$masaIII.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcbu3 = $data1[0]['TARIF_1'];
				$tarifBjcbu3 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjcbu3 = 0;
				$tarifBjcbu3 = 0;
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jCbulux.' and "GOLONGAN" = '.$masaII.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcbulux2 = $data1[0]['TARIF_1'];
				$tarifBjcbulux2 = $data1[0]['TARIF_2'];			
			} else if (empty($data1))  {		
				$tarifAjcbulux2 = 0;
				$tarifBjcbulux2 = 0;
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jCbulux.' and "GOLONGAN" = '.$masaIII.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcbulux3 = $data1[0]['TARIF_1'];
				$tarifBjcbulux3 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjcbulux3 = 0;
				$tarifBjcbulux3 = 0;
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjalber2 = $data1[0]['TARIF_1'];
				$tarifBjalber2 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjalber2 = 0;
				$tarifBjalber2 = 0;
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjalber3 = $data1[0]['TARIF_1'];
				$tarifBjalber3 = $data1[0]['TARIF_2'];			
			} else if (empty($data1))  {		
				$tarifAjalber3 = 0;
				$tarifBjalber3 = 0;
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjtruckbus2 = $data1[0]['TARIF_1'];
				$tarifBjtruckbus2 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjtruckbus2 = 0;
				$tarifBjtruckbus2 = 0;
			}
			
			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjtruckbus3 = $data1[0]['TARIF_1'];
				$tarifBjtruckbus3 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjtruckbus3 = 0;
				$tarifBjtruckbus3 = 0;
			}

			
			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jCargo.' and "GOLONGAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcargo2 = $data1[0]['TARIF_1'];
				$tarifBjcargo2 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjcargo2 = 0;
				$tarifBjcargo2 = 0;
			}
			
			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jCargo.' and "GOLONGAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcargo3 = $data1[0]['TARIF_1'];
				$tarifBjcargo3 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjcargo3 = 0;
				$tarifBjcargo3 = 0;
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jMotor.' and "GOLONGAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjmotor2 = $data1[0]['TARIF_1'];
				$tarifBjmotor2 = $data1[0]['TARIF_2'];
	
			} else if (empty($data1))  {		
				$tarifAjmotor2 = 0;
				$tarifBjmotor2 = 0;
		
			}
			
			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$jMotor.' and "GOLONGAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjmotor3 = $data1[0]['TARIF_1'];
				$tarifBjmotor3 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAjmotor3 = 0;
				$tarifBjmotor3 = 0;
			}
	      }

		  if ($tipe == 'PER TAHUN'){
			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbu_tl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcbutl = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAcbutl = 0;
		
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbu_nontl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcbunontl = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAcbunontl = 0;
	
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbu_luxurytl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcbuluxurytl = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAcbuluxurytl = 0;
	
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbu_luxuryntl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcbuluxuryntl = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAcbuluxuryntl = 0;
	
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol1.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck1  = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAtruck1 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck2  = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAtruck2 = 0;
			
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck3  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAtruck3 = 0;
	
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck4  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAtruck4 = 0;
		
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbtl.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruck5  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAtruck5 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruckn1  = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAtruckn1 = 0;
			
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAtruckn2  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAtruckn2 = 0;

			}
		
			$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifTruck)-> result_array();
			if ($data1){							
				$tarifAtruckn3  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAtruckn3 = 0;

			}
			
			$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifTruck)-> result_array();
			if ($data1){							
				$tarifAtruckn4  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAtruckn4 = 0;
		
			}

			$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifTruck)-> result_array();
			if ($data1){							
				$tarifAtruckn5  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAtruckn5 = 0;

			}

			$tarifTruck = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$truckbntl.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifTruck)-> result_array();
			if ($data1){							
				$tarifAtruckn5  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAtruckn5 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber1  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalber1 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber2  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalber2 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber3  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalber3 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber4  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalber4 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber5  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalber5 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$albertl.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber6  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalber6 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern1  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalbern1 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern2  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalbern2 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern3  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAalbern3 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern4  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAalbern4 = 0;
		
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern5  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAalbern5 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberntl.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalbern6  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalbern6 = 0;
		
			}
			
			
			$tarifMotor = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$motortl.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMotor)-> result_array();
			if ($data1){							
				$tarifAmotortl  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAmotortl = 0;
	
			}

			$tarifMotor = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$motorntl.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMotor)-> result_array();
			if ($data1){							
				$tarifAmotorntl  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAmotorntl = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoA.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargonormal  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargonormal = 0;
		
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoB.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargogB  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAcargogB = 0;
	
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoC.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargobC  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargobC = 0;
			
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "GOLONGAN" = '.$cargoD.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargolD  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAcargolD = 0;
		
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jCbu.' and "GOLONGAN" = '.$masaII.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcbu2 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAjcbu2 = 0;
	
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jCbu.' and "GOLONGAN" = '.$masaIII.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcbu3 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAjcbu3 = 0;
		
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jCbulux.' and "GOLONGAN" = '.$masaII.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcbulux2 = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAjcbulux2 = 0;
			      
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jCbulux.' and "GOLONGAN" = '.$masaIII.' 
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcbulux3 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAjcbulux3 = 0;
	
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjalber2 = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAjalber2 = 0;

			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjalber3 = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAjalber3 = 0;

			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjtruckbus2 = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAjtruckbus2 = 0;
		
			}
			
			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jTruckbus.' and "GOLONGAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjtruckbus3 = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAjtruckbus3 = 0;
		
			}

			
			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jCargo.' and "GOLONGAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcargo2 = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAjcargo2 = 0;

			}
			
			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jCargo.' and "GOLONGAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjcargo3 = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAjcargo3 = 0;
		
			}

			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jMotor.' and "GOLONGAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjmotor2 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAjmotor2 = 0;
		
			}
			
			$tarifJasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$jMotor.' and "GOLONGAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalDom.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifJasa)-> result_array();
			if ($data1){							
				$tarifAjmotor3 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAjmotor3 = 0;
			
			}
	      }
		}


			if (empty($bulan1)){
				$excel->setActiveSheetIndex(0)->setCellValue('E4',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('E5',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('E6',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('E7',  '');				

			} else if ($bulan1 == 'Januari'){
				$excel->setActiveSheetIndex(0)->setCellValue('E4',  $cbuTl1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E5',  $cbuNontl1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E6', '=SUM(E4:E5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('E9', $truckBustlA1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E10', $truckBustlB1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E11', $truckBustlC1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E12', $truckBustlD1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E13', $truckBustlE1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E14', '=SUM(E9:E13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('E17', $truckBusntlA1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E18', $truckBusntlB1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E19', $truckBusntlC1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E20', $truckBusntlD1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E21', $truckBusntlE1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E22', '=SUM(E17:E21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('E25', $albertlA1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E26', $albertlB1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E27', $albertlC1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E28', $albertlD1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E29', $albertlE1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E30', $albertlF1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E31', '=SUM(E25:E30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('E33', $alberntlA1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E34', $alberntlB1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E35', $alberntlC1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E36', $alberntlD1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E37', $alberntlE1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E38', $alberntlF1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E39', '=SUM(E33:E38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('E41', $motorTl1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E42', $motorNtl1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E45', $cargoA1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E46', $cargoB1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E47', $cargoC1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E48', $cargoD1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E52', $jasaCbuA1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E53', $jasaCbuB1?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('E55', $jasaAlberA1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E56', $jasaAlberB1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E58', $jasaTruckbusA1?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('E59', $jasaTruckbusB1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E61', $jasaCargoA1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E62', $jasaCargoB1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E64', $jasaMotorA1?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('E65', $jasaMotorB1?:'0'); 		

			} 

			if (empty($bulan2)){
				$excel->setActiveSheetIndex(0)->setCellValue('F4',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('F5',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('F6',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('F7',  ''); 
				$excel->setActiveSheetIndex(0)->setCellValue('F8',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('F9',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('F10', '');

			} else if ($bulan2 == 'Februari'){
				$excel->setActiveSheetIndex(0)->setCellValue('F4',  $cbuTl2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F5',  $cbuNontl2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F6', '=SUM(F4:F5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('F9', $truckBustlA2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F10', $truckBustlB2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F11', $truckBustlC2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F12', $truckBustlD2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F13', $truckBustlE2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F14', '=SUM(F9:F13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('F17', $truckBusntlA2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F18', $truckBusntlB2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F19', $truckBusntlC2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F20', $truckBusntlD2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F21', $truckBusntlE2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F22', '=SUM(F17:F21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('F25', $albertlA2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F26', $albertlB2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F27', $albertlC2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F28', $albertlD2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F29', $albertlE2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F30', $albertlF2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F31', '=SUM(F25:F30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('F33', $alberntlA2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F34', $alberntlB2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F35', $alberntlC2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F36', $alberntlD2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F37', $alberntlE2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F38', $alberntlF2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F39', '=SUM(F33:F38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('F41', $motorTl2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F42', $motorNtl2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F45', $cargoA2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F46', $cargoB2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F47', $cargoC2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F48', $cargoD2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F52', $jasaCbuA2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F53', $jasaCbuB2?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('F55', $jasaAlberA2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F56', $jasaAlberB2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F58', $jasaTruckbusA2?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('F59', $jasaTruckbusB2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F61', $jasaCargoA2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F62', $jasaCargoB2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F64', $jasaMotorA2?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('F65', $jasaMotorB2?:'0'); 	
				
			}
			
			if (empty($bulan3)){
				$excel->setActiveSheetIndex(0)->setCellValue('G4',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('G5',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('G6',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('G7',  ''); 
				$excel->setActiveSheetIndex(0)->setCellValue('G8',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('G9',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('G10', '');
				
			} else if ($bulan3 == 'Maret'){
				$excel->setActiveSheetIndex(0)->setCellValue('G4',  $cbuTl3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G5',  $cbuNontl3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G6', '=SUM(G4:G5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('G9', $truckBustlA3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G10', $truckBustlB3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G11', $truckBustlC3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G12', $truckBustlD3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G13', $truckBustlE3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G14', '=SUM(G9:G13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('G17', $truckBusntlA3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G18', $truckBusntlB3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G19', $truckBusntlC3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G20', $truckBusntlD3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G21', $truckBusntlE3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G22', '=SUM(G17:G21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('G25', $albertlA3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G26', $albertlB3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G27', $albertlC3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G28', $albertlD3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G29', $albertlE3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G30', $albertlF3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G31', '=SUM(G25:G30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('G33', $alberntlA3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G34', $alberntlB3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G35', $alberntlC3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G36', $alberntlD3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G37', $alberntlE3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G38', $alberntlF3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G39', '=SUM(G33:G38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('G41', $motorTl3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G42', $motorNtl3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G45', $cargoA3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G46', $cargoB3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G47', $cargoC3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G48', $cargoD3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G52', $jasaCbuA3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G53', $jasaCbuB3?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('G55', $jasaAlberA3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G56', $jasaAlberB3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G58', $jasaTruckbusA3?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('G59', $jasaTruckbusB3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G61', $jasaCargoA3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G62', $jasaCargoB3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G64', $jasaMotorA3?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('G65', $jasaMotorB3?:'0'); 	
		
			}
			if (empty($bulan4)){
				$excel->setActiveSheetIndex(0)->setCellValue('H4',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('H5',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('H6',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('H7',  ''); 
				$excel->setActiveSheetIndex(0)->setCellValue('H8',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('H9',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('H10', '');
			
			} else if ($bulan4 == 'April'){
				$excel->setActiveSheetIndex(0)->setCellValue('H4',  $cbuTl4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H5',  $cbuNontl4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H6', '=SUM(H4:H5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('H9', $truckBustlA4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H10', $truckBustlB4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H11', $truckBustlC4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H12', $truckBustlD4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H13', $truckBustlE4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H14', '=SUM(H9:H13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('H17', $truckBusntlA4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H18', $truckBusntlB4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H19', $truckBusntlC4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H20', $truckBusntlD4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H21', $truckBusntlE4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H22', '=SUM(H17:H21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('H25', $albertlA4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H26', $albertlB4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H27', $albertlC4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H28', $albertlD4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H29', $albertlE4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H30', $albertlF4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H31', '=SUM(H25:H30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('H33', $alberntlA4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H34', $alberntlB4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H35', $alberntlC4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H36', $alberntlD4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H37', $alberntlE4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H38', $alberntlF4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H39', '=SUM(H33:H38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('H41', $motorTl4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H42', $motorNtl4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H45', $cargoA4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H46', $cargoB4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H47', $cargoC4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H48', $cargoD4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H52', $jasaCbuA4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H53', $jasaCbuB4?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('H55', $jasaAlberA4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H56', $jasaAlberB4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H58', $jasaTruckbusA4?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('H59', $jasaTruckbusB4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H61', $jasaCargoA4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H62', $jasaCargoB4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H64', $jasaMotorA4?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('H65', $jasaMotorB4?:'0'); 	
	 	
			}
			if (empty($bulan5)){
				$excel->setActiveSheetIndex(0)->setCellValue('I4',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I5',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I6',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I7',  ''); 
				$excel->setActiveSheetIndex(0)->setCellValue('I8',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I9',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I10', '');
			
			} else if ($bulan5 == 'Mei'){
				$excel->setActiveSheetIndex(0)->setCellValue('I4',  $cbuTl5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I5',  $cbuNontl5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I6', '=SUM(I4:I5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('I9', $truckBustlA5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I10', $truckBustlB5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I11', $truckBustlC5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I12', $truckBustlD5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I13', $truckBustlE5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I14', '=SUM(I9:I13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('I17', $truckBusntlA5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I18', $truckBusntlB5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I19', $truckBusntlC5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I20', $truckBusntlD5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I21', $truckBusntlE5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I22', '=SUM(I17:I21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('I25', $albertlA5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I26', $albertlB5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I27', $albertlC5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I28', $albertlD5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I29', $albertlE5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I30', $albertlF5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I31', '=SUM(I25:I30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('I33', $alberntlA5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I34', $alberntlB5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I35', $alberntlC5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I36', $alberntlD5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I37', $alberntlE5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I38', $alberntlF5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I39', '=SUM(I33:I38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('I41', $motorTl5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I42', $motorNtl5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I45', $cargoA5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I46', $cargoB5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I47', $cargoC5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I48', $cargoD5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I52', $jasaCbuA5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I53', $jasaCbuB5?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('I55', $jasaAlberA5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I56', $jasaAlberB5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I58', $jasaTruckbusA5?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('I59', $jasaTruckbusB5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I61', $jasaCargoA5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I62', $jasaCargoB5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I64', $jasaMotorA5?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('I65', $jasaMotorB5?:'0'); 	

			}
		
			if (empty($bulan6)){
				$excel->setActiveSheetIndex(0)->setCellValue('I4',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I5',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I6',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I7',  ''); 
				$excel->setActiveSheetIndex(0)->setCellValue('I8',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I9',  '');
				$excel->setActiveSheetIndex(0)->setCellValue('I10', '');
			
			} else if ($bulan6 == 'Juni'){
				$excel->setActiveSheetIndex(0)->setCellValue('J4',  $cbuTl6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J5',  $cbuNontl6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J6', '=SUM(J4:J5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('J9', $truckBustlA6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J10', $truckBustlB6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J11', $truckBustlC6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J12', $truckBustlD6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J13', $truckBustlE6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J14', '=SUM(J9:J13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('J17', $truckBusntlA6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J18', $truckBusntlB6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J19', $truckBusntlC6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J20', $truckBusntlD6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J21', $truckBusntlE6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J22', '=SUM(J17:J21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('J25', $albertlA6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J26', $albertlB6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J27', $albertlC6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J28', $albertlD6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J29', $albertlE6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J30', $albertlF6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J31', '=SUM(J25:J30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('J33', $alberntlA6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J34', $alberntlB6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J35', $alberntlC6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J36', $alberntlD6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J37', $alberntlE6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J38', $alberntlF6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J39', '=SUM(J33:J38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('J41', $motorTl6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J42', $motorNtl6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J45', $cargoA6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J46', $cargoB6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J47', $cargoC6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J48', $cargoD6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J52', $jasaCbuA6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J53', $jasaCbuB6?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('J55', $jasaAlberA6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J56', $jasaAlberB6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J58', $jasaTruckbusA6?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('J59', $jasaTruckbusB6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J61', $jasaCargoA6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J62', $jasaCargoB6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J64', $jasaMotorA6?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('J65', $jasaMotorB6?:'0'); 	
			} 
			if (empty($bulan7)){
				$excel->setActiveSheetIndex(0)->setCellValue('K5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('K6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('K10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('K12', ''); 
			} else if ($bulan7 == 'Juli'){
				$excel->setActiveSheetIndex(0)->setCellValue('K4',  $cbuTl7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K5',  $cbuNontl7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K6', '=SUM(K4:K5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('K9', $truckBustlA7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K10', $truckBustlB7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K11', $truckBustlC7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K12', $truckBustlD7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K13', $truckBustlE7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K14', '=SUM(K9:K13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('K17', $truckBusntlA7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K18', $truckBusntlB7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K19', $truckBusntlC7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K20', $truckBusntlD7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K21', $truckBusntlE7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K22', '=SUM(K17:K21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('K25', $albertlA7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K26', $albertlB7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K27', $albertlC7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K28', $albertlD7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K29', $albertlE7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K30', $albertlF7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K31', '=SUM(K25:K30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('K33', $alberntlA7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K34', $alberntlB7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K35', $alberntlC7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K36', $alberntlD7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K37', $alberntlE7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K38', $alberntlF7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K39', '=SUM(K33:K38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('K41', $motorTl7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K42', $motorNtl7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K45', $cargoA7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K46', $cargoB7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K47', $cargoC7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K48', $cargoD7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K52', $jasaCbuA7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K53', $jasaCbuB7?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('K55', $jasaAlberA7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K56', $jasaAlberB7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K58', $jasaTruckbusA7?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('K59', $jasaTruckbusB7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K61', $jasaCargoA7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K62', $jasaCargoB7?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K64', $jasaMotorA7?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('K65', $jasaMotorB7?:'0'); 	
	
			} 
	
			if (empty($bulan8)){
				$excel->setActiveSheetIndex(0)->setCellValue('L5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('L6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('L10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('L12', ''); 
			} else if ($bulan8 == 'Agustus'){
				$excel->setActiveSheetIndex(0)->setCellValue('L4',  $cbuTl8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L5',  $cbuNontl8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L6', '=SUM(L4:L5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('L9', $truckBustlA8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L10', $truckBustlB8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L11', $truckBustlC8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L12', $truckBustlD8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L13', $truckBustlE8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L14', '=SUM(L9:L13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('L17', $truckBusntlA8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L18', $truckBusntlB8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L19', $truckBusntlC8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L20', $truckBusntlD8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L21', $truckBusntlE8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L22', '=SUM(L17:L21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('L25', $albertlA8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L26', $albertlB8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L27', $albertlC8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L28', $albertlD8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L29', $albertlE8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L30', $albertlF8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L31', '=SUM(L25:L30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('L33', $alberntlA8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L34', $alberntlB8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L35', $alberntlC8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L36', $alberntlD8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L37', $alberntlE8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L38', $alberntlF8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L39', '=SUM(L33:L38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('L41', $motorTl8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L42', $motorNtl8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L45', $cargoA8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L46', $cargoB8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L47', $cargoC8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L48', $cargoD8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L52', $jasaCbuA8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L53', $jasaCbuB8?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('L55', $jasaAlberA8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L56', $jasaAlberB8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L58', $jasaTruckbusA8?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('L59', $jasaTruckbusB8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L61', $jasaCargoA8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L62', $jasaCargoB8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L64', $jasaMotorA8?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('L65', $jasaMotorB8?:'0'); 	
	
			}
			
			if (empty($bulan9)){
				$excel->setActiveSheetIndex(0)->setCellValue('M5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('M6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('M10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('M12', ''); 
			} else if ($bulan9 == 'September'){
				$excel->setActiveSheetIndex(0)->setCellValue('M4',  $cbuTl9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M5',  $cbuNontl9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M6', '=SUM(M4:M5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('M9', $truckBustlA9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M10', $truckBustlB9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M11', $truckBustlC9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M12', $truckBustlD9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M13', $truckBustlE9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M14', '=SUM(M9:M13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('M17', $truckBusntlA9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M18', $truckBusntlB9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M19', $truckBusntlC9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M20', $truckBusntlD9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M21', $truckBusntlE9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M22', '=SUM(M17:M21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('M25', $albertlA9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M26', $albertlB9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M27', $albertlC9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M28', $albertlD9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M29', $albertlE9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M30', $albertlF9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M31', '=SUM(M25:M30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('M33', $alberntlA9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M34', $alberntlB9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M35', $alberntlC9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M36', $alberntlD9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M37', $alberntlE9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M38', $alberntlF9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M39', '=SUM(M33:M38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('M41', $motorTl9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M42', $motorNtl9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M45', $cargoA9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M46', $cargoB9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M47', $cargoC9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M48', $cargoD9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M52', $jasaCbuA9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M53', $jasaCbuB9?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('M55', $jasaAlberA9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M56', $jasaAlberB9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M58', $jasaTruckbusA9?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('M59', $jasaTruckbusB9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M61', $jasaCargoA9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M62', $jasaCargoB9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M64', $jasaMotorA9?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('M65', $jasaMotorB9?:'0'); 		
		
			}
			
			if (empty($bulan10)){
				$excel->setActiveSheetIndex(0)->setCellValue('N5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('N6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('N10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('N12', ''); 
			} else if ($bulan10 == 'Oktober'){
				$excel->setActiveSheetIndex(0)->setCellValue('N4',  $cbuTl10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N5',  $cbuNontl10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N6', '=SUM(N4:N5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('N9', $truckBustlA10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N10', $truckBustlB10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N11', $truckBustlC10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N12', $truckBustlD10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N13', $truckBustlE10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N14', '=SUM(N9:N13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('N17', $truckBusntlA10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N18', $truckBusntlB10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N19', $truckBusntlC10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N20', $truckBusntlD10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N21', $truckBusntlE10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N22', '=SUM(N17:N21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('N25', $albertlA10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N26', $albertlB10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N27', $albertlC10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N28', $albertlD10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N29', $albertlE10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N30', $albertlF10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N31', '=SUM(N25:N30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('N33', $alberntlA10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N34', $alberntlB10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N35', $alberntlC10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N36', $alberntlD10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N37', $alberntlE10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N38', $alberntlF10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N39', '=SUM(N33:N38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('N41', $motorTl10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N42', $motorNtl10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N45', $cargoA10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N46', $cargoB10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N47', $cargoC10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N48', $cargoD10?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N52', $jasaCbuA10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N53', $jasaCbuB10?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('N55', $jasaAlberA10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N56', $jasaAlberB10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N58', $jasaTruckbusA10?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('N59', $jasaTruckbusB10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N61', $jasaCargoA10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N62', $jasaCargoB10?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N64', $jasaMotorA10?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('N65', $jasaMotorB10?:'0'); 	
 	
			}
			
			if (empty($bulan11)){
				$excel->setActiveSheetIndex(0)->setCellValue('O5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('O6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('O10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('O12', ''); 
			} else if ($bulan11 ==  'November'){
				$excel->setActiveSheetIndex(0)->setCellValue('O4',  $cbuTl11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O5',  $cbuNontl11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O6', '=SUM(O4:O5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('O9', $truckBustlA11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O10', $truckBustlB11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O11', $truckBustlC11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O12', $truckBustlD11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O13', $truckBustlE11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O14', '=SUM(O9:O13)'?:'0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('O17', $truckBusntlA11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O18', $truckBusntlB11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O19', $truckBusntlC11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O20', $truckBusntlD11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O21', $truckBusntlE11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O22', '=SUM(O17:O21)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('O25', $albertlA11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O26', $albertlB11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O27', $albertlC11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O28', $albertlD11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O29', $albertlE11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O30', $albertlF11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O31', '=SUM(O25:O30)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('O33', $alberntlA11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O34', $alberntlB11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O35', $alberntlC11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O36', $alberntlD11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O37', $alberntlE11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O38', $alberntlF11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O39', '=SUM(O33:O38)'); 
				
				$excel->setActiveSheetIndex(0)->setCellValue('O41', $motorTl11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O42', $motorNtl11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O45', $cargoA11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O46', $cargoB11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O47', $cargoC11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O48', $cargoD11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O52', $jasaCbuA11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O53', $jasaCbuB11?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('O55', $jasaAlberA11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O56', $jasaAlberB11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O58', $jasaTruckbusA11?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('O59', $jasaTruckbusB11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O61', $jasaCargoA11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O62', $jasaCargoB11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O64', $jasaMotorA11?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('O65', $jasaMotorB11?:'0'); 		
			}

			
			if (empty($bulan12)){
				$excel->setActiveSheetIndex(0)->setCellValue('P5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('P6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('P10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('P12', ''); 
			} else if ($bulan12 == 'Desember'){
				$excel->setActiveSheetIndex(0)->setCellValue('P4',  $cbuTl12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P5',  $cbuNontl12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P6', '=SUM(P4:P5)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('P9', $truckBustlA12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P10', $truckBustlB12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P11', $truckBustlC12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P12', $truckBustlD12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P13', $truckBustlE12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P14', '=SUM(P9:P13)'?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P17', $truckBusntlA12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P18', $truckBusntlB12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P19', $truckBusntlC12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P20', $truckBusntlD12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P21', $truckBusntlE12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P22', '=SUM(P17:P21)'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P25', $albertlA12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P26', $albertlB12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P27', $albertlC12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P28', $albertlD12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P29', $albertlE12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P30', $albertlF12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P31', '=SUM(P25:P30)'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P33', $alberntlA12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P34', $alberntlB12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P35', $alberntlC12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P36', $alberntlD12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P37', $alberntlE12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P38', $alberntlF12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P39', '=SUM(P33:P38)'); 				
				$excel->setActiveSheetIndex(0)->setCellValue('P41', $motorTl12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P42', $motorNtl12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P45', $cargoA12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P46', $cargoB12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P47', $cargoC12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P48', $cargoD12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P52', $jasaCbuA12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P53', $jasaCbuB12?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('P55', $jasaAlberA12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P56', $jasaAlberB12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P58', $jasaTruckbusA12?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('P59', $jasaTruckbusB12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P61', $jasaCargoA12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P62', $jasaCargoB12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P64', $jasaMotorA12?:'0'); 			
				$excel->setActiveSheetIndex(0)->setCellValue('P65', $jasaMotorB12?:'0'); 		
			
			}
			$excel->setActiveSheetIndex(0)->setCellValue('Q4', '=SUM(E4:P4)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q5', '=SUM(E5:P5)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q6', '=SUM(E6:P6)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q9', '=SUM(E9:P9)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q10', '=SUM(E10:P10)');	
			$excel->setActiveSheetIndex(0)->setCellValue('Q11', '=SUM(E11:P11)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q12', '=SUM(E12:P12)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q13', '=SUM(E13:P13)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q14', '=SUM(E14:P14)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q17', '=SUM(E17:P17)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q18', '=SUM(E18:P18)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q19', '=SUM(E19:P19)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q20', '=SUM(E20:P20)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q21', '=SUM(E21:P21)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q22', '=SUM(E22:P22)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q25', '=SUM(E25:P25)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q26', '=SUM(E26:P26)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q27', '=SUM(E27:P27)');				
			$excel->setActiveSheetIndex(0)->setCellValue('Q28', '=SUM(E28:P28)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q29', '=SUM(E29:P29)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q30', '=SUM(E30:P30)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q31', '=SUM(E31:P31)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q33', '=SUM(E33:P33)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q34', '=SUM(E34:P34)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q35', '=SUM(E35:P35)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q36', '=SUM(E36:P36)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q37', '=SUM(E37:P37)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q38', '=SUM(E38:P38)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q39', '=SUM(E39:P39)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q41', '=SUM(E41:P41)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q42', '=SUM(E42:P42)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q45', '=SUM(E45:P45)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q46', '=SUM(E46:P46)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q47', '=SUM(E47:P47)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q48', '=SUM(E48:P48)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q52', '=SUM(E52:P52)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q53', '=SUM(E53:P53)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q55', '=SUM(E55:P55)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q56', '=SUM(E56:P56)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q58', '=SUM(E58:P58)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q59', '=SUM(E59:P59)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q61', '=SUM(E61:P61)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q62', '=SUM(E62:P62)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q64', '=SUM(E64:P64)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q65', '=SUM(E65:P65)');

			if ($tipe == 'PER TAHUN'){
				$excel->setActiveSheetIndex(0)->setCellValue('R4', $tarifAcbutl);
				$excel->setActiveSheetIndex(0)->setCellValue('R5', $tarifAcbunontl);
				$excel->setActiveSheetIndex(0)->setCellValue('R6', '=SUM(R4:R5)');		
				$excel->setActiveSheetIndex(0)->setCellValue('R9', $tarifAtruck1);
				$excel->setActiveSheetIndex(0)->setCellValue('R10', $tarifAtruck2);
				$excel->setActiveSheetIndex(0)->setCellValue('R11', $tarifAtruck3);
				$excel->setActiveSheetIndex(0)->setCellValue('R12', $tarifAtruck4);
				$excel->setActiveSheetIndex(0)->setCellValue('R13', $tarifAtruck5);
				$excel->setActiveSheetIndex(0)->setCellValue('R14', '=SUM(R11:R15)');		
				$excel->setActiveSheetIndex(0)->setCellValue('R17', $tarifAtruckn1);
				$excel->setActiveSheetIndex(0)->setCellValue('R18', $tarifAtruckn2);
				$excel->setActiveSheetIndex(0)->setCellValue('R19', $tarifAtruckn3);
				$excel->setActiveSheetIndex(0)->setCellValue('R20', $tarifAtruckn4);
				$excel->setActiveSheetIndex(0)->setCellValue('R21', $tarifAtruckn5);
				$excel->setActiveSheetIndex(0)->setCellValue('R22', '=SUM(R19:R23)');		
				$excel->setActiveSheetIndex(0)->setCellValue('R25', $tarifAalber1);				
				$excel->setActiveSheetIndex(0)->setCellValue('R26', $tarifAalber2);
				$excel->setActiveSheetIndex(0)->setCellValue('R27', $tarifAalber3);
				$excel->setActiveSheetIndex(0)->setCellValue('R28', $tarifAalber4);
				$excel->setActiveSheetIndex(0)->setCellValue('R29', $tarifAalber5);
				$excel->setActiveSheetIndex(0)->setCellValue('R30', $tarifAalber6);
				$excel->setActiveSheetIndex(0)->setCellValue('R31', '=SUM(R27:R32)');		
				$excel->setActiveSheetIndex(0)->setCellValue('R33', $tarifAalbern1);
				$excel->setActiveSheetIndex(0)->setCellValue('R34', $tarifAalbern2);
				$excel->setActiveSheetIndex(0)->setCellValue('R35', $tarifAalbern3);
				$excel->setActiveSheetIndex(0)->setCellValue('R36', $tarifAalbern4);
				$excel->setActiveSheetIndex(0)->setCellValue('R37', $tarifAalbern5);
				$excel->setActiveSheetIndex(0)->setCellValue('R38', $tarifAalbern6);
				$excel->setActiveSheetIndex(0)->setCellValue('R39', '=SUM(R35:R40)');		
				$excel->setActiveSheetIndex(0)->setCellValue('R41', $tarifAmotortl);
				$excel->setActiveSheetIndex(0)->setCellValue('R42', $tarifAmotorntl);
				$excel->setActiveSheetIndex(0)->setCellValue('R45', $tarifAcargonormal);
				$excel->setActiveSheetIndex(0)->setCellValue('R46', $tarifAcargogB);
				$excel->setActiveSheetIndex(0)->setCellValue('R47', $tarifAcargobC);
				$excel->setActiveSheetIndex(0)->setCellValue('R48', $tarifAcargolD);		
				$excel->setActiveSheetIndex(0)->setCellValue('R52', $tarifAjcbu2);
				$excel->setActiveSheetIndex(0)->setCellValue('R53', $tarifAjcbu3);
				$excel->setActiveSheetIndex(0)->setCellValue('R55', $tarifAjalber2);
				$excel->setActiveSheetIndex(0)->setCellValue('R56', $tarifAjalber3);
				$excel->setActiveSheetIndex(0)->setCellValue('R58', $tarifAjtruckbus2);
				$excel->setActiveSheetIndex(0)->setCellValue('R59', $tarifAjtruckbus3);
				$excel->setActiveSheetIndex(0)->setCellValue('R61', $tarifAjcargo2);
				$excel->setActiveSheetIndex(0)->setCellValue('R62', $tarifAjcargo3);
				$excel->setActiveSheetIndex(0)->setCellValue('R64', $tarifAjmotor2);
				$excel->setActiveSheetIndex(0)->setCellValue('R65', $tarifAjmotor3);

			    $excel->setActiveSheetIndex(0)->setCellValue('S4', '=(Q4*R4)');
				$excel->setActiveSheetIndex(0)->setCellValue('S5', '=(Q5*R5)');
				$excel->setActiveSheetIndex(0)->setCellValue('S6', '=(Q6*R6)');
				$excel->setActiveSheetIndex(0)->setCellValue('S9', '=(Q9*R9)');
				$excel->setActiveSheetIndex(0)->setCellValue('S10', '=(Q10*R10)');		
				$excel->setActiveSheetIndex(0)->setCellValue('S11', '=(Q11*R11)');
				$excel->setActiveSheetIndex(0)->setCellValue('S12', '=(Q12*R12)');
				$excel->setActiveSheetIndex(0)->setCellValue('S13', '=(Q13*R13)');
				$excel->setActiveSheetIndex(0)->setCellValue('S14', '=(Q14*R14)');
				$excel->setActiveSheetIndex(0)->setCellValue('S17', '=(Q17*R17)');
				$excel->setActiveSheetIndex(0)->setCellValue('S18', '=SUM(S11:S15)');
				$excel->setActiveSheetIndex(0)->setCellValue('S19', '=(Q19*R19)');
				$excel->setActiveSheetIndex(0)->setCellValue('S20', '=(Q20*R20)');
				$excel->setActiveSheetIndex(0)->setCellValue('S21', '=(Q21*R21)');
				$excel->setActiveSheetIndex(0)->setCellValue('S22', '=(Q22*R22)');
				$excel->setActiveSheetIndex(0)->setCellValue('S25', '=(Q25*R25)');
				$excel->setActiveSheetIndex(0)->setCellValue('S26', '=SUM(S19:S25)');	
				$excel->setActiveSheetIndex(0)->setCellValue('S27', '=(Q27*R27)');				
				$excel->setActiveSheetIndex(0)->setCellValue('S28', '=(Q28*R28)');
				$excel->setActiveSheetIndex(0)->setCellValue('S29', '=(Q29*R29)');
				$excel->setActiveSheetIndex(0)->setCellValue('S30', '=(Q30*R30)');
				$excel->setActiveSheetIndex(0)->setCellValue('S31', '=(Q31*R31)');
				$excel->setActiveSheetIndex(0)->setCellValue('S33', '=(Q33*R33)');
				$excel->setActiveSheetIndex(0)->setCellValue('S34', '=SUM(S27:S33)');	
				$excel->setActiveSheetIndex(0)->setCellValue('S35', '=(Q35*R35)');
				$excel->setActiveSheetIndex(0)->setCellValue('S36', '=(Q36*R36)');
				$excel->setActiveSheetIndex(0)->setCellValue('S37', '=(Q37*R37)');
				$excel->setActiveSheetIndex(0)->setCellValue('S38', '=(Q38*R38)');
				$excel->setActiveSheetIndex(0)->setCellValue('S39', '=(Q39*R39)');
				$excel->setActiveSheetIndex(0)->setCellValue('S41', '=(Q41*R41)');
				$excel->setActiveSheetIndex(0)->setCellValue('S42', '=SUM(R35:R41)');	
				$excel->setActiveSheetIndex(0)->setCellValue('S45', '=(Q45*R45)');
				$excel->setActiveSheetIndex(0)->setCellValue('S46', '=(Q46*R46)');
				$excel->setActiveSheetIndex(0)->setCellValue('S47', '=(Q47*R47)');
				$excel->setActiveSheetIndex(0)->setCellValue('S48', '=(Q48*R48)');
				$excel->setActiveSheetIndex(0)->setCellValue('S52', '=(Q52*R52)');
				$excel->setActiveSheetIndex(0)->setCellValue('S53', '=(Q53*R53)');	
				$excel->setActiveSheetIndex(0)->setCellValue('S55', '=(Q55*R55)');
				$excel->setActiveSheetIndex(0)->setCellValue('S56', '=(Q56*R56)');
				$excel->setActiveSheetIndex(0)->setCellValue('S58', '=(Q58*R58)');
				$excel->setActiveSheetIndex(0)->setCellValue('S59', '=(Q59*R59)');
				$excel->setActiveSheetIndex(0)->setCellValue('S61', '=(Q61*R61)');
				$excel->setActiveSheetIndex(0)->setCellValue('S62', '=(Q62*R62)');
				$excel->setActiveSheetIndex(0)->setCellValue('S64', '=(Q64*R64)');
				$excel->setActiveSheetIndex(0)->setCellValue('S65', '=(Q65*R65)');		

				$excel->setActiveSheetIndex(0)->setCellValue('T4', '=S4');
				$excel->setActiveSheetIndex(0)->setCellValue('T5', '=S5');
				$excel->setActiveSheetIndex(0)->setCellValue('T6', '=S6');
				$excel->setActiveSheetIndex(0)->setCellValue('T9', '=S9');
				$excel->setActiveSheetIndex(0)->setCellValue('T10', '=S10');		
				$excel->setActiveSheetIndex(0)->setCellValue('T11', '=S11');
				$excel->setActiveSheetIndex(0)->setCellValue('T12', '=S12');
				$excel->setActiveSheetIndex(0)->setCellValue('T13', '=S13');
				$excel->setActiveSheetIndex(0)->setCellValue('T14', '=S14');
				$excel->setActiveSheetIndex(0)->setCellValue('T17', '=S17');
				$excel->setActiveSheetIndex(0)->setCellValue('T18', '=S18');
				$excel->setActiveSheetIndex(0)->setCellValue('T19', '=S19');
				$excel->setActiveSheetIndex(0)->setCellValue('T20', '=S20');
				$excel->setActiveSheetIndex(0)->setCellValue('T21', '=S21');
				$excel->setActiveSheetIndex(0)->setCellValue('T22', '=S22');
				$excel->setActiveSheetIndex(0)->setCellValue('T25', '=S25');
				$excel->setActiveSheetIndex(0)->setCellValue('T26', '=S26');	
				$excel->setActiveSheetIndex(0)->setCellValue('T27', '=S27');				
				$excel->setActiveSheetIndex(0)->setCellValue('T28', '=S28');
				$excel->setActiveSheetIndex(0)->setCellValue('T29', '=S29');
				$excel->setActiveSheetIndex(0)->setCellValue('T30', '=S30');
				$excel->setActiveSheetIndex(0)->setCellValue('T31', '=S31');
				$excel->setActiveSheetIndex(0)->setCellValue('T33', '=S33');
				$excel->setActiveSheetIndex(0)->setCellValue('T34', '=S34');	
				$excel->setActiveSheetIndex(0)->setCellValue('T35', '=S35');
				$excel->setActiveSheetIndex(0)->setCellValue('T36', '=S36');
				$excel->setActiveSheetIndex(0)->setCellValue('T37', '=S37');
				$excel->setActiveSheetIndex(0)->setCellValue('T38', '=S38');
				$excel->setActiveSheetIndex(0)->setCellValue('T39', '=S39');
				$excel->setActiveSheetIndex(0)->setCellValue('T41', '=S41');
				$excel->setActiveSheetIndex(0)->setCellValue('T42', '=S42');	
				$excel->setActiveSheetIndex(0)->setCellValue('T45', '=S45');
				$excel->setActiveSheetIndex(0)->setCellValue('T46', '=S46');
				$excel->setActiveSheetIndex(0)->setCellValue('T47', '=S47');
				$excel->setActiveSheetIndex(0)->setCellValue('T48', '=S48');
				$excel->setActiveSheetIndex(0)->setCellValue('T52', '=S52');
				$excel->setActiveSheetIndex(0)->setCellValue('T53', '=S53');	
				$excel->setActiveSheetIndex(0)->setCellValue('T55', '=S55');
				$excel->setActiveSheetIndex(0)->setCellValue('T56', '=S56');
				$excel->setActiveSheetIndex(0)->setCellValue('T58', '=S58');
				$excel->setActiveSheetIndex(0)->setCellValue('T59', '=S59');
				$excel->setActiveSheetIndex(0)->setCellValue('T61', '=S61');
				$excel->setActiveSheetIndex(0)->setCellValue('T62', '=S62');
				$excel->setActiveSheetIndex(0)->setCellValue('T64', '=S64');
				$excel->setActiveSheetIndex(0)->setCellValue('T65', '=S65');							
				
				$excel->setActiveSheetIndex(0)->setCellValue('U4', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U7', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U8', '');		
				$excel->setActiveSheetIndex(0)->setCellValue('U11', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U12', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U13', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U14', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U15', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U16', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U19', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U20', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U21', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U22', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U23', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U24', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('U27', '');				
				$excel->setActiveSheetIndex(0)->setCellValue('U28', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U29', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U30', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U31', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U32', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U33', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('U35', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U36', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U37', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U38', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U39', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U40', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U41', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('U43', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U44', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U47', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U48', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U49', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U50', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('U54', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U55', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U57', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U58', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U60', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U61', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U63', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U64', '');
				$excel->setActiveSheetIndex(0)->setCellValue('U65', '');
			
				$excel->setActiveSheetIndex(0)->setCellValue('V4', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V7', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V8', '');		
				$excel->setActiveSheetIndex(0)->setCellValue('V11', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V12', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V13', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V14', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V15', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V16', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V19', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V20', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V21', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V22', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V23', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V24', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('V27', '');				
				$excel->setActiveSheetIndex(0)->setCellValue('V28', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V29', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V30', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V31', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V32', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V33', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('V35', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V36', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V37', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V38', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V39', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V40', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V41', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('V43', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V44', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V47', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V48', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V49', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V50', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('V54', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V55', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V57', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V58', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V60', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V61', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V63', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V64', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V65', '');
	
			}

			if ($tipe == 'PER SEMESTER' || $tipe == 'PER TRIWULAN'){
			$excel->setActiveSheetIndex(0)->setCellValue('R4', $tarifAcbutl);
			$excel->setActiveSheetIndex(0)->setCellValue('R5', $tarifAcbunontl);
			$excel->setActiveSheetIndex(0)->setCellValue('R6', '=SUM(R4:R7)');	
			$excel->setActiveSheetIndex(0)->setCellValue('R9', $tarifAtruck1);
			$excel->setActiveSheetIndex(0)->setCellValue('R10', $tarifAtruck2);
			$excel->setActiveSheetIndex(0)->setCellValue('R11', $tarifAtruck3);
			$excel->setActiveSheetIndex(0)->setCellValue('R12', $tarifAtruck4);
			$excel->setActiveSheetIndex(0)->setCellValue('R13', $tarifAtruck5);
			$excel->setActiveSheetIndex(0)->setCellValue('R14', '=SUM(R11:R15)');	
			$excel->setActiveSheetIndex(0)->setCellValue('R17', $tarifAtruckn1);
			$excel->setActiveSheetIndex(0)->setCellValue('R18', $tarifAtruckn2);
			$excel->setActiveSheetIndex(0)->setCellValue('R19', $tarifAtruckn3);
			$excel->setActiveSheetIndex(0)->setCellValue('R20', $tarifAtruckn4);
			$excel->setActiveSheetIndex(0)->setCellValue('R21', $tarifAtruckn5);
			$excel->setActiveSheetIndex(0)->setCellValue('R22', '=SUM(R19:R23)');	
			$excel->setActiveSheetIndex(0)->setCellValue('R25', $tarifAalber1);				
			$excel->setActiveSheetIndex(0)->setCellValue('R26', $tarifAalber2);
			$excel->setActiveSheetIndex(0)->setCellValue('R27', $tarifAalber3);
			$excel->setActiveSheetIndex(0)->setCellValue('R28', $tarifAalber4);
			$excel->setActiveSheetIndex(0)->setCellValue('R29', $tarifAalber5);
			$excel->setActiveSheetIndex(0)->setCellValue('R30', $tarifAalber6);
			$excel->setActiveSheetIndex(0)->setCellValue('R31', '=SUM(R27:R32)');	
			$excel->setActiveSheetIndex(0)->setCellValue('R33', $tarifAalbern1);
			$excel->setActiveSheetIndex(0)->setCellValue('R34', $tarifAalbern2);
			$excel->setActiveSheetIndex(0)->setCellValue('R35', $tarifAalbern3);
			$excel->setActiveSheetIndex(0)->setCellValue('R36', $tarifAalbern4);
			$excel->setActiveSheetIndex(0)->setCellValue('R37', $tarifAalbern5);
			$excel->setActiveSheetIndex(0)->setCellValue('R38', $tarifAalbern6 ?: '0');
			$excel->setActiveSheetIndex(0)->setCellValue('R39', '=SUM(R35:R40)');	
			$excel->setActiveSheetIndex(0)->setCellValue('R41', $tarifAmotortl);
			$excel->setActiveSheetIndex(0)->setCellValue('R42', $tarifAmotorntl);
			$excel->setActiveSheetIndex(0)->setCellValue('R45', $tarifAcargonormal);
			$excel->setActiveSheetIndex(0)->setCellValue('R46', $tarifAcargogB);
			$excel->setActiveSheetIndex(0)->setCellValue('R47', $tarifAcargobC);
			$excel->setActiveSheetIndex(0)->setCellValue('R48', $tarifAcargolD);	
			$excel->setActiveSheetIndex(0)->setCellValue('R52', $tarifAjcbu2);
			$excel->setActiveSheetIndex(0)->setCellValue('R53', $tarifAjcbu3);
			$excel->setActiveSheetIndex(0)->setCellValue('R55', $tarifAjalber2);
			$excel->setActiveSheetIndex(0)->setCellValue('R56', $tarifAjalber3);
			$excel->setActiveSheetIndex(0)->setCellValue('R58', $tarifAjtruckbus2);
			$excel->setActiveSheetIndex(0)->setCellValue('R59', $tarifAjtruckbus3);
			$excel->setActiveSheetIndex(0)->setCellValue('R61', $tarifAjcargo2);
			$excel->setActiveSheetIndex(0)->setCellValue('R62', $tarifAjcargo3);
			$excel->setActiveSheetIndex(0)->setCellValue('R64', $tarifAjmotor2);
			$excel->setActiveSheetIndex(0)->setCellValue('R65', $tarifAjmotor3);

			$excel->setActiveSheetIndex(0)->setCellValue('S4', $tarifBcbutl);
			$excel->setActiveSheetIndex(0)->setCellValue('S5', $tarifBcbunontl);
			$excel->setActiveSheetIndex(0)->setCellValue('S6', '=SUM(S4:R7)');	
			$excel->setActiveSheetIndex(0)->setCellValue('S9', $tarifBtruck1);
			$excel->setActiveSheetIndex(0)->setCellValue('S10', $tarifBtruck2);
			$excel->setActiveSheetIndex(0)->setCellValue('S11', $tarifBtruck3);
			$excel->setActiveSheetIndex(0)->setCellValue('S12', $tarifBtruck4);
			$excel->setActiveSheetIndex(0)->setCellValue('S13', $tarifBtruck5);
			$excel->setActiveSheetIndex(0)->setCellValue('S14', '=SUM(S11:R15)');	
			$excel->setActiveSheetIndex(0)->setCellValue('S17', $tarifBtruckn1);
			$excel->setActiveSheetIndex(0)->setCellValue('S18', $tarifBtruckn2);
			$excel->setActiveSheetIndex(0)->setCellValue('S19', $tarifBtruckn3);
			$excel->setActiveSheetIndex(0)->setCellValue('S20', $tarifBtruckn4);
			$excel->setActiveSheetIndex(0)->setCellValue('S21', $tarifBtruckn5);
			$excel->setActiveSheetIndex(0)->setCellValue('S22', '=SUM(S19:R23)');	
			$excel->setActiveSheetIndex(0)->setCellValue('S25', $tarifBalber1);				
			$excel->setActiveSheetIndex(0)->setCellValue('S26', $tarifBalber2);
			$excel->setActiveSheetIndex(0)->setCellValue('S27', $tarifBalber3);
			$excel->setActiveSheetIndex(0)->setCellValue('S28', $tarifBalber4);
			$excel->setActiveSheetIndex(0)->setCellValue('S29', $tarifBalber5);
			$excel->setActiveSheetIndex(0)->setCellValue('S30', $tarifBalber6);
			$excel->setActiveSheetIndex(0)->setCellValue('S31', '=SUM(S27:R32)');	
			$excel->setActiveSheetIndex(0)->setCellValue('S33', $tarifBalbern1);
			$excel->setActiveSheetIndex(0)->setCellValue('S34', $tarifBalbern2);
			$excel->setActiveSheetIndex(0)->setCellValue('S35', $tarifBalbern3);
			$excel->setActiveSheetIndex(0)->setCellValue('S36', $tarifBalbern4);
			$excel->setActiveSheetIndex(0)->setCellValue('S37', $tarifBalbern5);
			$excel->setActiveSheetIndex(0)->setCellValue('S38', $tarifBalbern6 ?: '0');
			$excel->setActiveSheetIndex(0)->setCellValue('S39', '=SUM(S35:R40)');	
			$excel->setActiveSheetIndex(0)->setCellValue('S41', $tarifBmotortl);
			$excel->setActiveSheetIndex(0)->setCellValue('S42', $tarifBmotorntl);
			$excel->setActiveSheetIndex(0)->setCellValue('S45', $tarifBcargonormal);
			$excel->setActiveSheetIndex(0)->setCellValue('S46', $tarifBcargogB);
			$excel->setActiveSheetIndex(0)->setCellValue('S47', $tarifBcargobC);
			$excel->setActiveSheetIndex(0)->setCellValue('S48', $tarifBcargolD);	
			$excel->setActiveSheetIndex(0)->setCellValue('S52', $tarifBjcbu2);
			$excel->setActiveSheetIndex(0)->setCellValue('S53', $tarifBjcbu3);		
			$excel->setActiveSheetIndex(0)->setCellValue('S55', $tarifBjalber2);
			$excel->setActiveSheetIndex(0)->setCellValue('S56', $tarifBjalber3);
			$excel->setActiveSheetIndex(0)->setCellValue('S58', $tarifBjtruckbus2);
			$excel->setActiveSheetIndex(0)->setCellValue('S59', $tarifBjtruckbus3);
			$excel->setActiveSheetIndex(0)->setCellValue('S61', $tarifBjcargo2);
			$excel->setActiveSheetIndex(0)->setCellValue('S62', $tarifBjcargo3);
			$excel->setActiveSheetIndex(0)->setCellValue('S64', $tarifBjmotor2);
			$excel->setActiveSheetIndex(0)->setCellValue('S65', $tarifBjmotor3);

			$excel->setActiveSheetIndex(0)->setCellValue('T4', '=(Q4*R4)');
			$excel->setActiveSheetIndex(0)->setCellValue('T5', '=(Q5*R5)');
			$excel->setActiveSheetIndex(0)->setCellValue('T6', '=(Q6*R6)');
			$excel->setActiveSheetIndex(0)->setCellValue('T9', '=(Q9*R9)');
			$excel->setActiveSheetIndex(0)->setCellValue('T10', '=(Q10*R10)');	
			$excel->setActiveSheetIndex(0)->setCellValue('T11', '=(Q11*R11)');
			$excel->setActiveSheetIndex(0)->setCellValue('T12', '=(Q12*R12)');
			$excel->setActiveSheetIndex(0)->setCellValue('T13', '=(Q13*R13)');
			$excel->setActiveSheetIndex(0)->setCellValue('T14', '=(Q14*R14)');
			$excel->setActiveSheetIndex(0)->setCellValue('T17', '=(Q17*R17)');
			$excel->setActiveSheetIndex(0)->setCellValue('T18', '=(Q18*R18)');
			$excel->setActiveSheetIndex(0)->setCellValue('T19', '=(Q19*R19)');
			$excel->setActiveSheetIndex(0)->setCellValue('T20', '=(Q20*R20)');
			$excel->setActiveSheetIndex(0)->setCellValue('T21', '=(Q21*R21)');
			$excel->setActiveSheetIndex(0)->setCellValue('T22', '=(Q22*R22)');
			$excel->setActiveSheetIndex(0)->setCellValue('T25', '=(Q25*R25)');
			$excel->setActiveSheetIndex(0)->setCellValue('T26', '=(Q26*R26)');
			$excel->setActiveSheetIndex(0)->setCellValue('T27', '=(Q27*R27)');				
			$excel->setActiveSheetIndex(0)->setCellValue('T28', '=(Q28*R28)');
			$excel->setActiveSheetIndex(0)->setCellValue('T29', '=(Q29*R29)');
			$excel->setActiveSheetIndex(0)->setCellValue('T30', '=(Q30*R30)');
			$excel->setActiveSheetIndex(0)->setCellValue('T31', '=(Q31*R31)');
			$excel->setActiveSheetIndex(0)->setCellValue('T33', '=(Q33*R33)');
			$excel->setActiveSheetIndex(0)->setCellValue('T34', '=(Q34*R34)');
			$excel->setActiveSheetIndex(0)->setCellValue('T35', '=(Q35*R35)');
			$excel->setActiveSheetIndex(0)->setCellValue('T36', '=(Q36*R36)');
			$excel->setActiveSheetIndex(0)->setCellValue('T37', '=(Q37*R37)');
			$excel->setActiveSheetIndex(0)->setCellValue('T38', '=(Q38*R38)');
			$excel->setActiveSheetIndex(0)->setCellValue('T39', '=(Q39*R39)');
			$excel->setActiveSheetIndex(0)->setCellValue('T41', '=(Q41*R41)');
			$excel->setActiveSheetIndex(0)->setCellValue('T42', '=(Q42*R42)');
			$excel->setActiveSheetIndex(0)->setCellValue('T45', '=(Q45*R45)');
			$excel->setActiveSheetIndex(0)->setCellValue('T46', '=(Q46*R46)');
			$excel->setActiveSheetIndex(0)->setCellValue('T47', '=(Q47*R47)');
			$excel->setActiveSheetIndex(0)->setCellValue('T48', '=(Q48*R48)');
			$excel->setActiveSheetIndex(0)->setCellValue('T52', '=(Q52*R52)');
			$excel->setActiveSheetIndex(0)->setCellValue('T53', '=(Q53*R53)');
			$excel->setActiveSheetIndex(0)->setCellValue('T55', '=(Q55*R55)');
			$excel->setActiveSheetIndex(0)->setCellValue('T56', '=(Q56*R56)');
			$excel->setActiveSheetIndex(0)->setCellValue('T58', '=(Q58*R58)');
			$excel->setActiveSheetIndex(0)->setCellValue('T59', '=(Q59*R59)');
			$excel->setActiveSheetIndex(0)->setCellValue('T61', '=(Q61*R61)');
			$excel->setActiveSheetIndex(0)->setCellValue('T62', '=(Q62*R62)');
			$excel->setActiveSheetIndex(0)->setCellValue('T64', '=(Q64*R64)');
			$excel->setActiveSheetIndex(0)->setCellValue('T64', '=(Q64*R64)');
			$excel->setActiveSheetIndex(0)->setCellValue('T66', '=(Q66*R66)');
			$excel->setActiveSheetIndex(0)->setCellValue('T67', '=(Q67*R67)');
			$excel->setActiveSheetIndex(0)->setCellValue('T69', '=(Q69*R69)');
			$excel->setActiveSheetIndex(0)->setCellValue('T70', '=(Q70*R70)');				

			$excel->setActiveSheetIndex(0)->setCellValue('U4', '=(Q4*R4)');
			$excel->setActiveSheetIndex(0)->setCellValue('U5', '=(Q5*R5)');
			$excel->setActiveSheetIndex(0)->setCellValue('U6', '=(Q6*R6)');
			$excel->setActiveSheetIndex(0)->setCellValue('U9', '=(Q9*R9)');
			$excel->setActiveSheetIndex(0)->setCellValue('U10', '=(Q10*R10)');	
			$excel->setActiveSheetIndex(0)->setCellValue('U11', '=(Q11*S11)');
			$excel->setActiveSheetIndex(0)->setCellValue('U12', '=(Q12*S12)');
			$excel->setActiveSheetIndex(0)->setCellValue('U13', '=(Q13*S13)');
			$excel->setActiveSheetIndex(0)->setCellValue('U14', '=(Q14*S14)');
			$excel->setActiveSheetIndex(0)->setCellValue('U17', '=(Q17*S17)');
			$excel->setActiveSheetIndex(0)->setCellValue('U18', '=(Q18*S18)');
			$excel->setActiveSheetIndex(0)->setCellValue('U19', '=(Q19*S19)');
			$excel->setActiveSheetIndex(0)->setCellValue('U20', '=(Q20*S20)');
			$excel->setActiveSheetIndex(0)->setCellValue('U21', '=(Q21*S21)');
			$excel->setActiveSheetIndex(0)->setCellValue('U22', '=(Q22*S22)');
			$excel->setActiveSheetIndex(0)->setCellValue('U25', '=(Q25*S25)');
			$excel->setActiveSheetIndex(0)->setCellValue('U26', '=(Q26*S26)');
			$excel->setActiveSheetIndex(0)->setCellValue('U27', '=(Q27*S27)');				
			$excel->setActiveSheetIndex(0)->setCellValue('U28', '=(Q28*S28)');
			$excel->setActiveSheetIndex(0)->setCellValue('U29', '=(Q29*S29)');
			$excel->setActiveSheetIndex(0)->setCellValue('U30', '=(Q30*S30)');
			$excel->setActiveSheetIndex(0)->setCellValue('U31', '=(Q31*S31)');
			$excel->setActiveSheetIndex(0)->setCellValue('U33', '=(Q33*S33)');
			$excel->setActiveSheetIndex(0)->setCellValue('U34', '=(Q34*S34)');
			$excel->setActiveSheetIndex(0)->setCellValue('U35', '=(Q35*S35)');
			$excel->setActiveSheetIndex(0)->setCellValue('U36', '=(Q36*S36)');
			$excel->setActiveSheetIndex(0)->setCellValue('U37', '=(Q37*S37)');
			$excel->setActiveSheetIndex(0)->setCellValue('U38', '=(Q38*S38)');
			$excel->setActiveSheetIndex(0)->setCellValue('U39', '=(Q39*S39)');
			$excel->setActiveSheetIndex(0)->setCellValue('U41', '=(Q41*S41)');
			$excel->setActiveSheetIndex(0)->setCellValue('U42', '=(Q42*S42)');
			$excel->setActiveSheetIndex(0)->setCellValue('U45', '=(Q45*S45)');
			$excel->setActiveSheetIndex(0)->setCellValue('U46', '=(Q46*S46)');
			$excel->setActiveSheetIndex(0)->setCellValue('U47', '=(Q47*S47)');
			$excel->setActiveSheetIndex(0)->setCellValue('U48', '=(Q48*S48)');
			$excel->setActiveSheetIndex(0)->setCellValue('U52', '=(Q52*S52)');
			$excel->setActiveSheetIndex(0)->setCellValue('U53', '=(Q53*S53)');
			$excel->setActiveSheetIndex(0)->setCellValue('U55', '=(Q55*S55)');
			$excel->setActiveSheetIndex(0)->setCellValue('U56', '=(Q56*S56)');
			$excel->setActiveSheetIndex(0)->setCellValue('U58', '=(Q58*S58)');
			$excel->setActiveSheetIndex(0)->setCellValue('U59', '=(Q59*S59)');
			$excel->setActiveSheetIndex(0)->setCellValue('U61', '=(Q61*S61)');
			$excel->setActiveSheetIndex(0)->setCellValue('U62', '=(Q62*S62)');
			$excel->setActiveSheetIndex(0)->setCellValue('U64', '=(Q64*S64)');
			$excel->setActiveSheetIndex(0)->setCellValue('U64', '=(Q64*S64)');
			$excel->setActiveSheetIndex(0)->setCellValue('U65', '=(Q65*S65)');	
			
			$excel->setActiveSheetIndex(0)->setCellValue('V4', '=(T4*R4)');
			$excel->setActiveSheetIndex(0)->setCellValue('V5', '=(T5*R5)');
			$excel->setActiveSheetIndex(0)->setCellValue('V6', '=(T6*R6)');
			$excel->setActiveSheetIndex(0)->setCellValue('V9', '=(T9*R9)');
			$excel->setActiveSheetIndex(0)->setCellValue('V10', '=(T10*R10)');	
			$excel->setActiveSheetIndex(0)->setCellValue('V11', '=(T11+U11)');
			$excel->setActiveSheetIndex(0)->setCellValue('V12', '=(T12+U12)');
			$excel->setActiveSheetIndex(0)->setCellValue('V13', '=(T13+U13)');
			$excel->setActiveSheetIndex(0)->setCellValue('V14', '=(T14+U14)');
			$excel->setActiveSheetIndex(0)->setCellValue('V17', '=(T17+U17)');
			$excel->setActiveSheetIndex(0)->setCellValue('V18', '=(T18+U18)');
			$excel->setActiveSheetIndex(0)->setCellValue('V19', '=(T19+U19)');
			$excel->setActiveSheetIndex(0)->setCellValue('V20', '=(T20+U20)');
			$excel->setActiveSheetIndex(0)->setCellValue('V21', '=(T21+U21)');
			$excel->setActiveSheetIndex(0)->setCellValue('V22', '=(T22+U22)');
			$excel->setActiveSheetIndex(0)->setCellValue('V25', '=(T25+U25)');
			$excel->setActiveSheetIndex(0)->setCellValue('V26', '=(T26+U26)');
			$excel->setActiveSheetIndex(0)->setCellValue('V27', '=(T27+U27)');				
			$excel->setActiveSheetIndex(0)->setCellValue('V28', '=(T28+U28)');
			$excel->setActiveSheetIndex(0)->setCellValue('V29', '=(T29+U29)');
			$excel->setActiveSheetIndex(0)->setCellValue('V30', '=(T30+U30)');
			$excel->setActiveSheetIndex(0)->setCellValue('V31', '=(T31+U31)');
			$excel->setActiveSheetIndex(0)->setCellValue('V33', '=(T33+U33)');
			$excel->setActiveSheetIndex(0)->setCellValue('V34', '=(T34+U34)');
			$excel->setActiveSheetIndex(0)->setCellValue('V35', '=(T35+U35)');
			$excel->setActiveSheetIndex(0)->setCellValue('V36', '=(T36+U36)');
			$excel->setActiveSheetIndex(0)->setCellValue('V37', '=(T37+U37)');
			$excel->setActiveSheetIndex(0)->setCellValue('V38', '=(T38+U38)');
			$excel->setActiveSheetIndex(0)->setCellValue('V39', '=(T39+U39)');
			$excel->setActiveSheetIndex(0)->setCellValue('V41', '=(T41+U41)');
			$excel->setActiveSheetIndex(0)->setCellValue('V42', '=(T42+U42)');
			$excel->setActiveSheetIndex(0)->setCellValue('V45', '=(T45+U45)');
			$excel->setActiveSheetIndex(0)->setCellValue('V46', '=(T46+U46)');
			$excel->setActiveSheetIndex(0)->setCellValue('V47', '=(T47+U47)');
			$excel->setActiveSheetIndex(0)->setCellValue('V48', '=(T48+U48)');
			$excel->setActiveSheetIndex(0)->setCellValue('V52', '=(T52+U52)');
			$excel->setActiveSheetIndex(0)->setCellValue('V53', '=(T53+U53)');
			$excel->setActiveSheetIndex(0)->setCellValue('V55', '=(T55+U55)');
			$excel->setActiveSheetIndex(0)->setCellValue('V56', '=(T56+U56)');
			$excel->setActiveSheetIndex(0)->setCellValue('V58', '=(T58+U58)');
			$excel->setActiveSheetIndex(0)->setCellValue('V59', '=(T59+U59)');
			$excel->setActiveSheetIndex(0)->setCellValue('V61', '=(T61+U61)');
			$excel->setActiveSheetIndex(0)->setCellValue('V62', '=(T62+U62)');
			$excel->setActiveSheetIndex(0)->setCellValue('V64', '=(T64+U64)');
			$excel->setActiveSheetIndex(0)->setCellValue('V64', '=(T64+U64)');
			$excel->setActiveSheetIndex(0)->setCellValue('V66', '=(T66+U66)');
			$excel->setActiveSheetIndex(0)->setCellValue('V67', '=(T67+U67)');
			$excel->setActiveSheetIndex(0)->setCellValue('V69', '=(T69+U69)');
			$excel->setActiveSheetIndex(0)->setCellValue('V70', '=(T70+U70)');	
			}

			// // Set width kolom
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(5); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(25); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(15); // Set width kolom D
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
            $excel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
			$excel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
			$excel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
			$excel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
			$excel->getActiveSheet()->getColumnDimension('V')->setWidth(30);

			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Laporan_Pendapatan");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Pelaporan Produksi dan Pendapatan per pusat layanan_DOM_'.$id.'_'.$end.'.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');
		


	}

	public function export_pendapatan_xls($id,$end,$type)
	{
		
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Laporan_Pendapatan')							
								   ->setTitle("Laporan_Pendapatan")
								   ->setSubject("Laporan_Pendapatan")
								   ->setDescription("Laporan_Pendapatan")
								   ->setKeywords("Data_Pendapatan");
		
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
			$excel->setActiveSheetIndex(0)->mergeCells('B1:B2')->setCellValue('B2', "");
		
			$excel->setActiveSheetIndex(0)->mergeCells('C1:C2')->setCellValue('C2', "");
			$excel->setActiveSheetIndex(0)->setCellValue('C1', "Layanan");

			$excel->setActiveSheetIndex(0)->mergeCells('D1:D2')->setCellValue('D1', "Satuan");
	
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
			$excel->setActiveSheetIndex(0)->setCellValue('Q2', "REALISASI 2022");
			$excel->setActiveSheetIndex(0)->setCellValue('R2',"Tarif I");
			$excel->setActiveSheetIndex(0)->setCellValue('S2',"Tarif II");
			$excel->setActiveSheetIndex(0)->setCellValue('T2',"Pendapatan I");
			$excel->setActiveSheetIndex(0)->setCellValue('U2',"Pendapatan II");
			$excel->setActiveSheetIndex(0)->setCellValue('V2',"Pendapatan s.d Desember");

			$excel->setActiveSheetIndex(0)->mergeCells('B3:C3')->setCellValue('B3', "CBU");
			$excel->setActiveSheetIndex(0)->setCellValue('A4', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A5', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A6', "3");
			$excel->setActiveSheetIndex(0)->setCellValue('A7', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('A8', "5");
			$excel->setActiveSheetIndex(0)->setCellValue('A9', "6");
			$excel->setActiveSheetIndex(0)->setCellValue('A10', "7");

			$excel->setActiveSheetIndex(0)->setCellValue('A14', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A22', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A23', "3");
			$excel->setActiveSheetIndex(0)->setCellValue('A31', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('A32', "5");
			$excel->setActiveSheetIndex(0)->setCellValue('A33', "6");
			$excel->setActiveSheetIndex(0)->setCellValue('A34', "7");
	
			$excel->setActiveSheetIndex(0)->setCellValue('A38', "1");
			$excel->setActiveSheetIndex(0)->setCellValue('A42', "2");
			$excel->setActiveSheetIndex(0)->setCellValue('A45', "3");
			$excel->setActiveSheetIndex(0)->setCellValue('A50', "4");
			$excel->setActiveSheetIndex(0)->setCellValue('A54', "5");
			$excel->setActiveSheetIndex(0)->setCellValue('A58', "6");
			$excel->setActiveSheetIndex(0)->setCellValue('A62', "7");
	
			$excel->setActiveSheetIndex(0)->setCellValue('C4', "Jasa Dermaga");
			$excel->setActiveSheetIndex(0)->setCellValue('C5', "Stevedoring");
			$excel->setActiveSheetIndex(0)->setCellValue('C6', "Cargo Handling");
			$excel->setActiveSheetIndex(0)->setCellValue('C7', "Kebersihan");
			$excel->setActiveSheetIndex(0)->setCellValue('C8', "Penumpukan Masa I");
			$excel->setActiveSheetIndex(0)->setCellValue('C9', "Penumpukan Masa II");
			$excel->setActiveSheetIndex(0)->setCellValue('C10', "Penumpukan Masa III");	

			$excel->setActiveSheetIndex(0)->mergeCells('B11:C11')->setCellValue('B11', "TOTAL CBU");

			$excel->setActiveSheetIndex(0)->mergeCells('B13:C13')->setCellValue('B13', "Alat Berat & Truck ");
			$excel->setActiveSheetIndex(0)->setCellValue('C14', "Jasa Dermaga");
			$excel->setActiveSheetIndex(0)->setCellValue('C15', "< 28");
			$excel->setActiveSheetIndex(0)->setCellValue('C16', "> 28 - 33");
			$excel->setActiveSheetIndex(0)->setCellValue('C17', "> 33 - 40");
		    $excel->setActiveSheetIndex(0)->setCellValue('C18', "> 40 - 50");
			$excel->setActiveSheetIndex(0)->setCellValue('C19', "> 50 - 80");
			$excel->setActiveSheetIndex(0)->setCellValue('C20', "> 80 - 100");
			$excel->setActiveSheetIndex(0)->setCellValue('C21', "> 100");
			$excel->setActiveSheetIndex(0)->setCellValue('C22', "Stevedoring");

			$excel->setActiveSheetIndex(0)->setCellValue('C23', "OPP/OPT");
			$excel->setActiveSheetIndex(0)->setCellValue('C24', "< 28");
			$excel->setActiveSheetIndex(0)->setCellValue('C25', "> 28 - 33");
			$excel->setActiveSheetIndex(0)->setCellValue('C26', "> 33 - 40");
		    $excel->setActiveSheetIndex(0)->setCellValue('C27', "> 40 - 50");
			$excel->setActiveSheetIndex(0)->setCellValue('C28', "> 50 - 80");
			$excel->setActiveSheetIndex(0)->setCellValue('C29', "> 80 - 100");
			$excel->setActiveSheetIndex(0)->setCellValue('C30', "> 100");	

			$excel->setActiveSheetIndex(0)->setCellValue('C31', "Kebersihan");
			$excel->setActiveSheetIndex(0)->setCellValue('C32', "Penumpukan Masa I");
			$excel->setActiveSheetIndex(0)->setCellValue('C33', "Penumpukan Masa II");
			$excel->setActiveSheetIndex(0)->setCellValue('C34', "Penumpukan Masa III");
			$excel->setActiveSheetIndex(0)->mergeCells('B35:C35')->setCellValue('B35', "TOTAL ALAT BERAT & TRUCK");

			$excel->setActiveSheetIndex(0)->mergeCells('B37:C37')->setCellValue('B37', "General Cargo");			
			$excel->setActiveSheetIndex(0)->setCellValue('C38', "Jasa Dermaga");
			$excel->setActiveSheetIndex(0)->setCellValue('C39', "Parts");
			$excel->setActiveSheetIndex(0)->setCellValue('C40', "Lainnya (0-20 feet)");		
			$excel->setActiveSheetIndex(0)->setCellValue('C41', "Lainnya (diatas 20 feet)");
			$excel->setActiveSheetIndex(0)->setCellValue('C42', "Stevedoring");
			$excel->setActiveSheetIndex(0)->setCellValue('C43', "Parts");
			$excel->setActiveSheetIndex(0)->setCellValue('C44', "Lainnya");	

			$excel->setActiveSheetIndex(0)->setCellValue('C45', "OPP/OPT");
			$excel->setActiveSheetIndex(0)->setCellValue('C46', "< 5");
			$excel->setActiveSheetIndex(0)->setCellValue('C47', "> 5 - 10");
			$excel->setActiveSheetIndex(0)->setCellValue('C48', "> 10 - 25");		
			$excel->setActiveSheetIndex(0)->setCellValue('C49', "> 25");
			$excel->setActiveSheetIndex(0)->setCellValue('C50', "Kebersihan");
			$excel->setActiveSheetIndex(0)->setCellValue('C51', "Parts");
			$excel->setActiveSheetIndex(0)->setCellValue('C52', "Lainnya (0-20 feet)");	
			$excel->setActiveSheetIndex(0)->setCellValue('C53', "Lainnya (diatas 20 feet)");
			$excel->setActiveSheetIndex(0)->setCellValue('C54', "Penumpukan Masa I");
			$excel->setActiveSheetIndex(0)->setCellValue('C55', "Parts");
			$excel->setActiveSheetIndex(0)->setCellValue('C56', "Lainnya (0-20 feet)");	
			$excel->setActiveSheetIndex(0)->setCellValue('C57', "Lainnya (diatas 20 feet)");
			$excel->setActiveSheetIndex(0)->setCellValue('C58', "Penumpukan Masa II");
			$excel->setActiveSheetIndex(0)->setCellValue('C59', "Parts");
			$excel->setActiveSheetIndex(0)->setCellValue('C60', "Lainnya (0-20 feet)");	
			$excel->setActiveSheetIndex(0)->setCellValue('C61', "Lainnya (diatas 20 feet)");
			$excel->setActiveSheetIndex(0)->setCellValue('C62', "Penumpukan Masa III");
			$excel->setActiveSheetIndex(0)->setCellValue('C63', "Parts");
			$excel->setActiveSheetIndex(0)->setCellValue('C64', "Lainnya (0-20 feet)");	
			$excel->setActiveSheetIndex(0)->setCellValue('C65', "Lainnya (diatas 20 feet)");
			$excel->setActiveSheetIndex(0)->mergeCells('B66:C66')->setCellValue('B66', "TOTAL GENERAL CARGO");
		
			$excel->setActiveSheetIndex(0)->setCellValue('D4',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D5',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D6',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D7',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D8',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D9',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D10', 'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D11',  'UNIT');
		
			$excel->setActiveSheetIndex(0)->setCellValue('D15',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D16',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D17',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D18',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D19',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D20',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D21',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D22',  'UNIT'); 	

			$excel->setActiveSheetIndex(0)->setCellValue('D24',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D25',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D26',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D27',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D28',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D29',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D30',  'UNIT');

			$excel->setActiveSheetIndex(0)->setCellValue('D32',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D33',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D34',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D35',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D31',  'UNIT');
	
			$excel->setActiveSheetIndex(0)->setCellValue('D39',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D40',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D41',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D44',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D43',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D46',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D47',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D48',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D49',  'UNIT');

			$excel->setActiveSheetIndex(0)->setCellValue('D51',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D52',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D53',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D55',  'UNIT');	;
			$excel->setActiveSheetIndex(0)->setCellValue('D56',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D57',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D59',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D60',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D61',  'UNIT');
			$excel->setActiveSheetIndex(0)->setCellValue('D63',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D64',  'UNIT'); 
			$excel->setActiveSheetIndex(0)->setCellValue('D65',  'UNIT');			
			$excel->setActiveSheetIndex(0)->setCellValue('D66',  'UNIT');	

			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($style);
			$excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(11); 
			$excel->getActiveSheet()->getStyle('A1:V1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('A2:V2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B9')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B14')->getFont()->setBold(true);	

			$excel->getActiveSheet()->getStyle('Q1:Q2')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('Q12')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A1:A66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('B1:B66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('C1:C66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('D1:D66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('E1:E66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('F1:F66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('G1:G66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('H1:H66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('I1:I66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('J1:J66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('K1:K66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('L1:L66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('M1:M66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('N1:N66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('O1:O66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('P1:P66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('Q1:Q66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('R1:R66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('S1:S66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('T1:T66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('U1:U66')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('V1:V66')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('A3:V3')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A4:V4')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A5:V5')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A6:V6')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A7:V7')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A8:V8')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A9:V9')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A10:V10')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A11:V11')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A12:V12')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A13:V13')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A14:V14')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A15:V15')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A16:V16')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A17:V17')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A18:V18')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A19:V19')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A20:V20')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A21:V21')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A22:V22')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A23:V23')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A24:V24')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A25:V25')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A26:V26')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A27:V27')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A28:V28')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A29:V29')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A30:V30')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A31:V31')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A32:V32')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A33:V33')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A34:V34')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A35:V35')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A36:V36')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A37:V37')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A38:V38')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A39:V39')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A40:V40')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A41:V41')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A42:V42')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A43:V43')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A44:V44')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A45:V45')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A46:V46')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A47:V47')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A48:V48')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A49:V49')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A50:V50')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A51:V51')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A52:V52')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A53:V53')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A54:V54')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A55:V55')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A56:V56')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A57:V57')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A58:V58')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A59:V59')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A60:V60')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A61:V61')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A62:V62')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A63:V63')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A64:V64')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A65:V65')->applyFromArray($style_row);
			$excel->getActiveSheet()->getStyle('A66:V66')->applyFromArray($style_row);

			$excel->getActiveSheet()->getStyle('Q1:Q75')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B3:C3')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B22:C22')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B14:C14')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B13:C13')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B11')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C23')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B35')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B37')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C38')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C42')->getFont()->setBold(true);	
			$excel->getActiveSheet()->getStyle('B45:C45')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C54')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C50')->getFont()->setBold(true);	
			$excel->getActiveSheet()->getStyle('C58')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C62')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B66:C66')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('C70')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('B74:C74')->getFont()->setBold(true);

			$excel->getActiveSheet()->getStyle('E4:V4')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E5:V5')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E6:V6')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E7:V7')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");			
			$excel->getActiveSheet()->getStyle('E8:V8')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E9:V9')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E10:V10')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E11:V11')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E12:V12')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E13:V13')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E14:V14')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E15:V15')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E16:V16')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E17:V17')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E18:V18')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E19:V19')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E20:V20')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");	
			$excel->getActiveSheet()->getStyle('E21:V21')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E22:V22')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E23:V23')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E24:V24')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E25:V25')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E26:V26')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E27:V27')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E28:V28')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E29:V29')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E30:V30')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E31:V31')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E32:V32')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E33:V33')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E34:V34')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E35:V35')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E36:V36')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E37:V37')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E38:V38')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E39:V39')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E40:V40')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E41:V41')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E42:V42')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E43:V43')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E44:V44')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E45:V45')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E46:V46')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E47:V47')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E48:V48')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E49:V49')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E50:V50')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E51:V51')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E52:V52')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E53:V53')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E54:V54')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E55:V55')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E56:V56')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E57:V57')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E58:V58')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E59:V59')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E60:V60')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E61:V61')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E62:V62')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E63:V63')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E64:V64')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E65:V65')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E66:V66')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E67:V67')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E68:V68')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E69:V69')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E70:V70')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E71:V71')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E72:V72')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E73:V73')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E74:V74')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
			$excel->getActiveSheet()->getStyle('E75:V75')->getNumberFormat()->setFormatCode("_(* #,##0_);_(* \(#,##0\);_(* \"-\"??_);_(@_)");
				
			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('tps_online/Model_lap_pendapatan');
			$con = $this->load->database('ikt_postgree', TRUE);
	
			$model = $this->Model_lap_pendapatan->get_data_pendapatan($id,$end);

			$cont = count($model['data']);
			$x = 0;
			while($x < $cont) {
				$PERIODE = $model["data"][$x]['periode'];		
				 $x++;			
		
			$TAHUN = explode('-', $PERIODE);
			$YEAR = $TAHUN[0];
			$OLD = $YEAR - 1; 
			
			$tipe = str_replace('%20',' ',$type);

			if (!empty($YEAR)) {
				$excel->setActiveSheetIndex(0)->mergeCells('Q1:Q2')->setCellValue('Q1', "Realisasi Tahun $YEAR");
			}

			if ($tipe == 'PER SEMESTER' || $tipe == 'PER TRIWULAN') {
				$excel->setActiveSheetIndex(0)->mergeCells('R1:S1')->setCellValue('R1', $tipe);
				$excel->setActiveSheetIndex(0)->mergeCells('T1:U1')->setCellValue('T1', $tipe);
		
			}
			if ($tipe == 'PER TAHUN'){
				$excel->setActiveSheetIndex(0)->mergeCells('R1:T1')->setCellValue('R1', $tipe);
				$excel->setActiveSheetIndex(0)->setCellValue('R2',"Tarif");
				$excel->setActiveSheetIndex(0)->setCellValue('S2',"Pendapatan");
				$excel->setActiveSheetIndex(0)->setCellValue('T2',"Pendapatan s.d Desember");	
				$excel->setActiveSheetIndex(0)->setCellValue('U2',"");		
				$excel->setActiveSheetIndex(0)->setCellValue('V2',"");
			}
			$dates = "'yyyy-mm'";
			$luxury = "'%PASSENGER CAR LUXURY%'";
			$kebersihan = "'KEBERSIHAN'";
			$passenger = "'PASSENGER CAR'";
			$jasaDermaga = "'JASA DERMAGA'";
			$cargoHandling = "'CARGO HANDLING'";
			$steveDoring = "'STEVEDORING'";
			$kebersihan_alber = "'%KEBERSIHAN ALAT BERAT%'";
			$stevedoring_part = "'SPAREPART-OV'";
			$kebersihanPart = "'%KEBERSIHAN SPAREPART%'";
			$kebersihanLain = "'KEBERSIHAN BOLSTER 0 s/d 20 FEET'";
			$kebersihanAtas = "'KEBERSIHAN BOLSTER diatas 20 FEET'";
			$car4 = "'CAR04'";
			$car1 = "'CAR01'";
			$car8 = "'CAR08'";
			$car10 = "'CAR10'";
			$car7 = "'CAR07'";
			$masaI = "'PENUMPUKAN MASA I'";
			$masaII = "'PENUMPUKAN MASA II'";
			$masaIII = "'PENUMPUKAN MASA III'";
			$optA = "'0 s/d 28 TON/M3'";
			$optB = "'28 s/d 33 TON/M3'";
			$optC = "'33 s/d 40 TON/M3'";
			$optD = "'40 s/d 50 TON/M3'";
			$optE = "'50 s/d 80 TON/M3'";
			$optF = "'80 s/d 100 TON/M3'";
			$optG = "'diatas 100 TON/M3'";

			$uni = "'UNITIZED - OV'";
			$shift = "'SHIFTING BY LANDED'";
			$mavvi = "'MAFFI - OV'";
			$boolster = "'BOLSTER - OV'";
			$board = "'BOARD STEVEDORING'";
			$penumpukan = "'%PENUMPUKAN%'";
			$stevedoring = "'%STEVEDORING%'";
			$spare = "'%SPAREPART - OPP/OPT%'";

			$alatberat1 = '%ALAT BERAT - MASA1%';
			$alatberat2 = '%ALAT BERAT - MASA2%';
			$alatberat3 = '%ALAT BERAT - MASA3%';
			$optgcA = "'0 s/d 5 TON/M3'";
			$optgcB = "'5 s/d 10 TON/M3'";
			$optgcC = "'10 s/d 25 TON/M3'";
			$optgcD = "'diatas 25 TON/M3'";

			$spare1 = "'SPAREPART - MASA1'";
			$spare2 = "'BOLSTER - 0 s/d 20 FEET - MASA 1'";
			$spare3 = "'MAFFI - 0 s/d 20 FEET - MASA 1'";
			$spare4 = "'MAFFI - 0 s/d 20 FEET MASA1'";
			$spare5 = "'MAFFI - diatas 20 FEET MASA1'";
			$spare6 = "'MAFFI - diatas 20 FEET - MASA 1'";
			$spare7 = "'BOLSTER - diatas 20 FEET MASA1'";
			$spare8= "'SPAREPART - MASA2','SPAREPART - MASA 2'";
			$spare9 = "'BOLSTER - 0 s/d 20 FEET - MASA 2'";
			$spare10 = "'MAFFI - 0 s/d 20 FEET - MASA 2'";
			$spare11 = "'MAFFI - 0 s/d 20 FEET MASA2'";
			$spare12 = "'MAFFI - diatas 20 FEET MASA2'";
			$spare13 = "'MAFFI - diatas 20 FEET - MASA 2'";
			$spare14 = "'BOLSTER - diatas 20 FEET MASA2'";
			$spare15 = "'SPAREPART - MASA3','SPAREPART - MASA 3'";
			$spare16 = "'BOLSTER - 0 s/d 20 FEET - MASA 3'";
			$spare17 = "'MAFFI - 0 s/d 20 FEET - MASA 3'";
			$spare18 = "'MAFFI - 0 s/d 20 FEET MASA3'";
			$spare19 = "'MAFFI - diatas 20 FEET MASA3'";
			$spare20 = "'MAFFI - diatas 20 FEET - MASA 3'";
			$spare21 = "'BOLSTER - diatas 20 FEET MASA3'";	

			$alatberat1 = "'$alatberat1'";
			$alatberat2 = "'$alatberat2'";
			$alatberat3 = "'$alatberat3'";

			$terminalIntr = "'INTERNASIONAL'";
			$cbu = "'%CBU%'";
			$cbuLuxury = "'CBU LUXURY'";	
			$alberTruck = "'ALAT BERAT & TRUCK'";
			$oppt = "'OPP/OPT'";
			$generalCargo = "'GENERAL CARGO'";
			$part = "'PARTS'";
			$lain = "'LAINNYA'";
			$lainI = "'LAINNYA(0-20 FEET)'";
			$lainII = "'DIATAS 20 FEET'";

			$lainA = "'LAINNYA(0-20 FEET)'";
			$lainB = "'LAINNYA(DIATAS 20 FEET)'";

			$lainY = "'LAINNYA (0-20 FEET)'";
			$lainZ = "'LAINNYA (DIATAS 20 FEET)'";

			if ($PERIODE == ''.$YEAR.'-01'){
				
				$bulan1 = 'Januari';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data1 = $con->query($data_jasadermaga1)-> result_array();
				if ($data1){							
				$jasaDermaga1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaDermaga1 = 0;
				}

				$data_cargohandling1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI"  like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($data_cargohandling1)-> result_array();
				if ($data1){							
				$cargoHandling1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cargoHandling1 = 0;
				}

				$data_stevedoring1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($data_stevedoring1)-> result_array();
				if ($data1){							
				$steveDoring1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$steveDoring1 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI"  like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data1 = $con->query($data_kebersihan)-> result_array();
				if ($data1){							
				$kebersihan1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihan1 = 0;
				}
	
				$data_masa1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($data_masa1)-> result_array();
				if ($data1){							
				$masa11 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masa11 = 0;
				}

				$data_masa2 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.'  
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($data_masa2)-> result_array();
				if ($data1){							
				$masa21 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masa21 = 0;
				}

				$data_masa3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($data_masa3)-> result_array();
				if ($data1){							
				$masa31 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masa31 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($dermagaLuxury)-> result_array();
				if ($data1){							
				$dermagaLux1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$dermagaLux1 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($stevedoringLuxury)-> result_array();
				if ($data1){							
				$stevedoringLux1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$stevedoringLux1 = '';
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data1){							
				$cargohandlingLux1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cargohandlingLux1 = '';
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data1){							
				$kebersihanLux1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihanLux1 = '';
				}
					
				$masaILuxury1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($masaILuxury1)-> result_array();
				if ($data1){							
				$masaILux1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masaILux1 = '';
				}

				$masaIILuxury1 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($masaIILuxury1)-> result_array();
				if ($data1){							
				$masaIILux1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masaIILux1 = 0;
				}

				$masaIIILuxury1  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data1 = $con->query($masaIIILuxury1)-> result_array();
				if ($data1){							
				$masaIIILux1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masaIIILux1 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaA_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaA_alber1 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaB_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaB_alber1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaC_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaC_alber1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaD_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaD_alber1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaE_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaE_alber1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaF_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaF_alber1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaG_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaG_alber1 = 0;
				}	

				$stevedoring_alber1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data1 = $con->query($stevedoring_alber1)-> result_array();
				if ($data1){							
					$stevedoring_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
					$stevedoring_alber1 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data1 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data1){							
				$kebersihan_alber1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihan_alber1 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_malberI)-> result_array();
				if ($data1){							
				$malber11 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$malber11 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_malberII)-> result_array();
				if ($data1){							
				$malber21 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$malber21 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_malberIII)-> result_array();
				if ($data1){							
				$malber31 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$malber31 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$opta1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$opta1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optb1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optb1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optc1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optc1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optd1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optd1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$opte1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$opte1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optf1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optf1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optg1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optg1 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($dataCargo)-> result_array();
				if ($data1){							
				$jasaA_cargo1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaA_cargo1 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($dataCargo)-> result_array();
				if ($data1){							
				$jasaB_cargo1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaB_cargo1 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($dataCargo)-> result_array();
				if ($data1){							
				$jasaC_cargo1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaC_cargo1 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($data_stv)-> result_array();
				if ($data1){							
				$stevedoring_part1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$stevedoring_part1 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data1 = $con->query($data_stvo)-> result_array();
				if ($data1){							
				$stevedoring_lain1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$stevedoring_lain1 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data1 = $con->query($data_optgc)-> result_array();
				if ($data1){							
				$optgcA_1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optgcA_1 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($data_optgc)-> result_array();
				if ($data1){							
				$optgcB_1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optgcB_1 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($data_optgc)-> result_array();
				if ($data1){							
				$optgcC_1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optgcC_1 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($data_optgc)-> result_array();
				if ($data1){							
				$optgcD_1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optgcD_1 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data1 = $con->query($data_gcp)-> result_array();
				if ($data1){							
				$kebersihanPart1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihanPart1 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data1 = $con->query($data_gcp)-> result_array();
				if ($data1){							
				$kebersihanLain1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihanLain1 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data1 = $con->query($data_gcp)-> result_array();
				if ($data1){							
				$kebersihanAtas1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihanAtas1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaA1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaA1 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaB1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaB1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaC1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaC1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaD1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaD1 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaE1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaE1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaF1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaF1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaG1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaG1 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaH1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaH1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaI1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaI1 = 0;
				}
			
			}

			if ($PERIODE == ''.$YEAR.'-02'){
				$bulan2 = 'Februari';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI"  like '.$cbu.'  
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data2 = $con->query($data_jasadermaga2)-> result_array();
				if ($data2){							
				$jasaDermaga2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaDermaga2 = 0;
				}

				$data_cargohandling2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI"  like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($data_cargohandling2)-> result_array();
				if ($data2){							
				$cargoHandling2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cargoHandling2 = 0;
				}

				$data_stevedoring2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($data_stevedoring2)-> result_array();
				if ($data2){							
				$steveDoring2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$steveDoring2 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data2 = $con->query($data_kebersihan)-> result_array();
				if ($data2){							
				$kebersihan2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$kebersihan2 = 0;
				}
	
				$data_masa2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($data_masa2)-> result_array();
				if ($data2){							
				$masa12 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$masa12 = 0;
				}

				$data_masa2 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($data_masa2)-> result_array();
				if ($data2){							
				$masa22 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$masa22 = 0;
				}

				$data_masa3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($data_masa3)-> result_array();
				if ($data2){							
				$masa32 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$masa32 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($dermagaLuxury)-> result_array();
				if ($data2){							
				$dermagaLux2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$dermagaLux2 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($stevedoringLuxury)-> result_array();
				if ($data2){							
				$stevedoringLux2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$stevedoringLux2 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data2){							
				$cargohandlingLux2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$cargohandlingLux2 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data2){							
				$kebersihanLux2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$kebersihanLux2 = 0;
				}
					
				$masaILuxury2 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($masaILuxury2)-> result_array();
				if ($data2){							
				$masaILux2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$masaILux2 = 0;
				}

				$masaIILuxury2 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($masaIILuxury2)-> result_array();
				if ($data2){							
				$masaIILux2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$masaIILux2 = 0;
				}

				$masaIIILuxury2  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data2 = $con->query($masaIIILuxury2)-> result_array();
				if ($data2){							
				$masaIIILux2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$masaIIILux2 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data2 = $con->query($dermagaAlber)-> result_array();
				if ($data2){							
				$jasaA_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaA_alber2 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data2 = $con->query($dermagaAlber)-> result_array();
				if ($data2){							
				$jasaB_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaB_alber2 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data2 = $con->query($dermagaAlber)-> result_array();
				if ($data2){							
				$jasaC_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaC_alber2 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data2 = $con->query($dermagaAlber)-> result_array();
				if ($data2){							
				$jasaD_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaD_alber2 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data2 = $con->query($dermagaAlber)-> result_array();
				if ($data2){							
				$jasaE_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaE_alber2 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data2 = $con->query($dermagaAlber)-> result_array();
				if ($data2){							
				$jasaF_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaF_alber2 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data2 = $con->query($dermagaAlber)-> result_array();
				if ($data2){							
				$jasaG_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaG_alber2 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data2 = $con->query($stevedoring_alber)-> result_array();
				if ($data2){							
					$stevedoring_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
					$stevedoring_alber2 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data2 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data2){							
				$kebersihan_alber2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$kebersihan_alber2 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_malberI)-> result_array();
				if ($data2){							
				$malber12 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$malber12 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_malberII)-> result_array();
				if ($data2){							
				$malber22 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$malber22 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_malberIII)-> result_array();
				if ($data2){							
				$malber32 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$malber32 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_opt)-> result_array();
				if ($data2){							
				$opta2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$opta2 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_opt)-> result_array();
				if ($data2){							
				$optb2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optb2 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_opt)-> result_array();
				if ($data2){							
				$optc2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optc2 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_opt)-> result_array();
				if ($data2){							
				$optd2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optd2 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_opt)-> result_array();
				if ($data2){							
				$opte2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$opte2 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_opt)-> result_array();
				if ($data2){							
				$optf2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optf2 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_opt)-> result_array();
				if ($data2){							
				$optg2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optg2 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($dataCargo)-> result_array();
				if ($data2){							
				$jasaA_cargo2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaA_cargo2 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($dataCargo)-> result_array();
				if ($data2){							
				$jasaB_cargo2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaB_cargo2 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($dataCargo)-> result_array();
				if ($data2){							
				$jasaC_cargo2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$jasaC_cargo2 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($data_stv)-> result_array();
				if ($data2){							
				$stevedoring_part2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$stevedoring_part2 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data2 = $con->query($data_stvo)-> result_array();
				if ($data2){							
				$stevedoring_lain2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$stevedoring_lain2 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data2 = $con->query($data_optgc)-> result_array();
				if ($data2){							
				$optgcA_2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optgcA_2 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($data_optgc)-> result_array();
				if ($data2){							
				$optgcB_2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optgcB_2 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($data_optgc)-> result_array();
				if ($data2){							
				$optgcC_2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optgcC_2 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($data_optgc)-> result_array();
				if ($data2){							
				$optgcD_2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$optgcD_2 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data2 = $con->query($data_gcp)-> result_array();
				if ($data2){							
				$kebersihanPart2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$kebersihanPart2 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data2 = $con->query($data_gcp)-> result_array();
				if ($data2){							
				$kebersihanLain2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$kebersihanLain2 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data2 = $con->query($data_gcp)-> result_array();
				if ($data2){							
				$kebersihanAtas2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$kebersihanAtas2 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaA2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaA2 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaB2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaB2 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaC2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaC2 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaD2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaD2 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaE2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaE2 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaF2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaF2 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaG2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaG2 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaH2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaH2 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data2 = $con->query($data_penumpukan)-> result_array();
				if ($data2){							
				$MasaI2 = $data2[0]['TOTAL'];
				} else if (empty($data2))  {		
				$MasaI2 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-03'){
				$bulan3 = 'Maret';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data3 = $con->query($data_jasadermaga3)-> result_array();
				if ($data3){							
				$jasaDermaga3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaDermaga3 = 0;
				}

				$data_cargohandling3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($data_cargohandling3)-> result_array();
				if ($data3){							
				$cargoHandling3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cargoHandling3 = 0;
				}

				$data_stevedoring3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($data_stevedoring3)-> result_array();
				if ($data3){							
				$steveDoring3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$steveDoring3 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data3 = $con->query($data_kebersihan)-> result_array();
				if ($data3){							
				$kebersihan3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$kebersihan3 = 0;
				}
	
				$data_masa1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($data_masa1)-> result_array();
				if ($data3){							
				$masa13 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$masa13 = 0;
				}

				$data_masa2 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($data_masa2)-> result_array();
				if ($data3){							
				$masa23 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$masa23 = 0;
				}

				$data_masa3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($data_masa3)-> result_array();
				if ($data3){							
				$masa33 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$masa33 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($dermagaLuxury)-> result_array();
				if ($data3){							
				$dermagaLux3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$dermagaLux3 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($stevedoringLuxury)-> result_array();
				if ($data3){							
				$stevedoringLux3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$stevedoringLux3 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data3){							
				$cargohandlingLux3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$cargohandlingLux3 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data3){							
				$kebersihanLux3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$kebersihanLux3 = 0;
				}
					
				$masaILuxury3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($masaILuxury3)-> result_array();
				if ($data3){							
				$masaILux3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$masaILux3 = 0;
				}

				$masaIILuxury3 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($masaIILuxury3)-> result_array();
				if ($data3){							
				$masaIILux3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$masaIILux3 = 0;
				}

				$masaIIILuxury3  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data3 = $con->query($masaIIILuxury3)-> result_array();
				if ($data3){							
				$masaIIILux3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$masaIIILux3 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data3 = $con->query($dermagaAlber)-> result_array();
				if ($data3){							
				$jasaA_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaA_alber3 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data3 = $con->query($dermagaAlber)-> result_array();
				if ($data3){							
				$jasaB_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaB_alber3 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data3 = $con->query($dermagaAlber)-> result_array();
				if ($data3){							
				$jasaC_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaC_alber3 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data3 = $con->query($dermagaAlber)-> result_array();
				if ($data3){							
				$jasaD_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaD_alber3 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data3 = $con->query($dermagaAlber)-> result_array();
				if ($data3){							
				$jasaE_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaE_alber3 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data3 = $con->query($dermagaAlber)-> result_array();
				if ($data3){							
				$jasaF_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaF_alber3 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data3 = $con->query($dermagaAlber)-> result_array();
				if ($data3){							
				$jasaG_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaG_alber3 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data3 = $con->query($stevedoring_alber)-> result_array();
				if ($data3){							
					$stevedoring_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
					$stevedoring_alber3 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data3 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data3){							
				$kebersihan_alber3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$kebersihan_alber3 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_malberI)-> result_array();
				if ($data3){							
				$malber13 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$malber13 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_malberII)-> result_array();
				if ($data3){							
				$malber23 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$malber23 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_malberIII)-> result_array();
				if ($data3){							
				$malber33 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$malber33 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_opt)-> result_array();
				if ($data3){							
				$opta3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$opta3 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_opt)-> result_array();
				if ($data3){							
				$optb3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optb3 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_opt)-> result_array();
				if ($data3){							
				$optc3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optc3 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_opt)-> result_array();
				if ($data3){							
				$optd3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optd3 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_opt)-> result_array();
				if ($data3){							
				$opte3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$opte3 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_opt)-> result_array();
				if ($data3){							
				$optf3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optf3 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_opt)-> result_array();
				if ($data3){							
				$optg3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optg3 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($dataCargo)-> result_array();
				if ($data3){							
				$jasaA_cargo3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaA_cargo3 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($dataCargo)-> result_array();
				if ($data3){							
				$jasaB_cargo3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaB_cargo3 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($dataCargo)-> result_array();
				if ($data3){							
				$jasaC_cargo3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$jasaC_cargo3 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($data_stv)-> result_array();
				if ($data3){							
				$stevedoring_part3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$stevedoring_part3 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data3 = $con->query($data_stvo)-> result_array();
				if ($data3){							
				$stevedoring_lain3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$stevedoring_lain3 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data3 = $con->query($data_optgc)-> result_array();
				if ($data3){							
				$optgcA_3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optgcA_3 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($data_optgc)-> result_array();
				if ($data3){							
				$optgcB_3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optgcB_3 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($data_optgc)-> result_array();
				if ($data3){							
				$optgcC_3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optgcC_3 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($data_optgc)-> result_array();
				if ($data3){							
				$optgcD_3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$optgcD_3 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data3 = $con->query($data_gcp)-> result_array();
				if ($data3){							
				$kebersihanPart3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$kebersihanPart3 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data3 = $con->query($data_gcp)-> result_array();
				if ($data3){							
				$kebersihanLain3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$kebersihanLain3 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data3 = $con->query($data_gcp)-> result_array();
				if ($data3){							
				$kebersihanAtas3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$kebersihanAtas3 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaA3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaA3 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaB3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaB3 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaC3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaC3 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaD3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaD3 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaE3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaE3 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaF3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaF3 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaG3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaG3 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaH3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaH3 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data3 = $con->query($data_penumpukan)-> result_array();
				if ($data3){							
				$MasaI3 = $data3[0]['TOTAL'];
				} else if (empty($data3))  {		
				$MasaI3 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-04'){
				$bulan4 = 'April';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data4 = $con->query($data_jasadermaga4)-> result_array();
				if ($data4){							
				$jasaDermaga4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaDermaga4 = 0;
				}

				$data_cargohandling4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($data_cargohandling4)-> result_array();
				if ($data4){							
				$cargoHandling4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cargoHandling4 = 0;
				}

				$data_stevedoring4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($data_stevedoring4)-> result_array();
				if ($data4){							
				$steveDoring4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$steveDoring4 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data4 = $con->query($data_kebersihan)-> result_array();
				if ($data4){							
				$kebersihan4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$kebersihan4 = 0;
				}
	
				$data_masa1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($data_masa1)-> result_array();
				if ($data4){							
				$masa14 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$masa14 = 0;
				}

				$data_masa2 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($data_masa2)-> result_array();
				if ($data4){							
				$masa24 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$masa24 = 0;
				}

				$data_masa3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($data_masa3)-> result_array();
				if ($data4){							
				$masa34 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$masa34 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($dermagaLuxury)-> result_array();
				if ($data4){							
				$dermagaLux4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$dermagaLux4 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($stevedoringLuxury)-> result_array();
				if ($data4){							
				$stevedoringLux4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$stevedoringLux4 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data4){							
				$cargohandlingLux4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$cargohandlingLux4 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data4){							
				$kebersihanLux4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$kebersihanLux4 = 0;
				}
					
				$masaILuxury4 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($masaILuxury4)-> result_array();
				if ($data4){							
				$masaILux4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$masaILux4 = 0;
				}

				$masaIILuxury4 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($masaIILuxury4)-> result_array();
				if ($data4){							
				$masaIILux4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$masaIILux4 = 0;
				}

				$masaIIILuxury4  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data4 = $con->query($masaIIILuxury4)-> result_array();
				if ($data4){							
				$masaIIILux4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$masaIIILux4 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data4 = $con->query($dermagaAlber)-> result_array();
				if ($data4){							
				$jasaA_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaA_alber4 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data4 = $con->query($dermagaAlber)-> result_array();
				if ($data4){							
				$jasaB_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaB_alber4 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data4 = $con->query($dermagaAlber)-> result_array();
				if ($data4){							
				$jasaC_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaC_alber4 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data4 = $con->query($dermagaAlber)-> result_array();
				if ($data4){							
				$jasaD_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaD_alber4 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data4 = $con->query($dermagaAlber)-> result_array();
				if ($data4){							
				$jasaE_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaE_alber4 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data4 = $con->query($dermagaAlber)-> result_array();
				if ($data4){							
				$jasaF_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaF_alber4 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data4 = $con->query($dermagaAlber)-> result_array();
				if ($data4){							
				$jasaG_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaG_alber4 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data4 = $con->query($stevedoring_alber)-> result_array();
				if ($data4){							
					$stevedoring_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
					$stevedoring_alber4 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data4 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data4){							
				$kebersihan_alber4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$kebersihan_alber4 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_malberI)-> result_array();
				if ($data4){							
				$malber14 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$malber14 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_malberII)-> result_array();
				if ($data4){							
				$malber24 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$malber24 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_malberIII)-> result_array();
				if ($data4){							
				$malber34 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$malber34 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_opt)-> result_array();
				if ($data4){							
				$opta4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$opta4 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_opt)-> result_array();
				if ($data4){							
				$optb4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optb4 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_opt)-> result_array();
				if ($data4){							
				$optc4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optc4 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_opt)-> result_array();
				if ($data4){							
				$optd4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optd4 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_opt)-> result_array();
				if ($data4){							
				$opte4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$opte4 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_opt)-> result_array();
				if ($data4){							
				$optf4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optf4 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_opt)-> result_array();
				if ($data4){							
				$optg4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optg4 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($dataCargo)-> result_array();
				if ($data4){							
				$jasaA_cargo4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaA_cargo4 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($dataCargo)-> result_array();
				if ($data4){							
				$jasaB_cargo4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaB_cargo4 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($dataCargo)-> result_array();
				if ($data4){							
				$jasaC_cargo4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$jasaC_cargo4 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($data_stv)-> result_array();
				if ($data4){							
				$stevedoring_part4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$stevedoring_part4 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data4 = $con->query($data_stvo)-> result_array();
				if ($data4){							
				$stevedoring_lain4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$stevedoring_lain4 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data4 = $con->query($data_optgc)-> result_array();
				if ($data4){							
				$optgcA_4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optgcA_4 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($data_optgc)-> result_array();
				if ($data4){							
				$optgcB_4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optgcB_4 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($data_optgc)-> result_array();
				if ($data4){							
				$optgcC_4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optgcC_4 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($data_optgc)-> result_array();
				if ($data4){							
				$optgcD_4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$optgcD_4 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data4 = $con->query($data_gcp)-> result_array();
				if ($data4){							
				$kebersihanPart4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$kebersihanPart4 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data4 = $con->query($data_gcp)-> result_array();
				if ($data4){							
				$kebersihanLain4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$kebersihanLain4 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data4 = $con->query($data_gcp)-> result_array();
				if ($data4){							
				$kebersihanAtas4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$kebersihanAtas4 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaA4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaA4 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaB4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaB4 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaC4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaC4 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaD4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaD4 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaE4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaE4 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaF4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaF4 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaG4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaG4 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaH4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaH4 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data4 = $con->query($data_penumpukan)-> result_array();
				if ($data4){							
				$MasaI4 = $data4[0]['TOTAL'];
				} else if (empty($data4))  {		
				$MasaI4 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-05'){
				$bulan5 = 'Mei';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data5 = $con->query($data_jasadermaga5)-> result_array();
				if ($data5){							
				$jasaDermaga5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaDermaga5 = 0;
				}

				$data_cargohandling5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($data_cargohandling5)-> result_array();
				if ($data5){							
				$cargoHandling5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cargoHandling5 = 0;
				}

				$data_stevedoring5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($data_stevedoring5)-> result_array();
				if ($data5){							
				$steveDoring5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$steveDoring5 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data5 = $con->query($data_kebersihan)-> result_array();
				if ($data5){							
				$kebersihan5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$kebersihan5 = 0;
				}
	
				$data_masa1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($data_masa1)-> result_array();
				if ($data5){							
				$masa15 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$masa15 = 0;
				}

				$data_masa2 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($data_masa2)-> result_array();
				if ($data5){							
				$masa25 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$masa25 = 0;
				}

				$data_masa3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($data_masa3)-> result_array();
				if ($data5){							
				$masa35 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$masa35 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($dermagaLuxury)-> result_array();
				if ($data5){							
				$dermagaLux5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$dermagaLux5 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($stevedoringLuxury)-> result_array();
				if ($data5){							
				$stevedoringLux5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$stevedoringLux5 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data5){							
				$cargohandlingLux5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$cargohandlingLux5 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data5){							
				$kebersihanLux5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$kebersihanLux5 = 0;
				}
					
				$masaILuxury5 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($masaILuxury5)-> result_array();
				if ($data5){							
				$masaILux5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$masaILux5 = 0;
				}

				$masaIILuxury5 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($masaIILuxury5)-> result_array();
				if ($data5){							
				$masaIILux5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$masaIILux5 = 0;
				}

				$masaIIILuxury5  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data5 = $con->query($masaIIILuxury5)-> result_array();
				if ($data5){							
				$masaIIILux5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$masaIIILux5 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data5 = $con->query($dermagaAlber)-> result_array();
				if ($data5){							
				$jasaA_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaA_alber5 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data5 = $con->query($dermagaAlber)-> result_array();
				if ($data5){							
				$jasaB_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaB_alber5 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data5 = $con->query($dermagaAlber)-> result_array();
				if ($data5){							
				$jasaC_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaC_alber5 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data5 = $con->query($dermagaAlber)-> result_array();
				if ($data5){							
				$jasaD_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaD_alber5 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data5 = $con->query($dermagaAlber)-> result_array();
				if ($data5){							
				$jasaE_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaE_alber5 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data5 = $con->query($dermagaAlber)-> result_array();
				if ($data5){							
				$jasaF_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaF_alber5 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data5 = $con->query($dermagaAlber)-> result_array();
				if ($data5){							
				$jasaG_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaG_alber5 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data5 = $con->query($stevedoring_alber)-> result_array();
				if ($data5){							
					$stevedoring_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
					$stevedoring_alber5 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data5 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data5){							
				$kebersihan_alber5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$kebersihan_alber5 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_malberI)-> result_array();
				if ($data5){							
				$malber15 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$malber15 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_malberII)-> result_array();
				if ($data5){							
				$malber25 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$malber25 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_malberIII)-> result_array();
				if ($data5){							
				$malber35 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$malber35 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_opt)-> result_array();
				if ($data5){							
				$opta5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$opta5 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_opt)-> result_array();
				if ($data5){							
				$optb5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optb5 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_opt)-> result_array();
				if ($data5){							
				$optc5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optc5 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_opt)-> result_array();
				if ($data5){							
				$optd5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optd5 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_opt)-> result_array();
				if ($data5){							
				$opte5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$opte5 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_opt)-> result_array();
				if ($data5){							
				$optf5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optf5 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_opt)-> result_array();
				if ($data5){							
				$optg5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optg5 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($dataCargo)-> result_array();
				if ($data5){							
				$jasaA_cargo5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaA_cargo5 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($dataCargo)-> result_array();
				if ($data5){							
				$jasaB_cargo5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaB_cargo5 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($dataCargo)-> result_array();
				if ($data5){							
				$jasaC_cargo5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$jasaC_cargo5 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($data_stv)-> result_array();
				if ($data5){							
				$stevedoring_part5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$stevedoring_part5 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data5 = $con->query($data_stvo)-> result_array();
				if ($data5){							
				$stevedoring_lain5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$stevedoring_lain5 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data5 = $con->query($data_optgc)-> result_array();
				if ($data5){							
				$optgcA_5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optgcA_5 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($data_optgc)-> result_array();
				if ($data5){							
				$optgcB_5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optgcB_5 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($data_optgc)-> result_array();
				if ($data5){							
				$optgcC_5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optgcC_5 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($data_optgc)-> result_array();
				if ($data5){							
				$optgcD_5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$optgcD_5 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data5 = $con->query($data_gcp)-> result_array();
				if ($data5){							
				$kebersihanPart5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$kebersihanPart5 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data5 = $con->query($data_gcp)-> result_array();
				if ($data5){							
				$kebersihanLain5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$kebersihanLain5 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data5 = $con->query($data_gcp)-> result_array();
				if ($data5){							
				$kebersihanAtas5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$kebersihanAtas5 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaA5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaA5 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaB5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaB5 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaC5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaC5 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaD5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaD5 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaE5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaE5 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaF5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaF5 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaG5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaG5 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaH5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaH5 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data5 = $con->query($data_penumpukan)-> result_array();
				if ($data5){							
				$MasaI5 = $data5[0]['TOTAL'];
				} else if (empty($data5))  {		
				$MasaI5 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-06'){
				$bulan6 = 'Juni';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data6 = $con->query($data_jasadermaga6)-> result_array();
				if ($data6){							
				$jasaDermaga6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaDermaga6 = 0;
				}

				$data_cargohandling6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($data_cargohandling6)-> result_array();
				if ($data6){							
				$cargoHandling6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cargoHandling6 = 0;
				}

				$data_stevedoring6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($data_stevedoring6)-> result_array();
				if ($data6){							
				$steveDoring6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$steveDoring6 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data6 = $con->query($data_kebersihan)-> result_array();
				if ($data6){							
				$kebersihan6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$kebersihan6 = 0;
				}
	
				$data_masa1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($data_masa1)-> result_array();
				if ($data6){							
				$masa16 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$masa16 = 0;
				}

				$data_masa2 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($data_masa2)-> result_array();
				if ($data6){							
				$masa26 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$masa26 = 0;
				}

				$data_masa3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($data_masa3)-> result_array();
				if ($data6){							
				$masa36 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$masa36 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($dermagaLuxury)-> result_array();
				if ($data6){							
				$dermagaLux6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$dermagaLux6 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($stevedoringLuxury)-> result_array();
				if ($data6){							
				$stevedoringLux6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$stevedoringLux6 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data6){							
				$cargohandlingLux6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$cargohandlingLux6 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data6){							
				$kebersihanLux6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$kebersihanLux6 = 0;
				}
					
				$masaILuxury6 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($masaILuxury6)-> result_array();
				if ($data6){							
				$masaILux6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$masaILux6 = 0;
				}

				$masaIILuxury6 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($masaIILuxury6)-> result_array();
				if ($data6){							
				$masaIILux6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$masaIILux6 = 0;
				}

				$masaIIILuxury6  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data6 = $con->query($masaIIILuxury6)-> result_array();
				if ($data6){							
				$masaIIILux6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$masaIIILux6 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data6 = $con->query($dermagaAlber)-> result_array();
				if ($data6){							
				$jasaA_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaA_alber6 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data6 = $con->query($dermagaAlber)-> result_array();
				if ($data6){							
				$jasaB_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaB_alber6 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data6 = $con->query($dermagaAlber)-> result_array();
				if ($data6){							
				$jasaC_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaC_alber6 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data6 = $con->query($dermagaAlber)-> result_array();
				if ($data6){							
				$jasaD_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaD_alber6 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data6 = $con->query($dermagaAlber)-> result_array();
				if ($data6){							
				$jasaE_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaE_alber6 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data6 = $con->query($dermagaAlber)-> result_array();
				if ($data6){							
				$jasaF_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaF_alber6 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data6 = $con->query($dermagaAlber)-> result_array();
				if ($data6){							
				$jasaG_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaG_alber6 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data6 = $con->query($stevedoring_alber)-> result_array();
				if ($data6){							
					$stevedoring_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
					$stevedoring_alber6 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data6 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data6){							
				$kebersihan_alber6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$kebersihan_alber6 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_malberI)-> result_array();
				if ($data6){							
				$malber16 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$malber16 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_malberII)-> result_array();
				if ($data6){							
				$malber26 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$malber26 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_malberIII)-> result_array();
				if ($data6){							
				$malber36 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$malber36 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_opt)-> result_array();
				if ($data6){							
				$opta6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$opta6 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_opt)-> result_array();
				if ($data6){							
				$optb6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optb6 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_opt)-> result_array();
				if ($data6){							
				$optc6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optc6 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_opt)-> result_array();
				if ($data6){							
				$optd6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optd6 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_opt)-> result_array();
				if ($data6){							
				$opte6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$opte6 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_opt)-> result_array();
				if ($data6){							
				$optf6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optf6 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_opt)-> result_array();
				if ($data6){							
				$optg6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optg6 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($dataCargo)-> result_array();
				if ($data6){							
				$jasaA_cargo6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaA_cargo6 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($dataCargo)-> result_array();
				if ($data6){							
				$jasaB_cargo6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaB_cargo6 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($dataCargo)-> result_array();
				if ($data6){							
				$jasaC_cargo6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$jasaC_cargo6 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($data_stv)-> result_array();
				if ($data6){							
				$stevedoring_part6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$stevedoring_part6 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data6 = $con->query($data_stvo)-> result_array();
				if ($data6){							
				$stevedoring_lain6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$stevedoring_lain6 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data6 = $con->query($data_optgc)-> result_array();
				if ($data6){							
				$optgcA_6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optgcA_6 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($data_optgc)-> result_array();
				if ($data6){							
				$optgcB_6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optgcB_6 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($data_optgc)-> result_array();
				if ($data6){							
				$optgcC_6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optgcC_6 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($data_optgc)-> result_array();
				if ($data6){							
				$optgcD_6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$optgcD_6 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data6 = $con->query($data_gcp)-> result_array();
				if ($data6){							
				$kebersihanPart6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$kebersihanPart6 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data6 = $con->query($data_gcp)-> result_array();
				if ($data6){							
				$kebersihanLain6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$kebersihanLain6 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data6 = $con->query($data_gcp)-> result_array();
				if ($data6){							
				$kebersihanAtas6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$kebersihanAtas6 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaA6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaA6 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaB6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaB6 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaC6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaC6 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaD6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaD6 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaE6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaE6 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaF6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaF6 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaG6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaG6 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaH6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaH6 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data6 = $con->query($data_penumpukan)-> result_array();
				if ($data6){							
				$MasaI6 = $data6[0]['TOTAL'];
				} else if (empty($data6))  {		
				$MasaI6 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-07'){
				$bulan7 = 'Juli';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data7 = $con->query($data_jasadermaga7)-> result_array();
				if ($data7){							
				$jasaDermaga7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaDermaga7 = 0;
				}

				$data_cargohandling7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($data_cargohandling7)-> result_array();
				if ($data7){							
				$cargoHandling7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cargoHandling7 = 0;
				}

				$data_stevedoring7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($data_stevedoring7)-> result_array();
				if ($data7){							
				$steveDoring7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$steveDoring7 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data7 = $con->query($data_kebersihan)-> result_array();
				if ($data7){							
				$kebersihan7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$kebersihan7 = 0;
				}
	
				$data_masa7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($data_masa7)-> result_array();
				if ($data7){							
				$masa17 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$masa17 = 0;
				}

				$data_masa7 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($data_masa7)-> result_array();
				if ($data7){							
				$masa27 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$masa27 = 0;
				}

				$data_masa7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($data_masa7)-> result_array();
				if ($data7){							
				$masa37 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$masa37 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($dermagaLuxury)-> result_array();
				if ($data7){							
				$dermagaLux7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$dermagaLux7 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($stevedoringLuxury)-> result_array();
				if ($data7){							
				$stevedoringLux7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$stevedoringLux7 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data7){							
				$cargohandlingLux7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$cargohandlingLux7 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data7){							
				$kebersihanLux7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$kebersihanLux7 = 0;
				}
					
				$masaILuxury7 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($masaILuxury7)-> result_array();
				if ($data7){							
				$masaILux7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$masaILux7 = 0;
				}

				$masaIILuxury7 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($masaIILuxury7)-> result_array();
				if ($data7){							
				$masaIILux7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$masaIILux7 = 0;
				}

				$masaIIILuxury7  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data7 = $con->query($masaIIILuxury7)-> result_array();
				if ($data7){							
				$masaIIILux7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$masaIIILux7 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data7 = $con->query($dermagaAlber)-> result_array();
				if ($data7){							
				$jasaA_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaA_alber7 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data7 = $con->query($dermagaAlber)-> result_array();
				if ($data7){							
				$jasaB_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaB_alber7 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data7 = $con->query($dermagaAlber)-> result_array();
				if ($data7){							
				$jasaC_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaC_alber7 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data7 = $con->query($dermagaAlber)-> result_array();
				if ($data7){							
				$jasaD_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaD_alber7 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data7 = $con->query($dermagaAlber)-> result_array();
				if ($data7){							
				$jasaE_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaE_alber7 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data7 = $con->query($dermagaAlber)-> result_array();
				if ($data7){							
				$jasaF_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaF_alber7 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data7 = $con->query($dermagaAlber)-> result_array();
				if ($data7){							
				$jasaG_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaG_alber7 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data7 = $con->query($stevedoring_alber)-> result_array();
				if ($data7){							
					$stevedoring_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
					$stevedoring_alber7 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data7 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data7){							
				$kebersihan_alber7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$kebersihan_alber7 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_malberI)-> result_array();
				if ($data7){							
				$malber17 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$malber17 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_malberII)-> result_array();
				if ($data7){							
				$malber27 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$malber27 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_malberIII)-> result_array();
				if ($data7){							
				$malber37 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$malber37 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_opt)-> result_array();
				if ($data7){							
				$opta7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$opta7 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_opt)-> result_array();
				if ($data7){							
				$optb7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optb7 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_opt)-> result_array();
				if ($data7){							
				$optc7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optc7 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_opt)-> result_array();
				if ($data7){							
				$optd7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optd7 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_opt)-> result_array();
				if ($data7){							
				$opte7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$opte7 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_opt)-> result_array();
				if ($data7){							
				$optf7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optf7 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_opt)-> result_array();
				if ($data7){							
				$optg7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optg7 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($dataCargo)-> result_array();
				if ($data7){							
				$jasaA_cargo7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaA_cargo7 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($dataCargo)-> result_array();
				if ($data7){							
				$jasaB_cargo7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaB_cargo7 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($dataCargo)-> result_array();
				if ($data7){							
				$jasaC_cargo7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$jasaC_cargo7 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($data_stv)-> result_array();
				if ($data7){							
				$stevedoring_part7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$stevedoring_part7 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data7 = $con->query($data_stvo)-> result_array();
				if ($data7){							
				$stevedoring_lain7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$stevedoring_lain7 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data7 = $con->query($data_optgc)-> result_array();
				if ($data7){							
				$optgcA_7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optgcA_7 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($data_optgc)-> result_array();
				if ($data7){							
				$optgcB_7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optgcB_7 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($data_optgc)-> result_array();
				if ($data7){							
				$optgcC_7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optgcC_7 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($data_optgc)-> result_array();
				if ($data7){							
				$optgcD_7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$optgcD_7 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data7 = $con->query($data_gcp)-> result_array();
				if ($data7){							
				$kebersihanPart7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$kebersihanPart7 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data7 = $con->query($data_gcp)-> result_array();
				if ($data7){							
				$kebersihanLain7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$kebersihanLain7 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data7 = $con->query($data_gcp)-> result_array();
				if ($data7){							
				$kebersihanAtas7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$kebersihanAtas7 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaA7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaA7 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaB7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaB7 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaC7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaC7 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaD7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaD7 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaE7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaE7 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaF7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaF7 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaG7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaG7 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaH7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaH7 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data7 = $con->query($data_penumpukan)-> result_array();
				if ($data7){							
				$MasaI7 = $data7[0]['TOTAL'];
				} else if (empty($data7))  {		
				$MasaI7 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-08'){
				$bulan8 = 'Agustus';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data8 = $con->query($data_jasadermaga8)-> result_array();
				if ($data8){							
				$jasaDermaga8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaDermaga8 = 0;
				}

				$data_cargohandling8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($data_cargohandling8)-> result_array();
				if ($data8){							
				$cargoHandling8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cargoHandling8 = 0;
				}

				$data_stevedoring8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($data_stevedoring8)-> result_array();
				if ($data8){							
				$steveDoring8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$steveDoring8 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data8 = $con->query($data_kebersihan)-> result_array();
				if ($data8){							
				$kebersihan8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$kebersihan8 = 0;
				}
	
				$data_masa8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($data_masa8)-> result_array();
				if ($data8){							
				$masa18 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$masa18 = 0;
				}

				$data_masa8 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($data_masa8)-> result_array();
				if ($data8){							
				$masa28 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$masa28 = 0;
				}

				$data_masa8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($data_masa8)-> result_array();
				if ($data8){							
				$masa38 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$masa38 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($dermagaLuxury)-> result_array();
				if ($data8){							
				$dermagaLux8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$dermagaLux8 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($stevedoringLuxury)-> result_array();
				if ($data8){							
				$stevedoringLux8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$stevedoringLux8 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data8){							
				$cargohandlingLux8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$cargohandlingLux8 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data8){							
				$kebersihanLux8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$kebersihanLux8 = 0;
				}
					
				$masaILuxury8 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($masaILuxury8)-> result_array();
				if ($data8){							
				$masaILux8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$masaILux8 = 0;
				}

				$masaIILuxury8 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($masaIILuxury8)-> result_array();
				if ($data8){							
				$masaIILux8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$masaIILux8 = 0;
				}

				$masaIIILuxury8  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data8 = $con->query($masaIIILuxury8)-> result_array();
				if ($data8){							
				$masaIIILux8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$masaIIILux8 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data8 = $con->query($dermagaAlber)-> result_array();
				if ($data8){							
				$jasaA_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaA_alber8 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data8 = $con->query($dermagaAlber)-> result_array();
				if ($data8){							
				$jasaB_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaB_alber8 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data8 = $con->query($dermagaAlber)-> result_array();
				if ($data8){							
				$jasaC_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaC_alber8 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data8 = $con->query($dermagaAlber)-> result_array();
				if ($data8){							
				$jasaD_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaD_alber8 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data8 = $con->query($dermagaAlber)-> result_array();
				if ($data8){							
				$jasaE_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaE_alber8 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data8 = $con->query($dermagaAlber)-> result_array();
				if ($data8){							
				$jasaF_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaF_alber8 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data8 = $con->query($dermagaAlber)-> result_array();
				if ($data8){							
				$jasaG_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaG_alber8 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data8 = $con->query($stevedoring_alber)-> result_array();
				if ($data8){							
					$stevedoring_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
					$stevedoring_alber8 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data8 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data8){							
				$kebersihan_alber8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$kebersihan_alber8 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_malberI)-> result_array();
				if ($data8){							
				$malber18 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$malber18 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_malberII)-> result_array();
				if ($data8){							
				$malber28 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$malber28 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_malberIII)-> result_array();
				if ($data8){							
				$malber38 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$malber38 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_opt)-> result_array();
				if ($data8){							
				$opta8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$opta8 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_opt)-> result_array();
				if ($data8){							
				$optb8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optb8 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_opt)-> result_array();
				if ($data8){							
				$optc8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optc8 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_opt)-> result_array();
				if ($data8){							
				$optd8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optd8 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_opt)-> result_array();
				if ($data8){							
				$opte8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$opte8 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_opt)-> result_array();
				if ($data8){							
				$optf8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optf8 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_opt)-> result_array();
				if ($data8){							
				$optg8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optg8 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($dataCargo)-> result_array();
				if ($data8){							
				$jasaA_cargo8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaA_cargo8 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($dataCargo)-> result_array();
				if ($data8){							
				$jasaB_cargo8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaB_cargo8 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($dataCargo)-> result_array();
				if ($data8){							
				$jasaC_cargo8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$jasaC_cargo8 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($data_stv)-> result_array();
				if ($data8){							
				$stevedoring_part8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$stevedoring_part8 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data8 = $con->query($data_stvo)-> result_array();
				if ($data8){							
				$stevedoring_lain8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$stevedoring_lain8 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data8 = $con->query($data_optgc)-> result_array();
				if ($data8){							
				$optgcA_8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optgcA_8 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($data_optgc)-> result_array();
				if ($data8){							
				$optgcB_8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optgcB_8 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($data_optgc)-> result_array();
				if ($data8){							
				$optgcC_8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optgcC_8 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($data_optgc)-> result_array();
				if ($data8){							
				$optgcD_8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$optgcD_8 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data8 = $con->query($data_gcp)-> result_array();
				if ($data8){							
				$kebersihanPart8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$kebersihanPart8 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data8 = $con->query($data_gcp)-> result_array();
				if ($data8){							
				$kebersihanLain8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$kebersihanLain8 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data8 = $con->query($data_gcp)-> result_array();
				if ($data8){							
				$kebersihanAtas8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$kebersihanAtas8 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaA8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaA8 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaB8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaB8 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaC8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaC8 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaD8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaD8 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaE8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaE8 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaF8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaF8 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaG8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaG8 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaH8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaH8 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data8 = $con->query($data_penumpukan)-> result_array();
				if ($data8){							
				$MasaI8 = $data8[0]['TOTAL'];
				} else if (empty($data8))  {		
				$MasaI8 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-09'){
				$bulan9 = 'September';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data9 = $con->query($data_jasadermaga9)-> result_array();
				if ($data9){							
				$jasaDermaga9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaDermaga9 = 0;
				}

				$data_cargohandling9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($data_cargohandling9)-> result_array();
				if ($data9){							
				$cargoHandling9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cargoHandling9 = 0;
				}

				$data_stevedoring9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($data_stevedoring9)-> result_array();
				if ($data9){							
				$steveDoring9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$steveDoring9 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data9 = $con->query($data_kebersihan)-> result_array();
				if ($data9){							
				$kebersihan9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$kebersihan9 = 0;
				}
	
				$data_masa9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($data_masa9)-> result_array();
				if ($data9){							
				$masa19 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$masa19 = 0;
				}

				$data_masa9 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($data_masa9)-> result_array();
				if ($data9){							
				$masa29 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$masa29 = 0;
				}

				$data_masa9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($data_masa9)-> result_array();
				if ($data9){							
				$masa39 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$masa39 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($dermagaLuxury)-> result_array();
				if ($data9){							
				$dermagaLux9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$dermagaLux9 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($stevedoringLuxury)-> result_array();
				if ($data9){							
				$stevedoringLux9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$stevedoringLux9 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data9){							
				$cargohandlingLux9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$cargohandlingLux9 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data9){							
				$kebersihanLux9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$kebersihanLux9 = 0;
				}
					
				$masaILuxury9 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($masaILuxury9)-> result_array();
				if ($data9){							
				$masaILux9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$masaILux9 = 0;
				}

				$masaIILuxury9 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($masaIILuxury9)-> result_array();
				if ($data9){							
				$masaIILux9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$masaIILux9 = 0;
				}

				$masaIIILuxury9  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data9 = $con->query($masaIIILuxury9)-> result_array();
				if ($data9){							
				$masaIIILux9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$masaIIILux9 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data9 = $con->query($dermagaAlber)-> result_array();
				if ($data9){							
				$jasaA_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaA_alber9 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data9 = $con->query($dermagaAlber)-> result_array();
				if ($data9){							
				$jasaB_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaB_alber9 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data9 = $con->query($dermagaAlber)-> result_array();
				if ($data9){							
				$jasaC_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaC_alber9 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data9 = $con->query($dermagaAlber)-> result_array();
				if ($data9){							
				$jasaD_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaD_alber9 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data9 = $con->query($dermagaAlber)-> result_array();
				if ($data9){							
				$jasaE_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaE_alber9 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data9 = $con->query($dermagaAlber)-> result_array();
				if ($data9){							
				$jasaF_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaF_alber9 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data9 = $con->query($dermagaAlber)-> result_array();
				if ($data9){							
				$jasaG_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaG_alber9 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data9 = $con->query($stevedoring_alber)-> result_array();
				if ($data9){							
					$stevedoring_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
					$stevedoring_alber9 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data9 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data9){							
				$kebersihan_alber9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$kebersihan_alber9 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_malberI)-> result_array();
				if ($data9){							
				$malber19 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$malber19 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_malberII)-> result_array();
				if ($data9){							
				$malber29 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$malber29 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_malberIII)-> result_array();
				if ($data9){							
				$malber39 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$malber39 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_opt)-> result_array();
				if ($data9){							
				$opta9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$opta9 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_opt)-> result_array();
				if ($data9){							
				$optb9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optb9 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_opt)-> result_array();
				if ($data9){							
				$optc9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optc9 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_opt)-> result_array();
				if ($data9){							
				$optd9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optd9 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_opt)-> result_array();
				if ($data9){							
				$opte9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$opte9 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_opt)-> result_array();
				if ($data9){							
				$optf9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optf9 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_opt)-> result_array();
				if ($data9){							
				$optg9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optg9 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($dataCargo)-> result_array();
				if ($data9){							
				$jasaA_cargo9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaA_cargo9 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($dataCargo)-> result_array();
				if ($data9){							
				$jasaB_cargo9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaB_cargo9 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($dataCargo)-> result_array();
				if ($data9){							
				$jasaC_cargo9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$jasaC_cargo9 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($data_stv)-> result_array();
				if ($data9){							
				$stevedoring_part9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$stevedoring_part9 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data9 = $con->query($data_stvo)-> result_array();
				if ($data9){							
				$stevedoring_lain9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$stevedoring_lain9 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data9 = $con->query($data_optgc)-> result_array();
				if ($data9){							
				$optgcA_9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optgcA_9 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($data_optgc)-> result_array();
				if ($data9){							
				$optgcB_9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optgcB_9 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($data_optgc)-> result_array();
				if ($data9){							
				$optgcC_9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optgcC_9 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($data_optgc)-> result_array();
				if ($data9){							
				$optgcD_9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$optgcD_9 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data9 = $con->query($data_gcp)-> result_array();
				if ($data9){							
				$kebersihanPart9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$kebersihanPart9 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data9 = $con->query($data_gcp)-> result_array();
				if ($data9){							
				$kebersihanLain9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$kebersihanLain9 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data9 = $con->query($data_gcp)-> result_array();
				if ($data9){							
				$kebersihanAtas9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$kebersihanAtas9 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaA9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaA9 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaB9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaB9 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaC9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaC9 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaD9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaD9 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaE9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaE9 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaF9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaF9 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaG9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaG9 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaH9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaH9 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data9 = $con->query($data_penumpukan)-> result_array();
				if ($data9){							
				$MasaI9 = $data9[0]['TOTAL'];
				} else if (empty($data9))  {		
				$MasaI9 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-10'){
				$bulan10 = 'Oktober';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data10 = $con->query($data_jasadermaga10)-> result_array();
				if ($data10){							
				$jasaDermaga10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaDermaga10 = 0;
				}

				$data_cargohandling10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($data_cargohandling10)-> result_array();
				if ($data10){							
				$cargoHandling10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cargoHandling10 = 0;
				}

				$data_stevedoring10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($data_stevedoring10)-> result_array();
				if ($data10){							
				$steveDoring10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$steveDoring10 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data10 = $con->query($data_kebersihan)-> result_array();
				if ($data10){							
				$kebersihan10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$kebersihan10 = 0;
				}
	
				$data_masa10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($data_masa10)-> result_array();
				if ($data10){							
				$masa110 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$masa110 = 0;
				}

				$data_masa10 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($data_masa10)-> result_array();
				if ($data10){							
				$masa210 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$masa210 = 0;
				}

				$data_masa10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($data_masa10)-> result_array();
				if ($data10){							
				$masa310 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$masa310 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($dermagaLuxury)-> result_array();
				if ($data10){							
				$dermagaLux10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$dermagaLux10 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($stevedoringLuxury)-> result_array();
				if ($data10){							
				$stevedoringLux10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$stevedoringLux10 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data10){							
				$cargohandlingLux10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$cargohandlingLux10 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data10){							
				$kebersihanLux10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$kebersihanLux10 = 0;
				}
					
				$masaILuxury10 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($masaILuxury10)-> result_array();
				if ($data10){							
				$masaILux10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$masaILux10 = 0;
				}

				$masaIILuxury10 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($masaIILuxury10)-> result_array();
				if ($data10){							
				$masaIILux10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$masaIILux10 = 0;
				}

				$masaIIILuxury10  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data10 = $con->query($masaIIILuxury10)-> result_array();
				if ($data10){							
				$masaIIILux10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$masaIIILux10 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data10 = $con->query($dermagaAlber)-> result_array();
				if ($data10){							
				$jasaA_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaA_alber10 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data10 = $con->query($dermagaAlber)-> result_array();
				if ($data10){							
				$jasaB_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaB_alber10 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data10 = $con->query($dermagaAlber)-> result_array();
				if ($data10){							
				$jasaC_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaC_alber10 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data10 = $con->query($dermagaAlber)-> result_array();
				if ($data10){							
				$jasaD_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaD_alber10 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data10 = $con->query($dermagaAlber)-> result_array();
				if ($data10){							
				$jasaE_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaE_alber10 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data10 = $con->query($dermagaAlber)-> result_array();
				if ($data10){							
				$jasaF_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaF_alber10 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data10 = $con->query($dermagaAlber)-> result_array();
				if ($data10){							
				$jasaG_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaG_alber10 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data10 = $con->query($stevedoring_alber)-> result_array();
				if ($data10){							
					$stevedoring_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
					$stevedoring_alber10 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data10 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data10){							
				$kebersihan_alber10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$kebersihan_alber10 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_malberI)-> result_array();
				if ($data10){							
				$malber110 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$malber110 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_malberII)-> result_array();
				if ($data10){							
				$malber210 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$malber210 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_malberIII)-> result_array();
				if ($data10){							
				$malber310 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$malber310 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_opt)-> result_array();
				if ($data10){							
				$opta10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$opta10 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_opt)-> result_array();
				if ($data10){							
				$optb10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optb10 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_opt)-> result_array();
				if ($data10){							
				$optc10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optc10 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_opt)-> result_array();
				if ($data10){							
				$optd10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optd10 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_opt)-> result_array();
				if ($data10){							
				$opte10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$opte10 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_opt)-> result_array();
				if ($data10){							
				$optf10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optf10 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_opt)-> result_array();
				if ($data10){							
				$optg10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optg10 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($dataCargo)-> result_array();
				if ($data10){							
				$jasaA_cargo10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaA_cargo10 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($dataCargo)-> result_array();
				if ($data10){							
				$jasaB_cargo10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaB_cargo10 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($dataCargo)-> result_array();
				if ($data10){							
				$jasaC_cargo10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$jasaC_cargo10 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($data_stv)-> result_array();
				if ($data10){							
				$stevedoring_part10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$stevedoring_part10 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data10 = $con->query($data_stvo)-> result_array();
				if ($data10){							
				$stevedoring_lain10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$stevedoring_lain10 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data10 = $con->query($data_optgc)-> result_array();
				if ($data10){							
				$optgcA_10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optgcA_10 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($data_optgc)-> result_array();
				if ($data10){							
				$optgcB_10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optgcB_10 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($data_optgc)-> result_array();
				if ($data10){							
				$optgcC_10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optgcC_10 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($data_optgc)-> result_array();
				if ($data10){							
				$optgcD_10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$optgcD_10 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data10 = $con->query($data_gcp)-> result_array();
				if ($data10){							
				$kebersihanPart10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$kebersihanPart10 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data10 = $con->query($data_gcp)-> result_array();
				if ($data10){							
				$kebersihanLain10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$kebersihanLain10 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data10 = $con->query($data_gcp)-> result_array();
				if ($data10){							
				$kebersihanAtas10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$kebersihanAtas10 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaA10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaA10 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaB10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaB10 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaC10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaC10 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaD10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaD10 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaE10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaE10 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaF10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaF10 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaG10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaG10 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaH10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaH10 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data10 = $con->query($data_penumpukan)-> result_array();
				if ($data10){							
				$MasaI10 = $data10[0]['TOTAL'];
				} else if (empty($data10))  {		
				$MasaI10 = 0;
				}
			}

			if ($PERIODE == ''.$YEAR.'-11'){
				$bulan11 = 'November';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data11 = $con->query($data_jasadermaga11)-> result_array();
				if ($data11){							
				$jasaDermaga11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaDermaga11 = 0;
				}

				$data_cargohandling11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($data_cargohandling11)-> result_array();
				if ($data11){							
				$cargoHandling11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cargoHandling11 = 0;
				}

				$data_stevedoring11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($data_stevedoring11)-> result_array();
				if ($data11){							
				$steveDoring11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$steveDoring11 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data11 = $con->query($data_kebersihan)-> result_array();
				if ($data11){							
				$kebersihan11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$kebersihan11 = 0;
				}
	
				$data_masa11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($data_masa11)-> result_array();
				if ($data11){							
				$masa111 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$masa111 = 0;
				}

				$data_masa11 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($data_masa11)-> result_array();
				if ($data11){							
				$masa211 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$masa211 = 0;
				}

				$data_masa11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($data_masa11)-> result_array();
				if ($data11){							
				$masa311 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$masa311 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($dermagaLuxury)-> result_array();
				if ($data11){							
				$dermagaLux11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$dermagaLux11 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($stevedoringLuxury)-> result_array();
				if ($data11){							
				$stevedoringLux11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$stevedoringLux11 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data11){							
				$cargohandlingLux11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$cargohandlingLux11 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data11){							
				$kebersihanLux11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$kebersihanLux11 = 0;
				}
					
				$masaILuxury11 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($masaILuxury11)-> result_array();
				if ($data11){							
				$masaILux11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$masaILux11 = 0;
				}

				$masaIILuxury11 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($masaIILuxury11)-> result_array();
				if ($data11){							
				$masaIILux11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$masaIILux11 = 0;
				}

				$masaIIILuxury11  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data11 = $con->query($masaIIILuxury11)-> result_array();
				if ($data11){							
				$masaIIILux11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$masaIIILux11 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data11 = $con->query($dermagaAlber)-> result_array();
				if ($data11){							
				$jasaA_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaA_alber11 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data11 = $con->query($dermagaAlber)-> result_array();
				if ($data11){							
				$jasaB_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaB_alber11 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data11 = $con->query($dermagaAlber)-> result_array();
				if ($data11){							
				$jasaC_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaC_alber11 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data11 = $con->query($dermagaAlber)-> result_array();
				if ($data11){							
				$jasaD_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaD_alber11 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data11 = $con->query($dermagaAlber)-> result_array();
				if ($data11){							
				$jasaE_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaE_alber11 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data11 = $con->query($dermagaAlber)-> result_array();
				if ($data11){							
				$jasaF_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaF_alber11 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data11 = $con->query($dermagaAlber)-> result_array();
				if ($data11){							
				$jasaG_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaG_alber11 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data11 = $con->query($stevedoring_alber)-> result_array();
				if ($data11){							
					$stevedoring_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
					$stevedoring_alber11 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data11 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data11){							
				$kebersihan_alber11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$kebersihan_alber11 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_malberI)-> result_array();
				if ($data11){							
				$malber111 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$malber111 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_malberII)-> result_array();
				if ($data11){							
				$malber211 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$malber211 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_malberIII)-> result_array();
				if ($data11){							
				$malber311 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$malber311 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_opt)-> result_array();
				if ($data11){							
				$opta11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$opta11 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_opt)-> result_array();
				if ($data11){							
				$optb11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optb11 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_opt)-> result_array();
				if ($data11){							
				$optc11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optc11 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_opt)-> result_array();
				if ($data11){							
				$optd11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optd11 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_opt)-> result_array();
				if ($data11){							
				$opte11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$opte11 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_opt)-> result_array();
				if ($data11){							
				$optf11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optf11 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_opt)-> result_array();
				if ($data11){							
				$optg11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optg11 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($dataCargo)-> result_array();
				if ($data11){							
				$jasaA_cargo11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaA_cargo11 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($dataCargo)-> result_array();
				if ($data11){							
				$jasaB_cargo11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaB_cargo11 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($dataCargo)-> result_array();
				if ($data11){							
				$jasaC_cargo11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$jasaC_cargo11 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($data_stv)-> result_array();
				if ($data11){							
				$stevedoring_part11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$stevedoring_part11 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data11 = $con->query($data_stvo)-> result_array();
				if ($data11){							
				$stevedoring_lain11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$stevedoring_lain11 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data11 = $con->query($data_optgc)-> result_array();
				if ($data11){							
				$optgcA_11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optgcA_11 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($data_optgc)-> result_array();
				if ($data11){							
				$optgcB_11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optgcB_11 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($data_optgc)-> result_array();
				if ($data11){							
				$optgcC_11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optgcC_11 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($data_optgc)-> result_array();
				if ($data11){							
				$optgcD_11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$optgcD_11 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data11 = $con->query($data_gcp)-> result_array();
				if ($data11){							
				$kebersihanPart11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$kebersihanPart11 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data11 = $con->query($data_gcp)-> result_array();
				if ($data11){							
				$kebersihanLain11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$kebersihanLain11 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data11 = $con->query($data_gcp)-> result_array();
				if ($data11){							
				$kebersihanAtas11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$kebersihanAtas11 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaA11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaA11 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaB11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaB11 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaC11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaC11 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaD11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaD11 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaE11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaE11 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaF11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaF11 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaG11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaG11 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaH11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaH11 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data11 = $con->query($data_penumpukan)-> result_array();
				if ($data11){							
				$MasaI11 = $data11[0]['TOTAL'];
				} else if (empty($data11))  {		
				$MasaI11 = 0;
				}
			}
		
			if ($PERIODE == ''.$YEAR.'-12'){
				$bulan12 = 'Desember';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data12 = $con->query($data_jasadermaga12)-> result_array();
				if ($data12){							
				$jasaDermaga12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaDermaga12 = 0;
				}

				$data_cargohandling12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($data_cargohandling12)-> result_array();
				if ($data12){							
				$cargoHandling12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cargoHandling12 = 0;
				}

				$data_stevedoring12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($data_stevedoring12)-> result_array();
				if ($data12){							
				$steveDoring12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$steveDoring12 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';		
				
				$data12 = $con->query($data_kebersihan)-> result_array();
				if ($data12){							
				$kebersihan12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$kebersihan12 = 0;
				}
	
				$data_masa12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($data_masa12)-> result_array();
				if ($data12){							
				$masa112 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$masa112 = 0;
				}

				$data_masa12 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($data_masa12)-> result_array();
				if ($data12){							
				$masa212 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$masa212 = 0;
				}

				$data_masa12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($data_masa12)-> result_array();
				if ($data12){							
				$masa312 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$masa312 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($dermagaLuxury)-> result_array();
				if ($data12){							
				$dermagaLux12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$dermagaLux12 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($stevedoringLuxury)-> result_array();
				if ($data12){							
				$stevedoringLux12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$stevedoringLux12 = 0;
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data12){							
				$cargohandlingLux12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$cargohandlingLux12 = 0;
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data12){							
				$kebersihanLux12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$kebersihanLux12 = 0;
				}
					
				$masaILuxury12 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($masaILuxury12)-> result_array();
				if ($data12){							
				$masaILux12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$masaILux12 = 0;
				}

				$masaIILuxury12 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($masaIILuxury12)-> result_array();
				if ($data12){							
				$masaIILux12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$masaIILux12 = 0;
				}

				$masaIIILuxury12  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';

				$data12 = $con->query($masaIIILuxury12)-> result_array();
				if ($data12){							
				$masaIIILux12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$masaIIILux12 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data12 = $con->query($dermagaAlber)-> result_array();
				if ($data12){							
				$jasaA_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaA_alber12 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data12 = $con->query($dermagaAlber)-> result_array();
				if ($data12){							
				$jasaB_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaB_alber12 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data12 = $con->query($dermagaAlber)-> result_array();
				if ($data12){							
				$jasaC_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaC_alber12 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data12 = $con->query($dermagaAlber)-> result_array();
				if ($data12){							
				$jasaD_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaD_alber12 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data12 = $con->query($dermagaAlber)-> result_array();
				if ($data12){							
				$jasaE_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaE_alber12 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data12 = $con->query($dermagaAlber)-> result_array();
				if ($data12){							
				$jasaF_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaF_alber12 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
				$data12 = $con->query($dermagaAlber)-> result_array();
				if ($data12){							
				$jasaG_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaG_alber12 = 0;
				}	

				$stevedoring_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data12 = $con->query($stevedoring_alber)-> result_array();
				if ($data12){							
					$stevedoring_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
					$stevedoring_alber12 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') = '.$PERIODE.' ';
	
				$data12 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data12){							
				$kebersihan_alber12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$kebersihan_alber12 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_malberI)-> result_array();
				if ($data12){							
				$malber112 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$malber112 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_malberII)-> result_array();
				if ($data12){							
				$malber212 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$malber212 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_malberIII)-> result_array();
				if ($data12){							
				$malber312 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$malber312 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_opt)-> result_array();
				if ($data12){							
				$opta12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$opta12 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_opt)-> result_array();
				if ($data12){							
				$optb12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optb12 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_opt)-> result_array();
				if ($data12){							
				$optc12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optc12 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_opt)-> result_array();
				if ($data12){							
				$optd12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optd12 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_opt)-> result_array();
				if ($data12){							
				$opte12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$opte12 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_opt)-> result_array();
				if ($data12){							
				$optf12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optf12 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_opt)-> result_array();
				if ($data12){							
				$optg12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optg12 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($dataCargo)-> result_array();
				if ($data12){							
				$jasaA_cargo12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaA_cargo12 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.'  OR "GOLONGAN" ='.$lainY.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($dataCargo)-> result_array();
				if ($data12){							
				$jasaB_cargo12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaB_cargo12 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($dataCargo)-> result_array();
				if ($data12){							
				$jasaC_cargo12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$jasaC_cargo12 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($data_stv)-> result_array();
				if ($data12){							
				$stevedoring_part12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$stevedoring_part12 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'				';

				$data12 = $con->query($data_stvo)-> result_array();
				if ($data12){							
				$stevedoring_lain12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$stevedoring_lain12 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';

				$data12 = $con->query($data_optgc)-> result_array();
				if ($data12){							
				$optgcA_12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optgcA_12 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($data_optgc)-> result_array();
				if ($data12){							
				$optgcB_12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optgcB_12 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($data_optgc)-> result_array();
				if ($data12){							
				$optgcC_12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optgcC_12 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($data_optgc)-> result_array();
				if ($data12){							
				$optgcD_12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$optgcD_12 = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'		
				';
				$data12 = $con->query($data_gcp)-> result_array();
				if ($data12){							
				$kebersihanPart12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$kebersihanPart12 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'	
				';
				$data12 = $con->query($data_gcp)-> result_array();
				if ($data12){							
				$kebersihanLain12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$kebersihanLain12 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';
				$data12 = $con->query($data_gcp)-> result_array();
				if ($data12){							
				$kebersihanAtas12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$kebersihanAtas12 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaA12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaA12 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaB12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaB12 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaC12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaC12 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'			
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaD12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaD12 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'					
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaE12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaE12 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaF12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaF12 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'									
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaG12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaG12 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'													
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaH12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaH12 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and  to_char("PERIODE",'.$dates.') = '.$PERIODE.'											
				';

				$data12 = $con->query($data_penumpukan)-> result_array();
				if ($data12){							
				$MasaI12 = $data12[0]['TOTAL'];
				} else if (empty($data12))  {		
				$MasaI12 = 0;
				}
			}
		}
			$semester = "'Per Semester'";
			$triwulan = "'Per Triwulan'";
			$tahun = "'Per Tahun'";

			$YEAR = "'$YEAR'";
			$cbuLux = "'CBU LUXURY'";
			$gol1 = "'< 28'";
			$gol2 = "'> 28 - 33'";
			$gol3 = "'> 33 - 40'";
			$gol4 = "'> 40 - 50'";
			$gol5 = "'> 50 - 80'";
			$gol6 = "'> 80 - 100'";
			$gol7 = "'> 100'";
			
			$oppt1 = "'< 5'";
			$oppt2 = "'> 5 - 10'";
			$oppt3 = "'> 10 - 15'";	
			$oppt4 = "'> 15 - 20'";	
			$oppt5 = "'> 25'";
		
			if ($OLD){				
			
				$x = "$OLD-01";	
				$y = "$OLD-12";	
				$old = "'$x'";
				$ago = "'$y'";
				$datest = "'yyyy'";
				$OLD = "'$OLD'";

				$bulan1 = 'Januari';
				$PERIODE = "'$PERIODE'";

				$data_jasadermaga1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
				$data1 = $con->query($data_jasadermaga1)-> result_array();
				if ($data1){							
				$jasaDermaga_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaDermaga_old1 = 0;
				}

				$data_cargohandling1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($data_cargohandling1)-> result_array();
				if ($data1){							
				$cargoHandling_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cargoHandling_old1 = 0;
				}

				$data_stevedoring1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($data_stevedoring1)-> result_array();
				if ($data1){							
				$steveDoring_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$steveDoring_old1 = 0;
				}

				$data_kebersihan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';		
				
				$data1 = $con->query($data_kebersihan)-> result_array();
				if ($data1){							
				$kebersihan_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihan_old1 = 0;
				}
	
				$data_masa1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($data_masa1)-> result_array();
				if ($data1){							
				$masa_old11 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masa_old11 = 0;
				}

				$data_masa2 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($data_masa2)-> result_array();
				if ($data1){							
				$masa_old21 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masa_old21 = 0;
				}

				$data_masa3 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" like '.$cbu.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($data_masa3)-> result_array();
				if ($data1){							
				$masa_old31 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masa_old31 = 0;
				}

				$dermagaLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$jasaDermaga.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($dermagaLuxury)-> result_array();
				if ($data1){							
				$dermagaLux_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$dermagaLux_old1 = 0;
				}

				$stevedoringLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($stevedoringLuxury)-> result_array();
				if ($data1){							
				$stevedoringLux_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$stevedoringLux_old1 = '';
				}

				$cargohandlingLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$cargoHandling.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data1){							
				$cargohandlingLux_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$cargohandlingLux_old1 = '';
				}

				$kebersihanLuxury = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($cargohandlingLuxury)-> result_array();
				if ($data1){							
				$kebersihanLux_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihanLux_old1 = '';
				}
					
				$masaILuxury1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaI.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($masaILuxury1)-> result_array();
				if ($data1){							
				$masaILux_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masaILux_old1 = '';
				}

				$masaIILuxury1 ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaII.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($masaIILuxury1)-> result_array();
				if ($data1){							
				$masaIILux_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masaIILux_old1 = 0;
				}

				$masaIIILuxury1  = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$cbuLuxury.' 
				and "LAYANAN" = '.$masaIII.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';

				$data1 = $con->query($masaIIILuxury1)-> result_array();
				if ($data1){							
				$masaIIILux_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$masaIIILux_old1 = 0;
				}

				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optA.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaA_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaA_alber_old1 = 0;
				}
				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optB.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaB_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaB_alber_old1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optC.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaC_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaC_alber_old1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optD.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaD_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaD_alber_old1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optE.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaE_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaE_alber_old1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optF.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaF_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaF_alber_old1 = 0;
				}

				
				$dermagaAlber ='select SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$jasaDermaga.' and "GOLONGAN" ='.$optG.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
				$data1 = $con->query($dermagaAlber)-> result_array();
				if ($data1){							
				$jasaG_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaG_alber_old1 = 0;
				}	

				$stevedoring1 = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$steveDoring.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
	
				$data1 = $con->query($stevedoring1)-> result_array();
				if ($data1){							
					$stevedoring_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
					$stevedoring_alber_old1 = 0;
				}

				$data_kebersihan_alber = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$kebersihan.' 
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' ';
	
				$data1 = $con->query($data_kebersihan_alber)-> result_array();
				if ($data1){							
				$kebersihan_alber_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihan_alber_old1 = 0;
				}

				$data_malberI = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaI.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_malberI)-> result_array();
				if ($data1){							
				$malber_old11 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$malber_old11 = 0;
				}

				$data_malberII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaII.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_malberII)-> result_array();
				if ($data1){							
				$malber_old21 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$malber_old21 = 0;
				}

				$data_malberIII = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" = '.$masaIII.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_malberIII)-> result_array();
				if ($data1){							
				$malber_old31 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$malber_old31 = 0;
				}

		
				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optA.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$opta_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$opta_old1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optB.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optb_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optb_old1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optC.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optc_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optc_old1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optD.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optd_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optd_old1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optE.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$opte_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$opte_old1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optF.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optf_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optf_old1 = 0;
				}

				$data_opt = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$alberTruck.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optG.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_opt)-> result_array();
				if ($data1){							
				$optg_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optg_old1 = 0;
				}
				

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and "GOLONGAN" ='.$part.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($dataCargo)-> result_array();
				if ($data1){							
				$jasaA_cargo_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaA_cargo_old1 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.') and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($dataCargo)-> result_array();
				if ($data1){							
				$jasaB_cargo_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaB_cargo_old1 = 0;
				}

				$dataCargo = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$jasaDermaga.' and ("GOLONGAN" ='.$lainII.'  OR "GOLONGAN" ='.$lainZ.') and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($dataCargo)-> result_array();
				if ($data1){							
				$jasaC_cargo_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$jasaC_cargo_old1 = 0;
				}

				$data_stv = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$part.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($data_stv)-> result_array();
				if ($data1){							
				$stevedoring_part_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$stevedoring_part_old1 = 0;
				}

				$data_stvo ='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$steveDoring.' and "GOLONGAN" ='.$lain.' and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 				';

				$data1 = $con->query($data_stvo)-> result_array();
				if ($data1){							
				$stevedoring_lain_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$stevedoring_lain_old1 = 0;
				}
						
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcA.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 	
				';

				$data1 = $con->query($data_optgc)-> result_array();
				if ($data1){							
				$optgcA_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optgcA_old1 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcB.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($data_optgc)-> result_array();
				if ($data1){							
				$optgcB_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optgcB_old1 = 0;
				}

				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcC.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($data_optgc)-> result_array();
				if ($data1){							
				$optgcC_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optgcC_old1 = 0;
				}
				
				$data_optgc = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.' 
				and "LAYANAN" ='.$oppt.' and "GOLONGAN" ='.$optgcD.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($data_optgc)-> result_array();
				if ($data1){							
				$optgcD_old1  = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$optgcD_old1  = 0;
				}

				$data_gcp = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and "GOLONGAN" ='.$part.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 		
				';
				$data1 = $con->query($data_gcp)-> result_array();
				if ($data1){							
				$kebersihanPart_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihanPart_old1 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainI.' OR "GOLONGAN" ='.$lainY.')
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 	
				';
				$data1 = $con->query($data_gcp)-> result_array();
				if ($data1){							
				$kebersihanLain_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihanLain_old1 = 0;
				}

				$data_gcp='SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$kebersihan.' and ("GOLONGAN" ='.$lainII.' OR "GOLONGAN" ='.$lainZ.')
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';
				$data1 = $con->query($data_gcp)-> result_array();
				if ($data1){							
				$kebersihanAtas_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$kebersihanAtas_old1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and "GOLONGAN" ='.$part.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaA_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaA_old1 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaB_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaB_old1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaI.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 			
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaC_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaC_old1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and "GOLONGAN" ='.$part.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 			
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaD_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaD_old1 = 0;
				}
				
				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 					
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaE_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaE_old1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 									
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaF_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaF_old1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and "GOLONGAN" ='.$part.'
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 									
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaG_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaG_old1 = 0;
				}			

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainA.'OR "GOLONGAN" ='.$lainY.')
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 													
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaH_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaH_old1 = 0;
				}

				$data_penumpukan = 'SELECT SUM("TOTAL") "TOTAL"
				FROM "MART_INCOME_PER_SERVICE" mips 
				WHERE "TERMINAL" = '.$terminalIntr.' and "KOMODITI" = '.$generalCargo.'  
				and "LAYANAN" ='.$masaIII.' and ("GOLONGAN" ='.$lainB.'OR "GOLONGAN" ='.$lainZ.')
				and to_char("PERIODE",'.$dates.') BETWEEN '.$old.' AND '.$ago.' 											
				';

				$data1 = $con->query($data_penumpukan)-> result_array();
				if ($data1){							
				$MasaI_old1 = $data1[0]['TOTAL'];
				} else if (empty($data1))  {		
				$MasaI_old1 = 0;
				}
			
			}	
		if ($tipe == 'PER TAHUN'){
			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$jasaDermaga.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAdermaga = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAdermaga = 0;
	
			}

			
			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAstvdoring = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAstvdoring = 0;
		
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$cargoHandling.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcargohandling = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAcargohandling  = 0;
		
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAkebersihan = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAkebersihan = 0;
		
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaI   = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAmasaI  = 0;
	
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaII   = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAmasaII  = 0;
		
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaIII   = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAmasaIII  = 0;
		
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$jasaDermaga.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAdermagalux = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAdermagalux = 0;
		
			}

			
			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAstvdoringlux = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAstvdoringlux = 0;
	
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$cargoHandling.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAcargohandlinglux = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargohandlinglux  = 0;
		
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAkebersihanlux = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAkebersihanlux = 0;
	
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIlux   = $data1[0]['TARIF_1'];
			} else if (empty($data1))  {		
				$tarifAmasaIlux  = 0;
		
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIIlux = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAmasaIIlux = 0;

			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIIIlux  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAmasaIIIlux = 0;
	
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber1  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAalber1 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber2  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAalber2 = 0;

			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber3  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalber3 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber4  = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAalber4 = 0;
	
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber5  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAalber5 = 0;
	
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber6  = $data1[0]['TARIF_1'];
			
			} else if (empty($data1))  {		
				$tarifAalber6 = 0;
	
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber7  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAalber7 = 0;

			}

			$tarifStvalber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifStvalber)-> result_array();
			if ($data1){							
				$tarifAstvalber  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAstvalber = 0;

			}
			
			
			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt1  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAoppt1 = 0;

			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt2  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAoppt2 = 0;

			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt3  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAoppt3 = 0;

			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt4  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAoppt4 = 0;

			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt5  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAoppt5 = 0;

			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt6  = $data1[0]['TARIF_1'];  
	
			} else if (empty($data1))  {		
				$tarifAoppt6 = 0;

			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt7  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAoppt7 = 0;

			}
			
			$tarifAlberkebersihan = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberkebersihan)-> result_array();
			if ($data1){							
				$tarifAkebersihanoppt  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAkebersihanoppt = 0;

			} 

			
			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIalber   = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAmasaIalber  = 0;
	
			}

			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIIalber = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAmasaIIalber = 0;
	
			}

			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIIIalber  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAmasaIIIalber = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargojasa  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAcargojasa = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$lainY.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargolain  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargolain = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$lainZ.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargolain2  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAcargolain2 = 0;

			}
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$steveDoring.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargostv  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargostv = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$steveDoring.' and "GOLONGAN" = '.$lain.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAstvlain  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAstvlain = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt1  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargooppt1 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt2  = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAcargooppt2 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt3  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargooppt3 = 0;
	
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt4  = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargooppt4 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt5  = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAcargooppt5 = 0;
	
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan1 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAcargokebersihan1 = 0;
	
			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$lainY.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan2 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAcargokebersihan2 = 0;
	
			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$lainZ.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan3 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAcargokebersihan3 = 0;

			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa11 = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargomasa11 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$lainY.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa12 = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargomasa12 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$lainZ.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa13 = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargomasa13 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa21 = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargomasa21 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$lainY.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa22 = $data1[0]['TARIF_1'];

			} else if (empty($data1))  {		
				$tarifAcargomasa22 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$lainZ.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa23 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {
				$tarifAcargomasa23 = 0;

			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa31 = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAcargomasa31 = 0;
	
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$lainY.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa32 = $data1[0]['TARIF_1'];
	
			} else if (empty($data1))  {		
				$tarifAcargomasa32 = 0;
	
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$tahun.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$lainZ.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa33 = $data1[0]['TARIF_1'];
		
			} else if (empty($data1))  {		
				$tarifAcargomasa33 = 0;

			}
		}
		if ($tipe == 'PER SEMESTER'){
			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$jasaDermaga.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAdermaga = $data1[0]['TARIF_1'];
				$tarifBdermaga = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAdermaga = 0;
				$tarifBdermaga = 0;
			}

			
			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAstvdoring = $data1[0]['TARIF_1'];
				$tarifBstvdoring = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAstvdoring = 0;
				$tarifBstvdoring = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$cargoHandling.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcargohandling = $data1[0]['TARIF_1'];
				$tarifBcargohandling  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargohandling  = 0;
				$tarifBcargohandling  = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAkebersihan = $data1[0]['TARIF_1'];
				$tarifBkebersihan  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAkebersihan = 0;
				$tarifBkebersihan = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaI   = $data1[0]['TARIF_1'];
				$tarifBmasaI  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaI  = 0;
				$tarifBmasaI = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaII   = $data1[0]['TARIF_1'];
				$tarifBmasaII  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaII  = 0;
				$tarifBmasaII = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaIII   = $data1[0]['TARIF_1'];
				$tarifBmasaIII  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIII  = 0;
				$tarifBmasaIII = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$jasaDermaga.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAdermagalux = $data1[0]['TARIF_1'];
				$tarifBdermagalux = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAdermagalux = 0;
				$tarifBdermagalux = 0;
			}

			
			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAstvdoringlux = $data1[0]['TARIF_1'];
				$tarifBstvdoringlux = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAstvdoringlux = 0;
				$tarifBstvdoringlux = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$cargoHandling.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAcargohandlinglux = $data1[0]['TARIF_1'];
				$tarifBcargohandlinglux  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargohandlinglux  = 0;
				$tarifBcargohandlinglux  = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAkebersihanlux = $data1[0]['TARIF_1'];
				$tarifBkebersihanlux  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAkebersihanlux = 0;
				$tarifBkebersihanlux = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIlux   = $data1[0]['TARIF_1'];
				$tarifBmasaIlux  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIlux  = 0;
				$tarifBmasaIlux = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIIlux = $data1[0]['TARIF_1'];
				$tarifBmasaIIlux = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIIlux = 0;
				$tarifBmasaIIlux = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIIIlux  = $data1[0]['TARIF_1'];
				$tarifBmasaIIIlux  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIIIlux = 0;
				$tarifBmasaIIIlux = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber1  = $data1[0]['TARIF_1'];
				$tarifBalber1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber1 = 0;
				$tarifBalber1 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber2  = $data1[0]['TARIF_1'];
				$tarifBalber2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber2 = 0;
				$tarifBalber2 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber3  = $data1[0]['TARIF_1'];
				$tarifBalber3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber3 = 0;
				$tarifBalber3 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber4  = $data1[0]['TARIF_1'];
				$tarifBalber4  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber4 = 0;
				$tarifBalber4 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber5  = $data1[0]['TARIF_1'];
				$tarifBalber5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber5 = 0;
				$tarifBalber5 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber6  = $data1[0]['TARIF_1'];
				$tarifBalber6  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber6 = 0;
				$tarifBalber6 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber7  = $data1[0]['TARIF_1'];
				$tarifBalber7  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber7 = 0;
				$tarifBalber7 = 0;
			}

			$tarifStvalber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAstvalber  = $data1[0]['TARIF_1'];
				$tarifBstvalber  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAstvalber = 0;
				$tarifBstvalber = 0;
			}
			
			
			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAoppt1  = $data1[0]['TARIF_1'];
				$tarifBoppt1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt1 = 0;
				$tarifBoppt1 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt2  = $data1[0]['TARIF_1'];
				$tarifBoppt2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt2 = 0;
				$tarifBoppt2 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt3  = $data1[0]['TARIF_1'];
				$tarifBoppt3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt3 = 0;
				$tarifBoppt3 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt4  = $data1[0]['TARIF_1'];
				$tarifBoppt4  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt4 = 0;
				$tarifBoppt4 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt5  = $data1[0]['TARIF_1'];
				$tarifBoppt5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt5 = 0;
				$tarifBoppt5 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt6  = $data1[0]['TARIF_1'];  
				$tarifBoppt6  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt6 = 0;
				$tarifBoppt6 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt7  = $data1[0]['TARIF_1'];
				$tarifBoppt7  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt7 = 0;
				$tarifBoppt7 = 0;
			}
			
			$tarifAlberkebersihan = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberkebersihan)-> result_array();
			if ($data1){							
				$tarifAkebersihanoppt  = $data1[0]['TARIF_1'];
				$tarifBkebersihanoppt  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAkebersihanoppt = 0;
				$tarifBkebersihanoppt = 0;
			} 

			
			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIalber   = $data1[0]['TARIF_1'];
				$tarifBmasaIalber  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIalber  = 0;
				$tarifBmasaIalber = 0;
			}

			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIIalber = $data1[0]['TARIF_1'];
				$tarifBmasaIIalber = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIIalber = 0;
				$tarifBmasaIIalber = 0;
			}

			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIIIalber  = $data1[0]['TARIF_1'];
				$tarifBmasaIIIalber  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIIIalber = 0;
				$tarifBmasaIIIalber = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargojasa  = $data1[0]['TARIF_1'];
				$tarifBcargojasa  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargojasa = 0;
				$tarifBcargojasa = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargolain  = $data1[0]['TARIF_1'];
				$tarifBcargolain  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargolain = 0;
				$tarifBcargolain = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargolain2  = $data1[0]['TARIF_1'];
				$tarifBcargolain2 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargolain2 = 0;
				$tarifBcargolain2 = 0;
			}
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$steveDoring.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargostv  = $data1[0]['TARIF_1'];
				$tarifBcargostv  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargostv = 0;
				$tarifBcargostv = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$steveDoring.' and "GOLONGAN" = '.$lain.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAstvlain  = $data1[0]['TARIF_1'];
				$tarifBstvlain  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAstvlain = 0;
				$tarifBstvlain = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt1  = $data1[0]['TARIF_1'];
				$tarifBcargooppt1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt1 = 0;
				$tarifBcargooppt1 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt2  = $data1[0]['TARIF_1'];
				$tarifBcargooppt2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt2 = 0;
				$tarifBcargooppt2 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt3  = $data1[0]['TARIF_1'];
				$tarifBcargooppt3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt3 = 0;
				$tarifBcargooppt3 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt4  = $data1[0]['TARIF_1'];
				$tarifBcargooppt4  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt4 = 0;
				$tarifBcargooppt4 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt5  = $data1[0]['TARIF_1'];
				$tarifBcargooppt5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt5 = 0;
				$tarifBcargooppt5 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan1 = $data1[0]['TARIF_1'];
				$tarifBcargokebersihan1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargokebersihan1 = 0;
				$tarifBcargokebersihan1 = 0;
			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan2 = $data1[0]['TARIF_1'];
				$tarifBcargokebersihan2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargokebersihan2 = 0;
				$tarifBcargokebersihan2 = 0;
			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan3 = $data1[0]['TARIF_1'];
				$tarifBcargokebersihan3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargokebersihan3 = 0;
				$tarifBcargokebersihan3 = 0;
			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa11 = $data1[0]['TARIF_1'];
				$tarifBcargomasa11  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa11 = 0;
				$tarifBcargomasa11 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa12 = $data1[0]['TARIF_1'];
				$tarifBcargomasa12  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa12 = 0;
				$tarifBcargomasa12 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa13 = $data1[0]['TARIF_1'];
				$tarifBcargomasa13  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa13 = 0;
				$tarifBcargomasa13 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa21 = $data1[0]['TARIF_1'];
				$tarifBcargomasa21  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa21 = 0;
				$tarifBcargomasa21 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa22 = $data1[0]['TARIF_1'];
				$tarifBcargomasa22  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa22 = 0;
				$tarifBcargomasa22 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa23 = $data1[0]['TARIF_1'];
				$tarifBcargomasa23 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa23 = 0;
				$tarifBcargomasa23 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa31 = $data1[0]['TARIF_1'];
				$tarifBcargomasa31 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa31 = 0;
				$tarifBcargomasa31 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa32 = $data1[0]['TARIF_1'];
				$tarifBcargomasa32 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa32 = 0;
				$tarifBcargomasa32 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$semester.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa33 = $data1[0]['TARIF_1'];
				$tarifBcargomasa33 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa33 = 0;
				$tarifBcargomasa33 = 0;
			}
		}

		if ($tipe == 'PER TRIWULAN'){
			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$jasaDermaga.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAdermaga = $data1[0]['TARIF_1'];
				$tarifBdermaga = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAdermaga = 0;
				$tarifBdermaga = 0;
			}

			
			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAstvdoring = $data1[0]['TARIF_1'];
				$tarifBstvdoring = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAstvdoring = 0;
				$tarifBstvdoring = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$cargoHandling.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAcargohandling = $data1[0]['TARIF_1'];
				$tarifBcargohandling  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargohandling  = 0;
				$tarifBcargohandling  = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAkebersihan = $data1[0]['TARIF_1'];
				$tarifBkebersihan  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAkebersihan = 0;
				$tarifBkebersihan = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaI   = $data1[0]['TARIF_1'];
				$tarifBmasaI  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaI  = 0;
				$tarifBmasaI = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaII   = $data1[0]['TARIF_1'];
				$tarifBmasaII  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaII  = 0;
				$tarifBmasaII = 0;
			}

			$tarifCbu = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" like '.$cbu.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbu)-> result_array();
			if ($data1){							
				$tarifAmasaIII   = $data1[0]['TARIF_1'];
				$tarifBmasaIII  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIII  = 0;
				$tarifBmasaIII = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$jasaDermaga.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAdermagalux = $data1[0]['TARIF_1'];
				$tarifBdermagalux = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAdermagalux = 0;
				$tarifBdermagalux = 0;
			}

			
			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAstvdoringlux = $data1[0]['TARIF_1'];
				$tarifBstvdoringlux = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAstvdoringlux = 0;
				$tarifBstvdoringlux = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$cargoHandling.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAcargohandlinglux = $data1[0]['TARIF_1'];
				$tarifBcargohandlinglux  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargohandlinglux  = 0;
				$tarifBcargohandlinglux  = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAkebersihanlux = $data1[0]['TARIF_1'];
				$tarifBkebersihanlux  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAkebersihanlux = 0;
				$tarifBkebersihanlux = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIlux   = $data1[0]['TARIF_1'];
				$tarifBmasaIlux  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIlux  = 0;
				$tarifBmasaIlux = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIIlux = $data1[0]['TARIF_1'];
				$tarifBmasaIIlux = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIIlux = 0;
				$tarifBmasaIIlux = 0;
			}

			$tarifCbulux = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$cbuLux.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCbulux)-> result_array();
			if ($data1){							
				$tarifAmasaIIIlux  = $data1[0]['TARIF_1'];
				$tarifBmasaIIIlux  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIIIlux = 0;
				$tarifBmasaIIIlux = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber1  = $data1[0]['TARIF_1'];
				$tarifBalber1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber1 = 0;
				$tarifBalber1 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber2  = $data1[0]['TARIF_1'];
				$tarifBalber2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber2 = 0;
				$tarifBalber2 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber3  = $data1[0]['TARIF_1'];
				$tarifBalber3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber3 = 0;
				$tarifBalber3 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber4  = $data1[0]['TARIF_1'];
				$tarifBalber4  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber4 = 0;
				$tarifBalber4 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber5  = $data1[0]['TARIF_1'];
				$tarifBalber5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber5 = 0;
				$tarifBalber5 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber6  = $data1[0]['TARIF_1'];
				$tarifBalber6  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber6 = 0;
				$tarifBalber6 = 0;
			}

			$tarifAlber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAalber7  = $data1[0]['TARIF_1'];
				$tarifBalber7  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAalber7 = 0;
				$tarifBalber7 = 0;
			}

			$tarifStvalber = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$steveDoring.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAstvalber  = $data1[0]['TARIF_1'];
				$tarifBstvalber  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAstvalber = 0;
				$tarifBstvalber = 0;
			}
			
			
			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlber)-> result_array();
			if ($data1){							
				$tarifAoppt1  = $data1[0]['TARIF_1'];
				$tarifBoppt1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt1 = 0;
				$tarifBoppt1 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt2  = $data1[0]['TARIF_1'];
				$tarifBoppt2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt2 = 0;
				$tarifBoppt2 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt3  = $data1[0]['TARIF_1'];
				$tarifBoppt3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt3 = 0;
				$tarifBoppt3 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt4  = $data1[0]['TARIF_1'];
				$tarifBoppt4  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt4 = 0;
				$tarifBoppt4 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt5  = $data1[0]['TARIF_1'];
				$tarifBoppt5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt5 = 0;
				$tarifBoppt5 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol6.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt6  = $data1[0]['TARIF_1'];  
				$tarifBoppt6  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt6 = 0;
				$tarifBoppt6 = 0;
			}

			$tarifAlberoppt = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$gol7.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberoppt)-> result_array();
			if ($data1){							
				$tarifAoppt7  = $data1[0]['TARIF_1'];
				$tarifBoppt7  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAoppt7 = 0;
				$tarifBoppt7 = 0;
			}
			
			$tarifAlberkebersihan = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$kebersihan.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifAlberkebersihan)-> result_array();
			if ($data1){							
				$tarifAkebersihanoppt  = $data1[0]['TARIF_1'];
				$tarifBkebersihanoppt  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAkebersihanoppt = 0;
				$tarifBkebersihanoppt = 0;
			} 

			
			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIalber   = $data1[0]['TARIF_1'];
				$tarifBmasaIalber  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIalber  = 0;
				$tarifBmasaIalber = 0;
			}

			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIIalber = $data1[0]['TARIF_1'];
				$tarifBmasaIIalber = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIIalber = 0;
				$tarifBmasaIIalber = 0;
			}

			$tarifMasa = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$alberTruck.' and "PELAYANAN" = '.$masaIII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifMasa)-> result_array();
			if ($data1){							
				$tarifAmasaIIIalber  = $data1[0]['TARIF_1'];
				$tarifBmasaIIIalber  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAmasaIIIalber = 0;
				$tarifBmasaIIIalber = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargojasa  = $data1[0]['TARIF_1'];
				$tarifBcargojasa  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargojasa = 0;
				$tarifBcargojasa = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargolain  = $data1[0]['TARIF_1'];
				$tarifBcargolain  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargolain = 0;
				$tarifBcargolain = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$jasaDermaga.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargolain2  = $data1[0]['TARIF_1'];
				$tarifBcargolain2 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargolain2 = 0;
				$tarifBcargolain2 = 0;
			}
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$steveDoring.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargostv  = $data1[0]['TARIF_1'];
				$tarifBcargostv  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargostv = 0;
				$tarifBcargostv = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$steveDoring.' and "GOLONGAN" = '.$lain.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAstvlain  = $data1[0]['TARIF_1'];
				$tarifBstvlain  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAstvlain = 0;
				$tarifBstvlain = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt1.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt1  = $data1[0]['TARIF_1'];
				$tarifBcargooppt1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt1 = 0;
				$tarifBcargooppt1 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt2.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt2  = $data1[0]['TARIF_1'];
				$tarifBcargooppt2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt2 = 0;
				$tarifBcargooppt2 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt3.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt3  = $data1[0]['TARIF_1'];
				$tarifBcargooppt3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt3 = 0;
				$tarifBcargooppt3 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt4.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt4  = $data1[0]['TARIF_1'];
				$tarifBcargooppt4  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt4 = 0;
				$tarifBcargooppt4 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$oppt.' and "GOLONGAN" = '.$oppt5.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargooppt5  = $data1[0]['TARIF_1'];
				$tarifBcargooppt5  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargooppt5 = 0;
				$tarifBcargooppt5 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan1 = $data1[0]['TARIF_1'];
				$tarifBcargokebersihan1  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargokebersihan1 = 0;
				$tarifBcargokebersihan1 = 0;
			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan2 = $data1[0]['TARIF_1'];
				$tarifBcargokebersihan2  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargokebersihan2 = 0;
				$tarifBcargokebersihan2 = 0;
			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$kebersihan.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargokebersihan3 = $data1[0]['TARIF_1'];
				$tarifBcargokebersihan3  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargokebersihan3 = 0;
				$tarifBcargokebersihan3 = 0;
			}

			
			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa11 = $data1[0]['TARIF_1'];
				$tarifBcargomasa11  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa11 = 0;
				$tarifBcargomasa11 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa12 = $data1[0]['TARIF_1'];
				$tarifBcargomasa12  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa12 = 0;
				$tarifBcargomasa12 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaI.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa13 = $data1[0]['TARIF_1'];
				$tarifBcargomasa13  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa13 = 0;
				$tarifBcargomasa13 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa21 = $data1[0]['TARIF_1'];
				$tarifBcargomasa21  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa21 = 0;
				$tarifBcargomasa21 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa22 = $data1[0]['TARIF_1'];
				$tarifBcargomasa22  = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa22 = 0;
				$tarifBcargomasa22 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaII.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa23 = $data1[0]['TARIF_1'];
				$tarifBcargomasa23 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa23 = 0;
				$tarifBcargomasa23 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$part.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa31 = $data1[0]['TARIF_1'];
				$tarifBcargomasa31 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa31 = 0;
				$tarifBcargomasa31 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$lainI.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa32 = $data1[0]['TARIF_1'];
				$tarifBcargomasa32 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa32 = 0;
				$tarifBcargomasa32 = 0;
			}

			$tarifCargo = 'SELECT "TARIF_1","TARIF_2"  FROM "DASHBOARD_TARIF_TW" dtt 
			where "TYPE" = '.$triwulan.' and "KOMODITI" = '.$generalCargo.' and "PELAYANAN" = '.$masaIII.' and "GOLONGAN" = '.$lainII.'
			and "TERMINAL" = '.$terminalIntr.' and "TAHUN" = '.$YEAR.'';
			$data1 = $con->query($tarifCargo)-> result_array();
			if ($data1){							
				$tarifAcargomasa33 = $data1[0]['TARIF_1'];
				$tarifBcargomasa33 = $data1[0]['TARIF_2'];
			} else if (empty($data1))  {		
				$tarifAcargomasa33 = 0;
				$tarifBcargomasa33 = 0;
			}
		}

	
			if (empty($bulan1)){
				$excel->setActiveSheetIndex(0)->setCellValue('E4', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E5', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E6', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E7', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E8', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E9', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E10','0');

				$excel->setActiveSheetIndex(0)->setCellValue('E12', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E13', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E14', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E15', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E16', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E17', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E18', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E19', '0'); 

				$excel->setActiveSheetIndex(0)->setCellValue('E23', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E24', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E25', '0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E26', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E27', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E28', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E29', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E32', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E32', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E33', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E34', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E35', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E36', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E37', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E38', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E39', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E40', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E41', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E42', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E43', '0'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('E47', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E48', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E49', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E51', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E52', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E54', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E55', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E56', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E57', '0');
				$excel->setActiveSheetIndex(0)->setCellValue('E58', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E60', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E61', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E62', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E64', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E65', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E66', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E68', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E69', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E70', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E72', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E73', '0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E74', '0'); 	
				$excel->setActiveSheetIndex(0)->setCellValue('E75', '0');	

			} else if ($bulan1 == 'Januari'){
				$excel->setActiveSheetIndex(0)->setCellValue('E4', $jasaDermaga1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E5', $steveDoring1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E6', $cargoHandling1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E7', $kebersihan1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E8', $masa11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E9', $masa21?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E10', $masa31?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E11', '=SUM(E4:E10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('E15', $jasaA_alber1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E16', $jasaB_alber1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E17', $jasaC_alber1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E18', $jasaD_alber1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E19', $jasaE_alber1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E20', $jasaF_alber1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E21', $jasaG_alber1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E22',  $stevedoring_alber1); 
				$excel->setActiveSheetIndex(0)->setCellValue('E24', $opta1?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('E25', $optb1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E26', $optc1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E27', $optd1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E28', $opte1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E29', $optf1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E30', $optg1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E31', $kebersihan_alber1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E32', $malber11?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E33', $malber21?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E34', $malber31?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E35', '=SUM(E15:E34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('E39', $jasaA_cargo1 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E40', $jasaB_cargo1 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E41', $jasaC_cargo1 ?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('E43', $stevedoring_part1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E44', $stevedoring_lain1?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('E46', $optgcA_1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E47', $optgcB_1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E48', $optgcC_1?:'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('E49', $optgcD_1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E51', $kebersihanPart1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E52', $kebersihanLain1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E53', $kebersihanAtas1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E55', $MasaA1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E56', $MasaB1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E57', $MasaC1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E59', $MasaD1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E60', $MasaE1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E61', $MasaF1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E63', $MasaG1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E64', $MasaH1?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('E65', $MasaI1?:'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('E66','=SUM(E39:E65)');			

			} 

			if (empty($bulan2)){
				$excel->setActiveSheetIndex(0)->setCellValue('F5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('F6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('F10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('F12', ''); 
			} else if ($bulan2 == 'Februari'){
				$excel->setActiveSheetIndex(0)->setCellValue('F4', $jasaDermaga2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F5', $steveDoring2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F6', $cargoHandling2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F7', $kebersihan2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F8', $masa12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F9', $masa22?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F10', $masa32?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F11', '=SUM(F4:F10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('F15', $jasaA_alber2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F16', $jasaB_alber2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F17', $jasaC_alber2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F18', $jasaD_alber2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F19', $jasaE_alber2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F20', $jasaF_alber2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F21', $jasaG_alber2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F22',  $stevedoring_alber2); 
				$excel->setActiveSheetIndex(0)->setCellValue('F24', $opta2?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('F25', $optb2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F26', $optc2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F27', $optd2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F28', $opte2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F29', $optf2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F30', $optg2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F31', $kebersihan_alber2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F32', $malber12?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F33', $malber22?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F34', $malber32?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F35', '=SUM(F15:F34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('F39', $jasaA_cargo2 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F40', $jasaB_cargo2 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F41', $jasaC_cargo2 ?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('F43', $stevedoring_part2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F44', $stevedoring_lain2?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('F46', $optgcA_2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F47', $optgcB_2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F48', $optgcC_2?:'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('F49', $optgcD_2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F51', $kebersihanPart2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F52', $kebersihanLain2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F53', $kebersihanAtas2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F55', $MasaA2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F56', $MasaB2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F57', $MasaC2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F59', $MasaD2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F60', $MasaE2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F61', $MasaF2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F63', $MasaG2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F64', $MasaH2?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('F65', $MasaI2?:'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('F66','=SUM(F39:F65)');	
			}
			
			if (empty($bulan3)){
				$excel->setActiveSheetIndex(0)->setCellValue('G5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('G6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('G10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('G12', ''); 
			} else if ($bulan3 == 'Maret'){
				$excel->setActiveSheetIndex(0)->setCellValue('G4', $jasaDermaga3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G5', $steveDoring3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G6', $cargoHandling3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G7', $kebersihan3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G8', $masa13?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G9', $masa23?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G10', $masa33?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G11', '=SUM(G4:G10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('G15', $jasaA_alber3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G16', $jasaB_alber3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G17', $jasaC_alber3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G18', $jasaD_alber3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G19', $jasaE_alber3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G20', $jasaF_alber3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G21', $jasaG_alber3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G22',  $stevedoring_alber3); 
				$excel->setActiveSheetIndex(0)->setCellValue('G24', $opta3?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('G25', $optb3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G26', $optc3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G27', $optd3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G28', $opte3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G29', $optf3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G30', $optg3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G31', $kebersihan_alber3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G32', $malber13?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G33', $malber23?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G34', $malber33?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G35', '=SUM(G15:G34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('G39', $jasaA_cargo3 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G40', $jasaB_cargo3 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G41', $jasaC_cargo3 ?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('G43', $stevedoring_part3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G44', $stevedoring_lain3?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('G46', $optgcA_3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G47', $optgcB_3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G48', $optgcC_3?:'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('G49', $optgcD_3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G51', $kebersihanPart3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G52', $kebersihanLain3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G53', $kebersihanAtas3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G55', $MasaA3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G56', $MasaB3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G57', $MasaC3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G59', $MasaD3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G60', $MasaE3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G61', $MasaF3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G63', $MasaG3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G64', $MasaH3?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('G65', $MasaI3?:'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('G66','=SUM(G39:G65)');	
			}
			if (empty($bulan4)){
				$excel->setActiveSheetIndex(0)->setCellValue('H5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('H6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('H10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('H12', ''); 
			} else if ($bulan4 == 'April'){
				$excel->setActiveSheetIndex(0)->setCellValue('H4', $jasaDermaga4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H5', $steveDoring4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H6', $cargoHandling4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H7', $kebersihan4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H8', $masa14?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H9', $masa24?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H10', $masa34?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H11', '=SUM(H4:H10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('H15', $jasaA_alber4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H16', $jasaB_alber4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H17', $jasaC_alber4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H18', $jasaD_alber4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H19', $jasaE_alber4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H20', $jasaF_alber4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H21', $jasaG_alber4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H22',  $stevedoring_alber4); 
				$excel->setActiveSheetIndex(0)->setCellValue('H24', $opta4?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('H25', $optb4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H26', $optc4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H27', $optd4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H28', $opte4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H29', $optf4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H30', $optg4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H31', $kebersihan_alber4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H32', $malber14?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H33', $malber24?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H34', $malber34?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H35', '=SUM(H15:H34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('H39', $jasaA_cargo4 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H40', $jasaB_cargo4 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H41', $jasaC_cargo4 ?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('H43', $stevedoring_part4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H44', $stevedoring_lain4?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('H46', $optgcA_4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H47', $optgcB_4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H48', $optgcC_4?:'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('H49', $optgcD_4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H51', $kebersihanPart4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H52', $kebersihanLain4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H53', $kebersihanAtas4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H55', $MasaA4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H56', $MasaB4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H57', $MasaC4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H59', $MasaD4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H60', $MasaE4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H61', $MasaF4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H63', $MasaG4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H64', $MasaH4?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('H65', $MasaI4?:'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('H66','=SUM(H39:H65)');		
			}
			if (empty($bulan5)){
				$excel->setActiveSheetIndex(0)->setCellValue('I5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('I6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('I10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('I12', ''); 
			} else if ($bulan5 == 'Mei'){
				$excel->setActiveSheetIndex(0)->setCellValue('I4', $jasaDermaga5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I5', $steveDoring5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I6', $cargoHandling5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I7', $kebersihan5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I8', $masa15?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I9', $masa25?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I10', $masa35?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I11', '=SUM(I4:I10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('I15', $jasaA_alber5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I16', $jasaB_alber5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I17', $jasaC_alber5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I18', $jasaD_alber5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I19', $jasaE_alber5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I20', $jasaF_alber5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I21', $jasaG_alber5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I22',  $stevedoring_alber5); 
				$excel->setActiveSheetIndex(0)->setCellValue('I24', $opta5?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('I25', $optb5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I26', $optc5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I27', $optd5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I28', $opte5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I29', $optf5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I30', $optg5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I31', $kebersihan_alber5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I32', $malber15?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I33', $malber25?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I34', $malber35?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I35', '=SUM(I15:I34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('I39', $jasaA_cargo5 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I40', $jasaB_cargo5 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I41', $jasaC_cargo5 ?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('I43', $stevedoring_part5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I44', $stevedoring_lain5?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('I46', $optgcA_5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I47', $optgcB_5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I48', $optgcC_5?:'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('I49', $optgcD_5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I51', $kebersihanPart5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I52', $kebersihanLain5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I53', $kebersihanAtas5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I55', $MasaA5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I56', $MasaB5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I57', $MasaC5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I59', $MasaD5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I60', $MasaE5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I61', $MasaF5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I63', $MasaG5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I64', $MasaH5?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('I65', $MasaI5?:'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('I66','=SUM(I39:I65)');	
			}
		
			if (empty($bulan6)){
				$excel->setActiveSheetIndex(0)->setCellValue('J5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('J6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('J10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('J12', ''); 
			} else if ($bulan6 == 'Juni'){
				$excel->setActiveSheetIndex(0)->setCellValue('J4', $jasaDermaga6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J5', $steveDoring6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J6', $cargoHandling6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J7', $kebersihan6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J8', $masa16?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J9', $masa26?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J10', $masa36?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J11', '=SUM(J4:J10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('J15', $jasaA_alber6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J16', $jasaB_alber6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J17', $jasaC_alber6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J18', $jasaD_alber6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J19', $jasaE_alber6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J20', $jasaF_alber6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J21', $jasaG_alber6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J22',  $stevedoring_alber6); 
				$excel->setActiveSheetIndex(0)->setCellValue('J24', $opta6?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('J25', $optb6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J26', $optc6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J27', $optd6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J28', $opte6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J29', $optf6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J30', $optg6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J31', $kebersihan_alber6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J32', $malber16?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J33', $malber26?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J34', $malber36?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J35', '=SUM(J15:J34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('J39', $jasaA_cargo6 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J40', $jasaB_cargo6 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J41', $jasaC_cargo6 ?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('J43', $stevedoring_part6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J44', $stevedoring_lain6?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('J46', $optgcA_6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J47', $optgcB_6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J48', $optgcC_6?:'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('J49', $optgcD_6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J51', $kebersihanPart6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J52', $kebersihanLain6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J53', $kebersihanAtas6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J55', $MasaA6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J56', $MasaB6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J57', $MasaC6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J59', $MasaD6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J60', $MasaE6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J61', $MasaF6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J63', $MasaG6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J64', $MasaH6?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('J65', $MasaI6?:'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('J66','=SUM(J39:J65)');	
			} 
			if (empty($bulan7)){
				$excel->setActiveSheetIndex(0)->setCellValue('K5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('K6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('K10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('K12', ''); 
			} else if ($bulan7 == 'Juli'){
				$excel->setActiveSheetIndex(0)->setCellValue('K4', $jasaDermaga7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K5', $steveDoring7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K6', $cargoHandling7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K7', $kebersihan7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K8', $masa17? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K9', $masa27? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K10', $masa37? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K11', '=SUM(K4:K10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('K15', $jasaA_alber7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K16', $jasaB_alber7? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K17', $jasaC_alber7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K18', $jasaD_alber7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K19', $jasaE_alber7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K20', $jasaF_alber7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K21', $jasaG_alber7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K22',  $stevedoring_alber7? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K24', $opta7? :'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('K25', $optb7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K26', $optc7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K27', $optd7? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K28', $opte7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K29', $optf7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K30', $optg7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K31', $kebersihan_alber7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K32', $malber17? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K33', $malber27? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K34', $malber37? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K35', '=SUM(K15:K34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('K39', $jasaA_cargo7 ?  :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K40', $jasaB_cargo7 ?  :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K41', $jasaC_cargo7 ?  :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('K43', $stevedoring_part7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K44', $stevedoring_lain7? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('K46', $optgcA_7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K47', $optgcB_7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K48', $optgcC_7? :'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('K49', $optgcD_7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K51', $kebersihanPart7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K52', $kebersihanLain7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K53', $kebersihanAtas7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K55', $MasaA7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K56', $MasaB7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K57', $MasaC7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K59', $MasaD7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K60', $MasaE7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K61', $MasaF7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K63', $MasaG7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K64', $MasaH7? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('K65', $MasaI7? :'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('K66','=SUM(K39:K65)');	
			} 
	
			if (empty($bulan8)){
				$excel->setActiveSheetIndex(0)->setCellValue('L5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('L6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('L10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('L12', ''); 
			} else if ($bulan8 == 'Agustus'){
				$excel->setActiveSheetIndex(0)->setCellValue('L4', $jasaDermaga8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L5', $steveDoring8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L6', $cargoHandling8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L7', $kebersihan8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L8', $masa18?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L9', $masa28?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L10', $masa38?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L11', '=SUM(L4:L10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('L15', $jasaA_alber8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L16', $jasaB_alber8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L17', $jasaC_alber8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L18', $jasaD_alber8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L19', $jasaE_alber8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L20', $jasaF_alber8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L21', $jasaG_alber8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L22',  $stevedoring_alber8); 
				$excel->setActiveSheetIndex(0)->setCellValue('L24', $opta8?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('L25', $optb8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L26', $optc8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L27', $optd8?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L28', $opte8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L29', $optf8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L30', $optg8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L31', $kebersihan_alber8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L32', $malber18?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L33', $malber28?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L34', $malber38?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L35', '=SUM(L15:L34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('L39', $jasaA_cargo8 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L40', $jasaB_cargo8 ?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L41', $jasaC_cargo8 ?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('L43', $stevedoring_part8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L44', $stevedoring_lain8?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('L46', $optgcA_8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L47', $optgcB_8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L48', $optgcC_8?:'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('L49', $optgcD_8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L51', $kebersihanPart8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L52', $kebersihanLain8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L53', $kebersihanAtas8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L55', $MasaA8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L56', $MasaB8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L57', $MasaC8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L59', $MasaD8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L60', $MasaE8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L61', $MasaF8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L63', $MasaG8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L64', $MasaH8?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('L65', $MasaI8?:'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('L66','=SUM(L39:L65)');	
			}
			
			if (empty($bulan9)){
				$excel->setActiveSheetIndex(0)->setCellValue('M5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('M6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('M10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('M12', ''); 
			} else if ($bulan9 == 'September'){
				$excel->setActiveSheetIndex(0)->setCellValue('M4', $jasaDermaga9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M5', $steveDoring9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M6', $cargoHandling9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M7', $kebersihan9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M8', $masa19?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M9', $masa29?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M10', $masa39?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M11', '=SUM(M4:M10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('M15', $jasaA_alber9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M16', $jasaB_alber9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M17', $jasaC_alber9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M18', $jasaD_alber9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M19', $jasaE_alber9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M20', $jasaF_alber9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M21', $jasaG_alber9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M22',  $stevedoring_alber9); 
				$excel->setActiveSheetIndex(0)->setCellValue('M24', $opta9?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('M25', $optb9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M26', $optc9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M27', $optd9?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M28', $opte9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M29', $optf9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M30', $optg9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M31', $kebersihan_alber9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M32', $malber19?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M33', $malber29?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M34', $malber39?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M35', '=SUM(M15:M34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('M39', $jasaA_cargo9 ? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M40', $jasaB_cargo9 ? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M41', $jasaC_cargo9 ? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('M43', $stevedoring_part9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M44', $stevedoring_lain9?:'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('M46', $optgcA_9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M47', $optgcB_9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M48', $optgcC_9?:'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('M49', $optgcD_9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M51', $kebersihanPart9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M52', $kebersihanLain9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M53', $kebersihanAtas9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M55', $MasaA9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M56', $MasaB9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M57', $MasaC9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M59', $MasaD9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M60', $MasaE9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M61', $MasaF9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M63', $MasaG9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M64', $MasaH9?:'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('M65', $MasaI9?:'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('M66','=SUM(M39:M65)');	
			}
			
			if (empty($bulan10)){
				$excel->setActiveSheetIndex(0)->setCellValue('N5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('N6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('N10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('N12', ''); 
			} else if ($bulan10 == 'Oktober'){
				$excel->setActiveSheetIndex(0)->setCellValue('N4', $jasaDermaga10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N5', $steveDoring10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N6', $cargoHandling10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N7', $kebersihan10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N8', $masa110? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N9', $masa210? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N10', $masa310? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N11', '=SUM(N4:N10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('N15', $jasaA_alber10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N16', $jasaB_alber10? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N17', $jasaC_alber10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N18', $jasaD_alber10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N19', $jasaE_alber10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N20', $jasaF_alber10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N21', $jasaG_alber10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N22',  $stevedoring_alber10? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N24', $opta10? :'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('N25', $optb10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N26', $optc10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N27', $optd10? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N28', $opte10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N29', $optf10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N30', $optg10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N31', $kebersihan_alber10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N32', $malber110? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N33', $malber210? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N34', $malber310? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N35', '=SUM(N15:N34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('N39', $jasaA_cargo10 ?  :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N40', $jasaB_cargo10 ?  :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N41', $jasaC_cargo10 ?  :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('N43', $stevedoring_part10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N44', $stevedoring_lain10? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('N46', $optgcA_10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N47', $optgcB_10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N48', $optgcC_10? :'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('N49', $optgcD_10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N51', $kebersihanPart10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N52', $kebersihanLain10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N53', $kebersihanAtas10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N55', $MasaA10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N56', $MasaB10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N57', $MasaC10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N59', $MasaD10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N60', $MasaE10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N61', $MasaF10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N63', $MasaG10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N64', $MasaH10? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('N65', $MasaI10? :'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('N66','=SUM(N39:N65)');	
			}
			
			if (empty($bulan11)){
				$excel->setActiveSheetIndex(0)->setCellValue('O5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('O6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('O10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('O12', ''); 
			} else if ($bulan11 ==  'November'){
				$excel->setActiveSheetIndex(0)->setCellValue('O4', $jasaDermaga11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O5', $steveDoring11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O6', $cargoHandling11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O7', $kebersihan11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O8', $masa111? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O9', $masa211? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O10', $masa311? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O11', '=SUM(O4:O10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('O15', $jasaA_alber11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O16', $jasaB_alber11? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O17', $jasaC_alber11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O18', $jasaD_alber11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O19', $jasaE_alber11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O20', $jasaF_alber11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O21', $jasaG_alber11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O22',  $stevedoring_alber11? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O24', $opta11? :'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('O25', $optb11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O26', $optc11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O27', $optd11? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O28', $opte11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O29', $optf11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O30', $optg11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O31', $kebersihan_alber11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O32', $malber111? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O33', $malber211? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O34', $malber311? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O35', '=SUM(O15:O34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('O39', $jasaA_cargo11 ?  :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O40', $jasaB_cargo11 ?  :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O41', $jasaC_cargo11 ?  :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('O43', $stevedoring_part11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O44', $stevedoring_lain11? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('O46', $optgcA_11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O47', $optgcB_11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O48', $optgcC_11? :'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('O49', $optgcD_11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O51', $kebersihanPart11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O52', $kebersihanLain11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O53', $kebersihanAtas11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O55', $MasaA11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O56', $MasaB11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O57', $MasaC11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O59', $MasaD11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O60', $MasaE11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O61', $MasaF11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O63', $MasaG11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O64', $MasaH11? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('O65', $MasaI11? :'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('O66','=SUM(O39:O65)');	
			}

			
			if (empty($bulan12)){
				$excel->setActiveSheetIndex(0)->setCellValue('P5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('P6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('P10', '');
				$excel->setActiveSheetIndex(0)->setCellValue('P12', ''); 
			} else if ($bulan12 == 'Desember'){
				$excel->setActiveSheetIndex(0)->setCellValue('P4', $jasaDermaga12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P5', $steveDoring12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P6', $cargoHandling12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P7', $kebersihan12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P8', $masa112? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P9', $masa212? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P10', $masa312? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P11', '=SUM(P4:P10)'); 

				$excel->setActiveSheetIndex(0)->setCellValue('P15', $jasaA_alber12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P16', $jasaB_alber12? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P17', $jasaC_alber12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P18', $jasaD_alber12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P19', $jasaE_alber12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P20', $jasaF_alber12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P21', $jasaG_alber12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P22',  $stevedoring_alber12? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P24', $opta12? :'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('P25', $optb12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P26', $optc12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P27', $optd12? :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P28', $opte12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P29', $optf12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P30', $optg12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P31', $kebersihan_alber12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P32', $malber112? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P33', $malber212? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P34', $malber312? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P35', '=SUM(P15:P34)'); 
		
				$excel->setActiveSheetIndex(0)->setCellValue('P39', $jasaA_cargo12 ?  :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P40', $jasaB_cargo12 ?  :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P41', $jasaC_cargo12 ?  :'0');
				$excel->setActiveSheetIndex(0)->setCellValue('P43', $stevedoring_part12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P44', $stevedoring_lain12? :'0'); 
				$excel->setActiveSheetIndex(0)->setCellValue('P46', $optgcA_12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P47', $optgcB_12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P48', $optgcC_12? :'0'); 		
				$excel->setActiveSheetIndex(0)->setCellValue('P49', $optgcD_12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P51', $kebersihanPart12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P52', $kebersihanLain12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P53', $kebersihanAtas12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P55', $MasaA12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P56', $MasaB12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P57', $MasaC12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P59', $MasaD12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P60', $MasaE12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P61', $MasaF12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P63', $MasaG12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P64', $MasaH12? :'0');  
				$excel->setActiveSheetIndex(0)->setCellValue('P65', $MasaI12? :'0');  	
				$excel->setActiveSheetIndex(0)->setCellValue('P66','=SUM(P39:P65)');	
			}
			
			$excel->setActiveSheetIndex(0)->setCellValue('Q4', '=SUM(E4:P4)');    
			$excel->setActiveSheetIndex(0)->setCellValue('Q5', '=SUM(E5:P5)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q6', '=SUM(E6:P6)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q7', '=SUM(E7:P7)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q8', '=SUM(E8:P8)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q9', '=SUM(E9:P9)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q10','=SUM(E10:P10)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q11', '=SUM(E11:P11)');

			$excel->setActiveSheetIndex(0)->setCellValue('Q15', '=SUM(E15:P15)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q16', '=SUM(E16:P16)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q17', '=SUM(E17:P17)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q18', '=SUM(E18:P18)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q19', '=SUM(E19:P19)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q20', '=SUM(E20:P20)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q21', '=SUM(E21:P21)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q22', '=SUM(E22:P22)');

			$excel->setActiveSheetIndex(0)->setCellValue('Q24', '=SUM(E24:P24)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q25', '=SUM(E25:P25)');				
			$excel->setActiveSheetIndex(0)->setCellValue('Q26', '=SUM(E26:P26)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q27', '=SUM(E27:P27)');				
			$excel->setActiveSheetIndex(0)->setCellValue('Q28', '=SUM(E28:P28)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q29', '=SUM(E29:P29)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q30', '=SUM(E30:P30)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q31', '=SUM(E31:P31)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q32', '=SUM(E32:P32)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q33', '=SUM(E33:P33)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q34', '=SUM(E34:P34)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q35', '=SUM(E35:P35)');
		
			$excel->setActiveSheetIndex(0)->setCellValue('Q39', '=SUM(E39:P39)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q40', '=SUM(E40:P40)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q41', '=SUM(E41:P41)');

			$excel->setActiveSheetIndex(0)->setCellValue('Q43', '=SUM(E43:P43)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q44', '=SUM(E44:P44)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q46', '=SUM(E46:P46)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q47', '=SUM(E47:P47)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q48', '=SUM(E48:P48)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q49', '=SUM(E49:P49)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q51', '=SUM(E51:P51)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q52', '=SUM(E52:P52)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q53', '=SUM(E53:P53)');

			$excel->setActiveSheetIndex(0)->setCellValue('Q55', '=SUM(E55:P55)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q56', '=SUM(E56:P56)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q57', '=SUM(E57:P57)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q59', '=SUM(E59:P59)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q60', '=SUM(E60:P60)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q61', '=SUM(E61:P61)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q63', '=SUM(E63:P63)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q64', '=SUM(E64:P64)');
			$excel->setActiveSheetIndex(0)->setCellValue('Q65', '=SUM(E65:P65)');	
			$excel->setActiveSheetIndex(0)->setCellValue('Q66', '=SUM(E66:P66)');
			
			if($tipe == 'PER TAHUN'){
				$excel->setActiveSheetIndex(0)->setCellValue('R4', $tarifAdermaga?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R5', $tarifAstvdoring?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R6', $tarifAcargohandling?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R7', $tarifAkebersihan?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R8', $tarifAmasaI?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R9', $tarifAmasaII?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R10', $tarifAmasaIII?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R11', '=SUM(R4:R10)');	

				$excel->setActiveSheetIndex(0)->setCellValue('R15', $tarifAalber1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R16', $tarifAalber2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R17', $tarifAalber3?:'0');				
				$excel->setActiveSheetIndex(0)->setCellValue('R18', $tarifAalber4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R19', $tarifAalber5?:'0');			
				$excel->setActiveSheetIndex(0)->setCellValue('R20', $tarifAalber6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R21', $tarifAalber7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R22', $tarifAstvalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R24', $tarifAoppt1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R25', $tarifAoppt2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R26', $tarifAoppt3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R27', $tarifAoppt4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R28', $tarifAoppt5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R29', $tarifAoppt6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R30', $tarifAoppt7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R31', $tarifAkebersihanoppt?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R32', $tarifAmasaIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R33', $tarifAmasaIIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R34', $tarifAmasaIIIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R35', '=SUM(R15:R34)');						
				$excel->setActiveSheetIndex(0)->setCellValue('R39', $tarifAcargojasa?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R40', $tarifAcargolain?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R41', $tarifAcargolain2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R43', $tarifAcargostv?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R44', $tarifAstvlain?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R46', $tarifAcargooppt1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R47', $tarifAcargooppt2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R48', $tarifAcargooppt3?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('R49', $tarifAcargooppt5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R51', $tarifAcargokebersihan1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R52', $tarifAcargokebersihan2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R53', $tarifAcargokebersihan3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R55', $tarifAcargomasa11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R56', $tarifAcargomasa12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R57', $tarifAcargomasa13?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R59', $tarifAcargomasa21?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R60', $tarifAcargomasa22?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R61', $tarifAcargomasa23?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R63', $tarifAcargomasa31?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R64', $tarifAcargomasa32?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R65', $tarifAcargomasa33?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R66', '=SUM(R39:R65)');

				$excel->setActiveSheetIndex(0)->setCellValue('S4', '=(Q4*R4)');
				$excel->setActiveSheetIndex(0)->setCellValue('S5', '=(Q5*R5)');
				$excel->setActiveSheetIndex(0)->setCellValue('S6', '=(Q6*R6)');
				$excel->setActiveSheetIndex(0)->setCellValue('S7', '=(Q7*R7)');
				$excel->setActiveSheetIndex(0)->setCellValue('S8', '=(Q8*R8)');	
				$excel->setActiveSheetIndex(0)->setCellValue('S9', '=(Q9*R9)');	
				$excel->setActiveSheetIndex(0)->setCellValue('S10', '=(Q10*R10)');	
				$excel->setActiveSheetIndex(0)->setCellValue('S11', '=(Q11*R11)');
		
				$excel->setActiveSheetIndex(0)->setCellValue('S15', '=(Q15*R15)');
				$excel->setActiveSheetIndex(0)->setCellValue('S16', '=(Q16*R16)');
				$excel->setActiveSheetIndex(0)->setCellValue('S17', '=(Q17*R17)');
				$excel->setActiveSheetIndex(0)->setCellValue('S18', '=(Q18*R18)');
				$excel->setActiveSheetIndex(0)->setCellValue('S19', '=(Q19*R19)');				
				$excel->setActiveSheetIndex(0)->setCellValue('S20', '=(Q20*R20)');
				$excel->setActiveSheetIndex(0)->setCellValue('S21', '=(Q21*R21)');
				$excel->setActiveSheetIndex(0)->setCellValue('S22', '=(Q22*R22)');		
				$excel->setActiveSheetIndex(0)->setCellValue('S24', '=(Q24*R24)');
				$excel->setActiveSheetIndex(0)->setCellValue('S25', '=(Q25*R25)');
				$excel->setActiveSheetIndex(0)->setCellValue('S26', '=(Q26*R26)');
				$excel->setActiveSheetIndex(0)->setCellValue('S27', '=(Q27*R27)');
				$excel->setActiveSheetIndex(0)->setCellValue('S28', '=(Q28*R28)');
				$excel->setActiveSheetIndex(0)->setCellValue('S29', '=(Q29*R29)');
				$excel->setActiveSheetIndex(0)->setCellValue('S30', '=(Q30*R30)');
				$excel->setActiveSheetIndex(0)->setCellValue('S31', '=(Q31*R31)');
				$excel->setActiveSheetIndex(0)->setCellValue('S32', '=(Q32*R32)');
				$excel->setActiveSheetIndex(0)->setCellValue('S33', '=(Q33*R33)');
				$excel->setActiveSheetIndex(0)->setCellValue('S34', '=(Q34*R34)');
				$excel->setActiveSheetIndex(0)->setCellValue('S35', '=(Q35*R35)');			
				$excel->setActiveSheetIndex(0)->setCellValue('S39', '=(Q39*R39)');
				$excel->setActiveSheetIndex(0)->setCellValue('S40', '=(Q40*R40)');
				$excel->setActiveSheetIndex(0)->setCellValue('S41', '=(Q41*R41)');
				$excel->setActiveSheetIndex(0)->setCellValue('S43', '=(Q43*R43)');
				$excel->setActiveSheetIndex(0)->setCellValue('S44', '=(Q44*R44)');				
				$excel->setActiveSheetIndex(0)->setCellValue('S46', '=(Q46*R46)');
				$excel->setActiveSheetIndex(0)->setCellValue('S47', '=(Q47*R47)');
				$excel->setActiveSheetIndex(0)->setCellValue('S48', '=(Q48*R48)');
				$excel->setActiveSheetIndex(0)->setCellValue('S49', '=(Q49*R49)');
				$excel->setActiveSheetIndex(0)->setCellValue('S51', '=(Q51*R51)');
				$excel->setActiveSheetIndex(0)->setCellValue('S52', '=(Q52*R52)');
				$excel->setActiveSheetIndex(0)->setCellValue('S53', '=(Q53*R53)');	
				$excel->setActiveSheetIndex(0)->setCellValue('S55', '=(Q55*R55)');
				$excel->setActiveSheetIndex(0)->setCellValue('S56', '=(Q56*R56)');
				$excel->setActiveSheetIndex(0)->setCellValue('S57', '=(Q57*R57)');
				$excel->setActiveSheetIndex(0)->setCellValue('S59', '=(Q59*R59)');
				$excel->setActiveSheetIndex(0)->setCellValue('S60', '=(Q60*R60)');
				$excel->setActiveSheetIndex(0)->setCellValue('S61', '=(Q61*R61)');
				$excel->setActiveSheetIndex(0)->setCellValue('S63', '=(Q63*R63)');
				$excel->setActiveSheetIndex(0)->setCellValue('S64', '=(Q64*R64)');
				$excel->setActiveSheetIndex(0)->setCellValue('S65', '=(Q65*R65)');			
			
				$excel->setActiveSheetIndex(0)->setCellValue('T4', '=(Q4*R4)');
				$excel->setActiveSheetIndex(0)->setCellValue('T5', '=(Q5*R5)');
				$excel->setActiveSheetIndex(0)->setCellValue('T6', '=(Q6*R6)');
				$excel->setActiveSheetIndex(0)->setCellValue('T7', '=(Q7*R7)');
				$excel->setActiveSheetIndex(0)->setCellValue('T8', '=(Q8*R8)');	
				$excel->setActiveSheetIndex(0)->setCellValue('T9', '=(Q9*R9)');	
				$excel->setActiveSheetIndex(0)->setCellValue('T10', '=(Q10*R10)');	
				$excel->setActiveSheetIndex(0)->setCellValue('T11', '=(Q11*R11)');
	
				$excel->setActiveSheetIndex(0)->setCellValue('T15', '=(Q15*R15)');
				$excel->setActiveSheetIndex(0)->setCellValue('T16', '=(Q16*R16)');
				$excel->setActiveSheetIndex(0)->setCellValue('T17', '=(Q17*R17)');
				$excel->setActiveSheetIndex(0)->setCellValue('T18', '=(Q18*R18)');
				$excel->setActiveSheetIndex(0)->setCellValue('T19', '=(Q19*R19)');
				$excel->setActiveSheetIndex(0)->setCellValue('T15', '=(Q15*R15)');
				$excel->setActiveSheetIndex(0)->setCellValue('T16', '=(Q16*R16)');
				$excel->setActiveSheetIndex(0)->setCellValue('T17', '=(Q17*R17)');
				$excel->setActiveSheetIndex(0)->setCellValue('T18', '=(Q18*R18)');
				$excel->setActiveSheetIndex(0)->setCellValue('T19', '=(Q19*R19)');				
				$excel->setActiveSheetIndex(0)->setCellValue('T20', '=(Q20*R20)');
				$excel->setActiveSheetIndex(0)->setCellValue('T21', '=(Q21*R21)');
				$excel->setActiveSheetIndex(0)->setCellValue('T22', '=(Q22*R22)');		
				$excel->setActiveSheetIndex(0)->setCellValue('T24', '=(Q24*R24)');
				$excel->setActiveSheetIndex(0)->setCellValue('T25', '=(Q25*R25)');
				$excel->setActiveSheetIndex(0)->setCellValue('T26', '=(Q26*R26)');
				$excel->setActiveSheetIndex(0)->setCellValue('T27', '=(Q27*R27)');
				$excel->setActiveSheetIndex(0)->setCellValue('T28', '=(Q28*R28)');
				$excel->setActiveSheetIndex(0)->setCellValue('T29', '=(Q29*R29)');
				$excel->setActiveSheetIndex(0)->setCellValue('T30', '=(Q30*R30)');
				$excel->setActiveSheetIndex(0)->setCellValue('T31', '=(Q31*R31)');
				$excel->setActiveSheetIndex(0)->setCellValue('T32', '=(Q32*R32)');
				$excel->setActiveSheetIndex(0)->setCellValue('T33', '=(Q33*R33)');
				$excel->setActiveSheetIndex(0)->setCellValue('T34', '=(Q34*R34)');
				$excel->setActiveSheetIndex(0)->setCellValue('T35', '=(Q35*R35)');			
				$excel->setActiveSheetIndex(0)->setCellValue('T39', '=(Q39*R39)');
				$excel->setActiveSheetIndex(0)->setCellValue('T40', '=(Q40*R40)');
				$excel->setActiveSheetIndex(0)->setCellValue('T41', '=(Q41*R41)');
				$excel->setActiveSheetIndex(0)->setCellValue('T43', '=(Q43*R43)');
				$excel->setActiveSheetIndex(0)->setCellValue('T44', '=(Q44*R44)');				
				$excel->setActiveSheetIndex(0)->setCellValue('T46', '=(Q46*R46)');
				$excel->setActiveSheetIndex(0)->setCellValue('T47', '=(Q47*R47)');
				$excel->setActiveSheetIndex(0)->setCellValue('T48', '=(Q48*R48)');
				$excel->setActiveSheetIndex(0)->setCellValue('T49', '=(Q49*R49)');
				$excel->setActiveSheetIndex(0)->setCellValue('T51', '=(Q51*R51)');
				$excel->setActiveSheetIndex(0)->setCellValue('T52', '=(Q52*R52)');
				$excel->setActiveSheetIndex(0)->setCellValue('T53', '=(Q53*R53)');
				$excel->setActiveSheetIndex(0)->setCellValue('T55', '=(Q55*R55)');
				$excel->setActiveSheetIndex(0)->setCellValue('T56', '=(Q56*R56)');
				$excel->setActiveSheetIndex(0)->setCellValue('T57', '=(Q57*R57)');
				$excel->setActiveSheetIndex(0)->setCellValue('T59', '=(Q59*R59)');
				$excel->setActiveSheetIndex(0)->setCellValue('T60', '=(Q60*R60)');
				$excel->setActiveSheetIndex(0)->setCellValue('T61', '=(Q61*R61)');
				$excel->setActiveSheetIndex(0)->setCellValue('T63', '=(Q63*R63)');
				$excel->setActiveSheetIndex(0)->setCellValue('T64', '=(Q64*R64)');
				$excel->setActiveSheetIndex(0)->setCellValue('T65', '=(Q65*R65)');				
				$excel->setActiveSheetIndex(0)->setCellValue('T66', '=(Q66*R66)');					
				
				$excel->setActiveSheetIndex(0)->setCellValue('V4', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V5', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V6', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V7', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V8', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('V9', '');	
				$excel->setActiveSheetIndex(0)->setCellValue('V10', '');		

				$excel->setActiveSheetIndex(0)->setCellValue('V12', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V13', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V14', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V15', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V16', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V17', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V18', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V19', '');

				$excel->setActiveSheetIndex(0)->setCellValue('V23', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V24', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V25', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V26', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V27', '');				
				$excel->setActiveSheetIndex(0)->setCellValue('V28', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V29', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V30', '');
		
				$excel->setActiveSheetIndex(0)->setCellValue('V32', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V33', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V34', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V35', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V36', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V37', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V38', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V39', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V40', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V41', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V42', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V43', '');
			
				$excel->setActiveSheetIndex(0)->setCellValue('V47', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V48', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V49', '');

				$excel->setActiveSheetIndex(0)->setCellValue('V51', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V52', '');
				
				$excel->setActiveSheetIndex(0)->setCellValue('V54', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V55', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V56', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V57', '');

				$excel->setActiveSheetIndex(0)->setCellValue('V59', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V60', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V61', '');		

				$excel->setActiveSheetIndex(0)->setCellValue('V63', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V64', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V65', '');

				$excel->setActiveSheetIndex(0)->setCellValue('V67', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V68', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V69', '');	

				$excel->setActiveSheetIndex(0)->setCellValue('V71', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V72', '');
				$excel->setActiveSheetIndex(0)->setCellValue('V73', '');				
				$excel->setActiveSheetIndex(0)->setCellValue('V74', '');
				}
				if($tipe == 'PER SEMESTER' || $tipe == 'PER TRIWULAN'){
				$excel->setActiveSheetIndex(0)->setCellValue('R4', $tarifAdermaga?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R5', $tarifAstvdoring?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R6', $tarifAcargohandling?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R7', $tarifAkebersihan?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R8', $tarifAmasaI?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R9', $tarifAmasaII?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R10', $tarifAmasaIII?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R11', '=SUM(R4:R10)');

				$excel->setActiveSheetIndex(0)->setCellValue('R15', $tarifAalber1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R16', $tarifAalber2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R17', $tarifAalber3?:'0');				
				$excel->setActiveSheetIndex(0)->setCellValue('R18', $tarifAalber4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R19', $tarifAalber5?:'0');			
				$excel->setActiveSheetIndex(0)->setCellValue('R20', $tarifAalber6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R21', $tarifAalber7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R22', $tarifAstvalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R24', $tarifAoppt1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R25', $tarifAoppt2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R26', $tarifAoppt3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R27', $tarifAoppt4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R28', $tarifAoppt5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R29', $tarifAoppt6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R30', $tarifAoppt7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R31', $tarifAkebersihanoppt?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R32', $tarifAmasaIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R33', $tarifAmasaIIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R34', $tarifAmasaIIIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R35', '=SUM(R15:R34)');	

				$excel->setActiveSheetIndex(0)->setCellValue('R39', $tarifAcargojasa?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R40', $tarifAcargolain?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R41', $tarifAcargolain2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R43', $tarifAcargostv?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R44', $tarifAstvlain?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R46', $tarifAcargooppt1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R47', $tarifAcargooppt2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R48', $tarifAcargooppt3?:'0');	
				$excel->setActiveSheetIndex(0)->setCellValue('R49', $tarifAcargooppt5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R51', $tarifAcargokebersihan1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R52', $tarifAcargokebersihan2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R53', $tarifAcargokebersihan3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R55', $tarifAcargomasa11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R56', $tarifAcargomasa12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R57', $tarifAcargomasa13?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R59', $tarifAcargomasa21?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R60', $tarifAcargomasa22?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R61', $tarifAcargomasa23?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R63', $tarifAcargomasa31?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R64', $tarifAcargomasa32?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R65', $tarifAcargomasa33?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('R66', '=SUM(R39:R65)');

				$excel->setActiveSheetIndex(0)->setCellValue('S4', $tarifBdermaga?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S5', $tarifBstvdoring?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S6', $tarifBcargohandling?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S7', $tarifBkebersihan?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S8', $tarifBmasaI?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S9', $tarifBmasaII?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S10', $tarifBmasaIII?:'0');		
				$excel->setActiveSheetIndex(0)->setCellValue('S11', '=SUM(S4:S10)');

				$excel->setActiveSheetIndex(0)->setCellValue('S15', $tarifBalber1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S16', $tarifBalber2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S17', $tarifBalber3?:'0');				
				$excel->setActiveSheetIndex(0)->setCellValue('S18', $tarifBalber4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S19', $tarifBalber5?:'0');			
				$excel->setActiveSheetIndex(0)->setCellValue('S20', $tarifBalber6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S21', $tarifBalber7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S22', $tarifBstvalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S24', $tarifBoppt1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S25', $tarifBoppt2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S26', $tarifBoppt3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S27', $tarifBoppt4?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S28', $tarifBoppt5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S29', $tarifBoppt6?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S30', $tarifBoppt7?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S31', $tarifBkebersihanoppt?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S32', $tarifBmasaIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S33', $tarifBmasaIIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S34', $tarifBmasaIIIalber?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S35', '=SUM(S15:S34)');

				$excel->setActiveSheetIndex(0)->setCellValue('S39', $tarifBcargojasa?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S40', $tarifBcargolain?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S41', $tarifBcargolain2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S43', $tarifBcargostv?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S44', $tarifBstvlain?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S46', $tarifBcargooppt1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S47', $tarifBcargooppt2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S48', $tarifBcargooppt3?:'0');		
				$excel->setActiveSheetIndex(0)->setCellValue('S49', $tarifBcargooppt5?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S51', $tarifBcargokebersihan1?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S52', $tarifBcargokebersihan2?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S53', $tarifBcargokebersihan3?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S55', $tarifBcargomasa11?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S56', $tarifBcargomasa12?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S57', $tarifBcargomasa13?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S59', $tarifBcargomasa21?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S60', $tarifBcargomasa22?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S61', $tarifBcargomasa23?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S63', $tarifBcargomasa31?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S64', $tarifBcargomasa32?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S65', $tarifBcargomasa33?:'0');
				$excel->setActiveSheetIndex(0)->setCellValue('S66', '=SUM(S39:S65)');

				$excel->setActiveSheetIndex(0)->setCellValue('T4', '=(Q4*R4)');
				$excel->setActiveSheetIndex(0)->setCellValue('T5', '=(Q5*R5)');
				$excel->setActiveSheetIndex(0)->setCellValue('T6', '=(Q6*R6)');
				$excel->setActiveSheetIndex(0)->setCellValue('T7', '=(Q7*R7)');
				$excel->setActiveSheetIndex(0)->setCellValue('T8', '=(Q8*R8)');	
				$excel->setActiveSheetIndex(0)->setCellValue('T9', '=(Q9*R9)');	
				$excel->setActiveSheetIndex(0)->setCellValue('T10', '=(Q10*R10)');
				$excel->setActiveSheetIndex(0)->setCellValue('T11', '=(Q11*R11)');
		
				$excel->setActiveSheetIndex(0)->setCellValue('T15', '=(Q15*R15)');
				$excel->setActiveSheetIndex(0)->setCellValue('T16', '=(Q16*R16)');
				$excel->setActiveSheetIndex(0)->setCellValue('T17', '=(Q17*R17)');
				$excel->setActiveSheetIndex(0)->setCellValue('T18', '=(Q18*R18)');
				$excel->setActiveSheetIndex(0)->setCellValue('T19', '=(Q19*R19)');

				$excel->setActiveSheetIndex(0)->setCellValue('T15', '=(Q15*R15)');
				$excel->setActiveSheetIndex(0)->setCellValue('T16', '=(Q16*R16)');
				$excel->setActiveSheetIndex(0)->setCellValue('T17', '=(Q17*R17)');
				$excel->setActiveSheetIndex(0)->setCellValue('T18', '=(Q18*R18)');
				$excel->setActiveSheetIndex(0)->setCellValue('T19', '=(Q19*R19)');				
				$excel->setActiveSheetIndex(0)->setCellValue('T20', '=(Q20*R20)');
				$excel->setActiveSheetIndex(0)->setCellValue('T21', '=(Q21*R21)');
				$excel->setActiveSheetIndex(0)->setCellValue('T22', '=(Q22*R22)');		
				$excel->setActiveSheetIndex(0)->setCellValue('T24', '=(Q24*R24)');
				$excel->setActiveSheetIndex(0)->setCellValue('T25', '=(Q25*R25)');
				$excel->setActiveSheetIndex(0)->setCellValue('T26', '=(Q26*R26)');
				$excel->setActiveSheetIndex(0)->setCellValue('T27', '=(Q27*R27)');
				$excel->setActiveSheetIndex(0)->setCellValue('T28', '=(Q28*R28)');
				$excel->setActiveSheetIndex(0)->setCellValue('T29', '=(Q29*R29)');
				$excel->setActiveSheetIndex(0)->setCellValue('T30', '=(Q30*R30)');
				$excel->setActiveSheetIndex(0)->setCellValue('T31', '=(Q31*R31)');
				$excel->setActiveSheetIndex(0)->setCellValue('T32', '=(Q32*R32)');
				$excel->setActiveSheetIndex(0)->setCellValue('T33', '=(Q33*R33)');
				$excel->setActiveSheetIndex(0)->setCellValue('T34', '=(Q34*R34)');
				$excel->setActiveSheetIndex(0)->setCellValue('T35', '=(Q35*R35)');			
				$excel->setActiveSheetIndex(0)->setCellValue('T39', '=(Q39*R39)');
				$excel->setActiveSheetIndex(0)->setCellValue('T40', '=(Q40*R40)');
				$excel->setActiveSheetIndex(0)->setCellValue('T41', '=(Q41*R41)');
				$excel->setActiveSheetIndex(0)->setCellValue('T43', '=(Q43*R43)');
				$excel->setActiveSheetIndex(0)->setCellValue('T44', '=(Q44*R44)');				
				$excel->setActiveSheetIndex(0)->setCellValue('T46', '=(Q46*R46)');
				$excel->setActiveSheetIndex(0)->setCellValue('T47', '=(Q47*R47)');
				$excel->setActiveSheetIndex(0)->setCellValue('T48', '=(Q48*R48)');
				$excel->setActiveSheetIndex(0)->setCellValue('T49', '=(Q49*R49)');
				$excel->setActiveSheetIndex(0)->setCellValue('T51', '=(Q51*R51)');
				$excel->setActiveSheetIndex(0)->setCellValue('T52', '=(Q52*R52)');
				$excel->setActiveSheetIndex(0)->setCellValue('T53', '=(Q53*R53)');	
				$excel->setActiveSheetIndex(0)->setCellValue('T55', '=(Q55*R55)');
				$excel->setActiveSheetIndex(0)->setCellValue('T56', '=(Q56*R56)');
				$excel->setActiveSheetIndex(0)->setCellValue('T57', '=(Q57*R57)');
				$excel->setActiveSheetIndex(0)->setCellValue('T59', '=(Q59*R59)');
				$excel->setActiveSheetIndex(0)->setCellValue('T60', '=(Q60*R60)');
				$excel->setActiveSheetIndex(0)->setCellValue('T61', '=(Q61*R61)');
				$excel->setActiveSheetIndex(0)->setCellValue('T63', '=(Q63*R63)');
				$excel->setActiveSheetIndex(0)->setCellValue('T64', '=(Q64*R64)');
				$excel->setActiveSheetIndex(0)->setCellValue('T65', '=(Q65*R65)');				
				$excel->setActiveSheetIndex(0)->setCellValue('T66', '=(Q66*R66)');	

				$excel->setActiveSheetIndex(0)->setCellValue('U4', '=(Q4*S4)');
				$excel->setActiveSheetIndex(0)->setCellValue('U5', '=(Q5*S5)');
				$excel->setActiveSheetIndex(0)->setCellValue('U6', '=(Q6*S6)');
				$excel->setActiveSheetIndex(0)->setCellValue('U7', '=(Q7*S7)');
				$excel->setActiveSheetIndex(0)->setCellValue('U8', '=(Q8*S8)');	
				$excel->setActiveSheetIndex(0)->setCellValue('U9', '=(Q9*S9)');	
				$excel->setActiveSheetIndex(0)->setCellValue('U10', '=(Q10*S10)');
				$excel->setActiveSheetIndex(0)->setCellValue('U11', '=(Q11*S11)');			

				$excel->setActiveSheetIndex(0)->setCellValue('U15', '=(Q15*S15)');
				$excel->setActiveSheetIndex(0)->setCellValue('U16', '=(Q16*S16)');
				$excel->setActiveSheetIndex(0)->setCellValue('U17', '=(Q17*S17)');
				$excel->setActiveSheetIndex(0)->setCellValue('U18', '=(Q18*S18)');
				$excel->setActiveSheetIndex(0)->setCellValue('U19', '=(Q19*S19)');				
				$excel->setActiveSheetIndex(0)->setCellValue('U20', '=(Q20*S20)');
				$excel->setActiveSheetIndex(0)->setCellValue('U21', '=(Q21*S21)');
				$excel->setActiveSheetIndex(0)->setCellValue('U22', '=(Q22*S22)');		
				$excel->setActiveSheetIndex(0)->setCellValue('U24', '=(Q24*S24)');
				$excel->setActiveSheetIndex(0)->setCellValue('U25', '=(Q25*S25)');
				$excel->setActiveSheetIndex(0)->setCellValue('U26', '=(Q26*S26)');
				$excel->setActiveSheetIndex(0)->setCellValue('U27', '=(Q27*S27)');
				$excel->setActiveSheetIndex(0)->setCellValue('U28', '=(Q28*S28)');
				$excel->setActiveSheetIndex(0)->setCellValue('U29', '=(Q29*S29)');
				$excel->setActiveSheetIndex(0)->setCellValue('U30', '=(Q30*S30)');
				$excel->setActiveSheetIndex(0)->setCellValue('U31', '=(Q31*S31)');
				$excel->setActiveSheetIndex(0)->setCellValue('U32', '=(Q32*S32)');
				$excel->setActiveSheetIndex(0)->setCellValue('U33', '=(Q33*S33)');
				$excel->setActiveSheetIndex(0)->setCellValue('U34', '=(Q34*S34)');
				$excel->setActiveSheetIndex(0)->setCellValue('U35', '=(Q35*S35)');			
				$excel->setActiveSheetIndex(0)->setCellValue('U39', '=(Q39*S39)');
				$excel->setActiveSheetIndex(0)->setCellValue('U40', '=(Q40*S40)');
				$excel->setActiveSheetIndex(0)->setCellValue('U41', '=(Q41*S41)');
				$excel->setActiveSheetIndex(0)->setCellValue('U43', '=(Q43*S43)');
				$excel->setActiveSheetIndex(0)->setCellValue('U44', '=(Q44*S44)');				
				$excel->setActiveSheetIndex(0)->setCellValue('U46', '=(Q46*S46)');
				$excel->setActiveSheetIndex(0)->setCellValue('U47', '=(Q47*S47)');
				$excel->setActiveSheetIndex(0)->setCellValue('U48', '=(Q48*S48)');
				$excel->setActiveSheetIndex(0)->setCellValue('U49', '=(Q49*S49)');
				$excel->setActiveSheetIndex(0)->setCellValue('U51', '=(Q51*S51)');
				$excel->setActiveSheetIndex(0)->setCellValue('U52', '=(Q52*S52)');
				$excel->setActiveSheetIndex(0)->setCellValue('U53', '=(Q53*S53)');
				$excel->setActiveSheetIndex(0)->setCellValue('U55', '=(Q55*S55)');
				$excel->setActiveSheetIndex(0)->setCellValue('U56', '=(Q56*S56)');
				$excel->setActiveSheetIndex(0)->setCellValue('U57', '=(Q57*S57)');
				$excel->setActiveSheetIndex(0)->setCellValue('U59', '=(Q59*S59)');
				$excel->setActiveSheetIndex(0)->setCellValue('U60', '=(Q60*S60)');
				$excel->setActiveSheetIndex(0)->setCellValue('U61', '=(Q61*S61)');
				$excel->setActiveSheetIndex(0)->setCellValue('U63', '=(Q63*S63)');
				$excel->setActiveSheetIndex(0)->setCellValue('U64', '=(Q64*S64)');
				$excel->setActiveSheetIndex(0)->setCellValue('U65', '=(Q65*S65)');				
				$excel->setActiveSheetIndex(0)->setCellValue('U66', '=(Q66*S66)');
				
				$excel->setActiveSheetIndex(0)->setCellValue('V4', '=(T4+U4)');
				$excel->setActiveSheetIndex(0)->setCellValue('V5', '=(T5+U5)');
				$excel->setActiveSheetIndex(0)->setCellValue('V6', '=(T6+U6)');
				$excel->setActiveSheetIndex(0)->setCellValue('V7', '=(T7+U7)');
				$excel->setActiveSheetIndex(0)->setCellValue('V8', '=(T8+U8)');	
				$excel->setActiveSheetIndex(0)->setCellValue('V9', '=(T9+U9)');	
				$excel->setActiveSheetIndex(0)->setCellValue('V10', '=(T10+U10)');		
				$excel->setActiveSheetIndex(0)->setCellValue('V11', '=(T11+U11)');	

				$excel->setActiveSheetIndex(0)->setCellValue('V15', '=(T15+U15)');
				$excel->setActiveSheetIndex(0)->setCellValue('V16', '=(T16+U16)');
				$excel->setActiveSheetIndex(0)->setCellValue('V17', '=(T17+U17)');
				$excel->setActiveSheetIndex(0)->setCellValue('V18', '=(T18+U18)');
				$excel->setActiveSheetIndex(0)->setCellValue('V19', '=(T19+U19)');
				$excel->setActiveSheetIndex(0)->setCellValue('V15', '=(T15+U15)');
				$excel->setActiveSheetIndex(0)->setCellValue('V16', '=(T16+U16)');
				$excel->setActiveSheetIndex(0)->setCellValue('V17', '=(T17+U17)');
				$excel->setActiveSheetIndex(0)->setCellValue('V18', '=(T18+U18)');
				$excel->setActiveSheetIndex(0)->setCellValue('V19', '=(T19+U19)');				
				$excel->setActiveSheetIndex(0)->setCellValue('V20', '=(T20+U20)');
				$excel->setActiveSheetIndex(0)->setCellValue('V21', '=(T21+U21)');
				$excel->setActiveSheetIndex(0)->setCellValue('V22', '=(T22+U22)');		
				$excel->setActiveSheetIndex(0)->setCellValue('V24', '=(T24+U24)');
				$excel->setActiveSheetIndex(0)->setCellValue('V25', '=(T25+U25)');
				$excel->setActiveSheetIndex(0)->setCellValue('V26', '=(T26+U26)');
				$excel->setActiveSheetIndex(0)->setCellValue('V27', '=(T27+U27)');
				$excel->setActiveSheetIndex(0)->setCellValue('V28', '=(T28+U28)');
				$excel->setActiveSheetIndex(0)->setCellValue('V29', '=(T29+U29)');
				$excel->setActiveSheetIndex(0)->setCellValue('V30', '=(T30+U30)');
				$excel->setActiveSheetIndex(0)->setCellValue('V31', '=(T31+U31)');
				$excel->setActiveSheetIndex(0)->setCellValue('V32', '=(T32+U32)');
				$excel->setActiveSheetIndex(0)->setCellValue('V33', '=(T33+U33)');
				$excel->setActiveSheetIndex(0)->setCellValue('V34', '=(T34+U34)');
				$excel->setActiveSheetIndex(0)->setCellValue('V35', '=(T35+U35)');			
				$excel->setActiveSheetIndex(0)->setCellValue('V39', '=(T39+U39)');
				$excel->setActiveSheetIndex(0)->setCellValue('V40', '=(T40+U40)');
				$excel->setActiveSheetIndex(0)->setCellValue('V41', '=(T41+U41)');
				$excel->setActiveSheetIndex(0)->setCellValue('V43', '=(T43+U43)');
				$excel->setActiveSheetIndex(0)->setCellValue('V44', '=(T44+U44)');				
				$excel->setActiveSheetIndex(0)->setCellValue('V46', '=(T46+U46)');
				$excel->setActiveSheetIndex(0)->setCellValue('V47', '=(T47+U47)');
				$excel->setActiveSheetIndex(0)->setCellValue('V48', '=(T48+U48)');
				$excel->setActiveSheetIndex(0)->setCellValue('V49', '=(T49+U49)');
				$excel->setActiveSheetIndex(0)->setCellValue('V51', '=(T51+U51)');
				$excel->setActiveSheetIndex(0)->setCellValue('V52', '=(T52+U52)');
				$excel->setActiveSheetIndex(0)->setCellValue('V53', '=(T53+U53)');
				$excel->setActiveSheetIndex(0)->setCellValue('V55', '=(T55+U55)');
				$excel->setActiveSheetIndex(0)->setCellValue('V56', '=(T56+U56)');
				$excel->setActiveSheetIndex(0)->setCellValue('V57', '=(T57+U57)');
				$excel->setActiveSheetIndex(0)->setCellValue('V59', '=(T59+U59)');
				$excel->setActiveSheetIndex(0)->setCellValue('V60', '=(T60+U60)');
				$excel->setActiveSheetIndex(0)->setCellValue('V61', '=(T61+U61)');
				$excel->setActiveSheetIndex(0)->setCellValue('V63', '=(T63+U63)');
				$excel->setActiveSheetIndex(0)->setCellValue('V64', '=(T64+U64)');
				$excel->setActiveSheetIndex(0)->setCellValue('V65', '=(T65+U65)');				
				$excel->setActiveSheetIndex(0)->setCellValue('V66', '=(T66+U66)');

		}
			// // Set width kolom
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(10); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(5); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(25); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(15); // Set width kolom D
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
			$excel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
			$excel->getActiveSheet()->getColumnDimension('T')->setWidth(30);
			$excel->getActiveSheet()->getColumnDimension('U')->setWidth(30);
			$excel->getActiveSheet()->getColumnDimension('V')->setWidth(30);

			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Laporan_Pendapatan");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Pelaporan Produksi dan Pendapatan per pusat layanan_INTR_'.$id.'_'.$end.'.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');
		


	}
}