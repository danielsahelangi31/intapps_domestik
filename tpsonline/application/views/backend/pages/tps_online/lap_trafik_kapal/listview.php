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
			
             <h1 style="margin-bottom:25px;margin-left:-25px">Laporan Trafik/Arus Kedatangan Kapal</h1>
			
            <?php echo form_open('');?>
			<div class="row">
            <div class="d-flex justify-content-left">
                    <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                              
  
                  <div class="row">
                        <div class="d-flex justify-content-left">
                         <div class="col-md-6">
                        <div class="card rounded-0 shadow" style="width: 200%;">
                            <div class="card-body">                                    
                        <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label class="col-form-label">Periode Awal<b class="text-danger">*</b></label>
                            <input type="month" class="form-control" id="PERIODE_AWAL" name="PERIODE_AWAL" value="PERIODE_AWAL"/>                            
                        </div>
                        
                        <div class="form-group" style="margin-left:0px;width:48.5%;"> 
                            <label class="col-form-label">Periode Akhir<b class="text-danger">*</b></label>
                            <input type="month" class="form-control" id="PERIODE_AKHIR" name="PERIODE_AKHR"/>                            
                        </div>   

                        <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <div class="form-group" style="margin-left:-15px;margin-right:1px">
                                <label>Terminal<b class="text-danger">*</b></label>
                                <select class="form-control" name="TERMINAL" id="TERMINAL">
                                    <option value="">-- PILIH --</option>
                                    <option value="DOM">DOMESTIK</option>
                                    <option value="INT">INTERNASIONAL</option>                                    
                                    
                                </select> 
							  </div>                       
                            </div>   
                         </div>  
                        </div>
                        <div class="btn"  id="simpan_load" style="display:auto;margin-left:0%">                           
							<a href=""  class="btn btn-success btn-sm" id="simpan">  <i class="glyphicon glyphicon-download"></i> Unduh xls</a></a>                       
                           
                        </div>             
                            </div>
                        </div>
                    </div>
			</div>
		
                    <?php echo form_close();?>
                                  
         
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
    <script type="text/javascript" src="<?php echo base_url('assets/js/notify.min.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	
	<script type="text/javascript">
		
		$(document).ready(function(){

            $('#simpan').click(function(e){
               
				var param = {   
         
					'PERIODE_AWAL' : $('#PERIODE_AWAL').val(),
					'PERIODE_AKHIR' : $('#PERIODE_AKHIR').val(),
                    'TERMINAL' : $('#TERMINAL').val(),      
                      
                }		
                console.log(param);
				is_error = false;         

				if(!param.PERIODE_AWAL || param.PERIODE_AWAL == ""){
					$('#PERIODE_AWAL').parent().addClass('has-error');
					add_validation_popover('#PERIODE_AWAL', 'Periode Awal Harus dipilih');
					
					is_error = true;
				}
				
                if(!param.PERIODE_AKHIR || param.PERIODE_AKHIR == ""){
					$('#PERIODE_AKHIR').parent().addClass('has-error');
					add_validation_popover('#PERIODE_AKHIR', 'Periode Akhir Harus dipilih');
					
					is_error = true;
				}

                if(!param.TERMINAL || param.TERMINAL == ""){
					$('#TERMINAL').parent().addClass('has-error');
					add_validation_popover('#TERMINAL', 'Terminal Harus dipilih');
					
					is_error = true;
				}
	
           	
                $awal = $('#PERIODE_AWAL').val().split('-');
                $akhir = $('#PERIODE_AKHIR').val().split('-');
           
                $periodeAwal = $awal[0];
                $periodeAkhir = $akhir[0];

                console.log('periode_awal',$periodeAwal);
                console.log('periode_akhir',$periodeAkhir);
                console.log('err', is_error);
                if(is_error){      
                    $.notify("Harap perbaiki field yang ditandai","error");     

				}else if ($periodeAwal !== $periodeAkhir){
                    $.notify("Tahun berjalan harus sama","error");
                }else{
                var PERIODE_AWAL = $('#PERIODE_AWAL').val();
                var PERIODE = new Date(PERIODE_AWAL);
                var start = moment(PERIODE).format('YYYY-MM'); ;
                console.log('awal',start);
         
                var PERIODE_AKHIR = $('#PERIODE_AKHIR').val();
                var AKHIR = new Date(PERIODE_AKHIR);
                var end = moment(AKHIR).format('YYYY-MM'); ;
                var terminal = $('#TERMINAL').val();
                var urls = bs.baseURL + 'tps_online/lap_trafik_kapal/export_laporan_xls/'+start+'/'+end
                console.log('end',end)
                console.log('url',urls)
                e.preventDefault(); 
                if (terminal == 'DOM'){
                    window.location.href = bs.baseURL + 'tps_online/lap_trafik_kapal/export_laporan_xls/'+start+'/'+end+'/'+terminal;
                }else if (terminal == 'INT') {
                    window.location.href = bs.baseURL + 'tps_online/lap_trafik_kapal/export_laporan_intr/'+start+'/'+end+'/'+terminal;
                }
        
                }
                return false;
			});

            function auto_remove_popover_on_change(){
                $(this).popover('destroy');
                $(this).parent().removeClass('has-error');
                
                $(this).unbind('change', auto_remove_popover_on_change);
            }
            
            function add_validation_popover(selector, msg, position){
                if(typeof(position) === 'undefined'){
                    position = 'right';
                }
            
                $(selector).popover('destroy');

                $(selector).popover({
                    'content' : msg,
                    'placement' : 'auto ' + position,
                    'trigger' : 'focus'
                });
                
                $(selector).popover('show');
                $(selector).change(auto_remove_popover_on_change);
            }
      
        });

	</script>
</body>
</html>