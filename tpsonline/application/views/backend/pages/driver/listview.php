<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">

			<h1>Data Supir Truck</h1>
			<p class="lead">
				<small>Daftarkan supir truk anda untuk mempermudah pemilihan supir.</small>
			</p>

			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						<a href="<?php echo site_url('driver/add') ?>" class="btn btn-primary">Driver Baru</a>
					</div>
				</div>
			</div>

			<hr />

			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>Nama</th>
						<th>Nomor Handphone</th>
						<th>Plat Nomor</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($datasource){
						foreach($datasource as $row){
					?>
					<tr>
						<td><?php echo $row->nama_supir ?></td>
                        <td><?php echo $row->nomor_handphone ?></td>
                        <td><?php echo $row->plat_nomor ?></td>
                        <td>
							<a href="<?php echo site_url('driver/edit/'. $row->id) ?>" class="edit_link">Edit Supir</a> |
							<a href="<?php echo site_url('driver/delete/'. $row->id) ?>" class="edit_link">Delete Supir</a>
                        </td>
					</tr>
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