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
						<h2>Manual Input SPPB</h2>
						<hr><br>
					</div>
				
				</div>
				<div class="row">
					<form class="form-horizontal" id="formSppb">
						<div class="form-group">
							<label class="control-label col-sm-2" for="visit_id">Visit ID Kapal</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="visit_id" name="visit_id" placeholder="Autocompleted Visit ID Kapal (5 digit terakhir)">
							</div>
							
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="no_sppb">SPPB</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="no_sppb" name="no_sppb" placeholder="Nomor SPPB">
							</div>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" class="form-control date" id="date_sppb" name="date_sppb" placeholder="Tanggal SPPB">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2 align-right" for="no_bl">BL</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="no_bl" placeholder="Nomor BL (autocomplete, minimal 4 digit)" name="no_bl">
							</div>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" class="form-control date" id="date_bl" name="date_bl" placeholder="Tanggal BL">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="no_bc11">BC 11</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="no_bc11" name="no_bc11" placeholder="Nomor BC 11">
							</div>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" class="form-control date" id="date_bc11" name="date_bc11" placeholder="Tanggal BC11">
									<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="no_pos_bc11">No POS BC 11</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="no_pos_bc11" name="no_pos_bc11" placeholder="No POS BC 11">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="npwpCon">Consignee</label>
							<div class="col-sm-4">
								<input type="text" class="form-control" id="npwpCon" name="npwpCon" placeholder="NPWP">
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="namaCon" name="namaCon" placeholder="Nama">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="merk_kemasan">Merk Kemasan</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="merk_kemasan" name="merk_kemasan" placeholder="Merk Kemasan">
							</div>
							
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="jumlah_kemasan">Jumlah Kemasan</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="jumlah_kemasan" name="jumlah_kemasan" placeholder="Jumlah Kemasan">
							</div>
							
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="jenis_kemasan">Jenis Kemasan</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" id="jenis_kemasan" name="jenis_kemasan" placeholder="Jenis Kemasan">
							</div>
							
						</div>
						
						<div class="pull-right">
							<div class="btn ajax-load" id="simpan_load" style="display:none"></div>
							<a href="#" class="btn btn-primary" id="simpan">Simpan</a>
							<a  class="btn btn-danger" id="batal">Batal</a>
						</div>
					</form>
				</div>
				
				</div><!-- /.container -->
			</div>
			
			<?php $this->load->view('backend/elements/footer') ?>
			<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
			<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
			<script type="text/javascript">
				$(document).ready(function(){
					initialize();
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

					function auto_remove_popover_on_change(){
						$(this).popover('destroy');
						$(this).parent().removeClass('has-error');
						
						$(this).unbind('change', auto_remove_popover_on_change);
					}

					$( function() {
					    var availableVISIT = [<?php echo '"' . implode('","', $visitAuto) . '"'; ?> ];
					    $( "#visit_id" ).autocomplete({
					      minLength: 4,
					      source: availableVISIT,
					      select: function( event, ui ) {
					      		$("#visit_id").val(ui.item.value);
					      }
					    });
					  } );
					
				    $( "#no_bl" ).autocomplete({
				      source: function( request, response ) {
				        $.ajax( {
				          url: bs.siteURL + 'tps_online/manual_sppb/getBL',
				          dataType: "json",
		                  data: {
								term: request.term
						  },
				          success: function( data ) {
				          	
				            response( data );
				          }
				        } );
				      },
				      minLength: 4,
				      select: function( event, ui ) {
					      		$("#no_bl").val(ui.item.value);
					  }
				    });

				    $('#visit_id').change(function(){
				    	let val = $(this).val();
				    	$.ajax( {
				          url: bs.siteURL + 'tps_online/manual_sppb/autoFill',
				          dataType: "json",
				          type: "post",
		                  data: {
		                  	vis: val,
		                  	type: 'visit'
		                  },
				          success: function( data ) {

					            if (data === undefined || data.length == 0) {
					            	$('#no_bc11').val('');
					            	$('#date_bc11').val('');
					            }
					            else{
					            	$('#no_bc11').val(data[0].BC11);
					            	$('#date_bc11').val(data[0].BC11_DATE);
					            }
				            	
				            }
				            
				          
				        } );
				    })

				    $('#no_bl').change(function(){
				    	let val = $(this).val();
				    	$.ajax( {
				          url: bs.siteURL + 'tps_online/manual_sppb/autoFill',
				          dataType: "json",
				          type: "post",
		                  data: {
		                  	bl: val,
		                  	type: 'bl'
		                  },
				          success: function( data ) {
					            if (data === undefined || data.length == 0) {
					            	$('#npwpCon').val('');
					            	$('#namaCon').val('');
					            }
					            else{
					            	$('#npwpCon').val(data[0].CONSIGNEE_TAX_REF);
					            	$('#namaCon').val(data[0].CONSIGNEE_NAME);
					            }
				            	
				            }
				            
				          
				        } );
				    })
				  

					$('#batal').click(function(){
						window.location = bs.siteURL + 'tps_online/';

					});

					$('#simpan').click(function(){
						var is_error = false;
						let data = $('#formSppb').serializeArray();
						data.forEach(function(obj){
							if (obj.value == '' || obj.value == null) {
								$('#'+obj.name).parent().addClass('has-error');
								add_validation_popover('#'+obj.name, 'wajib diisi');
								
								is_error = true;
							};

						});

						if (is_error) {
							sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
						}else{
							$.ajax({
								type: 'post',
								url: bs.siteURL + 'tps_online/manual_sppb/simpan/'+bs.token,
			                    data: data,
			                    dataType: 'JSON',
			                    success: function(e){
			                    	if (e.success == true) {

			                    		sc_alert('Sukses', e.msg);
			                    		setTimeout(function(){
			                    			window.location = bs.siteURL + 'tps_online/';
			                    		}, 2500);
			                    		
			                    		
			                    		
			                    	}
			                    	else {
			                    		sc_alert('Error', e.msg);
			                    	}
			                    }

							});

						}
					
						



					});


				});




			</script>




















		</body>
	</html>