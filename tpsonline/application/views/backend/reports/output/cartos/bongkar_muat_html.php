<!DOCTYPE html>
<html>
<head>
	<title>Report Bongkar Muat</title>
	<style type="text/css">
	table{
		border-collapse: collapse;
	}
	
	table td, table th{
		border: 1px solid #000;
		padding: 2px;
	}
	
	.fullwidth{
		width:100%;
	}
	
	td.right{
		text-align:right;
	}
	
	.pull-right{
		float:right;
	}
	</style>
</head>
<body>
	<div class="header">
		<div class="visit_date_info pull-right">
			<table>
				<tbody>
					<tr>
						<td>TB</td>
						<td>:</td>
						<td><?php echo $kapal->ARRIVAL ?></td>
					</tr>
					<tr>
						<td>TD</td>
						<td>:</td>
						<td><?php echo $kapal->DEPARTURE ?></td>
					</tr>
				<tbody>
			</table>
		</div>
		
		<h1>DAILY REPORT OF SHIP OPERATION</h1>
		<h2><?php echo $kapal->VISIT_NAME ?></h2>
	</div>
	<table class="fullwidth">
		<thead>
			<tr>
				<th rowspan="2">No</th>
				<th rowspan="2">Description</th>
				<th rowspan="2">Type Cargo</th>
				<th colspan="2">Planning</th>
				<th colspan="2">Actual</th>
				<th rowspan="2">Summary</th>
				<th rowspan="2">CBM</th>
				<th rowspan="2">TON</th>
				<th rowspan="2">CBM/TON</th>
				<th rowspan="2">Ceiling</th>
				<th rowspan="2">Cargodoring</th>
				<th rowspan="2">Stevedoring</th>
				<th rowspan="2">Information/Stack</th>
			</tr>
			<tr>
				<th>Discharging</th>
				<th>Loading</th>
				<th>Discharging</th>
				<th>Loading</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="15"><em>CARGO IMPORT</em></td>
			</tr>
			<?php
			$i = 1;
			
			$total_plan_discharge = 0;
			$total_plan_loading = 0;
			$total_actual_discharge = 0;
			$total_actual_loading = 0;
			$total_summary = 0;
			$total_m3 = 0;
			$total_tonnage = 0;
			
			foreach($datasource as $row){
				if($row->EI == 'I'){
					$CBM_TONNAGE = $row->M3 > $row->TONNAGE ? $row->M3 : $row->TONNAGE;
					
					$total_plan_discharge += $row->JUMLAH;
					$total_actual_discharge += $row->JUMLAH;
					$total_summary += $row->JUMLAH;
					$total_m3 += $row->M3;
					$total_tonnage += $row->TONNAGE;
			?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $row->KD_CARGO ?></td>
				<td class="right">&nbsp;</td>
				<td class="right"><?php echo $row->JUMLAH ?></td>
				<td class="right">&nbsp;</td>
				<td class="right"><?php echo $row->JUMLAH ?></td>
				<td class="right">&nbsp;</td>
				<td class="right"><?php echo $row->JUMLAH ?></td>
				<td class="right"><?php echo $row->M3 ?></td>
				<td class="right"><?php echo $row->TONNAGE ?></td>
				<td class="right"><?php echo $CBM_TONNAGE ?></td>
				<td class="right"><?php echo ceil($CBM_TONNAGE) ?></td>
				<td><?php echo $row->PBM_CARGODORING ?></td>
				<td><?php echo $row->PBM_STEVEDORING_ON_VESSEL ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php
				}
			}
			?>
			<tr>
				<td colspan="15"><em>CARGO EXPORT</em></td>
			</tr>
			<?php
			$i = 1;
			foreach($datasource as $row){
				if($row->EI == 'E'){
					$CBM_TONNAGE = $row->M3 > $row->TONNAGE ? $row->M3 : $row->TONNAGE;
					
					$total_plan_loading += $row->JUMLAH;
					$total_actual_loading += $row->JUMLAH;
					$total_summary += $row->JUMLAH;
					$total_m3 += $row->M3;
					$total_tonnage += $row->TONNAGE;
			?>
			<tr>
				<td><?php echo $i++ ?></td>
				<td><?php echo $row->KD_CARGO ?></td>
				<td class="right">&nbsp;</td>
				<td class="right">&nbsp;</td>
				<td class="right"><?php echo $row->JUMLAH ?></td>
				<td class="right">&nbsp;</td>
				<td class="right"><?php echo $row->JUMLAH ?></td>
				<td class="right"><?php echo $row->JUMLAH ?></td>
				<td class="right"><?php echo $row->M3 ?></td>
				<td class="right"><?php echo $row->TONNAGE ?></td>
				<td class="right"><?php echo $CBM_TONNAGE ?></td>
				<td class="right"><?php echo ceil($CBM_TONNAGE) ?></td>
				<td><?php echo $row->PBM_CARGODORING ?></td>
				<td><?php echo $row->PBM_STEVEDORING_ON_VESSEL ?></td>
				<td>&nbsp;</td>
			</tr>
			<?php
				}
			}
			?>
			<tr>
				<td colspan="3"><em>SUMMARY</em></td>
				<td><?php echo $total_plan_discharge ?></td>
				<td><?php echo $total_plan_loading ?></td>
				<td><?php echo $total_actual_discharge ?></td>
				<td><?php echo $total_actual_loading ?></td>
				<td colspan="8">&nbsp;</td>				
			</tr>
			<tr>
				<td colspan="7"><em>GRAND TOTAL</em></td>
				<td><?php echo $total_summary ?></td>
				<td><?php echo $total_m3 ?></td>
				<td><?php echo $total_tonnage ?></td>
				<td colspan="5">&nbsp;</td>				
			</tr>
		</tbody>
	</table>
	
	<h2>Notes</h2>
	<?php
	$statistik_import = NULL;
	$statistik_export = NULL;
	
	foreach($statistik as &$row){
		if($row->EI == 'E') $statistik_export = $row;
		else $statistik_import = $row;
	}
	?>
	<table>
		<tr>
			<td>Opened Rampdoor</td>
			<td>:</td>
			<td><?php echo $kapal->OPERATIONAL ?></td>
		</tr>
		<tr>
			<td>Closed Rampdoor</td>
			<td>:</td>
			<td><?php echo $kapal->COMPLETION ?></td>
		</tr>
		<tr>
			<td>Commenced to Discharge</td>
			<td>:</td>
			<td><?php echo $statistik_import->START_WORK_PBM ?></td>
		</tr>
		<tr>
			<td>Completed to Discharge</td>
			<td>:</td>
			<td><?php echo $statistik_import->END_WORK_PBM ?></td>
		</tr>
		<tr>
			<td>Commenced to Load</td>
			<td>:</td>
			<td><?php echo $statistik_export->START_WORK_PBM ?></td>
		</tr>
		<tr>
			<td>Completed to Load</td>
			<td>:</td>
			<td><?php echo $statistik_export->END_WORK_PBM ?></td>
		</tr>
		<tr>
			<td>Jumlah Gang</td>
			<td>:</td>
			<td><?php '-' ?></td>
		</tr>
		<tr>
			<td>LOA</td>
			<td>:</td>
			<td><?php '-' ?></td>
		</tr>
		<tr>
			<td>GRT</td>
			<td>:</td>
			<td><?php echo '-' ?></td>
		</tr>
	</table>
	
</body>
</html>