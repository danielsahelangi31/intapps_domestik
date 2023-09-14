<?php 
$timer['rendering_start'] = microtime(true);
$excel = library('PHPExcel/phpexcel');

$charsets = array();
for($i = 0; $i <= 128; $i++){	
	if($i < 26){
		$charsets[$i + 1] = chr(65 + ($i % 26));
	}else{
		$charsets[$i + 1] = $charsets[floor($i / 26)].chr(65 + ($i % 26));
	}
}




$excel->setActiveSheetIndex(0);
$worksheet = $excel->getActiveSheet();

// Column Sizing
$column_sizes = array(
	'A' => 30,
	'B' => 150,
	'C' => 75,
	'D' => 75,
	'E' => 110,
	'F' => 75,
	'G' => 75,
	'H' => 75,
	'I' => 75,
	'J' => 100,
	'K' => 60,
	'L' => 60,
	'M' => 85,
	'N' => 85,
	'O' => 85,
);

foreach($column_sizes as $col => $width){
	$worksheet->getColumnDimension($col)->setWidth($width / 5.5);
}

$worksheet->SetCellValue('A2', 'REKAPITULASI DATA CODECO COARRI');
$worksheet->mergeCells('A2:I2');
$worksheet->SetCellValue('A3', 'PT INTEGRASI LOGISTIK CIPTA SOLUSI');
$worksheet->mergeCells('A3:I3');
$worksheet->SetCellValue('A4', 'PERIODE : '.$awal.' S.D. '.$akhir);
$worksheet->mergeCells('A4:I4');

$row = 6;
$col = 0;

// Define Header
$headers = array(
	// First Row Header
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'NO',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'VESSEL',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'VOYAGE IN',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'VOYAGE OUT',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'BL NUMBER',
	
	$charsets[++$col].$row.':'.$charsets[$col = $col + 1].($row) => 'IMPORT',
	$charsets[++$col].$row.':'.$charsets[$col = $col + 1].($row) => 'EXPORT',						
	
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'DATE SEND',
	//$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'DATE SEND COARRI',
	
	// Second Row Header	
	$charsets[$col = 6].($row+1) => 'DISCHARGING',
	$charsets[++$col].($row+1) => 'GATE-OUT',	
	
	$charsets[$col = 8].($row+1) => 'GATE-IN',
	$charsets[++$col].($row+1) => 'LOADING',
);

// Set Default Style
$border_style = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	)
);
// Make Header
foreach($headers as $addr => $val){
	$def = explode(':', $addr);
	
	$worksheet->SetCellValue($def[0], $val);
	$worksheet->getStyle($addr)->applyFromArray($border_style);
	if(count($def) == 2){
		$worksheet->mergeCells($addr);
	}
}


// Contents
$row += 2;
$col = 1;
$no = 1;


//// CODECO
foreach($codeco as $item){
	if($item->DIRECTION == '1'){
		
		$worksheet->SetCellValue($charsets[$col++].$row, $no);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VISIT_NAME);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VOYAGE_IN);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VOYAGE_OUT);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->BL_NUMBER);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->CODECO);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->DATE_SEND_CODECO);
		//$worksheet->SetCellValue($charsets[$col++].$row, 0);
		
		$addr = 'A'.$row.':'.$charsets[$col-1].$row;
		$worksheet->getStyle($addr)->applyFromArray($border_style);
		
		$row++;
		$no++;
		$col = 1;
	}
}
foreach($codeco as $item){
	if($item->DIRECTION == '2'){
		
		$worksheet->SetCellValue($charsets[$col++].$row, $no);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VISIT_NAME);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VOYAGE_IN);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VOYAGE_OUT);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->BL_NUMBER);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->CODECO);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->DATE_SEND_CODECO);
		//$worksheet->SetCellValue($charsets[$col++].$row, 0);
		
		$addr = 'A'.$row.':'.$charsets[$col-1].$row;
		$worksheet->getStyle($addr)->applyFromArray($border_style);
		
		$row++;
		$no++;
		$col = 1;
	}
}

/// COARRI
foreach($coarri as $item){
	if($item->DIRECTION == '1'){
		
		$worksheet->SetCellValue($charsets[$col++].$row, $no);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VISIT_NAME);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VOYAGE_IN);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VOYAGE_OUT);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->BL_NUMBER);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->COARRI);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->DATE_SEND_COARRI);
		//$worksheet->SetCellValue($charsets[$col++].$row, 0);
		
		$addr = 'A'.$row.':'.$charsets[$col-1].$row;
		$worksheet->getStyle($addr)->applyFromArray($border_style);
		
		$row++;
		$no++;
		$col = 1;
	}
}
foreach($coarri as $item){
	if($item->DIRECTION == '2'){
		
		$worksheet->SetCellValue($charsets[$col++].$row, $no);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VISIT_NAME);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VOYAGE_IN);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->VOYAGE_OUT);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->BL_NUMBER);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, 0);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->COARRI);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->DATE_SEND_COARRI);
		//$worksheet->SetCellValue($charsets[$col++].$row, 0);
		
		$addr = 'A'.$row.':'.$charsets[$col-1].$row;
		$worksheet->getStyle($addr)->applyFromArray($border_style);
		
		$row++;
		$no++;
		$col = 1;
	}
}
/*
// Footer Sum Line 1
$worksheet->SetCellValue($charsets[$col++].$row, 'SUMMARY');
$worksheet->mergeCells('A'.$row.':C'.$row);
$col = 4;
$worksheet->SetCellValue($charsets[$col++].$row, $total_plan_discharge);
$worksheet->SetCellValue($charsets[$col++].$row, $total_plan_loading);
$worksheet->SetCellValue($charsets[$col++].$row, $total_actual_discharge);
$worksheet->SetCellValue($charsets[$col++].$row, $total_actual_loading);
$row++;
$col = 1;

// Footer Sum Line 2
$worksheet->SetCellValue($charsets[$col++].$row, 'GRAND TOTAL');
$worksheet->mergeCells('A'.$row.':G'.$row);
$col = 8;
$worksheet->SetCellValue($charsets[$col++].$row, $total_summary);
$worksheet->SetCellValue($charsets[$col++].$row, $total_m3);
$worksheet->SetCellValue($charsets[$col++].$row, $total_tonnage);

$row = $row + 2;
$col = 1;

// Notes Section
$worksheet->SetCellValue($charsets[$col++].$row, 'NOTES');
$row++;

$statistik_import = NULL;
$statistik_export = NULL;

foreach($statistik as &$stat){
	if($stat->EI == 'E') $statistik_export = $stat;
	else $statistik_import = $stat;
}
*/
$row++;$row++;
$col = 8;
$worksheet->SetCellValue($charsets[$col++].$row, 'Jakarta,                                          2018');
$worksheet->mergeCells('H'.$row.':J'.$row);
$row++;$row++;$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, '  PT EDI Indonesia');
$worksheet->mergeCells('B'.$row.':C'.$row);
$col++;$col++;
$worksheet->SetCellValue($charsets[$col++].$row, '  PT Integrasi Logistik Cipta Solusi');
$worksheet->mergeCells('F'.$row.':H'.$row);
$row++;$row++;$row++;$row++;$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, '  Johan Pratyaksono');
$worksheet->mergeCells('B'.$row.':C'.$row);
$col++;$col++;
$worksheet->SetCellValue($charsets[$col++].$row, '  ..................');
$worksheet->mergeCells('F'.$row.':H'.$row);

// Finalize
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan DATA CODECO COARRI CAR TERMINAL ('.$awal.' sd '.$akhir.').xlsx"');

$objWriter->save('php://output');
$excel->disconnectWorksheets();