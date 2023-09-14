<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Data Log</h1>
			<p class="lead">
				<small></small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						
					</div>
				</div>
			</div>
			
			<hr />
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th><?php echo gridHeader('ID', 'ID', $cfg) ?></th>
									<th><?php echo gridHeader('VIN', 'VIN', $cfg) ?></th>
									<th><?php echo gridHeader('KETERANGAN', 'KETERANGAN', $cfg) ?></th>
									
								</tr>
							</thead>
							<tbody>
								<?php
								$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;
								
								if($datasource){
									foreach($datasource as $row){
								?>
								<tr>
									<td><?php echo $row->ID ?></td>
									<td><?php echo $row->VIN ?></td>
									<td><?php echo $row->KETERANGAN ?></td>
									
								</tr>
								<?php
									}
								}else{
								?>
								<tr><td colspan="9"><em>Tidak ada data</em></td></tr>
								<?php	
								}
								?>
							</tbody>
						</table>
					</div>
					
					<?php $this->load->view('backend/components/paging') ?>
				</div>
				
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
	
	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script>	</script>
</body>
</html>