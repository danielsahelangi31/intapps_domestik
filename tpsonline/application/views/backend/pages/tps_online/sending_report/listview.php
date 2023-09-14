<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Log Pengiriman Data</h1>
			<p class="lead">
				<small></small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						
					</div>
				</div>
			</div>
			
			<hr />
			
			<div class="table-responsive">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th><?php echo gridHeader('SVC_INSTANCE', 'Method', $cfg) ?></th>
							<th><?php echo gridHeader('ID_TRX', 'REF Number', $cfg) ?></th>
							<th><?php echo gridHeader('BL_NUMBER', 'Nomor BL', $cfg) ?></th>
							<th><?php echo gridHeader('VISIT_ID', 'Visit ID', $cfg) ?></th>
							<th><?php echo gridHeader('CUSTOMS_CARGO_TYPE', 'Tipe Kargo', $cfg) ?></th>
							<th><?php echo gridHeader('COUNTERS', 'Pengiriman Ke', $cfg) ?></th>
							<th><?php echo gridHeader('SUM_CARGO', 'Jumlah Cargo', $cfg) ?></th>
							<th><?php echo gridHeader('ACK', 'Respon Pengiriman', $cfg) ?></th>
							<th><?php echo gridHeader('STATUS', 'Status', $cfg) ?></th>
							<!--<th>Tindakan</th>-->
						</tr>
					</thead>
					<tbody>
						<?php
						$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;
						
						if($datasource){
							foreach($datasource as $row){
						?>
						<tr>
							<td><?php echo $row->SVC_INSTANCE ?></td>
							<td><?php echo $row->ID_TRX ?></td>
							<td><?php echo $row->BL_NUMBER ?></td>
							<td><?php echo $row->VISIT_ID ?></td>
							<td><?php echo $row->CUSTOMS_CARGO_TYPE ?></td>
							<td><?php echo $row->COUNTERS ?></td>
							<td><?php echo $row->SUM_CARGO ?></td>
							<td><?php echo $row->ACK ?></td>
							<td><?php echo $row->STATUS ?></td>
							<!--<td>
								<a href="<?php echo site_url('tps_online/send_report/view/'.$row->BL_NUMBER.'/'.$grid_state) ?>" class="edit_link">Lihat Detail</a>
							</td>-->
						</tr>
						<?php
							}
						}else{
						?>
						<tr><td colspan="7"><em>Tidak ada data</em></td></tr>
						<?php	
						}
						?>
					</tbody>
				</table>
			</div>
			
			<?php $this->load->view('backend/components/paging') ?>
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>