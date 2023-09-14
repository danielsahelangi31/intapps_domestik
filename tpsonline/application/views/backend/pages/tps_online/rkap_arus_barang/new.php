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

                            <?php echo form_error('upload_vin_excel', '<div class="error">', '</div><br/>'); ?>
                
                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Terminal<b class="text-danger">*</b></label>
                                          <select class="form-control" name="TERMINAL" id="TERMINAL">
                                              <option value="">-- PILIH --</option>
                                              <option value="DOMESTIK">DOMESTIK</option>
                                              <option value="INTERNASIONAL">INTERNASIONAL</option>                                    
                                                    
                                         </select> 
                                </div>  
                            </div>   

                            <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Jenis<b class="text-danger">*</b></label>
                                          <select class="form-control" name="JENIS" id="JENIS">
                                              <option value="">-- PILIH --</option>
                                              <option value="EKSPOR">EKSPOR</option>
                                              <option value="IMPOR">IMPOR</option>   
                                              <option value="BONGKAR">BONGKAR</option>
                                              <option value="MUAT">MUAT</option>                                    
                                                    
                                         </select> 
                                </div>  
                            </div>   

                        <div class="col-lg-4 col-md-4 col-sm-12 col-12">
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
                          
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Komoditi<b class="text-danger">*</b></label>
                                          <select class="form-control" name="KOMODITI" id="KOMODITI">
                                              <option value="">-- PILIH --</option>
                                              <option value="MOBIL">MOBIL</option>
                                              <option value="ALAT BERAT">ALAT BERAT</option>     
                                              <option value="TRUCK/BUS">TRUCK/BUS</option>  
                                              <option value="PENUMPANG">PENUMPANG</option> 
                                              <option value="SEPEDA">SEPEDA</option> 
                                              <option value="GENERAL CARGO">GENERAL CARGO</option>  
                                              <option value="MOTOR">MOTOR</option>                       
                                                    
                                         </select> 
                                </div>  
                            </div>   

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                        <label>Satuan<b class="text-danger">*</b></label>
                                          <select class="form-control" name="SATUAN" id="SATUAN">
                                              <option value="">-- PILIH --</option>
                                              <option value="GT">GT</option>                                              
                                              <option value="UNIT">UNIT</option>   
                                              <option value="M3">M3</option>
                                              <option value="TON">TON</option>  
                                              <option value="PKGS">PKGS</option>
                                              <option value="ORANG">ORANG</option>
                                              <option value="UNIT/M3">UNIT/M3</option>    
                                              <option value="TON/M3">TON/M3</option> 
                                                    
                                         </select> 
                                </div>   
                            </div>   
                                   
                                           
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label class="col-form-label">JANUARI:<b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="JANUARI" name="JANUARI"/>
                            </div> 
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>FEBRUARI: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="FEBRUARI"  name="FEBRUARI">
                            </div>
                            </div>	                        
                        
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>MARET: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="MARET"  name="MARET">
                            </div> 
                            </div>

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>APRIL: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="APRIL"  name="APRIL">
                            </div> 
                            </div> 
                        
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>MEI: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="MEI"  name="MEI">
                            </div> 
                            </div>                            
                            
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>JUNI: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="JUNI"  name="JUNI">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>JULI: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="JULI"  name="JULI">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>AGUSTUS: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="AGUSTUS"  name="AGUSTUS">
                            </div>
                            </div>  

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>SEPTEMBER: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="SEPTEMBER"  name="SEPTEMBER">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>OKTOBER: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="OKTOBER"  name="OKTOBER">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>NOVEMBER: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="NOVEMBER"  name="NOVEMBER">
                            </div> 
                            </div> 

                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;margin-right:1px">
                            <label>DESEMBER: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="DESEMBER"  name="DESEMBER">
                            </div> 
                            </div> 

                        <hr/>
                   
                        <div class="btn"  id="simpan_load" style="display:auto;margin-left:0%">                          
							<a href=""  class="btn btn-primary" id="simpan">Simpan</a>
                            <a href="<?php echo site_url('tps_online/rkap_arus_barang/listview') ?>" class="btn btn-default">Kembali</a>
                        </div>
                  
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

        $("#upload_vin_excel").click(function() {
            $('#excel-upload').val('');
            $("#upload_vin_excel").val("");
            console.log('upload xls1')
        });

        $(document)
            .on('change', '.btn-file :file', function() {
                var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
                console.log('upload xls')
            });

	$(document).ready(function(){
        
        $('#KOMODITI').click(function() {
          console.log('komoditi:', $('#KOMODITI').val());   
            if($('#KOMODITI').val() == "MOBIL") {              
                    $('#SATUAN').val('UNIT');
                
            } else if($('#KOMODITI').val() == "ALAT BERAT"){
                    $('#SATUAN').val('UNIT');
            } else if($('#KOMODITI').val() == "GENERAL CARGO"){
                    $('#SATUAN').val('PKGS');            
             } else if($('#KOMODITI').val() == "TRUCK/BUS"){
                    $('#SATUAN').val('UNIT');
            } else if($('#KOMODITI').val() == "MOTOR"){
                    $('#SATUAN').val('UNIT');            
            } else if($('#KOMODITI').val() == "PENUMPANG"){
                    $('#SATUAN').val('ORANG');  
            } else if($('#KOMODITI').val() == "SEPEDA"){
                    $('#SATUAN').val('UNIT');            
            } else {
                    $('#SATUAN').val('');
            }
        });

        $('#TERMINAL').click(function() {
          console.log('terminal:', $('#TERMINAL').val());   
            if($('#TERMINAL').val() == "DOMESTIK") {              
                    $('#JENIS').val('BONGKAR');     
                
            } else if($('#TERMINAL').val() == "INTERNASIONAL"){
                    $('#JENIS').val('EKSPOR');
            } 
        });


        
        $("#upload_vin_excel").change(function() {
                if ($("#upload_vin_excel").val() !== "") {
                    console.log('upload xls2')
                }
            })

            $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;

                if (input.length) {
                    input.val(log);
                } else {
                    if (log) alert(log);
                }
            });

        $('#simpan').click(function(){
            var param = {                         
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
				
				
				console.log(param);
				is_error = false;
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
				console.log('err', is_error)
				if(is_error){
                    console.log('err', is_error)
                    $.notify("Harap perbaiki field yang ditandai","error");  
			
				}else{		
		
				
					var urls = bs.siteURL + 'FormArus/save/' + bs.token;
                    console.log('url', urls)
                               
					$.post(urls, param, function(data){
         
                        console.log('list_view1', data);
                        
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
                             window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview';   
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