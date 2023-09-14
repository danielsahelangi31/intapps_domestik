<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">RKAP ARUS BARANG</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                             
                 
                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Terminal<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" readonly id="TERMINAL" name="TERMINAL" value="<?php echo $kunjung->TERMINAL ?>"/> 
                            
                                </div>  
                            </div>   

                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Jenis<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" readonly id="JENIS" name="JENIS" value="<?php echo $kunjung->JENIS ?>"/> 
                             
                                </div>  
                            </div>   

                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Komoditi<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" readonly id="KOMODITI" name="KOMODITI" value="<?php echo $kunjung->KOMODITI ?>"/> 
                                  
                                </div>  
                            </div>   

                             <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-13px;margin-right:1px">
                                <label>Tahun<b class="text-danger">*</b></label>
                                <input type="text" class="form-control" readonly id="TAHUN" name="TAHUN" value="<?php echo $kunjung->TAHUN ?>"/>             	

                                </div>                       
                            </div>              
                          
                                                       
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">                           
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">    
                                        <label>Satuan<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" readonly id="SATUAN" name="SATUAN" value="<?php echo $kunjung->SATUAN ?>"/>
                             
                              </div>  
                              </div>
                            
                             <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">     
                            <label class="col-form-label">JANUARI:<b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="JANUARI" name="JANUARI"  value="<?php echo $kunjung->JANUARI ?>"/>
                            </div> 
                            </div> 
                      
                                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>FEBRUARI: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="FEBRUARI"  name="FEBRUARI" value="<?php echo $kunjung->FEBRUARI ?>">
                            </div>
                            </div>	                        
                        
                                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>MARET: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="MARET"  name="MARET"  value="<?php echo $kunjung->MARET ?>">
                            </div> 
                            </div>	    

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>APRIL: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="APRIL"  name="APRIL"  value="<?php echo $kunjung->APRIL?>">
                            </div> 
                            </div>	      

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>MEI: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="MEI"  name="MEI"  value="<?php echo $kunjung->MEI ?>">
                            </div>
                            </div>	 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>JUNI: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="JUNI"  name="JUNI"  value="<?php echo $kunjung->JUNI ?>">
                            </div> 
                            </div>	    
                                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>JULI: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="JULI"  name="JULI"  value="<?php echo $kunjung->JULI ?>">
                            </div> 
                            </div>	    
                                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>AGUSTUS: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="AGUSTUS"  name="AGUSTUS"  value="<?php echo $kunjung->AGUSTUS ?>">
                            </div> 
                            </div>	    
                                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>SEPTEMBER: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="SEPTEMBER"  name="SEPTEMBER"  value="<?php echo $kunjung->SEPTEMBER ?>">
                            </div> 
                            </div>	    
                                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>OKTOBER: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="OKTOBER"  name="OKTOBER" value="<?php echo $kunjung->OKTOBER ?>">
                            </div> 
                            </div>	    
                           
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>NOVEMBER: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="NOVEMBER"  name="NOVEMBER"  value="<?php echo $kunjung->NOVEMBER ?>">
                            </div> 
                            </div>	    
                                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                             <div class="form-group" style="margin-left:-15px;margin-right:1px">  
                            <label>DESEMBER: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="DESEMBER"  name="DESEMBER"  value="<?php echo $kunjung->DESEMBER ?>">
                            </div> 
                            </div>	    

                        <div class="btn"  id="simpan_load" style="display:auto;margin-left:0%">                   
							<a href=""  class="btn btn-primary" id="update_arus">Simpan</a>
                            <a href="<?php echo site_url('tps_online/rkap_arus_barang/listview') ?>" class="btn btn-default">Kembali</a>
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
        
        $('#update_arus').click(function(){
                var now = new Date(Date.now()).toLocaleString("id-ID");
                console.log('now',now);         

                id = <?php echo $kunjung->id_barang ?>;
				var param = {   
                    'id_barang' : id,
                    'TERMINAL' : $('#TERMINAL').val(),
					'JENIS' : $('#JENIS').val(),         
                    'TAHUN' : $('#TAHUN').val(),
                    'KOMODITI' : $('#KOMODITI').val(),
                    'SATUAN' : $('#SATUAN').val(),
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
				
				is_error = false;
				console.log(param);
				
				if(!param.TERMINAL || param.TERMINAL == ""){
					$('#TERMINAL').parent().addClass('has-error');
					add_validation_popover('#TERMINAL', 'TERMINAL Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.TAHUN || param.TAHUN== ""){
					$('#TAHUN').parent().addClass('has-error');
					add_validation_popover('#TAHUN', 'TAHUN Harus diisi');
					
					is_error = true;
				}

                if(!param.JENIS || param.JENIS == ""){
					$('#JENIS').parent().addClass('has-error');
					add_validation_popover('#JENIS', 'JENIS Harus diisi');
					
					is_error = true;
				}

                if(!param.SATUAN || param.SATUAN == ""){
					$('#SATUAN').parent().addClass('has-error');
					add_validation_popover('#SATUAN', 'SATUAN Harus diisi');
					
					is_error = true;
				}

                
                if(!param.KOMODITI || param.KOMODITI == ""){
					$('#KOMODITI').parent().addClass('has-error');
					add_validation_popover('#KOMODITI', 'KOMODITI Harus diisi');
					
					is_error = true;
				}
                if(!param.JANUARI || param.JANUARI == ""){
					$('#JANUARI').parent().addClass('has-error');
					add_validation_popover('#JANUARI', 'JANUARI Harus diisi');
					
					is_error = true;
				}

                if(!param.FEBRUARI || param.FEBRUARI == ""){
					$('#FEBRUARI').parent().addClass('has-error');
					add_validation_popover('#FEBRUARI', 'FEBRUARI Harus diisi');
					
					is_error = true;
				}

                if(!param.MARET || param.MARET == ""){
					$('#MARET').parent().addClass('has-error');
					add_validation_popover('#MARET', 'MARET Harus diisi');
					
					is_error = true;
				}
                
                
                
                if(!param.APRIL || param.APRIL == ""){
					$('#APRIL').parent().addClass('has-error');
					add_validation_popover('#APRIL', 'APRIL Harus diisi');
					
					is_error = true;
				}

                
                if(!param.MEI || param.MEI == ""){
					$('#MEI').parent().addClass('has-error');
					add_validation_popover('#MEI', 'MEI Harus diisi');
					
					is_error = true;
				}

                
                if(!param.JUNI || param.MARET == ""){
					$('#JUNI').parent().addClass('has-error');
					add_validation_popover('#JUNI', 'JUNI Harus diisi');
					
					is_error = true;
				}


                if(!param.JULI || param.JULI == ""){
					$('#JULI').parent().addClass('has-error');
					add_validation_popover('#JULI', 'JULI Harus diisi');
					
					is_error = true;
				}

                
                if(!param.AGUSTUS || param.AGUSTUS == ""){
					$('#AGUSTUS').parent().addClass('has-error');
					add_validation_popover('#AGUSTUS', 'AGUSTUS Harus diisi');
					
					is_error = true;
				}

                
                if(!param.SEPTEMBER || param.SEPTEMBER == ""){
					$('#SEPTEMBER').parent().addClass('has-error');
					add_validation_popover('#SEPTEMBER', 'SEPTEMBER Harus diisi');
					
					is_error = true;
				}

                
                if(!param.OKTOBER || param.OKTOBER == ""){
					$('#OKTOBER').parent().addClass('has-error');
					add_validation_popover('#OKTOBER', 'OKTOBER Harus diisi');
					
					is_error = true;
				}

                
                if(!param.NOVEMBER || param.NOVEMBER == ""){
					$('#NOVEMBER').parent().addClass('has-error');
					add_validation_popover('#NOVEMBER', 'NOVEMBER Harus diisi');
					
					is_error = true;
				}

                
                if(!param.DESEMBER || param.DESEMBER == ""){
					$('#DESEMBER').parent().addClass('has-error');
					add_validation_popover('#DESEMBER', 'DESEMBER Harus diisi');
					
					is_error = true;
				}
			
				if(is_error){
                    $.notify("Harap perbaiki field yang ditandai","error");  

				}else{		
					var urls = bs.siteURL + 'FormArus/update_barang/' + bs.token;
                    console.log('url', urls)
                    $.notify("Data berhasil ditambahkan", "success");
                    window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview';   
					$.post(urls, param, function(data){
                        $('#simpan_load').show();
                        console.log('list_view1')
                       $.notify("Data berhasil ditambahkan", "success");
                       window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview';     
                        
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