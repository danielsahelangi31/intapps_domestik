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
		<h1>DAILY REPORT TPS ONLINE </h1>
		<!--<h2><?php echo $kapal->VISIT_NAME ?></h2>-->
	</div>
	<table class="fullwidth">
		<thead>
			<tr>
			  <th rowspan="3">VESSEL</th>
			  <th rowspan="3">BL NUMBER</th>
			  <th colspan="10">IMPORT</th>
			  <th colspan="10">EXPORT</th>
			</tr>
			<tr>
			  <th colspan="5">DISCHARGE</th>
			  <th colspan="5">GATE-OUT</th>
			  <th colspan="5">GATE-IN</th>
			  <th colspan="5">LOAD</th>
			</tr>
			<tr>
			  <th>CBU</th>
			  <th>HH</th>
			  <th>PARTS</th>
			  <th>MOTOR</th>
			  <th bgcolor="#FF8000">TOTAL</th>
			  <th>CBU</th>
			  <th>HH</th>
			  <th>PARTS</th>
			  <th>MOTOR</th>
			  <th bgcolor="#FF8000">TOTAL</th>
			  <th>CBU</th>
			  <th>HH</th>
			  <th>PARTS</th>
			  <th>MOTOR</th>
			  <th bgcolor="#FF8000">TOTAL</th>
			  <th>CBU</th>
			  <th>HH</th>
			  <th>PARTS</th>
			  <th>MOTOR</th>
			  <th bgcolor="#FF8000">TOTAL</th>
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
				if($row->EI == I){
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
				if($row->EI == E){
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
</body>
</html>