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
						<h2>Edit BL</h2>
						<hr><br>
					</div>
				
				</div>
				<div class="row">
					<form class="form-horizontal" id="formBL">
						<div class="form-group">
							<label class="control-label col-sm-2" for="visit_id">No BL Lama</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="bl_lama" name="bl_lama" placeholder="No BL Lama">
							</div>
							
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="no_bl">No BL Baru</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="bl_baru" name="bl_baru" placeholder="No BL Baru">
							</div>							
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2 align-right" for="no_bl">Jumlah VIN Baru</label>
							<div class="col-sm-6">
								<input type="text" class="form-control" id="jml_vin" placeholder="Jumlah Vin" name="jml_vin">
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
				  

					$('#batal').click(function(){
						window.location = bs.siteURL + 'tps_online/';

					});

					$('#simpan').click(function(){
						var is_error = false;
						let data = $('#formBL').serializeArray();
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
							request = $.ajax({
								type: 'post',
								url: bs.siteURL + 'tps_online/edit_bl/simpan/'+bs.token,
			                    data: data

							});
                            request.done(function (response, textStatus, jqXHR){
                                // Log a message to the console
                                console.log("Hooray, it worked!");  
                                //console.log(response);                              
                                alert("Data sudah dimasukkan!");
                            });
                            request.fail(function (jqXHR, textStatus, errorThrown){
                            // Log the error to the console
                            console.error(
                                "The following error occurred: "+
                                textStatus, errorThrown
                            );
    });

						}

					});


				});

			</script>




















		</body>
	</html>