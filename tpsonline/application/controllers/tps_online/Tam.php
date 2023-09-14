<?php
class Tam extends CI_Controller{
	private $local_db;
	
	
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
			$this->load->model('tps_online/kunjungan_kapal_model');
			// $this->load->library('PHPExcel/PHPExcel/IOFactory');
		}
	}
	
	private function get_db(){
		if(!$this->local_db){
			$this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
			$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
			$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		}
		
		return $this->local_db;
	}

	public function index()
	{
		$this->load->view('backend/pages/tam/view_tam');
	}

	public function load_data_tam($token = null)
	{
		if($this->auth->token == $token){
			$db = $this->get_db();
			
			$this->load->model('tps_online/model_tam');
		
			$model = $this->model_tam->get_data_tam(); // $_POST['model_name'], $_POST['perusahaan']
			
			
			header('Content-Type: application/json');
			echo json_encode($model);
		}
		else{
			var_dump($_REQUEST);
			echo json_encode('INVALID TOKEN');	
		}
	}

	public function export_tam_xls()
	{
		
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Laporan TAM')
								   // ->setLastModifiedBy('My Notes Code')
								   ->setTitle("Laporan Data TAM")
								   ->setSubject("TAM")
								   ->setDescription("Laporan Semua Data ")
								   ->setKeywords("Data TAM");

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
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
				),
				'borders' => array(
					'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
					'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
					'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
					'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
				)
			);

			// $excel->setActiveSheetIndex(0)->setCellValue('A1', "LAPORAN DATA TAM"); // Set kolom A1 dengan tulisan "DATA SISWA"
			// $excel->getActiveSheet()->mergeCells('A1:I1'); // Set Merge Cell pada kolom A1 sampai E1
			// $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
			// $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
			// $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1

			// Buat header tabel nya pada baris ke 3
			$excel->setActiveSheetIndex(0)->setCellValue('A1', "Vin"); // Set kolom A3 dengan tulisan "NO"
			$excel->setActiveSheetIndex(0)->setCellValue('B1', "BlNumber"); // Set kolom B3 dengan tulisan "NIS"
			$excel->setActiveSheetIndex(0)->setCellValue('C1', "LogiscsticCompany"); // Set kolom C3 dengan tulisan "NAMA"
			$excel->setActiveSheetIndex(0)->setCellValue('D1', "AnnouncedDate"); // Set kolom D3 dengan tulisan "JENIS KELAMIN"
			$excel->setActiveSheetIndex(0)->setCellValue('E1', "OnTerminalDate"); // Set kolom E3 dengan tulisan "ALAMAT"
			$excel->setActiveSheetIndex(0)->setCellValue('F1', "LoadedDate");
			$excel->setActiveSheetIndex(0)->setCellValue('G1', "LeftDate"); // Set kolom B3 dengan tulisan "NIS"
			$excel->setActiveSheetIndex(0)->setCellValue('H1', "AtaDate"); // Set kolom C3 dengan tulisan "NAMA"
			$excel->setActiveSheetIndex(0)->setCellValue('I1', "UpdateDate"); // Set kolom D3 dengan tulisan "JENIS KELAMIN"
			
			// Apply style header yang telah kita buat tadi ke masing-masing kolom header
			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('B1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('C1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('D1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('E1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('F1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('G1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('H1')->applyFromArray($style_col);
			$excel->getActiveSheet()->getStyle('I1')->applyFromArray($style_col);

			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('tps_online/model_tam');
				
			$model = $this->model_tam->get_data_tam();
			// echo "<pre>";
			// print_r($model);die();

			// $no = 1; // Untuk penomoran tabel, di awal set dengan 1
			$numrow = 2; // Set baris pertama untuk isi tabel adalah baris ke 4
			
			foreach($model['data'] as $data){ // Lakukan looping pada variabel siswa

				$excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $data['VIN']);
				$excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $data['BL_NUMBER']);
				$excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $data['LOGISCTICCOMPANY']);
				$excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $data['ANNOUNCEDDATE']);
				$excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $data['ONTERMINALDATE']);
				$excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $data['LOADEDDATE']);
				$excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $data['LEFTDATE']);
				$excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $data['ATADATE']);
				$excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, $data['UPDATEDATE']);
				
				// print_r($data);
				// print_r($numrow);die();
				// Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
				$excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_row);
				$excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
				$excel->getActiveSheet()->getStyle('C'.$numrow)->applyFromArray($style_row);
				$excel->getActiveSheet()->getStyle('D'.$numrow)->applyFromArray($style_row);
				$excel->getActiveSheet()->getStyle('E'.$numrow)->applyFromArray($style_row);
				$excel->getActiveSheet()->getStyle('F'.$numrow)->applyFromArray($style_row);
				$excel->getActiveSheet()->getStyle('G'.$numrow)->applyFromArray($style_row);
				$excel->getActiveSheet()->getStyle('H'.$numrow)->applyFromArray($style_row);
				$excel->getActiveSheet()->getStyle('I'.$numrow)->applyFromArray($style_row);
				
				// $no++; // Tambah 1 setiap kali looping
				$numrow++; // Tambah 1 setiap kali looping
			}

			// Set width kolom
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(30); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(30); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(30); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(30); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('E')->setWidth(30); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('F')->setWidth(30); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('G')->setWidth(30); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('H')->setWidth(30); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
			
			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("TAM_HANDLINGUNIT");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="TAM_HANDLINGUNIT.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->save('php://output');
		


	}
}
