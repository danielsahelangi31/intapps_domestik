<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
	<style>
		.border-left-warning {
		    border-left: .25rem solid #f6c23e !important;
		}

		.mb-4, .my-4 {
		    margin-bottom: 1.5rem !important;
		}

		.card {
		    position: relative;
		    display: -webkit-box;
		    display: -ms-flexbox;
		    display: flex;
		    -webkit-box-orient: vertical;
		    -webkit-box-direction: normal;
		    -ms-flex-direction: column;
		    flex-direction: column;
		    min-width: 0;
		    word-wrap: break-word;
		    background-color: #fff;
		    background-clip: border-box;
		    border: 1px solid #e3e6f0;
		        border-left-color: rgb(227, 230, 240);
		        border-left-style: solid;
		        border-left-width: 1px;
		    border-radius: .35rem;
		}

		*, ::after, ::before {
		    -webkit-box-sizing: border-box;
		    box-sizing: border-box;
		}

		.card-header:first-child {
		    border-radius: calc(.35rem - 1px) calc(.35rem - 1px) 0 0;
		}

		.pb-3, .py-3 {
		    padding-bottom: 1rem !important;
		}

		.pt-3, .py-3 {
		    padding-top: 1rem !important;
		}

		.align-items-center {
		    -webkit-box-align: center !important;
		    -ms-flex-align: center !important;
		    align-items: center !important;
		}

		.card-header {
		    padding: .75rem 1.25rem;
		        padding-top: 0.75rem;
		        padding-bottom: 0.75rem;
		    margin-bottom: 0;
		    color: inherit;
		    background-color: #f8f9fc;
		    border-bottom: 1px solid #e3e6f0;
		}

		.card-body {
		    -webkit-box-flex: 1;
		    -ms-flex: 1 1 auto;
		    flex: 1 1 auto;
		    padding: 1.25rem;
		}

		.text-white {
		    color: #fff !important;
		}

		.text-warning {
		    color: #f6c23e !important;
		}

		.text-success {
		    color: #1cc88a !important;
		}

		.text-danger {
		    color: #e74a3b !important;
		}

		.text-primary {
		    color: #4e73df !important;
		}

		.shadow {
		    -webkit-box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
		    box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
		}

		.bg-primary {
		    background-color: #4e73df !important;
		}

		.bg-success {
		    background-color: #1cc88a !important;
		}

		.bg-warning {
		    background-color: #f6c23e !important;
		}

		.bg-danger {
		    background-color: #e74a3b !important;
		}

		.bg-purple {
		    background-color: #ff4a6b !important;
		}

		.bg-blue {
		    background-color: #96b3d9 !important;
		}

		.bg-dark-blue {
		    background-color: #96b3d9 !important;
		}

		.bg-gray {
		    background-color: #6d8ba6 !important;
		}

		.space{
			margin-top: 10px;
			margin-bottom: 5px;
		}

		.label-right{
			text-align: right;
		}
	</style>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			<div class="col-md-6">
				<div class="col-md-12 card border-left-warning">
					<div class="card-header text-primary"><strong>Data Kapal</strong></div>
					<div class="col-md-12 bg-primary text-white card shadow">	
		              <div class="form-group">
		              	<label class="col-lg-2 control-label" style="text-align: right;">Kapal :</label>
		                <select id="VISIT_ID" name="VISIT_ID" oninput="fillData()" class="col-lg-10 form-control select2" style="width: 80%;">
		                  <option selected disabled>Pilih Kapal</option>
<?php
	foreach ($ALL_KAPAL as $key) {
?>
		                  <option value="<?=$key['VISIT_ID']?> <?=$key['VISIT_NAME']?>"><?=$key['VISIT_NAME']?> <?=$key['VOYAGE_IN']?>/<?=$key['VOYAGE_OUT']?></option>
<?php
	}
