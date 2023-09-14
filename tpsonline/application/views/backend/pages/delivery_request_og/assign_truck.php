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
                	<h2>Pilih Trucking</h2>
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
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="col-lg-4 control-label">Consignee</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="consignee"><?php echo $del_req->consignee ?></p>
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
						<legend class="delivery-request-border">Data Peti Kemas & Assignment</legend>
						<p><em>Isikan nomor HP Supir atau pilih dari perusahaan Trucking yang telah bekerja sama dengan kami. Gate Ticket akan dikirimkan langsung via SMS / Elektronis ke supir atau perusahaan trucking yang dipilih.</em></p>
						<p><em>Khusus untuk pembayaran delivery melalui Smartcargo, <strong>SP2 Dicetak di Terminal sebelum Gate Out</strong></em></p>
						<div class="col-lg-12">
							<div class="row">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>No Petikemas</th>
											<th>Komoditas</th>
											<th>Berat</th>
											<th>Tipe</th>
											<th>Jalur Pengiriman Tiket Gate In</th>
											<th>Pilih Perusahaan Angkutan / Nomor HP Supir</th>
										</tr>
									</thead>
									<tbody>
										<?php
										foreach($del_req->detail as $row){
										?>
										<tr>
											<td><?php echo $row->nomor_container ?></td>
											<td><?php echo $row->commodity ?></td>
											<td><?php  ?></td>
											<td><?php echo $row->ukuran_container.$row->tipe_container ?></td>
											<?php
											if($row->ocean_going_delivery_truck_assignment_id){
											?>
											<td>
												<em>Tiket sudah dikirimkan</em>
											</td>
											<td>
												<a href="<?php echo site_url('delivery_request_og/reset_security_code/'.$row->id) ?>" class="reset_code">Reset Code</a>
												<a href="<?php echo site_url('delivery_request_og/view_code/'.$row->id) ?>" class="view_code">View Code</a>
											</td>
											<?php
											}else{
											?>
											<td>
												<select class="form-control metode_kirim" name="metode_kirim[<?php echo $row->id ?>]">
													<option value="">-- Pilih --</option>
													<option value="TRUCKING_COMPANY">Diserahkan ke Perusahaan Angkutan</option>
													<option value="SMS">SMS Langsung ke Supir</option>
													<option value="RFID">Tiket RFID</option>
												</select>
											</td>
											<td>
													<input type="hidden" name="trucking_company_id[<?php echo $row->id ?>]" >
													<input type="text" class="form-control trucking-contact tval TM_TRUCKING_COMPANY" placeholder="Ketik Perusahaan Angkutan" autocomplete="off" />
													<div class="row tval TM_SMS" style="display:none">
														<div class="col-lg-6">
															<input type="text" class="form-control" name="truck_id[<?php echo $row->id ?>]" placeholder="Truck ID" />
														</div>
														<div class="col-lg-6">
															<div class="input-group">
																<span class="input-group-addon">+62</span>
																<input type="text" class="form-control" name="nomor_handphone[<?php echo $row->id ?>]" placeholder="Nomor Handphone" />
															</div>															
														</div>
													</div>
													<input type="text" class="form-control tval TM_RFID" name="truck_id[<?php echo $row->id ?>]" style="display:none" placeholder="Ketik Nomor Tiket RFID" />
												</div>
											</td>
											<?php
											}
											?>
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
						<button class="btn btn-primary fr" type="submit" name="submit">Set Trucking</button>
						<a href="<?php echo site_url('delivery_request_og/listview') ?>" class="btn btn-default">Batal</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
    <script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>

<script type="text/javascript">
	var datasource = [];
	$(document).ready(function(){
		$('.metode_kirim').change(function(){
			var metode = $(this).val();
			
			$(this).parent().parent().find('.tval').hide();
			$(this).parent().parent().find('.TM_' + metode).show();
		});
	
		$('.trucking-contact').typeahead({
			minLength: 2,
			highlighter: function(item) {
				console.log('SET');
				if (!item) {
					return "<span>No Match Found!.</span>";
				} else {
					return "<span>" + datasource[parseInt(item)].nama_perusahaan + "</span>";
				}
			},
			matcher: function(){
				return true;
			},
			updater : function(item){
				if(item){
					$(this.$element).siblings().val(item);
					return datasource[parseInt(item)].nama_perusahaan;
				}else{
					return null;
				}
			},
			source: function (query, process) {
				$(this.$element).siblings().val('');
				
				//	This is going to make an HTTP post request to the controller
				$.post(bs.siteURL + 'delivery_request_og_aux/get_trucking_company', { query: query, token : bs.token }, function (data) {
					// Cache result
					datasource = new Array();
					
					var list = [];
					for(var i = 0; i < data.datasource.length; i++){
						list.push('' + data.datasource[i].trucking_company_id);
						datasource[data.datasource[i].trucking_company_id] = data.datasource[i];
					}
					
					return process(list); 
				}, 'json');
			},
		});
	});
</script>
</body>
</html>