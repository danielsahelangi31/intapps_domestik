<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			<h1>Data Histori</h1>

			<p class="lead">
				<small></small>
			</p>
			<hr />
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">tahun</label>
						<select name="tahun" class="form-control" id="filterTahun">
							<option value="">Pilih Tahun</option>
							<?php 
							for ($i=2015; $i <= date('Y') ; $i++) { 
								if ($i == date('Y')) {
									echo "<option value='".$i."' selected>".$i."</option>";	
								}
								else {
									echo "<option value=".$i.">".$i."</option>";	
								}
							}
							 ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
					
				</div>
			</div>
			<div class="table-responsive" >
				<table class="table table-striped table-condensed table-hover" id="tb_dathis">
					<thead>
						<tr>
							<th>VISIT ID</th>
							<th>VISIT NAME</th>
							<th>VOYAGE IN</th>
							<th>ARRIVAL</th>
							<th>DEPARTURE</th>
							<th>JML BL</th>
							<th>JML VIN</th>
							<th>JML SUKSES CODECO</th>
							<th>JML SUKSES COARRI</th>
							<th>JML CAR</th>
							<th>JML KMS</th>
							<th>JML VALID CAR</th>
							<th>JML VALID KMS</th>
						</tr>
					</thead>
				</table>
			</div>
			
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
</body>
<script type="text/javascript">
	$(document).ready(function(){
		 let url = bs.siteURL + 'tps_online/data_histori/get_data/' + bs.token;
		 let tabel = $('#tb_dathis').DataTable({
            "bProcessing": true,
            "serverSide": true,
            "ajax":{
                url : url,
                type: "post",
                data: function(d){
                    d.tahun = $('#filterTahun').val();
                    
                }, 
                dataType: 'json',
                error: function(response){
                    alert('Failed Load DataTables Data');
                },

            },
            "aoColumns": [
		        { "mData": 'VISIT_ID'},
		        { "mData": 'VISIT_NAME' },
		        { "mData": 'VOYAGE_IN' },
		        { "mData": 'ARRIVAL' },
		        { "mData": 'DEPARTURE' },
		        { "mData": 'JML_BL' },
		        { "mData": 'JML_VIN' },
		        { "mData": 'JML_SUKSES_CODECO' },
		        { "mData": 'JML_SUKSES_COARRI' },
		        { "mData": 'JML_CAR' },
		        { "mData": 'JML_KMS' },
		        { "mData": 'JML_VALID_CAR' },
		        { "mData": 'JML_VALID_KMS' }
		 
		    ],         
            "lengthChange": true,
            "bFilter": true
        });

	$('#filterTahun').change(function(){
		tabel.ajax.reload();
	});


	});





</script>



</html>