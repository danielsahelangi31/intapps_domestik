<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>
<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
             <h1 style="margin-bottom:25px;margin-left:-25px">KPI</h1>
			
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
                                         
                                            <div class="btn"  id="simpan_load" style="margin-left:0%">                              
                                              <a href="<?php echo site_url('tps_online/kpi_barang/new') ?>" class="btn btn-primary">Tambah Baru</a>     
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
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Periode', $cfg) ?></th>               
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'USH', $cfg) ?></th>     
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'BOR(%)', $cfg) ?></th>      
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'YOR(%)', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'ET/BT(%)', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Zero Defect', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Safety', $cfg) ?></th> 
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'SLA Pranota BM', $cfg) ?></th>
                        
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php                    
                           $grid_state = 'tps_online/kpi_barang/listview/p:1';                      
                        
                               if($databarang){    
                                 foreach($databarang as $row){                                
                       
                                    ?>
                                    <tr>                                    
                                        <td style="text-align: center"><?php echo $row['TERMINAL']?></td>                                       
                                        <td style="text-align: center"><?php echo date('Y', strtotime($row['PERIODE']))?></td>                       
                                        <td style="text-align: center"><?php echo $row['USH']?></td>                              
                                        <td style="text-align: center"><?php echo $row['BOR'] ?></td>
                                        <td style="text-align: center"><?php echo $row['YOR']?></td>                                       
                                        <td style="text-align: center"><?php echo $row['ET_BT']?></td>  
                                        <td style="text-align: center"><?php echo $row['ZERO_DEFECT']?></td> 
                                        <td style="text-align: center"><?php echo $row['SAFETY']?></td>
                                        <td style="text-align: center"><?php echo $row['SLA_PRANOTABM']?></td>                                       

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

		        { "mData": 'TANGGAL_TIME' },
		        { "mData": 'SHIFT' },
		        { "mData": 'ACTIVITY' },
		        { "mData": 'TIME_END' },
		        { "mData": 'REALISASI_MUAT' },
		        { "mData": 'REALISASI_BONGKAR' },
		        { "mData": 'REMAINING_MUAT' },
		        { "mData": 'REMAINING_BONGKAR' }		    
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