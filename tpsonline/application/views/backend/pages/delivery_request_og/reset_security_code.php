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
                	<h2>Setel Ulang Supir</h2>
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
						<legend class="delivery-request-border">Data Dokumen</legend>
						<div class="form-group">
							<label class="col-lg-4 control-label">Nomor Petikemas</label>
							<div class="col-lg-8">
								<p class="form-control-static"><?php echo $trucking_request->container_number ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Jenis Petikemas</label>
							<div class="col-lg-8">
								<p class="form-control-static"><?php echo $trucking_request->iso_code ? $trucking_request->iso_code : $trucking_request->container_size.$trucking_request->container_type ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Barang</label>
							<div class="col-lg-8">
								<p class="form-control-static"><?php echo $trucking_request->commodity ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Nomor Segel</label>
							<div class="col-lg-8">
								<p class="form-control-static"><?php echo $trucking_request->seal_number ? $trucking_request->seal_number : '-' ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Terminal</label>
							<div class="col-lg-8">
								<p class="form-control-static"><?php echo $trucking_request->nama_terminal_petikemas.'/'.$trucking_request->nama_pelabuhan ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Rencana Tanggal Ambil</label>
							<div class="col-lg-8">
								<p class="form-control-static"><?php echo date('d-F-Y', strtotime($trucking_request->rencana_ambil)) ?></p>
							</div>
						</div>
					</fieldset>
				</div>

				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Piih Supir</legend>
						<div class="col-lg-12">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td><label class="text-left">Nama</label></td>
										<td><input type="text" class="form-control" id="nama_supir" name="nama_supir" autocomplete="off" value="<?php echo post('nama_supir') ?>" ></td>
									</tr>
									<tr>
										<td><label class="text-left">Truck ID / No. Polisi</label></td>
										<td><input type="text" class="form-control" id="truck_id" name="truck_id" value="<?php echo post('truck_id') ?>" ></td>
									</tr>
									<tr>
										<td><label class="text-left">No. HP</label></td>
										<td>
											<div class="input-group">
												<span class="input-group-addon">+62</span>
												<input type="text" class="form-control" id="nomor_handphone" name="nomor_handphone" value="<?php echo post('nomor_handphone') ?>" />
											</div>
											<em>Tanpa angka 0 didepan atau +62<br />GSM: 81312341234<br />CDMA: 217311234</em>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</fieldset>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="pull-right">
						<button class="btn btn-primary fr" type="submit" name="submit">Simpan</button>
						<a href="<?php echo site_url('trucking/listview') ?>" class="btn btn-default">Kembali</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>

<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
<script type="text/javascript">
	$(document).ready(function(){	
		var datasource = [];
	
		$('#nama_supir').typeahead({
			minLength: 2,
			highlighter: function(item) {
				if (!item) {
					return "<span>No Match Found!.</span>";
				} else {
					var rec = datasource[parseInt(item)];
					return "<span><strong>" + rec.nama_supir + "</strong><br />" + rec.nomor_handphone + ", " + rec.plat_nomor + "</span>";
				}
			},
			matcher: function(){
				return true;
			},
			updater : function(item){
				if(item){
					var rec = datasource[parseInt(item)];
					
					$('#truck_id').val(rec.plat_nomor);
					
					var no_hp = rec.nomor_handphone;
					if(no_hp){
						if(no_hp[0] == '0'){
							no_hp = no_hp.substring(1);
						}else{
							no_hp = no_hp.replace('+62');
						}
					}
					
					$('#nomor_handphone').val(no_hp);
					
					return rec.nama_supir;
				}else{
					return null;
				}
			},
			source: function (query, process) {				
				//	This is going to make an HTTP post request to the controller
				$.post(bs.siteURL + 'trucking_aux/get_driver', { query: query, token : bs.token }, function (data) {
					// Cache result
					datasource = new Array();
					
					var list = [];
					for(var i = 0; i < data.datasource.length; i++){
						list.push('' + data.datasource[i].id);
						datasource[data.datasource[i].id] = data.datasource[i];
					}
					
					return process(list); 
				}, 'json');
			},
		});
	});
</script>
</body>
</html>