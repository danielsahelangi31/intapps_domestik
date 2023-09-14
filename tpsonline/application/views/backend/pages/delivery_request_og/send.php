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
                	<h2>Kirim Request Delivery</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>
			
			<?php
			if(isset($info_msg)){
			?>
			<div class="alert alert-success"><?php echo $info_msg ?></div>
			<?php
			}
			
			if(isset($error_msg)){
			?>
			<div class="alert alert-danger"><strong>Error!</strong><br /><?php echo $error_msg ?></div>
			<?php
			}
			?>
			
			<?php echo form_open(null, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
			<input type="hidden" id="id" value="1">

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
									<p class="form-control-static" id="nomor_sppb"><?php echo $del_req->nomor_sppb ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Tanggal SPPB</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="tanggal_sppb"><?php echo $del_req->tanggal_sppb ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Rencana Tanggal Ambil</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="rencana_ambil"><?php echo date('d-M-Y', strtotime($del_req->rencana_ambil)) ?></p>
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
							<div class="row">
								<div class="pull-left">
									<?php
									if(!$del_req->status_kirim){
									?>
									<a data-toggle="modal" href="#myModal" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Buang Permintaan Delivery</a>
									<?php
									}else{
									?>
									<p>Permintaan Delivery sudah dikirim ke Terminal. <a href="<?php echo site_url('delivery_request_og/preview_invoice/'.$del_req->id) ?>">Lihat Proforma / Invoice.</a></p>
									<?php
									}
									?>
								</div>
								<div class="pull-right">
									<?php
									if(!$del_req->status_kirim){
									?>
									<button class="btn btn-primary fr" name="kirim" type="submit" value="1"><span class="glyphicon glyphicon-floppy-saved"></span> Kirim</button>
									<?php
									}
									?>
									<a href="<?php echo site_url('delivery_request_og/listview') ?>" class="btn btn-default">Kembali</a>
								</div>
							</div>
						</div>
						
					</fieldset>
				</div>
			</div>
			<?php echo form_close() ?>

			<!-- Modal Dialog Deletion -->
	  		<div class="modal fade" id="myModal">
	    		<div class="modal-dialog">
	      			<div class="modal-content">
	        			<div class="modal-body">
	          				Anda ingin membuang permintaan delivery ?
	        			</div>
	        			<div class="modal-footer">
	        				<button type="button" class="btn btn-primary" id="btn-deletion-ok">Ok</button>
	          				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        			</div>
	      			</div><!-- /.modal-content -->
	    		</div><!-- /.modal-dialog -->
	  		</div><!-- /.modal -->

	  		<!-- Modal Dialog Send Success -->
	  		<div class="modal fade" id="send-success-dialog">
	    		<div class="modal-dialog">
	      			<div class="modal-content">
	      				<div class="modal-header">
	      					Kirim Data Sukses
	      				</div>	
	        			<div class="modal-body">
	        				<p>Data Berhasil Dikirim</p>
	        				<p>Anda ingin melihat proforma ?</p>
	        			</div>
	        			<div class="modal-footer">
	        				<button type="button" class="btn btn-primary" id="btn-step-to-proforma">Ya</button>
	        				<button type="button" class="btn btn-primary" id="btn-step-to-listview">Tidak, kembali ke Daftar Delivery</button>
	        			</div>
	      			</div><!-- /.modal-content -->
	    		</div><!-- /.modal-dialog -->
	  		</div><!-- /.modal -->

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">
$('#btn-deletion-ok').on('click', function(e) {
    $.post('<?php echo site_url("delivery_request_og_aux/delete")?>', 
		{
			id : $('#id').val()
		},
		function(data) {
			if(data.status){
				$("#myModal").modal('hide'); 
				setTimeout("window.parent.location.href = '<?php echo site_url("delivery_request_og/listview")?>'",400);
			}else{}
		},
		'JSON'
	);
});

$('#btn-step-to-proforma').on('click', function(e) {
    window.parent.location.href = '<?php echo site_url("delivery_request_og/preview_invoice/1")?>';
});

$('#btn-step-to-listview').on('click', function(e) {
    window.parent.location.href = '<?php echo site_url("delivery_request_og/listview")?>';
});

function Kirim()
{
	$.post('<?php echo site_url("delivery_request_og_aux/kirim")?>', function(res) {
		if (res) {
		    $('#send-success-dialog').modal('show');
		};
	});
}
</script>
</body>
</html>