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
                	<h2>Mandiri Clickpay</h2>
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
						
			<ul class="nav nav-tabs" id="payment_interface_tab">
			  <li class="active"><a href="#pembayaran">Pembayaran</a></li>
			  <li><a href="#request_data">Data Request <span class="badge" title="Jumlah Container"><?php echo count($del_req->detail) ?></span></a></li>
			  <li><a href="#invoice">Invoice</a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="pembayaran">
					<div class="row">
						<div class="col-lg-12">
							<fieldset class="delivery-request-border">
								<legend class="delivery-request-border">Pembayaran Mandiri Clickpay</legend>
								
								<div class="col-lg-6">
									<div class="form-group">
										<label class="col-lg-4 control-label">Total Tagihan</label>
										<div class="col-lg-8">
											<p class="form-control-static" id="total_tagihan">Rp. <?php echo number_format((int) $invoice->header->kredit, 0, ',', '.') ?>,-</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Biaya Transaksi</label>
										<div class="col-lg-8">
											<p class="form-control-static" id="biaya_transaksi">Rp. 2.000,-</p>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-lg-4 control-label">Total Transaksi</label>
										<div class="col-lg-8">
											<p class="form-control-static" id="total_transaksi">Rp. <?php echo number_format((int) $invoice->header->kredit + 2000, 0, ',', '.') ?>,-</p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">10 Digit Terakhir Nomor Kartu Debit</label>
										<div class="col-lg-8">
											<p class="form-control-static 10_digit_card"><em>Masukkan Nomor Kartu Terlebih Dahulu</em></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Nomor Kartu Debit</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="nomor_kartu" name="nomor_kartu" placeholder="Masukkan Nomor Kartu Debit" value="<?php echo set_value('nomor_kartu') ?>" maxlength="16" />
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Response Appli 3 Token Mandiri</label>
										<div class="col-lg-8">
											<input type="password" class="form-control" id="token_response" name="token_response" placeholder="Masukkan Nomor yang Tertera di Token" value="" />
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="sidebar info_clickpay_mandiri">
										<h4 class="sidebar row-fluid">Petunjuk Pembayaran Mandiri Clickpay</h4><br />
										<div class="item no1">
											<h5 style="height:40px">Masuk Appli 3</h5>
										</div>
										
										<div class="item no2">
											<h5 style="height:40px">Masukkan 10 Digit Terakhir No Kartu Anda (<strong><span class="10_digit_card"><em>Masukkan Nomor Kartu Debit Terlebih Dahulu</em></span></strong>)</h5>
										</div>
										
										<div class="item no3">
											<h5 style="height:40px">Masukkan Total Transaksi. (<strong><?php echo (int) $invoice->header->kredit + 2000 ?></strong>)</h5>
										</div>
										
										<div class="item no4">
											<h5 style="height:40px">Masukkan ID Transaksi (<strong><?php echo $del_req->id ?></strong>)</h5>
										</div>
										
										<div class="item no5">
											<h5 style="height:40px">Isikan nomor yang tampil di Token ke SmartCargo</h5>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
				</div>
				
				<div class="tab-pane" id="request_data">
					<div class="row">
						<div class="col-lg-12">
							<fieldset class="delivery-request-border">
								<legend class="delivery-request-border">Data Delivery Order</legend>
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
													<th>Ukuran / Tipe</th>
													<th>Komoditas</th>
													<th>Hazard</th>
													<th>Keterangan</th>
												</tr>
											</thead>
											<tbody id="container_landing">
												<?php 
												foreach($del_req->detail as $row){
												?>
												<tr>
													<td><?php echo $row->nomor_container ?></td>
													<td><?php echo $row->kode_container.' / '.$row->tipe_container  ?></td>
													<td><?php echo $row->commodity ?></td>
													<td><?php echo $row->hazard ? 'Ya' : 'Tidak' ?></td>
													<td><?php  ?></td>
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
				</div>
			
				<div class="tab-pane" id="invoice">
					<div class="row">
						<div class="col-lg-12">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>No</th>
										<th>Keterangan</th>
										<th>Qty</th>
										<th>Harga Satuan</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody id="container_landing">
									<?php 
									$i = 1;
									foreach($invoice->detail as $row){
									?>
									<tr>
										<td><?php echo $i++ ?></td>
										<td><?php echo $row->uraian  ?></td>
										<td><?php echo $row->qty ?></td>
										<td><?php echo $row->tarif ?></td>
										<td><?php echo $row->total ?></td>
									</tr>
									<?php
									}
									?>
									
									<tr>
										<td colspan="4" class="text-right">Total</td>
										<td class="text-right"><?php echo $invoice->header->kredit ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="pull-left">
					<a href="<?php echo site_url('delivery_request_og/preview_invoice/'.$del_req->id) ?>" class="btn btn-danger">Ganti Cara Pembayaran</a>
				</div>
				<div class="pull-right">
					
					<button class="btn btn-primary fr" name="kirim" type="submit" value="1">Bayar</button>
					<a href="<?php echo site_url('delivery_request_og/listview') ?>" class="btn btn-default">Kembali</a>
				</div>
			</div>
			
			
			<?php echo form_close() ?>

			<!-- Modal Dialog Deletion -->
	  		<div class="modal fade" id="myModal">
	    		<div class="modal-dialog">
	      			<div class="modal-content">
	        			<div class="modal-body">
	          				Anda ingin mengganti cara pembayaran?
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
$(document).ready(function(){
	$('#payment_interface_tab a').click(function (e) {
		e.preventDefault()
		$(this).tab('show')
	});
	
	$('#nomor_kartu').keyup(function (){
		var value = $(this).val();
		
		if(value.length == 16){
			$('.10_digit_card').html(value.substring(value.length - 10));
		}else{
			$('.10_digit_card').html('<em>Harap lengkapi nomor kartu anda</em>');
		}
	});
	$('#nomor_kartu').keyup();
});


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



</script>
</body>
</html>