<html>

<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
	<script src="<?php echo base_url('assets/js/jquery-2.0.3.min.js'); ?>"></script>

	<script>
	$(document).ready(function(){
		// Sembunyikan alert validasi kosong
		$("#kosong").hide();
	});
	</script>

</head>
<body>

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
			<!-- <br><br><br> -->
			<h1>Upload Manifest</h1>
			<hr>

			<p class="lead">
				<small></small>
			</p>

			<form id="form_preview" method="post" action="<?php echo base_url("tps_online/Upload_manifest/upload_csv"); ?>" enctype="multipart/form-data">
				
				<div class="row">
					<!-- <div id="overlay">
						<div class="cv-spinner">
							<span class="spinner"></span>
						</div>
					</div> -->
					<div class="col-md-6">
						<fieldset class="delivery-request-border">
							<legend class="delivery-request-border">Download Form Upload Manifest </legend>
							<br>
							<div class="row">
								<div class="col-md-4">
									<a href="<?php echo base_url("/assets/template/Form_Input_Manifest.xlsx");?>" class="btn btn-success" >Download Form</a>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-8">
									<input type="file" name="file_vin" id="file_vin" class="form-control-file"/>
								</div>
							</div>
						</fieldset>
					</div>	

					<div class="col-md-6">
						<!-- <fieldset class="delivery-request-border">
							<legend class="delivery-request-border">Download Format BL Dan Upload Kembali </legend>
							<br>
							<div class="row">
								<div class="col-md-4">
									<a href="<?php echo base_url("tps_online/consignment/download_format_bl"); ?>" class="btn btn-success" >Download Format BL</a>		
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-8">
									<input type="file" name="file_bl" id="file_bl" class="form-control-file"/>
								</div>
							</div>
						</fieldset> -->
					</div>

				</div>

				<div class="row">
					<div class="col-md-12">
						<input type="submit" name="preview" id="preview" value="Preview" class="btn btn-primary">
					</div>
				</div>
				
			</form>

			<?php
				if(isset($_POST['preview'])){

					$load = $sheet;

					$load_bl = $sheet_bl;
			?>

			<div class="row">
					<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Pilih Kunjungan Kapal</legend>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-md-3 control-label">Visit ID</label>
										<div class="col-lg-8">
											<?php 
											$dataCon= array();
											foreach ($VISIT_ID_DS as $row) {
												$dataCon[] = $row->VISIT_ID.' '.$row->VISIT_NAME;
											}
											 ?>
											<!-- <select class="form-control" id="VISIT_ID">
												<option value="">-- Pilih --</option>
												<?php
												
												foreach($VISIT_ID_DS as $row){
												?>
												<option value="<?php echo $row->VISIT_ID ?>" <?php echo $row->VISIT_ID == $VISIT_ID ? 'selected="selected"' : '' ?>><?php echo $dataCon[] = $row->VISIT_ID.' '.$row->VISIT_NAME ?></option>
												<?php
												}
												?>
											</select> -->
											
											<input type="text" autocomplete="off" class="form-control" id="VISIT_ID" name="VISIT_ID" value="<?php echo $VISIT_ID  ?>" placeholder="5 Karakter Terakhir VISIT ID"/>
										
										</div>
								</div>	
							</div>

								<div class="col-md-6">
									<div class="form-group">
										<label class="col-md-3 control-label visit_id_loading">Nama Kapal</label>
										<div class="col-lg-8">
											<p class="form-control-static" id="VISIT_NAME"></p>
										</div>
									</div>
								</div>
						</div>
						<br>
						<br>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="col-lg-4 control-label visit_id_loading">Tiba<sup>1</sup> / Berangkat<sup>1</sup></label>
									<div class="col-lg-8">
										<p class="form-control-static"><span id="ETA"></span> / <span id="ETD"></span></p>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="col-lg-4 control-label visit_id_loading">Load / Discharge</label>
									<div class="col-lg-8">
										<p class="form-control-static"><span id="LOAD_PORT"></span> / <span id="DISCHARGER_PORT"></span></p>
									</div>
								</div>
							</div>
						</div>
							
							<div id="ship_edit_link" class="pull-right" style="display:none">
								<a href="#" target="_blank">  Edit Kapal</a>
							</div>
							
						</fieldset>
					</div>
				</div>

			<div class="row">
				<div class="col-md-12">
				<fieldset class="delivery-request-border">
					<legend class="delivery-request-border">Preview file form data VIN</legend>
						<div class="table-responsive col-md-12" style="width:100%; height:430px; overflow:auto">
							<table class="table table-striped table-condensed table-hover" id="tabel_vin"  >
								<tr style="">
									  <!-- <td><b><center>VISIT ID</center></b></td>
								      <td><b><center>NAMA PERUSAHAAN</center></b></td>
								      <td><b><center>TIBA</center></b></td>
								      <td><b><center>BERANGKAT</center></b></td>
								      <td><b><center>LOAD</center></b></td>
								      <td><b><center>DISCHARGER</center></b></td> -->
								      <td><b><center>NOMOR MASTER BL</center></b></td>
								      <td><b><center>TANGGAL MASTER BL</center></b></td>
								      <td><b><center>NOMOR HOUSE BL</center></b></td>
								      <td><b><center>TANGGAL HOUSE BL</center></b></td>
								      <td><b><center>NAMA PERUSAHAAN</center></b></td>
								      <td><b><center>NPWP</center></b></td>
								      <td><b><center>JENIS CARGO</center></b></td>
								      <td><b><center>VIN</center></b></td>
								</tr>

								<?php 
									$numrow = 1;
									$baris = 'A';
									$countKosong = 0;
								?>

								<?php foreach($sheet as $in=>$ss): ?>
									<?php if ( $ss['A'] == '' || $ss['B'] == '' || $ss['C'] == '' || $ss['D'] == '' ||
											   $ss['E'] == '' || $ss['F'] == '' || $ss['G'] == '' || $ss['H'] == '') { ?>

										<?php if($numrow > 1  && $ss[$baris] != '') : ?>
											<?php
											$countKosong++;
										?>
											<tr>
												<td style="background: #E07171;">  <?php echo $ss['A'] ?> </td>
												<td style="background: #E07171;">  <?php echo $ss['B'] ?> </td>
												<td style="background: #E07171;">  <?php echo $ss['C'] ?> </td>
												<td style="background: #E07171;">  <?php echo $ss['D'] ?> </td>
												<td style="background: #E07171;">  <?php echo $ss['E'] ?> </td>
												<td style="background: #E07171;">  <?php echo $ss['F'] ?> </td>
												<td style="background: #E07171;">  <?php echo $ss['G'] ?> </td>
												<td style="background: #E07171;">  <?php echo $ss['H'] ?> </td>
											</tr>

										<?php endif;?>
										
									<?php  } else { ?>
										
												<?php if($numrow > 1  && $ss[$baris] != '') : ?>
													<tr>
														<td>  <?php echo $ss['A'] ?> </td>
														<td>  <?php echo $ss['B'] ?> </td>
														<td>  <?php echo $ss['C'] ?> </td>
														<td>  <?php echo $ss['D'] ?> </td>
														<td>  <?php echo $ss['E'] ?> </td>
														<td>  <?php echo $ss['F'] ?> </td>
														<td>  <?php echo $ss['G'] ?> </td>
														<td>  <?php echo $ss['H'] ?> </td>
													</tr>
												<?php endif;?>

											<?php } ?>
									
								<?php 
								$numrow++;
								$baris++;
								endforeach; 
								?>
							</table>
						</div>
				</fieldset>	
				</div>	
			</div>
			<div class="row">
				<div class="col-md-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Preview file form data BL</legend>
						<div class="table-responsive col-md-12" style="width:100%; height:430px; overflow:auto">
					<table class="table table-striped table-condensed table-hover" id="tabel_bl"  >
					<tr style="">
					      <td><b><center>NOMOR MASTER BL</center></b></td>
					      <td><b><center>NOMOR HOUSE BL</center></b></td>
					      <td><b><center>JENIS CARGO</center></b></td>
					      <td><b><center>BRUTO</center></b></td>
					      <td><b><center>JUMLAH</center></b></td>
					</tr>

						<?php 
							$numrow2 = 1;
							$baris2 = 'A';
							$countKosong2 = 0;
						?>
						<?php foreach($sheet_bl as $sbl=>$bl): ?>
							<?php 
								if($bl['A'] == '' && $bl['B'] == '' && $bl['C'] == '' && $bl['D'] == '' &&
									   $bl['E'] == ''){

							
							 	}else if ( $bl['A'] == '' || $bl['B'] == '' || $bl['C'] == '' || $bl['D'] == '' ||
									   $bl['E'] == '') { ?>

									   	
									   	
								<?php if($numrow2 > 1 ) : ?>
									
										<?php $countKosong2++; ?>
									
									<tr>
										<td style="background: #E07171;">  <?php echo $bl['A'] ?> </td>
										<td style="background: #E07171;">  <?php echo $bl['B'] ?> </td>
										<td style="background: #E07171;">  <?php echo $bl['C'] ?> </td>
										<td style="background: #E07171;">  <?php echo $bl['D'] ?> </td>
										<td style="background: #E07171;">  <?php echo $bl['E'] ?> </td>
									</tr>
									
								<?php endif;?>
						
							<?php  } else { ?>

										<?php if($numrow2 > 1 ) : ?>
											<tr>
												<td>  <?php echo $bl['A'] ?> </td>
												<td>  <?php echo $bl['B'] ?> </td>
												<td>  <?php echo $bl['C'] ?> </td>
												<td>  <?php echo $bl['D'] ?> </td>
												<td>  <?php echo $bl['E'] ?> </td>
											</tr>
										<?php endif;?>

									<?php } ?>
						<?php 
						$numrow2++;
						$baris2++;
						
						endforeach; 
						?>
					</table>
				</div>
					</fieldset>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-6">
					<form id="form_import"> 
							<input type="hidden" name="nmfile_vin" id="nmfile_vin" value="<?php echo $nmfile_vin ?>">
							<?php if ($countKosong > 0 || $countKosong2 > 0) : ?> 
								<script type="text/javascript">
									$(document).ready(function(){
										alert('Data masih ada yang kosong .! Lihat pada tabel preview berwarna merah .! mohon perbaiki Form Input Manifest lalu upload kembali...');
									});
								</script>
							<?php else : ?>
							<button type='button' name='import' id="import" class="btn btn-primary" >Import</button>
							<?php endif ?>
							<a href="<?php echo base_url('tps_online/Upload_manifest/');?>" class="btn btn-warning">Cancel</a>
					</form>
				</div>
			</div>
			
			<?php
				}
			?>
		</div> <!--container-->	
	</div> <!--wrap-->

	<div>
	<?php $this->load->view('backend/elements/footer') ?>
	</div>
	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>

	

	<script type="text/javascript">
	var datepickerBases = {
		autoclose: true,
		weekStart : 1,
		forceParse : true,
		language : 'id',
		orientation : 'top auto',
		format : 'yyyy-mm-dd'
	}
	
	$(document).ready(function(){
		$('#android_ready, #html5_ready').tooltip().show();
		
	
		$.fn.datepicker.dates['id'] = {
			days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
			daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
			daysMin: ["Mg", "Sn", "Se", "Rb", "Km", "Jm", "Sa", "Mg"],
			months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			today: "Hari Ini",
			clear: "Bersihkan"
		};
		
		$('#CUSTOMS_DATE').datepicker(datepickerBases);
	});
	</script>

