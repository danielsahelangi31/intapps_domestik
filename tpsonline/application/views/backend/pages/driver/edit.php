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
                	<h1>Edit Member</h1>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						<a href="<?php echo site_url('member/listview') ?>" class="btn btn-primary">Kembali</a>
					</div>
				</div>
			</div>

			<hr />

			<div class="row">
				<form role="form" class="form-horizontal" action="" method="post">
				<input type="hidden" name="id" value="<?php echo $datasource->id; ?>" />
				
				<div class="span12">
					
					<div class="form-group">
						<label class="col-lg-4 text-left">Nama Supir *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_supir" placeholder="" value="<?php echo $datasource->nama_supir; ?>" /><?php echo form_error('nama_supir', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Nomor Handphone *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nomor_handphone" placeholder="" maxlength="20" value="<?php echo $datasource->nomor_handphone; ?>" /><?php echo form_error('nomor_handphone', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Plat Nomor *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="plat_nomor" placeholder="" value="<?php echo $datasource->plat_nomor; ?>" /><?php echo form_error('plat_nomor', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<div class="col-lg-offset-4 col-lg-4">
							<button class="btn btn-primary fr" type="submit">Submit</button>
						</div>
					</div>

					<div class="col-lg-offset-4 col-lg-4">
						<p>* Mandatory fields</p>
					</div>
	
				</div>
				</form>
			</div>

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>