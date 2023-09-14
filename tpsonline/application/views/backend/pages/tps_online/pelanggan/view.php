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
				<legend class="delivery-request-border">Data Pelanggan</legend>			
				<div class="row">
					<div class="col-lg-12">
						
						<div class="form-group">
							<label class="col-lg-4 control-label">NAMA PERUSAHAAN</label>
							<div class="col-lg-8">
								<p class="form-control-static"><?php echo $pelanggan->NAMA_PERUSAHAAN ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">NPWP</label>
							<div class="col-lg-8">
								<p class="form-control-static"><?php echo $pelanggan->NPWP ?></p>
							</div>
						</div>
					</div>
					
					
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
<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
<script type="text/javascript">

$(document).ready(function(){
	
});
</script>
</body>
</html>