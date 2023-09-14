<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			<h1>Data Histori Import</h1>

			<p class="lead">
				<small></small>
			</p>
			<hr />
			<div class="row">
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">tahun</label>
						<select name="tahun" class="form-control input-sm " id="filterTahun">
							
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
				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">bulan</label>
						<select name="tahun" class="form-control input-sm " id="filterBulan">
							<?php 
							
							for ($i=1; $i <= 12 ; $i++) { 
								$mon = DateTime::createFromFormat('!m', $i);
								if ($i == (int)date('m')) {
									echo "<option value='".$mon->format('m')."' selected>".$mon->format('F')."</option>";	
								}
								else {
									echo "<option value=".$mon->format('m').">".$mon->format('F')."</option>";	
								}
							}
							 ?>
						</select>
					</div>
				</div>
			</div>
			
			<div class="table-responsive" >
				<table class="table table-striped table-condensed table-hover " id="tb_dathis">
					<thead>
						<tr>
			                <th rowspan="2">VISIT ID</th>
			                <th rowspan="2">VISIT NAME</th>
			                <th rowspan="2">VOYAGE IN</th>
			                <th rowspan="2">VOYAGE OUT</th>
			                <th rowspan="2">ARRIVAL</th>
			                <th rowspan="2">DEPARTURE</th>
			                <th rowspan="2">JML BL</th>
			                <th rowspan="2">JML VIN</th>
			                <th rowspan="2">JML CAR</th>
			                <th rowspan="2">JML KMS</th>
			                <th colspan="2">GATE IN/OUT</th>
			                <th colspan="2">LOAD/DISCHARGE</th>
			            </tr>
			            <tr>
			                <th>JML SUKSES</th>
			                <th>JML SELISIH</th>
			                <th>JML SUKSES</th>
			                <th>JML SELISIH</th>
			            </tr>
						<!-- <tr> -->

							<!-- <th>VISIT ID</th>
							<th>VISIT NAME</th>
							<th>VOYAGE IN</th>
							<th>VOYAGE OUT</th>
							<th>ARRIVAL</th>
							<th>DEPARTURE</th>
							<th>JML BL</th>
							<th>JML VIN</th>
							<th>JML CAR</th>
							<th>JML KMS</th>
							<th colspan="2">GATE IN/OUT</th>
							<th>JML SUKSES</th>
							<th>JML SELISIH</th>
							<th colspan="2">LOAD/DISCHARGE</th>
							<th>JML SUKSES</th>
							<th>JML SELISIH</th>
							 -->
							
							<!-- <th>JML VALID KMS</th> -->
						<!-- </tr> -->
					</thead>
				</table>
			</div>
			
		</div><!-- /.container -->

			
	</div>

    <?php $this->load->view('backend/elements/footer') ?>

	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		 let url = bs.siteURL + 'tps_online/notifikasi/get_data/' + bs.token;
		 let tabel = $('#tb_dathis').DataTable({
            "bProcessing": true,
            "serverSide": false,
            "paging": true,
            "searching": true,
            "ajax":{
                url : url,
                type: "post",
                data: function(d){
                    d.tahun = $('#filterTahun').val();
                    d.bulan = $('#filterBulan').val();
                    
                }, 
                dataType: 'json',
                error: function(response){
                    alert('Failed Load DataTables Data');
                },

            },
            "aoColumns": [
		        { 
		        	"mData": 'VISIT_ID',
		        	"render": function(data,type,row,meta){
		        		if (type == 'display') {
		        			data = "<a href='"+bs.siteURL+"tps_online/notifikasi/view/"+ data + "'>" + data + "</a>";
		        		}
		        		return data;
		        	}
		    	},
		        { "mData": 'VISIT_NAME' },
		        { "mData": 'VOYAGE_IN' },
		        { "mData": 'VOYAGE_OUT' },
		        { "mData": 'ARRIVAL' },
		        { "mData": 'DEPARTURE' },
		        { "mData": 'JML_BL' },
		        { "mData": 'JML_VIN' },
		        { "mData": 'JML_CAR' },
		        { "mData": 'JML_KMS' },
		        
		        //GATEINOUT
		        { "mData": 'JML_SUKSES_CODECO' },
		        { "mData": 'JML_SELISIH_GATEINOUT', 
		          "render": function(data,type,row,meta){
		          	
		          	var selisih_gateinout = row.JML_VIN - row.JML_SUKSES_CODECO;
		        		
		        		if (selisih_gateinout != 0) {
		        			return '<p style="color:red;">'+selisih_gateinout+'</p>';
		        		}else{
		        			return '<p style="color:blue;">'+selisih_gateinout+'</p>';
		        		}
		        		
		        			
		        	}

		    	},
		        
		        //LOAD/DISCHARGE
		        { "mData": 'JML_SUKSES_COARRI' },
		        { "mData": 'JML_SELISIH_LOADDISCHARGE', 
		         "render": function(data,type,row,meta){
		          	
		         	var selisih_loaddischarge = row.JML_VIN - row.JML_SUKSES_COARRI;

		         		if (selisih_loaddischarge != 0) {
		         			return '<p style="color:red;">'+selisih_loaddischarge+'</p>';
		         		}else{
		         			return '<p style="color:blue;">'+selisih_loaddischarge+'</p>';
		         		}
		          }

		    	}
		        
		        // { "mData": 'JML_VALID_CAR' },
		        // { "mData": 'JML_VALID_KMS' }
		 
		    ],         
            "lengthChange": true,
            "bFilter": true
        });

	$('#filterTahun').change(function(){
		tabel.ajax.reload();
	});
	$('#filterBulan').change(function(){
		tabel.ajax.reload();
	});


	});





</script>
</body>
</html>
