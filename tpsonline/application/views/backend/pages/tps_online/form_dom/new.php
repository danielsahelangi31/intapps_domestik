<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" />
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
            <h1 style="margin-bottom:25px;margin-left:-25px">MONITORING BONGKAR/MUAT KAPAL</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                           
         
						<div class="col-lg-12 col-md-12 col-sm-12 col-12">
                            <div class="form-group"style="margin-right:0px">                         
                            <input type="hidden" class="form-control" id="ID_VVD" name="ID_VVD"  value="<?php echo $vesel->ID_VVD; ?>"/>
                            </div> 
                         </div> 

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-right:0px">
                            <label>Nama KAPAL : <b class="text-danger">*</b></label>
                            <input type="text" class="form-control" readonly id="NAMA_KAPAL" name="NAMA_KAPAL" value="<?php echo $vesel->VESSEL_NAME;?>"/>

               
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label>Kade/Dermaga:<b class="text-danger">*</b></label>
                            <input type="text" class="form-control" readonly id="KADE_DERMAGA" name="KADE_DERMAGA" value="<?php echo $vesel->KADE_NAME; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">Voyage<b class="text-danger">*</b></label>
                             <input type="text" class="form-control" readonly id="VOYAGE" name="VOYAGE" value="<?php echo $vesel->VOY_IN; ?>"/>
                     
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">PBM<b class="text-danger">*</b></label>
                            <input type="text" class="form-control" readonly id="PBM" name="PBM" value="<?php echo $vesel->PBM; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">RENCANA BONGKAR<b class="text-danger">*</b></label>
								<input type="number" <?php echo empty( $datasource[0]->RENCANA_BONGKAR) ? '' : 'readonly' ?> class="form-control right" id="RENCANA_BONGKAR" name="RENCANA_BONGKAR" value="<?php echo $datasource[0]->RENCANA_BONGKAR; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">RENCANA MUAT<b class="text-danger">*</b></label>
                           		<input type="number" <?php echo empty( $datasource[0]->RENCANA_MUAT) ? '' : 'readonly' ?> class="form-control right" id="RENCANA_MUAT" name="RENCANA_MUAT" value="<?php echo $datasource[0]->RENCANA_MUAT; ?>"/>
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
                            <input type="datetime-local" class="form-control right" id="ATA" name="ATA" value="<?php echo $ATAH; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ETB<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control right" readonly id="ETB" name="ETB" value="<?php echo $ETB; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ATB<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control right" id="ATB" name="ATB" value="<?php echo $ATBH; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ETD<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control" id="ETD" readonly name="ETD" value="<?php echo $ETD; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label class="col-form-label">ATD<b class="text-danger">*</b></label>
                            <input  type="datetime-local" class="form-control" id="ATD" name="ATD" value="<?php echo $ATDH; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group"style="margin-right:0px">
                            <label >Commence:</label>
                            <input type="datetime-local" class="form-control" id="COMMENCE" name="COMMENCE" value="<?php echo $COMMENCEH; ?>"/>
                        </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group"style="margin-right:0px">
                            <label>Completed:</label>
                            <input type="datetime-local" class="form-control" id="COMPLETE" name="COMPLETE" value="<?php echo $COMPLETEH; ?>"/>
                            </div> 
                         </div> 
			

                        <hr>
                      
                         <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">                     
							<label >Shift</label>					
								<select class="form-control" id="SHIFT" name="SHIFT">
									<option value="">-- Pilih --</option>
									<?php
                                    $TYPE_SHIFT = $view['TYPE_SHIFT'];
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
									<option value="0">-- Pilih --</option>
									<?php
                                    $TYPE_ACTIVITY = $view['TYPE_ACTIVITY'];
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
                            <input type="datetime-local" class="form-control" id="TIME_START" name="TIME_START"/>                            
                        </div>
                        <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">  
                            <label class="col-form-label">Time End<b class="text-danger">*</b></label>
                            <input type="datetime-local" class="form-control" id="TIME_END" name="TIME_END"/>                            
                        </div>
                        
                        <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">                           
                            <label>REALISASI BONGKAR: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="REALISASI_BONGKAR"  name="REALISASI_BONGKAR">                            
                        </div>	                        
                        
                        <div class="form-group" style="margin-left:0px;width:48.5%;margin: auto;display: block;">  
                            <label>REALISASI MUAT: <b class="text-danger">*</b></label>
                            <input type="number" class="form-control" id="REALISASI_MUAT"  name="REALISASI_MUAT">                            
                        </div>                        
                        
                        <hr/>
                        <div class="btn" id="simpan_load" style="display:auto;margin-left:23%"></div>                    
							<a href=""  class="btn btn-primary" style="" id="simpan">Simpan</a>
                            <a href="<?php echo site_url('tps_online/form_dom/listview') ?>" class="btn btn-default">Kembali</a>
                     
                                </form>
                            </div>
                        </div>
                    </div>
			</div>
		
			
			<?php echo form_close() ?>
		
			<div id="kampret_loader"></div>

		</div>
		
	</div>

    <div id="visitID" class="modal fade" role="dialog">
    <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">List Vessel Name</h4>
      </div>
      <div class="modal-body">
        
       		<table class="table" id="visitTable">
            	<thead>
                	<tr>
                    	<th>VESSEL_NAME</th>
                        <th>VOYAGE</th>
                        <th>PBM</th>
                        <th>ETA</th>				
                    </tr>
                </thead>
                <tbody>
                	<?php 
                        $datasource = $view['datasource'];
						foreach($datasource as $dt){
                         
                            ?>
                     
                    <tr>
                    	<td>
						<a href="javascript::void(0);" onClick="setVisitID('<?php echo $dt->VESSEL_NAME;?>','<?php echo $dt->VOY_IN;?>')" >
						<?php echo $dt->VESSEL_NAME;?>
                        </a>
                        </td>   
                        <td><?php echo $dt->VOY_IN;?></td>   
                        <td><?php echo $dt->PBM;?></td>   
                        <td><?php echo $dt->ETA;?></td>  
				
                    </tr>
                     <?php }?>
                </tbody>
            </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
	</div>
    </div>
  </div>
