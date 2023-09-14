<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/tablesorter/style.css') ?>" />
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Realisasi Bongkar / Muat</h1>
			<p class="lead">
				<small></small>
			</p>
						
			<hr />
			
			<?php
			if(isset($success_msg)){
			?>
			<div class="alert alert-success fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Sukses</h4>
				<p><?php echo $success_msg ?></p>
			</div>
			<?php
			}
			?>
			
			<?php
			if(isset($error_msg)){
			?>
			<div class="alert alert-danger fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4>Error</h4>
				<p><?php echo $error_msg ?></p>
			</div>
			<?php
			}
			?>
						
			<?php
			echo form_open(null, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal'));
			?>
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						
						<legend class="delivery-request-border">Data Kapal</legend>
						<div class="form-group">
							<label class="col-lg-4 control-label">Visit ID</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="visit_id" name="visit_id" placeholder="Isikan Visit ID" value="<?php echo htmlspecialchars(post('visit_id')) ?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-lg-12">
								<div class="pull-right">
									<button class="btn btn-primary fr" type="submit" name="simpan" id="simpan" value="1" data-loading-text="Silakan Tunggu ..."><span class="glyphicon glyphicon-cloud-download"></span> Lihat Data</button>
								</div>
							</div>
						</div>		
					</fieldset>
				</div>
			</div>
			<?php
			echo form_close();
			?>
			
			<?php
			if(isset($datasource)){
				echo form_open(null, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal'));
			?>
			<input type="hidden" name="visit_id" value="<?php echo $datasource->VISIT_ID ?>" />
			<div class="row">
				<div class="col-lg-12">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#overview" data-toggle="tab">Informasi Umum</a></li>
						<?php
						if($datasource->IS_VALID == 'Y'){
							$total_import = 0;
							$total_export = 0;
							
							foreach($datasource->DETAIL->DOCUMENT as $doc){
								if($doc->DIRECTION == 'I') $total_import++;
								else $total_export++;
							}
						
						?>
						<li><a href="#detail_import" data-toggle="tab">Detail Import <span class="badge"><?php echo $total_import ?></span></a></li>
						<li><a href="#detail_export" data-toggle="tab">Detail Export <span class="badge"><?php echo $total_export ?></span></a></li>
						<?php
						}
						?>
					</ul>
					
					<!-- Tab panes -->
					<div class="tab-content">
						<div class="tab-pane fade in active" id="overview">
							<div class="row">
								<div class="col-lg-6">
									<!-- LEFT SIDE -->
									
									<div class="form-group">
										<label class="col-lg-4 control-label">Visit ID</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->VISIT_ID ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Visit Name</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->VISIT_NAME ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Vessel Status</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->VESSEL_STATUS ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Vessel Code</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->VESSEL_CODE ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Voyage In</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->VOYAGE_IN ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Voyage Out</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->VOYAGE_OUT ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">ETA</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->ETA ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">ETD</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->ETD ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Arrival</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->ARRIVAL ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Operational</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->OPERATIONAL ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Completion</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->COMPLETION ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Departure</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->DEPARTURE ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Owner Code</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->OWNER_CODE ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Agent Code</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->AGENT_CODE ?></p>
										</div>
									</div>
									<!--<div class="form-group">
										<label class="col-lg-4 control-label">Position Code</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->POSITION_CODE ?></p>
										</div>
									</div>
									<div class="form-group">
										<label class="col-lg-4 control-label">Next Port</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php //echo $datasource->NEXT_PORT ?></p>
										</div>
									</div>-->
									<div class="form-group">
										<label class="col-lg-4 control-label">Draft</label>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $datasource->DRAFT ?></p>
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<!-- RIGHT SIDE -->	
								</div>
							</div>
						</div>
						
						<?php
						if($datasource->IS_VALID == 'Y'){
						?>
						<div class="tab-pane fade in" id="detail_import">
							<!-- DETAIL IMPORT -->
							<div class="row table-wide">
								<table class="table table-bordered table-hover tablesorter">
									<thead>
										<tr>
											<th>No</th>
											<th>RSI ID</th>
											<th>Merk / Type</th>
											<th>Consignee</th>
											<th>Logistik</th>
											<th>Jenis</th>
											<th>Cargo Toyota</th>
											<th>Unit</th>
											<th>M<sup>3</sup></th>
											<th>Ton</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$i = 1;
										foreach($datasource->DETAIL->DOCUMENT as $doc){
											if($doc->DIRECTION == 'I'){
										?>
										<tr>
											<td><?php echo $i++ ?></td>
											<td><?php echo $doc->NO_BOOKING ?></td>
											<td><?php echo $doc->KD_CARGO ?></td>
											<td><?php echo $doc->CONSIGNEE_ID_EXTREF ?></td>
											<td><?php echo $doc->LOGISTIC_COMPANY_EXTREF ?></td>
											<td><?php echo $doc->KD_CARGO ?></td>
											<td><?php echo $doc->CARGO_TOYOTA ?></td>
											<td><?php echo $doc->SUM_VIN ?></td>
											<td><?php echo $doc->SUM_M3 ?></td>
											<td><?php echo $doc->SUM_TONNAGE ?></td>											
										</tr>
										<?php
											}
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="tab-pane fade in" id="detail_export">
							<!-- DETAIL EXPORT -->
							<div class="row table-wide">
								<table class="table table-bordered table-hover tablesorter">
									<thead>
										<tr>
											<th>No</th>
											<th>RSI ID</th>
											<th>Merk / Type</th>
											<th>Consignee</th>
											<th>Logistik</th>
											<th>Jenis</th>
											<th>Cargo Toyota</th>
											<th>Unit</th>
											<th>M<sup>3</sup></th>
											<th>Ton</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$i = 1;
										foreach($datasource->DETAIL->DOCUMENT as $doc){
											if($doc->DIRECTION == 'E'){
										?>
										<tr>
											<td><?php echo $i++ ?></td>
											<td><?php echo $doc->NO_BOOKING ?></td>
											<td><?php echo $doc->KD_CARGO ?></td>
											<td><?php echo $doc->CONSIGNEE_ID_EXTREF ?></td>
											<td><?php echo $doc->LOGISTIC_COMPANY_EXTREF ?></td>
											<td><?php echo $doc->KD_CARGO ?></td>
											<td><?php echo $doc->CARGO_TOYOTA ?></td>
											<td><?php echo $doc->SUM_VIN ?></td>
											<td><?php echo $doc->SUM_M3 ?></td>
											<td><?php echo $doc->SUM_TONNAGE ?></td>											
										</tr>
										<?php
											}
										}
										?>
									</tbody>
								</table>
							</div>
						</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			
			<?php
				if($datasource->IS_VALID == 'Y'){
			?>
			<div class="row">
				<div class="col-lg-12">
					<div class="pull-right">
						<button class="btn btn-success fr" type="submit" name="approve" id="approve" value="1" data-loading-text="Silakan Tunggu ..."><span class="glyphicon glyphicon-ok"></span> Approve & Transfer</button>
						<button class="btn btn-danger fr" type="submit" name="reject" id="reject" value="1" data-loading-text="Silakan Tunggu ..."><span class="glyphicon glyphicon-remove"></span> Reject</button>
					</div>
				</div>						
			</div>
			<?php
				}
			?>
			<?php
				echo form_close();
			}
			?>	
			
		</div><!-- /.container -->
	</div>
    <?php $this->load->view('backend/elements/footer') ?>
	
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.tablesorter.js') ?>"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#simpan, #approve, #reject').click(function () {
			var btn = $(this);
			btn.button('loading');
		});
		
		$(".tablesorter").tablesorter();
	});
	</script>
</body>
</html>