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
                                              <option value="">-- PILIH --</option>
                                              <option value="DOMESTIK">DOMESTIK</option>
                                              <option value="INTERNASIONAL">INTERNASIONAL</option>                                    
                                                    
                                         </select> 
                                </div>  
                             
                            <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label class="col-form-label">Periode<b class="text-danger">*</b></label>                           
                                  <select class="form-control" name="TAHUN" id="TAHUN">
                                    <?php for($i = date('Y'); $i < date('Y+1'); $i++){ ?>
                                    <option value="<?= date('Y') ?>"><?= date('Y') ?></option>		                   
                                    <option value="<?= date('Y')+1?>"><?= date('Y')+1 ?></option>
                                    <?php } ?>
		                           </select>								                       
                            </div>
                            <div class="form-group" style="margin-left:0px;width:95%;"> 
                            <h4>Kinerja Terminal</h4>    
                            <hr />   
                            </div>        
                            <div class="col-lg-2 col-md-2 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label class="col-form-label">USH:<b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="USH" name="USH"/>
                            </div> 
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>BOR(%): <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="BOR"  name="BOR">
                            </div>
                            </div>	                        
                        
                            <div class="col-lg-2 col-md-2 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>YOR(%): <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="YOR"  name="YOR">
                            </div> 
                            </div>

                            <div class="col-lg-2 col-md-2 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>ET/BT(%): <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="ET_BT"  name="ET_BT">
                            </div> 
                            </div>         

                            <div class="form-group" style="margin-left:0px;width:95%;"> 
                            <h4></h4>                           
                            </div>                                      

                            <div class="form-group" style="margin-left:0px;width:95%;"> 
                            <h4>Kinerja Quality</h4>    
                            <hr />   
                            </div>     

                            <div class="form-group" style="margin-left:0px;width:48.5%;">
                            <label>Zero Defect: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="ZERO_DEFECT"  name="ZERO_DEFECT">
                            </div> 

                            <div class="form-group" style="margin-left:0px;width:48.5%;">
                            <label>Safety: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="SAFETY"  name="SAFETY">
                            </div> 

                            <div class="form-group" style="margin-left:0px;width:95%;"> 
                            <h4>Kinerja Pranota</h4>    
                            <hr />   
                            </div>     

                            <div class="form-group" style="margin-left:0px;width:48.5%;">
                            <label>SLA Pranota BM: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="SLA_PRANOTABM"  name="SLA_PRANOTABM">
                            </div>                       

                            <hr/>
                   
                        <div class="btn"  id="simpan_load" style="display:auto;margin-left:0%">            
							<a href=""  class="btn btn-primary" id="simpan">Simpan</a>
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
        
           
        $('#simpan').click(function(){
            var dt = $('#TAHUN').val();            
            var periode = new Date(dt);
            var periode = periode.toLocaleString();
            console.log("datey",periode);   

            var param = {                         
					'TERMINAL' : $('#TERMINAL').val(),
					'PERIODE' : periode,   
                    'USH' : $('#USH').val(),
                    'BOR' : $('#BOR').val(),
                    'YOR' : $('#YOR').val(),
                    'ET_BT' : $('#ET_BT').val(),
                    'ZERO_DEFECT' : $('#ZERO_DEFECT').val(),
                    'SAFETY' : $('#SAFETY').val(),
                    'SLA_PRANOTABM' : $('#SLA_PRANOTABM').val()        
                                    
                            
                }		
				
				
				console.log(param);
				is_error = false;
				if(!param.TERMINAL || param.TERMINAL == ""){
					$('#TERMINAL').parent().addClass('has-error');
					add_validation_popover('#TERMINAL', 'TERMINAL harus dipilih');
					
					is_error = true;
				}
				
				if(!param.PERIODE || param.PERIODE == ""){
					$('#PERIODE').parent().addClass('has-error');
					add_validation_popover('#PERIODE', 'PERIODE harus diisi');
					
					is_error = true;
				}

                if(!param.USH || param.USH == ""){
					$('#USH').parent().addClass('has-error');
					add_validation_popover('#USH', 'USH harus diisi');
					
					is_error = true;
				}

                if(!param.BOR || param.BOR == ""){
					$('#BOR').parent().addClass('has-error');
					add_validation_popover('#BOR', 'BOR harus diisi');
					
					is_error = true;
				}

                if(!param.YOR || param.YOR == ""){
					$('#YOR').parent().addClass('has-error');
					add_validation_popover('#YOR', 'YOR harus diisi');
					
					is_error = true;
				}

                if(!param.ET_BT || param.ET_BT == ""){
					$('#ET_BT').parent().addClass('has-error');
					add_validation_popover('#ET_BT', 'ET_BT harus diisi');
					
					is_error = true;
				}

                if(!param.ZERO_DEFECT || param.ZERO_DEFECT == ""){
					$('#ZERO_DEFECT').parent().addClass('has-error');
					add_validation_popover('#ZERO_DEFECT', 'ZERO_DEFECT harus diisi');
					
					is_error = true;
				}
              
                
                if(!param.SAFETY || param.SAFETY == ""){
					$('#SAFETY').parent().addClass('has-error');
					add_validation_popover('#SAFETY', 'SAFETY harus diisi');
					
					is_error = true;
				}

                
                if(!param.SLA_PRANOTABM || param.SLA_PRANOTABM == ""){
					$('#SLA_PRANOTABM').parent().addClass('has-error');
					add_validation_popover('#SLA_PRANOTABM', 'SLA_PRANOTABM harus diisi');
					
					is_error = true;
				}

               
				console.log('err', is_error)
				if(is_error){
                    console.log('err', is_error)
                    $.notify("Harap perbaiki field yang ditandai","error");  
				
				}else{		
		
				
					var urls = bs.siteURL + 'FormKpi/save/' + bs.token;
                    console.log('url', urls)
                    
					$.post(urls, param, function(data){
              
                        console.log('list_kpi', data);
                        
                        var datas = data.split(':');
                        console.log('list 2', datas);
                        for (var i=0; i< datas.length;i++){
                        if (datas[i] == '"Data sudah tersedia"}'){
                            var arus = false;
                            break;
                        } else {
                            var arus = true;
                        }
                        }
            
						if (arus == false) {                            
							sc_alert('Error', 'Data sudah tersedia');
						} else if(arus == true){                                       
						     $.notify("Data berhasil ditambahkan", "success");
                             window.location.href = bs.siteURL + 'tps_online/kpi_barang/listview';   
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

            initialize();
	});
	</script>
</body>
</html>