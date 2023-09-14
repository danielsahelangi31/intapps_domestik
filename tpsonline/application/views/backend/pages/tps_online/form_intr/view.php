<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">MONITORING BM KAPAL INTERNASIONAL</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                          
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-right:0px">
                            <label>Nama KAPAL : <b class="text-danger">*</b></label>
                            <input type="text" class="form-control right" readonly id="NAMA_KAPAL" name="NAMA_KAPAL" value="<?php echo $kunjung->NAMA_KAPAL ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label>Kade/Dermaga:<b class="text-danger">*</b></label>
                            <input type="text" class="form-control right" readonly id="KADE_DERMAGA" name="KADE_DERMAGA" value="<?php echo $kunjung->KADE_DERMAGA ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="form-group" style="margin-left:-15px;width:50%;"> 
                                <label class="col-form-label">Voyage<b class="text-danger">*</b></label>
                                <input type="text" class="form-control right" readonly id="VOYAGE" name="VOYAGE" value="<?php echo $kunjung->VOYAGE ?>"/>
                            </div>
                        </div>        
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">RENCANA BONGKAR<b class="text-danger">*</b></label>
                                <input type="number" class="form-control right" readonly id="RENCANA_BONGKAR" name="RENCANA_BONGKAR" value="<?php echo $kunjung->RENCANA_BONGKAR ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">RENCANA MUAT<b class="text-danger">*</b></label>
                                <input type="number" class="form-control" readonly id="RENCANA_MUAT" name="RENCANA_MUAT" value="<?php echo $kunjung->RENCANA_MUAT ?>"/>
                            </div>
                        </div>
                
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ETA<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control right" readonly id="ETA" name="ETA" value="<?php echo $ETA ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ATA<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control right" id="ATA" name="ATA" value="<?php echo $ATA ?>"/>
                            <!-- value="2018-06-12T19:30"/> -->
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ETB<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control right" readonly id="ETB" name="ETB" value="<?php echo $ETB ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ATB<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control right" id="ATB" name="ATB" value="<?php echo $ATB ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ETD<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control" readonly id="ETD" name="ETD" value="<?php echo $ETD ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ATD<b class="text-danger">*</b></label>
                            <input  type="datetime-local" class="form-control" id="ATD" name="ATD" value="<?php echo $ATD ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group"style="margin-right:0px">
                            <label >Commence:</label>
                            <input type="datetime-local" class="form-control" id="COMMENCE" name="COMMENCE"  value="<?php echo $COMMENCE ?>"/>
                        </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group"style="margin-right:0px">
                            <label>Completed:</label>
                            <input type="datetime-local" class="form-control" id="COMPLETE" name="COMPLETE"  value="<?php echo $COMPLETE ?>"/>
                            </div> 
                         </div> 
                   
                         <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">                        
							<label >Shift</label>						
								<select class="form-control" id="SHIFT" name="SHIFT">
                                <option value="<?php echo $kunjung->SHIFT ?>"> <?php echo $kunjung->SHIFT ?></option>
									<?php
									foreach($TYPE_SHIFT as $row){								
									?>
									<option value="<?php echo $row->NAMA_SHIFT ?>" <?php if(@$detail[0]->TYPE_SHIFT==$row->NAMA_SHIFT) echo 'selected';?>>
									<?php echo $row->NAMA_SHIFT.' '.$row->ID_SHIFT ?></option>
									<?php
									}
									?>
								</select>						
						</div>
                    
                        <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">    
                                <label>Activity<b class="text-danger">*</b></label>
                              
								<select class="form-control" id="ACTIVITY" name="ACTIVITY">
                                <option value="<?php echo $kunjung->ACTIVITY ?>"> <?php echo $kunjung->ACTIVITY ?></option>
									<?php
									foreach($TYPE_ACTIVITY as $row){								
									?>
									<option value="<?php echo $row->NAMA_ACTIVITY ?>" <?php if(@$detail[0]->TYPE_ACTVITY==$row->NAMA_ACTIVITY) echo 'selected';?>>
									<?php echo $row->NAMA_ACTIVITY.' '.$row->ID_ACTIVITY ?></option>
									<?php
									}
									?>
								</select>
							                             
                            </div>
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">  
                            <label class="col-form-label">Time Start<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control" id="TIME_START" name="TIME_START" value="<?php echo $TIME_START ?>"/>                            
                            </div>              
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">    
                            <label class="col-form-label">Time End<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control" id="TIME_END" name="TIME_END" value="<?php echo $TIME_END?>"/>
                            </div> 
                      
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">    
                            <label>REALISASI BONGKAR: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="REALISASI_BONGKAR" value="<?php echo $kunjung->REALISASI_BONGKAR ?>" name="REALISASI_BONGKAR">
                            </div>                            	                        
                        
                            <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">    
                            <label>REALISASI MUAT: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="REALISASI_MUAT" value="<?php echo $kunjung->REALISASI_MUAT ?>" name="REALISASI_MUAT">
                            </div> 
                        
                        <hr/>
                   
                        <div class="btn" id="simpan_load"  style="display:auto;margin-left:23%"></div>                 
							<a href="#" class="btn btn-primary" id="simpann">Simpan</a>
                            <a href="<?php echo site_url('tps_online/form_intr/listview') ?>" class="btn btn-default">Kembali</a>                     
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
   
        $('#simpann').click(function(){
            			
				var etay = $('#ETA').val();  
                var mydate = new Date(etay);
                var ETA = mydate.toLocaleString("id-ID")
                console.log('datay', ETA)

                var etby = $('#ETB').val();  
                var mydateb = new Date(etby);
                var ETB = mydateb.toLocaleString("id-ID")
                console.log('dataB', ETB)

                var etdy = $('#ETD').val();  
                var mydated = new Date(etdy);
                var ETD = mydated.toLocaleString("id-ID")

                var atay = $('#ATA').val();  
                var mydatea = new Date(atay);
                var ATA = mydatea.toLocaleString("id-ID")
                
                var atby = $('#ATB').val();  
                var mydatec = new Date(atby);
                var ATB = mydatec.toLocaleString("id-ID")

                var atad = $('#ATD').val();  
                var mydatee = new Date(atad);
                var ATD = mydatee.toLocaleString("id-ID")

                var commence = $('#COMMENCE').val();  
                var mydatem = new Date(commence);
                var COMMENCE = mydatem.toLocaleString("id-ID")

                var complete = $('#COMPLETE').val();  
                var mydatet = new Date(complete);
                var COMPLETE = mydatet.toLocaleString("id-ID")

                var timestart = $('#TIME_START').val();  
                var mydatest = new Date(timestart);
                var TIME_START = mydatest.toLocaleString("id-ID");

                var timend = $('#TIME_END').val();  
                var mydatex = new Date(timend);
                var TIME_END = mydatex.toLocaleString("id-ID")

                var now = new Date(Date.now()).toLocaleString("id-ID");
                console.log('now',now);

                id_header = <?php echo $kunjung->ID_HEADER ?>;
                id_monitoring_detail = <?php echo $kunjung->ID_MONITORING_HEADER ?>;
           
				var param = {             
                    'ID_HEADER' : id_header,
                    'ID_MONITORING_DETAIL' : id_monitoring_detail,
                    'TANGGAL_TIME' : now,
					'NAMA_KAPAL' : $('#NAMA_KAPAL').val(),				
                    'VOYAGE' : $('#VOYAGE').val(),                 
                    'RENCANA_BONGKAR' : $('#RENCANA_BONGKAR').val(),
                    'RENCANA_MUAT' : $('#RENCANA_MUAT').val(),
                    'ETA' : ETA,
                    'ATA' : ATA,
                    'ETB' : ETB,
                    'ATB' : ATB,
                    'ETD' : ETD,
                    'ATD' : ATD,
                    'COMMENCE' : COMMENCE,
                    'COMPLETE' : COMPLETE,
                    'SHIFT' : $('#SHIFT').val(),
                    'ACTIVITY' : $('#ACTIVITY').val(),
                    'TIME_START' : TIME_START,
                    'TIME_END' : TIME_END,
                    'REALISASI_BONGKAR' : $('#REALISASI_BONGKAR').val(),
                    'REALISASI_MUAT' : $('#REALISASI_MUAT').val(),
                        
                }		
				
				console.log(param);			
                is_error = false;				
      
	    if(!param.ACTIVITY || param.ACTIVITY == "OPEN RAMPDOOR"){				
		
                if(!param.ATA || param.ATA == "" || param.ATA == "1/1/1970 07.00.00"){
					$('#ATA').parent().addClass('has-error');
					add_validation_popover('#ATA', 'ATA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ATB || param.ATB == "" || param.ATB == "1/1/1970 07.00.00"){
					$('#ATB').parent().addClass('has-error');
					add_validation_popover('#ATB', 'ATB Harus diisi');
					
					is_error = true;
				}
             
                if(!param.ACTIVITY || param.ACTIVITY == ""){
					$('#ACTIVITY').parent().addClass('has-error');
					add_validation_popover('#ACTIVITY', 'ACTIVITY Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_START || param.TIME_START == "" || param.TIME_START == "1/1/1970 07.00.00"){
					$('#TIME_START').parent().addClass('has-error');
					add_validation_popover('#TIME_START', 'TIME START Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_END || param.TIME_END== "" || param.TIME_END == "1/1/1970 07.00.00"){
					$('#TIME_END').parent().addClass('has-error');
					add_validation_popover('#TIME_END', 'TIME END Harus diisi');
					
					is_error = true;
				}
                if(!param.REALISASI_BONGKAR || param.REALISASI_BONGKAR == "" ){
					$('#REALISASI_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#REALISASI_BONGKAR', 'REALISASI BONGKAR Harus diisi');
					
					is_error = true;
				}
                if(!param.REALISASI_MUAT || param.REALISASI_MUAT == "" ){
					$('#REALISASI_MUAT').parent().addClass('has-error');
					add_validation_popover('#REALISASI_MUAT', 'REALISASI MUAT Harus diisi');
					
					is_error = true;
                }
        }
        if(param.ACTIVITY == 'VESSEL DEPARTURE'){
         
                if(!param.ATA || param.ATA == "" || param.ATA == "1/1/1970 07.00.00"){
					$('#ATA').parent().addClass('has-error');
					add_validation_popover('#ATA', 'ATA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ATB || param.ATB == "" || param.ATB == "1/1/1970 07.00.00"){
					$('#ATB').parent().addClass('has-error');
					add_validation_popover('#ATB', 'ATB Harus diisi');
					
					is_error = true;
				}
   
                if(!param.COMMENCE || param.COMMENCE == "" || param.COMMENCE == "1/1/1970 07.00.00"){
					$('#COMMENCE').parent().addClass('has-error');
					add_validation_popover('#COMMENCE', 'COMMENCE Harus diisi');
					
					is_error = true;
				}
             
                if(!param.ACTIVITY || param.ACTIVITY == ""){
					$('#ACTIVITY').parent().addClass('has-error');
					add_validation_popover('#ACTIVITY', 'ACTIVITY Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_START || param.TIME_START == "" || param.TIME_START == "1/1/1970 07.00.00"){
					$('#TIME_START').parent().addClass('has-error');
					add_validation_popover('#TIME_START', 'TIME START Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_END || param.TIME_END== "" || param.TIME_END == "1/1/1970 07.00.00"){
					$('#TIME_END').parent().addClass('has-error');
					add_validation_popover('#TIME_END', 'TIME END Harus diisi');
					
					is_error = true;
				}
                if(!param.REALISASI_BONGKAR || param.REALISASI_BONGKAR == "" ){
					$('#REALISASI_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#REALISASI_BONGKAR', 'REALISASI BONGKAR Harus diisi');
					
					is_error = true;
				}
                if(!param.REALISASI_MUAT || param.REALISASI_MUAT == "" ){
					$('#REALISASI_MUAT').parent().addClass('has-error');
					add_validation_popover('#REALISASI_MUAT', 'REALISASI MUAT Harus diisi');
					
					is_error = true;
				}
                if(!param.COMPLETE || param.COMPLETE == "" || param.COMPLETE == "1/1/1970 07.00.00"){
					$('#COMPLETE').parent().addClass('has-error');
					add_validation_popover('#COMPLETE', 'COMPLETE Harus diisi');
					
					is_error = true;
				}
                if(!param.ATD || param.ATD == "" || param.ATD == "1/1/1970 07.00.00"){
					$('#ATD').parent().addClass('has-error');
					add_validation_popover('#ATD', 'ATD Harus diisi');
					
					is_error = true;
				}
        }
        if(param.ACTIVITY == 'COMMENCE OPERATION' || param.ACTIVITY == 'BREAK' || param.ACTIVITY == 'RESUME OPERATION'){
            
                if(!param.ATA || param.ATA == "" || param.ATA == "1/1/1970 07.00.00"){
					$('#ATA').parent().addClass('has-error');
					add_validation_popover('#ATA', 'ATA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ATB || param.ATB == "" || param.ATB == "1/1/1970 07.00.00"){
					$('#ATB').parent().addClass('has-error');
					add_validation_popover('#ATB', 'ATB Harus diisi');
					
					is_error = true;
				}
   
                if(!param.COMMENCE || param.COMMENCE == "" || param.COMMENCE == "1/1/1970 07.00.00"){
					$('#COMMENCE').parent().addClass('has-error');
					add_validation_popover('#COMMENCE', 'COMMENCE Harus diisi');
					
					is_error = true;
				}
             
                if(!param.ACTIVITY || param.ACTIVITY == ""){
					$('#ACTIVITY').parent().addClass('has-error');
					add_validation_popover('#ACTIVITY', 'ACTIVITY Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_START || param.TIME_START == "" || param.TIME_START == "1/1/1970 07.00.00"){
					$('#TIME_START').parent().addClass('has-error');
					add_validation_popover('#TIME_START', 'TIME START Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_END || param.TIME_END== "" || param.TIME_END == "1/1/1970 07.00.00"){
					$('#TIME_END').parent().addClass('has-error');
					add_validation_popover('#TIME_END', 'TIME END Harus diisi');
					
					is_error = true;
				}
                if(!param.REALISASI_BONGKAR || param.REALISASI_BONGKAR == "" ){
					$('#REALISASI_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#REALISASI_BONGKAR', 'REALISASI BONGKAR Harus diisi');
					
					is_error = true;
				}
                if(!param.REALISASI_MUAT || param.REALISASI_MUAT == "" ){
					$('#REALISASI_MUAT').parent().addClass('has-error');
					add_validation_popover('#REALISASI_MUAT', 'REALISASI MUAT Harus diisi');
					
					is_error = true;
				}
        }
           if(param.ACTIVITY == 'COMPLETE OPERATION' || param.ACTIVITY == 'CLOSE RAMPDOOR' ||  param.ACTIVITY == 'WAITING CLEARANCE DOCS'){
                if(!param.ATA || param.ATA == "" || param.ATA == "1/1/1970 07.00.00"){
					$('#ATA').parent().addClass('has-error');
					add_validation_popover('#ATA', 'ATA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ATB || param.ATB == "" || param.ATB == "1/1/1970 07.00.00"){
					$('#ATB').parent().addClass('has-error');
					add_validation_popover('#ATB', 'ATB Harus diisi');
					
					is_error = true;
				}
   
                if(!param.COMMENCE || param.COMMENCE == "" || param.COMMENCE == "1/1/1970 07.00.00"){
					$('#COMMENCE').parent().addClass('has-error');
					add_validation_popover('#COMMENCE', 'COMMENCE Harus diisi');
					
					is_error = true;
				}
             
                if(!param.ACTIVITY || param.ACTIVITY == ""){
					$('#ACTIVITY').parent().addClass('has-error');
					add_validation_popover('#ACTIVITY', 'ACTIVITY Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_START || param.TIME_START == "" || param.TIME_START == "1/1/1970 07.00.00"){
					$('#TIME_START').parent().addClass('has-error');
					add_validation_popover('#TIME_START', 'TIME START Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_END || param.TIME_END== "" || param.TIME_END == "1/1/1970 07.00.00"){
					$('#TIME_END').parent().addClass('has-error');
					add_validation_popover('#TIME_END', 'TIME END Harus diisi');
					
					is_error = true;
				}
                if(!param.REALISASI_BONGKAR || param.REALISASI_BONGKAR == "" ){
					$('#REALISASI_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#REALISASI_BONGKAR', 'REALISASI BONGKAR Harus diisi');
					
					is_error = true;
				}
                if(!param.REALISASI_MUAT || param.REALISASI_MUAT == "" ){
					$('#REALISASI_MUAT').parent().addClass('has-error');
					add_validation_popover('#REALISASI_MUAT', 'REALISASI MUAT Harus diisi');
					
					is_error = true;
				}
                if(!param.COMPLETE || param.COMPLETE == "" || param.COMPLETE == "1/1/1970 07.00.00"){
					$('#COMPLETE').parent().addClass('has-error');
					add_validation_popover('#COMPLETE', 'COMPLETE Harus diisi');
					
					is_error = true;
				}
        }

                    console.log('err', is_error);
				if(is_error){
					sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
				}else{		
					$('#simpan_load').show();
				
					var urls = bs.siteURL + 'FormIntr/update/' + bs.token;
                    console.log('url',urls);                   
					$.post(urls, param, function(data){
                        $.notify("Data berhasil ditambahkan", "success");    
                        window.location.href = bs.siteURL + 'tps_online/form_intr/listview';   
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
					});
				}
				
				return false;
                
			});
			
			function reset_form(){
				$('#NAMA_KAPAL, #KADE_DERMAGA, #PBM, #VOYAGE, #COMMENCE, #COMPLETED').val('');
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