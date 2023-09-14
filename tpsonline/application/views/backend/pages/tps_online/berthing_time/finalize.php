<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1 style="margin-bottom:25px;margin-left:-25px">ACTIVITY BERTHING PLAN</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                     
                               
                            <div class="form-group col-md-6" style="margin-top:10px;margin-left:20px">
                                    <!-- <button type="submit" class="btn btn-success" id="simpan">Cari</button> -->
                                    <!-- <button class="btn btn-primary" a href="FormDetail/form_detail">Tambah Baru</button>   -->
                                    <a href="<?php echo site_url('tps_online/berthing_time/new') ?>" class="btn btn-primary">Tambah Baru</a>                               
                              </div>
                                   
                        
                                   
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed" id="dt_tabel">
                        <thead>
                            <tr>     
                                <th><?php echo gridHeader('VESSEL_CODE', 'Kode Kapal', $cfg) ?></th>
                                <th><?php echo gridHeader('VESSEL_NAME', 'Nama Kapal', $cfg) ?></th>          
                                <th><?php echo gridHeader('VOYAGE_IN', 'Voyage IN', $cfg) ?></th>  
                                <th><?php echo gridHeader('VOYAGE_OUT', 'Voyage OUT', $cfg) ?></th>  
                                <th><?php echo gridHeader('KADE_NAME', 'Nama Kade', $cfg) ?></th> 
                                <th><?php echo gridHeader('KADE_AWAL', 'Kade Awal', $cfg) ?></th>   
                                <th><?php echo gridHeader('KADE_AKHIR', 'Kade Akhir', $cfg) ?></th>  
                                                                          
                                <th>Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php
                             $grid_state = 'tps_online/berthing_time/finalize/p:1';
                        //    $grid_state = 'DashboardReal/form_manual' . '/p:' . $cfg->currPage;;
                        
                        if($databm){                          
                            foreach($databm as $row){
                          // print_r($row);
                            // var_dump($row);die()
                                //echo var_dump($data)
                               //echo $row[$i]['VESSEL_NAME'];
                                // print_r($row[0]['VESSEL_NAME'])
                                ?>
                                <tr>             
                                        <td><?php echo $row->VESSEL_CODE?></td> 
                                        <td><?php echo $row->VESSEL_NAME?></td>  
                                        <td><?php echo $row->VOYAGE_IN ?></td>  
                                        <td><?php echo $row->VOYAGE_OUT ?></td>                                  
                                        <td><?php echo $row->KADE_NAME?></td> 
                                        <td><?php echo $row->KADE_AWAL?></td>    
                                        <td><?php echo $row->KADE_AKHIR?></td>       
                                        
                                        <td>
                                        <a href="<?php echo site_url('tps_online/berthing_time/view/' . $row->id_berthing . '/' . $grid_state) ?>" class="edit_link">Edit</a>                                            
                                                                              
                                     
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
		</div><!-- /.container -->
		
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