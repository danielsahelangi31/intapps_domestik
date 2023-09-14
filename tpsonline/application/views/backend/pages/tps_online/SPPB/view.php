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
                	<h2>Re - Release Data SPPB</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>

			<?php
			if(isset($error_msg)){
			?>
			<div class="alert alert-danger fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4><?php echo isset($error_header) ? $error_header : 'Maaf Tidak Bisa Memproses Lebih Lanjut!' ?></h4>
				<p><?php echo $error_msg ?></p>
			</div>
			<?php
			}
			?>
			
			<?php
			if(isset($info_msg)){
			?>
			<div class="alert alert-success fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<h4><?php echo $info_msg ?></h4>
			</div>
			<?php
			}
			?>
			
			<?php echo form_open("tps_online/sppb/getCargoCTOS", array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
			
			
			<fieldset class="delivery-request-border">
				<legend class="delivery-request-border">Data SPPB</legend>			
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<label class="col-lg-4 control-label">Nomor BL</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="NO_BL_AWB"><?php echo $sppb->NO_BL_AWB ?></p>
								<input type="hidden" class="form-control" id="CAR" name="CAR" value="<?php echo $sppb->CAR ?>"/>
								<input type="hidden" class="form-control" id="NO_BL_AWB" name="NO_BL_AWB" value="<?php echo $sppb->NO_BL_AWB ?>"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Tanggal BL</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="TGL_BL_AWB"><?php echo $sppb->TGL_BL_AWB ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Nomor SPPB</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="NO_SPPB"><?php echo $sppb->NO_SPPB ?></p>
								<input type="hidden" class="form-control" id="NO_SPPB" name="NO_SPPB" value="<?php echo $sppb->NO_SPPB ?>"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Tanggal SPPB</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="TGL_SPPB"><?php echo $sppb->TGL_SPPB ?></p>
								<input type="hidden" class="form-control" id="TGL_SPPB" name="TGL_SPPB" value="<?php echo $sppb->TGL_SPPB ?>"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">NPWP Importir</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="NPWP_IMP"><?php echo $sppb->NPWP_IMP ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Importir</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="NAMA_IMP"><?php echo $sppb->NAMA_IMP ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Bruto / Netto</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="BRUTO_NETTO"><?php echo $sppb->BRUTO ?> / <?php echo $sppb->NETTO ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Jumlah Cargo</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="JUMLAH_CARGO"><?php echo $sppb->JUMLAH_CARGO ?></p>
								<input type="hidden" class="form-control" id="JUMLAH_CARGO" name="JUMLAH_CARGO" value="<?php echo $sppb->JUMLAH_CARGO ?>"/>
							</div>
						</div>
					</div>
					
					<div class="col-lg-6">
						<div class="form-group">
							<label class="col-lg-4 control-label">Vessel</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="NM_ANGKUT"><?php echo $sppb->NM_ANGKUT ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Voyage</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="NO_VOY_FLIGHT"><?php echo $sppb->NO_VOY_FLIGHT ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">BC 11</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="NO_BC11"><?php echo $sppb->NO_BC11 ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Tanggal BC 11</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="TGL_BC11"><?php echo $sppb->TGL_BC11 ?></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">No Pos BC 11</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="NO_POS_BC11"><?php echo $sppb->NO_POS_BC11 ?></p>
							</div>
						</div>
					</div>
				</div>
			</fieldset>			
			
			
			<fieldset class="delivery-request-border">
				<legend class="delivery-request-border">Data Kargo</legend>
						
					<br/>
						
						<div class="col-lg-12 alpha beta">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<th>No</th>
										<th>VIN</th>
										<th>Model</th>
										<th>Maker</th>
										<th>Direction</th>
										<th>Consignee</th>
										<th>Logistik</th>
									</tr>
								</thead>
								<tbody>
									<?php
									if($cargo){
										$i=1;
										foreach($cargo as $row){
										
									?>
									<tr>
										<td><?php echo $i++ ?></td>
										<td><?php echo $row->VIN ?></td>
										<td><?php echo $row->MODEL_NAME ?></td>
										<td><?php echo $row->MAKE_NAME ?></td>
										<td><?php 
										if($row->DIRECTION == 1)
											{ echo "IMPORT";}
										else if ($row->DIRECTION == 2)
											{ echo "ECPORT";}
										else if ($row->DIRECTION == 3)
											{ echo "TRANSHIPMENT";}
										else if ($row->DIRECTION == 4)
											{ echo "SHIFFTING";}
										else
											{ echo "DIRECTION TIDAK TERDAFTAR";}
										?>
										</td>
										<td><?php echo $row->CONSIGNEE_NAME ?></td>
										<td><?php echo $row->LOGISTIC_COMPANY ?></td>
									</tr>
									<?php
										}
									}else{
									?>
									<tr><td colspan="7"><em>Belum ada cargo yang di Assign dengan Bl ini, mohon update cargo untuk BL ini agar dapat melakukan release cargo</em></td></tr>
									<?php	
									}
									?>
								</tbody>
							</table>
						</div>
			</fieldset>
				<div class="row">
				<?php 
					if(count($cargo) == 0)
					{
				?>
					<div class="col-lg-12">
						<div class="pull-right">
							<a href="<?php echo site_url($grid_state) ?>" class="btn btn-default">Kembali</a>
						</div>
					</div>
				<?php
					}
					else{
				?>
					<div class="col-lg-12">
						<div class="pull-right">
							<button class="btn btn-primary fr" type="submit" name="simpan" id="simpan" value="1" data-loading-text="Silakan Tunggu ..."><span class="glyphicon glyphicon-cloud-download"></span> Release Data</button>
							<a href="<?php echo site_url($grid_state) ?>" class="btn btn-default">Kembali</a>
						</div>
					</div>
				<?php
					}
				?>
				</div>
			<?php echo form_close() ?>

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