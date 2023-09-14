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
                	<h2>Dokumen EDI</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>

			<?php echo form_open(null, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>

			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Data Kapal / PKK</legend>
						
                        <p><em>Periksa Kembali data delivery order anda</em></p>
                        
                        <div class="col-lg-6">
							<div class="form-group">
								<label class="col-lg-4 control-label">Shipping Line</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="kode_shipping_line">PT. Samudera Indonesia</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Origin / Dest</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="pol_pod">SIN/JKT</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Voyage / Vessel Name</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="voyage_vessel">088E/MV. Cape Fulmar</p>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="col-lg-4 control-label">Nomor PKK</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="arrival_date">PKKK00111101</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Renc. Tgl Kedatangan</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="arrival_date">10-Okt-2013</p>
								</div>
							</div>
							<div class="form-group">
								<label class="col-lg-4 control-label">Renc. Tgl Berangkat</label>
								<div class="col-lg-8">
									<p class="form-control-static" id="arrival_date">12-Okt-2013</p>
								</div>
							</div>
							
							
						</div>
					</fieldset>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Unggah EDI</legend>
						
                        <div class="row" style="margin-bottom:10px">
							<div class="col-md-6">
								<p><em>Daftar unggahan dokumen EDI</em></p>
							</div>
							<div class="col-md-6">
								<div class="pull-right">
									<a href="<?php echo site_url('delivery_request_og/add') ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Tambah Unggahan</a>
								</div>
							</div>
						</div>
                        <div class="col-lg-12">
                        	<div class="row">
								<table class="table table-bordered">
                                    <thead>
										<tr>
											<th><input type="checkbox" id="check_all" value=""> Pilih</th>
											<th>Nomor Unggahan</th>
											<th>Jenis Dokumen</th>
											<th>Waktu Unggah</th>
											<th>Nama File</th>
											<th>Keterangan</th>
										</tr>
									</thead>
									<tbody id="container_landing">
										<tr>
											<td colspan="6"><em>Belum ada dokumen diunggah</em></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</fieldset>					
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Generate EDI</legend>
						<div class="row" style="margin-bottom:10px">
							<div class="col-md-6">
								<p><em>Daftar pesanan dokumen EDI. Anda dapat memantau status dokumen EDI yang Anda pesan.</em></p>
							</div>
							<div class="col-md-6">
								<div class="pull-right">
									<a href="<?php echo site_url('delivery_request_og/add') ?>" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Tambah Pesanan</a>
								</div>
							</div>
						</div>
                        
                        <div class="col-lg-12">
                        	<div class="row">
								<table class="table table-bordered">
                                    <thead>
										<tr>
											<th><input type="checkbox" id="check_all" value=""> Pilih</th>
											<th>Nomor Unggahan</th>
											<th>Jenis Dokumen</th>
											<th>Waktu Unggah</th>
											<th>Nama File</th>
											<th>Keterangan</th>
										</tr>
									</thead>
									<tbody id="container_landing">
										<tr>
											<td colspan="6"><em>Belum ada dokumen dipesan</em></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						
						<a class="btn btn-danger fr" href="<?php echo site_url('delivery_request_og/listview') ?>">Unduh Semua Dokumen</a>
					</fieldset>					
				</div>
			</div>
			
			<div class="row">
				<div class="col-lg-12">
					<div class="pull-right">
						<button class="btn btn-primary fr" type="submit" name="simpan_kirim" id="simpan_kirim" value="1"><span class="glyphicon glyphicon-floppy-saved"></span> Simpan Dokumen</button>
						<a class="btn btn-default fr" href="<?php echo site_url('delivery_request_og/listview') ?>">Kembali</a>
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
	var do_valid = false;
	
	$('#check_all').click(function(){
		if($(this).prop('checked')){
			$('#container_landing input[name=coreor_line_id\\[\\]]').prop('checked', 'checked');
		}else{	
			$('#container_landing input[name=coreor_line_id\\[\\]]').removeAttr('checked');
		}
	});
	
	$('#cari_do').click(function(){
		do_valid = false;
		
		var url = bs.siteURL + 'delivery_request_og_aux/get_delivery_order';
		var param = {
			token : bs.token,
			nomor_do : $('#nomor_do').val()	
		}
		
		if(!param.nomor_do){
			sc_alert('Error', 'Harap isi nomor DO');
			return;
		}
		
		$('#nomor_do').addClass('ajax-load');
		
		$.post(url, param, function(data){
			$('#nomor_do').removeClass('ajax-load');
			
			if(data.success){
				do_valid = true;
				
				var header = data.datasource;
				$('#nama_terminal_petikemas').html(header.nama_terminal_petikemas + ' / ' + header.nama_pelabuhan);
				$('#kode_shipping_line').html(header.kode_shipping_line);
				$('#pol_pod').html(header.pol + ' / ' + header.pod);
				$('#voyage_vessel').html(header.voyage + ' / ' + header.vessel_name);
				$('#arrival_date').html(header.arrival_date);
				$('#consignee').html(header.consignee);
				$('#nomor_sppb').html(header.nomor_sppb);
				$('#tanggal_sppb').html(header.tanggal_sppb);
				
				$('#container_landing').html('');
				
				for(var i = 0; i < data.datasource.detail.length; i++){
					var cont = data.datasource.detail[i];
					
					var elStr = 
						'<tr>'+
						'	<td><input type="checkbox" '+ (cont.siap_delivery ? 'name="coreor_line_id[]" value="' + cont.coreor_line_id + '"' : 'disabled="disabled"') +'></td>'+
						'	<td>' + cont.nomor_container + '</td>'+
						'	<td>' + cont.iso_code + '</td>'+
						'	<td>' + cont.commodity + '</td>'+
						'	<td>' + (cont.hazard == 1 ? 'Ya' : 'Tidak') + '</td>'+
						'	<td>' + cont.status_msg + '</td>'+
						'</tr>';
						
					$(elStr).appendTo('#container_landing');
				}
				
			}else{
				sc_alert('Error', data.msg);
			}
		}, 'json');
	});
	
	$('#main_form').submit(function(){
		var errors = [];
		if(!$('#nomor_do').val()){
			errors.push('Nomor DO wajib diisi');
		}else if(!do_valid){
			errors.push('DO Belum di validasi, silakan klik pencarian');	
		}
		
		if(!$('#nomor_sppb').val()){
			errors.push('Nomor SPPB harus diisi');	
		}
		
		if(!$('#tanggal_sppb').val()){
			errors.push('Tanggal SPPB harus diisi');	
		}
		
		if(!$('#rencana_ambil').val()){
			errors.push('Tanggal rencana ambil harus diisi');	
		}
		
		var total_container = 0;
		$('#container_landing input[name=coreor_line_id\\[\\]]').each(function(){
			if($(this).prop('checked')){
				total_container++;	
			}
		});
		
		if(total_container == 0){
			errors.push('Setidaknya anda harus memilih satu petikemas yang akan di ambil. Tandai petikemas yang akan diambil dengan membubuhkan tanda cek di kolom paling kiri.');	
		}
		
		if(errors.length == 0){
			var url = bs.siteURL + 'delivery_request_og_aux/add';
			var param = $(this).serializeArray();
			param.push({
				name : 'token',
				value : bs.token
			});
			
			$.post(url, param, function(data){
				if(data.success){
					sc_alert('Sukses', 'Sukses simpan request, silakan lanjutkan dengan pembayaran');
				}else{
					sc_alert('Error ' + data.err_code, data.msg);
				}
			}, 'json');
		}else{
			var str = '';
			for(var i = 0; i < errors.length; i++){
				str += '<p>' + errors[i] + '</p>';	
			}
			sc_alert('Harap perbaiki kesalahan berikut', str);
		}
		
		return false;
	});
	
	initialize();
});
</script>
</body>
</html>