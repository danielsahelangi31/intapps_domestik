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
				
				<div class="col-lg-6">
					
					<div class="form-group">
						<label class="col-lg-4 text-left">Company Name *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_perusahaan" placeholder="" value="<?php echo $datasource->nama_perusahaan; ?>" /><?php echo form_error('nama_perusahaan', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Tax Reference (NPWP) *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="npwp" placeholder="" maxlength="20" value="<?php echo $datasource->npwp; ?>" /><?php echo form_error('npwp', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Phone *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="com_telepon" placeholder="" value="<?php echo $datasource->com_telepon; ?>" /><?php echo form_error('com_telepon', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Fax</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="fax" placeholder="" value="<?php echo $datasource->fax; ?>" />
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Business Type *</label>
						<div class="col-lg-8">
							<select class="form-control" name="membership_type" disabled>
								<option value="">-- Select --</option>
								<option value="FREIGHT_FORWARDER" <?php if( !empty($datasource->freight_id) ) echo 'selected'?> >Freight Forwarder</option>
								<option value="TRUCKING_COMPANY" <?php if( !empty($datasource->trucking_id) ) echo 'selected'?> >Trucking Company</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-4 text-left">Address</label>
						<div class="col-lg-8">
							<textarea class="form-control" name="alamat" rows="4"><?php echo $datasource->alamat; ?></textarea>
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
	
				<div class="col-lg-6">
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Username *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="username" placeholder="" value="<?php echo $datasource->username; ?>" /><?php echo form_error('username', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>

					<hr class="fancy" />

					<div class="form-group">
						<label class="col-lg-4 text-left">Full Name *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_lengkap" placeholder="" value="<?php echo $datasource->nama_lengkap; ?>" /><?php echo form_error('nama_lengkap', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-4 text-left">Mobile phone *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="handphone" placeholder="" value="<?php echo $datasource->handphone; ?>" /><?php echo form_error('handphone', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Phone</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="telepon" placeholder="" value="<?php echo $datasource->telepon; ?>" /><?php echo form_error('telepon', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-lg-4 text-left">Email *</label>
						<div class="col-lg-8">
							<input type="email" class="form-control" name="email" placeholder="" value="<?php echo $datasource->email; ?>" /><?php echo form_error('email', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
				</div>
				</form>
			</div>

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>