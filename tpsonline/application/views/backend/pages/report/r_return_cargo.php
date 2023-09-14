<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Return Cargo</h1>
			<p class="lead">
				<small>Status: <?= $ket ?></small>
			</p>
			
			<hr />

			<div class="row">
	          <div class="col-lg-12">
	            <div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                </div>
	              </div>
	              <div class="card-body">
	          	<table class="table table-striped table-condensed" id="t_return_cargo">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>VIN</th>
	                        <th>Maker</th>
	                        <th>Truck Code</th>
	                        <th>Created At</th>
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

<script type="text/javascript">
$(document).ready(function() { 

	$('#t_return_cargo').DataTable({
		"processing": true,
		"serverSide": true,
		"deferRender": true,
		"dom": 'Bfrtip',
		"buttons": [
		    'colvis',
		    'pageLength'
		],
		"order": [],
		"ajax": {
		    "url": '<?= site_url() ?>dashboard_eticket/get_rc/<?= $rc_status ?>/<?= $bulan ?>/<?= $tahun ?>',
		    "type": "POST"
		},
		"columnDefs": [{
		        "targets": [0],
		        "orderable": false,
		    },
		    {
		        "targets": [1, 2, 3],
		        "visible": true,
		        "searchable": true
		    },
		]
		});
    
});
</script>
</body>
</html>