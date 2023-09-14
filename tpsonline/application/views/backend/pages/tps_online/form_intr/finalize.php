<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1 style="margin-bottom:25px;margin-left:-25px">ACTIVITY BM INTERNASIONAL</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 210%;">
                            <div class="card-body">                 
                           
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed" id="dt_tabel">
                        <thead>
                            <tr>                            
                                <th><?php echo gridHeader('VESSEL_NAME', 'Nama Kapal', $cfg) ?></th>                              
                                <th><?php echo gridHeader('VOYAGE', 'Voyage', $cfg) ?></th>                        
                                <th><?php echo gridHeader('ATA', 'ATA', $cfg) ?></th>
                                <th><?php echo gridHeader('ATB', 'ATB', $cfg) ?></th>
                                <th><?php echo gridHeader('ATD', 'ATD', $cfg) ?></th>
                                <th><?php echo gridHeader('REALISASI BONGKAR', 'REALISASI BONGKAR', $cfg) ?></th>    
                                <th><?php echo gridHeader('REALISASI MUAT', 'REALISASI MUAT', $cfg) ?></th>    
                                <th><?php echo gridHeader('ACTIVITY', 'ACTIVITY', $cfg) ?></th>                          
                                <th><?php echo gridHeader('TIME_START', 'TIME_START', $cfg) ?></th>
                                <th><?php echo gridHeader('TIME_END', 'TIME_END', $cfg) ?></th>
                                <th><?php echo gridHeader('ET_BT', 'ET_BT', $cfg) ?></th>
                                <th><?php echo gridHeader('USH', 'USH', $cfg) ?></th>

                                <th>Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php
                             $grid_state = 'tps_online/form_intr/finalize/p:1';
                         
                               if($databm){                          
                                foreach($databm as $row){
                            
                                    ?>
                                    <tr>                      
                                        <td><?php echo $row->NAMA_KAPAL?></td>                                       
                                        <td><?php echo $row->VOYAGE?></td>   
                                        <td><?php
                                          if ($row->ATA == ''|| $row->ATA == '1970-01-01 07:00:00') {
                                                echo "-";
                                          
                                            } else {
                                                echo date('d-M-Y H:i:s', strtotime($row->ATA));
                                            }
                                        ?></td>  
                                          <td><?php
                                            if ($row->ATB == ''|| $row->ATB == '1970-01-01 07:00:00') {
                                                echo "-";
                                          
                                            } else {
                                                echo date('d-M-Y H:i:s', strtotime($row->ATB));
                                            }
                                        ?></td>                                    
                                              <td><?php
                                            if ($row->ATD == '' || $row->ATD == '1970-01-01 07:00:00') {
                                                echo "-";
                                          
                                            } else {
                                                echo date('d-M-Y H:i:s', strtotime($row->ATD));
                                            }
                                        ?></td>                                      
                                        <!-- <td><?php echo $row->PBM?></td> -->
                                        <td><?php echo $row->REALISASI_BONGKAR?></td>  
                                        <td><?php echo $row->REALISASI_MUAT?></td>
                                        <td><?php echo $row->ACTIVITY?></td>
                                        <!-- <td><?php echo $row->SHIFT?></td>                                    -->
                                     
                                        <td><?php echo date('d-M-Y H:i:s', strtotime($row->TIME_START)) ?></td>
                                        <td><?php echo date('d-M-Y H:i:s', strtotime($row->TIME_END)) ?></td>
                                        <td><?php echo $row->ET_BT ?></td>  
                                        <td><?php echo $row->USH ?></td>  
                                                      
                                        
                                        <td>
                                        <a href="<?php echo site_url('tps_online/form_intr/view/' . $row->ID_HEADER . '/' . $grid_state) ?>" class="edit_link">Edit</a>                                           
                                     
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
                    <div class="form-group col-md-6" style="margin-top:10px;margin-left:0px">                             
                         <a href="<?php echo site_url('tps_online/form_intr/listview') ?>" class="btn btn-primary">Kembali</a>            
                    </div>  
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
		
            let url = bs.siteURL + 'FormDeskripsi/load_vessel/' + bs.token;
            console.log('testtt tamm2', url)
          $.get(url, function(data){
               console.log('testtt tamm34', data)
           

               var a = new Date('2018-01-17T12:18')
               var b = new Date('2018-01-17T13:18')

            console.log(Math.abs(a - b)) // safe to use
            console.log(Math.abs(b - a)) // safe to use         
           
          
				if(data){
                    var rec = data.data[0];        
                    var total_rencana = +rec.RENCANA_BONGKAR + +rec.RENCANA_MUAT;
                    console.log('total', total_rencana)
                    $('#NAMA_KAPAL').html(rec.NAMA_KAPAL);
                    $('#VOYAGE').html(rec.VOYAGE);
                    $('#KADE_DERMAGA').html(rec.KADE_DERMAGA);
                    $('#PBM').html(rec.PBM);
                    $('#ETA').html(rec.ETA);
                    $('#ETB').html(rec.ETB);
                    $('#ETD').html(rec.ETD);
                    $('#ATA').html(rec.ATA);
                    $('#ATB').html(rec.ATB);
                    $('#ATD').html(rec.ATD);
                    $('#TOTAL_RENCANA').html(total_rencana);
                    $('#RENCANA_BONGKAR').html(rec.RENCANA_BONGKAR);
                    $('#RENCANA_MUAT').html(rec.RENCANA_MUAT);
                    $('#COMMENCE').html(rec.COMMENCE);
                    $('#COMPLETE').html(rec.COMPLETE);
					
		
				}else{
					sc_alert('Error', data.msg);
				}
			}, 'json');

           
     

        })

	</script>
</body>
</html>