<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<?php
$target_url = $this->router->fetch_class().'/'.$this->router->fetch_method();
if($this->router->fetch_directory()){
	$target_url = $this->router->fetch_directory().'/'.$target_url;
}
?>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">

			<h1>Detail Truck Out</h1>
			<p class="lead">
				<small></small>
			</p>
			<hr>
			<br>

			
			<div class="row ct-listview-toolbar">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table-condensed" id="dt_tabel">
							<thead>
								<tr>
                                	<th>VISIT ID</th>
                                	<th>PLAT</th>
                                	<th>DRIVER</th>
                                	<th>VISIT STATUS</th>
                                	<th>JUMLAH VIN</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($truk_out as $to) { ?>
								<tr>
									<td><?php echo $to->VISIT_ID ?></td>
									<td><?php echo $to->PLAT ?></td>
									<td><?php echo $to->DRIVER ?></td>
									<td><?php echo $to->VISITSTATUS ?></td>
									<td>
										<a href="<?php echo site_url('DashboardReal/vin_detail_truk_out/' . $to->VISIT_ID.'/' ) ?>" class="edit_link">
										<?php echo $to->JML_VIN ?>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>

		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>

	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	
	<script type="text/javascript">
		
		let tabel = $('#dt_tabel').DataTable({
            "bProcessing": true,
            "serverSide" : false,
            "paging"     : true,
            "searching"  : true,
        });

	</script>

</body>
</html>
