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
                	<h2>Edit Permintaan Delivery</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>
			
			<?php echo form_open(null, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
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
									<input type="text" class="form-control" id="nomor_sppb" name="nomor_sppb" placeholder="Masukkan Nomor SPPB" value="<?php echo $del_req->nomor_sppb ?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Tanggal SPPB</label>
								<div class="col-lg-8">
									<input type="text" class="form-control date" id="tanggal_sppb" name="tanggal_sppb" placeholder="Masukkan Tanggal SPPB" value="<?php echo $del_req->tanggal_sppb ? date('d-M-Y', strtotime($del_req->tanggal_sppb)) : '' ?>" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Rencana Tanggal Ambil</label>
								<div class="col-lg-8">
									<input type="text" class="form-control date" id="rencana_ambil" name="rencana_ambil" placeholder="Masukkan Tanggal Rencana Ambil" value="<?php echo $del_req->rencana_ambil ? date('d-M-Y', strtotime($del_req->rencana_ambil)) : '' ?>" />
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
						<button class="btn btn-primary fr" name="kirim" type="submit" value="1"><span class="glyphicon glyphicon-floppy-disk"></span> Simpan</button>
						<?php
						}
						?>
						<a href="<?php echo site_url('delivery_request_og/listview') ?>" class="btn btn-default">Kembali</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

			<!-- Modal -->
	  		<div class="modal fade" id="myModal">
	    		<div class="modal-dialog">
	      			<div class="modal-content">
	        			<div class="modal-body">
	          				Anda ingin membuang permintaan delivery ?
	        			</div>
	        			<div class="modal-footer">
	        				<button type="button" class="btn btn-primary" id="btn-ok">Ya</button>
	          				<button type="button" class="btn btn-default" data-dismiss="modal">Tidak</button>
	        			</div>
	      			</div><!-- /.modal-content -->
	    		</div><!-- /.modal-dialog -->
	  		</div><!-- /.modal -->

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
<script type="text/javascript">

$(document).ready(function(){
	$('#main_form').submit(function(){
		var errors = [];
		
		if(!$('#nomor_sppb').val()){
			errors.push('Nomor SPPB harus diisi');	
		}
		
		if(!$('#tanggal_sppb').val()){
			errors.push('Tanggal SPPB ambil harus diisi');	
		}
		
		if(!$('#rencana_ambil').val()){
			errors.push('Tanggal rencana ambil harus diisi');	
		}
		
		if(errors.length == 0){
			var url = bs.siteURL + 'delivery_request_og_aux/edit';
			var param = $(this).serializeArray();
			param.push({
				name : 'token',
				value : bs.token
			});
			
			$.post(url, param, function(data){
				if(data.success){
					sc_alert('Sukses', 'Sukses simpan request, silakan lanjutkan dengan pembayaran');
				}else{
					sc_alert('Error ' + data.err_code, data.msg);
				}
			}, 'json');
		}else{
			var str = '';
			for(var i = 0; i < errors.length; i++){
				str += '<p>' + errors[i] + '</p>';	
			}
			sc_alert('Harap perbaiki kesalahan berikut', str);
		}
		
		return false;
	});
});
</script>
</body>
</html>