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
			
            <h1 style="margin-bottom:25px;margin-left:-25px">BERTHING PLAN</h1>
			
			<?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
	
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                           
      
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                            <div class="form-group" style="margin-right:0px">
                            <label>Nama KAPAL : <b class="text-danger">*</b></label>
                            <input type="text" class="form-control" id="VESSEL_NAME" name="VESSEL_NAME" autocomplete='off' value="<?php echo $VISIT_NAME;?>" onClick="showModal();"/>

               
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                            <label>Kode Kapal:<b class="text-danger">*</b></label>
                            <input type="text" class="form-control" readonly id="VESSEL_CODE" name="VESSEL_CODE" value="<?= $view['datafield']->VESSEL_CODE; ?>"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">Voyage IN:<b class="text-danger">*</b></label>
                             <input type="text" class="form-control" readonly id="VOYAGE_IN" name="VOYAGE_IN" value="<?= $view['datafield']->VOYAGE_IN; ?>"/>
                     
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
							<div class="form-group" style="margin-right:0px">
                                <label class="col-form-label">Voyage OUT:<b class="text-danger">*</b></label>
                            <input type="text" class="form-control" readonly id="VOYAGE_OUT" name="VOYAGE_OUT" value="<?= $view['datafield']->VOYAGE_OUT; ?>"/>
                            </div>
                        </div>
                        <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                                <label>Nama Kade<b class="text-danger">*</b></label>
                                <select class="form-control" name="KADE_NAME" id="KADE_NAME">
                                    <option value="">-- PILIH --</option>
                                    <option value="TPT 1">TPT 1</option>
                                    <option value="TPT 2">TPT 2</option>
                                    <option value="TPT 3">TPT 3</option>  
                                    <option value="TPT 4">TPT 4</option>    
                                    <option value="TPT 5A">TPT 5A</option> 
                                    <option value="TPT 5B">TPT 5B</option>
                                    <option value="EX PRESIDEN">EX PRESIDEN</option>   
                                </select> 
							
                            </div>                       
                        </div>   
                   
							<div class="form-group" style="margin-left:0px;width:48.5%;"> 
                                <label class="col-form-label">Kade Meter Awal:<b class="text-danger">*</b></label>
                                <input type="number" class="form-control right" id="KADE_AWAL" name="KADE_AWAL"/>
                            </div>
                     
							<div class="form-group" style="margin-left:0px;width:48.5%;"> 
                                <label class="col-form-label">Kade Meter Akhir:<b class="text-danger">*</b></label>
                                <input type="number" class="form-control" id="KADE_AKHIR" name="KADE_AKHIR"/>
                            </div>

                      
                            <!-- <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label>Ex Kapal</label>
                                <select class="form-control" id="EX_KAPAL" name="EX_KAPAL">
                                    <option value="">-- PILIH --</option>
                                    <?php
                                    foreach ($databerth as $make){
                                        ?>
                                        <option value="<?php echo $make->VISIT_NAME; ?>" ><?php echo $make->VISIT_NAME; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php echo form_error('directionType', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>	
                              </div>
                            </div>                        -->
                            
                         <hr/>
                        <div class="btn" id="simpan_load" style="display:auto;margin-left:0%"></div>                
							<a href=""  class="btn btn-primary" style="" id="simpan_berth">Simpan</a>
                            <a href="<?php echo site_url('tps_online/berthing_time/listview') ?>" class="btn btn-default">Kembali</a>
                    
                                </form>
                            </div>
                        </div>
                    </div>
			</div>
		
			
			<?php echo form_close() ?>
		
			<div id="kampret_loader"></div>

		</div><!-- /.container -->
		
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
                    	<th>VISIT NAME</th>
                        <th>VESSEL CODE</th>
                        <th>VOYAGE IN</th>                     
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
						<a href="javascript::void(0);" onClick="setVisitID('<?php echo $dt->VISIT_NAME;?>','<?php echo $dt->VOYAGE_IN;?>')" >
						<?php echo $dt->VISIT_NAME;?>
                        </a>
                        </td>   
                        <td><?php echo $dt->VESSEL_CODE;?></td>   
                        <td><?php echo $dt->VOYAGE_IN;?></td>                    
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
        document.location="<?php echo site_url('tps_online/berthing_time/neww');?>"+"?id="+id+"&voyage="+voyage+"";
    }
	
    
    </script>

    <script type="text/javascript">
	$(document).ready(function(){
        // $('#EX_KAPAL').select2();
        $("#visitTable").dataTable();
		$('#android_ready, #html5_ready').tooltip().show();

        visit_id = "<?= $view['datafield']->VISIT_ID; ?>";
        console.log('visit_id', visit_id);
        $('#simpan_berth').click(function(){
				
				
				var param = {
                    'VISIT_ID' : visit_id,
                    'VESSEL_NAME' : $('#VESSEL_NAME').val(),
                    'VESSEL_CODE' : $('#VESSEL_CODE').val(),
                    'VOYAGE_IN'   : $('#VOYAGE_IN').val(),
                    'VOYAGE_OUT' : $('#VOYAGE_OUT').val(),
                    'KADE_NAME' : $('#KADE_NAME').val(),
					'KADE_AWAL' : $('#KADE_AWAL').val(),
                    'KADE_AKHIR' : $('#KADE_AKHIR').val(),
                            
                }		
				
				
				console.log(param);
                is_error = false;
                if(!param.VESSEL_NAME || param.VESSEL_NAME == ""){
					$('#VESSEL_NAME').parent().addClass('has-error');
					add_validation_popover('#VESSEL_NAME', 'Harus diisi');
					
					is_error = true;
				}

                if(!param.VESSEL_CODE || param.VESSEL_CODE == ""){
					$('#VESSEL_CODE').parent().addClass('has-error');
					add_validation_popover('#VESSEL_CODE', 'Harus diisi');
					
					is_error = true;
				}

                if(!param.VOYAGE_IN || param.VOYAGE_IN == ""){
					$('#VOYAGE_IN ').parent().addClass('has-error');
					add_validation_popover('#VOYAGE_IN', 'Harus diisi');
					
					is_error = true;
				}   
                if(!param.VOYAGE_OUT || param.VOYAGE_OUT == ""){
					$('#VOYAGE_OUT').parent().addClass('has-error');
					add_validation_popover('#VOYAGE_OUT', 'Harus diisi');
					
					is_error = true;
				} 			
                if(!param.KADE_NAME || param.KADE_NAME == ""){
					$('#KADE_NAME').parent().addClass('has-error');
					add_validation_popover('#KADE_NAME', 'Harus diisi');
					
					is_error = true;
				}

                if(!param.KADE_AWAL || param.KADE_AWAL == ""){
					$('#KADE_AWAL').parent().addClass('has-error');
					add_validation_popover('#KADE_AWAL', 'Harus diisi');
					
					is_error = true;
				}
              

                if(!param.KADE_AKHIR || param.KADE_AKHIR == ""){
					$('#KADE_AKHIR').parent().addClass('has-error');
					add_validation_popover('#KADE_AKHIR', 'Harus diisi');
					
					is_error = true;
				}              

				console.log('err', is_error)
				if(is_error){
                    $.notify("Harap perbaiki field yang ditandai","error");  
					//sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
				}else{		
					// $('#simpan_load').show();
                    $.notify("Data berhasil ditambahkan", "success");
                    window.location.href = bs.siteURL + 'tps_online/berthing_time/list_view'; 
					var urls = bs.siteURL + 'FormBerth/save/' + bs.token;
                    console.log('url', urls)
          
					$.post(urls, param, function(data){                      
                        console.log('list_view1')
                      
                       
                        // reset_form();
						if(data === 'Berhasil'){
                            console.log('list_view2',)             
							sc_alert('Sukses', data);
							reset_form();
						}else{
							sc_alert('ERROR', data);
						}
                        // $('#simpan_load').hide();
					}, 'json');
                 
		 	}
				
				return false;
			});
			
			function reset_form(){
                $('#VESSEL_NAME, #VESSEL_CODE, #VOYAGE_IN, #VOYAGE_OUT, #KADE_NAME, #KADE_AWAL, #KADE_AKHIR').val('');
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