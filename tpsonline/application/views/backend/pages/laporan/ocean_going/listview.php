<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">

			<h1>Rekap</h1>

			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/reportfilterform') ?>
				</div>
			</div>
			
			<hr />

			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>No Request</th>
						<th>Nomor DO</th>
						<th>Tanggal Ambil</th>
						<th>Terminal</th>
						<th>Consignee</th>
						<th>Total Biaya</th>
						<th>Lunas</th>
						<th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<tr><td colspan="8"><em>Tidak ada data</em></td></tr>
				</tbody>
			</table>
			<?php //$this->load->view('backend/components/paging') ?>
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>