<script type="text/javascript">


function findSelected(){ 
	  var rate= document.getElementById('TYPE_CARGO'); 
	  //var variable = document.getElementById('variable'); 
	  if(rate.value == "CBU"){
		alert("Data Bruto untuk Jenis Kargo CBU tidak perlu di isi");
		document.getElementById('BRUTO').disabled=true;
	  } else {
		document.getElementById('BRUTO').disabled=false;
	  }
	}

$(document).ready(function(){
	
	$( ".peb" ).hide();
	$( "#ORGANIZATION" ).keypress(function() {
	  $( function() {
		
		var url = bs.siteURL + 'tps_online/consignment/get_pelanggan/' + bs.token + '/';
		
		
		$( "#ORGANIZATION" ).autocomplete({
			source: function (request, response) {
					$.post(url+$('#ORGANIZATION').val(), request, response,'json');
					},
			change: function (event, ui) { 
						var nama_perusahaan = $( "#ORGANIZATION" ).val().split("-");
						var urls = bs.siteURL + 'tps_online/consignment/get_npwp/' + bs.token+ '/' + nama_perusahaan[0];
						var npwp =  $.post(urls,function(data){
							console.log(data[0]);
							$('#NPWP').val(data[0]);
						},'json');	
					}
		});
	  } );
	});

	$( function() {
		    var availableTags = [<?php echo '"' . implode('","', $dataCon) . '"'; ?> ];
		    $( "#VISIT_ID" ).autocomplete({
		      source: availableTags,
		      select: function( event, ui ) {
		      		$("#VISIT_ID").val(ui.item.value);
					lookup_visit_id();		      	
		      }
		   //    select: function (event, ui) {        
			  //         // console.log(ui.item.value);
			  //         lookup_visit_id();

			  //         return false;
			  // }
		    });
		  } );

	// $('#VISIT_IDX').change(lookup_visit_id);
	
	// $( "#VISIT_IDX" ).keypress(function() {
	//   $( function() {
		
	// 	var url = bs.siteURL + 'tps_online/consignment/get_visit/' + bs.token + '/';
		
		
	// 	$( "#VISIT_ID" ).autocomplete({
	// 		minLength: 4,
	// 		source: function (request, response) {
	// 				$.post(url+$('#VISIT_ID').val(), request, response,'json');
	// 				},
	// 		change: function (event, ui) { 
	// 					lookup_visit_id();	
	// 				}
	// 	});
	//   } );
	// });
	
	
	
	
	function lookup_visit_id(){
		if($('.vin_row').length > 0){
			if(confirm('Ubah data visit id akan reset VIN yang sudah ditambahkan')){
				$('.vin_row').remove();
				$('#vin_no_data').show();
			}else{
				return false;
			}
		}
	
		// var visit_id = $('#VISIT_ID').val(); Jika menggunakan select
		let  ar =  $('#VISIT_ID').val().split(' ');
		let visit_id = ar[0];
		
		if(visit_id){		
			var url = bs.siteURL + 'tps_online/kunjungan_kapal/get/' + bs.token;
			var param = {
				VISIT_ID : visit_id
			}
			
			$('#ship_edit_link').hide();
			
			// Set Loading Flag
			$('.visit_id_loading').addClass('ajax-load');
			
			$.post(url, param, function(data){
				// Unset Loading Flag
				$('.visit_id_loading').removeClass('ajax-load');
			
				if(data.success){
					var rec = data.datasource;
					$('#VISIT_NAME').html(rec.VISIT_NAME);
					$('#ETA').html(rec.ETA);
					$('#ETD').html(rec.ETD);
					$('#LOAD_PORT').html(rec.LOAD_PORT);
					$('#DISCHARGER_PORT').html(rec.DISCHARGER_PORT);
					
					var edit_link_url = bs.siteURL + 'tps_online/kunjungan_kapal/view/' + rec.VISIT_ID;
					
					$('#ship_edit_link').show().find('a').attr('href', edit_link_url);
				}else{
					sc_alert('Error', data.msg);
				}
			}, 'json');
		}else{
			$('#VISIT_NAME, #ETA, #ETD, #LOAD_PORT, #DISCHARGER_PORT').html('&nbsp;');
			$('#ship_edit_link').hide();
		}
	}

	$('#VISIT_ID').change(lookup_visit_id);

		
	function vin_popover(title, msg){
		$('#search_vin').parent().addClass('has-error');
		$('#search_vin').popover('destroy');

		$('#search_vin').popover({
			'title' : title,
			'content' : msg,
			'placement' : 'top',
			'trigger' : 'manual'
		});
		
		$('#search_vin').popover('show');
		
		$('#search_vin').keypress(destroy_popover);
		$('#search_vin').click(unbubble_event);
		
		$('body').click(destroy_popover);
	}

	function destroy_popover(){
		$('#search_vin').parent().removeClass('has-error');
		$('#search_vin').popover('destroy');
		
		$('body').unbind('click');
		$('#search_vin').unbind('keypress', destroy_popover);
	}
	
	function unbubble_event(){
		$(this).unbind('click');
		return false;
	}
	
	function auto_remove_popover_on_change(){
		$(this).popover('destroy');
		$(this).parent().removeClass('has-error');
		
		$(this).unbind('change', auto_remove_popover_on_change);
	}
	
	function add_validation_popover(selector, msg, position){
		if(typeof(position) === 'undefined'){
			position = 'right';
		}
	
		$(selector).popover('destroy');

		$(selector).popover({
			'content' : msg,
			'placement' : 'auto ' + position,
			'trigger' : 'focus'
		});
		
		$(selector).popover('show');
		$(selector).change(auto_remove_popover_on_change);
	}
	
	function destroy_all_validation_popovers(){
		$('.has-error').find('input, select').popover('destroy');
		$('.has-error').removeClass('has-error');
	}
	
	function count_vin_row(){
		var i = 1;
		$('.vin_row').each(function(){
			$(this).find('.counter').html(i++);
		});
	}
	
	function delete_row(){
		$(this).parent().parent().remove();
		count_vin_row();
		checking_peb();
		return false;
	}
	
	function add_vin_row(rec){
		var vin_escape = rec.VIN;
		var vin_true = vin_escape.replace( /(:|\.|\[|\]|,|=|@)/g, "\\$1" );
		//rec.replace( /(:|\.|\[|\]|,|=|@)/g, "\\$1" );
		//if($('#vin_landing #' + vin_true).length == 0){
			var elStr =
				'<tr class="vin_row" id="' + rec.VIN + '">' +
				'	<td class="counter"></td>' +
				'	<td>' + rec.VIN + '</td>' +
				'	<td>' + rec.MODEL_NAME + '</td>' +
				'	<td>' + rec.DIRECTION + '</td>' +
				'	<td class="no_peb">' + rec.CUSTOMS_NUMBER + '</td>' +
				'	<td class="tgl_peb">' + rec.CUSTOMS_DATE + '</td>' +
				'	<td><a href="#" class="del_row">Hapus</a> | <a href="' + bs.baseURL + 'tps_online/kargo/view/' + rec.VIN + '" target="_blank">Lihat</a></td>' +
				'</tr>';
			
			var el = $(elStr);
			$(el).find('.del_row').click(delete_row);
			
			$('#vin_landing').append(el);
			$('#npe_landing').empty();
			$('#npe_landing').append('<tr id="npe_no_data"><td colspan="2"><em>Silahkan Request NPE</em></td><tr>');
			
			count_vin_row();
		//}
	}
	
	function search_vin_bulk(){
		$('#search_vin').addClass('ajax-load');
		let  ar =  $('#VISIT_ID').val().split(' ');
		let visit_id = ar[0];
		var url = bs.siteURL + 'tps_online/consignment/get_bulk_vin/' + bs.token;
		var param = {
			'VISIT_ID' : visit_id,
			'VIN' : $('#search_vin').val(),
		}
		
		$.post(url, param, function(data){
			$('#search_vin').removeClass('ajax-load');
			
			if(typeof(data.datasource) !== 'undefined'){
				if(data.datasource.length > 0){
					$('#vin_no_data').hide();
				}
				
				for(var i = 0; i < data.datasource.length; i++){
					add_vin_row(data.datasource[i]);
				}
				checking_peb();
			}
						
			if(data.success == false){
				var msg = '';
				for(var i = 0; i < data.errors.length; i++){
					msg += '<p>' + data.errors[i] + '</p>';
				}
				
				if(msg){
					sc_alert('ERROR', msg);
				}
			}
			
			
		}, 'json');
	}
	
	function search_vin_single(){
		$('#search_vin').addClass('ajax-load');
		var ar =  $('#VISIT_ID').val().split(' ');
		let visit_id = ar[0];
		var url = bs.siteURL + 'tps_online/consignment/get_vin/' + bs.token;
		var param = {
			'VISIT_ID' : visit_id,
			'VIN' : $('#search_vin').val(),
		}
		
		$.post(url, param, function(data){
			$('#search_vin').removeClass('ajax-load');
			
			if(data.success){
				$('#vin_no_data').hide();
				var rec = data.datasource;
				
				add_vin_row(rec);
				checking_peb();
			}else{
				vin_popover('ERROR', data.msg);
			}
		}, 'json');
		
		
	}
	
	function checking_peb(){
		var param = {
			'CUSTOMS_NUMBER' : [],
			'CUSTOMS_DATE' : []
		}
		
		$('.vin_row').each(function(){
			param.CUSTOMS_NUMBER.push($(this).find(".no_peb").html());
			param.CUSTOMS_DATE.push($(this).find(".tgl_peb").html());
		});
		
		console.log(param.CUSTOMS_NUMBER);
		
		if(jQuery.inArray("null", param.CUSTOMS_NUMBER) !== -1){
			$( ".peb" ).show();
		}
		else{
			$( ".peb" ).hide();
		}
	}
	
	function search_vin(){
		if(!$('#VISIT_ID').val()){
			return vin_popover('PERINGATAN', 'Silakan pilih VISIT ID sebelum melanjutkan');
		}
		
		if(!$('#search_vin').val()){
			return vin_popover('PERINGATAN', 'Harap isi VIN number');
		}
		
		if($('#search_bulk').is(':checked')){
			search_vin_bulk();
		}else{
			search_vin_single();
		}
	}
	
	function reset_form(){
		$('#VISIT_ID, #BL_NUMBER, #BL_NUMBER_DATE, #HOUSE_BL_NUMBER, #HOUSE_BL_NUMBER_DATE, #TYPE_CARGO, #BRUTO,#JUMLAH, #search_vin').val('');
		$('#VISIT_NAME, #ETA, #ETD, #LOAD_PORT, #DISCHARGER_PORT').html('&nbsp;');
		$('#ship_edit_link').hide();
		$('.vin_row').remove();
		$('#vin_no_data').show();
	}
	
	
	$('#search_vin').keyup(function(e){
		if(e.keyCode == 13){
			search_vin();
		}
	});
	
	$('#add_vin').click(function(){
		search_vin();
		
		return false;
	});
	
	$('#get_npe').click(function(){
		$('#button_npe').hide();
		var param = {
			'NPWP' : $('#NPWP').val(),
			'CUSTOMS_NUMBER' : [],
			'CUSTOMS_DATE' : []
		}
		
		$('.vin_row').each(function(){
			if($(this).find(".no_peb").html() == "null"){
				param.CUSTOMS_NUMBER.push($('#CUSTOMS_NUMBER').val());
				param.CUSTOMS_DATE.push($('#CUSTOMS_DATE').val());
			}
			else{
				param.CUSTOMS_NUMBER.push($(this).find(".no_peb").html());
				param.CUSTOMS_DATE.push($(this).find(".tgl_peb").html());
			}
		});
		
		console.log(param);
		
		
		
		var url = bs.siteURL + 'tps_online/consignment/get_npe/' + bs.token;
			
		$.post(url, param, function(data){
			if(data.success){
				console.log(data.datasource);
				var deskripsi_npe = '<tr>' +
										'<td>NOMOR NPE</td>'+
										'<td id="data_no_npe">'+data.datasource.NONPE+'</td>'+
									'</tr>' +
									'<tr>' +
										'<td>TANGGAL NPE</td>'+
										'<td id="data_tgl_npe">'+data.datasource.TGLNPE+'</td>'+
									'</tr>'+
									'<tr>' +
										'<td>NAMA EKSPORTIR</td>'+
										'<td>'+data.datasource.NAMA_EKS+'</td>'+
									'</tr>' +
									'<tr>' +
										'<td>NPWP EKSPORTIR</td>'+
										'<td>'+data.datasource.NPWP_EKS+'</td>'+
									'</tr>' +
									'<tr>' +
										'<td>NOMOR PEB</td>'+
										'<td>'+data.datasource.NO_DAFTAR+'</td>'+
									'</tr>' +
									'<tr>' +
										'<td>TANGGAL PEB</td>'+
										'<td>'+data.datasource.TGL_DAFTAR+'</td>'+
									'</tr>';
				$('#npe_landing').empty();
				$('#npe_landing').append(deskripsi_npe);
				$('#button_npe').show();
			}
			else{
				var error_deskripsi = '<tr>' +
										'<td>Error</td>'+
										'<td>'+data.errors[0]+'</td>'+
									'</tr>';
				$('#npe_landing').empty();
				$('#npe_landing').append(error_deskripsi);
				console.log(data.errors);
				$('#button_npe').show();
			}
			
		}, 'json');
		
	});
	
	
	
	$('#simpan').click(function(){
		destroy_all_validation_popovers();
		
		var is_error = false;
		var ar =  $('#VISIT_ID').val().split(' ');
		var visit_id = ar[0];
		var param = {
			'VISIT_ID' : visit_id,
			// 'BL_NUMBER' : $('#BL_NUMBER').val(),
			// 'BL_NUMBER_DATE' : $('#BL_NUMBER_DATE').val(),
			'BL_NUMBER' : $('#BL_NUMBER').val(),
			'BL_NUMBER_DATE' : $('#BL_NUMBER_DATE').val(),
			'HOUSE_BL_NUMBER' : $('#HOUSE_BL_NUMBER').val(),
			'HOUSE_BL_NUMBER_DATE' : $('#HOUSE_BL_NUMBER_DATE').val(),
			'TYPE_CARGO' : $('#TYPE_CARGO').val(),
			'BRUTO' : $('#BRUTO').val(),
			'JUMLAH' : $('#JUMLAH').val(),
			'NPWP' : $('#NPWP').val(),
			'VIN' : [],
			'CUSTOMS_NUMBER' : [],
			'CUSTOMS_DATE' : [],
			'NO_NPE' : $("#data_no_npe").text(),
			'NPE_DATE' : $("#data_tgl_npe").text()
		}
		
		$('.vin_row').each(function(){
			param.VIN.push($(this).attr('id'));
			if($(this).find(".no_peb").html() == "null"){
				param.CUSTOMS_NUMBER.push($('#CUSTOMS_NUMBER').val());
				param.CUSTOMS_DATE.push($('#CUSTOMS_DATE').val());
			}
			else{
				param.CUSTOMS_NUMBER.push($(this).find(".no_peb").html());
				param.CUSTOMS_DATE.push($(this).find(".tgl_peb").html());
			}
			
		});
		
		
		
		console.log(param);
		
		if(!param.VISIT_ID){
			$('#VISIT_ID').parent().addClass('has-error');
			add_validation_popover('#VISIT_ID', 'Visit ID Harus dipilih');
			
			is_error = true;
		}
		
		// if(!param.BL_NUMBER){
		// 	$('#BL_NUMBER').parent().addClass('has-error');
		// 	add_validation_popover('#BL_NUMBER', 'Nomor BL Harus diisi');
			
		// 	is_error = true;
		// }
		
		// if(!param.BL_NUMBER_DATE){
		// 	$('#BL_NUMBER_DATE').parent().addClass('has-error');
		// 	add_validation_popover('#BL_NUMBER_DATE', 'Tanggal BL Harus diisi');
			
		// 	is_error = true;
		// }

		if(!param.BL_NUMBER){
			$('#BL_NUMBER').parent().addClass('has-error');
			add_validation_popover('#BL_NUMBER', 'Nomor BL Harus diisi');
			
			is_error = true;
		}
		
		if(!param.BL_NUMBER_DATE){
			$('#BL_NUMBER_DATE').parent().addClass('has-error');
			add_validation_popover('#BL_NUMBER_DATE', 'Tanggal BL Harus diisi');
			
			is_error = true;
		}

		if(!param.HOUSE_BL_NUMBER){
			$('#HOUSE_BL_NUMBER_HOUSE').parent().addClass('has-error');
			add_validation_popover('#HOUSE_BL_NUMBER', 'Nomor BL Harus diisi');
			
			is_error = true;
		}
		
		if(!param.HOUSE_BL_NUMBER_DATE){
			$('#HOUSE_BL_NUMBER_DATE').parent().addClass('has-error');
			add_validation_popover('#HOUSE_BL_NUMBER_DATE', 'Tanggal BL Harus diisi');
			
			is_error = true;
		}
		
		if(!param.TYPE_CARGO){
			$('#TYPE_CARGO').parent().addClass('has-error');
			add_validation_popover('#TYPE_CARGO', 'Jenis Cargo Harus diisi');
			
			is_error = true;
		}
		
		if(!param.BRUTO && param.TYPE_CARGO != 'CBU'){
			$('#BRUTO').parent().addClass('has-error');
			add_validation_popover('#BRUTO', 'BRUTO Harus diisi');
			
			is_error = true;
		}

		if(!param.JUMLAH){
			$('#JUMLAH').parent().addClass('has-error');
			add_validation_popover('#JUMLAH', 'JUMLAH Harus diisi');
			
			is_error = true;
		}
		
		if($('.vin_row').length == 0){
			$('#search_vin').parent().addClass('has-error');
			add_validation_popover('#search_vin', 'Minimal anda harus menambahkan 1 VIN', 'top');
			
			is_error = true;
		}
		
		if(is_error){
			sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
		}else{		
			$('#simpan_load').show();
		
			var url = bs.siteURL + 'tps_online/consignment/simpan/' + bs.token;
			
			$.post(url, param, function(data){
				$('#simpan_load').hide();
				
				if(data.success){
					sc_alert('Sukses', data.msg);
					reset_form();
				}else{
					sc_alert('ERROR', data.msg);
				}
			}, 'json');
		}
		
		return false;
	});
	
	initialize();
	lookup_visit_id();
});

