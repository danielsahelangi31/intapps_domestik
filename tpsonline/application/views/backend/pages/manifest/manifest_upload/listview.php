<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Unggah Manifest</h1>
			<p class="lead">
				<small></small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						<a href="<?php echo site_url('manifest/manifest_upload/add') ?>" class="btn btn-primary"><span class="glyphicon glyphicon-upload"></span> Unggah Manifest</a>
					</div>
				</div>
			</div>
			
			<hr />
			
			<div class="table-responsive">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th><?php echo gridHeader('no_ukk', 'No UKK', $cfg) ?></th>
							<th><?php echo gridHeader('voyage', 'Voyage', $cfg) ?></th>
							<th><?php echo gridHeader('call_sign', 'Call Sign', $cfg) ?></th>
							<th><?php echo gridHeader('nama_kapal', 'Nama Kapal', $cfg) ?></th>
							<th><?php echo gridHeader('pod', 'Tujuan', $cfg) ?></th>
							<th><?php echo gridHeader('pol', 'Asal', $cfg) ?></th>
							<th><?php echo gridHeader('ata', 'ATA @ POL', $cfg) ?></th>
							<th><?php echo gridHeader('atd', 'ATD @ POL', $cfg) ?></th>
							<th><?php echo gridHeader('waktu_upload', 'Waktu Upload', $cfg) ?></th>
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
							<td><?php echo $row->no_ukk ?></td>
							<td><?php echo $row->voyage ?></td>
							<td><?php echo $row->call_sign ?></td>
							<td><?php echo $row->nama_kapal ?></td>
							<td><?php echo $row->pod ?></td>
							<td><?php echo $row->pol ?></td>
							<td><?php echo date('d-M-Y', strtotime($row->ata)) ?></td>
							<td><?php echo date('d-M-Y', strtotime($row->atd)) ?></td>
							<td><?php echo date('d-M-Y H:i:s', strtotime($row->waktu_upload)) ?></td>
							<td>
								<a href="<?php echo site_url('manifest/manifest_upload/view/'.$row->id.'/'.$grid_state) ?>" class="edit_link">Lihat</a>
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