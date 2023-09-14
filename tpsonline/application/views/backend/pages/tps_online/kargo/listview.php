<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Data Kargo</h1>
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
							<th><?php echo gridHeader('BL_NUMBER', 'Nomor BL', $cfg) ?></th>
							<th><?php echo gridHeader('BL_NUMBER_DATE', 'Tanggal BL', $cfg) ?></th>
							<th><?php echo gridHeader('NO_PEB', 'No. PEB', $cfg) ?></th>
							<th><?php echo gridHeader('NO_NPE', 'No. NPE', $cfg) ?></th>
							<th><?php echo gridHeader('VISIT_ID', 'Visit ID', $cfg) ?></th>
							<th><?php echo gridHeader('COUNTOF', 'Jumlah VIN', $cfg) ?></th>
							<th>Tindakan</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;
						
						if($datasource){
							foreach($datasource as $row){
						?>
						<tr>
							<td><?php echo $row->BL_NUMBER ?></td>
							<td><?php echo $row->BL_NUMBER_DATE ?></td>
							<td><?php echo $row->NO_PEB ?></td>
							<td><?php echo $row->NO_NPE ?></td>
							<td><?php echo $row->VISIT_ID ?></td>
							<td><?php echo $row->COUNTOF ?></td>
							<td>
								<a href="<?php echo site_url('tps_online/kargolist/listview/'.$row->BL_NUMBER) ?>" class="edit_link">Lihat</a>
							</td>
						</tr>
						<?php
							}
						}else{
						?>
						<tr><td colspan="9"><em>Tidak ada data</em></td></tr>
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