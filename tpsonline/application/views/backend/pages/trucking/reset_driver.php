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
                	<h2>Pesanan Truck</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>

			<form role="form" class="form-horizontal" action="" method="post">
			<input type="hidden" id="id" value="1">
	
			<div class="row">
				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Dokumen</legend>
						<div class="col-lg-12">
							<label><?php echo 'nomor dokumen' ?></label><br/>
							<label><?php echo 'nomor dokumen' ?></label><br/>
						</div>
					</fieldset>
				</div>

				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Piih Supir</legend>
						<div class="col-lg-12">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td><label class="text-left">No. HP</label></td>
										<td><input type="text" class="form-control" id="driver-number" name="driver_number" ></td>
									</tr>
									<tr>
										<td><label class="text-left">Nama</label></td>
										<td><input type="text" class="form-control" id="driver-name" name="driver_name" ></td>
									</tr>
									<tr>
										<td><label class="text-left">Plat Nomor</label></td>
										<td><input type="text" class="form-control" id="number-plate" name="number_plate" ></td>
									</tr>
								</tbody>
							</table>
						</div>
					</fieldset>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="pull-right">
						<button class="btn btn-primary fr" type="submit" name="submit">Simpan</button>
						<a href="<?php echo site_url('trucking/listview') ?>" class="btn btn-default">Kembali</a>
					</div>
				</div>
			</div>
			</form>

		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>

<script src="<?php echo base_url('assets/js/typeahead.min.js') ?>"></script>
<script type="text/javascript">
	var $dataSource = [];

	$('#driver-number').typeahead({
		minLength: 3,
		updater: function (item) {
					/* do whatever you want with the selected item */
					$.post(bs.siteURL + 'trucking_aux/set_driver_detail',
							{ driver_number: item},
							function (data) {
								$('#driver-number').val(item);

								$('#driver-name').val(data.name);
								$('#driver-name').attr('disabled', 'disabled');

								$('#number-plate').val(data.number_plate);
								$('#number-plate').attr('disabled', 'disabled');
							},
							'json'
					)
		},
		highlighter: function(item) {
			//alert(item.indexOf());
			return "<span>"+item+"</span>";
		},
		source: function (query, process) {
			//	This is going to make an HTTP post request to the controller
			$dataSource = $.post(bs.siteURL + 'trucking_aux/get_trucking_contact_number', 
							{ q: query, token : bs.token }, 
							function (data) {
								return process(data.options); 
							},
							'json'
			);
			return $dataSource;
		},
	});
</script>
</body>
</html>