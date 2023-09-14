<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">RKAP PENDAPATAN</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                             
                 
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Komoditi<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" readonly id="KOMODITI" name="KOMODITI" value="<?php echo $kunjung->KOMODITI ?>"/> 
                                          <!-- <select class="form-control" name="KOMODITI" id="KOMODITI">
                                              <option value="<?php echo $kunjung->KOMODITI?>"><?php echo $kunjung->KOMODITI?></option>
                                              <option value="MOBIL">MOBIL</option>
                                              <option value="ALAT BERAT">ALAT BERAT</option>                                    
                                                    
                                         </select>  -->
                                </div>  
                            </div>   

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Pelayanan<b class="text-danger">*</b></label>
                                        <input type="text" class="form-control" readonly id="PELAYANAN" name="PELAYANAN" value="<?php echo $kunjung->PELAYANAN ?>"/> 
                                          <!-- <select class="form-control" name="TERMINAL" id="TERMINAL">
                                              <option value="<?php echo $kunjung->PELAYANAN?>"><?php echo $kunjung->PELAYANAN?></option>
                                              <option value="DOMESTIK">DOMESTIK</option>
                                              <option value="INTERNASIONAL">INTERNASIONAL</option>                                    
                                                    
                                         </select>  -->
                                </div>  
                            </div>   

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Golongan</label>
                                        <input type="text" class="form-control" readonly id="GOLONGAN" name="GOLONGAN" value="<?php echo $kunjung->GOLONGAN ?>"/> 
                                        <!-- <select class="form-control" name="JENIS" id="JENIS">
                                              <option value="<?php echo $kunjung->GOLONGAN?>"><?php echo $kunjung->GOLONGAN?></option>
                                              <option value="EKSPOR">EKSPOR</option>
                                              <option value="IMPOR">IMPOR</option>                                    
                                                    
                                         </select>  -->
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
                        <!-- <a href="<?php echo site_url('tps_online/form/finalize/' . $row->ID_MONITORING_HEADER . '/' . $grid_state) ?>" class="btn btn-primary">Simpan</a> -->
                             <!-- <a href="<?php echo site_url('tps_online/form/finalize') ?>" class="btn btn-primary">Simpan</a> -->
							<a href=""  class="btn btn-primary" id="update_arus">Simpan</a>
                            <a href="<?php echo site_url('tps_online/rkap_pendapatan/listview') ?>" class="btn btn-default">Kembali</a>
                        </div>
                         <!-- <div class="col-12" style="margin-top:20px;">
                             <button type="submit" class="btn btn-info" id="simpan">SAVE</button>                         
                        </div>        -->
                                </form>
                            </div>
                        </div>
                    </div>
			</div>
		
			
			<?php echo form_close() ?>
		
			<div id="kampret_loader"></div>
		</div><!-- /.container -->
        </div>
    </div>
		
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
	
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/notify.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script type="text/javascript">
	/* on AJAX Load Animator Overlay
	//  */
    // DateFormat df = new SimpleDateFormat("yyyy-MM-dd'T'hh:mm:ssZ");
    // //Date result;

	$(document).ready(function(){
        
        $('#update_arus').click(function(){
                var now = new Date(Date.now()).toLocaleString("id-ID");
                console.log('now',now);         

                id = <?php echo $kunjung->id_pendapatan ?>;
				var param = {   
                    'id_pendapatan' : id,
                    'PELAYANAN' : $('#PELAYANAN').val(),
					'GOLONGAN' : $('#GOLONGAN').val(),         
                    'TAHUN' : $('#TAHUN').val(),
                    'KOMODITI' : $('#KOMODITI').val(),
                    // 'SATUAN' : $('#SATUAN').val(),
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
				if(!param.PELAYANAN || param.PELAYANAN == ""){
					$('#PELAYANAN').parent().addClass('has-error');
					add_validation_popover('#PELAYANAN', 'PELAYANAN harus dipilih');
					
					is_error = true;
				}
				
				if(!param.TAHUN || param.TAHUN== ""){
					$('#TAHUN').parent().addClass('has-error');
					add_validation_popover('#TAHUN', 'TAHUN harus diisi');
					
					is_error = true;
				}

                // if(!param.GOLONGAN || param.GOLONGAN == ""){
				// 	$('#GOLONGAN').parent().addClass('has-error');
				// 	add_validation_popover('#GOLONGAN', 'GOLONGAN harus diisi');
					
				// 	is_error = true;
				// }

                // if(!param.SATUAN || param.SATUAN == ""){
				// 	$('#SATUAN').parent().addClass('has-error');
				// 	add_validation_popover('#SATUAN', 'SATUAN harus diisi');
					
				// 	is_error = true;
				// }

                
                if(!param.KOMODITI || param.KOMODITI == ""){
					$('#KOMODITI').parent().addClass('has-error');
					add_validation_popover('#KOMODITI', 'KOMODITI harus diisi');
					
					is_error = true;
				}
                if(!param.JANUARI || param.JANUARI == ""){
					$('#JANUARI').parent().addClass('has-error');
					add_validation_popover('#JANUARI', 'JANUARI harus diisi');
					
					is_error = true;
				}

                if(!param.FEBRUARI || param.FEBRUARI == ""){
					$('#FEBRUARI').parent().addClass('has-error');
					add_validation_popover('#FEBRUARI', 'FEBRUARI harus diisi');
					
					is_error = true;
				}

                if(!param.MARET || param.MARET == ""){
					$('#MARET').parent().addClass('has-error');
					add_validation_popover('#MARET', 'MARET harus diisi');
					
					is_error = true;
				}
                
                
                
                if(!param.APRIL || param.APRIL == ""){
					$('#APRIL').parent().addClass('has-error');
					add_validation_popover('#APRIL', 'APRIL harus diisi');
					
					is_error = true;
				}

                
                if(!param.MEI || param.MEI == ""){
					$('#MEI').parent().addClass('has-error');
					add_validation_popover('#MEI', 'MEI harus diisi');
					
					is_error = true;
				}

                
                if(!param.JUNI || param.MARET == ""){
					$('#JUNI').parent().addClass('has-error');
					add_validation_popover('#JUNI', 'JUNI harus diisi');
					
					is_error = true;
				}


                if(!param.JULI || param.JULI == ""){
					$('#JULI').parent().addClass('has-error');
					add_validation_popover('#JULI', 'JULI harus diisi');
					
					is_error = true;
				}

                
                if(!param.AGUSTUS || param.AGUSTUS == ""){
					$('#AGUSTUS').parent().addClass('has-error');
					add_validation_popover('#AGUSTUS', 'AGUSTUS harus diisi');
					
					is_error = true;
				}

                
                if(!param.SEPTEMBER || param.SEPTEMBER == ""){
					$('#SEPTEMBER').parent().addClass('has-error');
					add_validation_popover('#SEPTEMBER', 'SEPTEMBER harus diisi');
					
					is_error = true;
				}

                
                if(!param.OKTOBER || param.OKTOBER == ""){
					$('#OKTOBER').parent().addClass('has-error');
					add_validation_popover('#OKTOBER', 'OKTOBER harus diisi');
					
					is_error = true;
				}

                
                if(!param.NOVEMBER || param.NOVEMBER == ""){
					$('#NOVEMBER').parent().addClass('has-error');
					add_validation_popover('#NOVEMBER', 'NOVEMBER harus diisi');
					
					is_error = true;
				}

                
                if(!param.DESEMBER || param.DESEMBER == ""){
					$('#DESEMBER').parent().addClass('has-error');
					add_validation_popover('#DESEMBER', 'DESEMBER harus diisi');
					
					is_error = true;
				}
			
				//console.log('err', is_error)
				if(is_error){
                    $.notify("Harap perbaiki field yang ditandai","error");  
					//sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
				}else{		
					// $('#simpan_load').show();
				
					var urls = bs.siteURL + 'FormPendapatan/update_barang/' + bs.token;
                    console.log('url', urls)
                    $.notify("Data berhasil ditambahkan", "success");
                    window.location.href = bs.siteURL + 'tps_online/rkap_pendapatan/listview';   
					$.post(urls, param, function(data){
                        $('#simpan_load').show();
                        console.log('list_view1')
                       $.notify("Data berhasil ditambahkan", "success");
                       window.location.href = bs.siteURL + 'tps_online/rkap_pendapatan/listview';     
                        
                        reset_form();
						if(data === 'Berhasil'){
                            console.log('list_view2',)
                           // $.notify("Access granted", "success");
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