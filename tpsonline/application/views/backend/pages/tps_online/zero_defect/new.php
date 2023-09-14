<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
    <style>
     .select2-container .select2-selection--single {
        height: 34px;!important;
        }
    </style>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">ZERO DEFECT (QUALITY)</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                              
                 
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="form-group" style="margin-left:-3px;margin-right:17px">
                                        <label>Terminal<b class="text-danger">*</b></label>
                                          <select class="form-control" name="TERMINAL" id="TERMINAL">
                                              <option value="">-- PILIH --</option>
                                              <option value="DOMESTIK">DOMESTIK</option>
                                              <option value="INTERNASIONAL">INTERNASIONAL</option>                                    
                                                    
                                         </select> 
                                </div>  
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="form-group" style="margin-left:-3px;margin-right:17px">                     
							<label >Periode (Bulan)</label>		
                            <select class="form-control" name="PERIODE_BULAN" id="PERIODE_BULAN">
                                    <option value="">-- PILIH --</option>
                                    <option value="JANUARI">JANUARI</option>
                                    <option value="FEBRUARI">FEBRUARI</option>
                                    <option value="MARET">MARET</option>
                                    <option value="APRIL">APRIL</option>
                                    <option value="MEI">MEI</option>
                                    <option value="JUNI">JUNI</option>
                                    <option value="JULI">JULI</option>
                                    <option value="AGUSTUS">AGUSTUS</option>
                                    <option value="SEPTEMBER">SEPTEMBER</option>
                                    <option value="OKTOBER">OKTOBER</option>
                                    <option value="NOVEMBER">NOVEMBER</option>
                                    <option value="DESEMBER">DESEMBER</option>
                                </select>				
							</div>
                        </div>
                        
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="form-group" style="margin-left:-3px;margin-right:17px">
                                <label>Tahun<b class="text-danger">*</b></label>
                                <select class="form-control" name="TAHUN" id="TAHUN">
                                    <?php for($i = date('Y'); $i < date('Y+1'); $i++){ ?>
                                    <option value="<?= date('Y') ?>"><?= date('Y') ?></option>		                   
                                    <option value="<?= date('Y')+1?>"><?= date('Y')+1 ?></option>
                                    <?php } ?>
		                        </select>	
								
                                </div>                       
                            </div>  

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-3px;margin-right:17px"> 
                            <label>Maker<b class="text-danger">*</b></label>
                                <select class="form-control" id="MAKER" name="MAKER">
                                    <option value="">-- PILIH --</option>
                                    <?php
                                    foreach ($datasource as $make){
                                        ?>
                                        <option value="<?php echo $make->BRAND; ?>" ><?php echo $make->BRAND; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php echo form_error('directionType', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>	
                              </div>
                            </div>                       
                            </div>           
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;"> 
                            <label><b class="text-danger"></b></label>
                          
                            </div> 
                            <div class="form-group" style="margin-left:15px;width:95%;"> 
                            <h4>DEFECT LQ </h4>    
                            <hr />   
                            </div>
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;"> 
                            <label>LQ Gate 1 (Back KCY): <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="LQ_GATE_1_BACK_KCY"  name="LQ_GATE_1_BACK_KCY">
                            </div>  
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;"> 
                            <label>LQ Gate 1 (Quarantine): <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="LQ_GATE_1_QUARANTINE"  name="LQ_GATE_1_QUARANTINE">
                            </div>                                                
                        
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;"> 
                            <label>LQ Gate 2: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="LQ_GATE_2"  name="LQ_GATE_2">
                            </div> 

                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;"> 
                            <label>LQ Gate 3: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="LQ_GATE_3"  name="LQ_GATE_3">
                            </div> 
                        
                            <div class="form-group" style="margin-left:15px;width:95%;"> 
                            <h4>FALL OUT </h4>    
                            <hr /> 
                            </div> 
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;"> 
                            <label>Cargo Defect: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="CARGO_DEFECT"  name="CARGO_DEFECT">
                            </div> 
                    
                   
                        <div class="btn"  id="simpan_load" style="display:auto;margin-left:25%;margin-top:25px">                  
							<a href=""  class="btn btn-primary" id="simpan">Simpan</a>
                            <a href="<?php echo site_url('tps_online/zero_defect/listview') ?>" class="btn btn-default">Kembali</a>
                        </div>                
                                </form>
                            </div>
                        </div>
                    </div>
			</div>
		
			
			<?php echo form_close() ?>
		
			<div id="kampret_loader"></div>
		</div>
        </div>
    	</div>
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
	
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/notify.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script type="text/javascript">


	$(document).ready(function(){
        $('#MAKER').select2();      
        
        $('#simpan').click(function(){
                var now = new Date(Date.now()).toLocaleString("id-ID");
                console.log('now',now);           
                  
				var param = {  
                    'CREATED_DATE' : now,              
					'PERIODE_BULAN' : $('#PERIODE_BULAN').val(),
					'TAHUN' : $('#TAHUN').val(),         
                    'LQ_GATE_1_BACK_KCY' : $('#LQ_GATE_1_BACK_KCY').val(),
                    'LQ_GATE_1_QUARANTINE' : $('#LQ_GATE_1_QUARANTINE').val(),
                    'LQ_GATE_2' : $('#LQ_GATE_2').val(),
                    'LQ_GATE_3' : $('#LQ_GATE_3').val(),
                    'CARGO_DEFECT' : $('#CARGO_DEFECT').val(),
                    'TERMINAL' : $('#TERMINAL').val(),
                    'MAKER' : $('#MAKER').val()                            
                }		
				
				
				console.log(param);
				is_error = false;
				if(!param.PERIODE_BULAN || param.PERIODE_BULAN == ""){
					$('#PERIODE_BULAN').parent().addClass('has-error');
					add_validation_popover('#PERIODE_BULAN', 'Periode (Bulan) Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.TAHUN || param.TAHUN== ""){
					$('#TAHUN').parent().addClass('has-error');
					add_validation_popover('#TAHUN', 'TAHUN Harus diisi');
					
					is_error = true;
				}

                if(!param.LQ_GATE_1_BACK_KCY || param.LQ_GATE_1_BACK_KCY== ""){
					$('#LQ_GATE_1_BACK_KCY').parent().addClass('has-error');
					add_validation_popover('#LQ_GATE_1_BACK_KCY', 'LQ_GATE_1_BACK_KCY Harus diisi');
					
					is_error = true;
				}

                if(!param.LQ_GATE_1_QUARANTINE || param.LQ_GATE_1_QUARANTINE == ""){
					$('#LQ_GATE_1_QUARANTINE').parent().addClass('has-error');
					add_validation_popover('#LQ_GATE_1_QUARANTINE', 'LQ_GATE_1_QUARANTINE Harus diisi');
					
					is_error = true;
				}
                if(!param.LQ_GATE_2 || param.LQ_GATE_2 == ""){
					$('#LQ_GATE_2').parent().addClass('has-error');
					add_validation_popover('#LQ_GATE_2', 'LQ_GATE_2 Harus diisi');
					
					is_error = true;
				}

                if(!param.LQ_GATE_3 || param.LQ_GATE_3 == ""){
					$('#LQ_GATE_3').parent().addClass('has-error');
					add_validation_popover('#LQ_GATE_3', 'LQ_GATE_3 Harus diisi');
					
					is_error = true;
				}

                if(!param.CARGO_DEFECT || param.CARGO_DEFECT == ""){
					$('#CARGO_DEFECT').parent().addClass('has-error');
					add_validation_popover('#CARGO_DEFECT', 'CARGO_DEFECT Harus diisi');
					
					is_error = true;
				}
                if(!param.MAKER || param.MAKER == ""){
					$('#MAKER').parent().addClass('has-error');
					add_validation_popover('#MAKER', 'MAKER Harus diisi');
					
					is_error = true;
				}
                if(!param.TERMINAL || param.TERMINAL == ""){
					$('#TERMINAL').parent().addClass('has-error');
					add_validation_popover('#TERMINAL', 'TERMINAL Harus diisi');
					
					is_error = true;
				}
				console.log('err', is_error)
				if(is_error){
                    console.log('err', is_error)
                    $.notify("Harap perbaiki field yang ditandai","error");  
			
				}else{		
				
					var urls = bs.siteURL + 'FormZero/save/' + bs.token;
                    console.log('url', urls)                
           
					$.post(urls, param, function(data){
               
                        console.log('list_view1')  
                        $.notify("Data berhasil ditambahkan", "success");                 
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview';   
                        reset_form();
						if(data === 'Berhasil'){
                            console.log('list_view2',)
                 
							sc_alert('Sukses', data);
			
						}else{
							sc_alert('ERROR', data);
						}
                  
					});
                 
				}
				
				return false;
			});
			
			function reset_form(){
				$('#PERIODE_BULAN, #TAHUN').val('');
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