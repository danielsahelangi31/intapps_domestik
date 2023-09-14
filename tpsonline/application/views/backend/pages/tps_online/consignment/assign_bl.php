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
                	<h2>Assign Bill of Lading</h2>
                	<?php 
                		// var_dump($this->session->userdata('demo.sc8d7gads'));die;

                	 ?>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>
			
			<?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>

			
			<div class="row">
				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Pilih Kunjungan Kapal</legend>
						<div class="form-group">
							<label class="col-lg-4 control-label">Visit ID</label>
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
						<div class="form-group">
							<label class="col-lg-4 control-label visit_id_loading">Nama Kapal</label>
							<div class="col-lg-8">
								<p class="form-control-static" id="VISIT_NAME"></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label visit_id_loading">Tiba<sup>1</sup> / Berangkat<sup>1</sup></label>
							<div class="col-lg-8">
								<p class="form-control-static"><span id="ETA"></span> / <span id="ETD"></span></p>
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label visit_id_loading">Load / Discharge</label>
							<div class="col-lg-8">
								<p class="form-control-static"><span id="LOAD_PORT"></span> / <span id="DISCHARGER_PORT"></span></p>
							</div>
						</div>
						
						<div id="ship_edit_link" class="pull-right" style="display:none">
							<a href="#" target="_blank">Edit Kapal</a>
						</div>
					</fieldset>
				</div>
				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Bill Of Lading</legend>
						<!-- <div class="form-group">
							<label class="col-lg-4 control-label">Nomor BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="BL_NUMBER" value="" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Tanggal BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control date" id="BL_NUMBER_DATE" value="" />
							</div>
						</div> -->
						<div class="form-group">
							<label class="col-lg-4 control-label">Nomor Master BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="BL_NUMBER" name="BL_NUMBER" value="" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Tanggal Master BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control date" id="BL_NUMBER_DATE" name="BL_NUMBER_DATE" value="" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Nomor House BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="HOUSE_BL_NUMBER" name="HOUSE_BL_NUMBER" value="" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Tanggal House BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control date" id="HOUSE_BL_NUMBER_DATE" name="HOUSE_BL_NUMBER_DATE" value="" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Jenis Cargo</label>
							<div class="col-lg-8">
								<select class="form-control" id="TYPE_CARGO" name="TYPE_CARGO" onChange="findSelected()">
									<option value="">-- Pilih --</option>
									<?php
									foreach($TYPE_CARGO_DS as $row){
									?>
									<option value="<?php echo $row->CUSTOMS_CODE ?>"><?php echo $row->CUSTOMS_CODE.' '.$row->DESCRIPTION ?></option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group ui-widget">
							<label class="col-lg-4 control-label" for="ORGANIZATION">Nama Perusahaan</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="ORGANIZATION" name="ORGANIZATION" value="" />
							</div>
						</div>
						
						<div class="form-group ui-widget">
							<label class="col-lg-4 control-label" for="NPWP">NPWP</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="NPWP" name="NPWP" value="" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-lg-4 control-label">Bruto (Kg)</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="BRUTO" name="BRUTO" value="" />
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Jumlah </label>
							<div class="col-lg-8">
								<input type="number" class="form-control" id="JUMLAH" name="JUMLAH" value="" />
							</div>
						</div>
						
						<div class="form-group peb">
							<label class="col-lg-4 control-label text-danger">Nomor PEB</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="CUSTOMS_NUMBER" name="CUSTOMS_NUMBER" value="" />
							</div>
						</div>
						
						<div class="form-group peb">
							<label class="col-lg-4 control-label text-danger">Tanggal PEB</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="CUSTOMS_DATE" name="CUSTOMS_DATE" value="" />
							</div>
						</div>
					</fieldset>
				</div>
			</div>
						
			<p><sup>1</sup> Waktu yang ditampilkan adalah waktu setempat</p>
			
			
			<?php echo form_close() ?>
			
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Kargo</legend>
						
						<div class="form-inline" role="form">
							<div class="pull-right">
								<label>
									<input type="checkbox" id="search_bulk" value="1" />
									Masukkan banyak VIN sekaligus
								</label>
							</div>
							<div class="form-group">
								<input type="search_vin" class="form-control" id="search_vin" placeholder="Masukkan VIN Number lalu Enter" style="width:300px;">
							</div>
							<button type="button" class="btn btn-primary" id="add_vin">Tambahkan</button>
						</div>
						
						<br/>
						
						<div class="col-lg-12 alpha beta">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<th>No</th>
										<th>VIN</th>
										<th>VISIT ID</th>
										<th>Model</th>
										<th>Direction</th>
										<th>No. PEB</th>
										<th>Tanggal PEB</th>
										<th>Tindakan</th>
									</tr>
								</thead>
								<tbody id="vin_landing">
									<tr id="vin_no_data"><td colspan="8"><em>Belum ada VIN yang ditambahkan</em></td><tr>
								</tbody>
							</table>
						</div>
						<div class="col-lg-12 alpha beta">
							<table class="table table-bordered table-hover">
								<tbody id="npe_landing">
									<tr id="npe_no_data"><td colspan="2"><em>Silahkan Request NPE</em></td><tr>
								</tbody>
							</table>
							<div class="pull-right" id="button_npe">
								<button type="button" class="btn btn-primary" id="get_npe">Request NPE</button>
							</div>
						</div>
					</fieldset>
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-6">
					
				</div>
				<div class="col-lg-6">
					<div class="pull-right">
						<div class="btn ajax-load" id="simpan_load" style="display:none"></div>
						<a href="#" class="btn btn-primary" id="simpan">Simpan</a>
					</div>
				</div>
			</div>

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>

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
				'	<td>' + rec.VISIT_ID + '</td>' +
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

		if(param.JUMLAH != $('.vin_row').length){
			$('#JUMLAH').parent().addClass('has-error');
			add_validation_popover('#JUMLAH', 'VIN yang ditambahkan harus Sesuai dengan JUMLAH yang diinput');
			
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
</body>
</html>