<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Persetujuan Data</h1>
			<p class="lead">
				<small></small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					
				</div>
			</div>
			
			<hr />

			<div class="table-responsive">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th><?php echo gridHeader('partner_code', 'Kode Partner', $cfg) ?></th>
							<th><?php echo gridHeader('entity', 'Entity', $cfg) ?></th>
							<th><?php echo gridHeader('ilcs_id', 'ID ILCS', $cfg) ?></th>
							<th><?php echo gridHeader('receipt_time', 'Waktu Terima', $cfg) ?></th>
							<th><?php echo gridHeader('action_time', 'Waktu Tindakan', $cfg) ?></th>
							<th><?php echo gridHeader('action_taken', 'Tindakan Diambil', $cfg) ?></th>
							<th>Tindakan</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if($datasource){
							foreach($datasource as $row){
						?>
						<tr>
							<td><?php echo $row->partner_code ?></td>
							<td><?php echo $row->entity ?></td>
							<td><?php echo $row->ilcs_id ?></td>
							<td><?php echo date('d-M-Y H:i:s', strtotime($row->receipt_time)) ?></td>
							<td><?php echo date('d-M-Y H:i:s', strtotime($row->action_time)) ?></td>
							<td><?php echo $row->action_taken ?></td>
							<td>
								<a href="<?php echo site_url('ilcs_master_reference/data_approval/view/'.$row->master_data_approval_id) ?>">Lihat</a>
							</td>
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