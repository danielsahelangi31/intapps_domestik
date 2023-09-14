<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Selamat Datang di Integration Apps</h1>
			<p class="lead">
				<small>Silakan pilih menu diatas untuk memulai, untuk info lebih lanjut silakan klik Bantuan Help Desk.</small>
			</p>
			
			<hr />
            
			<h4>Jadwal Kapal</h4>
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>UKK</th>
						<th>Nama Kapal</th>
						<th>Voyage</th>
						<th>ETA</th>
						<th>ETD</th>
                        <th>Tujuan</th>
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