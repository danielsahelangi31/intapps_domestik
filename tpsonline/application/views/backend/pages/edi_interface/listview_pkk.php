<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1><a href="<?php echo site_url('edi_interface/listview_kapal') ?>">EDI Interface</a> / MV. CAPE FULMAR / Daftar PKK</h1>
			<p class="lead">
				<small>Klik Lihat Dokumen untuk unggah berkas EDI</small>
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

			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th><?php echo gridHeader('nomor_pkk', 'No PKK', $cfg) ?></th>
						<th><?php echo gridHeader('call_sign', 'Call Sign', $cfg) ?></th>
						<th><?php echo gridHeader('nama_kapal', 'Nama Kapal', $cfg) ?></th>
						<th><?php echo gridHeader('nama_kapal', 'Voyage In', $cfg) ?></th>
						<th><?php echo gridHeader('nama_kapal', 'Voyage Out', $cfg) ?></th>
						<th><?php echo gridHeader('nama_kapal', 'ETA', $cfg) ?></th>
						<th><?php echo gridHeader('nama_kapal', 'ETD', $cfg) ?></th>
						<th><?php echo gridHeader('nama_kapal', 'Jumlah Dokumen', $cfg) ?></th>
						<th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>PKK00001101111</td>
						<td>CPVULMR</td>
						<td>MV. Cape Fulmar</td>
						<td>088E</td>
						<td>088E</td>
						<td>10-Okt-2013</td>
						<td>12-Okt-2013</td>
						<td>8</td>
						<td>
							<a href="<?php echo site_url('edi_interface/dokumen') ?>">Lihat Dokumen</a>
						</td>
					</tr>
				</tbody>
			</table>
			<?php $this->load->view('backend/components/paging') ?>
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>