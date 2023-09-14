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
									<p class="form-control-static"><?php echo $approval_data->partner_code ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Nama Sumber</label>
								<div class="col-lg-8">
									<p class="form-control-static"><?php echo $approval_data->partner_name ?></p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Waktu Masuk</label>
								<div class="col-lg-8">
									<p class="form-control-static"><?php echo date('Y-m-d H:i:s', strtotime($approval_data->receipt_time)) ?></p>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<label class="control-label">Kode Lokal</label>
							<div style="border:1px solid #AAA; padding:10px; font-size:22px; font-weight:bold; border-radius:4px;">CAFU</div>
							
							
							<label class="control-label"><input type="checkbox"> Setujui Kode Lokal</label>
						</div>
					</fieldset>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<?php 
					$wokedah = 'SIP';
					if(is_object($data)){
						$view = array(
							'obj' => $data,
							'field_name' => '',
							'trace' => array()
						);
						$this->load->view('backend/pages/ilcs_master_reference/data_approval/data_form/'.get_class($data), $view);
					}
					?>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="fieldset-bordered" id="comment">
						<legend class="fieldset-bordered">Tindakan</legend>
						<select class="form-control" name="">
							<option value="">-- Pilih --</option>
							<option value="APPROVE">Setuju</option>
							<option value="REJECT">Tolak</option>
						</select>
					</fieldset>
				</div>
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
						<a class="btn btn-default fr" href="<?php echo site_url('ilcs_master_reference/data_approval/listview') ?>">Kembali</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

		</div><!-- /.container -->
	</div>
	
	<!-- Overlay -->
	<div class="imr-overlay" id="overlay_template" style="display:none">
		<div class="imr-overlay-ct imr-overlay-locked">
			<h2>Bagian ini terkunci</h2>
			<p>Anda telah memilih data dari yang telah tersedia. Tetapkan data sebagai data baru untuk melepas kunci.</p>
		</div>
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="lookup_modal" tabindex="-1" role="dialog" aria-labelledby="lookup_label" aria-hidden="true">
		<div class="modal-dialog"  style="width:80%">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="myModalLabel">Cari Data</h4>
				</div>
				<div class="modal-body">
					<div class="section">
						<p class="section-header"><strong><a href="#" class="section_toggle"><span class="caret"></span></a> Parameter Pencarian:</strong></p>
						
						<div>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>No</th>
											<th>Kolom</th>
											<th>Negasi</th>
											<th>Operator</th>
											<th>Kata Kunci</th>
											<th>&nbsp;</th>
										</tr>
									</thead>
									<tbody id="lookup_parameter_landing">
										<tr class="lookup_reference">
											<td class="lookup_parameter_number">1</td>
											<td>
												<select class="lookup_field form-control">
													<option value="field_name">field_label</option>
												</select>
											</td>
											<td><label><input type="checkbox" class="negation" value="1"> Tidak</label></td>
											<td>
												<select class="lookup_operator form-control">
													<option value="equal">Tepat Sama</option>
													<option value="contain">Mengandung</option>
													<option value="greater_than">Lebih Besar</option>
													<option value="less_than">Lebih Kecil</option>
													<option value="equal_greater_than">Sama atau Lebih Besar</option>
													<option value="equal_less_than">Sama atau Lebih Kecil</option>
												</select>
											</td>
											<td><input type="text" class="lookup_keyword form-control" /></td>
											<td><button type="button" class="close" aria-hidden="true">&times;</button></td>
										</tr>
									</tbody>
								</table>
							</div>
							
							<div class="form-inline">
								<div class="pull-left">
									<td colspan="4"><button class="btn btn-primary" type="button" id="lookup_add_param">Tambah Parameter</button></td>
								</div>
								
								<div class="pull-right">
									<div class="form-group">
										<select class="lookup_fields_relation form-control">
											<option value="AND">Semua Parameter Harus Terpenuhi</option>
											<option value="OR">Salah Satu Parameter Harus Terpenuhi</option>
										</select>
									</div>
									<div class="form-group">
										<button class="btn btn-primary" type="button" id="lookup_advance_search">Cari</button>
									</div>
								</div>
								
								<div class="clearfix margin-bottom"></div>
							</div>
						</div>
					</div>
					
					<hr />
					
					<div class="section">
						<p class="section-header"><strong><a href="#" class="section_toggle"><span class="caret"></span></a> Hasil Pencarian:</strong></p>
						<div>
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr id="lookup_header">
											<th>IMO Number</th>
											<th>MMSI</th>
											<th>Call Sign</th>
											<th>Nama Kapal</th>
											<th>Shipping Line</th>
										</tr>
									</thead>
									<tbody id="container_landing">
										<tr>
											<td colspan="5" class="center">Tidak ada data yang dapat ditampilkan</td>
										</tr>
									</tbody>
								</table>
							</div>
							
							<ul class="pagination pull-right remove-margin-top">
								<li><a href="#">&laquo;</a></li>
								<li><a href="#">1</a></li>
								<li><a href="#">2</a></li>
								<li><a href="#">3</a></li>
								<li><a href="#">4</a></li>
								<li><a href="#">5</a></li>
								<li><a href="#">&raquo;</a></li>
							</ul>
							
							<div class="clearfix"></div>
						</div>
					</div>
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
<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/ilcs_master_reference/js/data_approval.js') ?>"></script>

