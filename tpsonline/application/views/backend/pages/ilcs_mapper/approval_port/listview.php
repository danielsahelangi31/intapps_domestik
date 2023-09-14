<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Data Pelabuhan Baru</h1>
			<p class="lead">
				<small>Data pelabuhan yang belum disetujui sebagai referensi</small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						<a href="<?php echo site_url('delivery_request_og/add') ?>" class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"></span> Unduh Data</a>
						<a href="<?php echo site_url('delivery_request_og/add') ?>" class="btn btn-primary"><span class="glyphicon glyphicon-list-alt"></span> Impor dari Excel</a>
					</div>
				</div>
			</div>
			
			<hr />

			<table class="table table-striped table-condensed" style="width:1600px">
				<thead>
					<tr>
						<th style="width:5%"><?php echo gridHeader('kode_lokal', 'ID', $cfg) ?></th>
						<th style="width:10%"><?php echo gridHeader('kode_negara', 'Kode Negara', $cfg) ?></th>
						<th style="width:20%"><?php echo gridHeader('nama_pelabuhan', 'Nama Pelabuhan', $cfg) ?></th>
						
						<th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1004</td>
						<td>INA</td>
						<td>Tanjung Priok, Jakarta</td>
						
						<td><a href="#">Detail</a></td>
					</tr>
				
					<tr>
						<td>1004</td>
						<td>INA</td>
						<td>Tanjung Priok, Jakarta</td>
						<td><a href="#">Detail</a></td>
					</tr>
					
				</tbody>
			</table>
			<?php $this->load->view('backend/components/paging') ?>
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>