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
                	<h2>Lihat Data Pelanggan</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>
			
			<?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
			
			
			<?php
			if(isset($error_msg)){
			?>
			<div class="alert alert-danger fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4><?php echo isset($error_header) ? $error_header : 'Maaf Tidak Bisa Memproses Lebih Lanjut!' ?></h4>
				<p><?php echo $error_msg ?></p>
			</div>
			<?php
			}
			?>
			
			<?php
			if(isset($info_msg)){
			?>
			<div class="alert alert-success fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4><?php echo $info_msg ?></h4>
			</div>
			<?php
			}
			?>
			
			<?php echo form_open(NULL, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
			

			<fieldset class="delivery-request-border">
				<h4>Edit Pelanggan</h4>
					<?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
						
						<input type="hidden" class="form-control" id="ID" name="ID" value="<?php echo $pelanggan->ID ?>" />
						<div class="form-group ui-widget">
							<label class="col-lg-4 control-label" for="ORGANIZATION">Nama Perusahaan</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="ORGANIZATION" name="ORGANIZATION" value="<?php echo $pelanggan->NAMA_PERUSAHAAN ?>" />
							</div>
						</div>
						
						<div class="form-group ui-widget">
							<label class="col-lg-4 control-label" for="NPWP">NPWP</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="NPWP" name="NPWP" value="<?php echo $pelanggan->NPWP ?>" />
							</div>
						</div>
						
						<div class="pull-right">
							<div class="btn ajax-load" id="simpan_load" style="display:none"></div>
							<a href="#" class="btn btn-primary" id="simpan">Simpan</a>
						</div>
					<?php echo form_close() ?>
			</fieldset>
			
			
			<div class="row">
				<div class="col-lg-6">
					
				</div>
				<div class="col-lg-6">
					<div class="pull-right">
						<a href="<?php echo site_url($grid_state) ?>" class="btn btn-default">Kembali</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>
<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script>
		$(document).ready(function(){
			/**$( "#ORGANIZATION" ).keypress(function() {
				$('#ORGANIZATION').val (function () {
					return this.value.toUpperCase();
				});
			});*/
			
			function unbubble_event(){
				$(this).unbind('click');
				return false;
			}
			
			function auto_remove_popover_on_change(){
				$(this).popover('destroy');
				$(this).parent().removeClass('has-error');
				
				$(this).unbind('change', auto_remove_popover_on_change);
			}
			
			function add_validation_popover(selector, msg, position){
				if(typeof(position) === 'undefined'){
					position = 'right';
				}
			
				$(selector).popover('destroy');

				$(selector).popover({
					'content' : msg,
					'placement' : 'auto ' + position,
					'trigger' : 'focus'
				});
				
				$(selector).popover('show');
				$(selector).change(auto_remove_popover_on_change);
			}
			
			function destroy_all_validation_popovers(){
				$('.has-error').find('input, select').popover('destroy');
				$('.has-error').removeClass('has-error');
			}
			
			$("#NPWP").mask("99.999.999.9-999.999");
			
			$( "#ORGANIZATION" ).keyup(function() {
				$('#ORGANIZATION').val (function () {
					return this.value.toUpperCase();
				});
			});
			
			
			$('#simpan').click(function(){
				
				var is_error = false;
				
				var param = {
					'ID' : $('#ID').val(),
					'NAMA_PERUSAHAAN' : $('#ORGANIZATION').val(),
					'NPWP' : $('#NPWP').val()
				}
				
				
				
				
				console.log(param);
				
				if(!param.NAMA_PERUSAHAAN || param.NAMA_PERUSAHAAN == ""){
					$('#ORGANIZATION').parent().addClass('has-error');
					add_validation_popover('#ORGANIZATION', 'Nama Perusahaan Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.NPWP || param.NPWP == ""){
					$('#NPWP').parent().addClass('has-error');
					add_validation_popover('#NPWP', 'NPWP Harus diisi');
					
					is_error = true;
				}
				
				if(is_error){
					sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
				}else{		
					$('#simpan_load').show();
				
					var url = bs.siteURL + 'tps_online/pelanggan/update/' + bs.token;
					
					$.post(url, param, function(data){
						$('#simpan_load').hide();
						
						if(data.success){
							sc_alert('Sukses', data.msg);
							reset_form();
						}else{
							sc_alert('ERROR', data.msg);
						}
					}, 'json');
				}
				
				return false;
			});
			
			function reset_form(){
				$('#ORGANIZATION, #NPWP').val('');
			}
			
			initialize();
			
		});
	</script>
</body>
</html>