?>
		                </select>
		              </div>					
						<!-- <div class="form-group">
							<label class="col-lg-4 control-label" style="text-align: right;">Kapal :</label>
							<div class="col-lg-8">
								<?php 
								$dataCon= array();
								foreach ($VISIT_ID_DS as $row) {
									$dataCon[] = $row->VISIT_ID.' '.$row->VISIT_NAME;
								}
								 ?>								
								<input type="text" autocomplete="off" class="form-control" id="VISIT_ID" name="VISIT_ID" oninput="fillData()" value="<?php echo $VISIT_ID  ?>" placeholder="5 Karakter Terakhir VISIT ID"/>							
							</div>
						</div> -->
						<!-- <div><label class="col-lg-2 label-right">Voyage :</label><strong class="col-lg-10" id="VOY_IN"></strong></div> -->
					</div>
					<div>
						<table class="table">
							<thead>
								<tr>
									<th class="bg-warning text-white shadow"><div align="center">Arrival</div></th>
									<th class="bg-success text-white shadow"><div align="center">Operational</div></th>
									<th class="bg-danger text-white shadow"><div align="center">Completed</div></th>
									<th class="bg-purple text-white shadow"><div align="center">Departure</div></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="bg-warning text-white shadow"><div align="center"><strong id="ARRIVAL"></strong></div></td>
									<td class="bg-success text-white shadow"><div align="center"><strong id="OPERATIONAL"></strong></div></td>
									<td class="bg-danger text-white shadow"><div align="center"><strong id="COMPLETION"></strong></div></td>
									<td class="bg-purple text-white shadow"><div align="center"><strong id="DEPARTURE"></strong></div></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div>
						<table class="table">
							<thead>
								<tr>
									<th class="bg-blue text-white shadow"><div align="center">Jumlah BL</div></th>
									<th class="bg-dark-blue text-white shadow"><div align="center">Inward BC. 1.1</div></th>
									<th class="bg-gray text-white shadow"><div align="center">Outward BC. 1.1</div></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="bg-blue text-white shadow"><div align="center"><strong id="JML_BL"></strong></div></td>
									<td class="bg-dark-blue text-white shadow"><div align="center"><strong id="INBC11"></strong></div></td>
									<td class="bg-gray text-white shadow"><div align="center"><strong id="OUTBC11"></strong></div></td>
								</tr>
							</tbody>
						</table>
					</div>
					<!-- <div><label class="col-lg-12">Arrival :</label><strong class="col-lg-12" id="ARRIVAL"></strong></div> -->
					<!-- <div><label class="col-lg-12">Completed :</label><strong class="col-lg-12" id="COMPLETION"></strong></div> -->
					<!-- <div><label class="col-lg-12">Operational :</label><strong class="col-lg-12" id="OPERATIONAL"></strong></div> -->
					<!-- <div><label class="col-lg-12">Departure :</label><strong class="col-lg-12" id="DEPARTURE"></strong></div> -->
					<!-- <div><label class="col-lg-12">Jumlah BL :</label><strong class="col-lg-12" id="JML_BL"></strong></div> -->
					<!-- <div><label class="col-lg-12">Inward BC. 1.1 :</label><strong class="col-lg-12" id="INBC11"></strong></div> -->
					<!-- <div><label class="col-lg-12">Outward BC. 1.1 :</label><strong class="col-lg-12" id="OUTBC11"></strong></div> -->
				</div>
				<div class="col-md-6 card border-left-warning">
					<div class="card-header text-success"><strong>VIN</strong></div>
					<div class="bg-success text-white card shadow">
						<div id="VIN" style="height: 250px;"></div>
						<!-- <div class="space"><label class="col-md-4 label-right" >Export :</label><strong class="col-md-8" id="EXPORT"></strong></div> -->
						<!-- <div class="space"><label class="col-md-4 label-right" >Import :</label><strong class="col-md-8" id="IMPORT"></strong></div> -->
					</div>
				</div>
				<div class="col-md-6 card border-left-warning">
					<div class="card-header text-warning"><strong>Type VIN</strong></div>
					<div class="bg-warning text-white card shadow">
						<div id="TYPEVIN" style="height: 250px;"></div>
						<!-- <div class="space"><label class="col-lg-4 label-right">CBU :</label><strong class="col-lg-8" id="CBU"></strong></div> -->
						<!-- <div class="space"><label class="col-lg-4 label-right">HH :</label><strong class="col-lg-8" id="HH"></strong></div> -->
						<!-- <div class="space"><label class="col-lg-4 label-right">Spareparts :</label><strong class="col-lg-8" id="SPAREPARTS"></strong></div> -->
					</div>
				</div>
				<div class="col-md-6 card border-left-warning">
					<div class="card-header text-primary"><strong>Based on NPE</strong></div>
					<div class="bg-primary text-white card shadow">
						<div id="NPE" style="height: 250px;"></div>
						<!-- <div class="space"><label class="col-lg-4 label-right">NPE :</label><strong class="col-lg-8" id="NPE"></strong></div> -->
						<!-- <div class="space"><label class="col-lg-4 label-right">Non NPE :</label><strong class="col-lg-8" id="NON_NPE"></strong></div> -->
					</div>
				</div>
				<div class="col-md-6 card border-left-warning">
					<div class="card-header text-success"><strong>Jumlah</strong></div>
					<div class="bg-success text-white card shadow">
						<div id="TOTAL" style="height: 250px;"></div>
						<!-- <div class="space"><label class="col-lg-4 label-right">On Terminal Import :</label><strong class="col-lg-8" id="OT_IM"></strong></div> -->
						<!-- <div class="space"><label class="col-lg-4 label-right">On Terminal Export :</label><strong class="col-lg-8" id="OT_EX"></strong></div> -->
						<!-- <div class="space"><label class="col-lg-4 label-right">Loaded :</label><strong class="col-lg-8" id="LOADED"></strong></div> -->
						<!-- <div class="space"><label class="col-lg-4 label-right">Left :</label><strong class="col-lg-8" id="LEFT"></strong></div> -->
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card border-left-warning" style="min-height: 550px;">
					<div class="card-header text-warning"><strong>Camera</strong></div>
					<div class="col-md-12 card shadow">
