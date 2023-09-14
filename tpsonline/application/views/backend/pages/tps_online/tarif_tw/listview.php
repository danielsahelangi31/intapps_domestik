<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>
<body>

	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>
		
		<div class="container"  style="max-width: 1500px;">
			
             <h1 style="margin-bottom:25px;margin-left:160px">TARIF TW</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 220%;">
                            <div class="card-body">                              
                         
                            <form>
                                <div class="col-md-6" style="margin-bottom:5px;margin-left:200px">
                                        <?php $this->load->view('backend/components/searchform') ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">

                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-6" style="margin-top:5px;margin-left:200px">                          
                                    <div class="col-md-6">
                                        <div class="card rounded-0 shadow" style="width: 220%;">
                                        <div class="card-body">                                    
                                    
                                            <div class="btn"  id="simpan_load" style="margin-left:0%">                                    
                                              <a href="<?php echo site_url('tps_online/tarif_tw/new') ?>" class="btn btn-primary">Tambah Baru</a>     
                                        </div> 
                                        </div>  
                                        </div>                            
                              </div>
                                
                                   
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>  
                                <th style="text-align: center"><?php echo gridHeader('PERODE_BULAN', 'Terminal', $cfg) ?></th>                 
                                <th style="text-align: center"><?php echo gridHeader('PERODE_BULAN', 'Komoditi', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Pelayanan', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Golongan', $cfg) ?></th> 
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Tahun', $cfg) ?></th>   
                      
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php               
                           $grid_state = 'tps_online/tarif_tw/listview/p:1';                      
                        
                               if($datatarif){    
                                 foreach($datatarif as $row){
                                
                                    ?>
                                    <tr>      
                                        <td style="text-align: center"><?php echo $row['TERMINAL']?></td>                               
                                        <td style="text-align: center"><?php echo $row['KOMODITI']?></td>                                       
                                        <td style="text-align: center"><?php echo $row['PELAYANAN']?></td>   
                                        <td style="text-align: center"><?php echo $row['GOLONGAN']?></td>  
                                        <td style="text-align: center"><?php echo $row['TAHUN']?></td>  

                                        <td style="text-align: center">
                                        <a href="<?php echo site_url('tps_online/tarif_tw/finalize/' . $row['ID_TARIF'] .'/' . $grid_state) ?>" class="edit_link">View</a>                                            
                                        </td> 

                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr><td colspan="7"><em>Tidak ada data</em></td></tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php $this->load->view('backend/components/paging') ?>                 
                                    </div>
                                    </div>                                  
                                </form>
                            </div>
                        
			</div>
            </div>
            </div>		
			
			<?php echo form_close() ?>
		
			<div id="kampret_loader">

            </div>
		</div>
		
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
	
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
	
	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	
	<script type="text/javascript">
		
		$(document).ready(function(){
            $('#simpan').click(function(e){
        
               var param = {   
        
                   'PELAYARAN' : $('#PELAYARAN').val(), 
                   'TAHUN' : $('#TAHUN').val(), 
                   'TERMINAL' : $('#TERMINAL').val(),      
                     
               }		
               console.log(param);
               is_error = false;         

               console.log('err', is_error);
               if(is_error){      
                   $.notify("Harap perbaiki field yang ditandai","error");     
       
                }else{
   
                e.preventDefault(); 
				var urls = bs.siteURL + 'tps_online/rkap_trafik_kapal/search/' + bs.token;
                console.log('url', urls)             
				
                $.post(urls, param, function(data){
                    console.log('success');
     
                })
               }
               return false;
           });

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

        })

	</script>
</body>
</html>