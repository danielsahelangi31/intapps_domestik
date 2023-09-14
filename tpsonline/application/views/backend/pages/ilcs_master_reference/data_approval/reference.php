<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/ilcs_master_reference/css/ilcs_master_reference.css') ?>" />
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<div class="row">
            	<div class="col-md-8">
                	<h2>Persetujuan Data Masuk Kapal</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>

			<?php echo form_open(null, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="fieldset-bordered">
						<legend class="fieldset-bordered">Informasi Umum</legend>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="col-lg-4 control-label">Kode Sumber</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="kode_shipping_line"></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Nama Sumber</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="pol_pod">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Waktu Masuk</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="voyage_vessel"></p>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<label class="control-label">Kode Lokal</label>
							<div style="border:1px solid #AAA; padding:10px; font-size:22px; font-weight:bold;">CAFU</div>
							
							
							<label class="control-label"><input type="checkbox"> Setujui Kode Lokal</label>
						</div>
					</fieldset>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="fieldset-bordered fieldset-nested">
						<legend class="fieldset-bordered">
							<ol class="breadcrumb">
								<li><a href="#" class="collapse_fieldset"><span class="caret"></span></a></li>
								<li><a href="#">Vessel</a></li>
							</ol>
						</legend>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="col-lg-4 control-label">IMO Number</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="kode_shipping_line"></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">MMSI</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="pol_pod">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Call Sign</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="voyage_vessel"></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Nama Kapal</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="voyage_vessel"></p>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="col-lg-4 control-label">Panjang Kapal</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="kode_shipping_line"></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Lebar Kapal</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="pol_pod">
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Dead Weight Tonnage</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="voyage_vessel"></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Bendera</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="voyage_vessel"></p>
								</div>
							</div>
						</div>
						
						<a href="#">Lihat Detail</a>
						<div class="clearfix"></div>
						<hr>
						
						<p><strong>Saran Penggabungan Data</strong></p>
						
						<button class="btn btn-primary fr" type="button" onclick="woke()">Tetapkan Sebagai Data Baru</button>
						<button class="btn btn-primary fr" type="button" data-toggle="modal" data-target="#lookup_modal">Cari Data Lain</button>
						
						<br><br>
						
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>IMO Number</th>
									<th>MMSI</th>
									<th>Call Sign</th>
									<th>Nama Kapal</th>
									<th>Shipping Line</th>
								</tr>
							</thead>
							<tbody id="container_landing">
								<tr>
									<td colspan="5" class="center">Tidak ada data yang cocok dengan data ini</td>
								</tr>
							</tbody>
						</table>
						
						
						<div class="col-lg-12 alpha beta">
							<fieldset class="fieldset-bordered fieldset-nested" id="jenis_kapal">
								<legend class="fieldset-bordered">
									<ol class="breadcrumb">
										<li><a href="#" class="collapse_fieldset"><span class="caret"></span></a></li>
										<li><a href="#">Vessel</a></li>
										<li><a href="#">Vessel Type</a></li>
									</ol>
								</legend>
								<div class="col-lg-6">
									<div class="form-group">
										<label class="col-lg-4 control-label">Jenis Kapal</label>
										<div class="col-lg-8">
											<p class="form-control-static" id="kode_shipping_line">Container Ship</p>
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									
								
								</div>
								
								<div class="clearfix"></div>
								<hr>
								
								<p><strong>Saran Penggabungan Data</strong></p>
								
								<button class="btn btn-primary fr" type="button">Tetapkan Sebagai Data Baru</button>
								<button class="btn btn-primary fr" type="button">Cari Data Lain</button>
								
								<br><br>
								
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>ID</th>
											<th>Jenis Kapal</th>
										</tr>
									</thead>
									<tbody id="container_landing">
										<tr>
											<td><label><input type="radio" name="choose_vessel_type" value="1">1</label></td>
											<td>Container Ship</td>
										</tr>
									</tbody>
								</table>
								
								<div class="col-lg-12 alpha beta">
									<fieldset class="fieldset-bordered fieldset-nested" id="jenis_kapal">
										<legend class="fieldset-bordered">
											<ol class="breadcrumb">
												<li><a href="#" class="collapse_fieldset"><span class="caret"></span></a></li>
												<li><a href="#">Vessel</a></li>
												<li><a href="#">Vessel Type</a></li>
												<li class="active">Country</li>
											</ol>
										</legend>
										<div class="col-lg-6">
											<div class="form-group">
												<label class="col-lg-4 control-label">Jenis Kapal</label>
												<div class="col-lg-8">
													<p class="form-control-static" id="kode_shipping_line">Container Ship</p>
												</div>
											</div>
										</div>
										<div class="col-lg-6">
											
										
										</div>
										
										<div class="clearfix"></div>
										<hr>
										
										<p><strong>Saran Penggabungan Data</strong></p>
										
										<button class="btn btn-primary fr" type="button">Tetapkan Sebagai Data Baru</button>
										<button class="btn btn-primary fr" type="button">Cari Data Lain</button>
										
										<br><br>
										
										<table class="table table-bordered">
											<thead>
												<tr>
													<th>ID</th>
													<th>Jenis Kapal</th>
												</tr>
											</thead>
											<tbody id="container_landing">
												<tr>
													<td><label><input type="radio" name="choose_vessel_type" value="1">1</label></td>
													<td>Container Ship</td>
												</tr>
											</tbody>
										</table>
									</fieldset>
								</div>
								
								<div class="col-lg-12 alpha beta">
									<fieldset class="fieldset-bordered fieldset-nested" id="jenis_kapal">
										<legend class="fieldset-bordered">
											<ol class="breadcrumb">
												<li><a href="#" class="collapse_fieldset"><span class="caret"></span></a></li>
												<li><a href="#">Vessel</a></li>
												<li><a href="#">Vessel Type</a></li>
												<li class="active">Country</li>
											</ol>
										</legend>
										<div class="col-lg-6">
											<div class="form-group">
												<label class="col-lg-4 control-label">Jenis Kapal</label>
												<div class="col-lg-8">
													<p class="form-control-static" id="kode_shipping_line">Container Ship</p>
												</div>
											</div>
										</div>
										<div class="col-lg-6">
											
										
										</div>
										
										<div class="clearfix"></div>
										<hr>
										
										<p><strong>Saran Penggabungan Data</strong></p>
										
										<button class="btn btn-primary fr" type="button">Tetapkan Sebagai Data Baru</button>
										<button class="btn btn-primary fr" type="button">Cari Data Lain</button>
										
										<br><br>
										
										<table class="table table-bordered">
											<thead>
												<tr>
													<th>ID</th>
													<th>Jenis Kapal</th>
												</tr>
											</thead>
											<tbody id="container_landing">
												<tr>
													<td><label><input type="radio" name="choose_vessel_type" value="1">1</label></td>
													<td>Container Ship</td>
												</tr>
											</tbody>
										</table>
									</fieldset>
								</div>
								
							</fieldset>
						</div>
						
						
					</fieldset>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="fieldset-bordered" id="comment">
						<legend class="fieldset-bordered">Berikan Komentar</legend>
						<textarea class="form-control" rows="3"></textarea>
					</fieldset>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div class="pull-right">
						<button class="btn btn-primary fr" type="submit" name="simpan_kirim" id="simpan_kirim" value="1"><span class="glyphicon glyphicon-floppy-saved"></span> Simpan</button>
						<a class="btn btn-default fr" href="<?php echo site_url('delivery_request_og/listview') ?>">Kembali</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

		</div><!-- /.container -->
	</div>
	
	<div class="imr-overlay" id="overlay_template" style="display:none">
		<div class="imr-overlay-ct imr-overlay-locked">
			<h2>Bagian ini terkunci</h2>
			<p>Anda telah memilih data dari yang telah tersedia. Tetapkan data sebagai data baru untuk melepas kunci.</p>
		</div>
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="lookup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Cari Kapal</h4>
				</div>
				<div class="modal-body">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>IMO Number</th>
								<th>MMSI</th>
								<th>Call Sign</th>
								<th>Nama Kapal</th>
								<th>Shipping Line</th>
							</tr>
						</thead>
						<tbody id="container_landing">
							<tr>
								<td colspan="5" class="center">Tidak ada data yang cocok dengan data ini</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
					<button type="button" class="btn btn-primary">Pilih</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	
    <?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/ilcs_master_reference/js/data_approval.js') ?>"></script>
   

<script type="text/javascript">
$(document).ready(function(){
	$('.collapse_fieldset').click(function(){
		var target = $(this).parent().parent().parent();
		
		if($(target).hasClass('collapsed')){
			$(target).siblings().slideDown(200);
			$(target).removeClass('collapsed');
		}else{
			$(target).siblings().slideUp(200);
			$(target).addClass('collapsed');
		}
		
		return false;
	});
});
</script>
</body>
</html>