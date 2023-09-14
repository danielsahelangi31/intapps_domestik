<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Ringkasan Data Belum Disetujui</h1>
			<p class="lead">
				<small>Antarmuka ini menampilkan ringkasan data-data <em>master</em> yang belum disetujui untuk masuk ILCS Master Reference</small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
			</div>
			
			<hr />

			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th><?php echo gridHeader('jenis_Data', 'Jenis Data', $cfg) ?></th>
						<th><?php echo gridHeader('total_unmapped', 'Jumlah Data Belum Dicocokkan', $cfg) ?></th>
						<th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Pelabuhan</td>
						<td>5688</td>
						<td><a href="<?php echo site_url('ilcs_mapper/approval_port/listview') ?>">Rincian</a></td>
					</tr>
					<tr>
						<td>Kapal</td>
						<td>56</td>
						<td><a href="<?php echo site_url('ilcs_mapper/approval_vessel/listview') ?>">Rincian</a></td>
					</tr>
					<tr>
						<td>Agen Kapal</td>
						<td>32555</td>
						<td><a href="<?php echo site_url('ilcs_mapper/approval_shipping_agent/listview') ?>">Rincian</a></td>
					</tr>
					<?php
					if($datasource){
						foreach($datasource as $row){
							
					?>
					
					<?php
						}
					}else{
					?>
                    <tr><td colspan="8"><em>Tidak ada data</em></td></tr>
                    <?php	
					}
					?>
				</tbody>
			</table>
			<?php $this->load->view('backend/components/paging') ?>
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>