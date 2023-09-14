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
			
			

			<fieldset class="delivery-request-border">
				<legend class="delivery-request-border">Data Pelanggan</legend>			
				<div class="row">
					
					<?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
						
						<input type="hidden" class="form-control" id="ID" name="ID" value="<?php echo $pelanggan->ID ?>" />
						<div class="form-group ui-widget">
							<div class="col-lg-1"></div>
							<label class="col-lg-4 control-label" for="ORGANIZATION">Nama Perusahaan</label>
							<div class="col-lg-7">
								<p class="form-control-static"><?php echo $pelanggan->NAMA_PERUSAHAAN ?></p>
								<input type="hidden" class="form-control" id="ORGANIZATION" name="ORGANIZATION" value="<?php echo $pelanggan->NAMA_PERUSAHAAN ?>" />
							</div>
						</div>
						
						<div class="form-group ui-widget">
							<div class="col-lg-1"></div>
							<label class="col-lg-4 control-label" for="NPWP">NPWP</label>
							<div class="col-lg-7">
								<p class="form-control-static"><?php echo $pelanggan->NPWP ?></p>
								<input type="hidden" class="form-control" id="NPWP" name="NPWP" value="<?php echo $pelanggan->NPWP ?>" />
							</div>
						</div>
						
						<div class="form-group ui-widget">
							<div class="col-lg-1"></div>
							<label class="col-lg-4 control-label" for="konfirmasi">Konfirmasi</label>
							<div class="col-lg-7">
								<p class="form-control-static"><b>Apakah Anda yakin akan menghapus data pelanggan yang tercantum</b></p>
								
							</div>
						</div>
						
						<div class="pull-right">
							<div class="btn ajax-load" id="simpan_load" style="display:none"></div>
							<a href="#" class="btn btn-warning" id="hapus">Hapus</a>
						</div>
					<?php echo form_close() ?>
					
					
				</div>
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
<script type="text/javascript">

$(document).ready(function(){
	$('#hapus').click(function(){
				
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
		
			var url = bs.siteURL + 'tps_online/pelanggan/hapus/' + bs.token;
			
			$.post(url, param, function(data){
				$('#simpan_load').hide();
				
				if(data.success){
					sc_alert('Sukses', data.msg);
					console.log(data.msg);
					window.location.replace("<?php echo site_url($grid_state) ?>");
				}else{
					sc_alert('ERROR', data.msg);
				}
			}, 'json');
		}
		
		return false;
	});
	initialize();
});
</script>
</body>
</html>