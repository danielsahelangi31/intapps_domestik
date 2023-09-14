<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Delivery Request</h1>
			<p class="lead">
				<small>Siapkan Dokumen Delivery Order Anda.</small>
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
						<th><?php echo gridHeader('nomor_request_inhouse', 'No Req. Terminal', $cfg) ?></th>
						<th><?php echo gridHeader('nomor_do', 'Nomor DO', $cfg) ?></th>
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
							if($row->flag_lunas){
								$status_lunas = 'Lunas';
							}else{
								$status_lunas = 'Belum Lunas';	
							}
					?>
					<tr>
						<td><?php echo $row->nomor_request_inhouse ?></td>
						<td><?php echo $row->nomor_do ?></td>
                        <td><?php echo date('d-M-Y', strtotime($row->rencana_ambil)) ?></td>
                        <td><?php echo $row->nama_terminal_petikemas ?></td>
                        <td><?php echo $row->consignee ?></td>
						<td><?php echo $row->nomor_faktur_pajak ?></td>
						<td><?php echo $status_lunas ?></td>
                        <td>
							<?php
							if($row->status_kirim == 0){
							?>
							<a href="<?php echo site_url('delivery_request_og/edit/'.$row->id) ?>" class="edit_link">Edit</a> |
							<a href="<?php echo site_url('delivery_request_og/send/'.$row->id) ?>" class="edit_link">Kirim</a>
							<?php
							}else{
								if(!$row->nota_id){
							?>
							<a href="<?php echo site_url('delivery_request_og/view/'.$row->id) ?>" class="edit_link">Lihat</a> |
							<a href="<?php echo site_url('delivery_request_og/preview_invoice/'.$row->id) ?>" class="edit_link">Pembayaran &amp; Pra Nota</a>
							<?php
								}else{
							?>
							<a href="<?php echo site_url('delivery_request_og/view/'.$row->id) ?>" class="edit_link">Lihat</a> |
							<a href="<?php echo site_url('delivery_request_og/invoice/'.$row->id) ?>" class="edit_link">Invoice</a>
							<?php
								}
							
								if($row->flag_lunas){
							?>
							| <a href="<?php echo site_url('delivery_request_og/assign_truck/'.$row->id) ?>" class="assign_truck">Assign Truck</a>
							<?php
								}
							}
							?>
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