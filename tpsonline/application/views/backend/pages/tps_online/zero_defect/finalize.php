<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1 style="margin-bottom:25px;margin-left:-25px">ACTIVITY ZERO DEFECT (QUALITY)</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                     
                               
                            <div class="form-group col-md-6" style="margin-top:10px;margin-left:20px">                          
                                    <a href="<?php echo site_url('tps_online/zero_defect/new') ?>" class="btn btn-primary">Tambah Baru</a>                               
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
                                <th><?php echo gridHeader('LQ_GATE_1_BACK_KCY', 'LQ Gate1 (Back KCY)', $cfg) ?></th>                            
                                <th><?php echo gridHeader('LQ_GATE_1_QUARANTINE', 'LQ Gate1 (Quarantine)', $cfg) ?></th>
                                <th><?php echo gridHeader('LQ_GATE_2', 'LQ Gate 2', $cfg) ?></th>
                                <th><?php echo gridHeader('LQ_GATE_3', 'LQ Gate 3', $cfg) ?></th>
                                <th><?php echo gridHeader('CARGO_DEFECT', 'Cargo Defect', $cfg) ?></th>   
                                                                          
                                <th>Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php
                             $grid_state = 'tps_online/zero_defect/finalize/p:1';
                    
                        
                        if($databm){                          
                            foreach($databm as $row){                     
                                ?>
                                <tr>   
                                    <td><?php echo $row->TERMINAL?></td>        
                                    <td><?php echo date('d-m-Y H:i:s', strtotime($row->CREATED_DATE))?></td>                                 
                                    <td><?php echo $row->PERIODE_BULAN?></td>                                       
                                    <td><?php echo $row->TAHUN?></td>  
                                    <td><?php echo $row->MAKER?></td>                    
                                    <td><?php echo $row->LQ_GATE_1_BACK_KCY?></td>                              
                                    <td><?php echo $row->LQ_GATE_1_QUARANTINE?></td>                              
                                    <td><?php echo $row->LQ_GATE_2 ?></td>
                                    <td><?php echo $row->LQ_GATE_3?></td>                                       
                                    <td><?php echo $row->CARGO_DEFECT?></td>    
                                        
                                        <td>
                                        <a href="<?php echo site_url('tps_online/zero_defect/view/' . $row->id_zero . '/' . $grid_state) ?>" class="edit_link">Edit</a>                                            
                                                                              
                                     
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
            $('#MAKER').select2();
		
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