</div>
    <?php $this->load->view('backend/elements/footer') ?>
	
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/notify.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
    function showModal(){
        $("#visitID").modal("toggle");
    }
    function setVisitID(id, voyage){
        document.location="<?php echo site_url('tps_online/form_dom/neww');?>"+"?id="+id+"&voyage="+voyage+"";
    }
	
    
    </script>

    <script type="text/javascript">
	$(document).ready(function(){
        $("#visitTable").dataTable();
		$('#android_ready, #html5_ready').tooltip().show();

      
        $('#simpan').click(function(){
				
		
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

                var timend = $('#TIME_END').val();  
                var mydatex = new Date(timend);
                var TIME_END = mydatex.toLocaleString("id-ID")

                var timestart = $('#TIME_START').val();  
                var mydatest = new Date(timestart);
                var TIME_START = mydatest.toLocaleString("id-ID");

                var now = new Date(Date.now()).toLocaleString("id-ID");
                console.log('now',now);
            
				var param = {                                  
					'ID_VVD' : $('#ID_VVD').val(),
                    'TANGGAL_TIME' : now,
					'NAMA_KAPAL' : $('#NAMA_KAPAL').val(),
					'KADE_DERMAGA' : $('#KADE_DERMAGA').val(),
                    'VOYAGE' : $('#VOYAGE').val(),
                    'PBM' : $('#PBM').val(),
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
								
        if(!param.ACTIVITY || param.ACTIVITY == ""){				
				if(!param.NAMA_KAPAL || param.NAMA_KAPAL == ""){
					$('#NAMA_KAPAL').parent().addClass('has-error');
					add_validation_popover('#NAMA_KAPAL', 'Nama Kapal Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.VOYAGE || param.VOYAGE == ""){
					$('#VOYAGE').parent().addClass('has-error');
					add_validation_popover('#VOYAGE', 'Voyage Harus diisi');
					
					is_error = true;
				}

                if(!param.KADE_DERMAGA || param.KADE_DERMAGA == ""){
					$('#KADE_DERMAGA').parent().addClass('has-error');
					add_validation_popover('#KADE_DERMAGA', 'KADE DERMAGA Harus diisi');
					
					is_error = true;
				}

                if(!param.PBM || param.PBM == ""){
					$('#PBM').parent().addClass('has-error');
					add_validation_popover('#PBM', 'PBM Harus diisi');
					
					is_error = true;
				}
				
                if(!param.RENCANA_BONGKAR || param.RENCANA_BONGKAR == ""){
					$('#RENCANA_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#RENCANA_BONGKAR', 'RENCANA BONGKAR Harus diisi');
					
					is_error = true;
				}

                if(!param.RENCANA_MUAT || param.RENCANA_MUAT == ""){
					$('#RENCANA_MUAT').parent().addClass('has-error');
					add_validation_popover('#RENCANA_MUAT', 'RENCANA MUAT Harus diisi');
					
					is_error = true;
				}

                if(!param.ETA || param.ETA == "" || param.ETA == "Invalid Date"){
					$('#ETA').parent().addClass('has-error');
					add_validation_popover('#ETA', 'ETA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ETB || param.ETB == "" || param.ETB == "Invalid Date"){
					$('#ETB').parent().addClass('has-error');
					add_validation_popover('#ETB', 'ETB Harus diisi');
					
					is_error = true;
				}
                if(!param.ETD || param.ETD == "" || param.ETD == "Invalid Date"){
					$('#ETD').parent().addClass('has-error');
					add_validation_popover('#ETD', 'ETD Harus diisi');
					
					is_error = true;
				}
     }
    
    if(!param.ACTIVITY || param.ACTIVITY == "OPEN RAMPDOOR"){				
				if(!param.NAMA_KAPAL || param.NAMA_KAPAL == ""){
					$('#NAMA_KAPAL').parent().addClass('has-error');
					add_validation_popover('#NAMA_KAPAL', 'Nama Kapal Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.VOYAGE || param.VOYAGE == ""){
					$('#VOYAGE').parent().addClass('has-error');
					add_validation_popover('#VOYAGE', 'Voyage Harus diisi');
					
					is_error = true;
				}

                if(!param.KADE_DERMAGA || param.KADE_DERMAGA == ""){
					$('#KADE_DERMAGA').parent().addClass('has-error');
					add_validation_popover('#KADE_DERMAGA', 'KADE DERMAGA Harus diisi');
					
					is_error = true;
				}

                if(!param.PBM || param.PBM == ""){
					$('#PBM').parent().addClass('has-error');
					add_validation_popover('#PBM', 'PBM Harus diisi');
					
					is_error = true;
				}
				
                if(!param.RENCANA_BONGKAR || param.RENCANA_BONGKAR == ""){
					$('#RENCANA_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#RENCANA_BONGKAR', 'RENCANA BONGKAR Harus diisi');
					
					is_error = true;
				}

                if(!param.RENCANA_MUAT || param.RENCANA_MUAT == ""){
					$('#RENCANA_MUAT').parent().addClass('has-error');
					add_validation_popover('#RENCANA_MUAT', 'RENCANA MUAT Harus diisi');
					
					is_error = true;
				}

                if(!param.ETA || param.ETA == "" || param.ETA == "Invalid Date"){
					$('#ETA').parent().addClass('has-error');
					add_validation_popover('#ETA', 'ETA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ETB || param.ETB == "" || param.ETB == "Invalid Date"){
					$('#ETB').parent().addClass('has-error');
					add_validation_popover('#ETB', 'ETB Harus diisi');
					
					is_error = true;
				}
                if(!param.ETD || param.ETD == "" || param.ETD == "Invalid Date"){
					$('#ETD').parent().addClass('has-error');
					add_validation_popover('#ETD', 'ETD Harus diisi');
					
					is_error = true;
				}

                if(!param.ATA || param.ATA == "" || param.ATA == "Invalid Date"){
					$('#ATA').parent().addClass('has-error');
					add_validation_popover('#ATA', 'ATA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ATB || param.ATB == "" || param.ATB == "Invalid Date"){
					$('#ATB').parent().addClass('has-error');
					add_validation_popover('#ATB', 'ATB Harus diisi');
					
					is_error = true;
				}
    
                if(!param.ACTIVITY || param.ACTIVITY == ""){
					$('#ACTIVITY').parent().addClass('has-error');
					add_validation_popover('#ACTIVITY', 'ACTIVITY Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_START || param.TIME_START == "" || param.TIME_START == "Invalid Date"){
					$('#TIME_START').parent().addClass('has-error');
					add_validation_popover('#TIME_START', 'TIME START Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_END || param.TIME_END== "" || param.TIME_END == "Invalid Date"){
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
            if(!param.NAMA_KAPAL || param.NAMA_KAPAL == ""){
					$('#NAMA_KAPAL').parent().addClass('has-error');
					add_validation_popover('#NAMA_KAPAL', 'Nama Kapal Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.VOYAGE || param.VOYAGE == ""){
					$('#VOYAGE').parent().addClass('has-error');
					add_validation_popover('#VOYAGE', 'Voyage Harus diisi');
					
					is_error = true;
				}

                if(!param.KADE_DERMAGA || param.KADE_DERMAGA == ""){
					$('#KADE_DERMAGA').parent().addClass('has-error');
					add_validation_popover('#KADE_DERMAGA', 'KADE DERMAGA Harus diisi');
					
					is_error = true;
				}

                if(!param.PBM || param.PBM == ""){
					$('#PBM').parent().addClass('has-error');
					add_validation_popover('#PBM', 'PBM Harus diisi');
					
					is_error = true;
				}
				
                if(!param.RENCANA_BONGKAR || param.RENCANA_BONGKAR == ""){
					$('#RENCANA_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#RENCANA_BONGKAR', 'RENCANA BONGKAR Harus diisi');
					
					is_error = true;
				}

                if(!param.RENCANA_MUAT || param.RENCANA_MUAT == ""){
					$('#RENCANA_MUAT').parent().addClass('has-error');
					add_validation_popover('#RENCANA_MUAT', 'RENCANA MUAT Harus diisi');
					
					is_error = true;
				}

                if(!param.ETA || param.ETA == "" || param.ETA == "Invalid Date"){
					$('#ETA').parent().addClass('has-error');
					add_validation_popover('#ETA', 'ETA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ETB || param.ETB == "" || param.ETB == "Invalid Date"){
					$('#ETB').parent().addClass('has-error');
					add_validation_popover('#ETB', 'ETB Harus diisi');
					
					is_error = true;
				}
                if(!param.ETD || param.ETD == "" || param.ETD == "Invalid Date"){
					$('#ETD').parent().addClass('has-error');
					add_validation_popover('#ETD', 'ETD Harus diisi');
					
					is_error = true;
				}

                if(!param.ATA || param.ATA == "" || param.ATA == "Invalid Date"){
					$('#ATA').parent().addClass('has-error');
					add_validation_popover('#ATA', 'ATA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ATB || param.ATB == "" || param.ATB == "Invalid Date"){
					$('#ATB').parent().addClass('has-error');
					add_validation_popover('#ATB', 'ATB Harus diisi');
					
					is_error = true;
				}
   
                if(!param.COMMENCE || param.COMMENCE == "" || param.COMMENCE == "Invalid Date"){
					$('#COMMENCE').parent().addClass('has-error');
					add_validation_popover('#COMMENCE', 'COMMENCE Harus diisi');
					
					is_error = true;
				}
       
                if(!param.ACTIVITY || param.ACTIVITY == ""){
					$('#ACTIVITY').parent().addClass('has-error');
					add_validation_popover('#ACTIVITY', 'ACTIVITY Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_START || param.TIME_START == "" || param.TIME_START == "Invalid Date"){
					$('#TIME_START').parent().addClass('has-error');
					add_validation_popover('#TIME_START', 'TIME START Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_END || param.TIME_END== "" || param.TIME_END == "Invalid Date"){
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
                if(!param.COMPLETE || param.COMPLETE == "" || param.COMPLETE == "Invalid Date"){
					$('#COMPLETE').parent().addClass('has-error');
					add_validation_popover('#COMPLETE', 'COMPLETE Harus diisi');
					
					is_error = true;
				}
                if(!param.ATD || param.ATD == "" || param.ATD == "Invalid Date"){
					$('#ATD').parent().addClass('has-error');
					add_validation_popover('#ATD', 'ATD Harus diisi');
					
					is_error = true;
				}
        }
         if(param.ACTIVITY == 'COMMENCE OPERATION' || param.ACTIVITY == 'BREAK' || param.ACTIVITY == 'RESUME OPERATION'){
                    if(!param.NAMA_KAPAL || param.NAMA_KAPAL == ""){
					$('#NAMA_KAPAL').parent().addClass('has-error');
					add_validation_popover('#NAMA_KAPAL', 'Nama Kapal Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.VOYAGE || param.VOYAGE == ""){
					$('#VOYAGE').parent().addClass('has-error');
					add_validation_popover('#VOYAGE', 'Voyage Harus diisi');
					
					is_error = true;
				}

                if(!param.KADE_DERMAGA || param.KADE_DERMAGA == ""){
					$('#KADE_DERMAGA').parent().addClass('has-error');
					add_validation_popover('#KADE_DERMAGA', 'KADE DERMAGA Harus diisi');
					
					is_error = true;
				}

                if(!param.PBM || param.PBM == ""){
					$('#PBM').parent().addClass('has-error');
					add_validation_popover('#PBM', 'PBM Harus diisi');
					
					is_error = true;
				}
				
                if(!param.RENCANA_BONGKAR || param.RENCANA_BONGKAR == ""){
					$('#RENCANA_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#RENCANA_BONGKAR', 'RENCANA BONGKAR Harus diisi');
					
					is_error = true;
				}

                if(!param.RENCANA_MUAT || param.RENCANA_MUAT == ""){
					$('#RENCANA_MUAT').parent().addClass('has-error');
					add_validation_popover('#RENCANA_MUAT', 'RENCANA MUAT Harus diisi');
					
					is_error = true;
				}

                if(!param.ETA || param.ETA == "" || param.ETA == "Invalid Date"){
					$('#ETA').parent().addClass('has-error');
					add_validation_popover('#ETA', 'ETA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ETB || param.ETB == "" || param.ETB == "Invalid Date"){
					$('#ETB').parent().addClass('has-error');
					add_validation_popover('#ETB', 'ETB Harus diisi');
					
					is_error = true;
				}
                if(!param.ETD || param.ETD == "" || param.ETD == "Invalid Date"){
					$('#ETD').parent().addClass('has-error');
					add_validation_popover('#ETD', 'ETD Harus diisi');
					
					is_error = true;
				}

                if(!param.ATA || param.ATA == "" || param.ATA == "Invalid Date"){
					$('#ATA').parent().addClass('has-error');
					add_validation_popover('#ATA', 'ATA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ATB || param.ATB == "" || param.ATB == "Invalid Date"){
					$('#ATB').parent().addClass('has-error');
					add_validation_popover('#ATB', 'ATB Harus diisi');
					
					is_error = true;
				}
   
                if(!param.COMMENCE || param.COMMENCE == "" || param.COMMENCE == "Invalid Date"){
					$('#COMMENCE').parent().addClass('has-error');
					add_validation_popover('#COMMENCE', 'COMMENCE Harus diisi');
					
					is_error = true;
				}

                if(!param.ACTIVITY || param.ACTIVITY == ""){
					$('#ACTIVITY').parent().addClass('has-error');
					add_validation_popover('#ACTIVITY', 'ACTIVITY Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_START || param.TIME_START == "" || param.TIME_START == "Invalid Date"){
					$('#TIME_START').parent().addClass('has-error');
					add_validation_popover('#TIME_START', 'TIME START Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_END || param.TIME_END== "" || param.TIME_END == "Invalid Date"){
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
			
            if(!param.NAMA_KAPAL || param.NAMA_KAPAL == ""){
					$('#NAMA_KAPAL').parent().addClass('has-error');
					add_validation_popover('#NAMA_KAPAL', 'Nama Kapal Harus dipilih');
					
					is_error = true;
				}
				
				if(!param.VOYAGE || param.VOYAGE == ""){
					$('#VOYAGE').parent().addClass('has-error');
					add_validation_popover('#VOYAGE', 'Voyage Harus diisi');
					
					is_error = true;
				}

                if(!param.KADE_DERMAGA || param.KADE_DERMAGA == ""){
					$('#KADE_DERMAGA').parent().addClass('has-error');
					add_validation_popover('#KADE_DERMAGA', 'KADE DERMAGA Harus diisi');
					
					is_error = true;
				}

                if(!param.PBM || param.PBM == ""){
					$('#PBM').parent().addClass('has-error');
					add_validation_popover('#PBM', 'PBM Harus diisi');
					
					is_error = true;
				}
				
                if(!param.RENCANA_BONGKAR || param.RENCANA_BONGKAR == ""){
					$('#RENCANA_BONGKAR').parent().addClass('has-error');
					add_validation_popover('#RENCANA_BONGKAR', 'RENCANA BONGKAR Harus diisi');
					
					is_error = true;
				}

                if(!param.RENCANA_MUAT || param.RENCANA_MUAT == ""){
					$('#RENCANA_MUAT').parent().addClass('has-error');
					add_validation_popover('#RENCANA_MUAT', 'RENCANA MUAT Harus diisi');
					
					is_error = true;
				}

                if(!param.ETA || param.ETA == "" || param.ETA == "Invalid Date"){
					$('#ETA').parent().addClass('has-error');
					add_validation_popover('#ETA', 'ETA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ETB || param.ETB == "" || param.ETB == "Invalid Date"){
					$('#ETB').parent().addClass('has-error');
					add_validation_popover('#ETB', 'ETB Harus diisi');
					
					is_error = true;
				}
                if(!param.ETD || param.ETD == "" || param.ETD == "Invalid Date"){
					$('#ETD').parent().addClass('has-error');
					add_validation_popover('#ETD', 'ETD Harus diisi');
					
					is_error = true;
				}

                if(!param.ATA || param.ATA == "" || param.ATA == "Invalid Date"){
					$('#ATA').parent().addClass('has-error');
					add_validation_popover('#ATA', 'ATA Harus diisi');
					
					is_error = true;
				}
                
                if(!param.ATB || param.ATB == "" || param.ATB == "Invalid Date"){
					$('#ATB').parent().addClass('has-error');
					add_validation_popover('#ATB', 'ATB Harus diisi');
					
					is_error = true;
				}
   
                if(!param.COMMENCE || param.COMMENCE == "" || param.COMMENCE == "Invalid Date"){
					$('#COMMENCE').parent().addClass('has-error');
					add_validation_popover('#COMMENCE', 'COMMENCE Harus diisi');
					
					is_error = true;
				}

                if(!param.ACTIVITY || param.ACTIVITY == ""){
					$('#ACTIVITY').parent().addClass('has-error');
					add_validation_popover('#ACTIVITY', 'ACTIVITY Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_START || param.TIME_START == "" || param.TIME_START == "Invalid Date"){
					$('#TIME_START').parent().addClass('has-error');
					add_validation_popover('#TIME_START', 'TIME START Harus diisi');
					
					is_error = true;
				}
                if(!param.TIME_END || param.TIME_END== "" || param.TIME_END == "Invalid Date"){
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
                if(!param.COMPLETE || param.COMPLETE == "" || param.COMPLETE == "Invalid Date"){
					$('#COMPLETE').parent().addClass('has-error');
					add_validation_popover('#COMPLETE', 'COMPLETE Harus diisi');
					
					is_error = true;
				}
        }
				console.log('err', is_error)
				if(is_error){
					sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
				}else{				
					var urls = bs.siteURL + 'FormDetail/simpan/' + bs.token;
                    console.log('url', urls)
        
					$.post(urls, param, function(data){                      
                        console.log('list_view1', data);					
                        $.notify("Data berhasil ditambahkan", "success");
                       
						$(' #TIME_START, #TIME_END, #REALISASI_BONGKAR, #REALISASI_MUAT').val('');
						$('#SHIFT, #ACTIVITY').val('-- Pilih --');
						if(data){
                            console.log('list_view2',);
                    
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
				console.log('reset');
				$('#NAMA_KAPAL, #KADE_DERMAGA, #PBM, #VOYAGE, #RENCANA_BONGKAR, #RENCANA_MUAT, #ETA, #ATA, #ETB, #ATB, #ETD, #ATD, #COMMENCE, #COMPLETE, #SHIFT, #ACTIVITY, #TIME_START, #TIME_END, #REALISASI_BONGKAR, #REALISASI_MUAT').val('');
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
	
        $('#save').click(function(){
				
				var is_error = false;
				
				var param = {        
                    'ID_MONITORING_DETAIL' : $('#ID_MONITORING_DETAIL').val(),                   
					'ID_MONITORING_HEADER' : $('#ID_MONITORING_HEADER').val(),
					'TANGGAL_TIME' : $('#TANGGAL_TIME').val(),			
                    'SHIFT' : $('#SHIFT').val(),
                    'ACTIVITY' : $('#ACTIVITY').val(),
                    'TIMEND' : $('#TIMEND').val(),
                    'REALISASI_BONGKAR' : $('#BONGKAR').val(),
                    'REALISASI_MUAT' : $('#MUAT').val(),       
                    'REALISASI_BONGKAR_REPORT' : $('#BONGKAR_REPORT').val(),
                    'REALISASI_MUAT_REPORT' : $('#MUAT_REPORT').val(),
                    'REMAINING_BONGKAR' : $('#REMAINING_BONGKAR').val(),
                    'REMAINING_MUAT' : $('#REMAINING_MUAT').val()                      
                }		
				
				
				console.log(param);
				
				
				if(is_error){
					sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
				}else{		
					$('#simpan_load').show();
				
					var urls = bs.siteURL + 'FormDetail/simpann/' + bs.token;
                    console.log('url',urls)
                    
					$.post(urls, param, function(data){				
                        console.log('scss', data)
                        console.log('prm', param)
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
				$('#VESSEL_NAME, #KADE').val('');
			}
       
	});
	</script>
</body>
</html>