<script type="text/javascript" language="JavaScript">
	newImage = new Image();
	var url = "http://10.15.41.161:80/Streaming/Channels/1/picture?time="; //setting halaman awal pada saat dibuka
	// var url = "http://admin:IKTcam01@10.15.41.161:80/Streaming/Channels/1/picture?time="; //setting halaman awal pada saat dibuka
	
	function LoadNewImage()
	{
		var unique = new Date();
		document.images.webcam.src = newImage.src;
		newImage.src = url + unique.getTime();
	}

	function InitialImage()
	{
		var unique = new Date();
		newImage.onload = LoadNewImage;
		newImage.src =  url + unique.getTime();
		document.images.webcam.onload="";
	}
	
	
	
	function channel(url_,name)
	{	var unique = new Date();
		url = url_;
		document.getElementById('header').innerHTML = name;
		document.images.webcam.onload="";
		newImage.onload = LoadNewImage;
		newImage.src = url + unique.getTime() ;
		
	}
</script>

						<object 
		                    classid="clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921"  
		                    codebase="http://download.videolan.org/pub/videolan/vlc/last/win32/axvlc.cab" 
		                    id="vlc" 
		                    name="vlc" 
		                    class="vlcPlayer" 
		                    events="True"> 
		                    <param name="Src" value="rtsp://xxxxx:8080/Media/Live/Normal?camera=C_4&streamindex=1" /> <!-- ie --> 
		                    <param name="ShowDisplay" value="True" /> 
		                    <param name="AutoLoop" value="True" /> 
		                    <param name="AutoPlay" value="True" /> 
		                    <!-- win chrome and firefox--> 
		                    <embed id="vlcEmb"  type="application/x-google-vlc-plugin" 
		                    version="VideoLAN.VLCPlugin.2" autoplay="yes" loop="no" width="100%" height="280"
		                    target="rtsp://admin:IKTcam01@10.15.41.161:554/Streaming/Channels/102" ></embed> 
							<embed id="vlcEmb"  type="application/x-google-vlc-plugin" 
		                    version="VideoLAN.VLCPlugin.2" autoplay="yes" loop="no" width="100%" height="280"
		                    target="rtsp://admin:IKTcam02@10.15.41.162:554/Streaming/Channels/102" ></embed> 
		                </object>
						<!-- <img src="http://admin:IKTcam01@10.15.41.161:80/Streaming/Channels/1/picture" id="cctv" name="webcam" onload="InitialImage()" width="100%" alt="Maaf ... Sedang ada gangguan ..."> -->
					</div>
					<div class="col-md-12 bg-warning text-white card shadow">
						<div class="space"><label class="col-lg-4 label-right">VIN :</label><strong class="col-lg-8" id="DATA_VIN"></strong></div>
						<div class="space"><label class="col-lg-4 label-right">NPE :</label><strong class="col-lg-8" id="DATA_NPE"></strong></div>
						<div class="space"><label class="col-lg-4 label-right">Unit :</label><strong class="col-lg-8" id="DATA_UNIT"></strong></div>
						<div class="space"><label class="col-lg-4 label-right">Status :</label><strong class="col-lg-8" id="DATA_STATUS"></strong></div>
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
	$('.select2').select2()
	
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
					lookup_VIN_EI();
					lookup_type_VIN();
					lookup_NPE();
					lookup_data_jumlah();
					lookup_data_sum();
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
			var url = bs.siteURL + 'DashboardReal/get/' + bs.token;
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
					$('#VOY_IN').html(rec.VOYAGE_IN);
					$('#ARRIVAL').html(rec.ARRIVAL);
					$('#COMPLETION').html(rec.COMPLETION);
					$('#JML_BL').html(rec.JML_BL);
					$('#OPERATIONAL').html(rec.OPERATIONAL);
					$('#DEPARTURE').html(rec.DEPARTURE);
					$('#INBC11').html(rec.INWARD_BC11);
					$('#OUTBC11').html(rec.OUTWARD_BC11);
					
					// var edit_link_url = bs.siteURL + 'tps_online/kunjungan_kapal/view/' + rec.VISIT_ID;
					
					// $('#ship_edit_link').show().find('a').attr('href', edit_link_url);
				}else{
					sc_alert('Error', data.msg);
				}
			}, 'json');
		}else{
			$('#VISIT_NAME, #ETA, #ETD, #LOAD_PORT, #DISCHARGER_PORT').html('&nbsp;');
			$('#ship_edit_link').hide();
		}
	}

	function lookup_VIN_EI(){
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
			var url = bs.siteURL + 'DashboardReal/get_vin/' + bs.token;
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
					callVin(rec.JML_IM, rec.JML_EX);
					// $('#EXPORT').html(rec.JML_EX);
					// $('#IMPORT').html(rec.JML_IM);
					
					// var edit_link_url = bs.siteURL + 'tps_online/kunjungan_kapal/view/' + rec.VISIT_ID;
					
					// $('#ship_edit_link').show().find('a').attr('href', edit_link_url);
				}else{
					sc_alert('Error', data.msg);
				}
			}, 'json');
		}else{
			$('#VISIT_NAME, #ETA, #ETD, #LOAD_PORT, #DISCHARGER_PORT').html('&nbsp;');
			$('#ship_edit_link').hide();
		}
	}

	function lookup_type_VIN(){
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
			var url = bs.siteURL + 'DashboardReal/get_type/' + bs.token;
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
					callType(rec.JML_CBU, rec.JML_HH, rec.JML_PARTS);
					// $('#HH').html(rec.JML_HH);
					// $('#CBU').html(rec.JML_CBU);
					// $('#SPAREPARTS').html(rec.JML_PARTS);
					
					// var edit_link_url = bs.siteURL + 'tps_online/kunjungan_kapal/view/' + rec.VISIT_ID;
					
					// $('#ship_edit_link').show().find('a').attr('href', edit_link_url);
				}else{
					sc_alert('Error', data.msg);
				}
			}, 'json');
		}else{
			$('#VISIT_NAME, #ETA, #ETD, #LOAD_PORT, #DISCHARGER_PORT').html('&nbsp;');
			$('#ship_edit_link').hide();
		}
	}

	function lookup_NPE(){
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
			var url = bs.siteURL + 'DashboardReal/get_npe/' + bs.token;
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
					callNpe(rec.JML_NPE, rec.JML_NON);
					// $('#NPE').html(rec.JML_NPE);
					// $('#NON_NPE').html(rec.JML_NON);
					
					// var edit_link_url = bs.siteURL + 'tps_online/kunjungan_kapal/view/' + rec.VISIT_ID;
					
					// $('#ship_edit_link').show().find('a').attr('href', edit_link_url);
				}else{
					sc_alert('Error', data.msg);
				}
			}, 'json');
		}else{
			$('#VISIT_NAME, #ETA, #ETD, #LOAD_PORT, #DISCHARGER_PORT').html('&nbsp;');
			$('#ship_edit_link').hide();
		}
	}

	function lookup_data_jumlah(){
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
			var url = bs.siteURL + 'DashboardReal/get_data_jumlah/' + bs.token;
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
					callJmlh(rec.JML_IM, rec.JML_EX, rec.JML_LOAD, rec.JML_LEFT);
					// $('#OT_IM').html(rec.JML_IM);
					// $('#OT_EX').html(rec.JML_EX);
					// $('#LEFT').html(rec.JML_LEFT);
					// $('#LOADED').html(rec.JML_LOAD);
					
					// var edit_link_url = bs.siteURL + 'tps_online/kunjungan_kapal/view/' + rec.VISIT_ID;
					
					// $('#ship_edit_link').show().find('a').attr('href', edit_link_url);
				}else{
					sc_alert('Error', data.msg);
				}
			}, 'json');
		}else{
			$('#VISIT_NAME, #ETA, #ETD, #LOAD_PORT, #DISCHARGER_PORT').html('&nbsp;');
			$('#ship_edit_link').hide();
		}
	}

	function lookup_data_sum(){
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
			var url = bs.siteURL + 'DashboardReal/get_data_sum/' + bs.token;
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
					$('#DATA_VIN').html(rec.VIN);
					$('#DATA_NPE').html(rec.NO_NPE);
					$('#DATA_UNIT').html(rec.MODEL_NAME);
					
					// var edit_link_url = bs.siteURL + 'tps_online/kunjungan_kapal/view/' + rec.VISIT_ID;
					
					// $('#ship_edit_link').show().find('a').attr('href', edit_link_url);
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
	$('#VISIT_ID').change(lookup_VIN_EI);
	$('#VISIT_ID').change(lookup_type_VIN);
	$('#VISIT_ID').change(lookup_NPE);
	$('#VISIT_ID').change(lookup_data_jumlah);
	$('#VISIT_ID').change(lookup_data_sum);

		
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
function callVin(impor, ekspor){
	Highcharts.setOptions({
        colors: ['#4e73df','#1cc88a','#f6c23e','#e74a3b']
    });
	Highcharts.chart('VIN', {
	    title: {
	        text: ''
	    },
	    chart: {
	        type: 'column'
	    },
	    xAxis: {
	        categories: [
	         	''
	           
	        ],
	        crosshair: true
	    },
	    yAxis: {
	        min: 0
	    },
	    tooltip: {
	        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
	        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
	            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
	        footerFormat: '</table>',
	        shared: true,
	        useHTML: true
	    },
	    plotOptions: {
	        column: {
	            pointPadding: 0.2,
	            borderWidth: 0
	        }
	    },
	    series: [{
	        name: 'IMPORT',
	        data: [parseInt(impor)]
	    }, {
	        name: 'EXPORT',
	        data: [parseInt(ekspor)]
	    }]
	});
}

function callNpe(NPE, NON_NPE){
	Highcharts.setOptions({
        colors: ['#4e73df','#1cc88a','#f6c23e','#e74a3b']
    });
	Highcharts.chart('NPE', {
	    title: {
	        text: ''
	    },
	    chart: {
	        type: 'column'
	    },
	    xAxis: {
	        categories: [
	         	''
	           
	        ],
	        crosshair: true
	    },
	    yAxis: {
	        min: 0
	    },
	    tooltip: {
	        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
	        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
	            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
	        footerFormat: '</table>',
	        shared: true,
	        useHTML: true
	    },
	    plotOptions: {
	        column: {
	            pointPadding: 0.2,
	            borderWidth: 0
	        }
	    },
	    series: [{
	        name: 'NPE',
	        data: [parseInt(NPE)]
	    }, {
	        name: 'NON NPE',
	        data: [parseInt(NON_NPE)]
	    }]
	});
}

function callType(cbu, hh, parts){
	Highcharts.setOptions({
        colors: ['#4e73df','#1cc88a','#f6c23e','#e74a3b']
    });
	Highcharts.chart('TYPEVIN', {
	    title: {
	        text: ''
	    },
	    chart: {
	        type: 'column'
	    },
	    xAxis: {
	        categories: [
	         	''
	           
	        ],
	        crosshair: true
	    },
	    yAxis: {
	        min: 0
	    },
	    tooltip: {
	        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
	        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
	            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
	        footerFormat: '</table>',
	        shared: true,
	        useHTML: true
	    },
	    plotOptions: {
	        column: {
	            pointPadding: 0.2,
	            borderWidth: 0
	        }
	    },
	    series: [{
	        name: 'CBU',
	        data: [parseInt(cbu)]
	    }, {
	        name: 'HH',
	        data: [parseInt(hh)]
	    }, {
	        name: 'Spare Parts',
	        data: [parseInt(parts)]
	    }]
	});
}

function callJmlh(im, ex, load, left){
	Highcharts.setOptions({
        colors: ['#4e73df','#1cc88a','#f6c23e','#e74a3b']
    });
	Highcharts.chart('TOTAL', {
	    chart: {
	        plotBackgroundColor: null,
	        plotBorderWidth: null,
	        plotShadow: false,
	        type: 'pie'
	    },
	    title: {
	        text: ''
	    },
	    tooltip: {
	        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	    },
	    plotOptions: {
	        pie: {
	            allowPointSelect: true,
	            cursor: 'pointer',
	            dataLabels: {
	                enabled: false
	            },
	            showInLegend: true
	        }
	    },
	    series: [{
	        name: 'Jumlah',
	        colorByPoint: true,
	        data: [{
	            name: 'On Terminal Import',
	            y: parseInt(im)
	        }, {
	            name: 'On Terminal Export',
	            y: parseInt(ex)
	        }, {
	            name: 'Loaded',
	            y: parseInt(load)
	        }, {
	            name: 'Left',
	            y: parseInt(left)
	        }]
	    }]
	});
	Highcharts.chart('TOTAL', {
	    title: {
	        text: ''
	    },
	    chart: {
	        type: 'column'
	    },
	    xAxis: {
	        categories: [
	         	''
	           
	        ],
	        crosshair: true
	    },
	    yAxis: {
	        min: 0
	    },
	    tooltip: {
	        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
	        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
	            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
	        footerFormat: '</table>',
	        shared: true,
	        useHTML: true
	    },
	    plotOptions: {
	        column: {
	            pointPadding: 0.2,
	            borderWidth: 0
	        }
	    },
	    series: [{
	        name: 'On Terminal Import',
	        data: [parseInt(im)]
	    }, {
	        name: 'On Terminal Export',
	        data: [parseInt(ex)]
	    }, {
	        name: 'Loaded',
	        data: [parseInt(load)]
	    }, {
	        name: 'Left',
	        data: [parseInt(left)]
	    }]
	});
}
</script>    
</body>
</html>