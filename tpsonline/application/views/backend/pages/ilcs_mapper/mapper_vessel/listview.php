<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Pembersih Data Master Pelabuhan</h1>
			<p class="lead">
				<small>Antarmuka ini menampilkan data yang belum berhasil di map secara otomatis</small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						<a href="<?php echo site_url('delivery_request_og/add') ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Buat Permintaan Delivery</a>
					</div>
				</div>
			</div>
			
			<hr />

			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th><?php echo gridHeader('tipe', 'No Req. Terminal', $cfg) ?></th>
						<th><?php echo gridHeader('kode_lokal', 'Nomor DO', $cfg) ?></th>
						<th><?php echo gridHeader('rencana_ambil', 'Tgl Ambil', $cfg) ?></th>
						<th><?php echo gridHeader('nama_terminal_petikemas', 'Terminal', $cfg) ?></th>
						<th><?php echo gridHeader('consignee', 'Consignee', $cfg) ?></th>
						<th><?php echo gridHeader('nomor_faktur_pajak', 'Nomor Faktur', $cfg) ?></th>
						<th><?php echo gridHeader('status_lunas', 'Lunas', $cfg) ?></th>
						<th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($datasource){
						foreach($datasource as $row){
							
					?>
					<tr>
						<td></td>
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