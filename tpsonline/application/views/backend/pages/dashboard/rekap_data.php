<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>
		<div class="container"> 
			<h2>Rekapitulasi Data</h2>
            <hr>			
		  	<form class="form-inline">
		  		<div class="form-group">
				    <label>Periode Tanggal : </label>
				</div>
			  <div class="form-group mx-sm-3 mb-2">
			    <input type="input" class="form-control" name="filter_date" id="filter_date">
			  </div>
			  <button type="button" class="btn btn-primary mb-2" id="btn_exp_excel">Export Excel</button>
			</form>
		  	<br>
			<div class="table-responsive">
				<table id="tbl_rekap_data" class="table table-striped table-bordered" style="width:100%">
			        <thead>
			            <tr>							
							<th>Nama Perusahaan</th>
							<th>No. Plat Truck</th>
							<th>Supir</th>
							<th>Waktu</th>
							<th>No. Vin</th>
							<th>No. SPPB</th>
							<th>Hold Status</th>
							<th>Status</th>
							<th>Visit ID</th>
							<th>Vessel</th>
							<th>Bentuk</th>
							<th>Jenis</th>
							<th>Pembuat</th>
							<th>Model</th>
							<th>Penerima</th>
							<th>Tujuan Terakhir</th>
			            </tr>
			        </thead>
			        <tbody>
					</tbody>
			    </table>
			</div>		  
		</div>	
	</div>
    	<?php $this->load->view('backend/elements/footer') ?>
</body>

<script type="text/javascript">
	var tanggal_start;
	var tanggal_end;

	$(document).ready(function() {
		var table = $('#tbl_rekap_data').DataTable({
		    "responsive": true,
		    "processing": true,
		    "serverSide": true,
		    "order": [],
		    "ajax": {
		      "url": "http://172.16.254.51/intapps_dev_complus/dashboard/rekap_data_data",
		      "data": {
		      	"tanggal_start": function() { return tanggal_start; },
		      	"tanggal_end": function() { return tanggal_end; }
		      },
		      "type": "POST"
		    }
		});

		$('#filter_date').daterangepicker({
		    format: 'DD/MM/YYYY'
		});

		$('#filter_date').on('apply.daterangepicker', function(ev, picker) {
		    tanggal_start 	 = $('#filter_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
			tanggal_end   	 = $('#filter_date').data('daterangepicker').endDate.format('YYYY-MM-DD');
			table.ajax.reload();
		});

		$('#filter_date').on('cancel.daterangepicker', function(ev, picker) {
			tanggal_start 	 = '';
			tanggal_end   	 = '';
			$('#filter_date').val('');
			table.ajax.reload();
		});

		$("#btn_exp_excel").click(function(){
		    var tanggal_start = "";
		    var tanggal_end   = "";

			if($("#filter_date").val()!=""){			
			    tanggal_start = $('#filter_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
			    tanggal_end   = $('#filter_date').data('daterangepicker').endDate.format('YYYY-MM-DD');
			}

		    var parsing = '';
			      	parsing = parsing + tanggal_start + '_' ;
			      	parsing = parsing + tanggal_end;

			var baseURL = '<?php echo base_url() ?>';
			window.location = baseURL+'dashboard/export_rekap/'+parsing;
		});

	});
</script>
</html>