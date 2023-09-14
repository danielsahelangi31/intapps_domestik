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
                	<h2>Lihat Detail Manifest</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>
			
			<?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
			<input type="hidden" id="id" name="id" value="<?php echo $manifest->id ?>">
			
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Identifikasi Cepat</legend>
						<div class="col-lg-6" style="text-align:left; font-size:18px;">
							<label>Route: <?php echo $manifest->pol.'-'.$manifest->pod ?> | Ber<sup>1</sup>: <?php echo date('d M Y H:i', strtotime($manifest->atd)) ?></label>
						</div>
						<div class="col-lg-6" style="text-align:right; font-size:18px;">
							<label>UKK: <?php echo $manifest->no_ukk ?> | SHIP: <?php echo $manifest->nama_kapal ?> | VOY: <?php echo $manifest->voyage ?></label>
						</div>
					</fieldset>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Pelayaran</legend>
						<div class="form-group">
							<label class="col-lg-4 control-label">No UKK</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="no_ukk"><?php echo $manifest->no_ukk ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">POL / POD</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="pol_pod"><?php echo $manifest->pol.'-'.$manifest->pod ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Tiba<sup>1</sup> / Berangkat<sup>1</sup></label>
							<div class="col-lg-8">
								<p class="form-control-static" id="pol_pod"><?php echo date('d M Y H:i', strtotime($manifest->ata)).' / '.date('d M Y H:i', strtotime($manifest->atd)) ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Voyage Out</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="voyage"><?php echo $manifest->voyage ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Nama Kapal</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="nama_kapal"><?php echo $manifest->nama_kapal ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Call Sign</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="call_sign"><?php echo $manifest->call_sign ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">IMO Number</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="imo_number"><?php echo $manifest->imo_number ?></p>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Keagenan</legend>
						<div class="form-group">
							<label class="col-lg-4 control-label">Kode Agen</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="kode_shipping_line">NOT_AVAILABLE</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Nama Shipping Agen</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="kode_shipping_line">NOT_AVAILABLE</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Kode Shipping Line</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="pol_pod">NOT_AVAILABLE</p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Nama Shipping Line</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="arrival_date">NOT_AVAILABLE</p>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs">
						<li class="active"><a href="#informasi_umum" data-toggle="tab">Informasi Umum</a></li>
						<li><a href="#containers" data-toggle="tab">Container</a></li>
						<li><a href="#consignments" data-toggle="tab">Consignment</a></li>
						<li><a href="#packages" data-toggle="tab">Packages</a></li>
					</ul>
					
					<!-- Tab panes -->
					<div class="tab-content">
						<!-- Informasi Umum -->
						<div class="tab-pane fade in active" id="informasi_umum">
							<?php
							$informasi_kapal = $result->informasi_kapal->data;
							?>
							<div class="col-lg-12">
								<div class="row">
									<table class="table table-bordered table-hover">
										<tbody>
											<tr>
												<td>No UKK</td>
												<td><?php echo $informasi_kapal->no_ukk ?></td>
											</tr>
											<tr>
												<td>Nama Kapal</td>
												<td><?php echo $informasi_kapal->nama_kapal  ?></td>
											</tr>
											<tr>
												<td>Direction</td>
												<td><?php echo $informasi_kapal->direction  ?></td>
											</tr>
											<tr>
												<td>Inaportnet ATP Number</td>
												<td><?php echo $informasi_kapal->inaportnet_atp_number ?></td>
											</tr>
											<tr>
												<td>Voyage</td>
												<td><?php echo $informasi_kapal->voyage ?></td>
											</tr>
											<tr>
												<td>Voyage</td>
												<td><?php echo $informasi_kapal->previous_voyage ?></td>
											</tr>
											<tr>
												<td>Previous Voyage</td>
												<td><?php echo $informasi_kapal->call_sign ?></td>
											</tr>
											<tr>
												<td>Call Sign</td>
												<td><?php echo $informasi_kapal->imo_number ?></td>
											</tr>
											<tr>
												<td>IMO Number</td>
												<td><?php echo $informasi_kapal->last_port ?></td>
											</tr>
											<tr>
												<td>Last Port</td>
												<td><?php echo $informasi_kapal->last_port ?></td>
											</tr>
											<tr>
												<td>POL</td>
												<td><?php echo $informasi_kapal->pol ?></td>
											</tr>
											<tr>
												<td>POD</td>
												<td><?php echo $informasi_kapal->pod ?></td>
											</tr>
											<tr>
												<td>Next Port</td>
												<td><?php echo $informasi_kapal->next_port ?></td>
											</tr>
											<tr>
												<td>ATA @ POL</td>
												<td><?php echo $informasi_kapal->ata ?></td>
											</tr>
											<tr>
												<td>ATD @ POL</td>
												<td><?php echo $informasi_kapal->atd ?></td>
											</tr>
											<tr>
												<td>ETA @ POD</td>
												<td><?php echo $informasi_kapal->eta ?></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
				
						<!-- Containers -->
						<div class="tab-pane fade in" id="containers">
							<div class="col-lg-12">
								<div class="row table-wide">
									<table class="table table-bordered table-hover">
										<thead>
											<tr>
												<th rowspan="2">No</th>
												<th rowspan="2">Container Number</th>
												<th rowspan="2">ISO Code Container<th rowspan="2">
												<th rowspan="2">Type</th>
												<th rowspan="2">Size</th>
												<th rowspan="2">Status</th>
												<th rowspan="2">Plugging Required</th>
												<th colspan="2">Fumigation</th>
												<th rowspan="2">Kosong</th>
												<th rowspan="2">HZ</th>
												<th colspan="2">Reefer Temp</th>
												<th colspan="2">Weight</th>
												<th colspan="5">Overdimension</th>
												<th rowspan="2">POL</th>
												<th rowspan="2">POD</th>
												<th rowspan="2">Temp Unit</th>
												<th rowspan="2">Length Unit</th>
												<th rowspan="2">Weight Unit</th>
												<th colspan="2">Seal</th>
											</tr>
											<tr>
												<th>Required</th>
												<th>By</th>
												<th>Min</th>
												<th>Max</th>
												<th>Gross</th>
												<th>Tare</th>
												<th>Height</th>
												<th>Left</th>
												<th>Right</th>
												<th>Front</th>
												<th>Back</th>
												<th>Carrier</th>
												<th>Custom</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$i = 1;
											foreach($result->containers->data as $cont){
											?>
											<tr>
												<td><?php echo $i++ ?></td>
												<td><?php echo $cont->container_number ?></td>
												<td><?php echo $cont->iso_code_container ?></td>
												<td><?php echo $cont->type ?></td>
												<td><?php echo $cont->size ?></td>
												<td><?php echo $cont->status ?></td>
												<td><?php echo $cont->plugging_required ?></td>
												<td><?php echo $cont->fumigation_required ?></td>
												<td><?php echo $cont->fumigation_by ?></td>
												<td><?php echo $cont->empty ?></td>
												<td><?php echo $cont->hazard ?></td>
												<td><?php echo $cont->reefer_min_temp ?></td>
												<td><?php echo $cont->reefer_max_temp ?></td>
												<td><?php echo $cont->gross_weight ?></td>
												<td><?php echo $cont->tare_weight ?></td>
												<td><?php echo $cont->overdimension_height ?></td>
												<td><?php echo $cont->overdimension_left ?></td>
												<td><?php echo $cont->overdimension_right ?></td>
												<td><?php echo $cont->overdimension_front ?></td>
												<td><?php echo $cont->overdimension_back ?></td>
												<td><?php echo $cont->pol ?></td>
												<td><?php echo $cont->pod ?></td>
												<td><?php echo $cont->temp_unit ?></td>
												<td><?php echo $cont->length_unit ?></td>
												<td><?php echo $cont->weight_unit ?></td>
												<td><?php echo $cont->carrier_seal_number ?></td>
												<td><?php echo $cont->custom_seal_number ?></td>
											</tr>
											<?php
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
				
						<!-- Consigments -->
						<div class="tab-pane fade in" id="consignments">
							<div class="col-lg-12">
								<div class="row table-wide">
									<table class="table table-bordered table-hover">
										<thead>
											<tr>
												<th rowspan="2">No</th>
												<th rowspan="2">Nomor BL</th>
												<th rowspan="2">Tanggal BL</th>
												<th rowspan="2">Master BL</th>
												<th rowspan="2">Operator Code</th>
												<th rowspan="2">Transport Stage</th>
												<th colspan="6">LOCODE</th>
												<th rowspan="2">ETD</th>
												<th colspan="2">Shipper</th>
												<th colspan="2">Consignee</th>
												<th colspan="2">Notify</th>
												<th rowspan="2">Description</th>
												<th rowspan="2">Remarks</th>
											</tr>
											<tr>
												<th>Stuffing</th>
												<th>Sebelumnya</th>
												<th>Pel. Muat</th>
												<th>Pel. Bongkar</th>
												<th>Pel. Selanjutnya</th>
												<th>Pel. Akhir</th>
												<th>ETD</th>
												<th>Name</th>
												<th>Address</th>
												<th>Name</th>
												<th>Address</th>
												<th>Name</th>
												<th>Address</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$i = 1;
											foreach($result->consignments->data as $con){
											?>
											<tr>
												<td><?php echo $i++ ?></td>
												<td><?php echo $con->bl_number ?></td>
												<td><?php echo $con->bl_date ?></td>
												<td><?php echo $con->master_bl ?></td>
												<td><?php echo $con->carrier_operator_code ?></td>
												<td><?php echo $con->transport_stage ?></td>
												<td><?php echo $con->despatch_place_code ?></td>
												<td><?php echo $con->previous_port_code ?></td>
												<td><?php echo $con->load_port_code ?></td>
												<td><?php echo $con->discharge_port_code ?></td>
												<td><?php echo $con->next_port_code ?></td>
												<td><?php echo $con->destination_port_code ?></td>
												<td><?php echo $con->etd ?></td>
												<td><?php echo $con->shipper_name ?></td>
												<td><?php echo $con->shipper_address ?></td>
												<td><?php echo $con->consignee_name ?></td>
												<td><?php echo $con->consignee_address ?></td>
												<td><?php echo $con->notify_name ?></td>
												<td><?php echo $con->notify_address ?></td>
												<td><?php echo $con->description ?></td>
												<td><?php echo $con->remarks ?></td>
											</tr>
											<?php
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						
						<!-- Packages -->
						<div class="tab-pane fade in" id="packages">
							<div class="col-lg-12">
								<div class="row table-wide">
									<table class="table table-bordered table-hover">
										<thead>
											<tr>
												<th rowspan="2">No</th>
												<th rowspan="2">Nomor BL</th>
												<th rowspan="2">Qty</th>
												<th rowspan="2">UN Packaging Code</th>
												<th rowspan="2">No Container</th>
												<th colspan="2">Weight</th>
												<th rowspan="2">Volume</th>
												<th rowspan="2">Mengganggu</th>
												<th rowspan="2">HZ</th>
												<th colspan="4">Dangerous Goods</th>
												<th colspan="3">Refrigerating</th>
												<th colspan="2">Fumigation</th>
												<th rowspan="2">Circulation Req.</th>
												<th rowspan="2">HS Code</th>
												<th rowspan="2">Temp Unit</th>
												<th rowspan="2">Weight Unit</th>
												<th rowspan="2">Volume Unit</th>
												<th colspan="2">Dangerous Goods</th>
												<th rowspan="2">Description</th>
												<th rowspan="2">Remarks</th>
											</tr>
											<tr>
												<th>Jumlah</th>
												<th>Satuan</th>
												<th>Gross</th>
												<th>Net</th>
												<th>UN</th>
												<th>IMO</th>
												<th>Flash Point</th>
												<th>Packaging</th>
												<th>Required</th>
												<th>Min Temp</th>
												<th>Max Temp</th>
												<th>Required</th>
												<th>By</th>
												<th>Handling</th>
												<th>Description</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$i = 1;
											foreach($result->consignments->data as $con){
												foreach($con->packages as $package){
											?>
											<tr>
												<td><?php echo $i++ ?></td>
												<td><?php echo $package->bl_number ?></td>
												<td><?php echo $package->qty ?></td>
												<td><?php echo $package->qty_unit ?></td>
												<td><?php echo $package->un_packaging_code ?></td>
												<td><?php echo $package->container_number ?></td>
												<td><?php echo $package->gross_weight ?></td>
												<td><?php echo $package->net_weight ?></td>
												<td><?php echo $package->volume ?></td>
												<td><?php echo $package->polluting ?></td>
												<td><?php echo $package->hazard ?></td>
												<td><?php echo $package->un_dg_code ?></td>
												<td><?php echo $package->imo_dg_code ?></td>
												<td><?php echo $package->flash_point ?></td>
												<td><?php echo $package->dg_packaging_type ?></td>
												<td><?php echo $package->refrigeration_required ?></td>
												<td><?php echo $package->refrigeration_min_temp ?></td>
												<td><?php echo $package->refrigeration_max_temp ?></td>
												<td><?php echo $package->fumigation_required ?></td>
												<td><?php echo $package->fumigation_by ?></td>
												<td><?php echo $package->circulation_required ?></td>
												<td><?php echo $package->hs_code ?></td>
												<td><?php echo $package->temp_unit ?></td>
												<td><?php echo $package->weight_unit ?></td>
												<td><?php echo $package->volume_unit ?></td>
												<td><?php echo $package->dg_handling_instruction ?></td>
												<td><?php echo $package->dg_description ?></td>
												<td title="<?php echo $package->description ?>"><?php echo substr($package->description, 0, 40) ?></td>
												<td title="<?php echo $package->remarks ?>"><?php echo substr($package->remarks, 0, 40) ?></td>
											</tr>
											<?php
												}
											}
											?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<p><sup>1</sup> Waktu yang ditampilkan adalah waktu setempat</p>
			
			<div class="row">
				<div class="col-lg-6">
					<a href="<?php echo site_url('manifest/manifest_upload/unduh_manifest/'.$manifest->id) ?>" class="btn btn-primary"><span class="glyphicon glyphicon-download"></span> Unduh Manifest</a>
				</div>
				<div class="col-lg-6">
					<div class="pull-right">
						<a href="<?php echo site_url($grid_state) ?>" class="btn btn-default">Kembali</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

		</div><!-- /.container -->
	</div>
			
				<div>
			</div>
    <?php $this->load->view('backend/elements/footer') ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
<script type="text/javascript">

$(document).ready(function(){
	
});
</script>
</body>
</html>