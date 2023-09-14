<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
    <?php
$target_url = $this->router->fetch_class().'/'.$this->router->fetch_method();
if($this->router->fetch_directory()){
	$target_url = $this->router->fetch_directory().'/'.$target_url;
}
?>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
             <h1 style="margin-bottom:25px;margin-left:-25px">BERTHING PLAN</h1>
			
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
                                    <a href="<?php echo site_url('tps_online/berthing_time/new') ?>" class="btn btn-primary">Tambah Baru</a>                               
                              </div>

                      
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed" id="dt_tabel">
                        <thead>
                            <tr>
                                <!-- <th><?php echo gridHeader('VISIT_ID', 'ID Kapal', $cfg) ?></th>       -->
                                <th><?php echo gridHeader('VESSEL_CODE', 'Kode Kapal', $cfg) ?></th>
                                <th><?php echo gridHeader('VISIT_NAME', 'Nama Kapal', $cfg) ?></th>          
                                <th><?php echo gridHeader('VOYAGE_IN', 'Voyage IN', $cfg) ?></th>  
                                <th><?php echo gridHeader('VOYAGE_OUT', 'Voyage OUT', $cfg) ?></th>  
                                <th><?php echo gridHeader('ETA', 'ETA', $cfg) ?></th>
                                <th><?php echo gridHeader('ETD', 'ETD', $cfg) ?></th>                           
                              
                                <th>Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php
                           $grid_state = 'tps_online/berthing_time/listview/p:1';        
                        
                               if($databerth){                          
                                foreach($databerth as $row){
                                   //  var_dump($databerth);die();
                                    ?>
                                    <tr>
                                        <!-- <td><?php echo $row->VISIT_ID?></td> -->
                                        <td><?php echo $row->VESSEL_CODE?></td> 
                                        <td><?php echo $row->VISIT_NAME?></td>  
                                        <td><?php echo $row->VOYAGE_IN ?></td>  
                                        <td><?php echo $row->VOYAGE_OUT ?></td>    
                                        <td><?php echo date('d-M-Y H:i:s', strtotime($row->ETA)) ?></td>
                                        <td><?php echo date('d-M-Y H:i:s', strtotime($row->ETD)) ?></td>            
                                                                                          
                                        
                                        <td>
                                            <!-- <a href="<?php echo site_url('tps_online/berthing_time/view/' . $row->VISIT_ID . '/' . $grid_state) ?>" class="edit_link">Edit</a>                                             -->
                                            <a href="<?php echo site_url('tps_online/berthing_time/finalize/' . $row->VISIT_NAME . '/' .$row-> VOYAGE_IN . '/' . $grid_state) ?>" class="edit_link">Activity</a>  
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
		
			
			<?php echo form_close() ?>
		
			<div id="kampret_loader"></div>
		</div>
		
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
	
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
	
	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	
	<script type="text/javascript">
		
		$(document).ready(function(){

         
        })

	</script>
</body>
</html>