<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('domestik/backend/components/header_domestik') ?>

		<div class="container">

			<div class="row">
            	<div class="col-md-8">
                	<h1>Update Password</h1>
                </div>
			</div>

			<hr />

			<form role="form" class="form-horizontal" action="" method="post">
			<div class="row">
				<input type="hidden" name="id" value="<?php echo $datasource["ID_USER"]; ?>" />

				<div class="col-lg-12">

					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								<label class="text-left">Password Lama *</label>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-3">
								<input type="password" class="form-control" name="oldpassword" placeholder="" value="" /><?php echo form_error('oldpassword', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								<label class="text-left">Password *</label>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-3">
								<input type="password" class="form-control" name="password" placeholder="" value="" /><?php echo form_error('password', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<div class="col-lg-12">
								<label class="text-left">Confirm Password *</label>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-3">
								<input type="password" class="form-control" name="passconf" placeholder="" value="" /><?php echo form_error('passconf', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>
					</div>

				</div>

			</div>
			<div class="row">
				<div class="col-lg-6">
					<p>* Mandatory fields</p>
				</div>
				<div class="col-lg-6">
					<div class="pull-right">
						<button class="btn btn-primary fr" type="submit">Simpan</button>
					</div>
				</div>
			</div>
			</form>
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('domestik/backend/elements/footer_domestik') ?>

</body>
</html>
