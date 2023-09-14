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

			<h1>Download TAM</h1>
			<p class="lead">
				<small></small>
			</p>
			<hr>
			<div class="row">
			<div class="row">
				<div align="right">
					<a href="<?php echo base_url('tps_online/Tam/export_tam_xls');?>" class="btn btn-success btn-sm">
                       <i class="glyphicon glyphicon-download"></i> Unduh xls
                    </a>
                    <!-- <a href="<?php echo base_url('tps_online/consignment/export_tam_csv');?>" class="btn btn-success btn-sm">
                       <span class="glyphicon glyphicon-download"> Unduh CSV</span> 
                    </a> -->
				</div>
			</div>
			<br>
			<div class="row">
				<div class="row ct-listview-toolbar">
					<div class="col-md-12">
						<div class="table-responsive" style="width:100%; height:430px; overflow:auto">
							<table class="table table-striped table-condensed" id="dt_tabel">
								<thead>
									<tr>
	                                	<th>VIN</th>
	                                	<th>BL NUMBER</th>
	                                	<th>LOGISTIC COMPANY</th>
	                                	<th>ANNOUNCED DATE</th>
	                                	<th>ON TERMINAL DATE</th>
	                                	<th>LOADED DATE</th>
	                                	<th>LEFT DATE</th>
	                                	<th>ATA DATE</th>
	                                	<th>UPDATE DATE</th>
									</tr>
								</thead>				
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>


		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>

	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	
	<script type="text/javascript">
		
		$(document).ready(function(){
		 let url = bs.siteURL + 'tps_online/Tam/load_data_Tam/' + bs.token;
		 let tabel = $('#dt_tabel').DataTable({
            "bProcessing": true,
            "serverSide": false,
            "paging": true,
            "searching": true,
            "ajax":{
                url : url,
                type: "post",
                // data: function(d){
                //     d.model_name = $('#filter_make_name').val();
                //     d.perusahaan = $('#filter_perusahaan').val();            
                // }, 
                dataType: 'json',
                error: function(response){
                    alert('Failed Load DataTables Data');
                },

            },
            "aoColumns": [

		        { "mData": 'VIN' },
		        { "mData": 'BL_NUMBER' },
		        { "mData": 'LOGISCTICCOMPANY' },
		        { "mData": 'ANNOUNCEDDATE' },
		        { "mData": 'ONTERMINALDATE' },
		        { "mData": 'LOADEDDATE' },
		        { "mData": 'LEFTDATE' },
		        { "mData": 'ATADATE' },
		        { "mData": 'UPDATEDATE' }
		 
		    ],         
            "lengthChange": true,
            "bFilter": true
        });

		//  $('#filter_make_name').change(function(){
		// 	tabel.ajax.reload();
		// });
		 
		//   $('#filter_perusahaan').change(function(){
		// 	tabel.ajax.reload();
		// });
	});

	</script>

</body>
</html>
