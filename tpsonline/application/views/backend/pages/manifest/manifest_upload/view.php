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
                	<h2>Lihat Unggahan Manifest</h2>
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
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Statistik Manifest</legend>
						<div class="col-lg-12">
							<div class="row">
								<table class="table table-bordered table-striped">
									<tbody>
										<tr>
											<td style="width:5%">1</td>
											<td>Waktu Unggah</td>
											<td><?php echo date('d-M-Y H:i:s', strtotime($manifest->waktu_upload)) ?></td>
										</tr>
										<tr>
											<td style="width:5%">2</td>
											<td>File Unggahan</td>
											<td><a href="<?php echo site_url('manifest/manifest_upload/unduh_manifest/'.$manifest->id) ?>"><?php echo $manifest->nama_file_asli ?></a></td>
										</tr>
										<tr>
											<td style="width:5%">3</td>
											<td>Jumlah Petikemas</td>
											<td><?php echo $manifest->total_container ?></td>
										</tr>
										<tr>
											<td>4</td>
											<td>Jumlah Kargo</td>
											<td><?php echo $manifest->total_cargo ?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			
			<p><sup>1</sup> Waktu yang ditampilkan adalah waktu setempat</p>
			
			<div class="row">
				<div class="col-lg-6">
					<a href="<?php echo site_url('manifest/manifest_upload/unduh_manifest/'.$manifest->id) ?>" class="btn btn-primary"><span class="glyphicon glyphicon-download"></span> Unduh Manifest</a>
					<a href="<?php echo site_url('manifest/manifest_upload/reparse/'.$manifest->id) ?>" class="btn btn-primary"><span class="glyphicon glyphicon-folder-open"></span> Detail Manifest</a>
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
	
    <?php $this->load->view('backend/elements/footer') ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
<script type="text/javascript">

$(document).ready(function(){
	
});
</script>
</body>
</html>