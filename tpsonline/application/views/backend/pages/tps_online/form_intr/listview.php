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
			
             <h1 style="margin-bottom:25px;margin-left:-25px">MONITORING BM KAPAL INTERNASIONAL</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 250%;">
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
                                  
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed" id="dt_tabel">
                        <thead>
                            <tr>
                                 <th style="text-align: center"><?php echo gridHeader('VESSEL_CODE', 'Kode Kapal', $cfg) ?></th>      
                                 <th style="text-align: center"><?php echo gridHeader('VESSEL_NAME', 'Nama Kapal', $cfg) ?></th>                              
                                 <th style="text-align: center"><?php echo gridHeader('VOYAGE_IN', 'Voyage', $cfg) ?></th>  
                                 <th style="text-align: center"><?php echo gridHeader('KADE', 'Kade', $cfg) ?></th>                        
                                 <th style="text-align: center"><?php echo gridHeader('ETA', 'ETA', $cfg) ?></th>
                                 <th style="text-align: center"><?php echo gridHeader('ARRIVAL', 'ATA', $cfg) ?></th>
                                 <th style="text-align: center"><?php echo gridHeader('OPERATIONAL', 'ATB', $cfg) ?></th>    
                                 <th style="text-align: center"><?php echo gridHeader('DEPARTURE', 'ATD', $cfg) ?></th>                         
                              
                                 <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php
                           $grid_state = 'tps_online/form_intr/listview/p:1';
                   
                               if($dataintr){                          
                                foreach($dataintr as $row){                               
                                    ?>
                                    <tr>
                                         <td style="text-align: center"><?php echo $row->VESSEL_CODE?></td>
                                         <td><?php echo $row->VISIT_NAME?></td>                                       
                                         <td style="text-align: center"><?php echo $row->VOYAGE_IN ?></td> 
                                         <td style="text-align: center"><?php echo $row->POSITION_CODE ?></td>                              
                                        <td style="text-align: center"><?php
                                            if ($row->ETA == '') {
                                                echo "-";
                                          
                                            } else {
                                                echo date('d-M-Y H:i:s', strtotime($row->ETA));
                                            }
                                        ?></td> 
                                         <td style="text-align: center"><?php
                                            if ($row->ETA == '') {
                                                echo "-";
                                          
                                            } else {
                                                echo date('d-M-Y H:i:s', strtotime($row->ETA));
                                            }
                                        ?></td>  
                                           <td style="text-align: center"><?php
                                            if ($row->ARRIVAL == '') {
                                                echo "-";
                                          
                                            } else {
                                                echo date('d-M-Y H:i:s', strtotime($row->ARRIVAL));
                                            }
                                        ?></td>                                    
                                               <td style="text-align: center"><?php
                                            if ($row->DEPARTURE == '') {
                                                echo "-";
                                          
                                            } else {
                                                echo date('d-M-Y H:i:s', strtotime($row->DEPARTURE));
                                            }
                                        ?></td>              
                                         <td style="text-align: center">                                                                        
                                            <a href="<?php echo site_url('tps_online/form_intr/new/' .$row-> VISIT_NAME. '/' .$row->VOYAGE_IN . '/' . $grid_state) ?>" class="btn btn-primary">Tambah Baru</a>   
                                            <a href="<?php echo site_url('tps_online/form_intr/finalize/'. $row-> VISIT_NAME .'/' .$row-> VOYAGE_IN .'/' . $grid_state) ?>"  style="margin-left:5px" class="btn btn-danger">Activity</a> 
                                            <a href="<?php echo base_url('tps_online/form_intr/export_form_xls/' . $row->VISIT_NAME . '/'  .$row-> VOYAGE_IN .'/' . $grid_state)?>" class="btn btn-success btn-sm" style="margin-left:5px" id="unduh">
                                             <i class="glyphicon glyphicon-download"></i> Unduh xls</a>
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

    $('#unduh').click(function(){
            let url_bm = bs.siteURL + 'tps_online/form/load_data_bm/' + bs.token;
            console.log('url_bm', url_bm);
		    let tabel = $('#dt_tabel').DataTable({
            "bProcessing": true,
            "serverSide": false,
            "paging": true,
            "searching": true,
            "ajax":{
                url : url_bm,
                type: "post",
               
                dataType: 'json',
                error: function(response){
                    alert('Failed Load DataTables Data');
                },

            },
            "aoColumns": [

		        { "mData": 'SHIFT' },
		        { "mData": 'ACTIVITY' },	
		        { "mData": 'REALISASI_MUAT' },
		        { "mData": 'REALISASI_BONGKAR' },   
           	    
		    ],         
            "lengthChange": true,
            "bFilter": true
        });
        console.log('tabel_bm', tabel);
   });

        })

	</script>
</body>
</html>