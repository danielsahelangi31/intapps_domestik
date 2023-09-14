<!DOCTYPE html>
<html lang="id">
	<head>
		<?php $this->load->view('backend/elements/basic_head') ?>
	</head>
<body>

<style>
body {font-family: Arial;}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons inside the tab */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
  font-size: 17px;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  display: none;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-top: none;
}
</style>
	
<style type="text/css">
	#overlay{	
	position: fixed;
	top: 0;
	z-index: 100;
	width: 85%;
	height:100%;
	display: none;
	/*background: rgba(0,0,0,0.6);*/
}
.cv-spinner {
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;  
}
.spinner {
	width: 40px;
	height: 40px;
	border: 4px #ddd solid;
	border-top: 4px #2e93e6 solid;
	border-radius: 50%;
	animation: sp-anime 0.8s infinite linear;
}
@keyframes sp-anime {
	100% { 
		transform: rotate(360deg); 
	}
}
.is-hide{
	display:none;
}
</style>

		<div id="wrap">
			<?php $this->load->view('backend/components/header') ?>
			<div class="container">

				<div class="row">
					<div class="col-md-8">
						<h2>Manual Import SPPB</h2>
						<hr><br>
					</div>
				</div>

				

				<div class="row">
					<div class="col-sm-5 col-md-5 col-lg-5">
							<div class="form-group">
								<label class="control-label" for="doc_type">Jenis Dokumen</label>
									<select name="doc_type" id="doc_type" class="form-control" onclick="createUserJsObject.ShowDoc_type();">
										<option id="all">-Pilih Jenis Dokumen BC-</option>
										<option id="opt_bc20" value="opt_bc20">SPPB BC 2.0</option>
										<option id="opt_bc23" value="opt_bc23">SPPB BC 2.3</option>
										<option id="opt_spjm" value="opt_spjm">SPJM</option>
										<option id="opt_pabean" value="opt_pabean">Pabean</option>
										<option id="opt_manual" value="opt_manual">Manual</option>
										<!-- <?php
											if(!empty($doc_type['ID'])){
												for ($i=0; $i < count($doc_type['ID']) ; $i++) { 
													$id = $doc_type['ID'][$i];
													$name = $doc_type['DOC_TYPE'][$i];

													?>


													<option value="<?php echo $id;?>" id="id_option"><?php echo $name;?></option>
													<?php
												}
											}
										?> -->
									</select>
							</div>
					</div>
				</div>
	

					<div id="overlay">
							<div class="cv-spinner">
								<span class="spinner"></span>
							</div>
						</div>

				<div class="row" style="margin-left: 2px;">
					<div class="col-sm-8 col-md-8 col-lg-8">
						<div class="tampil_bc20" style="display:none;">
							<form class="form-horizontal" id="formSppb_bc20">
									<div class="form-group">
										<label class="control-label" for="No_Sppb">No Sppb</label>
											<input type="text" class="form-control" name="No_Sppb" id="No_Sppb" placeholder="No SPPB">
									</div>

									<div class="form-group">
										<label class="control-label" for="Tgl_Sppb">Tgl Sppb</label>
										<div class="input-group">
											<input type="text" class="form-control date" name="Tgl_Sppb" id="Tgl_Sppb" placeholder="Tanggal SPPB">
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
										
									</div>	
									<div class="form-group">
										<label class="control-label" for="NPWP_Imp">NPWP IMP</label>
											<input type="text" class="form-control number_valid_char" name="NPWP_Imp" id="NPWP_Imp" placeholder="NPWP IMP">
									</div>			
										
									<footer>
										<div class="form-group">
											<div class="btn ajax-load col-sm-8" id="simpan_load" style="display:none"></div>
											<input type="button" name="cek" value="Cek" class="btn btn-success simpanBC20" />
											<a  class="btn btn-danger" id="reset">Reset</a>
										</div>
									</footer>
									</form>
									<center><div id="getResponMessage"></div></center>
						</div>
					</div>
				</div>

				<div class="row" style="margin-left: 2px;">
					<div class="col-sm-8 col-md-8 col-lg-8">
						<div class="tampil_bc23" style="display:none;">
								<form class="form-horizontal" id="formSppb_bc23">
									<div class="form-group">
										<label class="control-label" for="No_Sppb">No Sppb</label>
											<input type="text" class="form-control" name="No_Sppb" id="No_Sppb_bc23" placeholder="No SPPB" required>
									</div>

									<div class="form-group">
										<label class="control-label" for="Tgl_Sppb_bc23">Tgl Sppb</label>
										<div class="input-group">
											<input type="text" class="form-control date" name="Tgl_Sppb" id="Tgl_Sppb_bc23" placeholder="Tanggal SPPB" required>
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											
										</div>
									</div>	
									<div class="form-group">
										<label class="control-label" for="NPWP_Imp">NPWP IMP</label>
											<input type="text" class="form-control number_valid_char" name="NPWP_Imp" id="NPWP_Imp_bc23" placeholder="NPWP IMP" required>
									</div>			
										
										<footer>
											<div class="form-group">
												<div class="btn ajax-load col-sm-8" id="simpan_load" style="display:none"></div>
												<input type="button" name="cek" value="Cek" class="btn btn-success simpanBC23" />
												<a  class="btn btn-danger" id="reset_bc23">Reset</a>
											</div>
										</footer>
									</form>
									<center><div id="getResponMessage_bc23"></div></center>
						</div>
					</div>
				</div>

				<div class="row" style="margin-left: 2px;">
					<div class="col-sm-8 col-md-8 col-lg-8">
						<div class="tampil_spjm" style="display:none;">
							<form class="form-horizontal" id="formSppb_bcSPJM">

								<div class="form-group">
									<label class="control-label" for="NoPib">No PIB</label>
										<input type="text" class="form-control" name="NoPib" id="NoPib" placeholder="No PIB">
								</div>
								
								<div class="form-group">
									<label class="control-label" for="tglPib">Tanggal PIB</label>
									<div class="input-group">
										<input type="text" class="form-control date" name="tglPib" id="tglPib" placeholder="Tanggal PIB" required="">
										<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
									</div>
								</div>

								<footer>
									<div class="form-group">
										<div class="btn ajax-load col-sm-8" id="simpan_load" style="display:none"></div>
										<input type="button" name="cek" value="Cek" class="btn btn-success simpanSPJM" />
										<a  class="btn btn-danger" id="reset_spjm">Reset</a>
									</div>
								</footer>
							</form>
							<center><div id="getResponMessage_spjm"></div></center>
						</div>
					</div>
				</div>

				<div class="row" style="margin-left: 2px;">
					<div class="col-sm-8 col-md-8 col-lg-8">
						<div class="tampil_pabean" style="display:none;">
							<form class="form-horizontal" id="formSppbPabean">



								<div class="form-group">
									<!-- <label class="control-label" for="KdDok">Kd Dokumen Pabean</label> -->
										<input type="hidden" class="form-control" name="KdDok" id="KdDok" placeholder="Kd Dokumen" value="41">
								</div>

								<div class="form-group">
									<label class="control-label" for="NoDok">No Dokumen Pabean</label>
										<input type="text" class="form-control" name="NoDok" id="NoDok" placeholder="No Dokumen">
								</div>

								<div class="form-group">
									<label class="control-label" for="TglDok">Tanggal Pabean</label>
										<div class="input-group">
											<input type="text" class="form-control date" name="TglDok" id="TglDok" placeholder="Tanggal Dokumen">
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
								</div>
											
								<footer>
									<div class="form-group">
										<div class="btn ajax-load col-sm-8" id="simpan_load" style="display:none"></div>
										<input type="button" name="cek" value="Cek" class="btn btn-success simpanPabean" />
										<a  class="btn btn-danger" id="reset_Pabean">Reset</a>
									</div>
								</footer>

							</form>
							<center><div id="getResponMessage_Pabean"></div></center>
						</div>
					</div>
				</div>

				<div class="row" style="margin-left: 2px;">
					<div class="col-sm-8 col-md-8 col-lg-8">
						<div class="tampil_manual" style="display:none;">
							<form class="form-horizontal" id="formSppbManual">

								<div class="form-group">
									<label class="control-label" for="pilih_dokumen_manual">Jenis Dokumen</label>
											<select name="pilih_dokumen_manual" id="pilih_dokumen_manual" class="form-control">
											<option id="all">-Pilih Jenis Dokumen BC-</option>
											<?php
												if(!empty($doc_type['ID'])){
													for ($i=0; $i < count($doc_type['ID']) ; $i++) { 
														$id = $doc_type['ID'][$i];
														$name = $doc_type['DOC_TYPE'][$i];
														?>

														<option value="<?php echo $id;?>" id="id_option" name="id_option"><?php echo $name;?></option>
														<?php

													}
												}
											?>
										</select>
								</div>

								<!-- <div class="form-group">
									<label class="control-label" for="KodeDok_manual">Kd Dokumen Manual</label>
										<input type="hidden" class="form-control" name="KodeDok_manual" id="KodeDok_manual" placeholder="Kd Dokumen" value="9">
								</div> -->

								<div class="form-group">
									<label class="control-label" for="NoDok_manual">No Dokumen Manual</label>
										<input type="text" class="form-control" name="NoDok_manual" id="NoDok_manual" placeholder="No Dokumen">
								</div>

								<div class="form-group">
									<label class="control-label" for="TglDok_manual">Tanggal Manual</label>
										<div class="input-group">
											<input type="text" class="form-control date" name="TglDok_manual" id="TglDok_manual" placeholder="Tanggal Dokumen">
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
								</div>
											
								<footer>
									<div class="form-group">
										<div class="btn ajax-load col-sm-8" id="simpan_load" style="display:none"></div>
										<input type="button" name="cek" value="Cek" class="btn btn-success simpanManual" />
										<a  class="btn btn-danger" id="reset_Manual">Reset</a>
									</div>
								</footer>
							</form>
							<center><div id="getResponMessage_Manual"></div></center>
						</div>
					</div>
				</div>
				

			</div><!-- /.container -->
			
		</div> <!-- wrap -->
			
			<?php $this->load->view('backend/elements/footer') ?>


				<script type="text/javascript">

					var Doc_type = jQuery('#doc_type');
					var select = this.value;
					Doc_type.change(function(){
						if ($(this).val() == 'opt_bc20'){
							$('.tampil_bc20').show();
							
							$('.tampil_bc23').hide();
							$('.tampil_spjm').hide();
							$('.tampil_pabean').hide();
							$('.tampil_manual').hide();

						}else if ($(this).val() == 'opt_bc23'){
							$('.tampil_bc23').show();

							$('.tampil_bc20').hide();
							$('.tampil_spjm').hide();
							$('.tampil_pabean').hide();
							$('.tampil_manual').hide();

						}else if ($(this).val() == 'opt_spjm'){
							$('.tampil_spjm').show();

							$('.tampil_bc20').hide();
							$('.tampil_pabean').hide();
							$('.tampil_bc23').hide();
							$('.tampil_manual').hide();

						}else if ($(this).val() == 'opt_pabean'){
							$('.tampil_pabean').show();

							$('.tampil_bc20').hide();
							$('.tampil_spjm').hide();
							$('.tampil_bc23').hide();
							$('.tampil_manual').hide();

						}else if ($(this).val() == 'opt_manual'){
							$('.tampil_manual').show();

							$('.tampil_bc20').hide();
							$('.tampil_spjm').hide();
							$('.tampil_bc23').hide();
							$('.tampil_pabean').hide();
						}else{

							$('.tampil_bc20').hide();
							$('.tampil_bc23').hide();
							$('.tampil_spjm').hide();
							$('.tampil_pabean').hide();
							$('.tampil_manual').hide();
						}
					});
				
				</script>
		
		<script type="text/javascript">

	
		// $("#NPWP_Imp").mask("99.999.999.9-999.999");

			$(function(){
				validasi_angka();
			})
			
		jQuery(function($){
			$(document).ajaxSend(function() {
					$("#overlay").fadeIn(300);　
			});
			$(".simpanBC20").click(function(){
				var data = $('#formSppb_bc20').serialize();
				var No_Sppb = $("#No_Sppb").val();
				var Tgl_Sppb = $("#Tgl_Sppb").val();
				var NPWP_Imp = $("#NPWP_Imp").val();
				
				if(No_Sppb && Tgl_Sppb && NPWP_Imp){
					$.ajax({
						type: 'POST',
						url: "<?php echo base_url('index.php/tps_online/import_sppb/recall')?>",
						data: data,
						dataType:"JSON",

						success: function(data) {
							console.log(data);
							if(data.result.GetImpor_SppbResult === 'Data tidak ditemukan'){
								$('#getResponMessage').html('Data Tidak ditemukan');	
							}
							else
							{
								$('#getResponMessage').html('Data Berhasil Diproses')
							}

							///$('#getResponMessage').append('<div>'+data.result.GetImpor_SppbResult+'</div>')
							//;
						}
					}).done(function() {
							setTimeout(function(){
								$("#overlay").fadeOut(300);
							},500);
					});
				}else{
					setTimeout(function(){
							$("#overlay").fadeOut(300);
						},500);
					alert('Harap data diisi dengan lengkap. Mohon dicek kembali');
				}
				
			});

			$(document).ajaxSend(function() {
					$("#overlay").fadeIn(300);　
			});
			$(".simpanSPJM").click(function(){

					var data = $('#formSppb_bcSPJM').serialize();
					var NoPib = $("#NoPib").val();
					var tglPib = $("#tglPib").val();

					if(NoPib && tglPib){
						$.ajax({
							type: 'POST',
							url: "<?php echo base_url('index.php/tps_online/import_sppb/dokumen_bc_spjm')?>",
							data: data,
							dataType:"JSON",

							success: function(data) {
								console.log(data);
								if(data.result === 'Data tidak ditemukan'){
									$('#getResponMessage_spjm').html('Data Tidak ditemukan');	
								}
								else
								{
									$('#getResponMessage_spjm').html('Data Berhasil Diproses')
								}
								///$('#getResponMessage').append('<div>'+data.result.GetImpor_SppbResult+'</div>')
								//;
							}
						}).done(function() {
							setTimeout(function(){
								$("#overlay").fadeOut(300);
							},500);
						});
					}else{
							setTimeout(function(){
									$("#overlay").fadeOut(300);
								},500);
							alert('Harap data diisi dengan lengkap. Mohon dicek kembali');
					}
				});

			$(document).ajaxSend(function() {
					$("#overlay").fadeIn(300);　
			});
			$(".simpanBC23").click(function(){
				// alert();
				// return;
					var data = $('#formSppb_bc23').serialize();
					var No_Sppb_bc23 = $("#No_Sppb_bc23").val();
					var Tgl_Sppb_bc23 = $("#Tgl_Sppb_bc23").val();
					var NPWP_Imp_bc23 = $("#NPWP_Imp_bc23").val();
					// console.log(No_Sppb);	
						if (No_Sppb_bc23 && Tgl_Sppb_bc23 && NPWP_Imp_bc23) {
							$.ajax({
								type: 'POST',
								url: "<?php echo base_url('tps_online/import_sppb/dokumen_bc23')?>",
								data: data,
								dataType:"JSON",

								success: function(data) {
									console.log(data);
									if(data.result.GetImpor_SppbResult === 'Data tidak ditemukan'){
										$('#getResponMessage_bc23').html('Data Tidak ditemukan');	
									}
									else
									{
										$('#getResponMessage_bc23').html('Data Berhasil Diproses')
									}

									///$('#getResponMessage').append('<div>'+data.result.GetImpor_SppbResult+'</div>')
									//;
								}
							}).done(function() {
								setTimeout(function(){
									$("#overlay").fadeOut(300);
								},500);
							});



						}else{
							setTimeout(function(){
									$("#overlay").fadeOut(300);
								},500);
							alert('Harap data BC 23 diisi dengan lengkap. Mohon dicek kembali');
						}
				});	
			}); 
			// bc20

			$(document).ajaxSend(function() {
					$("#overlay").fadeIn(300);　
			});
			$(".simpanPabean").click(function(){
				var data = $('#formSppbPabean').serialize();
				// var KdDok = $("#KdDok").val();
				var NoDok = $("#NoDok").val();
				var TglDok = $("#TglDok").val();
				
				if(NoDok && TglDok){
					$.ajax({
						type: 'POST',
						url: "<?php echo base_url('index.php/tps_online/import_sppb/pabean')?>",
						data: data,
						dataType:"JSON",

						success: function(data) {
							console.log(data);
							if(data.result.GetDok_PabeanResult === '<RESPON>Data Tidak Ditemukan</RESPON>'){

								$('#getResponMessage_Pabean').html('Data Tidak ditemukan');

							}else if (data.result.GetDok_PabeanResult === '<RESPON>Anda tidak berhak mengambil data ini...!!!</RESPON>'){
								
								$('#getResponMessage_Pabean').html('Anda tidak berhak mengambil data ini...!!!');
							
							}else{
								$('#getResponMessage_Pabean').html('Data Berhasil Diproses');
							}

							///$('#getResponMessage').append('<div>'+data.result.GetImpor_SppbResult+'</div>')
							//;
						}
					}).done(function() {
							setTimeout(function(){
								$("#overlay").fadeOut(300);
							},500);
					});
				}else{
					setTimeout(function(){
							$("#overlay").fadeOut(300);
						},500);
					alert('Harap data diisi dengan lengkap. Mohon dicek kembali');
				}
				
			});

			$(document).ajaxSend(function() {
					$("#overlay").fadeIn(300);　
			});
			$(".simpanManual").click(function(){
				var data = $('#formSppbManual').serialize();
				// var KdDok_manual = $("#KdDok_manual").val();
				var NoDok_manual = $("#NoDok_manual").val();
				var TglDok_manual = $("#TglDok_manual").val();
				
				if(NoDok_manual && TglDok_manual){
					$.ajax({
						type: 'POST',
						url: "<?php echo base_url('index.php/tps_online/import_sppb/dokumen_manual')?>",
						data: data,
						dataType:"JSON",

						success: function(data) {
							console.log(data);
							if(data.result.GetImpor_SppbResult === '<RESPON>Data Tidak Ditemukan</RESPON>'){
								$('#getResponMessage_Manual').html('Data Tidak ditemukan');	
							
							}else if (data.result.GetImpor_SppbResult === '<RESPON>Anda tidak berhak mengambil data ini...!!!</RESPON>') {
								$('#getResponMessage_Manual').html('Anda tidak berhak mengambil data ini...!!!');
							
							}else{
								$('#getResponMessage_Manual').html('Data Berhasil Diproses');
							}

							///$('#getResponMessage').append('<div>'+data.result.GetImpor_SppbResult+'</div>')
							//;
						}
					}).done(function() {
							setTimeout(function(){
								$("#overlay").fadeOut(300);
							},500);
					});
				}else{
					setTimeout(function(){
							$("#overlay").fadeOut(300);
						},500);
					alert('Harap data diisi dengan lengkap. Mohon dicek kembali');
				}
				
			});
		
			function validasi_angka()
			{
			  // buat validasi angka
			  $(".number_valid_char").on("keypress", function (event) {
			    var regex = /[0-9]/g;
			    var key = String.fromCharCode(event.which);
			      if (regex.test(key) || event.keyCode == 8 || event.keyCode == 9) {
			          return true;
			      }
			        return false;
			  });       
			}

			$('#reset').click(function(){
				$('#formSppb_bc20')[0].reset();
				$('#getResponMessage_bc20')[0].reset();
			});

			$('#reset_bc23').click(function(){
				$('#formSppb_bc23')[0].reset();
				$('#getResponMessage_bc23')[0].reset();
			});

			$('#reset_spjm').click(function(){
				$('#formSppb_bcSPJM')[0].reset();
				$('#getResponMessage_spjm')[0].reset();
			});

			$('#reset_Pabean').click(function(){
				$('#formSppbPabean')[0].reset();
				$('#getResponMessage_Pabean')[0].reset();
			});

			$('#reset_Manual').click(function(){
				$('#formSppbManual')[0].reset();
				$('#getResponMessage_Manual')[0].reset();
			});

			
		</script>
	</body>
</html>