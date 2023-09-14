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
                	<h1>Member Baru</h1>
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
				<div class="col-lg-6">
					<div class="form-group">
						<label class="col-lg-4 text-left">Company Name *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_perusahaan" placeholder="" value="<?php echo set_value('nama_perusahaan'); ?>" /><?php echo form_error('nama_perusahaan', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Tax Reference (NPWP) *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="npwp" placeholder="" maxlength="20" value="<?php echo set_value('npwp'); ?>" /><?php echo form_error('npwp', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Phone *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="com_telepon" placeholder="" value="<?php echo set_value('com_telepon'); ?>" /><?php echo form_error('com_telepon', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Fax</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="com_fax" placeholder="" value="<?php echo set_value('com_fax'); ?>" />
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Business Type *</label>
						<div class="col-lg-8">
							<select class="form-control" name="membership_type">
								<option value="">-- Select --</option>
								<option value="FREIGHT_FORWARDER" >Freight Forwarder</option>
								<option value="TRUCKING_COMPANY" >Trucking Company</option>
							</select>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Address</label>
						<div class="col-lg-8">
							<textarea class="form-control" name="com_alamat" rows="4"><?php echo set_value('com_alamat'); ?></textarea>
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
							<input type="text" class="form-control" name="username" id="username" placeholder="" value="<?php echo set_value('username'); ?>" /><?php echo form_error('username', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
						<!-- input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email"-->
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Password *</label>
						<div class="col-lg-8">
							<input type="password" class="form-control" name="password" placeholder="" value="" /><?php echo form_error('password', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Confirm Password *</label>
						<div class="col-lg-8">
							<input type="password" class="form-control" name="passconf" placeholder="" value="" /><?php echo form_error('passconf', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
						<hr class="fancy" />
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Full Name *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="nama_lengkap" placeholder="" value="<?php echo set_value('nama_lengkap'); ?>" /><?php echo form_error('nama_lengkap', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Mobile phone *</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="cp_handphone" placeholder="" value="<?php echo set_value('cp_handphone'); ?>" /><?php echo form_error('cp_handphone', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
	
					<div class="form-group">
						<label class="col-lg-4 text-left">Phone</label>
						<div class="col-lg-8">
							<input type="text" class="form-control" name="cp_telepon" placeholder="" value="<?php echo set_value('cp_telepon'); ?>" /><?php echo form_error('cp_telepon', '<div class="error">', '</div><br/>'); ?>
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-lg-4 text-left">Email *</label>
						<div class="col-lg-8">
							<input type="email" class="form-control" name="cp_email" placeholder="" value="<?php echo set_value('cp_email'); ?>" /><?php echo form_error('cp_email', '<div class="error">', '</div><br/>'); ?>
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