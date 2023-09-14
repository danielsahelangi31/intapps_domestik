<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>EDI Interface</h1>
			<p class="lead">
				<small>Pilih Kapal untuk menampilkan daftar PKK.</small>
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
						<th><?php echo gridHeader('nomor_pkk', 'Kode Kapal', $cfg) ?></th>
						<th><?php echo gridHeader('call_sign', 'Call Sign', $cfg) ?></th>
						<th><?php echo gridHeader('nama_kapal', 'Nama Kapal', $cfg) ?></th>
						<th><?php echo gridHeader('nama_agen', 'Nama Agen', $cfg) ?></th>
						<th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>CPVU00000001</td>
						<td>CPVULMR</td>
						<td>MV. Cape Fulmar</td>
						<td>PT. Samudera Indonesia</td>
						<td>
							<a href="<?php echo site_url('edi_interface/listview_pkk') ?>">Lihat PKK</a>
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