<?php
/** Laporan Produksi dan Pendapatan per Pusat Layanan
  *	Modul untuk mengunduh laporan produksi dan pendapatan per pusat layanan berdasarkan tahun dan terminal
  *
  */

class template_excel_truck extends CI_Controller{
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

			$excel->setActiveSheetIndex(0)->setCellValue('A1', "vin");
			$excel->setActiveSheetIndex(0)->setCellValue('B1', "fuel");
			$excel->setActiveSheetIndex(0)->setCellValue('C1', "model");
			$excel->setActiveSheetIndex(0)->setCellValue('D1', "final location");
			if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){
				$excel->setActiveSheetIndex(0)->setCellValue('E1', "shipping line");
			} else if ($this->userauth->getLoginData()->intapps_type !== 'ADMIN'){
				$excel->setActiveSheetIndex(0)->setCellValue('E1', " ");
			}
			
			$excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$excel->getActiveSheet()->getStyle('C1:E1')->applyFromArray($styleArray);
			$excel->getActiveSheet()->getStyle('B1:C1')->applyFromArray($style);	
			$excel->getActiveSheet()->getStyle('G1:H1')->applyFromArray($style);
		
			$totalCount = 10000;

		
			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('domestik/model_template_Excel');
	
			$model = $this->model_template_Excel->getModel();

			$rows=1;
		
			for($i=0; $i< count($model); $i++){			
				$excel->setActiveSheetIndex(0)->setCellValue('AA'.$rows, $model[$i]["NAME"]);
			$rows++;
		
			}
			$excel->getActiveSheet()->getStyle('AA1:AA'.count($model))->applyFromArray($styleWhite);	
		

			$excel->addNamedRange( 
				new PHPExcel_NamedRange(
				'model', 
				$excel->setActiveSheetIndex(0), 
				'AA1:AA'.count($model)
				) 
				);
		
			
			for($i=2; $i< $totalCount; $i++){
				$configs = array($model[$i]["NAME"]);			
				$objValidation = $excel->getActiveSheet()->getCell('C'.$i)->getDataValidation();
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
				$objValidation = $excel->getActiveSheet()->getCell('D'.$i)->getDataValidation();
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
			$excel->getActiveSheet()->getStyle('AC1:AC'.count($getConsignee))->applyFromArray($styleWhite);				
			$excel->getActiveSheet()->getStyle('AD1:AD'.count($getConsignee))->applyFromArray($styleWhite);
				
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
				$objValidation = $excel->getActiveSheet()->getCell('E'.$i)->getDataValidation();
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
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(30); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(20); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(43); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(25); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('E')->setWidth(40); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('F')->setWidth(20); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('G')->setWidth(20); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('H')->setWidth(20); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('I')->setWidth(15); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('J')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('K')->setWidth(15); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('L')->setWidth(15); // Set width kolom E


			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Template_VIN");
			$excel->setActiveSheetIndex(0);

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
			$excel->getProperties()->setCreator('Contoh_Template_VIN')							
								   ->setTitle("Contoh_Template_VIN")
								   ->setSubject("Contoh_Template_VIN")
								   ->setDescription("Contoh_Template_VIN")
								   ->setKeywords("Contoh_Template_VIN");
		
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
		
	
			$excel->setActiveSheetIndex(0)->setCellValue('A2', "Contoh: VINCONTOH0001");		
			$excel->setActiveSheetIndex(0)->setCellValue('B2', "SOLAR");
			$excel->setActiveSheetIndex(0)->setCellValue('C2', "APV GL");
			$excel->setActiveSheetIndex(0)->setCellValue('D2', "BEKASI");
			$excel->setActiveSheetIndex(0)->setCellValue('E2', "PT BUMI LINTAS TAMA");
			$excel->getActiveSheet()->getStyle('A2:E2')->applyFromArray($styleBlue);

			// Buat header tabel nya pada baris ke 3
			$excel->setActiveSheetIndex(0)->setCellValue('A1', "vin");
			$excel->setActiveSheetIndex(0)->setCellValue('B1', "fuel");
			$excel->setActiveSheetIndex(0)->setCellValue('C1', "model");
			$excel->setActiveSheetIndex(0)->setCellValue('D1', "final location");
			if ($this->userauth->getLoginData()->intapps_type === 'ADMIN'){
				$excel->setActiveSheetIndex(0)->setCellValue('E1', "shipping line");
			} else if ($this->userauth->getLoginData()->intapps_type !== 'ADMIN'){
				$excel->setActiveSheetIndex(0)->setCellValue('E1', " ");
			}
			
			$excel->getActiveSheet()->getStyle('A1:H1')->getFont()->setBold(true);
			$excel->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
			$excel->getActiveSheet()->getStyle('C1:E1')->applyFromArray($styleArray);
			$excel->getActiveSheet()->getStyle('B1:C1')->applyFromArray($style);	
			$excel->getActiveSheet()->getStyle('G1:H1')->applyFromArray($style);
	
	
			$totalCount = 10000;
			// for($i=2; $i< $totalCount; $i++){
			// 	$configs = "INBOUND, OUTBOUND";
			// 	$objValidation = $excel->getActiveSheet()->getCell('B'.$i)->getDataValidation();
			// 	$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST );
			// 	$objValidation->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION );
			// 	$objValidation->setAllowBlank(false);
			// 	$objValidation->setShowInputMessage(true);
			// 	$objValidation->setShowErrorMessage(true);
			// 	$objValidation->setShowDropDown(true);
			// 	$objValidation->setOperator('INBOUND');
			// 	$objValidation->setErrorTitle('Input error');
			// 	$objValidation->setError('Value is not in list.');
			// 	// $objValidation->setPromptTitle('Pick from list');
			// 	// $objValidation->setPrompt('Please choose');
			// 	$objValidation->setFormula1('"'.$configs.'"');

		
	
			
			// }
		
		
			// Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
			$this->load->model('domestik/model_template_Excel');
	
			$model = $this->model_template_Excel->getModel();

			$rows=1;
		
			for($i=0; $i< count($model); $i++){			
				$excel->setActiveSheetIndex(0)->setCellValue('AA'.$rows, $model[$i]["NAME"]);
			$rows++;
		
			}
			$excel->getActiveSheet()->getStyle('AA1:AA'.count($model))->applyFromArray($styleWhite);	
		

			$excel->addNamedRange( 
				new PHPExcel_NamedRange(
				'model', 
				$excel->setActiveSheetIndex(0), 
				'AA1:AA'.count($model)
				) 
				);
		
			
			for($i=2; $i< $totalCount; $i++){
				$configs = array($model[$i]["NAME"]);			
				$objValidation = $excel->getActiveSheet()->getCell('C'.$i)->getDataValidation();
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
				$objValidation = $excel->getActiveSheet()->getCell('D'.$i)->getDataValidation();
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
			$excel->getActiveSheet()->getStyle('AC1:AC'.count($getConsignee))->applyFromArray($styleWhite);				
			$excel->getActiveSheet()->getStyle('AD1:AD'.count($getConsignee))->applyFromArray($styleWhite);
				
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
				$objValidation = $excel->getActiveSheet()->getCell('E'.$i)->getDataValidation();
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
			$excel->getActiveSheet()->getColumnDimension('A')->setWidth(30); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('B')->setWidth(20); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('C')->setWidth(43); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('D')->setWidth(25); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('E')->setWidth(40); // Set width kolom E
			$excel->getActiveSheet()->getColumnDimension('F')->setWidth(20); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('G')->setWidth(20); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('H')->setWidth(20); // Set width kolom A
			$excel->getActiveSheet()->getColumnDimension('I')->setWidth(15); // Set width kolom B
			$excel->getActiveSheet()->getColumnDimension('J')->setWidth(15); // Set width kolom C
			$excel->getActiveSheet()->getColumnDimension('K')->setWidth(15); // Set width kolom D
			$excel->getActiveSheet()->getColumnDimension('L')->setWidth(15); // Set width kolom E


			// Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
			$excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

			// Set orientasi kertas jadi LANDSCAPE
			$excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Set judul file excel nya
			$excel->getActiveSheet(0)->setTitle("Contoh_Template_VIN");
			$excel->setActiveSheetIndex(0);

			// Proses file excel
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="Contoh_format_announcement_truck_outbound.xls"'); // Set nama file excel nya
			header('Cache-Control: max-age=0');

			$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
			$write->setPreCalculateFormulas(true);
			$write->save('php://output');
		


	}

}