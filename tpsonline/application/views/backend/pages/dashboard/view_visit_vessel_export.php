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

			<h1>Detail Vessel In</h1>
			<p class="lead">
				<small></small>
			</p>

			
			<div class="row ct-listview-toolbar">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table-condensed" id="dt_tabel">
							<thead>
								<tr>
									<th>ETA</th>
                                	<th>VISIT ID</th>
                                	<th>NAMA KAPAL</th>
                                	<th>VOYAGE IN</th>
                                	<th>VOYAGE OUT</th>
                                	<th>VISSIT STATUS</th>
                                	<th>JUMLAH VIN</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($visit_vessel_export as $row) { ?>
								<tr>
									<td><?php echo $row->ETA ?></td>
									<td><?php echo $row->VISIT_ID ?></td>
									<td><?php echo $row->NM_KAPAL ?></td>
									<td><?php echo $row->VOY_IN ?></td>
									<td><?php echo $row->VOY_OUT ?></td>
									<td><?= $row->VISITSTATUS ?></td>
									<td>
										<a href="<?php echo site_url('DashboardReal/vin_detail_vessel_in/' . $row->VISIT_ID.'/') ?>" class="edit_link">
										<?= $row->JML_VIN ?>
											
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
