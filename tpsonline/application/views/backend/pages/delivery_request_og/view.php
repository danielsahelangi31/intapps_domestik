<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<div class="row">
            	<div class="col-md-8">
                	<h2>Lihat Permintaan Delivery</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>
			
			<?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
			<input type="hidden" id="id" name="id" value="<?php echo $del_req->id ?>">
			
			<div class="row">
				<div class="col-lg-6">

					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Nomor Delivery Order / Tanggal Kadaluarsa</legend>
						<div class="col-lg-12">
							<label><?php echo $del_req->nomor_do ?> / EXP. <?php echo $del_req->expired_do ?></label>
						</div>
					</fieldset>

				</div>

				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Terminal</legend>
						<div class="col-lg-12">
							<label class="text-left"><?php echo $del_req->nama_terminal_petikemas ?>, <?php echo $del_req->nama_pelabuhan ?></label>
						</div>
					</fieldset>
				</div>

			</div>

			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Delivery Order</legend>
						
                        <p><em>Periksa Kembali data delivery order anda</em></p>
                        
                        <div class="col-lg-6">
							<div class="form-group">
								<label class="col-lg-4 control-label">Shipping Line</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="kode_shipping_line"><?php echo $del_req->kode_shipping_line ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Origin / Dest</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="pol_pod"><?php echo $del_req->port_of_loading.' / '.$del_req->port_of_discharge ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Voyage / Vessel Name</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="voyage_vessel"><?php echo $del_req->voyage.' / '.$del_req->call_sign ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Arrival Date</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="arrival_date"><?php echo $del_req->tanggal_datang ?></p>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="col-lg-4 control-label">Consignee</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="consignee"><?php echo $del_req->consignee ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Nomor SPPB</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="consignee"><?php echo $del_req->nomor_sppb ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Tanggal SPPB</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="consignee"><?php echo $del_req->tanggal_sppb ? date('d-M-Y', strtotime($del_req->tanggal_sppb)) : '' ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Rencana Tanggal Ambil</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="consignee"><?php echo $del_req->rencana_ambil ? date('d-M-Y', strtotime($del_req->rencana_ambil)) : '' ?></p>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Peti Kemas</legend>
						<div class="col-lg-12">
							<div class="row">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>Nomor Peti Kemas</th>
											<th>Nomor Segel</th>
											<th>Jenis Peti Kemas</th>
											<th>Komoditas</th>
											<th>Hazard</th>
										</tr>
									</thead>
									<tbody id="container_landing">
										<?php 
										foreach($del_req->detail as $row){
										?>
										<tr>
											<td><?php echo $row->nomor_container ?></td>
											<td><?php echo $row->seal_number ?></td>
											<td><?php echo $row->kode_container ?></td>
											<td><?php echo $row->commodity ?></td>
											<td><?php echo $row->hazard ? 'Ya' : 'Tidak' ?></td>
										</tr>
										<?php
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="pull-right">
						<a href="<?php echo site_url('delivery_request_og/listview') ?>" class="btn btn-default">Kembali</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

		

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
<script type="text/javascript">

$(document).ready(function(){
	
});
</script>
</body>
</html>