</script>

<script type="text/javascript">

		jQuery(function($){
			$(document).ajaxSend(function() {
					$("#overlay").fadeIn(300);　
			});


		$('#import').click(function(){
		
			var nmfile_vin = $('#nmfile_vin').val();
			var nmfile_bl = $('#nmfile_bl').val();

			if(nmfile_vin === '' && nmfile_bl === '' ){
				alert('Validasi error');
				// console.log(data);
			}else{
				// var jsonString = JSON.stringify(data);

				request = $.ajax({
					type : 'post',
					url  : bs.siteURL + 'tps_online/Upload_manifest/import/' + bs.token,
					dataType: 'json',
					data : {
							 nmfile_vin : nmfile_vin,
							 nmfile_bl : nmfile_bl,
					},
					cache : false
				});
				request.done(function(response, textStatus, jqXHR){
					// json = JSON.parse(response);
					setTimeout(function(){
								$("#overlay").fadeOut(300);
							},500);

					console.log(response.salah);
					if(response.salah > 0){
						$('#penjelasan').text('VIN yang berhasil di Update : '+ response.benar +' dan VIN gagal di Update : ' + response.salah);
                    	$('#text').text('Berikut data yang gagal di Update : ')
                    	$.each( response.vin_salah, function(index, value ) {
							$("#result").append(value + '</br>');

						});
					}else{
						// json = JSON.parse(response);
                    	$('#penjelasan').text('VIN yang berhasil di Update: '+ response.benar +' dan data gagal di Update: ' + response.salah);
					}
					$('#myModal').modal({backdrop: 'static', keyboard: false})  
                    $('#myModal').modal('show'); 
				});
				request.fail(function(jqXHR, textStatus, errorThrown){
					console.error("The following error occurred: "+
                textStatus, errorThrown);
				});
			}
		});
	});	
			
		
		

	</script>

	<div class="modal fade" id="myModal" role="dialog">
		    <div class="modal-dialog modal-lg">
		      <div class="modal-content">
		        <div class="modal-header">
		        	<a class="close" data-dimiss="modal" href="<?php echo base_url('tps_online/Upload_manifest/')?>">&times;</a>
		          <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
		          <h4 class="modal-title">Respon Update Manifest</h4>
		        </div>
		        <div class="modal-body">
		          <p id="penjelasan"></p>
		          <p id="text"></p>
		          <div id="result"></div>
		        </div>
		        <div class="modal-footer">
		          <a class="btn btn-default" href="<?php echo base_url('tps_online/Upload_manifest')?>">Close</a>
		          <!-- <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button> -->
		        </div>
		      </div>
		    </div>
		  </div>
	
</body>
</html>
