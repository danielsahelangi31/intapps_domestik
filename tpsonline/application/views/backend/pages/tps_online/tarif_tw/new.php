<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
    <style>
        .extra {
            display:none;
        }

        .semester {
            display:none;
        }
    </style>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">TARIF TW</h1>
			
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

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Komoditi<b class="text-danger">*</b></label>
                                        <select class="form-control" id="KOMODITI" name="KOMODITI">
                                            <option value="">-- PILIH --</option>
                                            <?php
                                            foreach ($datarkap as $make){
                                                ?>
                                                <option value="<?php echo $make->NAMA; ?>" ><?php echo $make->NAMA; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                </div>  
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Pelayanan</label>
                                            <select class="form-control" id="PELAYANAN" name="PELAYANAN">
                                                <option value="">-- PILIH --</option>
                                                <?php
                                                foreach ($dataPelayanan as $make){
                                                    ?>
                                                    <option value="<?php echo $make->NAMA; ?>" ><?php echo $make->NAMA; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                </div>  
                            </div>   

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Golongan</label>
                                          <select class="form-control" name="GOLONGAN" id="GOLONGAN">
                                          <option value="">-- PILIH --</option>
                                                <?php
                                                foreach ($dataGolongan as $make){
                                                    ?>
                                                    <option value="<?php echo $make->NAMA; ?>" ><?php echo $make->NAMA; ?></option>
                                                    <?php
                                                }
                                                ?>                                  
                                                    
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
                      
                            <input type="radio" id="pertahun" name="PER" value="pertahun">
                            <label for="pertahun">Per Tahun</label><br>

                            <input type="radio" id="persemester" name="PER" value="persemester">
                            <label for="persemester">Per Semester</label><br>

                            <input type="radio" id="pertriwulan" name="PER" value="pertriwulan">
                            <label for="pertriwulan">Per Triwulan</label>
                    
            
                         <div class="extra" id="extra_tahun">
                             <h3>Tarif Per Tahun</h3>    
                            <hr /> 
                            <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label class="col-form-label">TARIF:</label>
                            <input type="number" class="form-control" id="TARIF" name="TARIF"/>
                            </div>    
                         </div> 
                         
                         <div class="semester" id="extra_semester"> 
                           <h3>Tarif Per Semester</h3>    
                            <hr />                 
                             <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label class="col-form-label">TARIF I:</label>
                            <input type="number" class="form-control" id="TARIF_1" name="TARIF_1"/>
                            </div>  

                            <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label>TARIF II: </label>
                            <input type="number" class="form-control" id="TARIF_2"  name="TARIF_2">
                            </div>                	                        
                    
                        </div> 

                        <div class="extra" id="extra_triwulan">
                            <h3>Tarif Per Triwulan</h3>                
                            <h4>Triwulan I dan II</h4>    
                            <hr /> 
                            <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label class="col-form-label">TARIF :</label>
                            <input type="number" class="form-control" id="TARIF_I" name="TARIF_I"/>
                            </div>  
                            
                            <h4>Triwulan III dan IV</h4>    
                            <hr /> 
                            <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label>TARIF : </label>
                            <input type="number" class="form-control" id="TARIF_II"  name="TARIF_II">
                            </div>                	                        
                    
                        </div> 
                            <hr/>
                   
                        <div class="btn"  id="simpan_load" style="display:auto;margin-left:0%">                    
							<a href=""  class="btn btn-primary" id="simpan">Simpan</a>
                            <a href="<?php echo site_url('tps_online/tarif_tw/listview') ?>" class="btn btn-default">Kembali</a>
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
     
        $('#pertahun').click(function() {
          $("#extra_tahun").show();   
          $type = 'Per Tahun';    
        }); 

        $('#persemester').click(function() {     
            $("#extra_semester").show();   
            $type = 'Per Semester';
        });
        $('#pertriwulan').click(function() {
            $("#extra_triwulan").show();
            $type = 'Per Triwulan';          
        });
 
       
        $('#simpan').click(function(){

            if($type == 'Per Tahun'){
                if ( $('#TARIF').val() == ''){
                   $tarif = 0;
                   $tarif1 = '';
                   $tarif2 = '';
                   $tarifI = '';
                   $tarifII = '';
                } else {
                    $tarif = $('#TARIF').val();
                    $tarif1 = ''; 
                    $tarif2 = '';          
                    $tarifI = '';
                    $tarifII = '';
                }
            }

            if($type == 'Per Semester'){
                if ( $('#TARIF_1').val() == '' && $('#TARIF_2').val() == ''){
                   $tarif = '';
                   $tarif1 = 0;  
                   $tarif2 = 0;            
                   $tarifI = '';
                   $tarifII = '';
                } else if ( $('#TARIF_1').val() == ''){
                   $tarif1 = 0; 
                   $tarif2 = $('#TARIF_2').val();               
                   $tarif = '';
                   $tarifI = '';
                   $tarifII = '';
                } else if( $('#TARIF_2').val() == ''){
                   $tarif1 = $('#TARIF_1').val(); 
                   $tarif2 = 0;               
                   $tarif = '';
                   $tarifI = '';
                   $tarifII = '';
                } else {
                    $tarif = '';
                    $tarif1= $('#TARIF_1').val();
                    $tarif2=$('#TARIF_2').val();
                    $tarifI = '';
                    $tarifII = '';

                }
            }

            if($type == 'Per Triwulan'){
                if ( $('#TARIF_I').val() == '' && $('#TARIF_II').val() == ''){
                   $tarif = '';
                   $tarif1 = '';  
                   $tarif2 = '';            
                   $tarifI = 0;
                   $tarifII = 0;
                } else if ( $('#TARIF_I').val() == ''){
                   $tarif1 = ''; 
                   $tarif2 = '';               
                   $tarif = '';
                   $tarifI = 0;
                   $tarifII = $('#TARIF_II').val();
                } else if( $('#TARIF_II').val() == ''){
                   $tarif1 = ''; 
                   $tarif2 = '';               
                   $tarif = '';
                   $tarifI = $('#TARIF_I').val();
                   $tarifII = 0;
                } else {
                    $tarif = '';
                    $tarif1= '';
                    $tarif2= '';
                    $tarifI = $('#TARIF_I').val();
                    $tarifII = $('#TARIF_II').val();

                }
            }

            var param = {    
                    'TERMINAL' : $('#TERMINAL').val(),                     
					'KOMODITI' : $('#KOMODITI').val(),
					'PELAYANAN' : $('#PELAYANAN').val(),         
                    'GOLONGAN' : $('#GOLONGAN').val(),
                    'TAHUN' : $('#TAHUN').val(),  
                    'TARIF' : $tarif,                               
                    'TARIF_1' : $tarif1,
                    'TARIF_2' : $tarif2,    
                    'TARIF_I' : $tarifI,
                    'TARIF_II': $tarifII,          
                    'TYPE' : $type
                                   
                            
                }	
      
				console.log(param);
				is_error = false;
                if(!param.TERMINAL || param.TERMINAL == ""){
					$('#PERIODE_BULAN').parent().addClass('has-error');
					add_validation_popover('#TERMINAL', 'TERMINAL Harus dipilih');
					
					is_error = true;
				}
                
                if(!param.KOMODITI || param.KOMODITI == ""){
					$('#KOMODITI').parent().addClass('has-error');
					add_validation_popover('#KOMODITI', 'KOMODITI harus diisi');
					
					is_error = true;
				}
				
				if(!param.TAHUN || param.TAHUN== ""){
					$('#TAHUN').parent().addClass('has-error');
					add_validation_popover('#TAHUN', 'TAHUN harus diisi');
					
					is_error = true;
				}
        
				console.log('err', is_error)
				if(is_error){
                    console.log('err', is_error)
                    $.notify("Harap perbaiki field yang ditandai","error");  
	
				}else{				
					var urls = bs.siteURL + 'FormTarif/save/' + bs.token;
                    console.log('url', urls)
                               
					$.post(urls, param, function(data){           
                        console.log('list_tarif', data);
                        
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
                             window.location.href = bs.siteURL + 'tps_online/tarif_tw/listview';   
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