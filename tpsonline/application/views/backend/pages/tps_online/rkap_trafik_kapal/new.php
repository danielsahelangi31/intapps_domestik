<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">RKAP KUNJUNGAN KAPAL</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                              
                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Terminal<b class="text-danger">*</b></label>
                                          <select class="form-control" name="TERMINAL" id="TERMINAL">
                                              <option value="">-- PILIH --</option>
                                              <option value="DOMESTIK">DOMESTIK</option>
                                              <option value="INTERNASIONAL">INTERNASIONAL</option>    
                                              <option value="SATELIT">SATELIT</option>                                   
                                                    
                                         </select> 
                                </div>  
                            </div>  
                 

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                <label>Tahun<b class="text-danger">*</b></label>
                                <select class="form-control" name="TAHUN" id="TAHUN">
                                    <?php for($i = date('Y'); $i < date('Y+1'); $i++){ ?>
                                    <option value="<?= date('Y') ?>"><?= date('Y') ?></option>		                   
                                    <option value="<?= $i+1 ?>"><?= $i+1 ?></option>
                                    <?php } ?>
		                      </select>
								
                                </div>                       
                            </div>           
                          
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12"  style="margin-left:-15px;margin-right:1px">   
                            <h4>JANUARI:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:0px;margin-right:1px">   
                            <h4>FEBRUARI:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-3 col-md-3 col-sm-12 col-12">                        
                            <div class="form-group" style="margin-left:-15px;margin-right:1px"> 
                            <label class="col-form-label">UNIT</label>
                            <input type="number" class="form-control" id="JANUARI_UNIT" name="JANUARI"/>
                            </div> 
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">                      
                            <div class="form-group" style="margin-left:-15px;margin-right:1px"> 
                            <label class="col-form-label">GT<b class="text-danger"></b></label>
                            <input type="number" class="form-control" id="JANUARI" name="JANUARI"/>
                            </div> 
                            </div>                        
                            
                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">                          
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="FEBRUARI_UNIT"  name="FEBRUARI">
                            </div> 
                            </div>
                    
                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">                       
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>GT</label>
                            <input type="number" class="form-control" id="FEBRUARI"  name="FEBRUARI">
                            </div>
                            </div>                
                        
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12"  style="margin-left:-15px;margin-right:1px">   
                            <h4>MARET:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:0px;margin-right:1px">   
                            <h4>APRIL:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-3 col-md-3 col-sm-12 col-12">                        
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="MARET_UNIT"  name="MARET">
                            </div>
                            </div> 

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>GT</label>
                            <input type="number" class="form-control" id="MARET"  name="MARET">
                            </div> 
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="APRIL_UNIT"  name="APRIL">
                            </div> 
                            </div> 
                  
                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>GT</label>
                            <input type="number" class="form-control" id="APRIL"  name="APRIL">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:-15px;margin-right:1px">   
                            <h4>MEI:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:0px;margin-right:1px">   
                            <h4>JUNI:</h4> 
                            <hr />
                             </div>
                            <hr>

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="MEI_UNIT"  name="MEI">
                            </div> 
                            </div> 
                    
                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>GT</label>
                            <input type="number" class="form-control" id="MEI"  name="MEI">
                            </div>
                            </div>  

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="JUNI_UNIT"  name="JUNI">
                            </div>
                            </div>  

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>GT</label>
                            <input type="number" class="form-control" id="JUNI"  name="JUNI">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:-15px;margin-right:1px">   
                            <h4>JULI:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:0px;margin-right:1px">   
                            <h4>AGUSTUS:</h4> 
                            <hr />
                             </div>
                            <hr>

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="JULI_UNIT"  name="JULI">
                            </div>
                            </div>  

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px"> 
                            <label>GT</label>
                            <input type="number" class="form-control" id="JULI"  name="JULI">
                            </div> 
                            </div> 

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="AGUSTUS_UNIT"  name="AGUSTUS">
                            </div>
                            </div> 

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px"> 
                            <label>GT</label>
                            <input type="number" class="form-control" id="AGUSTUS"  name="AGUSTUS">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:-15px;margin-right:1px">   
                            <h4>SEPTEMBER:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:0px;margin-right:1px">   
                            <h4>OKTOBER:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="SEPTEMBER_UNIT"  name="SEPTEMBER">
                            </div>
                            </div>  

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px"> 
                            <label>GT</label>
                            <input type="number" class="form-control" id="SEPTEMBER"  name="SEPTEMBER">
                            </div> 
                            </div> 

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="OKTOBER_UNIT"  name="OKTOBER">
                            </div>
                            </div> 

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px"> 
                            <label>GT</label>
                            <input type="number" class="form-control" id="OKTOBER"  name="OKTOBER">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:-15px;margin-right:1px">   
                            <h4>NOVEMBER:</h4>   
                            <hr />                          
                             </div>

                             <div class="col-lg-6 col-md-6 col-sm-12 col-12" style="margin-left:0px;margin-right:1px">   
                            <h4>DESEMBER:</h4> 
                            <hr />
                             </div>

                             <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="NOVEMBER_UNIT"  name="NOVEMBER">
                            </div>
                            </div> 
                           
                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px"> 
                            <label>GT</label>
                            <input type="number" class="form-control" id="NOVEMBER"  name="NOVEMBER">
                            </div> 
                            </div> 

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>UNIT</label>
                            <input type="number" class="form-control" id="DESEMBER_UNIT"  name="DESEMBER">
                            </div>
                            </div>  

                            <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px"> 
                            <label>GT</label>
                            <input type="number" class="form-control" id="DESEMBER"  name="DESEMBER">
                            </div> 
                            </div>                     

                        <hr/>
                   
                        <div class="btn"  id="simpan_load" style="display:auto;margin-left:0%">                
							       <a href=""  class="btn btn-primary" id="simpan">Simpan</a>
                            <a href="<?php echo site_url('tps_online/rkap_trafik_kapal/listview') ?>" class="btn btn-default">Kembali</a>
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
            
                var param = {                         
					'TERMINAL' : $('#TERMINAL').val(),
					// 'PELAYARAN' : $('#PELAYARAN').val(),         
                    'TAHUN' : $('#TAHUN').val(),
                    'SATUAN' : 'GT',
                    'JANUARI' : $('#JANUARI').val(),
                    'FEBRUARI' : $('#FEBRUARI').val(),
                    'MARET' : $('#MARET').val(),
                    'APRIL' : $('#APRIL').val(),
                    'MEI' : $('#MEI').val(),
                    'JUNI' : $('#JUNI').val(),
                    'JULI' : $('#JULI').val(),
                    'AGUSTUS' : $('#AGUSTUS').val(),
                    'SEPTEMBER' : $('#SEPTEMBER').val(),
                    'OKTOBER' : $('#OKTOBER').val(), 
                    'NOVEMBER' : $('#NOVEMBER').val(),
                    'DESEMBER' : $('#DESEMBER').val()  
                }
                var param1 = {                         
					'TERMINAL' : $('#TERMINAL').val(),			       
                    'TAHUN' : $('#TAHUN').val(),
                    'SATUAN' : 'UNIT',
                    'JANUARI' : $('#JANUARI_UNIT').val(),
                    'FEBRUARI' : $('#FEBRUARI_UNIT').val(),
                    'MARET' : $('#MARET_UNIT').val(),
                    'APRIL' : $('#APRIL_UNIT').val(),
                    'MEI' : $('#MEI_UNIT').val(),
                    'JUNI' : $('#JUNI_UNIT').val(),
                    'JULI' : $('#JULI_UNIT').val(),
                    'AGUSTUS' : $('#AGUSTUS_UNIT').val(),
                    'SEPTEMBER' : $('#SEPTEMBER_UNIT').val(),
                    'OKTOBER' : $('#OKTOBER_UNIT').val(), 
                    'NOVEMBER' : $('#NOVEMBER_UNIT').val(),
                    'DESEMBER' : $('#DESEMBER_UNIT').val()    
                 }
                 if (param.JANUARI == ""){
                    param.JANUARI = 0;                    
                 }  if (param.FEBRUARI == ""){
                    param.FEBRUARI = 0;                    
                 }  if (param.MARET == ""){
                    param.MARET = 0;                    
                 }  if (param.APRIL == ""){
                    param.APRIL = 0;                    
                 }  if (param.MEI == ""){
                    param.MEI = 0;                    
                 }  if (param.JUNI == ""){
                    param.JUNI = 0;                    
                 }  if (param.JULI == ""){
                    param.JULI = 0;                    
                 }  if (param.AGUSTUS == ""){
                    param.AGUSTUS = 0;                    
                 }  if (param.SEPTEMBER == ""){
                    param.SEPTEMBER = 0;                    
                 }  if (param.OKTOBER == ""){
                    param.OKTOBER = 0;                    
                 }  if (param.NOVEMBER == ""){
                    param.NOVEMBER = 0;                    
                 }  if (param.DESEMBER == ""){
                    param.DESEMBER = 0;                    
                 }   

                 if (param1.JANUARI == ""){
                    param1.JANUARI = 0;                    
                 }  if (param1.FEBRUARI == ""){
                    param1.FEBRUARI = 0;                    
                 }  if (param1.MARET == ""){
                    param1.MARET = 0;                    
                 }  if (param1.APRIL == ""){
                    param1.APRIL = 0;                    
                 }  if (param1.MEI == ""){
                    param1.MEI = 0;                    
                 }  if (param1.JUNI == ""){
                    param1.JUNI = 0;                    
                 }  if (param1.JULI == ""){
                    param1.JULI = 0;                    
                 }  if (param1.AGUSTUS == ""){
                    param1.AGUSTUS = 0;                    
                 }  if (param1.SEPTEMBER == ""){
                    param1.SEPTEMBER = 0;                    
                 }  if (param1.OKTOBER == ""){
                    param1.OKTOBER = 0;                    
                 }  if (param1.NOVEMBER == ""){
                    param1.NOVEMBER = 0;                    
                 }  if (param1.DESEMBER == ""){
                    param1.DESEMBER = 0;                    
                 }       
                     
                 
				console.log(param);
				is_error = false;
				if(!param.TERMINAL || param.TERMINAL == ""){
					$('#PERIODE_BULAN').parent().addClass('has-error');
					add_validation_popover('#TERMINAL', 'TERMINAL Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.TAHUN || param.TAHUN== ""){
					$('#TAHUN').parent().addClass('has-error');
					add_validation_popover('#TAHUN', 'TAHUN Harus diisi');
					
					is_error = true;
				}

               
				console.log('err', is_error);

               
                    if(is_error){              
            
                       $.notify("Harap perbaiki field yang ditandai","error");  
                  
                    }else{                       	
            
				
					var url = bs.siteURL + 'FormTrafik/save/' + bs.token;
                    console.log('url', url)
                
                    $.post(url, param, function(data){
                    
                    console.log(data);                                                               
                        
                    var datas = data.split(':');
                        console.log('list', datas);
                        for (var i=0; i< datas.length;i++){
                        if (datas[i] == '"Data sudah tersedia"}'){
                            var trafik = false;
                            break;
                        } else {
                            var trafik = true;
                        }
                        }
                						
			
						if (trafik == false) {                    
						   sc_alert('Error', 'Data sudah tersedia');
						} else if(trafik == true){                           
				
                           window.location.href = bs.siteURL + 'tps_online/rkap_trafik_kapal/listview';   
                        }
                           
                        });
                         
                        $.post(url, param1, function(data){
                    
                    console.log(data);                                                               
                        
                    var datas = data.split(':');
                        console.log('list', datas);
                        for (var i=0; i< datas.length;i++){
                        if (datas[i] == '"Data sudah tersedia"}'){
                            var trafik = false;
                            break;
                        } else {
                            var trafik = true;
                        }
                        }
           						
			
						if (trafik == false) {                    
						   sc_alert('Error', 'Data sudah tersedia');
						} else if(trafik == true){                                 
					       $.notify("Data berhasil ditambahkan", "success");
                           window.location.href = bs.siteURL + 'tps_online/rkap_trafik_kapal/listview';   
                        }
                           
                        });
                       
                       return false;
				   
                    }
                       
      
                                
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