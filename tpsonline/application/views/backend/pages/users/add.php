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
					<h1>User Baru</h1>
				</div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						<a href="<?php echo site_url('member/listview') ?>"
							class="btn btn-primary">Kembali</a>
					</div>
				</div>
			</div>

			<hr />

			<div class="row">
				<form role="form" class="form-horizontal" action="" method="post">
					<div class="col-lg-6">

						<div class="form-group">
							<div class="row col-lg-4">
								<label class="text-left">Username *</label>
							</div>
							<div class="row col-lg-8">
								<input type="text" class="form-control" id="username"
									name="username" placeholder=""
									value="<?php echo set_value('username'); ?>" />
									<?php echo form_error('username', '<div class="error">', '</div><br/>'); ?>
								<div class="error"></div>
							</div>
						</div>

						<div class="form-group">
							<div class="row col-lg-4">
								<label class="text-left">Password *</label>
							</div>
							<div class="row col-lg-8">
								<input type="password" class="form-control" name="password"
									placeholder="" value="" />
									<?php echo form_error('password', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="row col-lg-4">
								<label class="text-left">Confirm Password *</label>
							</div>
							<div class="row col-lg-8">
								<input type="password" class="form-control" name="passconf"
									placeholder="" value="" />
									<?php echo form_error('passconf', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="row col-lg-4">
								<label class="text-left">Perusahaan Member *</label>
							</div>
							<div class="row col-lg-8">
								<input type="text" class="form-control" id="member-select"
									name="member_select"> <input type="hidden" id="member-id"
									name="member_id">
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<div class="row col-lg-4">
								<label class="text-left">Full Name *</label>
							</div>
							<div class="row col-lg-8">
								<input type="text" class="form-control" name="nama_lengkap"
									placeholder="" value="<?php echo set_value('nama_lengkap'); ?>" />
									<?php echo form_error('nama_lengkap', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="row col-lg-4">
								<label class="text-left">Mobile phone *</label>
							</div>
							<div class="row col-lg-8">
								<input type="text" class="form-control" name="handphone"
									placeholder="" value="<?php echo set_value('handphone'); ?>" />
									<?php echo form_error('handphone', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="row col-lg-4">
								<label class="text-left">Phone</label>
							</div>
							<div class="row col-lg-8">
								<input type="text" class="form-control" name="telepon"
									placeholder="" value="<?php echo set_value('telepon'); ?>" />
									<?php echo form_error('telepon', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="row col-lg-4">
								<label class="text-left">Email *</label>
							</div>
							<div class="row col-lg-8">
								<input type="email" class="form-control" name="email"
									placeholder="" value="<?php echo set_value('email'); ?>" />
									<?php echo form_error('email', '<div class="error">', '</div><br/>'); ?>
							</div>
						</div>
					</div>
					<p>* Mandatory fields</p>
					<div class="form-group pull-right">
						<button class="btn btn-primary fr" type="submit">Simpan</button>
						<a class="btn btn-default fr"
							href="<?php echo site_url('users/listview') ?>">Kembali</a>
					</div>
				</form>
			</div>

		</div>
		<!-- /.container -->
	</div>

	<?php $this->load->view('backend/elements/footer') ?>
	<script src="<?php echo base_url('assets/js/typeahead.min.js') ?>"></script>
	<script type="text/javascript">
	$('#member-select').typeahead({
		minLength: 3,
		updater: function (item) {
					//do whatever you want with the selected item
					var selectedId = item.replace(/([a-zA-Z\s.,-]+)([:]{1})([\d])$/i, '$3');
					var selectedItem = item.replace(/([a-zA-Z\s.,-]+)([:]{1})([\d])$/i, '$1');
					$('#member-id').val(selectedId); 
					return selectedItem;
		},
		highlighter: function(item) {
			/*var obj = JSON.parse(item);
			alert( obj.data[0]);*/
			
			/*var query = this.query.replace(/[a-zA-Z\s]{}()*+?.,\\\^$|#\s]/g, '\\$&')
			return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
				return "<div style='width:315px'>"+match+"</div>";
	        })*/

			//return item.replace(/([a-zA-Z\s.,]+)([:]{1})([\d])$/i, '$1$2$3');
			return item.replace(/([a-zA-Z\s.,-]+)([:]{1})([\d])$/i, "<div style='width:315px'>$1</div>");
		},
		source: function (query, process) {
			return $.post(bs.siteURL + 'users_aux/get_company', 
							{ q: query, token : bs.token }, 
							function (data) {
								return process(data.options); 
							},
							'json'
			);
		},
	});

	$('#username').typeahead({
		minLength: 3,
		updater: function (item) {
					//do whatever you want with the selected item
					/*var selectedId = item.replace(/([a-zA-Z\s.,-]+)([:]{1})([\d])$/i, '$3');
					var selectedItem = item.replace(/([a-zA-Z\s.,-]+)([:]{1})([\d])$/i, '$1');
					$('#member-id').val(selectedId); */
					$('.error').val(item);
					//return selectedItem;
		},
		highlighter: function(item) {
			return item.replace(/([a-zA-Z\s.,-]+)([:]{1})([\d])$/i, "<div style='width:315px'>$1</div>");
		},
		source: function (query, process) {
			return $.post(bs.siteURL + 'users_aux/check_username', 
							{ q: query, token : bs.token }, 
							function (data) {
								return process(data.options); 
							},
							'json'
			);
		},
	});
</script>

</body>
</html>
