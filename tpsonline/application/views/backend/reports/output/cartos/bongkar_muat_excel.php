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
	'B' => 165,
	'C' => 70,
	'D' => 60,
	'E' => 60,
	'F' => 60,
	'G' => 60,
	'H' => 60,
	'I' => 60,
	'J' => 60,
	'K' => 60,
	'L' => 60,
	'M' => 85,
	'N' => 85,
	'O' => 85,
);

foreach($column_sizes as $col => $width){
	$worksheet->getColumnDimension($col)->setWidth($width / 5.5);
}

$worksheet->SetCellValue('D2', 'DAILY REPORT OF SHIP OPERATION');
$worksheet->mergeCells('D2:I2');
$worksheet->SetCellValue('D3', $kapal->VISIT_NAME);
$worksheet->mergeCells('D3:I3');

$worksheet->SetCellValue('B3', 'PT. INDONESIA KENDARAAN TERMINAL / IKT');

$worksheet->SetCellValue('M2', 'TB: '.$kapal->ARRIVAL);
$worksheet->mergeCells('M2:O2');
$worksheet->SetCellValue('M3', 'TD: '.$kapal->DEPARTURE);
$worksheet->mergeCells('M3:O3');

$row = 5;
$col = 0;

// Define Header
$headers = array(
	// First Row Header
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'NO',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'DESCRIPTION',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'TYPE CARGO',
	
	$charsets[++$col].$row.':'.$charsets[$col = $col + 1].($row) => 'PLANNING',
	$charsets[++$col].$row.':'.$charsets[$col = $col + 1].($row) => 'ACTUAL',						
	
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'SUMMARY',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'CBM',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'TON',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'CBM	TON',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'CEILING',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'CARGODORING',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'STEVEDORING',
	$charsets[++$col].$row.':'.$charsets[$col].($row+1) => 'INFORMATION/STACK',
	
	// Second Row Header	
	$charsets[$col = 4].($row+1) => 'DISCHARGING',
	$charsets[++$col].($row+1) => 'LOADING',	
	
	$charsets[$col = 6].($row+1) => 'DISCHARGING',
	$charsets[++$col].($row+1) => 'LOADING',
);

// Make Header
foreach($headers as $addr => $val){
	$def = explode(':', $addr);
	
	$worksheet->SetCellValue($def[0], $val);
	if(count($def) == 2){
		$worksheet->mergeCells($addr);
	}
}

// Contents
$row += 2;
$col = 1;
$no = 1;

$total_plan_discharge = 0;
$total_plan_loading = 0;
$total_actual_discharge = 0;
$total_actual_loading = 0;
$total_summary = 0;
$total_m3 = 0;
$total_tonnage = 0;

$worksheet->SetCellValue('A'.$row, 'CARGO IMPORT');
$worksheet->mergeCells('A'.$row.':O'.$row);
$row++;

foreach($datasource as $item){
	if($item->EI == 'I'){
		$CBM_TONNAGE = $item->M3 > $item->TONNAGE ? $item->M3 : $item->TONNAGE;
		
		$total_plan_discharge += $item->JUMLAH;
		$total_actual_discharge += $item->JUMLAH;
		$total_summary += $item->JUMLAH;
		$total_m3 += $item->M3;
		$total_tonnage += $item->TONNAGE;
		
		$worksheet->SetCellValue($charsets[$col++].$row, $no);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->KD_CARGO);
		$worksheet->SetCellValue($charsets[$col++].$row, ' ');
		$worksheet->SetCellValue($charsets[$col++].$row, $item->JUMLAH);
		$worksheet->SetCellValue($charsets[$col++].$row, ' ');
		$worksheet->SetCellValue($charsets[$col++].$row, $item->JUMLAH);
		$worksheet->SetCellValue($charsets[$col++].$row, ' ');
		$worksheet->SetCellValue($charsets[$col++].$row, $item->JUMLAH);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->M3);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->TONNAGE);
		$worksheet->SetCellValue($charsets[$col++].$row, $CBM_TONNAGE);
		$worksheet->SetCellValue($charsets[$col++].$row, ceil($CBM_TONNAGE));
		$worksheet->SetCellValue($charsets[$col++].$row, $item->PBM_CARGODORING);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->PBM_STEVEDORING_ON_VESSEL);
		$worksheet->SetCellValue($charsets[$col++].$row, ' ');
		
		$row++;	
		$col = 1;
	}
}

$worksheet->SetCellValue('A'.$row, 'CARGO EXPORT');
$worksheet->mergeCells('A'.$row.':O'.$row);
$row++;

foreach($datasource as $item){
	if($item->EI == 'E'){
		$CBM_TONNAGE = $item->M3 > $item->TONNAGE ? $item->M3 : $item->TONNAGE;
					
		$total_plan_loading += $item->JUMLAH;
		$total_actual_loading += $item->JUMLAH;
		$total_summary += $item->JUMLAH;
		$total_m3 += $item->M3;
		$total_tonnage += $item->TONNAGE;
		
		$worksheet->SetCellValue($charsets[$col++].$row, $no);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->KD_CARGO);
		$worksheet->SetCellValue($charsets[$col++].$row, ' ');
		$worksheet->SetCellValue($charsets[$col++].$row, ' ');
		$worksheet->SetCellValue($charsets[$col++].$row, $item->JUMLAH);
		$worksheet->SetCellValue($charsets[$col++].$row, ' ');
		$worksheet->SetCellValue($charsets[$col++].$row, $item->JUMLAH);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->JUMLAH);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->M3);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->TONNAGE);
		$worksheet->SetCellValue($charsets[$col++].$row, $CBM_TONNAGE);
		$worksheet->SetCellValue($charsets[$col++].$row, ceil($CBM_TONNAGE));
		$worksheet->SetCellValue($charsets[$col++].$row, $item->PBM_CARGODORING);
		$worksheet->SetCellValue($charsets[$col++].$row, $item->PBM_STEVEDORING_ON_VESSEL);
		$worksheet->SetCellValue($charsets[$col++].$row, ' ');
		
		$row++;	
		$col = 1;
	}
}

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

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'Opened Rampdoor');
$worksheet->SetCellValue($charsets[$col++].$row, ': '.$kapal->OPERATIONAL);
$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'Closed Rampdoor');
$worksheet->SetCellValue($charsets[$col++].$row, ': '.$kapal->COMPLETION);
$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'Commenced to Discharge');
$worksheet->SetCellValue($charsets[$col++].$row, ': '.$statistik_import->START_WORK_PBM);
$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'Completed to Discharge');
$worksheet->SetCellValue($charsets[$col++].$row, ': '.$statistik_import->END_WORK_PBM);
$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'Commenced to Load');
$worksheet->SetCellValue($charsets[$col++].$row, ': '.$statistik_export->START_WORK_PBM);
$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'Completed to Load');
$worksheet->SetCellValue($charsets[$col++].$row, ': '.$statistik_export->END_WORK_PBM);
$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'Jumlah Gang');
$worksheet->SetCellValue($charsets[$col++].$row, ': -');
$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'LOA');
$worksheet->SetCellValue($charsets[$col++].$row, ': -');
$row++;

$col = 2;
$worksheet->SetCellValue($charsets[$col++].$row, 'GRT');
$worksheet->SetCellValue($charsets[$col++].$row, ': -');
$row++;

// Finalize
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan RBM'.$kapal->VISIT_NAME.'.xlsx"');

$objWriter->save('php://output');
$excel->disconnectWorksheets();