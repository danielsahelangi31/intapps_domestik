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

			<h1>Detail NON SPPB</h1>
			<p class="lead">
				<small></small>
			</p>
			<hr>
			<br>

			
			<div class="row ct-listview-toolbar">
				<div class="col-md-12">
					<div class="table-responsive" style="width:100%; height:430px; overflow:auto">
						<table class="table table-striped table-condensed" id="dt_tabel">
							<thead>
								<tr>
                                	<th>DTS ONTERMINAL</th>
                                	<th>VIN</th>
                                	<th>TYPE_CARGO</th>
                                	<th>LENGTH</th>
                                	<th>WIDTH</th>
                                	<th>HEIGHT</th>
                                	<th>COLOR</th>
                                	<th>MODEL NAME</th>
                                	<th>CUSTOMS NUMBER</th>
                                	<th>CUSTOMS DATE</th>
                                	<th>NAMA KAPAL</th>
                                	<th>VOYAGE IN</th>
                                	<th>TRUK PENGANGKUT</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($non_sppb as $row) { ?>
								<tr>
									<td><?php echo $row->DTS_ONTERMINAL ?></td>
									<td><?php echo $row->VIN ?></td>
									<td><?php echo $row->TYPE_CARGO ?></td>
									<td><?php echo $row->LENGTH ?></td>
									<td><?php echo $row->WIDTH ?></td>
									<td><?php echo $row->HEIGHT ?></td>
									<td><?php echo $row->COLOR ?></td>
									<td><?php echo $row->MODEL_NAME ?></td>
									<td><?php echo $row->CUSTOMS_NUMBER ?></td>
									<td><?php echo $row->CUSTOMS_DATE ?></td>
									<td><?php echo $row->NAMA_KAPAL ?></td>
									<td><?php echo $row->VOYAGE_IN ?></td>
									<td><?php echo $row->TRUK_PENGANGKUT ?></td>
									<!-- <td>
										<a href="<?php echo site_url('DashboardReal/vin_detail_truk_in/' . $row->VISIT_ID.'/') ?>" class="edit_link">
										<?php echo $row->JML_VIN ?>
									</td> -->
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
