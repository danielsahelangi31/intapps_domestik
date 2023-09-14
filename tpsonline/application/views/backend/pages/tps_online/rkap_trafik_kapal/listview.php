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
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                              
                         
                            <form>
                                <div class="col-md-6">
                                        <?php $this->load->view('backend/components/searchform') ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="pull-right">

                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group col-md-6" style="margin-top:10px;margin-left:20px">                                   
                                    <div class="col-md-6">
                                        <div class="card rounded-0 shadow" style="width: 200%;">
                                        <div class="card-body">                              
                                              <div class="btn"  id="simpan_load" style="margin-left:0%">                       
                                              <a href="<?php echo site_url('tps_online/rkap_trafik_kapal/new') ?>" class="btn btn-primary">Tambah Baru</a>     
                                        </div> 
                                        </div>  
                                        </div>                            
                              </div>
                                
                                   
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>          
                                <th style="text-align: center"><?php echo gridHeader('TERMINAL', 'Terminal', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Tahun', $cfg) ?></th>     
                                <th style="text-align: center"><?php echo gridHeader('SATUAN', 'Satuan', $cfg) ?></th>                            
                                <th style="text-align: center"><?php echo gridHeader('JANUARI', 'Januari', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('FEBRUARI', 'Februari', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('MARET', 'Maret', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('APRIL', 'April', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('MEI', 'Mei', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('JUNI', 'Juni', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('JULI', 'Juli', $cfg) ?></th> 
                                <th style="text-align: center"><?php echo gridHeader('AGUSTUS', 'Agustus', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('SEPTEMBER', 'September', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('OKTOBER', 'Oktober', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('NOVEMBER', 'November', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('DESEMBER', 'Desember', $cfg) ?></th>                        

                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php                      
                           $grid_state = 'tps_online/rkap_trafik_kapal/listview/p:1';                      
                        
                               if($datatrafik){    
                                 foreach($datatrafik as $row){
                     
                                    ?>
                                    <tr>                            
                         
                                        <td style="text-align: center"><?php echo $row['TERMINAL']?></td>                                  
                                        <td style="text-align: center"><?php echo $row['TAHUN']?></td>                              
                                        <td style="text-align: center"><?php echo $row['SATUAN']?></td>                                 
                                        <td style="text-align: right"><?php echo number_format($row['JANUARI'], 0, ",", ".")?></td>                                       
                                        <td style="text-align: right"><?php echo number_format($row['FEBRUARI'], 0, ",", ".")?></td>  
                                        <td style="text-align: right"><?php echo number_format($row['MARET'], 0, ",", ".")?></td>                                       
                                        <td style="text-align: right"><?php echo number_format($row['APRIL'], 0, ",", ".")?></td>  
                                        <td style="text-align: right"><?php echo number_format($row['MEI'], 0, ",", ".")?></td>                                       
                                        <td style="text-align: right"><?php echo number_format($row['JUNI'], 0, ",", ".")?></td>  
                                        <td style="text-align: right"><?php echo number_format($row['JULI'], 0, ",", ".")?></td>                                       
                                        <td style="text-align: right"><?php echo number_format($row['AGUSTUS'], 0, ",", ".")?></td>  
                                        <td style="text-align: right"><?php echo number_format($row['SEPTEMBER'], 0, ",", ".")?></td>                                       
                                        <td style="text-align: right"><?php echo number_format($row['OKTOBER'], 0, ",", ".")?></td> 
                                        <td style="text-align: right"><?php echo number_format($row['NOVEMBER'], 0, ",", ".")?></td>                                       
                                        <td style="text-align: right"><?php echo number_format($row['DESEMBER'], 0, ",", ".")?></td>   

                                        <td style="text-align: center">
                                            
                                        <a href="<?php echo site_url('tps_online/rkap_trafik_kapal/view/' . $row['id_trafik'] . '/' . $grid_state) ?>" class="edit_link">Edit</a>                                            
                                   
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
        
                   'TAHUN' : $('#TAHUN').val(), 
                   'TERMINAL' : $('#TERMINAL').val(),      
                     
               }		
               console.log(param);
               is_error = false;         

           
               console.log('err', is_error);
               if(is_error){      
                   $.notify("Harap perbaiki field yang ditandai","error");    
                
         
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