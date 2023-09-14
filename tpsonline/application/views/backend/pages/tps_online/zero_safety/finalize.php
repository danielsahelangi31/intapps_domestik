<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1 style="margin-bottom:25px;margin-left:-25px">ACTIVITY SAFETY</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                     
                               
                            <div class="form-group col-md-6" style="margin-top:10px;margin-left:20px">                      
                                    <a href="<?php echo site_url('tps_online/zero_safety/new') ?>" class="btn btn-primary">Tambah Baru</a>                               
                              </div>
                                   
                        
                                   
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed" id="dt_tabel">
                        <thead>
                            <tr>     
                                <th><?php echo gridHeader('TERMINAL', 'Terminal', $cfg) ?></th>
                                <th><?php echo gridHeader('CREATED_DATE', 'Entry Data', $cfg) ?></th>
                                <th><?php echo gridHeader('PERIODE_BULAN', 'Periode (Bulan)', $cfg) ?></th>  
                                <th><?php echo gridHeader('TAHUN', 'Tahun', $cfg) ?></th>    
                                <th><?php echo gridHeader('MAKER', 'Maker', $cfg) ?></th>                          
                                <th><?php echo gridHeader('ACCIDENT', 'Accident', $cfg) ?></th>                            
                                <th><?php echo gridHeader('INCIDENT', 'Incident', $cfg) ?></th>
                                <th><?php echo gridHeader('UNIT_IMPACT', 'Unit Impact', $cfg) ?></th>               
                                                                            
                                <th>Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php
                             $grid_state = 'tps_online/zero_safety/finalize/p:1';
               
                        
                        if($databm){                          
                            foreach($databm as $row){                  
                                ?>
                                <tr>   
                                        <td><?php echo $row->TERMINAL?></td> 
                                        <td><?php echo date('d-m-Y H:i:s', strtotime($row->CREATED_DATE))?></td>                                     
                                        <td><?php echo $row->PERIODE_BULAN?></td>                                       
                                        <td><?php echo $row->TAHUN?></td>   
                                        <td><?php echo $row->MAKER?></td>                                 
                                        <td><?php echo $row->ACCIDENT?></td>                              
                                        <td><?php echo $row->INCIDENT?></td>                              
                                        <td><?php echo $row->UNIT_IMPACT ?></td>     
                                        
                                        <td>
                                        <a href="<?php echo site_url('tps_online/zero_safety/view/' . $row->id_zero . '/' . $grid_state) ?>" class="edit_link">Edit</a>                                            
                                                                              
                                     
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
             </div>
           </div>
           </div>
           </div>
           </div>
           </div>
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