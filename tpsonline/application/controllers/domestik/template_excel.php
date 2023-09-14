<?php
/** Laporan Produksi dan Pendapatan per Pusat Layanan
  *	Modul untuk mengunduh laporan produksi dan pendapatan per pusat layanan berdasarkan tahun dan terminal
  *
  */

class template_excel extends CI_Controller{
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
            $this->local_db = $this->load->database('integrasi_cardom_dev', TRUE);
		
        }

        return $this->local_db;
    }	

	public function excel_download()
	{
		
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Template_VIN')							
								   ->setTitle("Template_VIN")
								   ->setSubject("Template_VIN")
								   ->setDescription("Template_VIN")
								   ->setKeywords("Template_VIN");
		
								   
			// $excel->createSheet();
			// $excel->createSheet();

			// // Set judul file excel nya
			$excel->getActiveSheet(1)->setTitle("Template_VIN");
			// $excel->setActiveSheetIndex(1);

			// $excel->getActiveSheet(2)->setTitle("Contoh_Template_VIN");
			// $excel->setActiveSheetIndex(2);

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

			$styleArray = array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'FF0000'),
					'size'  => 11,
					'name'  => 'Calibri'
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			
			);

			$styleWhite = array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'FFFFFF'),
					'size'  => 11,
					'name'  => 'Calibri'
				)
			
			);
			$styleBlue = array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '0000FF'),
					'size'  => 11,
					'name'  => 'Calibri'
				)
			
			);
	

			$border_style= array('borders' => array('right' => array('style' => 
			PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => '766f6e'),)));
			
			// Buat header tabel nya pada baris ke 3
			$excel->setActiveSheetIndex(0)->setCellValue('A1', "vin");
			$excel->setActiveSheetIndex(0)->setCellValue('B1', "direction");
			$excel->setActiveSheetIndex(0)->setCellValue('C1', "fuel");
			$excel->setActiveSheetIndex(0)->setCellValue('D1', "model");
			$excel->setActiveSheetIndex(0)->setCellValue('E1', "final location");
			if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){
				$excel->setActiveSheetIndex(0)->setCellValue('F1', "shipping line");
			} else if ($this->userauth->getLoginData()->intapps_type !== 'ADMIN'){
				$excel->setActiveSheetIndex(0)->setCellValue('F1', " ");
			} 

			$excel->getActiveSheet(0)->getStyle('A1:H1')->getFont()->setBold(true);
			$excel->getActiveSheet(0)->getStyle('A1:B1')->applyFromArray($styleArray);
			$excel->getActiveSheet(0)->getStyle('D1:F1')->applyFromArray($styleArray);
			$excel->getActiveSheet(0)->getStyle('C1')->applyFromArray($style);	
			$excel->getActiveSheet(0)->getStyle('G1:H1')->applyFromArray($style);
	
	
			$totalCount = 10000;
			for($i=2; $i< $totalCount; $i++){
				$configs = "INBOUND(DISCHARGE), OUTBOUND(LOADING)";
				$objValidation = $excel->getActiveSheet(0)->getCell('B'.$i)->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setOperator('INBOUND');
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				// $objValidation->setPromptTitle('Pick from list');
				// $objValidation->setPrompt('Please choose');
				$objValidation->setFormula1('"'.$configs.'"');

		
	
			
			}
		
		
			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('domestik/model_template_Excel');
	
			$model = $this->model_template_Excel->getModel();

			$rows=1;
		
			for($i=0; $i< count($model); $i++){			
				$excel->setActiveSheetIndex(0)->setCellValue('AA'.$rows, $model[$i]["NAME"]);
			$rows++;
		
			}
			$excel->getActiveSheet(0)->getStyle('AA1:AA'.count($model))->applyFromArray($styleWhite);	
		

			$excel->addNamedRange( 
				new PHPExcel_NamedRange(
				'model', 
				$excel->setActiveSheetIndex(0), 
				'AA1:AA'.count($model)
				) 
				);
		
			
			for($i=2; $i< $totalCount; $i++){				
				$objValidation = $excel->getActiveSheet(0)->getCell('D'.$i)->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				// $objValidation->setPromptTitle('Pick from list');
				// $objValidation->setPrompt('Please choose');
				$objValidation->setFormula1("=model");
			}
		//}
			$getFinal = $this->model_template_Excel->getFinal();

			$rows=1;
		
			for($i=0; $i< count($getFinal); $i++){			
				$excel->setActiveSheetIndex(0)->setCellValue('AB'.$rows, $getFinal[$i]["PORT_NAME"]);
			$rows++;
		
			}
			$excel->getActiveSheet()->getStyle('AB1:AB'.count($getFinal))->applyFromArray($styleWhite);				

			$excel->addNamedRange( 
				new PHPExcel_NamedRange(
				'final', 
				$excel->setActiveSheetIndex(0), 
				'AB1:AB'.count($getFinal)
				) 
				);

			for($i=2; $i< $totalCount; $i++){
				$objValidation = $excel->getActiveSheet(0)->getCell('E'.$i)->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				// $objValidation->setPromptTitle('Pick from list');
				// $objValidation->setPrompt('Please choose');
				$objValidation->setFormula1("=final");
			}
			
			
			$getConsignee = $this->model_template_Excel->getConsignee();
	
			$rows=1;
		
			for($i=0; $i< count($getConsignee); $i++){			
				$excel->setActiveSheetIndex(0)->setCellValue('AC'.$rows, $getConsignee[$i]["ID"]);
				$excel->setActiveSheetIndex(0)->setCellValue('AD'.$rows, $getConsignee[$i]["NAME"]);
			$rows++;
		
			}
			$excel->getActiveSheet(0)->getStyle('AC1:AC'.count($getConsignee))->applyFromArray($styleWhite);				
			$excel->getActiveSheet(0)->getStyle('AD1:AD'.count($getConsignee))->applyFromArray($styleWhite);
				
			$excel->addNamedRange( 
				new PHPExcel_NamedRange(
				'consignee', 
				$excel->setActiveSheetIndex(0), 
				'AC1:AC'.count($getConsignee)
				) 
				);

				$excel->addNamedRange( 
					new PHPExcel_NamedRange(
					'shipping', 
					$excel->setActiveSheetIndex(0), 
					'AD1:AD'.count($getConsignee)
					) 
					);

			if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){
			for($i=2; $i< $totalCount; $i++){
				$objValidation = $excel->getActiveSheet(0)->getCell('F'.$i)->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				// $objValidation->setPromptTitle('Pick from list');
				// $objValidation->setPrompt('Please choose');
				$objValidation->setFormula1("=shipping");
			}
			}
			// // Set width kolom
			$excel->getActiveSheet(0)->getColumnDimension('A')->setWidth(25); // Set width kolom A
			$excel->getActiveSheet(0)->getColumnDimension('B')->setWidth(25); // Set width kolom B
			$excel->getActiveSheet(0)->getColumnDimension('C')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet(0)->getColumnDimension('D')->setWidth(43); // Set width kolom D
			$excel->getActiveSheet(0)->getColumnDimension('E')->setWidth(25); // Set width kolom E
			$excel->getActiveSheet(0)->getColumnDimension('F')->setWidth(40); // Set width kolom B		


			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet(0)->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet(0)->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);


			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="format_announcement_vin.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');
		


	}

	public function contoh_template_vin()
	{
		
			// Load plugin PHPExcel nya
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			
			// Panggil class PHPExcel nya
			$excel = new PHPExcel();

			// Settingan awal fil excel
			$excel->getProperties()->setCreator('Template_VIN')							
								   ->setTitle("Template_VIN")
								   ->setSubject("Template_VIN")
								   ->setDescription("Template_VIN")
								   ->setKeywords("Template_VIN");
		

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

			$styleArray = array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'FF0000'),
					'size'  => 11,
					'name'  => 'Calibri'
				),
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			
			);

			$styleWhite = array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => 'FFFFFF'),
					'size'  => 11,
					'name'  => 'Calibri'
				)
			
			);
			$styleBlue = array(
				'font'  => array(
					'bold'  => true,
					'color' => array('rgb' => '0000FF'),
					'size'  => 11,
					'name'  => 'Calibri'
				)
			
			);
	

			$border_style= array('borders' => array('right' => array('style' => 
			PHPExcel_Style_Border::BORDER_THICK,'color' => array('argb' => '766f6e'),)));

			$excel->setActiveSheetIndex()->setCellValue('A1', "vin");
			$excel->setActiveSheetIndex()->setCellValue('B1', "direction");
			$excel->setActiveSheetIndex()->setCellValue('C1', "fuel");
			$excel->setActiveSheetIndex()->setCellValue('D1', "model");
			$excel->setActiveSheetIndex()->setCellValue('E1', "final location");
			$excel->setActiveSheetIndex()->setCellValue('A2', "Contoh: VINCONTOH0001");
			$excel->setActiveSheetIndex()->setCellValue('B2', "INBOUND(DISCHARGE)");
			$excel->setActiveSheetIndex()->setCellValue('C2', "SOLAR");
			$excel->setActiveSheetIndex()->setCellValue('D2', "APV GL");
			$excel->setActiveSheetIndex()->setCellValue('E2', "BEKASI");
			$excel->setActiveSheetIndex()->setCellValue('F2', "PT BUMI LINTAS TAMA");

			if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){
				$excel->setActiveSheetIndex(0)->setCellValue('F1', "shipping line");
			} else if ($this->userauth->getLoginData()->intapps_type !== 'ADMIN'){
				$excel->setActiveSheetIndex(0)->setCellValue('F1', " ");
			} 
			$excel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleBlue);

			$excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A1:B1')->applyFromArray($styleArray);
			$excel->getActiveSheet()->getStyle('D1:F1')->applyFromArray($styleArray);
			$excel->getActiveSheet()->getStyle('C1')->applyFromArray($style);	
			$excel->getActiveSheet()->getStyle('G1:H1')->applyFromArray($style);

		
			
			// Buat header tabel nya pada baris ke 3
			
	
	
			$totalCount = 10000;
			for($i=2; $i< $totalCount; $i++){
				$configs = "INBOUND(DISCHARGE), OUTBOUND(LOADING)";
				$objValidation = $excel->getActiveSheet(0)->getCell('B'.$i)->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setOperator('INBOUND');
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				// $objValidation->setPromptTitle('Pick from list');
				// $objValidation->setPrompt('Please choose');
				$objValidation->setFormula1('"'.$configs.'"');

		
	
			
			}
		
		
			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('domestik/model_template_Excel');
	
			$model = $this->model_template_Excel->getModel();

			$rows=1;
		
			for($i=0; $i< count($model); $i++){			
				$excel->setActiveSheetIndex(0)->setCellValue('AA'.$rows, $model[$i]["NAME"]);
			$rows++;
		
			}
			$excel->getActiveSheet(0)->getStyle('AA1:AA'.count($model))->applyFromArray($styleWhite);	
		

			$excel->addNamedRange( 
				new PHPExcel_NamedRange(
				'model', 
				$excel->setActiveSheetIndex(0), 
				'AA1:AA'.count($model)
				) 
				);
		
			
			for($i=2; $i< $totalCount; $i++){
				$configs = array($model[$i]["NAME"]);			
				$objValidation = $excel->getActiveSheet(0)->getCell('D'.$i)->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				// $objValidation->setPromptTitle('Pick from list');
				// $objValidation->setPrompt('Please choose');
				$objValidation->setFormula1("=model");
			}
		//}
			$getFinal = $this->model_template_Excel->getFinal();

			$rows=1;
		
			for($i=0; $i< count($getFinal); $i++){			
				$excel->setActiveSheetIndex(0)->setCellValue('AB'.$rows, $getFinal[$i]["PORT_NAME"]);
			$rows++;
		
			}
			$excel->getActiveSheet()->getStyle('AB1:AB'.count($getFinal))->applyFromArray($styleWhite);				

			$excel->addNamedRange( 
				new PHPExcel_NamedRange(
				'final', 
				$excel->setActiveSheetIndex(0), 
				'AB1:AB'.count($getFinal)
				) 
				);

			for($i=2; $i< $totalCount; $i++){
				$configs = $model[i]["PORT_NAME"];
				$objValidation = $excel->getActiveSheet(0)->getCell('E'.$i)->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				// $objValidation->setPromptTitle('Pick from list');
				// $objValidation->setPrompt('Please choose');
				$objValidation->setFormula1("=final");
			}
			
			
			$getConsignee = $this->model_template_Excel->getConsignee();
	
			$rows=1;
		
			for($i=0; $i< count($getConsignee); $i++){			
				$excel->setActiveSheetIndex(0)->setCellValue('AC'.$rows, $getConsignee[$i]["ID"]);
				$excel->setActiveSheetIndex(0)->setCellValue('AD'.$rows, $getConsignee[$i]["NAME"]);
			$rows++;
		
			}
			$excel->getActiveSheet(0)->getStyle('AC1:AC'.count($getConsignee))->applyFromArray($styleWhite);				
			$excel->getActiveSheet(0)->getStyle('AD1:AD'.count($getConsignee))->applyFromArray($styleWhite);
				
			$excel->addNamedRange( 
				new PHPExcel_NamedRange(
				'consignee', 
				$excel->setActiveSheetIndex(0), 
				'AC1:AC'.count($getConsignee)
				) 
				);

				$excel->addNamedRange( 
					new PHPExcel_NamedRange(
					'shipping', 
					$excel->setActiveSheetIndex(0), 
					'AD1:AD'.count($getConsignee)
					) 
					);

			if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){
			for($i=2; $i< $totalCount; $i++){
			
				$configs = $getConsignee[i]["NAME"];
				$objValidation = $excel->getActiveSheet(0)->getCell('F'.$i)->getDataValidation();
				$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
				$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
				$objValidation->setAllowBlank(false);
				$objValidation->setShowInputMessage(true);
				$objValidation->setShowErrorMessage(true);
				$objValidation->setShowDropDown(true);
				$objValidation->setErrorTitle('Input error');
				$objValidation->setError('Value is not in list.');
				// $objValidation->setPromptTitle('Pick from list');
				// $objValidation->setPrompt('Please choose');
				$objValidation->setFormula1("=shipping");
			}
			}
			// // Set width kolom
			$excel->getActiveSheet(0)->getColumnDimension('A')->setWidth(25); // Set width kolom A
			$excel->getActiveSheet(0)->getColumnDimension('B')->setWidth(25); // Set width kolom B
			$excel->getActiveSheet(0)->getColumnDimension('C')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet(0)->getColumnDimension('D')->setWidth(43); // Set width kolom D
			$excel->getActiveSheet(0)->getColumnDimension('E')->setWidth(25); // Set width kolom E
			$excel->getActiveSheet(0)->getColumnDimension('F')->setWidth(40); // Set width kolom B		


			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet(0)->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet(0)->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			
			$excel->getActiveSheet()->setTitle("Contoh_Template_VIN");
	

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Contoh_format_announcement_vin.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');
		


	}

}