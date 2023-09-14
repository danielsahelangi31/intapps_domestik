<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">

			<div class="row">
            	<div class="col-md-8">
                	<h2>Input  Bill of Lading</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">

					</div>
				</div>
			</div>

			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>


			<div class="row">
				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Pilih Kunjungan Kapal</legend>
						<div class="form-group">
							<label class="col-lg-4 control-label">Visit ID</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="VISIT_ID" name="VISIT_ID" value="<?php echo $VISIT_ID;?>" onClick="showModal();" />
                                
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
							<!--<a href="#" target="_blank">Edit Kapal</a>-->
						</div>
					</fieldset>
				</div>
				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Bill Of Lading</legend>
						<div class="form-group">
							<label class="col-lg-4 control-label">No POS BL</label>
							<div class="col-lg-8">
                            <input type="hidden" class="form-control" name="VISIT_ID" value="<?php echo $VISIT_ID;?>" />
								<input type="text" class="form-control" id="BL_NUMBER" name="POS_BL" value="<?php echo @$detail[0]->POS_BL;?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Tanggal BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control date" id="BL_NUMBER_DATE" name="TGL_BL" value="<?php echo @$detail[0]->TGL_BL;?>" required />
							</div>
						</div>
						<div class="form-group">
							<label class="col-lg-4 control-label">Nomor House BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="HOUSE_BL_NUMBER" name="HOUSE_BL_NUMBER"  value="<?php echo @$detail[0]->HOUSE_BL_NUMBER;?>" required />
							</div>
						</div>
                        <div class="form-group">
							<label class="col-lg-4 control-label">Nomor Master BL</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="HOUSE_BL_NUMBER" name="MASTER_BL_NUMBER" value="<?php echo @$detail[0]->MASTER_BL_NUMBER;?>" required />
							</div>
						</div>						
						<div class="form-group">
							<label class="col-lg-4 control-label">Jenis Cargo</label>
							<div class="col-lg-8">
								<select class="form-control" id="TYPE_CARGO" name="JENIS_MUATAN" required>
									<option value="">-- Pilih --</option>
									<?php
									foreach($TYPE_CARGO_DS as $row){
									?>
									<option value="<?php echo $row->CUSTOMS_CODE ?>" <?php if(@$detail[0]->JENIS_MUATAN==$row->CUSTOMS_CODE) echo 'selected';?>>
									<?php echo $row->CUSTOMS_CODE.' '.$row->DESCRIPTION ?></option>
									<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group ui-widget">
							<label class="col-lg-4 control-label" for="ORGANIZATION">Nama Perusahaan</label>
							<div class="col-lg-8">
								<input type="text" class="form-control" id="ORGANIZATION" name="NAMA_PERUSAHAAN" value="<?php echo @$detail[0]->NAMA_PERUSAHAAN;?>" required />
							</div>
						</div>

						<div class="form-group">
							<label class="col-lg-4 control-label">Bruto (Kg)</label>
							<div class="col-lg-8">
								<input type="number" class="form-control" id="JML_BRUTO" name="JML_BRUTO" value="<?php echo @$detail[0]->JML_BRUTO;?>" required  />
							</div>
						</div>
                        <div class="form-group">
							<label class="col-lg-4 control-label">Jumlah Muatan</label>
							<div class="col-lg-8">
								<input type="number" class="form-control" id="JML_MUATAN" name="JML_MUATAN" value="<?php echo @$detail[0]->JML_MUATAN;?>"  />
							</div>
						</div>
                        <div class="form-group">
							<label class="col-lg-4 control-label">Jumlah Volume</label>
							<div class="col-lg-8">
                            <input type="number" class="form-control" id="JML_VOLUME" name="JML_VOLUME" value="<?php echo @$detail[0]->JML_VOLUME;?>" required />
							</div>
						</div>
                        <div class="form-group">
							<div class="col-lg-8">
								<button type="submit" class="btn btn-primary"><?php echo $button;?></button>
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
						

						<div class="col-lg-12 alpha beta">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<th>No POS BL</th>
										<th>Tgl BL</th>
										<th>No. House BL</th>
										<th>No. Master BL</th>
										<th>Jenis Cargo</th>
										<th>Perusahaan</th>
                                        <th>Jml Muatan</th>
										<th>Jml Bruto</th>
                                        <th>Jml Volume</th>
                                        <th></th>
									</tr>
								</thead>
								<tbody id="vin_landing">
                                	<?php if(!@$blList){ ?>
									<tr id="vin_no_data"><td colspan="8"><em>Belum ada BL yang ditambahkan</em></td><tr>
									<?php } else {?>
                                    <?php foreach($blList as $bl){?>
                                    <tr>
                                    	<td><?php echo $bl->POS_BL;?></td>
                                        <td><?php echo date("j F Y",strtotime($bl->TGL_BL));?></td>
                                        <td><?php echo $bl->HOUSE_BL_NUMBER;?></td>
                                        <td><?php echo $bl->MASTER_BL_NUMBER;?></td>
                                        <td><?php echo $bl->JENIS_MUATAN;?></td>
                                        <td><?php echo $bl->NAMA_PERUSAHAAN;?></td>
                                        <td><?php echo $bl->JML_MUATAN;?></td>
                                        <td><?php echo $bl->JML_BRUTO;?></td>  
                                        <td><?php echo $bl->JML_VOLUME;?></td> 
                                        <td>
                                        <a href="<?php echo site_url('tps_online/notifikasi/input_bl/'.$VISIT_ID.'/'.$bl->MASTER_BL_NUMBER);?>" class="btn btn-info btn-sm">Edit</a>
                                        <a href="<?php echo site_url('tps_online/notifikasi/hapus_bl/'.$VISIT_ID.'/'.$bl->MASTER_BL_NUMBER);?>" class="btn btn-danger btn-sm">Hapus</a>
                                        </td> 
                                     </tr>
                                    <?php }?>
                                    <?php }?>
                                </tbody>
							</table>
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
						<!--<a href="#" class="btn btn-primary" id="simpan">Simpan</a>-->
					</div>
				</div>
			</div>

		</div><!-- /.container -->
	</div>
<!-- Modal -->
<div id="visitID" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">List Visit ID</h4>
      </div>
      <div class="modal-body">
        
       		<table class="table" id="visitTable">
            	<thead>
                	<tr>
                    	<th>VISIT ID</th>
                        <th>VESSEL NAME</th>
                        <th>ETA</th>
                        <th>ETD</th>
                    </tr>
                </thead>
                <tbody>
                	<?php 

						foreach($datasource as $dt){?>
                    <tr>
                    	<td>
						<a href="javascript::void(0);" onClick="setVisitID('<?php echo $dt->VISIT_ID;?>')" >
						<?php echo $dt->VISIT_ID;?>
                        </a>
                        </td>   
                        <td><?php echo $dt->VISIT_NAME;?></td>   
                        <td><?php echo $dt->ETA;?></td>   
                        <td><?php echo $dt->ETD;?></td>   
                    </tr>
                     <?php }?>
                </tbody>
            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
    <?php $this->load->view('backend/elements/footer') ?>

<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
function showModal(){
		$("#visitID").modal("toggle");
	}
function setVisitID(id){
	document.location="<?php echo site_url('tps_online/notifikasi/input_bl');?>"+"/"+id;
}
</script>

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
		$("#visitTable").dataTable();
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
	
	$( "#VISIT_ID" ).click(function() {
		
	});


	function lookup_visit_id(){
		if($('.vin_row').length > 0){
			if(confirm('Ubah data visit id akan reset VIN yang sudah ditambahkan')){
				$('.vin_row').remove();
				$('#vin_no_data').show();
			}else{
				return false;
			}
		}

		var visit_id = $('#VISIT_ID').val();
		if(visit_id){
			var url = bs.siteURL + 'tps_online/kunjungan_kapal/get/' + bs.token;
			visit_id = visit_id.split("-");
			var param = {
				VISIT_ID : visit_id[0]
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
					$('#VISIT_NAME').html('');
					$('#ETA').html('');
					$('#ETD').html('');
					$('#LOAD_PORT').html('');
					$('#DISCHARGER_PORT').html('');
				}
			}, 'json');
		}else{
			$('#VISIT_NAME, #ETA, #ETD, #LOAD_PORT, #DISCHARGER_PORT').html('&nbsp;');
			$('#ship_edit_link').hide();
		}
	}

	// $('#VISIT_ID').change(lookup_visit_id);


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
		if($('#vin_landing #' + vin_true).length == 0){
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
		}
	}

	function search_vin_bulk(){
		$('#search_vin').addClass('ajax-load');

		var url = bs.siteURL + 'tps_online/consignment/get_bulk_vin/' + bs.token;
		var visit_id = $('#VISIT_ID').val().split("-");
		var param = {
			'VISIT_ID' : visit_id[0],
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

		var url = bs.siteURL + 'tps_online/consignment/get_vin/' + bs.token;
		var visit_id = $('#VISIT_ID').val().split("-");
		var param = {
			'VISIT_ID' : visit_id[0],
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
		$('#VISIT_ID, #BL_NUMBER, #BL_NUMBER_DATE, #TYPE_CARGO, #BRUTO, #search_vin').val('');
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
		var visit_id = $('#VISIT_ID').val().split("-");
		var param = {
			'VISIT_ID' : visit_id[0],
			'BL_NUMBER' : $('#BL_NUMBER').val(),
			'BL_NUMBER_DATE' : $('#BL_NUMBER_DATE').val(),
			'HOUSE_BL_NUMBER' : $('#HOUSE_BL_NUMBER').val(),
			'HOUSE_BL_NUMBER_DATE' : $('#HOUSE_BL_NUMBER_DATE').val(),
			'TYPE_CARGO' : $('#TYPE_CARGO').val(),
			'BRUTO' : $('#BRUTO').val(),
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
