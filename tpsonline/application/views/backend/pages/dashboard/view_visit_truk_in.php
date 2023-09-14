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

			<h1>Detail Visit Truck In</h1>
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
								<?php foreach ($truk_in as $ti) { ?>
								<tr>
									<td><?php echo $ti->VISIT_ID ?></td>
									<td><?php echo $ti->PLAT ?></td>
									<td><?php echo $ti->DRIVER ?></td>
									<td><?php echo $ti->VISITSTATUS ?></td>
									<td>
										<a href="<?php echo site_url('DashboardReal/vin_detail_truk_in/' . $ti->VISIT_ID.'/') ?>" class="edit_link">
										<?php echo $ti->JML_VIN ?>
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
            "lengthChange": true,
            "bFilter": true
        });

	</script>

</body>
</html>
