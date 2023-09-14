<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Data Pelanggan</h1>
			<p class="lead">
				<small></small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						
					</div>
				</div>
			</div>
			
			<hr />
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-7">
					<div class="table-responsive">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th><?php echo gridHeader('ID', 'ID', $cfg) ?></th>
									<th><?php echo gridHeader('NAMA_PERUSAHAAN', 'Nama Perusahaan', $cfg) ?></th>
									<th><?php echo gridHeader('NPWP', 'NPWP', $cfg) ?></th>
									
									<th>Tindakan</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;
								
								if($datasource){
									foreach($datasource as $row){
								?>
								<tr>
									<td><?php echo $row->ID ?></td>
									<td><?php echo $row->NAMA_PERUSAHAAN ?></td>
									<td><?php echo $row->NPWP ?></td>
									
									<td>
										<a href="<?php echo site_url('tps_online/pelanggan/view/'.$row->ID.'/'.$grid_state) ?>" class="edit_link">Lihat</a>
										<a href="<?php echo site_url('tps_online/pelanggan/edit/'.$row->ID.'/'.$grid_state) ?>" class="edit_link">Edit</a>
										<a href="<?php echo site_url('tps_online/pelanggan/deletes/'.$row->ID.'/'.$grid_state) ?>" class="edit_link">Hapus</a>
									</td>
								</tr>
								<?php
									}
								}else{
								?>
								<tr><td colspan="9"><em>Tidak ada data</em></td></tr>
								<?php	
								}
								?>
							</tbody>
						</table>
					</div>
					
					<?php $this->load->view('backend/components/paging') ?>
				</div>
				<div class="col-md-5">
					<h4>Tambah Pelanggan</h4>
					<?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
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
						
						<div class="pull-right">
							<div class="btn ajax-load" id="simpan_load" style="display:none"></div>
							<a href="#" class="btn btn-primary" id="simpan">Simpan</a>
						</div>
					<?php echo form_close() ?>
				</div>
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
	
	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script>
		$(document).ready(function(){
			/**$( "#ORGANIZATION" ).keypress(function() {
				$('#ORGANIZATION').val (function () {
					return this.value.toUpperCase();
				});
			});*/
			
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
			
			$("#NPWP").mask("99.999.999.9-999.999");
			
			$( "#ORGANIZATION" ).keyup(function() {
				$('#ORGANIZATION').val (function () {
					return this.value.toUpperCase();
				});
			});
			
			
			$('#simpan').click(function(){
				
				var is_error = false;
				
				var param = {
					'NAMA_PERUSAHAAN' : $('#ORGANIZATION').val(),
					'NPWP' : $('#NPWP').val()
				}
				
				
				
				
				console.log(param);
				
				if(!param.NAMA_PERUSAHAAN || param.NAMA_PERUSAHAAN == ""){
					$('#ORGANIZATION').parent().addClass('has-error');
					add_validation_popover('#ORGANIZATION', 'Nama Perusahaan Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.NPWP || param.NPWP == ""){
					$('#NPWP').parent().addClass('has-error');
					add_validation_popover('#NPWP', 'NPWP Harus diisi');
					
					is_error = true;
				}
				
				if(is_error){
					sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
				}else{		
					$('#simpan_load').show();
				
					var url = bs.siteURL + 'tps_online/pelanggan/simpan/' + bs.token;
					
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
			
			function reset_form(){
				$('#ORGANIZATION, #NPWP').val('');
			}
			
			initialize();
			
		});
	</script>
</body>
</html>