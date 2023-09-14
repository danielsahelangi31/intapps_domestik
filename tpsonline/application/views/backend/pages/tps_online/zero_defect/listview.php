<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
    <style>
     .select2-container .select2-selection--single {
        height: 34px;!important;
        }
    </style>
</head>
<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
             <h1 style="margin-bottom:25px;margin-left:-25px">ZERO DEFECT (QUALITY)</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                              
                         
                            <form>                     
                                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:0px;width:100%;"> 
                                <label>Terminal: </label>
                                    <select class="form-control" name="TERMINAL" id="TERMINAL">                                                                                                                                    
                                                <option value="<?php echo $terminals ?>"><?php echo $terminals  ? : '-- Pilih --'?></option>                                     
                                                <option value="DOMESTIK">DOMESTIK</option>
                                                <option value="INTERNASIONAL">INTERNASIONAL</option>                                    
                                                        
                                    </select> 
                                </div> 
                                </div> 

                                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:0px;width:100%;"> 
                                <label>Periode (Bulan): </label>
                                    <select class="form-control" name="PERIODE_BULAN" id="PERIODE_BULAN">
                                        <option value="<?php echo $periods ?>"><?php echo $periods ? : '-- PILIH --'?></option>
                                        <option value="JANUARI">JANUARI</option>
                                        <option value="FEBRUARI">FEBRUARI</option>
                                        <option value="MARET">MARET</option>
                                        <option value="APRIL">APRIL</option>
                                        <option value="MEI">MEI</option>
                                        <option value="JUNI">JUNI</option>
                                        <option value="JULI">JULI</option>
                                        <option value="AGUSTUS">AGUSTUS</option>
                                        <option value="SEPTEMBER">SEPTEMBER</option>
                                        <option value="OKTOBER">OKTOBER</option>
                                        <option value="NOVEMBER">NOVEMBER</option>
                                        <option value="DESEMBER">DESEMBER</option>
                                    </select>	
                                </div>
                                </div>  

                                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:0px;width:100%;"> 
                                <label>Tahun: </label>
                                    <select class="form-control" name="TAHUN" id="TAHUN">
                                        <?php for($i = date('Y'); $i < date('Y+1'); $i++){ ?>
                                        <option value="<?php echo $tahuns ?>"><?php echo $tahuns  ? : '-- Pilih --'?></option>   
                                        <option value="<?= $i-1 ?>"><?= $i-1 ?></option>
                                        <option value="<?= date('Y') ?>"><?= date('Y') ?></option>		                   
                                        <option value="<?= $i+1 ?>"><?= $i+1 ?></option>
                                        <?php } ?>
                                    </select>
                                </div> 
                                </div>                     

                                <div class="col-lg-3 col-md-3 col-sm-12 col-12">
                                <div class="form-group" style="margin-left:0px;width:100%;"> 
                                <label>Maker: </label>
                                    <select class="form-control" id="MAKER" name="MAKER">
                                    <option value="<?php echo $makers ?>"><?php echo $makers  ? : '-- Pilih --'?></option>   
                                        <?php
                                        foreach ($datasource as $make){
                                            ?>
                                            <option value="<?php echo $make->BRAND; ?>" ><?php echo $make->BRAND; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div> 
                                </div> 

                                <div class="form-group col-md-6" style="margin-top:5px;margin-left:0px;width:100%">
                                   <button type="submit" class="btn btn-success" id="simpan">Cari</button>
                                   <a href="<?php echo site_url('tps_online/zero_defect/listview') ?>" class="btn btn-warning">Reset</a>  
                                   <hr /> 
                                </div>
                              
                                
                                <div class="form-group col-md-8" style="margin-top:-10px;margin-left:0px;">
                            
                                     <br>
                                    <a href="<?php echo site_url('tps_online/zero_defect/new') ?>" class="btn btn-primary">Tambah Baru</a>                               
                             
                                 </div>
                                
                                   
                    <?php echo form_close();?>
                                  
                    <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>                   
                                <th><?php echo gridHeader('TERMINAL', 'Terminal', $cfg) ?></th>
                                <th><?php echo gridHeader('PERIODE_BULAN', 'Periode (Bulan)', $cfg) ?></th>  
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Tahun', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('MAKER', 'Maker', $cfg) ?></th>                          
                                <th style="text-align: center"><?php echo gridHeader('LQ_GATE_1_BACK_KCY', 'LQ Gate1 (Back KCY)', $cfg) ?></th>                            
                                <th style="text-align: center"><?php echo gridHeader('LQ_GATE_1_QUARANTINE', 'LQ Gate1 (Quarantine)', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('LQ_GATE_2', 'LQ Gate 2', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('LQ_GATE_3', 'LQ Gate 3', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('CARGO_DEFECT', 'CARGO DEFECT', $cfg) ?></th>     
                                    
                              
                                <th>Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                            <?php                     
                           $grid_state = 'tps_online/zero_defect/listview/p:1';                      
                        
                               if($datazero){    
                                 foreach($datazero as $row){
                             
                                    ?>
                                    <tr>  
                                        <td><?php echo $row['terminal']?></td>                                    
                                        <td><?php echo $row['periode_bulan']?></td>                                       
                                        <td style="text-align: center"><?php echo $row['tahun']?></td>   
                                        <td style="text-align: center"><?php echo $row['maker']?></td>                                 
                                        <td style="text-align: center"><?php echo $row['lq_gate_1_back_kcy']?></td>                              
                                        <td style="text-align: center"><?php echo $row['lq_gate_1_quarantine']?></td>                              
                                        <td style="text-align: center"php echo $row['lq_gate_2'] ?></td>
                                        <td style="text-align: center"><?php echo $row['lq_gate_3']?></td>                                       
                                        <td style="text-align: center"><?php echo $row['cargo_defect']?></td>                                           

                                        <td style="text-align: center">                                            
                                            <a href="<?php echo site_url('tps_online/zero_defect/finalize/'.$row['periode_bulan']. '/' .$row['tahun'] . '/' .$row['terminal'] . '/'  .$row['maker'] . '/'. $grid_state) ?>" class="edit_link">Activity</a>                                            
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
    </div>
    </div>
    <?php $this->load->view('backend/elements/footer') ?>
	
	<script type="text/javascript" src="<?php echo base_url('assets/js/jquery.scrollTo-1.4.3.1-min.js') ?>"></script>
	
	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	
	<script type="text/javascript">
		
		$(document).ready(function(){
        $('#MAKER').select2();

        $('#simpan').click(function(e){
            var param = {                      
					'TERMINAL' : $('#TERMINAL').val(),
                    'TAHUN' : $('#TAHUN').val(),
                    'PERIODE_BULAN' : $('#PERIODE_BULAN').val(),
                    'MAKER' : $('#MAKER').val(),                  

                     }

                    console.log('prm',param);
                    terminal = $('#TERMINAL').val();
                    tahun = $('#TAHUN').val();
                    periode = $('#PERIODE_BULAN').val();
                    maker = $('#MAKER').val();               

                    var urls = bs.siteURL + 'tps_online/zero_defect/listvieww/'+terminal+'/'+tahun+'/'+periode + '/'+maker+'/';
                    console.log('url', urls)
                    e.preventDefault(); 
                    
                    if (terminal != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview1/'+terminal;           
                    } else if (periode != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview2/'+periode;    
                    } else if (tahun != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview3/'+tahun;    
                    } else if (maker != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview4/'+maker; 
                    }

                    if (terminal != '' && periode != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview5/'+terminal+'/'+periode;    
                    } else if (terminal != '' && tahun != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview6/'+terminal+'/'+tahun;    
                    } else if (terminal != '' && maker != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview7/'+terminal+'/'+maker;    
                    } else if (periode != '' && tahun != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview8/'+periode+'/'+tahun;    
                    } else if (periode != '' && maker != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview9/'+periode+'/'+maker;    
                    } else if (tahun != '' && maker != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview10/'+tahun+'/'+maker;    
                    }

                    if (terminal != '' && periode != '' && tahun != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview11/'+terminal+'/'+periode+'/'+tahun;    
                    } else if (terminal != '' && periode != '' && maker != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview12/'+terminal+'/'+periode+'/'+maker;    
                    } else if (terminal != '' && tahun != '' && maker != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview13/'+terminal+'/'+tahun+'/'+maker;    
                    } else if (periode != '' && tahun != '' && maker != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview14/'+periode+'/'+tahun+'/'+maker;  
                    }

                    if (terminal != '' && periode != '' && tahun != '' && maker != ''){
                        window.location.href = bs.siteURL + 'tps_online/zero_defect/listview15/'+terminal+'/'+periode+'/'+tahun+'/'+maker;      
                    }

					$.post(urls, function(data){
                     
                    });

					$.post(urls, param, function(data){
                     
                    });
        
        });

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