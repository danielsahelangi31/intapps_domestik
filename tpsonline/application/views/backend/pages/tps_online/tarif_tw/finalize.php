<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1 style="margin-bottom:25px;margin-left:-25px">TARIF TW</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                     
                               
                                                            
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed" id="dt_tabel">
                        <thead>
                            <tr>     
                                <th style="text-align: center"><?php echo gridHeader('PERODE_BULAN', 'Terminal', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('PERODE_BULAN', 'Komoditi', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Pelayanan', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Golongan', $cfg) ?></th> 
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Tahun', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Tipe Tarif', $cfg) ?></th>                      
                                <th style="text-align: center"><?php echo gridHeader('TARIF_TW_12', 'Tarif I', $cfg) ?></th>                            
                                <th style="text-align: center"><?php echo gridHeader('TARIF_TW_34', 'Tarif II', $cfg) ?></th>        
                                                                          
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php
                             $grid_state = 'tps_online/tarif_tw/finalize/p:1';
                                          
                        if($databm){                          
                            foreach($databm as $row){
                   
                                ?>
                                <tr>   
                                        <td style="text-align: center"><?php echo $row->TERMINAL?></td>   
                                        <td style="text-align: center"><?php echo $row->KOMODITI?></td>                                       
                                        <td style="text-align: center"><?php echo $row->PELAYANAN?></td>   
                                        <td style="text-align: center"><?php echo $row->GOLONGAN?></td>  
                                        <td style="text-align: center"><?php echo $row->TAHUN?></td>   
                                        <td style="text-align: center"><?php echo $row->TYPE?></td>                                        
                                        <td style="text-align: right"><?php echo number_format($row->TARIF_1, 2, ",", ".")?></td>         
                                        <td style="text-align: right"><?php echo number_format($row->TARIF_2, 2, ",", ".")?></td>                                                                  
                              
                                        <td style="text-align: center">
                                        <a href="<?php echo site_url('tps_online/tarif_tw/view/' . $row->ID_TARIF . '/' . $grid_state) ?>" class="edit_link">Edit</a>                                            
                                                                              
                                     
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