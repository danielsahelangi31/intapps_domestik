<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">KPI</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                             
                 
                            
                            <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                               <label>Terminal<b class="text-danger">*</b></label>
                               <select class="form-control" name="TERMINAL" id="TERMINAL">
                                <option value="<?php echo $kunjung->TERMINAL?>"><?php echo $kunjung->TERMINAL?></option>
                                <option value="DOMESTIK">DOMESTIK</option>
                                <option value="INTERNASIONAL">INTERNASIONAL</option>                                    
                                                    
                                </select> 
                            </div>  
                            

                            <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label class="col-form-label">Periode<b class="text-danger">*</b></label>
                            <input type="date" class="form-control" id="PERIODE_MULAI" name="PERIODE_MULAI" value="<?php echo $kunjung->PERIODE_MULAI?>">                            
                            </div>                        
                             
                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label class="col-form-label">USH:<b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="USH" name="USH" value="<?php echo $kunjung->USH ?>">
                            </div> 
                            </div>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>BOR(%): <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="BOR"  name="BOR" value="<?php echo $kunjung->BOR ?>">
                            </div>
                            </div>	                        
                        
                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>YOR(%): <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="YOR"  name="YOR" value="<?php echo $kunjung->YOR ?>">
                            </div> 
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>ET/BT(%): <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="ET_BT"  name="ET_BT" value="<?php echo $kunjung->ET_BT ?>">
                            </div> 
                            </div> 
                        
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>Zero Defect: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="ZERO_DEFECT"  name="ZERO_DEFECT" value="<?php echo $kunjung->ZERO_DEFECT ?>">
                            </div> 
                            </div>                            
                            
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>Traffic Kapal: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="TRAFFIC_KAPAL"  name="TRAFFIC_KAPAL" value="<?php echo $kunjung->TRAFFIC_KAPAL ?>">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>SLA Pranota BM: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="SLA_PRANOTABM"  name="SLA_PRANOTABM" value="<?php echo $kunjung->SLA_PRANOTABM ?>">
                            </div> 
                            </div>	    
                                 
                           
                        <div class="btn"  id="simpan_load" style="display:auto;margin-left:0%">                   
							<a href=""  class="btn btn-primary" id="update_kpi">Simpan</a>
                            <a href="<?php echo site_url('tps_online/kpi_barang/listview') ?>" class="btn btn-default">Kembali</a>
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
        
        $('#update_kpi').click(function(){
                var now = new Date(Date.now()).toLocaleString("id-ID");
                console.log('now',now);         

                id = <?php echo $kunjung->id_kpi ?>;
				var param = {   
                    'id_kpi' : id,
                    'TERMINAL' : $('#TERMINAL').val(),
					'PERIODE_MULAI' : $('#PERIODE_MULAI').val(),   
                    'USH' : $('#USH').val(),
                    'BOR' : $('#BOR').val(),
                    'YOR' : $('#YOR').val(),
                    'ET_BT' : $('#ET_BT').val(),
                    'ZERO_DEFECT' : $('#ZERO_DEFECT').val(),
                    'TRAFFIC_KAPAL' : $('#TRAFFIC_KAPAL').val(),
                    'SLA_PRANOTABM' : $('#SLA_PRANOTABM').val()                   
                     
                            
                }		
				
				is_error = false;
				console.log(param);
				
				if(!param.TERMINAL || param.TERMINAL == ""){
					$('#TERMINAL').parent().addClass('has-error');
					add_validation_popover('#TERMINAL', 'TERMINAL Harus dipilih');
					
					is_error = true;
				}
				
							
				if(!param.PERIODE_MULAI || param.PERIODE_MULAI == ""){
					$('#PERIODE_MULAI').parent().addClass('has-error');
					add_validation_popover('#PERIODE_MULAI', 'PERIODE_MULAI Harus diisi');
					
					is_error = true;
				}

                if(!param.USH || param.USH == ""){
					$('#USH').parent().addClass('has-error');
					add_validation_popover('#USH', 'USH Harus diisi');
					
					is_error = true;
				}

                if(!param.BOR || param.BOR == ""){
					$('#BOR').parent().addClass('has-error');
					add_validation_popover('#BOR', 'BOR Harus diisi');
					
					is_error = true;
				}

                if(!param.YOR || param.YOR == ""){
					$('#YOR').parent().addClass('has-error');
					add_validation_popover('#YOR', 'YOR Harus diisi');
					
					is_error = true;
				}

                if(!param.ET_BT || param.ET_BT == ""){
					$('#ET_BT').parent().addClass('has-error');
					add_validation_popover('#ET_BT', 'ET_BT Harus diisi');
					
					is_error = true;
				}

                if(!param.ZERO_DEFECT || param.ZERO_DEFECT == ""){
					$('#ZERO_DEFECT').parent().addClass('has-error');
					add_validation_popover('#ZERO_DEFECT', 'ZERO_DEFECT Harus diisi');
					
					is_error = true;
				}
              
                
                if(!param.TRAFFIC_KAPAL || param.TRAFFIC_KAPAL == ""){
					$('#TRAFFIC_KAPAL').parent().addClass('has-error');
					add_validation_popover('#TRAFFIC_KAPAL', 'TRAFFIC_KAPAL Harus diisi');
					
					is_error = true;
				}

                
                if(!param.SLA_PRANOTABM || param.SLA_PRANOTABM == ""){
					$('#SLA_PRANOTABM').parent().addClass('has-error');
					add_validation_popover('#SLA_PRANOTABM', 'SLA_PRANOTABM Harus diisi');
					
					is_error = true;
				}
		
				if(is_error){
                    $.notify("Harap perbaiki field yang ditandai","error");  
			
				}else{		
					
					var urls = bs.siteURL + 'FormKpi/update_kpi/' + bs.token;
                    console.log('url', urls)

					$.post(urls, param, function(data){
                        $('#simpan_load').show();
                        console.log('list_view1')
                       $.notify("Data berhasil ditambahkan", "success");
                       window.location.href = bs.siteURL + 'tps_online/kpi_barang/listview';     
                        
                        reset_form();
						if(data === 'Berhasil'){
                            console.log('list_view2',)
                        
							sc_alert('Sukses', data);
							reset_form();
						}else{
							sc_alert('ERROR', data);
						}
                        $('#simpan_load').hide();
					}, 'json');
                 
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