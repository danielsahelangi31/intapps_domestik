<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">BERTHING PLAN</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                              
                            
              
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-right:0px">
                            <label>Nama KAPAL : <b class="text-danger">*</b></label>
                            <input type="text" class="form-control right" readonly id="VESSEL_NAME" name="VESSEL_NAME" value="<?php echo $kunjung->VESSEL_NAME ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label>Vessel Code:<b class="text-danger">*</b></label>
                            <input type="text" class="form-control right" readonly id="VESSEL_CODE" name="VESSEL_CODE" value="<?php echo $kunjung->VESSEL_CODE ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">Voyage IN:<b class="text-danger">*</b></label>
                                <input type="text" class="form-control right" readonly id="VOYAGE_IN" name="VOYAGE_IN" value="<?php echo $kunjung->VOYAGE_IN ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">Voyage OUT:<b class="text-danger">*</b></label>
                                <input type="text" class="form-control right" readonly id="VOYAGE_OUT" name="VOYAGE_OUT" value="<?php echo $kunjung->VOYAGE_OUT ?>"/>
                            </div>
                        </div>
                        <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                                <label>Nama Kade<b class="text-danger">*</b></label>
                                <select class="form-control" name="KADE_NAME" id="KADE_NAME">
                                    <option value="<?php echo $kunjung->KADE_NAME ?>"><?php echo $kunjung->KADE_NAME ?></option>                        
                                    <option value="TPT 1">TPT 1</option>
                                    <option value="TPT 2">TPT 2</option>
                                    <option value="TPT 3">TPT 3</option>  
                                    <option value="TPT 4">TPT 4</option>    
                                    <option value="TPT 5A">TPT 5A</option> 
                                    <option value="TPT 5B">TPT 5B</option>
                                    <option value="EX PRESIDEN">EX PRESIDEN</option>  
                                </select> 
							
                            </div>                       
                        </div>   
                   
							<div class="form-group" style="margin-left:0px;width:48.5%;"> 
                                <label class="col-form-label">Kade Meter Awal:<b class="text-danger">*</b></label>
                                <input type="number" class="form-control right" id="KADE_AWAL" name="KADE_AWAL"  value="<?php echo $kunjung->KADE_AWAL ?>"/>
                            </div>
                     
							<div class="form-group" style="margin-left:0px;width:48.5%;"> 
                                <label class="col-form-label">Kade Meter Akhir:<b class="text-danger">*</b></label>
                                <input type="number" class="form-control" id="KADE_AKHIR" name="KADE_AKHIR"  value="<?php echo $kunjung->KADE_AKHIR ?>"/>
                            </div>
                   
                
                     
                        <hr/>
                   
                        <div class="btn" id="simpan_load"  style="display:auto;margin-left:0%"></div>                        
							<a href="#" class="btn btn-primary" id="update">Simpan</a>
                            <a href="<?php echo site_url('tps_online/berthing_time/finalize') ?>" class="btn btn-default">Kembali</a>
                            </form>
                            </div>
                        </div>
                    </div>
			</div>
		
			
			<?php echo form_close() ?>
		
			<div id="kampret_loader"></div>
		</div><!-- /.container -->
		
	</div>

    <?php $this->load->view('backend/elements/footer') ?>	
	
    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/notify.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script type="text/javascript">

	/* on AJAX Load Animator Overlay
	 */
	$(document).ready(function(){
        console.log('tzzz')

        $('#update').click(function(){
			
            visit_name = "<? echo $kunjung->VISIT_NAME ?>";
            visit_id = "<? echo $kunjung->VISIT_ID ?>";
            vessel_code = "<? echo $kunjung->VESSEL_CODE ?>";
            voyage_in = "<? echo $kunjung->VOYAGE_IN ?>";
            voyage_out = "<? echo $kunjung->VOYAGE_OUT ?>";
            id_berthing = <?php echo $kunjung->id_berthing ?>;       

            console.log('visit_id', visit_id);
				var param = {    
                    'ID_BERTHING' : id_berthing,
                    'VISIT_ID' : visit_id,
                    'VESSEL_NAME' :visit_name,
                    'VESSEL_CODE' : vessel_code,
                    'VOYAGE_IN'   : voyage_in,
                    'VOYAGE_OUT' : voyage_out,       
					'KADE_NAME' : $('#KADE_NAME').val(),
					'KADE_AWAL' : $('#KADE_AWAL').val(),
                    'KADE_AKHIR' : $('#KADE_AKHIR').val()
                 
                }		
				
				console.log(param);			
                is_error = false;
				
                if(!param.KADE_NAME || param.KADE_NAME == ""){
					$('#KADE_NAME').parent().addClass('has-error');
					add_validation_popover('#KADE_NAME', 'Harus diisi');
					
					is_error = true;
				}

                if(!param.KADE_AWAL || param.KADE_AWAL == ""){
					$('#KADE_AWAL').parent().addClass('has-error');
					add_validation_popover('#KADE_AWAL', 'Harus diisi');
					
					is_error = true;
				}
              

                if(!param.KADE_AKHIR || param.KADE_AKHIR == ""){
					$('#KADE_AKHIR').parent().addClass('has-error');
					add_validation_popover('#KADE_AKHIR', 'Harus diisi');
					
					is_error = true;
				}              

                console.log('err', is_error);
				if(is_error){
                    $.notify("Harap perbaiki field yang ditandai","error");  
					//sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
				}else{		
					$('#simpan_load').show();
				
					var url = bs.siteURL + 'FormBerth/update/' + bs.token;
                    console.log('url',url)
                    $.notify("Data berhasil ditambahkan", "success");
                    window.location.href = bs.siteURL + 'tps_online/berthing_time/listview';  
					$.post(url, param, function(data){
                       window.location.href = bs.siteURL + 'tps_online/berthing_time/listview';   
					    $('#simpan_load').hide();
                        console.log('scss', data)
                        console.log('prm', param)
                        reset_form();
						if(data){
                            console.log('scs', data)

							sc_alert('Sukses', data);
							reset_form();
						}else{
							sc_alert('ERROR', data);
						}
					}, 'json');
				}
				
				return false;
			});
			
			function reset_form(){
				$('#KADE_NAME, #KADE_AWAL, #KADE_AKHIR').val('');
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
	
 
         
	});
	</script>
</body>
</html>