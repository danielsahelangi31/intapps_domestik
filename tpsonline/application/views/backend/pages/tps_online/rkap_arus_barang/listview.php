<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>
<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
             <h1 style="margin-bottom:25px;margin-left:-25px">RKAP ARUS BARANG</h1>
			
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
                                <label>Jenis: </label>
                                <select class="form-control" name="JENIS" id="JENIS" >                                                                                                                                    
                                              <option value="<?php echo $jeniss ?>"><?php echo $jeniss  ? : '-- Pilih --'?></option>                           
                                              <option value="EKSPOR">EKSPOR</option>
                                              <option value="IMPOR">IMPOR</option>   
                                              <option value="BONGKAR">BONGKAR</option>
                                              <option value="MUAT">MUAT</option>                                    
                                                    
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
                                <label>Komoditi: </label>
                                <select class="form-control" name="KOMODITI" id="KOMODITI">
                                              <option value="<?php echo $komoditis ?>"><?php echo $komoditis ? : '-- Pilih --'?></option>   
                                              <option value="MOBIL">MOBIL</option>
                                              <option value="ALAT BERAT">ALAT BERAT</option>     
                                              <option value="TRUCK/BUS">TRUCK/BUS</option>  
                                              <option value="PENUMPANG">PENUMPANG</option> 
                                              <option value="SEPEDA">SEPEDA</option> 
                                              <option value="GENERAL CARGO">GENERAL CARGO</option>  
                                              <option value="MOTOR">MOTOR</option>                       
                                                    
                                         </select> 
                                </div> 
                                </div> 

                                <div class="form-group col-md-6" style="margin-top:5px;margin-left:0px;width:100%">
                                   <button type="submit" class="btn btn-success" id="simpan">Cari</button>
                                   <a href="<?php echo site_url('tps_online/rkap_arus_barang/listview') ?>" class="btn btn-warning">Reset</a>  
                                   <hr /> 
                                </div>
                                            <div class="btn"  id="simpan_load" style="margin-left:0%">                            
                                              <a href="<?php echo site_url('tps_online/rkap_arus_barang/new') ?>" class="btn btn-primary">Tambah Baru</a>     
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
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Jenis', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Tahun', $cfg) ?></th> 
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Komoditi', $cfg) ?></th>     
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Satuan', $cfg) ?></th>                            
                                <th style="text-align: center"><?php echo gridHeader('LQ_GATE_2', 'Januari', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('LQ_GATE_3', 'Februari', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('CARGO_DEFECT', 'Maret', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'April', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Mei', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Juni', $cfg) ?></th>    
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Juli', $cfg) ?></th> 
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Agustus', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'September', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Oktober', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'November', $cfg) ?></th>
                                <th style="text-align: center"><?php echo gridHeader('TAHUN', 'Desember', $cfg) ?></th> 
                                

                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
              
                        <tbody>
                        <?php           
                           $grid_state = 'tps_online/rkap_arus_barang/listview/p:1';                      
                        
                               if($databarang){    
                                 foreach($databarang as $row){                           
                                    ?>
                                    <tr>                                    
                                        <td style="text-align: center"><?php echo $row['TERMINAL']?></td>                                       
                                        <td style="text-align: center"><?php echo $row['JENIS']?></td>                                  
                                        <td style="text-align: center"><?php echo $row['TAHUN']?></td>                              
                                        <td style="text-align: center"><?php echo $row['KOMODITI']?></td>                              
                                        <td style="text-align: center"><?php echo $row['SATUAN'] ?></td>
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
                                            
                                        <a href="<?php echo site_url('tps_online/rkap_arus_barang/view/' . $row['id_barang'] . '/' . $grid_state) ?>" class="edit_link">Edit</a>                                            
                                   
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
		
			<div id="kampret_loader"></div>
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
					'TERMINAL' : $('#TERMINAL').val(),
                    'TAHUN' : $('#TAHUN').val(),
                    'JENIS' : $('#JENIS').val(),
                    'KOMODITI' : $('#KOMODITI').val(),                   

            }

                    console.log('prm',param);
                    terminal = $('#TERMINAL').val();
                    tahun = $('#TAHUN').val();
                    jenis = $('#JENIS').val();
                    komoditi = $('#KOMODITI').val();             

          
                    var urls = bs.siteURL + 'tps_online/rkap_arus_barang/listvieww/'+terminal+'/'+jenis+'/'+tahun + '/'+komoditi;
                    console.log('url', urls)
                    e.preventDefault(); 
                 
                    if (terminal != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview1/'+terminal;           
                    } else if (jenis != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview2/'+jenis;    
                    } else if (tahun != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview3/'+tahun;    
                    } else if (komoditi != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview4/'+komoditi; 
                    }

                    if (terminal != '' && jenis != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview5/'+terminal+'/'+jenis;    
                    } else if (terminal != '' && tahun != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview6/'+terminal+'/'+tahun;    
                    } else if (terminal != '' && komoditi != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview7/'+terminal+'/'+komoditi;    
                    } else if (jenis != '' && tahun != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview8/'+jenis+'/'+tahun;    
                    } else if (jenis != '' && komoditi != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview9/'+jenis+'/'+komoditi;    
                    } else if (tahun != '' && komoditi != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview10/'+tahun+'/'+komoditi;    
                    }

                    if (terminal != '' && jenis != '' && tahun != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview11/'+terminal+'/'+jenis+'/'+tahun;    
                    } else if (terminal != '' && jenis != '' && komoditi != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview12/'+terminal+'/'+jenis+'/'+komoditi;    
                    } else if (terminal != '' && tahun != '' && komoditi != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview13/'+terminal+'/'+tahun+'/'+komoditi;    
                    } else if (jenis != '' && tahun != '' && komoditi != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview14/'+jenis+'/'+tahun+'/'+komoditi;  
                    }

                    if (terminal != '' && jenis != '' && tahun != '' && komoditi != ''){
                        window.location.href = bs.siteURL + 'tps_online/rkap_arus_barang/listview15/'+terminal+'/'+jenis+'/'+tahun+'/'+komoditi;      
                    }

					$.post(urls, function(data){
                     
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