<script type="text/javascript">
$(document).ready(function(){
	$('.show_lookup').click(function(){
		var url = bs.baseURL + 'ilcs_master_reference/data_approval/get_lookup_config';
		var entity = $(this).attr('name');
		var param = {
			entity : entity
		};
		
		$.post(url, param, function(data){
			if(data.success){
				$('#lookup_header').html('');
				
				for(var field in data.fields){
					
					var field_label = data.fields[field];
					var elStr = '<th>' + field_label + '</th>';
					var el = $(elStr);
					$('#lookup_header').append(el);
				}
				
				$('#lookup_modal').modal('show');
			}else{
				alert('Lagi brekele bos!');
			}
		}, 'json');
		
		
	});

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
	
	$('.section_toggle').click(function(){
		var target = $(this).parents('.section-header');
		
		if($(target).hasClass('collapsed')){
			$(target).siblings().slideDown(200);
			$(target).removeClass('collapsed');
		}else{
			$(target).siblings().slideUp(200);
			$(target).addClass('collapsed');
		}
		
		return false;
	});
	
	$('.goto_form').click(function(){
		var target = $($(this).attr('href'));
		
		$.scrollTo(target, 400, {offset: 0 - $('.navbar').outerHeight() - 20});
		
		return false;
	});
	
	$('.detail_toggle').click(function(){
		if($(this).hasClass('active')){
			$(this).removeClass('active').siblings('.detail_data').slideUp(200);
			$(this).html('Lihat Detail');
		}else{
			$(this).addClass('active').siblings('.detail_data').slideDown(200);
			$(this).html('Sembunyikan Detail');
		}
		
		return false;
	});
	
	$('.new_data').click(function(){
		$(this).siblings('.table-grid').find('input:checked').removeAttr('checked');
	
		var target = $('#active-overlay');
		if(target.length){
			 // Exit Transition
			$(target).fadeOut(200).find('.imr-overlay-ct').animate({
				left : $(target).outerWidth(),
				width : 0
			}, 200, function(){
				$(this).parent().remove();
			});
		}
	});
	
	$('.data_join_select').change(function(){
		if($(this).is(':checked')){
			if($('#active-overlay').length == 0){
				// Enter Transition
				var target = $(this).parents('.data-form').find('.parents-ct');
				
				if(target.length){
					var tpl = $('#overlay_template').clone().attr('id', 'active-overlay');
					
					$(tpl).find('.imr-overlay-ct').css({
						width : $(target).outerWidth() - 20
					});
					
					$(tpl).css({
						position : 'absolute',
						overflow : 'hidden',
						top : $(target).offset().top,
						left : $(target).offset().left,
						width : $(target).outerWidth(),
						height : $(target).outerHeight(),
					}).appendTo('body').find('.imr-overlay-ct').css({
						position : 'relative',
						left : 0 - $(target).outerWidth(),
						opacity : 0
					}).animate({
						opacity : 1,
						left : 0
					}, 200, 'swing');
					
					$(tpl).fadeIn(100);
				}
			}
		}
	});
});
</script>
</body>
</html>