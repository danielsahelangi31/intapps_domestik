Data<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			<h4>Pemeriksaan Segel</h4>
			
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>No. Vin</th>
						<th>ID Truck</th>
						<th>Nama Perusahaan</th>
						<th>Gate In</th>
						<th>Gate Out</th>
                        <th>No. SPPB</th>
                        <th>Kargo</th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>